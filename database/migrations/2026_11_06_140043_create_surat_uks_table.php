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
        Schema::create('surat_uks', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->unique();
            $table->string('kode_verifikasi', 10)->unique(); // 4 digit kode unik (misal: 7F2B, A941)
            $table->string('jenis', 20)->default('pulang'); // 'pulang' = Surat Izin Pulang, 'sakit' = Surat Izin Sakit
            $table->date('tanggal_cetak'); // Tanggal cetak blangko fisik
            $table->date('tanggal_pembelian')->nullable(); // Tanggal santri membeli & memakai surat
            $table->foreignId('santri_id')->nullable()->constrained('santris')->nullOnDelete();
            $table->string('nama_santri')->nullable(); // Nama santri yang dicatat saat terjual
            $table->string('kelas')->nullable(); // Kelas santri
            $table->string('asrama')->nullable(); // Kamar / Asrama santri
            $table->string('keperluan')->nullable(); // Alasan izin pulang / keluhan sakit (jika diisi)
            $table->string('diagnosa')->nullable(); // Diagnosa sakit
            $table->decimal('harga', 10, 2)->default(5000); // Rp 5.000,- per lembar
            $table->string('status', 20)->default('tersedia'); // 'tersedia' (blangko stok), 'terjual' (terpakai & masuk kas), 'kembali' (sudah kembali)
            $table->dateTime('tanggal_kembali_riil')->nullable();
            $table->foreignId('kas_transaksi_id')->nullable()->constrained('kas_transaksis')->nullOnDelete(); // Tautan otomatis ke kas bendahara
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_uks');
    }
};
