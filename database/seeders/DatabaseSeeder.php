<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Selalu jalankan seeder sistem inti (Akun Sekretaris dari .env, Pengaturan, Kategori, Template, Beranda)
        $this->call([
            ProduksiSeeder::class,
        ]);

        // 2. Data contoh (dummy) hanya dijalankan jika bukan production dan SEED_DUMMY_DATA bernilai true
        $seedDummy = filter_var(env('SEED_DUMMY_DATA', !app()->environment('production')), FILTER_VALIDATE_BOOLEAN);

        if ($seedDummy) {
            $this->call([
                SantriSeeder::class,
                PengurusSeeder::class,
                SuratMasukSeeder::class,
                SuratKeluarSeeder::class,
                AgendaSeeder::class,
                ArsipSeeder::class,
                PengumumanSeeder::class,
            ]);
        }
    }
}
