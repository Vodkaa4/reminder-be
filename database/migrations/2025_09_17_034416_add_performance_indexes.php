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
        // Add indexes for employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->index(['contract_end', 'is_permanent'], 'idx_contract_status');
            $table->index(['dept', 'is_permanent'], 'idx_dept_status');
            $table->index('name');
            $table->index('nip');
        });

        // Add indexes for permits table
        Schema::table('permits', function (Blueprint $table) {
            $table->index(['expires_at', 'status'], 'idx_expires_status');
            $table->index('type');
            $table->index('pic');
        });

        // Add indexes for users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('email_verified_at');
            $table->index('role');
        });

        // Add indexes for reminder_logs table
        Schema::table('reminder_logs', function (Blueprint $table) {
            $table->index(['entity', 'status'], 'idx_entity_status');
            $table->index('target_date');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex('idx_contract_status');
            $table->dropIndex('idx_dept_status');
            $table->dropIndex(['name']);
            $table->dropIndex(['nip']);
        });

        Schema::table('permits', function (Blueprint $table) {
            $table->dropIndex('idx_expires_status');
            $table->dropIndex(['type']);
            $table->dropIndex(['pic']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['email_verified_at']);
            $table->dropIndex(['role']);
        });

        Schema::table('reminder_logs', function (Blueprint $table) {
            $table->dropIndex('idx_entity_status');
            $table->dropIndex(['target_date']);
            $table->dropIndex(['created_at']);
        });
    }
};