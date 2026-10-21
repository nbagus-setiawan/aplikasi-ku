<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class SuratKeluar extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_keluars';

    protected $fillable = [
        'no_surat',
        'tgl_surat',
        'tempat_surat',
        'tujuan',
        'perihal',
        'lampiran_teks',
        'kategori_id',
        'isi',
        'nama_penandatangan',
        'jabatan_penandatangan',
        'ukuran_kertas',
        'margin',
        'jumlah_penandatangan',
        'penandatangans',
        'file',
        'file_surat_asli',
        'file_final',
        'status',
        'metode_ttd',
        'status_ttd',
        'kode_verifikasi',
        'user_id',
    ];

    protected $casts = [
        'tgl_surat' => 'date',
        'penandatangans' => 'array',
        'jumlah_penandatangan' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriSurat::class, 'kategori_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tandaTangan(): HasOne
    {
        return $this->hasOne(TandaTangan::class, 'surat_keluar_id')->orderBy('urutan', 'asc');
    }

    public function tandaTangans(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TandaTangan::class, 'surat_keluar_id')->orderBy('urutan', 'asc');
    }

    public function isFinal(): bool
    {
        return in_array($this->status, ['final', 'terkirim'], true);
    }

    public function isLocked(): bool
    {
        return $this->isFinal();
    }

    public function getFormattedTglSuratAttribute(): string
    {
        return $this->tgl_surat ? Carbon::parse($this->tgl_surat)->format('d-m-Y') : '-';
    }
}
