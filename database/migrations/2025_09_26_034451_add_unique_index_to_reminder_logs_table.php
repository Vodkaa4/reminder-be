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
        Schema::table('reminder_logs', function (Blueprint $table) {
            // Add unique index for (entity, entity_id, target_date, rule_days)
            // Using varchar(191) for MySQL 5.7 compatibility
            $table->string('entity', 191)->change();
            $table->string('entity_id', 191)->change();
            $table->date('target_date')->change();
            $table->integer('rule_days')->change();
            
            // Add unique constraint
            $table->unique(['entity', 'entity_id', 'target_date', 'rule_days'], 'reminder_logs_unique_constraint');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminder_logs', function (Blueprint $table) {
            $table->dropUnique('reminder_logs_unique_constraint');
        });
    }
};