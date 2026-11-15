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
     * Dapatkan kondisi lengkap presensi saat ini (sholat yang aktif atau terdekat berikutnya, fase waktu, dan target countdown)
     */
    public static function getKondisiPresensi(?Carbon $now = null): array
    {
        $now = $now ?: Carbon::now();
        $jamSekarang = $now->format('H:i:s');
        $today = $now->toDateString();

        if (static::count() < 5) {
            static::inisialisasiDefault();
        }

        $jadwals = static::all()->keyBy('nama_sholat');

        // 1. Cek jika ada sholat yang dibuka manual secara eksplisit oleh sekretaris
        $manualOpen = $jadwals->firstWhere('is_buka_manual', true);
        if ($manualOpen) {
            $fase = ($jamSekarang <= $manualOpen->jam_tepat) ? 'tepat' : 'telat';
            $targetWaktu = ($fase === 'tepat')
                ? Carbon::parse($today . ' ' . $manualOpen->jam_tepat)
                : Carbon::parse($today . ' ' . $manualOpen->jam_tutup);

            // Jika jam sekarang sudah melampaui jam tutup tapi status manual buka, target 15 menit ke depan
            if ($now->greaterThan($targetWaktu)) {
                $targetWaktu = $now->copy()->addMinutes(15);
            }

            return [
                'sholat' => $manualOpen,
                'is_bisa_absen' => true,
                'fase' => $fase,
                'target_waktu' => $targetWaktu,
                'keterangan_fase' => ($fase === 'tepat') ? 'Batas Presensi Tepat Waktu' : 'Form Ditutup Dalam',
                'pesan_status' => 'Presensi dibuka manual oleh sekretaris.',
            ];
        }

        // 2. Cek sholat yang saat ini sedang aktif (jam_buka <= now <= jam_tutup) dan tidak ditutup manual
        foreach ($jadwals as $j) {
            if ($j->is_buka_manual === false) {
                continue; // Ditutup paksa
            }

            if ($jamSekarang >= $j->jam_buka && $jamSekarang <= $j->jam_tutup) {
                $fase = ($jamSekarang <= $j->jam_tepat) ? 'tepat' : 'telat';
                $targetWaktu = ($fase === 'tepat')
                    ? Carbon::parse($today . ' ' . $j->jam_tepat)
                    : Carbon::parse($today . ' ' . $j->jam_tutup);

                return [
                    'sholat' => $j,
                    'is_bisa_absen' => true,
                    'fase' => $fase,
                    'target_waktu' => $targetWaktu,
                    'keterangan_fase' => ($fase === 'tepat') ? 'Batas Presensi Tepat Waktu' : 'Form Ditutup Dalam',
                    'pesan_status' => 'Form presensi sholat ' . ucfirst($j->nama_sholat) . ' sedang dibuka.',
                ];
            }
        }

        // 3. Jika tidak ada yang buka saat ini: Cari sholat terdekat berikutnya yang akan datang
        $sholatKeys = array_keys(self::SHOLAT_LIST);
        $sholatBerikutnya = null;
        $targetWaktu = null;

        foreach ($sholatKeys as $key) {
            $j = $jadwals->get($key);
            if (!$j || $j->is_buka_manual === false) {
                continue;
            }

            if ($j->jam_buka > $jamSekarang) {
                $sholatBerikutnya = $j;
                $targetWaktu = Carbon::parse($today . ' ' . $j->jam_buka);
                break;
            }
        }

        // Jika tidak ada lagi sholat hari ini yang jam_buka > now (misal sudah lewat Isya), sholat berikutnya adalah Subuh besok
        if (!$sholatBerikutnya) {
            $subuh = $jadwals->get('subuh');
            $sholatBerikutnya = $subuh;
            $targetWaktu = Carbon::parse($now->copy()->addDay()->toDateString() . ' ' . ($subuh ? $subuh->jam_buka : '04:30:00'));
        }

        return [
            'sholat' => $sholatBerikutnya,
            'is_bisa_absen' => false,
            'fase' => 'tutup',
            'target_waktu' => $targetWaktu,
            'keterangan_fase' => 'Form Sholat ' . ucfirst($sholatBerikutnya->nama_sholat) . ' Dibuka Dalam',
            'pesan_status' => 'Presensi sholat saat ini sedang ditutup. Form presensi ' . ucfirst($sholatBerikutnya->nama_sholat) . ' akan dibuka pada pukul ' . substr($sholatBerikutnya->jam_buka, 0, 5) . ' WIB.',
        ];
    }

    /**
     * Cek apakah jam saat ini masih dalam rentang boleh absen untuk jadwal ini
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
