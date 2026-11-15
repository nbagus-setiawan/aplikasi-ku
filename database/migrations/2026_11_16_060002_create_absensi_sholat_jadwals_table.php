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
        Schema::create('absensi_sholat_jadwals', function (Blueprint $table) {
            $table->id();
            $table->enum('nama_sholat', ['subuh', 'dzuhur', 'ashar', 'maghrib', 'isya'])->unique();
            $table->time('jam_buka');
            $table->time('jam_tepat');
            $table->time('jam_tutup');
            $table->boolean('is_buka_manual')->nullable()->comment('null=otomatis ikuti jam, true=buka paksa, false=tutup paksa');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_sholat_jadwals');
    }
};
