<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProduksiSeeder extends Seeder
{
    /**
     * Seeder khusus publikasi / produksi (TANPA data contoh / dummy).
     * Hanya mengisi Akun Sekretaris (dari .env), Pengaturan Pesantren,
     * Kategori Jenis Surat, Template Surat, dan Tata Letak Beranda.
     */
    public function run(): void
    {
        $this->call([
            SekretarisSeeder::class,
            PengaturanSeeder::class,
            KategoriSuratSeeder::class,
            TemplateSuratSeeder::class,
            HalamanSeeder::class,
        ]);
    }
}
