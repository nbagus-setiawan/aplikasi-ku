<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi tabel dokumen_lampirans (Multi-berkas polymorphic).
     */
    public function up(): void
    {
        Schema::create('dokumen_lampirans', function (Blueprint $table) {
            $table->id();
            $table->string('attachable_type', 100);
            $table->unsignedBigInteger('attachable_id');
            $table->string('nama_berkas', 255);
            $table->string('path_berkas', 500);
            $table->string('mime_type', 100)->nullable();
            $table->unsignedBigInteger('ukuran_byte')->default(0);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();

            $table->index(['attachable_type', 'attachable_id'], 'dokumen_lampirans_attachable_index');
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_lampirans');
    }
};
