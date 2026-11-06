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
        Schema::table('surat_uks', function (Blueprint $table) {
            $table->string('file_surat')->nullable()->after('jenis'); // Path berkas asli (PDF / Word / Gambar)
            $table->string('file_bukti_terjual')->nullable()->after('status'); // Path berkas bukti terjual jika ada
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_uks', function (Blueprint $table) {
            $table->dropColumn(['file_surat', 'file_bukti_terjual']);
        });
    }
};
