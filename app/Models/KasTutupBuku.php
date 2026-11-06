<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KasTutupBuku extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_bulan',
        'tanggal_tutup',
        'saldo_awal',
        'total_debit',
        'total_kredit',
        'saldo_akhir',
        'status',
        'catatan',
        'user_id',
    ];

    protected $casts = [
        'tanggal_tutup' => 'date',
        'saldo_awal' => 'decimal:2',
        'total_debit' => 'decimal:2',
        'total_kredit' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Periksa apakah suatu bulan (format YYYY-MM) sedang berstatus TUTUP BUKU.
     */
    public static function isTutup(string $tahunBulan): bool
    {
        return static::where('tahun_bulan', $tahunBulan)
            ->where('status', 'tutup')
            ->exists();
    }

    /**
     * Ambil data rekaman tutup buku suatu bulan (jika ada).
     */
    public static function getBulan(string $tahunBulan): ?self
    {
        return static::where('tahun_bulan', $tahunBulan)->first();
    }
}
