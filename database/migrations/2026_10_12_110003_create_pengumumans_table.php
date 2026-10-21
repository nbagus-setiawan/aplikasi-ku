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
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->text('isi');
            $table->string('gambar')->nullable(); // Foto / thumbnail
            $table->string('kategori')->default('umum'); // umum, santri, wali, akademik
            $table->date('tgl_tayang');
            $table->date('tgl_kedaluwarsa')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->enum('status', ['draft', 'terbit'])->default('draft');
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
        Schema::dropIfExists('pengumumans');
    }
};
