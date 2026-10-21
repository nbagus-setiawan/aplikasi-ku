<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            if (! Schema::hasColumn('surat_masuks', 'tempat_surat')) {
                $table->string('tempat_surat')->nullable()->after('tgl_diterima');
            }
            if (! Schema::hasColumn('surat_masuks', 'lampiran_teks')) {
                $table->string('lampiran_teks')->nullable()->default('-')->after('perihal');
            }
            if (! Schema::hasColumn('surat_masuks', 'file_surat_asli')) {
                $table->string('file_surat_asli')->nullable()->after('file');
            }
            if (! Schema::hasColumn('surat_masuks', 'file_lampiran')) {
                $table->string('file_lampiran')->nullable()->after('file_surat_asli');
            }
        });
    }

    public function down(): void
    {
        Schema::table('surat_masuks', function (Blueprint $table) {
            $cols = array_filter(
                ['tempat_surat', 'lampiran_teks', 'file_surat_asli', 'file_lampiran'],
                fn ($c) => Schema::hasColumn('surat_masuks', $c)
            );
            if (! empty($cols)) {
                $table->dropColumn($cols);
            }
        });
    }
};
