<?php

namespace App\Services;

use Illuminate\Support\Str;

class KasDocxService
{
    public function __construct(
        protected DocxSuratParserService $docxParserService
    ) {}

    /**
     * Hasilkan file Word (.docx) buku kas menggunakan template resmi template buku kas.docx
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
     * Dapatkan path file template laporan buku kas yang valid
     */
    protected function getTemplatePath(): ?string
    {
        $candidates = [
            base_path('template buku kas.docx'),
            resource_path('templates/template buku kas.docx'),
            resource_path('templates/laporan-buku-kas.docx'),
            storage_path('app/templates/template buku kas.docx'),
            storage_path('app/templates/laporan-buku-kas.docx'),
            base_path('LAPORAN BUKU KAS.docx'),
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
     * Mendukung template resmi 8 kolom (NO, TANGGAL, NO.BUKTI, AKUN, KETERANGAN, DEBIT, KREDIT, SALDO).
     */
    protected function populateDocumentXml(string $docXml, array $data): string
    {
        // 1. Bersihkan tanda proofErr jika ada
        $docXml = preg_replace('/<w:proofErr[^>]*\/>/', '', $docXml);

        // 2. Format Rentang Periode Tanggal Berdasarkan Transaksi Riil (Bahasa Indonesia)
        $dari = $data['dari'] ?? null;
        $sampai = $data['sampai'] ?? null;
        $tglMulai = $data['tanggal_awal_teks'] ?? null;
        $tglAkhir = $data['tanggal_akhir_teks'] ?? null;

        if (! $tglMulai || ! $tglAkhir) {
            $transaksisList = collect($data['transaksis'] ?? []);
            if ($transaksisList->isNotEmpty()) {
                $minDate = $transaksisList->min('tanggal');
                $maxDate = $transaksisList->max('tanggal');
                $cMin = \Carbon\Carbon::parse($minDate)->locale('id');
                $cMax = \Carbon\Carbon::parse($maxDate)->locale('id');

                if ($cMin->year === $cMax->year) {
                    $tglMulai = $cMin->translatedFormat('d F');
                    $tglAkhir = $cMax->translatedFormat('d F Y');
                } else {
                    $tglMulai = $cMin->translatedFormat('d F Y');
                    $tglAkhir = $cMax->translatedFormat('d F Y');
                }
            } else {
                $cNow = \Carbon\Carbon::now()->locale('id');
                $tglMulai = $cNow->translatedFormat('d F');
                $tglAkhir = $cNow->translatedFormat('d F Y');
            }
        }

        // Ganti placeholder periode {tanggal_awal} s.d { tanggal_akhir} atau {tanggal_mulai} / {tanggal_akhir}
        $docXml = preg_replace('/\{[^{}]*tanggal_awal[^{}]*\}/s', $tglMulai, $docXml);
        $docXml = preg_replace('/\{[^{}]*tanggal_mulai[^{}]*\}/s', $tglMulai, $docXml);
        $docXml = preg_replace('/\{[^{}]*tanggal_akhir[^{}]*\}/s', $tglAkhir, $docXml);

        // 3. Modifikasi Tabel Transaksi Kas (Tabel Pertama)
        if (preg_match('/<w:tbl\b[^>]*>.*?<\/w:tbl>/s', $docXml, $tblMatch)) {
            $tblXml = $tblMatch[0];

            // Ekstrak baris tabel
            if (preg_match_all('/<w:tr\b[^>]*>.*?<\/w:tr>/s', $tblXml, $rowMatches) && count($rowMatches[0]) >= 2) {
                $rows = $rowMatches[0];

                $headerRow = $rows[0];
                $templateRow = $rows[1];
                $totalRow = $rows[count($rows) - 1]; // Baris total mutasi & saldo akhir

                // Data transaksi dan perhitungan saldo kas
                $transaksis = $data['transaksis'] ?? [];
                $saldoAwal = (float) ($data['saldoAwal'] ?? 0);
                $totalDebit = (float) ($data['totalDebit'] ?? 0);
                $totalKredit = (float) ($data['totalKredit'] ?? 0);
                $currentSaldo = $saldoAwal;

                $generatedRows = [];

                // Baris Saldo Awal jika ada
                if ($dari && $saldoAwal != 0) {
                    $saldoRow = $this->renderRow($templateRow, [
                        'no' => '-',
                        'tanggal' => date('d/m/Y', strtotime($dari)),
                        'no_bukti' => '-',
                        'akun' => 'SALDO AWAL KAS',
                        'keterangan' => 'Saldo awal kas periode sebelumnya',
                        'debit' => '-',
                        'kredit' => '-',
                        'saldo' => 'Rp ' . number_format($currentSaldo, 0, ',', '.'),
                    ]);
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
                    $tglStr = $t->tanggal ? date('d/m/Y', strtotime($t->tanggal)) : '-';

                    $rowXml = $this->renderRow($templateRow, [
                        'no' => (string) ($idx + 1),
                        'tanggal' => $tglStr,
                        'no_bukti' => (string) $t->kode_transaksi,
                        'akun' => (string) $t->kategori,
                        'keterangan' => (string) ($t->keterangan ?: '-'),
                        'debit' => $debitStr,
                        'kredit' => $kreditStr,
                        'saldo' => $saldoStr,
                    ]);
                    $generatedRows[] = $rowXml;
                }

                // Jika tidak ada data transaksi sama sekali
                if (empty($generatedRows)) {
                    $emptyRow = $this->renderRow($templateRow, [
                        'no' => '-',
                        'tanggal' => '-',
                        'no_bukti' => '-',
                        'akun' => '-',
                        'keterangan' => 'Tidak ada data transaksi kas pada periode ini.',
                        'debit' => '-',
                        'kredit' => '-',
                        'saldo' => 'Rp 0',
                    ]);
                    $generatedRows[] = $emptyRow;
                }

                // Format Baris Total Rekapitulasi
                $cleanTotal = $this->renderTotalRow($totalRow, $totalDebit, $totalKredit, $currentSaldo);

                // Rangkai kembali seluruh isi tabel
                $tblPrefixPos = strpos($tblXml, '<w:tr');
                $tblPrefix = substr($tblXml, 0, $tblPrefixPos);
                $newTbl = $tblPrefix . $headerRow . implode('', $generatedRows) . $cleanTotal . '</w:tbl>';

                $docXml = str_replace($tblMatch[0], $newTbl, $docXml);
            }
        }

        return $docXml;
    }

    /**
     * Render satu baris data transaksi pada template baris tabel
     */
    protected function renderRow(string $rowXml, array $vals): string
    {
        $r = $rowXml;

        // Ganti No.bukti terlebih dahulu sebelum No. agar tidak salah timpa
        $r = preg_replace('/\{[^{}]*No\.?_?bukti[^{}]*\}/i', htmlspecialchars($vals['no_bukti'], ENT_XML1, 'UTF-8'), $r);
        $r = preg_replace('/\{[^{}]*tanggal_transaksi[^{}]*\}/i', htmlspecialchars($vals['tanggal'], ENT_XML1, 'UTF-8'), $r);
        $r = preg_replace('/\{[^{}]*akun[^{}]*\}/i', htmlspecialchars($vals['akun'], ENT_XML1, 'UTF-8'), $r);
        $r = preg_replace('/\{[^{}]*keterangan[^{}]*\}/i', htmlspecialchars($vals['keterangan'], ENT_XML1, 'UTF-8'), $r);
        $r = preg_replace('/\{[^{}]*debit[^{}]*\}/i', htmlspecialchars($vals['debit'], ENT_XML1, 'UTF-8'), $r);
        $r = preg_replace('/\{[^{}]*kredit[^{}]*\}/i', htmlspecialchars($vals['kredit'], ENT_XML1, 'UTF-8'), $r);
        $r = preg_replace('/\{[^{}]*saldo[^{}]*\}/i', htmlspecialchars($vals['saldo'], ENT_XML1, 'UTF-8'), $r);
        // Terakhir ganti {No.}
        $r = preg_replace('/\{[^{}]*No\.[^{}]*\}/i', htmlspecialchars($vals['no'], ENT_XML1, 'UTF-8'), $r);

        return $r;
    }

    /**
     * Render baris rekapitulasi total mutasi dan saldo akhir
     */
    protected function renderTotalRow(string $totalRowXml, float $totalDebit, float $totalKredit, float $saldoAkhir): string
    {
        $r = $totalRowXml;

        // Template resmi 'template buku kas.docx' sudah memiliki awalan 'Rp ' sebelum placeholder
        if (preg_match('/\{[^{}]*jumlah_debit[^{}]*\}/i', $r)) {
            $r = preg_replace('/\{[^{}]*jumlah_debit[^{}]*\}/i', number_format($totalDebit, 0, ',', '.'), $r);
            $r = preg_replace('/\{[^{}]*jumlah_kredit[^{}]*\}/i', number_format($totalKredit, 0, ',', '.'), $r);
            $r = preg_replace('/\{[^{}]*jumlah_[^{}]*saldo[^{}]*\}/i', number_format($saldoAkhir, 0, ',', '.'), $r);
        } else {
            // Template alternatif dengan placeholder {total_debit}, {total_kredit}, {total_}
            $r = preg_replace('/\{[^{}]*total_debit[^{}]*\}/i', 'Rp ' . number_format($totalDebit, 0, ',', '.'), $r);
            $r = preg_replace('/\{[^{}]*total_kredit[^{}]*\}/i', 'Rp ' . number_format($totalKredit, 0, ',', '.'), $r);
            $r = preg_replace('/\{[^{}]*total_?[^{}]*\}/i', 'Rp ' . number_format($saldoAkhir, 0, ',', '.'), $r);
        }

        return $r;
    }
}
