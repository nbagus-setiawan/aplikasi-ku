<?php

namespace App\Services;

use App\Models\NomorSuratCounter;
use App\Models\Pengaturan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class NomorSuratService
{
    /**
     * Konversi angka bulan (1-12) ke angka romawi.
     */
    public function toRomanMonth(int $month): string
    {
        $romans = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII',
        ];

        return $romans[$month] ?? 'I';
    }

    /**
     * Format nomor surat berdasarkan urutan, kode jenis, dan tanggal.
     */
    public function formatNomorSurat(int $urutan, string $kategoriKode, Carbon|string|null $tanggal = null): string
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $pola = Pengaturan::get('pola_nomor_surat', '{urut}/{jenis}/{lembaga}/{bulan}/{tahun}');
        $lembaga = Pengaturan::get('kode_lembaga', 'AF-PA');
        $bulanRomawi = $this->toRomanMonth((int) $date->format('n'));
        $tahun = $date->format('Y');
        $urutFormatted = str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);

        return strtr($pola, [
            '{urut}' => $urutFormatted,
            '{jenis}' => $kategoriKode,
            '{lembaga}' => $lembaga,
            '{bulan}' => $bulanRomawi,
            '{tahun}' => $tahun,
        ]);
    }

    /**
     * Buat nomor surat keluar berikutnya secara atomik dengan lock database.
     */
    public function generateNomorSuratKeluar(string $kategoriKode, Carbon|string|null $tanggal = null): string
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $tahun = (int) $date->format('Y');

        return DB::transaction(function () use ($kategoriKode, $date, $tahun) {
            $counter = NomorSuratCounter::where('tahun', $tahun)
                ->where('tipe', 'keluar')
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = NomorSuratCounter::create([
                    'tahun' => $tahun,
                    'tipe' => 'keluar',
                    'urutan' => 1,
                ]);
                $urutan = 1;
            } else {
                $counter->urutan += 1;
                $counter->save();
                $urutan = $counter->urutan;
            }

            return $this->formatNomorSurat($urutan, $kategoriKode, $date);
        });
    }

    /**
     * Buat nomor agenda surat masuk berikutnya secara atomik per tahun.
     */
    public function generateNomorAgenda(Carbon|string|null $tanggal = null): string
    {
        $date = $tanggal ? Carbon::parse($tanggal) : Carbon::now();
        $tahun = (int) $date->format('Y');

        return DB::transaction(function () use ($tahun) {
            $counter = NomorSuratCounter::where('tahun', $tahun)
                ->where('tipe', 'agenda_masuk')
                ->lockForUpdate()
                ->first();

            if (! $counter) {
                $counter = NomorSuratCounter::create([
                    'tahun' => $tahun,
                    'tipe' => 'agenda_masuk',
                    'urutan' => 1,
                ]);
                $urutan = 1;
            } else {
                $counter->urutan += 1;
                $counter->save();
                $urutan = $counter->urutan;
            }

            return str_pad((string) $urutan, 3, '0', STR_PAD_LEFT);
        });
    }
}
