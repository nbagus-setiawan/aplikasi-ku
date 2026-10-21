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
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->string('ukuran_kertas', 20)->default('a4')->after('perihal');
            $table->string('margin', 50)->default('standar')->after('ukuran_kertas');
            $table->unsignedTinyInteger('jumlah_penandatangan')->default(1)->after('jabatan_penandatangan');
            $table->json('penandatangans')->nullable()->after('jumlah_penandatangan');
        });

        Schema::table('tanda_tangans', function (Blueprint $table) {
            $table->unsignedTinyInteger('urutan')->default(1)->after('surat_keluar_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            $table->dropColumn(['ukuran_kertas', 'margin', 'jumlah_penandatangan', 'penandatangans']);
        });

        Schema::table('tanda_tangans', function (Blueprint $table) {
            $table->dropColumn(['urutan']);
        });
    }
};
