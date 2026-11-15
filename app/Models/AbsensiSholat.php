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
}
