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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('nip')->unique(); // Nomor Induk Pegawai
            $table->string('name'); // Nama lengkap karyawan
            $table->string('email')->unique(); // Email karyawan
            $table->string('supervisor')->nullable(); // Nama atasan langsung
            $table->boolean('is_permanent'); // Status karyawan: permanen atau kontrak
            $table->date('contract_start'); // Tanggal mulai kontrak
            $table->date('contract_end')->nullable(); // Tanggal berakhir kontrak
            $table->date('resign_date')->nullable(); // Tanggal resign
            $table->string('dept'); // Departemen
            $table->string('sect')->nullable(); // Sub-seksi
            $table->string('position'); // Jabatan
            $table->string('location'); // Lokasi kerja
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
