<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agenda extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'agendas';

    protected $fillable = [
        'judul',
        'deskripsi',
        'mulai',
        'selesai',
        'lokasi',
        'pj_id',
        'status',
        'user_id',
    ];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
    ];

    public function pj(): BelongsTo
    {
        return $this->belongsTo(Pengurus::class, 'pj_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope agenda yang belum lewat / mendatang.
     */
    public function scopeMendatang(Builder $query): Builder
    {
        return $query->where('selesai', '>=', Carbon::now())
            ->whereIn('status', ['rencana', 'berjalan'])
            ->orderBy('mulai', 'asc');
    }

    /**
     * Scope agenda H-1 (besok hari).
     */
    public function scopeHMinusSatu(Builder $query): Builder
    {
        return $query->whereDate('mulai', Carbon::tomorrow())
            ->whereIn('status', ['rencana', 'berjalan'])
            ->orderBy('mulai', 'asc');
    }

    public function getFormattedMulaiAttribute(): string
    {
        return $this->mulai ? $this->mulai->format('d-m-Y H:i') : '-';
    }

    public function getFormattedSelesaiAttribute(): string
    {
        return $this->selesai ? $this->selesai->format('d-m-Y H:i') : '-';
    }
}
