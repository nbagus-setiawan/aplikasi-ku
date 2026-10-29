<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class KasTransaksi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kas_transaksis';

    protected $fillable = [
        'kode_transaksi',
        'tanggal',
        'jenis',
        'kategori',
        'nominal',
        'keterangan',
        'nama_pihak',
        'lampiran',
        'user_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeDebit($query)
    {
        return $query->where('jenis', 'debit');
    }

    public function scopeKredit($query)
    {
        return $query->where('jenis', 'kredit');
    }

    public function getFormattedNominalAttribute(): string
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getFormattedTanggalAttribute(): string
    {
        return $this->tanggal ? $this->tanggal->format('d-m-Y') : '-';
    }

    public function getBadgeClassAttribute(): string
    {
        return $this->jenis === 'debit'
            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
            : 'bg-rose-50 text-rose-700 border-rose-200';
    }

    public function getJenisLabelAttribute(): string
    {
        return $this->jenis === 'debit' ? 'Debit (Kas Masuk)' : 'Kredit (Kas Keluar)';
    }

    /**
     * Generate Nomor Bukti Transaksi Kas secara urut per bulan/tahun.
     * BKM: Bukti Kas Masuk (Debit)
     * BKK: Bukti Kas Keluar (Kredit)
     */
    public static function generateKode(string $jenis, ?string $tanggal = null): string
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $prefix = ($jenis === 'debit') ? 'BKM' : 'BKK';
        $period = $date->format('Ym'); // cth: 202610

        $latest = self::withTrashed()
            ->where('kode_transaksi', 'like', "{$prefix}-{$period}-%")
            ->orderByDesc('id')
            ->value('kode_transaksi');

        $nextNumber = 1;
        if ($latest && preg_match("/{$prefix}-{$period}-(\d+)/", $latest, $matches)) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return sprintf('%s-%s-%04d', $prefix, $period, $nextNumber);
    }
}
