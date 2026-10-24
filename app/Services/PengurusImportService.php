<?php

namespace App\Services;

use App\Models\Pengurus;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use League\Csv\Reader;
use Throwable;

class PengurusImportService
{
    /**
     * Header kolom standar template impor pengurus.
     */
    public const HEADERS = [
        'nama',
        'jabatan',
        'divisi',
        'no_hp',
        'periode_mulai',
        'periode_selesai',
        'is_aktif',
    ];

    /**
     * Hasilkan isi file template CSV untuk diunduh.
     */
    public function generateTemplateCsv(): string
    {
        $rows = [
            self::HEADERS,
            [
                'Ust. H. Ahmad Dahlan, S.Pd.I',
                'Ketua Pengurus',
                'Pimpinan & Sekretariat',
                '081234567890',
                '2026',
                '2030',
                'aktif',
            ],
            [
                'Ust. Muhammad Ridho, M.Ag',
                'Sekretaris Pondok',
                'Pimpinan & Sekretariat',
                '081398765432',
                '2026',
                '2030',
                'aktif',
            ],
            [
                'Ust. Syamsul Huda',
                'Kepala Keamanan & Ketertiban',
                'Keamanan',
                '081277889900',
                '2026',
                '2030',
                'aktif',
            ],
            [
                'Ust. Zulkifli Rahman',
                'Koordinator Kebersihan Lingkungan',
                'Kebersihan',
                '085211223344',
                '2025',
                '2026',
                'nonaktif',
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
     * Hasilkan file CSV ekspor data pengurus berdasarkan filter aktif atau seluruh data.
     */
    public function exportCsv(array $filters = []): string
    {
        $query = Pengurus::query();

        if (! empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_aktif']) && $filters['is_aktif'] !== '') {
            $query->where('is_aktif', (bool) $filters['is_aktif']);
        }

        if (! empty($filters['periode'])) {
            $query->where('periode_mulai', $filters['periode']);
        }

        $penguruses = $query->orderBy('is_aktif', 'desc')
                            ->orderBy('id', 'asc')
                            ->get();

        $output = fopen('php://temp', 'r+');
        // UTF-8 BOM
        fputs($output, "\xEF\xBB\xBF");

        // Header kolom laporan
        fputcsv($output, [
            'No',
            'Nama Lengkap',
            'Jabatan',
            'Divisi',
            'No. Handphone',
            'Periode Mulai',
            'Periode Selesai',
            'Status',
            'Tanggal Dibuat',
        ]);

        $no = 1;
        foreach ($penguruses as $p) {
            fputcsv($output, [
                $no++,
                $p->nama,
                $p->jabatan,
                $p->divisi ?: '-',
                $p->no_hp ?: '-',
                $p->periode_mulai ?: '-',
                $p->periode_selesai ?: '-',
                $p->is_aktif ? 'Aktif' : 'Nonaktif',
                $p->created_at ? $p->created_at->format('d-m-Y H:i') : '-',
            ]);
        }

        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }

    /**
     * Proses impor file data pengurus (CSV).
     *
     * @return array{sukses: int, gagal: int, baris_gagal: array<int, array{baris: int, data: array, alasan: string}>}
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

            // Normalisasi key header menjadi huruf kecil dan tanpa spasi liar
            $normalized = [];
            foreach ($record as $key => $val) {
                $cleanKey = strtolower(trim(str_replace([' ', '-', '_'], '_', (string) $key)));
                // Bersihkan karakter BOM tersembunyi pada header pertama jika ada
                $cleanKey = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $cleanKey);
                $normalized[$cleanKey] = is_string($val) ? trim($val) : $val;
            }

            // Validasi nama
            $nama = $normalized['nama'] ?? ($normalized['nama_lengkap'] ?? '');
            if (empty($nama)) {
                $failCount++;
                $failedRows[] = [
                    'baris' => $rowNumber,
                    'data' => $record,
                    'alasan' => 'Kolom nama wajib diisi.',
                ];
                continue;
            }

            // Validasi jabatan
            $jabatan = $normalized['jabatan'] ?? '';
            if (empty($jabatan)) {
                $failCount++;
                $failedRows[] = [
                    'baris' => $rowNumber,
                    'data' => $record,
                    'alasan' => 'Kolom jabatan wajib diisi.',
                ];
                continue;
            }

            // Normalisasi status aktif
            $statusRaw = strtolower((string) ($normalized['is_aktif'] ?? ($normalized['status'] ?? 'aktif')));
            $isAktif = ! in_array($statusRaw, ['0', 'false', 'tidak', 'nonaktif', 'off', 'keluar', 'pensiun'], true);

            $divisi = $normalized['divisi'] ?? null;
            $noHp = $normalized['no_hp'] ?? ($normalized['nohp'] ?? ($normalized['hp'] ?? null));
            $periodeMulai = $normalized['periode_mulai'] ?? ($normalized['tahun_mulai'] ?? null);
            $periodeSelesai = $normalized['periode_selesai'] ?? ($normalized['tahun_selesai'] ?? null);

            try {
                DB::transaction(function () use ($nama, $jabatan, $divisi, $noHp, $periodeMulai, $periodeSelesai, $isAktif) {
                    Pengurus::create([
                        'nama' => $nama,
                        'jabatan' => $jabatan,
                        'divisi' => $divisi ?: null,
                        'no_hp' => $noHp ?: null,
                        'periode_mulai' => $periodeMulai ?: null,
                        'periode_selesai' => $periodeSelesai ?: null,
                        'is_aktif' => $isAktif,
                    ]);
                });

                $successCount++;
            } catch (Throwable $e) {
                $failCount++;
                $failedRows[] = [
                    'baris' => $rowNumber,
                    'data' => $record,
                    'alasan' => 'Kesalahan database: ' . $e->getMessage(),
                ];
            }
        }

        return [
            'sukses' => $successCount,
            'gagal' => $failCount,
            'baris_gagal' => $failedRows,
        ];
    }
}
