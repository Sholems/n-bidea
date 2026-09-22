<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();
            $table->enum('fee_type', ['certification', 'renewal']);
            $table->decimal('amount', 10, 2);
            $table->enum('payment_status', ['unpaid', 'pending_confirmation', 'paid', 'waived'])->default('unpaid');
            $table->string('payment_reference')->nullable();
            $table->string('proof_file_path')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('business_id');
            $table->index('payment_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fees');
    }
};
