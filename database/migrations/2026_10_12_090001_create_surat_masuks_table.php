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
        Schema::create('surat_masuks', function (Blueprint $table) {
            $table->id();
            $table->string('no_agenda')->index();
            $table->string('no_surat');
            $table->date('tgl_surat');
            $table->date('tgl_diterima');
            $table->string('pengirim');
            $table->string('perihal');
            $table->foreignId('kategori_id')->nullable()->constrained('kategori_surats')->nullOnDelete();
            $table->string('file')->nullable();
            $table->text('disposisi')->nullable();
            $table->enum('status_disposisi', ['baru', 'diproses', 'selesai'])->default('baru')->index();
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
        Schema::dropIfExists('surat_masuks');
    }
};
