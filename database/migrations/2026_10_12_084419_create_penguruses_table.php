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
        Schema::create('penguruses', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('jabatan');
            $table->string('divisi')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('periode_mulai', 20)->nullable();
            $table->string('periode_selesai', 20)->nullable();
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();

            $table->index('is_aktif');
            $table->index(['periode_mulai', 'periode_selesai']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penguruses');
    }
};
