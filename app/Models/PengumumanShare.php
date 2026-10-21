<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PengumumanShare extends Model
{
    use HasFactory;

    protected $table = 'pengumuman_shares';

    protected $fillable = [
        'pengumuman_id',
        'platform',
        'ip_address',
    ];

    public function pengumuman(): BelongsTo
    {
        return $this->belongsTo(Pengumuman::class);
    }
}
