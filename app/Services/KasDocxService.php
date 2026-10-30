<?php

namespace App\Services;

use Illuminate\Support\Str;

class KasDocxService
{
    public function __construct(
        protected DocxSuratParserService $docxParserService
    ) {}

    /**
     * Hasilkan file Word (.docx) buku kas menggunakan template resmi LAPORAN BUKU KAS.docx
     * Murni menggunakan parser string & ZIP PHP (tanpa ketergantungan ekstensi DOMDocument/php-xml).
     */
    public function generateDocx(array $reportData): ?string
    {
        $templatePath = $this->getTemplatePath();
        if (! $templatePath || ! file_exists($templatePath)) {
            return null;
        }

        $entries = $this->docxParserService->readZipEntries($templatePath);
        if (empty($entries) || ! isset($entries['word/document.xml'])) {
            return null;
        }

        $docXml = $entries['word/document.xml'];
        $modifiedXml = $this->populateDocumentXml($docXml, $reportData);
        if ($modifiedXml) {
            $entries['word/document.xml'] = $modifiedXml;
        }

        $tempPath = rtrim(sys_get_temp_dir(), '/') . '/buku-kas-' . Str::random(12) . '.docx';
        $success = $this->docxParserService->writeZipEntries($tempPath, $entries);

        if ($success && file_exists($tempPath) && filesize($tempPath) > 0) {
            $binary = file_get_contents($tempPath);
            @unlink($tempPath);
            return $binary;
        }

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }

