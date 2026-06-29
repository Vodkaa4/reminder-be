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
            $indexesFound = collect(Schema::getIndexes('reminder_logs'))->pluck('name')->toArray();

            if (in_array('reminder_logs_unique_combination', $indexesFound)) {
                $table->dropUnique('reminder_logs_unique_combination');
            }
            if (in_array('reminder_logs_unique_constraint', $indexesFound)) {
                $table->dropUnique('reminder_logs_unique_constraint');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reminder_logs', function (Blueprint $table) {
            $table->unique(['entity', 'entity_id', 'target_date', 'rule_days'], 'reminder_logs_unique_combination');
            $table->unique(['entity', 'entity_id', 'target_date', 'rule_days'], 'reminder_logs_unique_constraint');
        });
    }
};
