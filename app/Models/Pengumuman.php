<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pengumuman extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pengumumans';

    protected $fillable = [
        'judul',
        'slug',
        'isi',
        'gambar',
        'posisi_gambar',
        'kategori',
        'tgl_tayang',
        'tgl_kedaluwarsa',
        'is_pinned',
        'status',
        'user_id',
    ];

    protected $casts = [
        'tgl_tayang' => 'date',
        'tgl_kedaluwarsa' => 'date',
        'is_pinned' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeTerbitPublik($query)
    {
        $today = Carbon::today()->toDateString();

        return $query->where('status', 'terbit')
            ->whereDate('tgl_tayang', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('tgl_kedaluwarsa')
                    ->orWhereDate('tgl_kedaluwarsa', '>=', $today);
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('tgl_tayang')
            ->orderByDesc('id');
    }

    public function getFormattedTglTayangAttribute()
    {
        return $this->tgl_tayang ? $this->tgl_tayang->format('d-m-Y') : '-';
    }

    public function getFormattedTglKedaluwarsaAttribute()
    {
        return $this->tgl_kedaluwarsa ? $this->tgl_kedaluwarsa->format('d-m-Y') : '-';
    }
}
