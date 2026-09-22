<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('businesses', 'country_code')) {
            Schema::table('businesses', function (Blueprint $table) {
                $table->string('country_code', 2)->default('NG')->after('business_type');
            });
        }

        if (! Schema::hasIndex('businesses', 'businesses_country_code_index')) {
            Schema::table('businesses', function (Blueprint $table) {
                $table->index('country_code');
            });
        }

        if (! Schema::hasColumn('document_types', 'country_code')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->string('country_code', 2)->nullable()->after('description');
            });
        }

        if (! Schema::hasIndex('document_types', 'document_types_country_code_index')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->index('country_code');
            });
        }

        $duplicateNames = DB::table('document_types')
            ->select('name')
            ->groupBy('name')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('name');

        foreach ($duplicateNames as $name) {
            $typeIds = DB::table('document_types')
                ->where('name', $name)
                ->orderBy('id')
                ->pluck('id');
            $canonicalTypeId = $typeIds->shift();

            DB::table('business_documents')
                ->whereIn('document_type_id', $typeIds)
                ->update(['document_type_id' => $canonicalTypeId]);

            DB::table('document_types')->whereIn('id', $typeIds)->delete();
        }

        if (! Schema::hasIndex('document_types', 'document_types_name_unique', 'unique')) {
            Schema::table('document_types', function (Blueprint $table) {
                $table->unique('name');
            });
        }

        $now = now();
        $requirements = collect([
            [
                'name' => 'CAC Registration Certificate',
                'description' => 'Official Certificate of Incorporation, Business Name Registration, or another valid registration document issued by the Corporate Affairs Commission.',
            ],
            [
                'name' => 'Tax/NRS Certificate',
                'description' => 'Valid tax registration document issued by the relevant revenue authority and showing the business tax or revenue registration number.',
            ],
            [
                'name' => 'Proof of Address',
                'description' => 'Recent utility bill, tenancy agreement, property document, bank statement, or another official record confirming the business address.',
            ],
            [
                'name' => 'NIN Document',
                'description' => 'Valid National Identification Number slip belonging to the business owner, director, or authorised representative.',
            ],
            [
                'name' => 'Other Supporting Document',
                'description' => 'Additional licence, permit, certificate, regulatory approval, business profile, or supporting evidence confirming the business operations and eligibility.',
                'is_required' => false,
            ],
        ])->map(fn (array $requirement): array => [
            'is_required' => true,
            ...$requirement,
            'country_code' => 'NG',
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        DB::table('document_types')->upsert(
            $requirements,
            ['name'],
            ['description', 'country_code', 'is_required', 'status', 'updated_at'],
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_types', function (Blueprint $table) {
            $table->dropUnique(['name']);
            $table->dropIndex(['country_code']);
            $table->dropColumn('country_code');
        });

        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex(['country_code']);
            $table->dropColumn('country_code');
        });
    }
};
