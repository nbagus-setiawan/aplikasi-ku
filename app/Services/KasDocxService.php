<?php

namespace App\Services;

use DOMDocument;
use DOMXPath;
use Illuminate\Support\Str;

class KasDocxService
{
    public function __construct(
        protected DocxSuratParserService $docxParserService
    ) {}

    /**
     * Hasilkan file Word (.docx) buku kas menggunakan template resmi LAPORAN BUKU KAS.docx
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
     * Isi placeholder dan perbanyak baris tabel sesuai data transaksi
     */
    protected function populateDocumentXml(string $docXml, array $data): string
    {
        // 1. Bersihkan penanda ejaan bawaan Word yang sering memecah string placeholder
        $docXml = preg_replace('/<w:proofErr[^>]*\/>/', '', $docXml);

        $doc = new DOMDocument();
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = false;

        $loaded = @$doc->loadXML($docXml);
        if (! $loaded) {
            return $docXml;
        }

        $xpath = new DOMXPath($doc);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

        // 2. Format Periode Tanggal
        $dari = $data['dari'] ?? null;
        $sampai = $data['sampai'] ?? null;
        if ($dari && $sampai) {
            $periodeText = 'PERIODE : ' . date('d/m/Y', strtotime($dari)) . ' s.d. ' . date('d/m/Y', strtotime($sampai));
        } elseif ($dari) {
            $periodeText = 'PERIODE : Mulai ' . date('d/m/Y', strtotime($dari));
        } elseif ($sampai) {
            $periodeText = 'PERIODE : Sampai ' . date('d/m/Y', strtotime($sampai));
        } else {
            $periodeText = 'PERIODE : Semua Transaksi Pembukuan';
        }

        // Terapkan ke paragraf yang memuat kata PERIODE
        $paragraphs = $xpath->query('//w:p');
        foreach ($paragraphs as $p) {
            $textNodes = $xpath->query('.//w:t', $p);
            $fullText = '';
            foreach ($textNodes as $tn) {
                $fullText .= $tn->nodeValue;
            }
            if (str_contains(strtoupper($fullText), 'PERIODE')) {
                $first = true;
                foreach ($textNodes as $tn) {
                    if ($first) {
                        $tn->nodeValue = $periodeText;
                        $first = false;
                    } else {
                        $tn->nodeValue = '';
                    }
                }
            }
        }

        // 3. Modifikasi Tabel Utama (Tabel Kas)
        $tables = $xpath->query('//w:tbl');
        if ($tables->length > 0) {
            $tbl = $tables->item(0);
            $rows = $xpath->query('.//w:tr', $tbl);

            if ($rows->length >= 2) {
                $templateRow = $rows->item(1); // Baris yang berisi {No.}, {No._bukti), {akun}, dsb.
                $totalRow = $rows->item($rows->length - 1); // Baris terakhir (Total Mutasi & Saldo Akhir)

                // Simpan baris kosong di antara template dan total jika ada untuk dihapus
                $rowsToRemove = [];
                for ($i = 1; $i < $rows->length - 1; $i++) {
                    $rowsToRemove[] = $rows->item($i);
                }

                $setCellText = function ($cell, string $text) use ($xpath) {
                    $tNodes = $xpath->query('.//w:t', $cell);
                    $first = true;
                    foreach ($tNodes as $tn) {
                        if ($first) {
                            $tn->nodeValue = htmlspecialchars($text, ENT_XML1, 'UTF-8');
                            $first = false;
                        } else {
                            $tn->nodeValue = '';
                        }
                    }
                };

                $transaksis = $data['transaksis'] ?? [];
                $saldoAwal = (float) ($data['saldoAwal'] ?? 0);
                $currentSaldo = $saldoAwal;

                $rowCount = 0;

                // Jika ada saldo awal sebelum periode
                if ($dari && $saldoAwal != 0) {
                    $saldoRow = $templateRow->cloneNode(true);
                    $cells = $xpath->query('.//w:tc', $saldoRow);
                    if ($cells->length >= 6) {
                        $setCellText($cells->item(0), '-');
                        $setCellText($cells->item(1), '-');
                        $setCellText($cells->item(2), 'SALDO AWAL KAS');
                        $setCellText($cells->item(3), '-');
                        $setCellText($cells->item(4), '-');
                        $setCellText($cells->item(5), number_format($currentSaldo, 0, ',', '.'));
                    }
                    $tbl->insertBefore($saldoRow, $totalRow);
                    $rowCount++;
                }

                // Tambahkan setiap transaksi
                foreach ($transaksis as $idx => $t) {
                    if ($t->jenis === 'debit') {
                        $currentSaldo += (float) $t->nominal;
                    } else {
                        $currentSaldo -= (float) $t->nominal;
                    }

                    $newRow = $templateRow->cloneNode(true);
                    $cells = $xpath->query('.//w:tc', $newRow);

                    if ($cells->length >= 6) {
                        $setCellText($cells->item(0), (string) ($idx + 1));
                        $setCellText($cells->item(1), (string) $t->kode_transaksi);
                        $setCellText($cells->item(2), (string) $t->kategori);
                        $setCellText($cells->item(3), $t->jenis === 'debit' ? number_format((float) $t->nominal, 0, ',', '.') : '-');
                        $setCellText($cells->item(4), $t->jenis === 'kredit' ? number_format((float) $t->nominal, 0, ',', '.') : '-');
                        $setCellText($cells->item(5), number_format($currentSaldo, 0, ',', '.'));
                    }

                    $tbl->insertBefore($newRow, $totalRow);
                    $rowCount++;
                }

                // Jika sama sekali tidak ada data
                if ($rowCount === 0) {
                    $emptyRow = $templateRow->cloneNode(true);
                    $cells = $xpath->query('.//w:tc', $emptyRow);
                    if ($cells->length >= 6) {
                        $setCellText($cells->item(0), '-');
                        $setCellText($cells->item(1), '-');
                        $setCellText($cells->item(2), 'Tidak ada data transaksi kas.');
                        $setCellText($cells->item(3), '-');
                        $setCellText($cells->item(4), '-');
                        $setCellText($cells->item(5), '0');
                    }
                    $tbl->insertBefore($emptyRow, $totalRow);
                }

                // Hapus template row dan baris kosong perantara
                foreach ($rowsToRemove as $r) {
                    $tbl->removeChild($r);
                }

                // 4. Update Baris Total Mutasi & Saldo Akhir
                $totalCells = $xpath->query('.//w:tc', $totalRow);
                $totalDebit = (float) ($data['totalDebit'] ?? 0);
                $totalKredit = (float) ($data['totalKredit'] ?? 0);

                if ($totalCells->length >= 5) {
                    $setCellText($totalCells->item(2), number_format($totalDebit, 0, ',', '.'));
                    $setCellText($totalCells->item(3), number_format($totalKredit, 0, ',', '.'));
                    $setCellText($totalCells->item(4), number_format($currentSaldo, 0, ',', '.'));
                }
            }
        }

        return $doc->saveXML();
    }
}
