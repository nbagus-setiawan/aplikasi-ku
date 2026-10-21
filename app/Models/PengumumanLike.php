<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumumanLike extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_likes';

    protected $fillable = [
        'pengumuman_id',
        'ip_address',
        'user_agent',
    ];

    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class);
    }
}
