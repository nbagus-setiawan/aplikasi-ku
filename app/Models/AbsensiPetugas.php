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
        'pengurus_id',
        'keterangan',
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
}
