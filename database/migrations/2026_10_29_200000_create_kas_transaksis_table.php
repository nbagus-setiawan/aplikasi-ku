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
        Schema::create('kas_transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 50)->unique();
            $table->date('tanggal');
            $table->enum('jenis', ['debit', 'kredit']);
            $table->string('kategori', 100);
            $table->decimal('nominal', 15, 2);
            $table->text('keterangan');
            $table->string('nama_pihak', 150)->nullable();
            $table->string('lampiran')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['tanggal', 'jenis']);
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kas_transaksis');
    }
};
