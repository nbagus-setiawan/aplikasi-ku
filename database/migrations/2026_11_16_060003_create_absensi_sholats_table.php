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
        Schema::create('absensi_sholats', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->enum('waktu_sholat', ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya']);
            $table->foreignId('petugas_id')->constrained('absensi_petugas')->cascadeOnDelete();
            $table->foreignId('koordinator_id')->nullable()->constrained('absensi_petugas')->nullOnDelete();
            $table->time('jam_absen');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alfa'])->default('hadir');
            $table->enum('kategori_waktu', ['tepat_waktu', 'terlambat'])->nullable();
            $table->string('keterangan')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['tanggal', 'waktu_sholat', 'petugas_id'], 'absensi_unik_per_sholat_harian');
            $table->index(['tanggal', 'waktu_sholat', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_sholats');
    }
};
