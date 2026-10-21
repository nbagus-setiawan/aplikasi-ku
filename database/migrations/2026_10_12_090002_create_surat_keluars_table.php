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
        Schema::create('surat_keluars', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat')->unique();
            $table->date('tgl_surat');
            $table->string('tujuan');
            $table->string('perihal');
            $table->foreignId('kategori_id')->constrained('kategori_surats');
            $table->longText('isi');
            $table->string('nama_penandatangan');
            $table->string('jabatan_penandatangan');
            $table->string('file')->nullable();
            $table->string('file_final')->nullable();
            $table->enum('status', ['draft', 'final', 'terkirim'])->default('draft')->index();
            $table->enum('metode_ttd', ['digital', 'basah'])->nullable();
            $table->enum('status_ttd', ['belum', 'sudah'])->default('belum');
            $table->string('kode_verifikasi', 64)->unique();
            $table->foreignId('user_id')->constrained('users');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_keluars');
    }
};
