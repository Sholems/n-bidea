<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->index(['status', 'verification_expires_at'], 'businesses_status_expiry_index');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['user_id', 'action', 'created_at'], 'audit_user_action_date_index');
        });
    }

    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropIndex('businesses_status_expiry_index');
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->dropIndex('audit_user_action_date_index');
        });
    }
};
