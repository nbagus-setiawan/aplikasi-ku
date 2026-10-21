<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Arsip extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'arsips';

    protected $fillable = [
        'judul',
        'kategori',
        'tahun',
        'file',
        'keterangan',
        'surat_masuk_id',
        'surat_keluar_id',
        'user_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function suratMasuk(): BelongsTo
    {
        return $this->belongsTo(SuratMasuk::class, 'surat_masuk_id');
    }

    public function suratKeluar(): BelongsTo
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_keluar_id');
    }

    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->file, PATHINFO_EXTENSION));
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->file_extension === 'pdf';
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->file_extension, ['jpg', 'jpeg', 'png'], true);
    }
}
