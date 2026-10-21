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
        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('nama');
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_wali')->nullable();
            $table->string('hp_wali')->nullable();
            $table->string('kamar')->nullable();
            $table->string('kelas')->nullable();
            $table->string('tahun_masuk', 10)->nullable();
            $table->string('status', 20)->default('aktif'); // aktif, lulus, keluar, pindah
            $table->string('foto')->nullable();
            $table->softDeletes();
            $table->timestamps();

            // Indexes untuk filter & search cepat (kebutuhan non-fungsional PRD: < 2 detik per 5000 data)
            $table->index('nama');
            $table->index('status');
            $table->index('kelas');
            $table->index('kamar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('santris');
    }
};
