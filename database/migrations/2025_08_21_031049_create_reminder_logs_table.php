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
        Schema::create('reminder_logs', function (Illuminate\Database\Schema\Blueprint $t) {
            $t->id();
            $t->enum('entity', ['contract'])->index();
            $t->unsignedBigInteger('entity_id');     // id employees
            $t->date('target_date');                 // tanggal jatuh tempo
            $t->integer('rule_days');                // H-berapa
            $t->string('recipient',191)->nullable(); // email tujuan
            $t->enum('channel', ['email','whatsapp','both'])->default('email');
            $t->enum('status', ['sent','skipped','failed'])->index();
            $t->text('meta')->nullable();            // error/detail JSON
            $t->timestamps();
            $t->unique(['entity','entity_id','target_date','rule_days']); // idempotent
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
