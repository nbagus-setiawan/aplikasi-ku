<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSholatJadwal extends Model
{
    use HasFactory;

    protected $table = 'absensi_sholat_jadwals';

    protected $fillable = [
        'nama_sholat',
        'jam_buka',
        'jam_tepat',
        'jam_tutup',
        'is_buka_manual',
    ];

    protected $casts = [
        'is_buka_manual' => 'boolean',
    ];

    /**
     * Daftar nama sholat standar
     */
    public const SHOLAT_LIST = [
        'subuh' => 'Subuh',
        'dzuhur' => 'Dzuhur',
        'ashar' => 'Ashar',
        'maghrib' => 'Maghrib',
        'isya' => 'Isya',
    ];

    /**
     * Inisialisasi 5 jadwal sholat default jika belum ada di database
     */
    public static function inisialisasiDefault(): void
    {
        $defaults = [
            'subuh'   => ['jam_buka' => '04:30:00', 'jam_tepat' => '05:00:00', 'jam_tutup' => '05:30:00'],
            'dzuhur'  => ['jam_buka' => '12:00:00', 'jam_tepat' => '12:30:00', 'jam_tutup' => '13:00:00'],
            'ashar'   => ['jam_buka' => '15:15:00', 'jam_tepat' => '15:45:00', 'jam_tutup' => '16:15:00'],
            'maghrib' => ['jam_buka' => '18:00:00', 'jam_tepat' => '18:30:00', 'jam_tutup' => '19:00:00'],
            'isya'    => ['jam_buka' => '19:20:00', 'jam_tepat' => '19:50:00', 'jam_tutup' => '20:20:00'],
        ];

        foreach ($defaults as $nama => $times) {
            static::firstOrCreate(
                ['nama_sholat' => $nama],
                [
                    'jam_buka' => $times['jam_buka'],
                    'jam_tepat' => $times['jam_tepat'],
                    'jam_tutup' => $times['jam_tutup'],
                    'is_buka_manual' => null,
                ]
            );
        }
    }

    /**
     * Cari sholat yang sedang aktif pada jam sekarang
     */
    public static function getSholatAktifSaatIni(?Carbon $now = null): ?self
    {
        $now = $now ?: Carbon::now();
        $jamSekarang = $now->format('H:i:s');

        // Pastikan jadwal default ada
        if (static::count() < 5) {
            static::inisialisasiDefault();
        }

        $jadwals = static::all();

        // 1. Cek jika ada sholat yang dibuka manual secara eksplisit
        $manualOpen = $jadwals->firstWhere('is_buka_manual', true);
        if ($manualOpen) {
            return $manualOpen;
        }

        // 2. Cek sholat yang jam_buka <= now <= jam_tutup dan tidak ditutup manual
        foreach ($jadwals as $j) {
            if ($j->is_buka_manual === false) {
                continue; // Ditutup paksa secara manual oleh sekretaris
            }

            if ($jamSekarang >= $j->jam_buka && $jamSekarang <= $j->jam_tutup) {
                return $j;
            }
        }

        // 3. Fallback: Cari sholat terdekat berikutnya atau sebelumnya jika tidak ada yang pas aktif
        return null;
    }

    /**
     * Tentukan kategori waktu saat ini: 'tepat_waktu' atau 'terlambat'
     */
    public function getKategoriWaktu(?Carbon $now = null): string
    {
        $now = $now ?: Carbon::now();
        $jamSekarang = $now->format('H:i:s');

        if ($jamSekarang <= $this->jam_tepat) {
            return 'tepat_waktu';
        }

        return 'terlambat';
    }

    /**
     * Cek apakah jam saat ini masih dalam rentang boleh absen
     */
    public function isBisaAbsen(?Carbon $now = null): bool
    {
        if ($this->is_buka_manual === true) {
            return true;
        }
        if ($this->is_buka_manual === false) {
            return false;
        }

        $now = $now ?: Carbon::now();
        $jamSekarang = $now->format('H:i:s');

        return $jamSekarang >= $this->jam_buka && $jamSekarang <= $this->jam_tutup;
    }
}
