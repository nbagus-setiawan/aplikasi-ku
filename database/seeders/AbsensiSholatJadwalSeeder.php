<?php

namespace Database\Seeders;

use App\Models\AbsensiSholatJadwal;
use Illuminate\Database\Seeder;

class AbsensiSholatJadwalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AbsensiSholatJadwal::inisialisasiDefault();
    }
}
