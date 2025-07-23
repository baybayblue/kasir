<?php
// File: database/migrations/..._create_jenis_akuns_table.php

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
        Schema::create('jenis_akuns', function (Blueprint $table) {
            $table->id();
            $table->string('nama_akun');
            $table->text('keterangan')->nullable();
            $table->enum('jenis', ['Harta', 'Utang', 'Modal', 'Pendapatan', 'Beban']);
            $table->enum('status', ['Aktif', 'Tidak Aktif'])->default('Aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jenis_akuns');
    }
};
