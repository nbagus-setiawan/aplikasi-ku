<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumumanKomentar extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_komentars';

    protected $fillable = [
        'pengumuman_id',
        'nama',
        'isi',
        'ip_address',
        'is_tampil',
    ];

    protected $casts = [
        'is_tampil' => 'boolean',
    ];

    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class);
    }

    /**
     * Nama yang ditampilkan di publik. Jika kosong, default ke 'User Publik'.
     */
    public function getNamaTampilAttribute(): string
    {
        return !empty(trim($this->nama ?? '')) ? trim($this->nama) : 'User Publik';
    }
}
