<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Halaman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'halamans';

    protected $fillable = [
        'slug',
        'judul',
        'konten',
        'status',
        'is_beranda',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'konten' => 'array',
        'is_beranda' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function revisis()
    {
        return $this->hasMany(HalamanRevisi::class)->latest();
    }

    public function scopeBeranda($query)
    {
        return $query->where('is_beranda', true);
    }

    public function scopeTerbit($query)
    {
        return $query->where('status', 'terbit');
    }
}
