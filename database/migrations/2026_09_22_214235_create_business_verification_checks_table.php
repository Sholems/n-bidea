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
        Schema::create('business_verification_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->foreignId('super_admin_id')->constrained('users')->cascadeOnDelete();
            $table->enum('method', ['phone_call', 'site_visit']);
            $table->enum('decision', ['verified', 'not_verified']);
            $table->text('note');
            $table->timestamp('checked_at');
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['business_id', 'checked_at']);
        });

        DB::table('businesses')
            ->where('status', 'approved')
            ->whereNotNull('verified_at')
            ->update(['status' => 'verified']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_verification_checks');
    }
};
