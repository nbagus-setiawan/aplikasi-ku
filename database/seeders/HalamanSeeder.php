<?php

namespace Database\Seeders;

use App\Models\Halaman;
use App\Models\HalamanRevisi;
use App\Models\User;
use App\Services\HalamanBlockService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HalamanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first();
        $blockService = new HalamanBlockService();
        $blocks = $blockService->getDefaultBerandaBlocks();

        $halaman = Halaman::updateOrCreate(
            ['is_beranda' => true],
            [
                'slug' => 'beranda',
                'judul' => 'Beranda Utama Pondok Pesantren Al-Falah Putra',
                'konten' => $blocks,
                'status' => 'terbit',
                'published_at' => Carbon::now(),
                'user_id' => $user?->id,
            ]
        );

        // Buat revisi awal
        HalamanRevisi::create([
            'halaman_id' => $halaman->id,
            'konten' => $blocks,
            'catatan' => 'Inisialisasi susunan beranda resmi bawaan',
            'user_id' => $user?->id,
        ]);
    }
}
