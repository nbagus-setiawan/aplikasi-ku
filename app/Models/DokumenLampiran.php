<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class DokumenLampiran extends Model
{
    protected $guarded = [];

    /**
     * Relasi polymorphic ke model pemilik (SuratMasuk, SuratKeluar, Arsip).
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Format ukuran berkas (KB / MB).
     */
    public function getUkuranFormattedAttribute(): string
    {
        $bytes = (int) $this->ukuran_byte;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2, ',', '.') . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 0, ',', '.') . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Dapatkan ekstensi file.
     */
    public function getEkstensiAttribute(): string
    {
        return strtolower(pathinfo($this->path_berkas ?: $this->nama_berkas, PATHINFO_EXTENSION));
    }

    /**
     * Badge tampilan tipe berkas.
     */
    public function getTipeBadgeAttribute(): array
    {
        $ext = $this->ekstensi;
        if ($ext === 'pdf') {
            return ['label' => 'PDF', 'class' => 'bg-rose-50 text-rose-700 border border-rose-200'];
        }
        if (in_array($ext, ['doc', 'docx'])) {
            return ['label' => 'DOCX', 'class' => 'bg-blue-50 text-blue-700 border border-blue-200'];
        }
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
            return ['label' => 'GAMBAR', 'class' => 'bg-emerald-50 text-emerald-700 border border-emerald-200'];
        }
        return ['label' => strtoupper($ext ?: 'BERKAS'), 'class' => 'bg-slate-50 text-slate-700 border border-slate-200'];
    }

    public function isPdf(): bool
    {
        return $this->ekstensi === 'pdf';
    }

    public function isWord(): bool
    {
        return in_array($this->ekstensi, ['doc', 'docx']);
    }

    public function isImage(): bool
    {
        return in_array($this->ekstensi, ['jpg', 'jpeg', 'png', 'webp']);
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path_berkas);
    }

    /**
     * Hapus berkas fisik di disk penyimpanan.
     */
    public function hapusBerkasFisik(): void
    {
        if ($this->path_berkas && Storage::disk('public')->exists($this->path_berkas)) {
            Storage::disk('public')->delete($this->path_berkas);
        }
    }
}
