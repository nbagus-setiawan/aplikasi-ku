<?php

namespace Database\Seeders;

use App\Models\Santri;
use Illuminate\Database\Seeder;

class SantriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $santris = [
            [
                'nis' => '2026001',
                'nama' => 'Muhammad Azhar Pratama',
                'tempat_lahir' => 'Gelumbang',
                'tanggal_lahir' => '2010-04-12',
                'alamat' => 'Desa Putak, Kec. Gelumbang, Kab. Muara Enim',
                'nama_wali' => 'H. Muhammad Arsyad',
                'hp_wali' => '081234567801',
                'kamar' => 'Dawami',
                'kelas' => '1 Ula',
                'tahun_masuk' => '2026',
                'status' => 'aktif',
            ],
            [
                'nis' => '2026002',
                'nama' => 'Ahmad Fathoni Al-Hafidz',
                'tempat_lahir' => 'Muara Enim',
                'tanggal_lahir' => '2010-08-19',
                'alamat' => 'Jl. Jenderal Sudirman No. 45, Muara Enim',
                'nama_wali' => 'Ust. Zulkifli',
                'hp_wali' => '081398765402',
                'kamar' => 'Abu Bakar 01',
                'kelas' => '1 Ula',
                'tahun_masuk' => '2026',
                'status' => 'aktif',
            ],
            [
                'nis' => '2025010',
                'nama' => 'Rizky Firmansyah',
                'tempat_lahir' => 'Prabumulih',
                'tanggal_lahir' => '2009-02-15',
                'alamat' => 'Jl. Padat Karya, Prabumulih Timur',
                'nama_wali' => 'Bambang Sutejo',
                'hp_wali' => '085211223303',
                'kamar' => 'Umar 02',
                'kelas' => '2 Wustho',
                'tahun_masuk' => '2025',
                'status' => 'aktif',
            ],
            [
                'nis' => '2024005',
                'nama' => 'Zainal Abidin',
                'tempat_lahir' => 'Palembang',
                'tanggal_lahir' => '2008-11-20',
                'alamat' => 'Jl. Demang Lebar Daun, Palembang',
                'nama_wali' => 'H. Fahrurrazi',
                'hp_wali' => '085344556604',
                'kamar' => 'Utsman 03',
                'kelas' => '3 Ulya',
                'tahun_masuk' => '2024',
                'status' => 'lulus',
            ],
            [
                'nis' => '2025022',
                'nama' => 'Fahmi Khairullah',
                'tempat_lahir' => 'Indralaya',
                'tanggal_lahir' => '2009-06-05',
                'alamat' => 'Desa Tanjung Raja, Kab. Ogan Ilir',
                'nama_wali' => 'Drs. H. Mulyadi',
                'hp_wali' => '081277889905',
                'kamar' => 'Ali 04',
                'kelas' => '2 Wustho',
                'tahun_masuk' => '2025',
                'status' => 'aktif',
            ],
        ];

        foreach ($santris as $s) {
            Santri::updateOrCreate(['nis' => $s['nis']], $s);
        }
    }
}
