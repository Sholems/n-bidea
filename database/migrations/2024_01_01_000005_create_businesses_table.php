<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('registry_number')->nullable()->unique();
            $table->string('business_name');
            $table->string('trading_name')->nullable();
            $table->string('registration_number');
            $table->string('cac_number')->nullable();
            $table->string('nrs_number')->nullable();
            $table->string('nin')->nullable();
            $table->string('business_type');
            $table->foreignId('sector_id')->nullable()->constrained('sectors')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('address');
            $table->string('state');
            $table->string('lga')->nullable();
            $table->string('city');
            $table->string('phone');
            $table->string('email');
            $table->string('website')->nullable();
            $table->string('contact_person_name');
            $table->string('contact_person_phone');
            $table->string('contact_person_email');
            $table->string('trade_activity')->nullable();
            $table->string('border_route')->nullable();
            $table->enum('status', [
                'draft', 'submitted', 'under_review', 'correction_required',
                'approved', 'rejected', 'verified', 'expired', 'suspended',
            ])->default('draft');
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('verification_expires_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('status');
            $table->index('state');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
