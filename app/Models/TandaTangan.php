<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TandaTangan extends Model
{
    use HasFactory;

    protected $table = 'tanda_tangans';

    protected $fillable = [
        'surat_keluar_id',
        'urutan',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'data_ttd',
        'token_sekali_pakai',
        'waktu_ttd',
        'ip_address',
        'expires_at',
    ];

    protected $casts = [
        'waktu_ttd' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function suratKeluar(): BelongsTo
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isUsed(): bool
    {
        return !empty($this->waktu_ttd);
    }
}