        return null;
    }

    /**
     * Dapatkan path file template laporan buku kas
     */
    protected function getTemplatePath(): ?string
    {
        $candidates = [
            base_path('LAPORAN BUKU KAS.docx'),
            resource_path('templates/laporan-buku-kas.docx'),
            storage_path('app/templates/laporan-buku-kas.docx'),
        ];

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Isi placeholder dan gandakan baris tabel sesuai data transaksi.
     * Implementasi murni string/regex tanpa membutuhkan Class DOMDocument.
     */
    protected function populateDocumentXml(string $docXml, array $data): string
    {
        // 1. Bersihkan tanda proofErr jika ada
        $docXml = preg_replace('/<w:proofErr[^>]*\/>/', '', $docXml);

        // 2. Format Rentang Periode Tanggal
        $dari = $data['dari'] ?? null;
        $sampai = $data['sampai'] ?? null;

        if ($dari && $sampai) {
            $tglMulai = date('d/m/Y', strtotime($dari));
            $tglAkhir = date('d/m/Y', strtotime($sampai));
        } elseif ($dari) {
            $tglMulai = date('d/m/Y', strtotime($dari));
            $tglAkhir = 'Selesai';
        } elseif ($sampai) {
            $tglMulai = 'Awal';
            $tglAkhir = date('d/m/Y', strtotime($sampai));
        } else {
            $tglMulai = 'Awal';
            $tglAkhir = 'Semua Periode';
        }

        $docXml = str_replace(
            ['{tanggal_mulai}', '{tanggal_akhir}'],
            [$tglMulai, $tglAkhir],
            $docXml
        );

        // 3. Modifikasi Tabel Transaksi Kas
        if (preg_match('/<w:tbl\b[^>]*>.*?<\/w:tbl>/s', $docXml, $tblMatch)) {
            $tblXml = $tblMatch[0];

            // Pastikan tblBorders terpasang pada w:tblPr agar garis tepi tabel (termasuk garis bawah) tegas
            $borderXml = '<w:tblBorders>'
                . '<w:top w:val="single" w:sz="6" w:space="0" w:color="000000"/>'
                . '<w:left w:val="single" w:sz="6" w:space="0" w:color="000000"/>'
                . '<w:bottom w:val="single" w:sz="6" w:space="0" w:color="000000"/>'
                . '<w:right w:val="single" w:sz="6" w:space="0" w:color="000000"/>'
                . '<w:insideH w:val="single" w:sz="6" w:space="0" w:color="000000"/>'
                . '<w:insideV w:val="single" w:sz="6" w:space="0" w:color="000000"/>'
                . '</w:tblBorders>';

            if (str_contains($tblXml, '<w:tblBorders>')) {
                $tblXml = preg_replace('/<w:tblBorders>.*?<\/w:tblBorders>/s', $borderXml, $tblXml);
            } else {
                $tblXml = preg_replace('/(<\/w:tblPr>)/', $borderXml . '$1', $tblXml);
            }

            // Ekstrak baris tabel
            if (preg_match_all('/<w:tr\b[^>]*>.*?<\/w:tr>/s', $tblXml, $rowMatches) && count($rowMatches[0]) >= 2) {
                $rows = $rowMatches[0];

                $headerRow = $rows[0];
                $templateRow = $rows[1];
                $totalRow = $rows[count($rows) - 1];

                // Data transaksi dan perhitungan saldo kas
                $transaksis = $data['transaksis'] ?? [];
                $saldoAwal = (float) ($data['saldoAwal'] ?? 0);
                $totalDebit = (float) ($data['totalDebit'] ?? 0);
                $totalKredit = (float) ($data['totalKredit'] ?? 0);
                $currentSaldo = $saldoAwal;

                $generatedRows = [];

                // Baris Saldo Awal jika ada
                if ($dari && $saldoAwal != 0) {
                    $saldoRow = str_replace(
                        ['{No.}', '{No._bukti}', '{akun}', '{debit}', '{kredit}', '{saldo}'],
                        ['-', '-', 'SALDO AWAL KAS', '-', '-', 'Rp ' . number_format($currentSaldo, 0, ',', '.')],
                        $templateRow
                    );
                    $generatedRows[] = $saldoRow;
                }

                // Baris-baris Transaksi
                foreach ($transaksis as $idx => $t) {
                    if ($t->jenis === 'debit') {
                        $currentSaldo += (float) $t->nominal;
                    } else {
                        $currentSaldo -= (float) $t->nominal;
                    }

                    $debitStr = $t->jenis === 'debit' ? 'Rp ' . number_format((float) $t->nominal, 0, ',', '.') : '-';
                    $kreditStr = $t->jenis === 'kredit' ? 'Rp ' . number_format((float) $t->nominal, 0, ',', '.') : '-';
                    $saldoStr = 'Rp ' . number_format((float) $currentSaldo, 0, ',', '.');

                    $newRow = str_replace(
                        ['{No.}', '{No._bukti}', '{akun}', '{debit}', '{kredit}', '{saldo}'],
                        [
                            (string) ($idx + 1),
                            htmlspecialchars((string) $t->kode_transaksi, ENT_XML1, 'UTF-8'),
                            htmlspecialchars((string) $t->kategori, ENT_XML1, 'UTF-8'),
                            $debitStr,
                            $kreditStr,
                            $saldoStr,
                        ],
                        $templateRow
                    );
                    $generatedRows[] = $newRow;
                }

                // Jika tidak ada data transaksi sama sekali
                if (empty($generatedRows)) {
                    $emptyRow = str_replace(
                        ['{No.}', '{No._bukti}', '{akun}', '{debit}', '{kredit}', '{saldo}'],
                        ['-', '-', 'Tidak ada data transaksi kas.', '-', '-', 'Rp 0'],
                        $templateRow
                    );
                    $generatedRows[] = $emptyRow;
                }

                // Format Baris Total Rekapitulasi
                $cleanTotal = str_replace(
                    ['{total_debit}', '{total_kredit}', '{total_}'],
                    [
                        'Rp ' . number_format($totalDebit, 0, ',', '.'),
                        'Rp ' . number_format($totalKredit, 0, ',', '.'),
                        'Rp ' . number_format($currentSaldo, 0, ',', '.'),
                    ],
                    $totalRow
                );

                // Rangkai kembali seluruh isi tabel
                $tblPrefixPos = strpos($tblXml, '<w:tr');
                $tblPrefix = substr($tblXml, 0, $tblPrefixPos);
                $newTbl = $tblPrefix . $headerRow . implode('', $generatedRows) . $cleanTotal . '</w:tbl>';

                $docXml = str_replace($tblMatch[0], $newTbl, $docXml);
            }
        }

        return $docXml;
    }
}
