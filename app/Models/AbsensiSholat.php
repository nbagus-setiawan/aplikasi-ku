<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiSholat extends Model
{
    use HasFactory;

    protected $table = 'absensi_sholats';

    protected $fillable = [
        'tanggal',
        'waktu_sholat',
        'petugas_id',
        'koordinator_id',
        'jam_absen',
        'status',
        'kategori_waktu',
        'keterangan',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Relasi ke Petugas yang diabsen
     */
    public function petugas()
    {
        return $this->belongsTo(AbsensiPetugas::class, 'petugas_id');
    }

    /**
     * Relasi ke Koordinator yang mencatat absen
     */
    public function koordinator()
    {
        return $this->belongsTo(AbsensiPetugas::class, 'koordinator_id');
    }

    /**
     * Scope filter tanggal
     */
    public function scopeTanggal($query, $tanggal)
    {
        return $query->whereDate('tanggal', $tanggal);
    }

    /**
     * Scope filter sholat
     */
    public function scopeSholat($query, $sholat)
    {
        return $query->where('waktu_sholat', $sholat);
    }

    /**
     * Scope filter status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope filter bulan (format Y-m)
     */
    public function scopeBulan($query, $yearMonth)
    {
        return $query->where('tanggal', 'like', $yearMonth . '-%');
    }

    /**
     * Otomatis membuat record ALFA bagi petugas korlap yang belum absen untuk waktu sholat yang telah ditutup hari ini
     */
    public static function autoAlfaPetugasYangBelumAbsen(?\Carbon\Carbon $now = null): int
    {
        $now = $now ?: \Carbon\Carbon::now();
        $today = $now->toDateString();
        $jamSekarang = $now->format('H:i:s');

        AbsensiSholatJadwal::inisialisasiDefault();
        $jadwals = AbsensiSholatJadwal::all()->keyBy('nama_sholat');

        $totalCreated = 0;

        foreach ($jadwals as $namaSholat => $j) {
            // Waktu sholat dianggap ditutup jika:
            // 1. Ditutup manual (is_buka_manual === false)
            // 2. ATAU jam sekarang sudah melewati jam_tutup dan tidak dibuka manual (is_buka_manual !== true)
            $isTutup = ($j->is_buka_manual === false) || ($j->is_buka_manual !== true && $jamSekarang > $j->jam_tutup);

            if (!$isTutup) {
                continue;
            }

            // Ambil semua petugas aktif yang bertugas pada sholat ini
            $petugasList = AbsensiPetugas::aktif()->untukSholat($namaSholat)->get();
            if ($petugasList->isEmpty()) {
                continue;
            }

            // Ambil ID petugas yang sudah punya catatan presensi sholat ini pada hari ini
            $sudahAbsenIds = static::whereDate('tanggal', $today)
                ->where('waktu_sholat', $namaSholat)
                ->pluck('petugas_id')
                ->all();

            $belumAbsen = $petugasList->whereNotIn('id', $sudahAbsenIds);

            foreach ($belumAbsen as $p) {
                static::create([
                    'tanggal' => $today,
                    'waktu_sholat' => $namaSholat,
                    'petugas_id' => $p->id,
                    'koordinator_id' => null,
                    'jam_absen' => $j->jam_tutup,
                    'status' => 'alfa',
                    'kategori_waktu' => null,
                    'keterangan' => 'Otomatis Alfa (Tidak mengisi presensi hingga batas waktu tutup)',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'System Auto-Alfa',
                ]);
                $totalCreated++;
            }
        }

        return $totalCreated;
    }
}
