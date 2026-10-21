<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratMasuk extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_masuks';

    protected $fillable = [
        'no_agenda',
        'no_surat',
        'tgl_surat',
        'tgl_diterima',
        'tempat_surat',
        'pengirim',
        'perihal',
        'lampiran_teks',
        'kategori_id',
        'file',
        'file_surat_asli',
        'file_lampiran',
        'disposisi',
        'status_disposisi',
        'user_id',
    ];

    protected $casts = [
        'tgl_surat' => 'date',
        'tgl_diterima' => 'date',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriSurat::class, 'kategori_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getFormattedTglSuratAttribute(): string
    {
        return $this->tgl_surat ? Carbon::parse($this->tgl_surat)->format('d-m-Y') : '-';
    }

    public function getFormattedTglDiterimaAttribute(): string
    {
        return $this->tgl_diterima ? Carbon::parse($this->tgl_diterima)->format('d-m-Y') : '-';
    }
}
