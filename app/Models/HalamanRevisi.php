<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HalamanRevisi extends Model
{
    use HasFactory;

    protected $table = 'halaman_revisis';

    protected $fillable = [
        'halaman_id',
        'konten',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'konten' => 'array',
    ];

    public function halaman()
    {
        return $this->belongsTo(Halaman::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
