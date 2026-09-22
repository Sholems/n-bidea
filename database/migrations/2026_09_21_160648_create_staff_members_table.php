<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained()->cascadeOnDelete();
            $table->string('staff_number')->nullable()->unique();
            $table->string('full_name');
            $table->string('job_title');
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('nationality');
            $table->date('date_of_birth')->nullable();
            $table->string('nin')->nullable();
            $table->string('passport_number')->nullable();
            $table->string('status')->default('draft');
            $table->text('correction_response')->nullable();
            $table->text('review_note')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('verification_expires_at')->nullable();
            $table->timestamps();
            $table->index(['business_id', 'status']);
            $table->index(['status', 'verification_expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_members');
    }
};
