<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriSurat extends Model
{
    use HasFactory;

    protected $table = 'kategori_surats';

    protected $fillable = [
        'nama',
        'kode',
        'keterangan',
    ];

    /**
     * Cari kategori berdasarkan nama atau kode, atau buat baru secara otomatis jika belum ada.
     */
    public static function resolveOrCreate(?string $input): ?self
    {
        if ($input === null) {
            return null;
        }

        $input = trim($input);
        if ($input === '') {
            return null;
        }

        // 1. Cek kecocokan nama atau kode (case-insensitive)
        $kategori = static::whereRaw('LOWER(nama) = ?', [strtolower($input)])
            ->orWhereRaw('LOWER(kode) = ?', [strtolower($input)])
            ->first();

        if ($kategori) {
            return $kategori;
        }

        // 2. Jika input berupa kode (misal "SU.01", "SPb.02", atau satu kata singkatan tanpa spasi)
        if (!str_contains($input, ' ') && strlen($input) <= 10) {
            $kode = strtoupper($input);
            return static::firstOrCreate(
                ['kode' => $kode],
                ['nama' => $kode, 'keterangan' => 'Jenis surat ' . $kode]
            );
        }

        // 3. Buat kode singkatan unik dari nama jenis surat
        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', $input);
        $words = array_filter(explode(' ', (string) $clean));
        $prefix = '';
        foreach ($words as $w) {
            $prefix .= strtoupper($w[0]);
        }
        if (empty($prefix)) {
            $prefix = 'SRT';
        }
        $prefix = substr($prefix, 0, 5);

        // Cari nomor urut berikutnya agar kode unik
        $counter = 1;
        do {
            $candidateKode = sprintf('%s.%02d', $prefix, $counter);
            $exists = static::where('kode', $candidateKode)->exists();
            $counter++;
        } while ($exists && $counter <= 99);

        return static::create([
            'nama' => ucwords(strtolower($input)),
            'kode' => $candidateKode,
            'keterangan' => 'Jenis surat ' . ucwords(strtolower($input)),
        ]);
    }
}
