<?php

namespace App\Console\Commands;

use App\Models\AbsensiSholat;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AbsensiAutoAlfaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'absensi:auto-alfa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Otomatis menandai Alfa bagi petugas korlap yang belum mengisi presensi sholat setelah waktu sholat ditutup';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();
        $this->info("Menjalankan pemeriksaan otomatis Alfa presensi korlap pada {$now->toDateTimeString()}...");

        $total = AbsensiSholat::autoAlfaPetugasYangBelumAbsen($now);

        // Periksa juga hari kemarin jika ada sholat malam yang belum tertutup
        $yesterday = $now->copy()->subDay();
        $total += AbsensiSholat::autoAlfaPetugasYangBelumAbsen($yesterday);

        if ($total > 0) {
            $this->info("Berhasil mencatat {$total} petugas sebagai Alfa otomatis.");
        } else {
            $this->info("Tidak ada petugas yang perlu di-alfa-kan saat ini.");
        }

        return Command::SUCCESS;
    }
}
