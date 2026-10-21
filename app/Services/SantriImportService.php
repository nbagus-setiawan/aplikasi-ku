<?php

namespace App\Services;

use App\Models\Santri;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Throwable;

class SantriImportService
{
    /**
     * Header kolom standar template impor santri.
     */
    public const HEADERS = [
        'nis',
        'nama',
        'tempat_lahir',
        'tanggal_lahir',
        'alamat',
        'nama_wali',
        'hp_wali',
        'kamar',
        'kelas',
        'tahun_masuk',
        'status',
    ];

    /**
     * Hasilkan isi file template CSV untuk diunduh.
     */
    public function generateTemplateCsv(): string
    {
        $rows = [
            self::HEADERS,
            [
                '2026001',
                'Ahmad Dahlan Putra',
                'Muara Enim',
                '2010-05-15',
                'Desa Putak, Kec. Gelumbang',
                'H. Sulaiman',
                '081234567890',
                'Kamar Abu Bakar 01',
                '1 Ula',
                '2026',
                'aktif',
            ],
            [
                '2026002',
                'Muhammad Ridho',
                'Prabumulih',
                '2011-08-20',
                'Jl. Jenderal Sudirman, Gelumbang',
                'H. Abdurrahman',
                '081398765432',
                'Kamar Umar 02',
                '1 Ula',
                '2026',
                'aktif',
            ],
        ];

        $output = fopen('php://temp', 'r+');
        // Tambahkan UTF-8 BOM agar rapi saat dibuka di Microsoft Excel
        fputs($output, "\xEF\xBB\xBF");

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Proses impor file data santri (CSV/Excel).
     */
    public function import(UploadedFile $file): array
    {
        $path = $file->getRealPath();

        $reader = Reader::createFromPath($path, 'r');
        $reader->setHeaderOffset(0);
        $reader->skipEmptyRecords();

        $records = $reader->getRecords();

        $successCount = 0;
        $failCount = 0;
        $failedRows = [];

        $rowNumber = 1; // baris 1 adalah header

        foreach ($records as $record) {
            $rowNumber++;

            // Normalisasi key record (lowercase dan trim)
            $row = [];
            foreach ($record as $key => $val) {
                $cleanKey = strtolower(trim((string) $key));
                $row[$cleanKey] = trim((string) $val);
            }

            $nis = $row['nis'] ?? '';
            $nama = $row['nama'] ?? ($row['nama_lengkap'] ?? '');

            // Validasi baris
            if (empty($nis)) {
                $failCount++;
                $failedRows[] = [
                    'baris' => $rowNumber,
                    'nis' => '-',
                    'nama' => $nama ?: '-',
                    'alasan' => 'Kolom NIS kosong atau tidak ditemukan.',
                ];
                continue;
            }

            if (empty($nama)) {
                $failCount++;
                $failedRows[] = [
                    'baris' => $rowNumber,
                    'nis' => $nis,
                    'nama' => '-',
                    'alasan' => 'Kolom nama santri kosong.',
                ];
                continue;
            }

            // Cek apakah NIS sudah ada di database
            if (Santri::where('nis', $nis)->exists()) {
                $failCount++;
                $failedRows[] = [
                    'baris' => $rowNumber,
                    'nis' => $nis,
                    'nama' => $nama,
                    'alasan' => "NIS '{$nis}' sudah terdaftar dalam sistem (duplikat).",
                ];
                continue;
            }

            // Normalisasi status
            $status = strtolower($row['status'] ?? 'aktif');
            if (!in_array($status, ['aktif', 'lulus', 'keluar', 'pindah'])) {
                $status = 'aktif';
            }

            // Normalisasi tanggal lahir (dukung Y-m-d dan d-m-Y)
            $tanggalLahir = null;
            if (!empty($row['tanggal_lahir'])) {
                try {
                    $tanggalLahir = Carbon::parse($row['tanggal_lahir'])->format('Y-m-d');
                } catch (Throwable) {
                    $tanggalLahir = null;
                }
            }

            try {
                Santri::create([
                    'nis' => $nis,
                    'nama' => $nama,
                    'tempat_lahir' => $row['tempat_lahir'] ?? null,
                    'tanggal_lahir' => $tanggalLahir,
                    'alamat' => $row['alamat'] ?? null,
                    'nama_wali' => $row['nama_wali'] ?? null,
                    'hp_wali' => $row['hp_wali'] ?? ($row['no_hp_wali'] ?? null),
                    'kamar' => $row['kamar'] ?? null,
                    'kelas' => $row['kelas'] ?? null,
                    'tahun_masuk' => $row['tahun_masuk'] ?? date('Y'),
                    'status' => $status,
                ]);

                $successCount++;
            } catch (Throwable $e) {
                $failCount++;
                $failedRows[] = [
                    'baris' => $rowNumber,
                    'nis' => $nis,
                    'nama' => $nama,
                    'alasan' => 'Gagal menyimpan ke database: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'total' => $successCount + $failCount,
            'sukses' => $successCount,
            'gagal' => $failCount,
            'gagal_list' => $failedRows,
        ];
    }
}
