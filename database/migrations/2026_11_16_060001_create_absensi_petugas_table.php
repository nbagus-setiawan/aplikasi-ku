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
        Schema::create('absensi_petugas', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->enum('bagian', ['koordinator', 'anggota'])->default('anggota');
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->foreignId('pengurus_id')->nullable()->constrained('penguruses')->nullOnDelete();
            $table->string('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'bagian']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_petugas');
    }
};
