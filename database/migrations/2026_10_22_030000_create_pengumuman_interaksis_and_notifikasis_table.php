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
        // 1. Tabel Suka / Like Pengumuman
        Schema::create('pengumuman_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumuman_id')->constrained('pengumumans')->cascadeOnDelete();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['pengumuman_id', 'ip_address']);
        });

        // 2. Tabel Komentar Pengumuman Publik
        Schema::create('pengumuman_komentars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumuman_id')->constrained('pengumumans')->cascadeOnDelete();
            $table->string('nama', 100)->nullable(); // Nama opsional, jika kosong "User Publik" / "Hamba Allah"
            $table->text('isi');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('is_tampil')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Pembagian / Share Pengumuman
        Schema::create('pengumuman_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengumuman_id')->constrained('pengumumans')->cascadeOnDelete();
            $table->string('platform', 50)->default('whatsapp'); // whatsapp, facebook, salin_tautan
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });

        // 4. Tabel Notifikasi Panel Sekretaris (Lonceng Navbar Admin)
        Schema::create('notifikasis', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->text('pesan');
            $table->string('tipe', 50)->default('info'); // like, komentar, share
            $table->foreignId('pengumuman_id')->nullable()->constrained('pengumumans')->nullOnDelete();
            $table->string('link')->nullable();
            $table->json('data_tambahan')->nullable();
            $table->boolean('is_dibaca')->default(false);
            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifikasis');
        Schema::dropIfExists('pengumuman_shares');
        Schema::dropIfExists('pengumuman_komentars');
        Schema::dropIfExists('pengumuman_likes');
    }
};
