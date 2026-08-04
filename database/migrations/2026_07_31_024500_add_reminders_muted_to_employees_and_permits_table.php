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
        Schema::table('employees', function (Blueprint $table) {
            $table->boolean('reminders_muted')->default(false)->after('resign_date');
        });

        Schema::table('permits', function (Blueprint $table) {
            $table->boolean('reminders_muted')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn('reminders_muted');
        });

        Schema::table('permits', function (Blueprint $table) {
            $table->dropColumn('reminders_muted');
        });
    }
};
