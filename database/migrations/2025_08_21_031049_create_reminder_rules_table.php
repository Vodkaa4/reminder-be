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
        Schema::create('reminder_rules', function (Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->enum('entity', ['contract']);        // sementara fokus kontrak
            $t->integer('days_before');              // contoh 30
            $t->enum('channel', ['email','whatsapp','both'])->default('email');
            $t->boolean('active')->default(true);
            $t->timestamps();
            $t->unique(['entity','days_before']);    // cegah duplikat rule
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_rules');
    }
};
