<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notifikasi extends Model
{
    use HasFactory;

    protected $table = 'notifikasis';

    protected $fillable = [
        'judul',
        'pesan',
        'tipe',
        'pengumuman_id',
        'link',
        'data_tambahan',
        'is_dibaca',
        'dibaca_pada',
    ];

    protected $casts = [
        'is_dibaca' => 'boolean',
        'dibaca_pada' => 'datetime',
        'data_tambahan' => 'array',
    ];

    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class);
    }

    public function scopeBelumDibaca($query)
    {
        return $query->where('is_dibaca', false);
    }

    public function scopeTerbaru($query)
    {
        return $query->orderByDesc('created_at');
    }

    public function tandaiDibaca(): void
    {
        $this->update([
            'is_dibaca' => true,
            'dibaca_pada' => now(),
        ]);
    }
}
