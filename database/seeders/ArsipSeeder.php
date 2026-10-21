<?php

namespace Database\Seeders;

use App\Models\Arsip;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ArsipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $suratMasuk = SuratMasuk::first();
        $suratKeluar = SuratKeluar::first();

        // Pastikan direktori arsip ada dan buat berkas contoh
        Storage::disk('public')->makeDirectory('arsip');

        $sampleFile1 = 'arsip/sk-legalitas-pesantren.pdf';
        $sampleFile2 = 'arsip/mou-kerjasama-kesehatan.pdf';
        $sampleFile3 = 'arsip/piagam-penghargaan-mqk.pdf';

        if (! Storage::disk('public')->exists($sampleFile1)) {
            Storage::disk('public')->put($sampleFile1, "%PDF-1.4 Contoh berkas SK Legalitas Pondok Pesantren Al Falah Putra");
        }
        if (! Storage::disk('public')->exists($sampleFile2)) {
            Storage::disk('public')->put($sampleFile2, "%PDF-1.4 Contoh berkas MoU Kerjasama Layanan Kesehatan");
        }
        if (! Storage::disk('public')->exists($sampleFile3)) {
            Storage::disk('public')->put($sampleFile3, "%PDF-1.4 Contoh berkas Piagam Juara Umum MQK");
        }

        Arsip::create([
            'judul' => 'Surat Keputusan Izin Operasional Lembaga Pesantren',
            'kategori' => 'SK & Legalitas',
            'tahun' => 2026,
            'file' => $sampleFile1,
            'keterangan' => 'Dokumen izin operasional dari Kementerian Agama Republik Indonesia.',
            'surat_masuk_id' => $suratMasuk ? $suratMasuk->id : null,
            'surat_keluar_id' => null,
            'user_id' => $user ? $user->id : 1,
        ]);

        Arsip::create([
            'judul' => 'Perjanjian Kerjasama Pemeriksaan Berkala Kesehatan Santri',
            'kategori' => 'Perjanjian & MoU',
            'tahun' => 2026,
            'file' => $sampleFile2,
            'keterangan' => 'MoU antara Yayasan Pondok Pesantren Al Falah Putra dengan Puskesmas.',
            'surat_masuk_id' => null,
            'surat_keluar_id' => $suratKeluar ? $suratKeluar->id : null,
            'user_id' => $user ? $user->id : 1,
        ]);

        Arsip::create([
            'judul' => 'Piagam Penghargaan Juara Umum Musabaqah Qira\'atil Kutub (MQK)',
            'kategori' => 'Sertifikat & Piagam',
            'tahun' => 2025,
            'file' => $sampleFile3,
            'keterangan' => 'Arsip piagam kejuaraan tingkat provinsi.',
            'surat_masuk_id' => null,
            'surat_keluar_id' => null,
            'user_id' => $user ? $user->id : 1,
        ]);
    }
}
