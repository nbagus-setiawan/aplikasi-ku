<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SuratUks extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'surat_uks';

    protected $fillable = [
        'file_surat',
        'file_bukti_terjual',
        'nomor_surat',
        'kode_verifikasi',
        'jenis',
        'tanggal_cetak',
        'tanggal_pembelian',
        'santri_id',
        'nama_santri',
        'kelas',
        'asrama',
        'keperluan',
        'diagnosa',
        'harga',
        'status',
        'tanggal_kembali_riil',
        'kas_transaksi_id',
        'user_id',
    ];

    protected $casts = [
        'tanggal_cetak' => 'date',
        'tanggal_pembelian' => 'date',
        'tanggal_kembali_riil' => 'datetime',
        'harga' => 'decimal:2',
    ];

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_surat ? asset('storage/' . $this->file_surat) : null;
    }

    public function getFileBuktiUrlAttribute(): ?string
    {
        return $this->file_bukti_terjual ? asset('storage/' . $this->file_bukti_terjual) : null;
    }

    public function getFileExtAttribute(): string
    {
        return $this->file_surat ? strtolower(pathinfo($this->file_surat, PATHINFO_EXTENSION)) : '';
    }

    public function getIsPdfAttribute(): bool
    {
        return $this->file_ext === 'pdf';
    }

    public function getIsImageAttribute(): bool
    {
        return in_array($this->file_ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'], true);
    }

    public function getIsWordAttribute(): bool
    {
        return in_array($this->file_ext, ['doc', 'docx'], true);
    }

    /**
     * Relasi ke data santri (jika dihubungkan).
     */
    public function santri(): BelongsTo
    {
        return $this->belongsTo(Santri::class, 'santri_id');
    }

    /**
     * Relasi ke pembuat surat (sekretaris/user).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke kas transaksi bendahara.
     */
    public function kasTransaksi(): BelongsTo
    {
        return $this->belongsTo(KasTransaksi::class, 'kas_transaksi_id');
    }

    /**
     * Scope surat izin pulang.
     */
    public function scopePulang(Builder $query): Builder
    {
        return $query->where('jenis', 'pulang');
    }

    /**
     * Scope surat izin sakit.
     */
    public function scopeSakit(Builder $query): Builder
    {
        return $query->where('jenis', 'sakit');
    }

    /**
     * Scope surat blangko stok tersedia.
     */
    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('status', 'tersedia');
    }

    /**
     * Scope surat yang telah terjual / terpakai.
     */
    public function scopeTerjual(Builder $query): Builder
    {
        return $query->where('status', 'terjual');
    }

    /**
     * Scope surat yang sudah kembali ke pondok.
     */
    public function scopeKembali(Builder $query): Builder
    {
        return $query->where('status', 'kembali');
    }

    /**
     * Label jenis izin.
     */
    public function getJenisLabelAttribute(): string
    {
        return match ($this->jenis) {
            'sakit' => 'Surat Izin Sakit',
            default => 'Surat Izin Pulang',
        };
    }

    /**
     * Format harga.
     */
    public function getFormattedHargaAttribute(): string
    {
        return 'Rp ' . number_format($this->harga ?? 5000, 0, ',', '.');
    }

    /**
     * Generate 4-digit unique verification code.
     */
    public static function generateKodeVerifikasi(): string
    {
        $pool = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        do {
            $code = '';
            for ($i = 0; $i < 4; $i++) {
                $code .= $pool[random_int(0, strlen($pool) - 1)];
            }
        } while (self::withTrashed()->where('kode_verifikasi', $code)->exists());

        return $code;
    }
}
