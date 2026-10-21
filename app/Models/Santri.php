<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Santri extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'santris';

    protected $fillable = [
        'nis',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_wali',
        'hp_wali',
        'kamar',
        'kelas',
        'tahun_masuk',
        'status',
        'foto',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    /**
     * Format tanggal lahir dd-mm-yyyy sesuai konvensi PRD.
     */
    public function getTanggalLahirFormattedAttribute(): ?string
    {
        return $this->tanggal_lahir ? Carbon::parse($this->tanggal_lahir)->format('d-m-Y') : null;
    }

    /**
     * Label status berwarna untuk badge UI.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'aktif' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'lulus' => 'bg-blue-100 text-blue-800 border-blue-200',
            'keluar' => 'bg-red-100 text-red-800 border-red-200',
            'pindah' => 'bg-amber-100 text-amber-800 border-amber-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}
