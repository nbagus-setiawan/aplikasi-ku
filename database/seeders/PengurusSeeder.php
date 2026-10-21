<?php

namespace Database\Seeders;

use App\Models\Pengurus;
use Illuminate\Database\Seeder;

class PengurusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $penguruses = [
            [
                'nama' => 'KH. Nur Syahid Ramli, Lc.',
                'jabatan' => 'Pimpinan Pondok Pesantren',
                'divisi' => 'Pimpinan & Pengasuh',
                'no_hp' => '08125001001',
                'periode_mulai' => '2024',
                'periode_selesai' => '2028',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Ust. H. Fauzan Manshur',
                'jabatan' => 'Kepala Sekretariat & Tata Usaha',
                'divisi' => 'Pimpinan & Sekretariat',
                'no_hp' => '08125001002',
                'periode_mulai' => '2025',
                'periode_selesai' => '2027',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Ust. Ahmad Baihaki, S.Pd.I',
                'jabatan' => 'Koordinator Bidang Kesantrian',
                'divisi' => 'Bidang Kesantrian & Disiplin',
                'no_hp' => '08134002003',
                'periode_mulai' => '2025',
                'periode_selesai' => '2027',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Ust. Muhammad Ridwan',
                'jabatan' => 'Koordinator Keamanan & Ketertiban',
                'divisi' => 'Bidang Keamanan',
                'no_hp' => '08523004005',
                'periode_mulai' => '2025',
                'periode_selesai' => '2027',
                'is_aktif' => true,
            ],
            [
                'nama' => 'Ust. Syarifuddin, S.Ag',
                'jabatan' => 'Pengurus Bidang Sarana & Asrama',
                'divisi' => 'Bidang Sarana Prasarana',
                'no_hp' => '08532005006',
                'periode_mulai' => '2024',
                'periode_selesai' => '2026',
                'is_aktif' => true,
            ],
        ];

        foreach ($penguruses as $p) {
            Pengurus::updateOrCreate(['nama' => $p['nama'], 'jabatan' => $p['jabatan']], $p);
        }
    }
}
