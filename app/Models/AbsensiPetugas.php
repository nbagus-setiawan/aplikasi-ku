<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AbsensiPetugas extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'absensi_petugas';

    protected $fillable = [
        'nama',
        'bagian',
        'status',
        'jadwal_sholat',
        'pengurus_id',
        'keterangan',
    ];

    protected $casts = [
        'jadwal_sholat' => 'array',
    ];

    /**
     * Relasi opsional ke tabel Pengurus jika petugas diambil dari data pengurus
     */
    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class, 'pengurus_id');
    }

    /**
     * Relasi ke riwayat absensi sholat petugas
     */
    public function absensis()
    {
        return $this->hasMany(AbsensiSholat::class, 'petugas_id');
    }

    /**
     * Scope untuk petugas aktif saja
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope koordinator
     */
    public function scopeKoordinator($query)
    {
        return $query->where('bagian', 'koordinator');
    }

    /**
     * Scope anggota
     */
    public function scopeAnggota($query)
    {
        return $query->where('bagian', 'anggota');
    }

    /**
     * Scope petugas yang ditugaskan pada waktu sholat tertentu (atau bertugas di semua sholat jika null)
     */
    public function scopeUntukSholat($query, ?string $sholat)
    {
        if (!$sholat) {
            return $query;
        }

        return $query->where(function ($q) use ($sholat) {
            $q->whereNull('jadwal_sholat')
              ->orWhere('jadwal_sholat', '[]')
              ->orWhereJsonContains('jadwal_sholat', $sholat);
        });
    }

    /**
     * Cek apakah petugas bertugas pada waktu sholat tertentu
     */
    public function isBertugasPadaSholat(?string $sholat): bool
    {
        if (!$sholat) {
            return true;
        }

        if (empty($this->jadwal_sholat)) {
            return true; // Default bertugas di semua waktu sholat
        }

        return in_array($sholat, (array)$this->jadwal_sholat, true);
    }
}
