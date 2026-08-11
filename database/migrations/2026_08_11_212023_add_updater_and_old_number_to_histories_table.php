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
        Schema::table('contract_histories', function (Blueprint $table) {
            $table->string('updater_name')->nullable()->after('notes');
        });

        Schema::table('permit_histories', function (Blueprint $table) {
            $table->string('updater_name')->nullable()->after('notes');
            $table->string('old_number')->nullable()->after('expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permit_histories', function (Blueprint $table) {
            $table->dropColumn(['updater_name', 'old_number']);
        });

        Schema::table('contract_histories', function (Blueprint $table) {
            $table->dropColumn(['updater_name']);
        });
    }
};
