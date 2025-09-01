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
        Schema::create('permits', function (Blueprint $table) {
            $table->id();

            // Data utama
            $table->string('type', 50);                 // SIM, STNK, KIR, IMB/HO, AMDAL, SLF, dll
            $table->string('number', 100)->nullable()->index();
            $table->string('holder', 100)->nullable();  // nama pemegang / pemilik aset
            $table->string('asset_location', 50)->nullable();

            // Tanggal
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->index();        // dasar reminder H-60/H-30

            // PIC & status
            $table->string('pic', 100)->nullable();     // email PIC GA/HRD (boleh kosong)
            $table->enum('status', ['active','renewal','expired'])->default('active')->index();

            // Lainnya
            $table->text('notes')->nullable();
            $table->string('attachment_path', 255)->nullable(); // simpan path file (private)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('permits');
    }
};
