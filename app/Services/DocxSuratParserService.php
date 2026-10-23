<?php

namespace App\Services;

use App\Models\SuratKeluar;
use chillerlan\QRCode\Output\QROutputInterface;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocxSuratParserService
{
    /**
     * Ekstrak teks dan deteksi otomatis Nomor, Lampiran, Perihal, Tujuan (setelah Yth), Pengirim, dan Tanggal dari file (.docx/.doc/.pdf/.txt).
     */
    public function parseUploadedDocument(UploadedFile $file): array
    {
        $lines = $this->extractLines($file);
        $fullText = implode("\n", $lines);

        $noSurat = $this->detectNomorSurat($lines, $fullText);
        $lampiran = $this->detectLampiran($lines);
        $perihal = $this->detectPerihal($lines);
        $tujuan = $this->detectTujuan($lines);
        $pengirim = $this->detectPengirim($lines);
        $tanggalInfo = $this->detectTanggal($lines, $fullText);

        $isNomorLengkap = false;
        $kodeJenisTerdeteksi = 'SPm.01';

        if ($noSurat !== '') {
            $hasPlaceholder = preg_match('/(\.{2,}|_{2,}|\[\s*\]|^\/)/', $noSurat);
            $parts = array_values(array_filter(explode('/', $noSurat), fn ($p) => trim($p) !== ''));
            if (! $hasPlaceholder && count($parts) >= 2 && preg_match('/\d+/', $parts[0])) {
                $isNomorLengkap = true;
            }
            foreach ($parts as $part) {
                if (preg_match('/^[A-Za-z]{1,5}\.?\d{1,3}$/', trim($part))) {
                    $kodeJenisTerdeteksi = trim($part);
                    break;
                }
            }
        }

        $adaLampiran = $this->checkAdaLampiran($lampiran);
        $htmlParagraphs = array_map(fn ($l) => '<p>' . e($l) . '</p>', $lines);

        return [
            'no_surat' => $noSurat,
            'is_nomor_lengkap' => $isNomorLengkap,
            'kode_jenis' => $kodeJenisTerdeteksi,
            'lampiran_teks' => $lampiran !== '' ? $lampiran : '-',
            'ada_lampiran' => $adaLampiran,
            'perihal' => $perihal !== '' ? $perihal : pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            'tujuan' => $tujuan !== '' ? $tujuan : 'Penerima Surat',
            'pengirim' => $pengirim !== '' ? $pengirim : 'Instansi Pengirim',
            'tgl_surat' => $tanggalInfo['tgl_surat'],
            'tempat_surat' => $tanggalInfo['tempat_surat'],
            'isi_html' => implode("\n", $htmlParagraphs) ?: '<p>Dokumen Surat (' . e($file->getClientOriginalName()) . ')</p>',
            'raw_lines' => $lines,
        ];
    }

    /**
     * Baca XML dari dalam file .docx menggunakan ZipArchive ATAU unzip CLI / python3 jika ekstensi zip PHP belum aktif.
     */
    protected function readDocxXml(string $realPath, string $entryName = 'word/document.xml'): ?string
    {
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive;
            if ($zip->open($realPath) === true) {
                $xml = $zip->getFromName($entryName);
                $zip->close();
                if ($xml !== false) {
                    return $xml;
                }
            }
        }

        if (function_exists('shell_exec')) {
            // Fallback 1: gunakan unzip -p (bawaan Linux)
            $cmd = sprintf('unzip -p %s %s 2>/dev/null', escapeshellarg($realPath), escapeshellarg($entryName));
            $xml = @\shell_exec($cmd);
            if (is_string($xml) && (str_contains($xml, '<w:document') || str_contains($xml, '<w:hdr'))) {
                return $xml;
            }

            // Fallback 2: gunakan python3 zipfile
            $pyCmd = sprintf(
                'python3 -c "import zipfile, sys; z=zipfile.ZipFile(sys.argv[1]); sys.stdout.buffer.write(z.read(sys.argv[2]))" %s %s 2>/dev/null',
                escapeshellarg($realPath),
                escapeshellarg($entryName)
            );
            $xmlPy = @\shell_exec($pyCmd);
            if (is_string($xmlPy) && (str_contains($xmlPy, '<w:document') || str_contains($xmlPy, '<w:hdr'))) {
                return $xmlPy;
            }
        }

        return null;
    }

    /**
     * Ekstrak baris-baris teks dari .docx (termasuk header kop, paragraf, dan sel tabel), .pdf, .html, atau .txt.
     */
    public function extractLines(UploadedFile $file): array
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $realPath = $file->getRealPath();
        $lines = [];

        if ($ext === 'docx') {
            $xmlEntries = ['word/header1.xml', 'word/header2.xml', 'word/document.xml'];
            $tableLines = [];
            $orderedLines = [];

            foreach ($xmlEntries as $entry) {
                $xml = $this->readDocxXml($realPath, $entry);
                if ($xml === null) {
                    continue;
                }
                $dom = new \DOMDocument;
                @$dom->loadXML($xml);
                $xpath = new \DOMXPath($dom);
                $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');

                // Ambil baris per baris tabel (tr) agar pasangan "Nomor | : | 001/..." di tabel Word tergabung satu baris
                $rows = $xpath->query('//w:tr');
                if ($rows) {
                    foreach ($rows as $row) {
                        $cells = $xpath->query('.//w:tc', $row);
                        $cellTexts = [];
                        if ($cells) {
                            foreach ($cells as $cell) {
                                $pNodes = $xpath->query('.//w:p', $cell);
                                $pTexts = [];
                                if ($pNodes) {
                                    foreach ($pNodes as $pNode) {
                                        $t = trim($pNode->textContent);
                                        if ($t !== '') {
                                            $pTexts[] = $t;
                                        }
                                    }
                                }
                                if (! empty($pTexts)) {
                                    $cellTexts[] = implode(' ', $pTexts);
                                }
                            }
                        }
                        $rowLine = trim(implode(' ', $cellTexts));
                        if ($rowLine !== '') {
                            $tableLines[] = preg_replace('/\s+/u', ' ', $rowLine);
                        }
                    }
                }

                // Ambil semua paragraf sesuai urutan dokumen
                $allP = $xpath->query('//w:p');
                if ($allP) {
                    foreach ($allP as $p) {
                        $t = trim(preg_replace('/\s+/u', ' ', $p->textContent));
                        if ($t !== '') {
                            $orderedLines[] = $t;
                        }
                    }
                }
            }

            if (! empty($tableLines) || ! empty($orderedLines)) {
                return array_values(array_unique(array_merge($orderedLines, $tableLines)));
            }
        }

        if ($ext === 'pdf' && function_exists('shell_exec')) {
            $pdfTxt = @\shell_exec(sprintf('pdftotext -layout %s - 2>/dev/null', escapeshellarg($realPath)));
            if (is_string($pdfTxt) && trim($pdfTxt) !== '') {
                foreach (preg_split('/\r\n|\r|\n/', $pdfTxt) as $line) {
                    $clean = trim(preg_replace('/\s+/u', ' ', $line));
                    if ($clean !== '') {
                        $lines[] = $clean;
                    }
                }
                if (! empty($lines)) {
                    return $lines;
                }
            }
        }

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            return [];
        }

        $raw = @file_get_contents($realPath) ?: '';
        if ($ext === 'html' || $ext === 'htm') {
            $raw = strip_tags(preg_replace('/<\/(p|tr|div|li|h[1-6])>/i', "\n", $raw));
        }

        foreach (preg_split('/\r\n|\r|\n/', $raw) as $line) {
            $clean = trim(preg_replace('/\s+/u', ' ', $line));
            if ($clean !== '') {
                $lines[] = $clean;
            }
        }

        return $lines;
    }

    /**
     * Deteksi Nomor Surat dari baris-baris dokumen.
     */
    protected function detectNomorSurat(array $lines, string $fullText): string
    {
        foreach ($lines as $line) {
            if (preg_match('/^(?:Nomor|No\.?\s*Surat|No\.?)\s*[:.]?\s*(.+)$/iu', $line, $m)) {
                $val = trim($m[1]);
                $val = preg_replace('/\s+[A-Z][a-zA-Z\s]{2,20},\s*\d{1,2}\s+[A-Za-z]+\s+\d{4}.*$/u', '', $val);
                return trim($val);
            }
        }

        if (preg_match('/\b(\d{1,4}\/[A-Za-z0-9.\-_]+\/[A-Za-z0-9.\-_]+\/[IVXLCDM0-9]+\/\d{4})\b/u', $fullText, $m)) {
            return trim($m[1]);
        }

        return '';
    }

    /**
     * Deteksi Lampiran (mendukung "1 Lembar", "2 Lembar", "1 Berkas", "1 Bundel", "-", dll).
     */
    protected function detectLampiran(array $lines): string
    {
        foreach ($lines as $line) {
            if (preg_match('/^(?:Lampiran|Lamp\.?)\s*[:.]?\s*(.+)$/iu', $line, $m)) {
                $val = trim($m[1]);
                $val = preg_replace('/\s+(?:Perihal|Hal)\s*[:.].*$/iu', '', $val);
                return trim($val);
            }
        }

        return '-';
    }

    /**
     * Cek apakah string lampiran menunjukkan adanya lampiran (misal: "1 lembar", "2 lembar", "1 berkas", dll).
     */
    public function checkAdaLampiran(string $lampiran): bool
    {
        $clean = strtolower(trim($lampiran));
        if ($clean === '' || $clean === '-' || $clean === '--' || $clean === 'tidak ada' || $clean === 'nihil' || $clean === '0') {
            return false;
        }

        return true;
    }

    /**
     * Deteksi Perihal / Hal surat.
     */
    protected function detectPerihal(array $lines): string
    {
        foreach ($lines as $idx => $line) {
            if (preg_match('/^(?:Perihal|Hal)\s*[:.]?\s*(.+)$/iu', $line, $m)) {
                $val = trim($m[1]);
                $val = preg_replace('/\s+Kepada\s+Yth.*$/iu', '', $val);
                if ($val !== '') {
                    return $val;
                }
                if (isset($lines[$idx + 1])) {
                    return trim($lines[$idx + 1]);
                }
            }
        }

        foreach ($lines as $line) {
            if (preg_match('/^(SURAT\s+[A-Z\s]{3,40})$/u', strtoupper($line), $m)) {
                return ucwords(strtolower(trim($m[1])));
            }
        }

        return '';
    }

    /**
     * Deteksi Tujuan surat sesudah kata "Yth" / "Kepada Yth".
     */
    protected function detectTujuan(array $lines): string
    {
        foreach ($lines as $idx => $line) {
            if (preg_match('/(?:Kepada\s+)?Yth\.?\s*[:.]?\s*(.*)$/iu', $line, $m)) {
                $afterYth = trim($m[1]);
                if ($afterYth !== '' && ! preg_match('/^di[\s\-]*tempat$/iu', $afterYth)) {
                    return $afterYth;
                }
                for ($next = $idx + 1; $next <= min($idx + 3, count($lines) - 1); $next++) {
                    $candidate = trim($lines[$next]);
                    if ($candidate !== '' && ! preg_match('/^(?:di\b|tempat\b)/iu', $candidate)) {
                        return $candidate;
                    }
                }
            }
        }

        return '';
    }

    /**
     * Deteksi Pengirim / Instansi Asal dari kop surat (baris-baris awal sebelum Nomor/Lampiran/Perihal) atau label Pengirim/Dari.
     */
    protected function detectPengirim(array $lines): string
    {
        foreach ($lines as $line) {
            if (preg_match('/^(?:Pengirim|Dari|Instansi|Asal\s*Surat)\s*[:.]\s*(.+)$/iu', $line, $m)) {
                $val = trim($m[1]);
                if ($val !== '') {
                    return $val;
                }
            }
        }

        // Cari pada baris-baris awal kop surat sebelum kata Nomor / Lampiran / Perihal / Kepada Yth
        $kopCandidates = [];
        foreach (array_slice($lines, 0, 8) as $line) {
            if (preg_match('/^(?:Nomor|No\.|Lampiran|Lamp\.|Perihal|Hal|Kepada|Yth|Assalamu|Bismillah)/iu', $line)) {
                break;
            }
            if (preg_match('/^(?:Jl\.|Jalan|Alamat|Telp|Hp|Email|Website|Kode\s*Pos)/iu', $line)) {
                continue;
            }
            if (mb_strlen($line) >= 5 && mb_strlen($line) <= 120) {
                $kopCandidates[] = $line;
            }
        }

        if (! empty($kopCandidates)) {
            return implode(' - ', array_slice($kopCandidates, 0, 2));
        }

        return '';
    }

    /**
     * Render dokumen Word (.docx/.doc) menjadi gambar PNG asli (header s/d footer utuh seperti foto) untuk Surat Masuk.
     */
    public function renderWordToOriginalPng(string $sourcePath, string $storagePrefix = 'surat-masuk/dokumen'): ?string
    {
        if (! is_file($sourcePath)) {
            return null;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        if (! in_array($ext, ['docx', 'doc'], true) || ! function_exists('exec')) {
            return null;
        }

        $tempDir = rtrim(sys_get_temp_dir(), '/') . '/sap-masuk-' . Str::random(8);
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        try {
            $inputDoc = $tempDir . '/input_asli.' . $ext;
            copy($sourcePath, $inputDoc);

            if (app()->runningUnitTests()) {
                return null;
            }

            $userProfileDir = $tempDir . '/lo-profile';
            $cmd = sprintf(
                'soffice %s --headless --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg('-env:UserInstallation=file://' . $userProfileDir),
                escapeshellarg($tempDir),
                escapeshellarg($inputDoc)
            );
            @\exec($cmd, $out, $code);

            $pdfCandidate = $tempDir . '/input_asli.pdf';
            if (! is_file($pdfCandidate)) {
                return null;
            }

            $imgPrefix = $tempDir . '/page';
            $ppmCmd = sprintf('pdftoppm -png -r 150 %s %s 2>&1', escapeshellarg($pdfCandidate), escapeshellarg($imgPrefix));
            @\exec($ppmCmd, $ppmOut, $ppmCode);

            $pages = glob($tempDir . '/page*.png') ?: [];
            sort($pages);
            if (empty($pages)) {
                return null;
            }

            $storageRel = $storagePrefix . '-' . time() . '-' . Str::random(4) . '.png';
            Storage::disk('public')->put($storageRel, file_get_contents($pages[0]));

            return $storageRel;
        } finally {
            $this->cleanupDirectory($tempDir);
        }
    }

    /**
     * Deteksi Tempat dan Tanggal Surat dari naskah.
     */
    protected function detectTanggal(array $lines, string $fullText): array
    {
        $bulanMap = [
            'januari' => '01', 'februari' => '02', 'pebruari' => '02', 'maret' => '03',
            'april' => '04', 'mei' => '05', 'juni' => '06', 'juli' => '07',
            'agustus' => '08', 'september' => '09', 'oktober' => '10',
            'november' => '11', 'nopember' => '11', 'desember' => '12',
        ];

        $bulanPattern = implode('|', array_keys($bulanMap));

        foreach ($lines as $line) {
            if (preg_match('/([A-Za-z][A-Za-z ]{2,25}),\s*(\d{1,2})\s+(' . $bulanPattern . ')\s+(\d{4})/iu', $line, $m)) {
                $tempat = trim($m[1]);
                if (! in_array(strtolower($tempat), ['senin', 'selasa', 'rabu', 'kamis', 'jumat', "jum'at", 'sabtu', 'minggu', 'ahad'], true)) {
                    $day = str_pad($m[2], 2, '0', STR_PAD_LEFT);
                    $month = $bulanMap[strtolower($m[3])] ?? date('m');
                    $year = $m[4];

                    return [
                        'tempat_surat' => $tempat,
                        'tgl_surat' => "{$year}-{$month}-{$day}",
                    ];
                }
            }
        }

        if (preg_match('/\b(\d{1,2})\s+(' . $bulanPattern . ')\s+(\d{4})\b/iu', $fullText, $m)) {
            $day = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $month = $bulanMap[strtolower($m[2])] ?? date('m');
            $year = $m[3];

            return [
                'tempat_surat' => 'Putak',
                'tgl_surat' => "{$year}-{$month}-{$day}",
            ];
        }

        return [
            'tempat_surat' => 'Putak',
            'tgl_surat' => date('Y-m-d'),
        ];
    }

    /**
     * Buat PNG QR Code murni menggunakan chillerlan/php-qrcode + GD.
     */
    public function generateQrPngFile(string $url, string $targetPngPath): bool
    {
        try {
            $options = new QROptions();
            $options->outputInterface = \chillerlan\QRCode\Output\QRGdImagePNG::class;
            $options->scale = 5;
            $options->imageBase64 = false;
            $raw = (new QRCode($options))->render($url);

            if (is_string($raw) && str_starts_with($raw, 'data:')) {
                $raw = base64_decode(explode(',', $raw, 2)[1] ?? '');
            }

            if (is_string($raw) && strlen($raw) > 0) {
                file_put_contents($targetPngPath, $raw);
            }
        } catch (\Throwable $e) {
            // Lanjut ke fallback matrix GD di bawah
        }

        if (is_file($targetPngPath) && filesize($targetPngPath) > 0) {
            return true;
        }

        // Fallback langsung menggambar QRMatrix ke GD PNG
        try {
            $qr = new QRCode(new QROptions([
                'addQuietzone' => true,
                'quietzoneSize' => 2,
            ]));
            $qr->addByteSegment($url);
            $matrix = $qr->getQRMatrix();
            $size = $matrix->getSize();
            $scale = 5;
            $imgSize = $size * $scale;

            $im = imagecreatetruecolor($imgSize, $imgSize);
            $white = imagecolorallocate($im, 255, 255, 255);
            $black = imagecolorallocate($im, 0, 0, 0);
            imagefill($im, 0, 0, $white);

            for ($y = 0; $y < $size; $y++) {
                for ($x = 0; $x < $size; $x++) {
                    if ($matrix->check($x, $y)) {
                        imagefilledrectangle(
                            $im,
                            $x * $scale,
                            $y * $scale,
                            ($x + 1) * $scale - 1,
                            ($y + 1) * $scale - 1,
                            $black
                        );
                    }
                }
            }

            imagepng($im, $targetPngPath);
            imagedestroy($im);

            return is_file($targetPngPath) && filesize($targetPngPath) > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Buat badge verifikasi resmi berupa file gambar PNG yang memuat:
     * - QR Code resmi
     * - Kotak berbingkai rapi warna hijau emerald
     * - Teks tebal "Kode: [KODE_VERIFIKASI]"
     * - Label resmi "PP. AL-FALAH PUTAK"
     */
    public function generateQrBadgePng(string $url, string $kodeVerifikasi, string $targetPngPath): bool
    {
        $qrTemp = $targetPngPath . '.qr_tmp.png';
        $ok = $this->generateQrPngFile($url, $qrTemp);
        if (! $ok || ! is_file($qrTemp)) {
            return false;
        }

        if (! function_exists('imagecreatefrompng')) {
            rename($qrTemp, $targetPngPath);
            return is_file($targetPngPath);
        }

        $qrImg = @imagecreatefrompng($qrTemp);
        @unlink($qrTemp);
        if (! $qrImg) {
            return false;
        }

        $qrW = imagesx($qrImg);
        $qrH = imagesy($qrImg);

        $padX = 14;
        $padTop = 14;
        $padBottom = 12;
        $textHeight = 36;
        $badgeW = $qrW + ($padX * 2);
        $badgeH = $qrH + $padTop + $padBottom + $textHeight;

        $badge = imagecreatetruecolor($badgeW, $badgeH);
        $white = imagecolorallocate($badge, 255, 255, 255);
        $emerald = imagecolorallocate($badge, 16, 185, 129);
        $dark = imagecolorallocate($badge, 15, 23, 42);
        $greenText = imagecolorallocate($badge, 4, 120, 87);

        // Latar putih bersih
        imagefilledrectangle($badge, 0, 0, $badgeW - 1, $badgeH - 1, $white);

        // Garis batas luar emerald ganda rapi
        imagerectangle($badge, 0, 0, $badgeW - 1, $badgeH - 1, $emerald);
        imagerectangle($badge, 1, 1, $badgeW - 2, $badgeH - 2, $emerald);

        // Salin QR Code di tengah
        imagecopy($badge, $qrImg, $padX, $padTop, 0, 0, $qrW, $qrH);
        imagedestroy($qrImg);

        // Teks Kode Verifikasi
        $labelKode = 'Kode: ' . strtoupper($kodeVerifikasi);
        $font = 3;
        $fontW = imagefontwidth($font);
        $textX = max(4, (int) (($badgeW - (strlen($labelKode) * $fontW)) / 2));
        $textY = $padTop + $qrH + 6;
        imagestring($badge, $font, $textX, $textY, $labelKode, $dark);

        // Subteks pesantren
        $labelSub = 'PP. AL-FALAH PUTAK';
        $fontSub = 2;
        $fontSubW = imagefontwidth($fontSub);
        $subX = max(4, (int) (($badgeW - (strlen($labelSub) * $fontSub)) / 2));
        $subY = $textY + 16;
        imagestring($badge, $fontSub, $subX, $subY, $labelSub, $greenText);

        imagepng($badge, $targetPngPath);
        imagedestroy($badge);

        return is_file($targetPngPath) && filesize($targetPngPath) > 0;
    }

    /**
     * Pastikan surat keluar yang berasal dari upload Word sudah memiliki gambar tampilan ber-QR dan file Word ber-QR.
     */
    public function ensureWordQrProcessed(SuratKeluar $suratKeluar): void
    {
        if (empty($suratKeluar->file_surat_asli)) {
            return;
        }

        // Jika file asli sudah merupakan file hasil injeksi QR dan fisiknya ada di disk
        $isAlreadyWordQr = str_contains($suratKeluar->file_surat_asli, 'word-qr-')
            && Storage::disk('public')->exists($suratKeluar->file_surat_asli);

        if ($isAlreadyWordQr) {
            return;
        }

        $absSource = storage_path('app/public/' . $suratKeluar->file_surat_asli);
        if (! is_file($absSource)) {
            return;
        }

        $hasil = $this->processOriginalWordWithQr($suratKeluar, $absSource);

        if (! empty($hasil['file_word_qr'])) {
            $suratKeluar->file_surat_asli = $hasil['file_word_qr'];
            if (! empty($hasil['file_tampilan']) && ! str_ends_with(strtolower($hasil['file_tampilan']), '.docx')) {
                $suratKeluar->file_final = $hasil['file_tampilan'];
            }
            $suratKeluar->save();
        }
    }

    /**
     * Buat dokumen Word (.docx) resmi yang memuat kop instansi, nomor surat, isi, penandatangan,
     * serta Kode Verifikasi dan QR Code PNG resmi untuk surat yang dibuat lewat formulir web.
     *
     * @return string|null Path penyimpanan relatif di disk public
     */
    public function generateDocxFromSuratKeluar(SuratKeluar $suratKeluar): ?string
    {
        $suratKeluar->loadMissing(['kategori', 'tandaTangans']);
        $pengaturan = \App\Models\Pengaturan::getAllKeyValues();

        $namaPesantren = $pengaturan['nama_pesantren'] ?? 'PONDOK PESANTREN AL-FALAH PUTRA';
        $alamat = $pengaturan['alamat'] ?? 'Landasan Ulin Tengah, Liang Anggang, Banjarbaru, Kalimantan Selatan';
        $telepon = $pengaturan['telepon'] ?? '';
        $email = $pengaturan['email'] ?? '';
        $kontakTeks = trim($alamat . ($telepon ? ' | Telp: ' . $telepon : '') . ($email ? ' | Email: ' . $email : ''));

        $tempDir = rtrim(sys_get_temp_dir(), '/') . '/sap-gen-docx-' . ($suratKeluar->id ?: 'new') . '-' . Str::random(8);
        $buildDir = $tempDir . '/build';
        @mkdir($buildDir . '/_rels', 0775, true);
        @mkdir($buildDir . '/word/_rels', 0775, true);
        @mkdir($buildDir . '/word/media', 0775, true);

        try {
            $verifikasiUrl = url('/verifikasi/' . $suratKeluar->kode_verifikasi);
            $qrPngPath = $buildDir . '/word/media/qr_verifikasi.png';
            $this->generateQrPngFile($verifikasiUrl, $qrPngPath);

            // 1. [Content_Types].xml
            $contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
                '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
                '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
                '<Default Extension="xml" ContentType="application/xml"/>' .
                '<Default Extension="png" ContentType="image/png"/>' .
                '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>' .
                '</Types>';
            file_put_contents($buildDir . '/[Content_Types].xml', $contentTypes);

            // 2. _rels/.rels
            $rootRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
                '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
                '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>' .
                '</Relationships>';
            file_put_contents($buildDir . '/_rels/.rels', $rootRels);

            // 3. word/_rels/document.xml.rels
            $docRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
                '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
                '<Relationship Id="rIdQrVerif99" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/qr_verifikasi.png"/>' .
                '</Relationships>';
            file_put_contents($buildDir . '/word/_rels/document.xml.rels', $docRels);

            // 4. word/media/qr_verifikasi.png
            // 4. word/document.xml
            $cleanIsi = strip_tags(str_replace(['<br>', '<br/>', '<br />', '</p>'], ["\n", "\n", "\n", "\n\n"], $suratKeluar->isi ?? ''));
            $isiLines = array_filter(array_map('trim', explode("\n", $cleanIsi)), fn ($l) => $l !== '');

            $isiXml = '';
            foreach ($isiLines as $line) {
                $isiXml .= '<w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto" w:after="120"/><w:jc w:val="both"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="23"/></w:rPr><w:t xml:space="preserve">' . htmlspecialchars($line, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>';
            }

            $tglSuratTeks = ($suratKeluar->tempat_surat ?: 'Putak') . ', ' . ($suratKeluar->formatted_tgl_surat ?: now()->translatedFormat('d F Y'));
            $namaPenandatangan = $suratKeluar->nama_penandatangan ?: 'Sekretaris';
            $jabatanPenandatangan = $suratKeluar->jabatan_penandatangan ?: 'Sekretaris';
            $kodeEscaped = htmlspecialchars($suratKeluar->kode_verifikasi, ENT_XML1, 'UTF-8');

            $docXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
                '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main" ' .
                'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships" ' .
                'xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing" ' .
                'xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main" ' .
                'xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
                '<w:body>' .
                    // KOP SURAT
                    '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="40"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="32"/></w:rPr><w:t>' . htmlspecialchars($namaPesantren, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    '<w:p><w:pPr><w:jc w:val="center"/><w:spacing w:after="80"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="18"/><w:color w:val="555555"/></w:rPr><w:t>' . htmlspecialchars($kontakTeks, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    // Garis Kop
                    '<w:p><w:pPr><w:pBdr><w:bottom w:val="double" w:sz="18" w:space="1" w:color="000000"/></w:pBdr><w:spacing w:after="240"/></w:pPr></w:p>' .
                    // Tanggal di kanan
                    '<w:p><w:pPr><w:jc w:val="right"/><w:spacing w:after="160"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="22"/></w:rPr><w:t>' . htmlspecialchars($tglSuratTeks, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    // Nomor, Lampiran, Hal
                    '<w:p><w:pPr><w:spacing w:after="40"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="22"/></w:rPr><w:t>Nomor        : ' . htmlspecialchars($suratKeluar->no_surat, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    '<w:p><w:pPr><w:spacing w:after="40"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="22"/></w:rPr><w:t>Lampiran   : ' . htmlspecialchars($suratKeluar->lampiran_teks ?: '-', ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    '<w:p><w:pPr><w:spacing w:after="180"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="22"/></w:rPr><w:t>Perihal       : ' . htmlspecialchars($suratKeluar->perihal, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    // Tujuan / Kepada
                    '<w:p><w:pPr><w:spacing w:after="40"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="22"/></w:rPr><w:t>Kepada Yth,</w:t></w:r></w:p>' .
                    '<w:p><w:pPr><w:spacing w:after="40"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:sz w:val="22"/></w:rPr><w:t>' . htmlspecialchars($suratKeluar->tujuan, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    '<w:p><w:pPr><w:spacing w:after="200"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="22"/></w:rPr><w:t>di Tempat</w:t></w:r></w:p>' .
                    // Isi Surat
                    $isiXml .
                    // Penandatangan
                    '<w:p><w:pPr><w:spacing w:before="240" w:after="40"/><w:ind w:left="5000"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:sz w:val="22"/></w:rPr><w:t>' . htmlspecialchars($jabatanPenandatangan, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    '<w:p><w:pPr><w:spacing w:before="720" w:after="40"/><w:ind w:left="5000"/></w:pPr><w:r><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman"/><w:b/><w:u w:val="single"/><w:sz w:val="22"/></w:rPr><w:t>' . htmlspecialchars($namaPenandatangan, ENT_XML1, 'UTF-8') . '</w:t></w:r></w:p>' .
                    // Kode Verifikasi di Kanan Bawah (Font 11pt, rata kanan sesuai referensi.docx)
                    '<w:p>' .
                        '<w:pPr><w:jc w:val="end"/><w:spacing w:before="180" w:after="0"/><w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr></w:pPr>' .
                        '<w:r>' .
                            '<w:rPr><w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/><w:sz w:val="22"/><w:szCs w:val="22"/></w:rPr>' .
                            '<w:t xml:space="preserve">Kode Verifikasi: ' . $kodeEscaped . '</w:t>' .
                        '</w:r>' .
                    '</w:p>' .
                    '<w:sectPr>' .
                        '<w:pgSz w:w="11906" w:h="16838"/>' .
                        '<w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>' .
                    '</w:sectPr>' .
                '</w:body>' .
                '</w:document>';

            $docxFile = $tempDir . '/surat.docx';
            $entries = [
                '[Content_Types].xml' => $contentTypes,
                '_rels/.rels' => $rootRels,
                'word/_rels/document.xml.rels' => $docRels,
                'word/document.xml' => $docXml,
            ];

            $success = $this->writeZipEntries($docxFile, $entries);

            if (! $success) {
                return null;
            }

            $storageRel = 'surat-keluar/word-qr-' . ($suratKeluar->id ?: time()) . '-' . Str::random(4) . '.docx';
            Storage::disk('public')->put($storageRel, file_get_contents($docxFile));

            $suratKeluar->file_surat_asli = $storageRel;
            $suratKeluar->save();

            return $storageRel;
        } finally {
            $this->cleanupDirectory($tempDir);
        }
    }

    /**
     * Proses dokumen asli Word (.docx) tanpa mengubah format/header/footer sedikit pun,
     * hanya menyisipkan QR Code & Kode Verifikasi ke dokumen Word asli + menghasilkan gambar tampilan aslinya.
     *
     * @return array{file_word_qr: ?string, file_tampilan: ?string}
     */
    public function processOriginalWordWithQr(SuratKeluar $suratKeluar, string $sourceAbsolutePath): array
    {
        if (! is_file($sourceAbsolutePath)) {
            return ['file_word_qr' => null, 'file_tampilan' => null, 'file_pdf' => null];
        }

        // Gunakan direktori internal aplikasi agar aman dari batasan open_basedir hosting cPanel
        $tempDir = storage_path('app/temp/sap-word-' . ($suratKeluar->id ?: 'new') . '-' . Str::random(8));
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        try {
            $ext = strtolower(pathinfo($sourceAbsolutePath, PATHINFO_EXTENSION));
            $verifikasiUrl = url('/verifikasi/' . $suratKeluar->kode_verifikasi);
            $badgePngPath = $tempDir . '/qr_verifikasi.png';
            $this->generateQrBadgePng($verifikasiUrl, $suratKeluar->kode_verifikasi, $badgePngPath);

            $workingDocx = $tempDir . '/surat_ber_qr.docx';
            copy($sourceAbsolutePath, $workingDocx);

            $savedWordQrRel = null;
            if ($ext === 'docx') {
                $injected = $this->injectVerificationCodeIntoDocx($workingDocx, $suratKeluar->kode_verifikasi);
                if ($injected) {
                    $savedWordQrRel = 'surat-keluar/word-qr-' . ($suratKeluar->id ?: time()) . '-' . Str::random(4) . '.docx';
                    Storage::disk('public')->put($savedWordQrRel, file_get_contents($workingDocx));
                }
            }

            // Render PDF dan gambar tampilan asli dari dokumen Word jika didukung
            $targetDocForRender = ($savedWordQrRel && is_file($workingDocx)) ? $workingDocx : $sourceAbsolutePath;
            $renderResult = $this->renderWordAsOriginalDocument($targetDocForRender, $badgePngPath, $suratKeluar, $tempDir);

            return [
                'file_word_qr' => $savedWordQrRel,
                'file_tampilan' => $renderResult['image'] ?? null,
                'file_pdf' => $renderResult['pdf'] ?? null,
            ];
        } finally {
            $this->cleanupDirectory($tempDir);
        }
    }

    /**
     * Sisipkan teks Kode Verifikasi resmi (font 11pt, rata kanan) ke dalam berkas .docx asli
     * persis di bagian bawah naskah sebelum batas halaman/section, tanpa mengubah dokumen asli selain kode verifikasi.
     */
    protected function injectVerificationCodeIntoDocx(string $docxPath, string $kodeVerifikasi): bool
    {
        if (! is_file($docxPath)) {
            return false;
        }

        $entries = $this->readZipEntries($docxPath);
        if (! $entries || empty($entries['word/document.xml'])) {
            return false;
        }

        $docXml = $entries['word/document.xml'];
        $docXml = $this->appendVerificationCodeToDocumentXml($docXml, $kodeVerifikasi);
        $entries['word/document.xml'] = $docXml;

        return $this->writeZipEntries($docxPath, $entries);
    }

    /**
     * Alias kompatibilitas untuk injectQrIntoDocx
     */
    protected function injectQrIntoDocx(string $docxPath, string $qrPngPath, string $kodeVerifikasi, string $noSurat = ''): bool
    {
        return $this->injectVerificationCodeIntoDocx($docxPath, $kodeVerifikasi);
    }

    /**
     * Baca seluruh berkas di dalam ZIP (.docx) menjadi array asosiatif [nama_file => konten_biner].
     * Mendukung ekstensi ZipArchive, Pure PHP parser (zlib), dan CLI unzip sebagai fallback.
     */
    protected function readZipEntries(string $filePath): ?array
    {
        if (! is_file($filePath)) {
            return null;
        }

        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath) === true) {
                $entries = [];
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);
                    $entries[$name] = $zip->getFromIndex($i);
                }
                $zip->close();
                return $entries;
            }
        }

        // Pure PHP parser untuk format ZIP (memanfaatkan gzinflate bawaan PHP zlib)
        $data = @file_get_contents($filePath);
        if ($data !== false && strlen($data) >= 22) {
            $pos = strrpos($data, "\x50\x4b\x05\x06");
            if ($pos !== false) {
                $eocd = unpack('vdisk/vcdDisk/vcount/vtotalCount/VcdSize/VcdOffset/vcommentLen', substr($data, $pos + 4, 18));
                $cdOffset = $eocd['cdOffset'] ?? 0;
                $totalEntries = $eocd['totalCount'] ?? 0;

                if ($cdOffset < strlen($data) && $totalEntries > 0) {
                    $entries = [];
                    $p = $cdOffset;
                    for ($i = 0; $i < $totalEntries; $i++) {
                        if (substr($data, $p, 4) !== "\x50\x4b\x01\x02") {
                            break;
                        }
                        $h = unpack('vversion/vversionNeeded/vflag/vmethod/vmtime/vmdate/Vcrc/VcSize/VuSize/vnameLen/vextraLen/vcommentLen/vdisk/vattr/VextAttr/Voffset', substr($data, $p + 4, 42));
                        $p += 46;
                        $name = substr($data, $p, $h['nameLen']);
                        $p += $h['nameLen'] + $h['extraLen'] + $h['commentLen'];

                        $localOffset = $h['offset'];
                        if ($localOffset + 30 <= strlen($data)) {
                            $lh = unpack('vnameLen/vextraLen', substr($data, $localOffset + 26, 4));
                            $dataOffset = $localOffset + 30 + $lh['nameLen'] + $lh['extraLen'];
                            $compData = substr($data, $dataOffset, $h['cSize']);

                            if ($h['method'] == 8 && function_exists('gzinflate')) {
                                $content = @gzinflate($compData);
                            } else {
                                $content = $compData;
                            }
                            if ($content !== false) {
                                $entries[$name] = $content;
                            }
                        }
                    }
                    if (! empty($entries)) {
                        return $entries;
                    }
                }
            }
        }

        // Fallback CLI unzip jika fungsi exec aktif di server
        if (function_exists('exec')) {
            $extractDir = rtrim(sys_get_temp_dir(), '/') . '/sap-uz-' . Str::random(8);
            @mkdir($extractDir, 0775, true);
            $cmd = sprintf('unzip -q -o %s -d %s 2>&1', escapeshellarg($filePath), escapeshellarg($extractDir));
            @\exec($cmd, $out, $code);
            if ($code === 0) {
                $entries = [];
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($extractDir, \FilesystemIterator::SKIP_DOTS));
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $relName = str_replace('\\', '/', substr($file->getPathname(), strlen($extractDir) + 1));
                        $entries[$relName] = file_get_contents($file->getPathname());
                    }
                }
                $this->cleanupDirectory($extractDir);
                return $entries;
            }
            $this->cleanupDirectory($extractDir);
        }

        return null;
    }

    /**
     * Tulis ulang seluruh entri berkas ke file ZIP (.docx).
     * Mendukung ZipArchive, Pure-PHP writer (gzdeflate & pack), dan CLI zip.
     */
    protected function writeZipEntries(string $filePath, array $entries): bool
    {
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($filePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                foreach ($entries as $name => $content) {
                    $zip->addFromString($name, $content);
                }
                $zip->close();
                return is_file($filePath) && filesize($filePath) > 0;
            }
        }

        // Pure PHP writer untuk format ZIP (memanfaatkan gzdeflate & pack)
        if (function_exists('gzdeflate')) {
            $data = '';
            $cd = '';
            $count = count($entries);
            $dostime = ((int) date('Y') - 1980) << 25 | (int) date('m') << 21 | (int) date('d') << 16 | (int) date('H') << 11 | (int) date('i') << 5 | ((int) date('s') >> 1);

            foreach ($entries as $name => $content) {
                $offset = strlen($data);
                $crc = crc32($content);
                $uSize = strlen($content);
                $comp = gzdeflate($content, 6);
                $cSize = strlen($comp);
                $method = 8;

                if ($cSize >= $uSize) {
                    $comp = $content;
                    $cSize = $uSize;
                    $method = 0;
                }

                $nameLen = strlen($name);
                $header = pack('VvvvVVVVvv', 0x04034b50, 20, 0, $method, $dostime, $crc, $cSize, $uSize, $nameLen, 0);
                $data .= $header . $name . $comp;

                $cdRecord = pack('VvvvvVVVVvvvvvVV', 0x02014b50, 20, 20, 0, $method, $dostime, $crc, $cSize, $uSize, $nameLen, 0, 0, 0, 0, 32, $offset);
                $cd .= $cdRecord . $name;
            }

            $cdOffset = strlen($data);
            $cdSize = strlen($cd);
            $eocd = pack('VvvvvVVv', 0x06054b50, 0, 0, $count, $count, $cdSize, $cdOffset, 0);

            $ok = @file_put_contents($filePath, $data . $cd . $eocd);
            if ($ok !== false && is_file($filePath) && filesize($filePath) > 0) {
                return true;
            }
        }

        // Fallback CLI zip
        if (function_exists('exec')) {
            $packDir = rtrim(sys_get_temp_dir(), '/') . '/sap-zp-' . Str::random(8);
            @mkdir($packDir, 0775, true);
            foreach ($entries as $name => $content) {
                $targetFile = $packDir . '/' . $name;
                @mkdir(dirname($targetFile), 0775, true);
                file_put_contents($targetFile, $content);
            }
            @unlink($filePath);
            $cmd = sprintf('cd %s && zip -q -r %s . 2>&1', escapeshellarg($packDir), escapeshellarg($filePath));
            @\exec($cmd, $out, $code);
            $this->cleanupDirectory($packDir);
            return $code === 0 && is_file($filePath) && filesize($filePath) > 0;
        }

        return false;
    }

    /**
     * Tambahkan blok teks Kode Verifikasi resmi ke dalam document.xml dengan ukuran font 11 pt, rata kanan,
     * persis di bawah naskah sebelum <w:sectPr>, sesuai contoh referensi.docx.
     */
    protected function appendVerificationCodeToDocumentXml(string $docXml, string $kodeVerifikasi): string
    {
        $kodeClean = strtoupper(trim($kodeVerifikasi));

        // Hindari duplikasi jika kode verifikasi ini sudah ada di dalam dokumen
        if (str_contains($docXml, 'Kode Verifikasi: ' . $kodeClean) || str_contains($docXml, 'Kode verifikasi : ' . $kodeClean)) {
            return $docXml;
        }

        // Ukuran font 11 pt di OpenXML adalah 22 half-points (w:sz w:val="22" dan w:szCs w:val="22")
        // Rata kanan (w:jc w:val="end") persis seperti referensi.docx
        $kodeParagraph = '<w:p>' .
            '<w:pPr>' .
                '<w:pStyle w:val="Normal"/>' .
                '<w:jc w:val="end"/>' .
                '<w:spacing w:before="120" w:after="0"/>' .
                '<w:rPr>' .
                    '<w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>' .
                    '<w:sz w:val="22"/>' .
                    '<w:szCs w:val="22"/>' .
                '</w:rPr>' .
            '</w:pPr>' .
            '<w:r>' .
                '<w:rPr>' .
                    '<w:rFonts w:ascii="Times New Roman" w:hAnsi="Times New Roman" w:cs="Times New Roman"/>' .
                    '<w:sz w:val="22"/>' .
                    '<w:szCs w:val="22"/>' .
                '</w:rPr>' .
                '<w:t xml:space="preserve">Kode Verifikasi: ' . htmlspecialchars($kodeClean, ENT_XML1, 'UTF-8') . '</w:t>' .
            '</w:r>' .
        '</w:p>';

        if (str_contains($docXml, '<w:sectPr')) {
            $sectIdx = strrpos($docXml, '<w:sectPr');
            return substr($docXml, 0, $sectIdx) . $kodeParagraph . substr($docXml, $sectIdx);
        }

        if (str_contains($docXml, '</w:body>')) {
            $bodyIdx = strrpos($docXml, '</w:body>');
            return substr($docXml, 0, $bodyIdx) . $kodeParagraph . substr($docXml, $bodyIdx);
        }

        return $docXml;
    }

    /**
     * Alias kompatibilitas
     */
    protected function appendQrToDocumentXml(string $docXml, string $kodeVerifikasi): string
    {
        return $this->appendVerificationCodeToDocumentXml($docXml, $kodeVerifikasi);
    }

    /**
     * Render dokumen Word asli menjadi PDF resmi dan gambar PNG pratinjau (beresolusi tinggi,
     * mempertahankan 100% tata letak dokumen Word asli dan menyertakan QR Code & Kode Verifikasi di kanan bawah).
     *
     * @return array{image: ?string, pdf: ?string}
     */
    protected function renderWordAsOriginalDocument(string $sourcePath, string $badgePngPath, SuratKeluar $suratKeluar, string $tempDir): array
    {
        if (! function_exists('exec') || app()->runningUnitTests()) {
            return ['image' => null, 'pdf' => null];
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $inputDoc = $tempDir . '/input_asli.' . ($ext ?: 'docx');
        copy($sourcePath, $inputDoc);

        $userProfileDir = $tempDir . '/lo-profile';
        $cmd = sprintf(
            'soffice %s --headless --convert-to pdf --outdir %s %s 2>&1',
            escapeshellarg('-env:UserInstallation=file://' . $userProfileDir),
            escapeshellarg($tempDir),
            escapeshellarg($inputDoc)
        );
        @\exec($cmd, $out, $code);

        $pdfCandidate = $tempDir . '/input_asli.pdf';
        if (! is_file($pdfCandidate)) {
            return ['image' => null, 'pdf' => null];
        }

        // Simpan berkas PDF hasil konversi Word asli yang memuat QR code
        $savedPdfRel = 'surat-keluar/asli-pdf-' . ($suratKeluar->id ?: time()) . '-' . Str::random(4) . '.pdf';
        Storage::disk('public')->put($savedPdfRel, file_get_contents($pdfCandidate));

        // Ubah PDF hasil render Word asli menjadi gambar PNG (150 DPI) menggunakan pdftoppm
        $imgPrefix = $tempDir . '/page';
        $ppmCmd = sprintf('pdftoppm -png -r 150 %s %s 2>&1', escapeshellarg($pdfCandidate), escapeshellarg($imgPrefix));
        @\exec($ppmCmd, $ppmOut, $ppmCode);

        $pages = glob($tempDir . '/page*.png') ?: [];
        sort($pages);
        if (empty($pages)) {
            return ['image' => null, 'pdf' => $savedPdfRel];
        }

        $page1Path = $pages[0];

        // Sebagai proteksi ganda: pastikan badge QR Code tampak jelas di pojok kanan bawah
        if (function_exists('imagecreatefrompng') && is_file($badgePngPath)) {
            $docImg = @imagecreatefrompng($page1Path);
            $badgeImg = @imagecreatefrompng($badgePngPath);
            if ($docImg && $badgeImg) {
                $docW = imagesx($docImg);
                $docH = imagesy($docImg);
                $bW = imagesx($badgeImg);
                $bH = imagesy($badgeImg);

                // Ukuran badge sekitar 16% lebar halaman
                $targetW = (int) round($docW * 0.16);
                $targetH = (int) round(($targetW / $bW) * $bH);

                $marginRight = (int) round($docW * 0.05);
                $marginBottom = (int) round($docH * 0.05);

                $destX = $docW - $targetW - $marginRight;
                $destY = $docH - $targetH - $marginBottom;

                imagecopyresampled($docImg, $badgeImg, $destX, $destY, 0, 0, $targetW, $targetH, $bW, $bH);
                imagepng($docImg, $page1Path);
                imagedestroy($docImg);
                imagedestroy($badgeImg);
            }
        }

        $savedImageRel = 'surat-keluar/dokumen-' . ($suratKeluar->id ?: time()) . '-' . Str::random(4) . '.png';
        Storage::disk('public')->put($savedImageRel, file_get_contents($page1Path));

        return [
            'image' => $savedImageRel,
            'pdf' => $savedPdfRel,
        ];
    }

    /**
     * Konversi file docx menjadi konten binary PDF langsung menggunakan LibreOffice.
     */
    public function convertDocxToPdf(string $docxAbsolutePath): ?string
    {
        if (! function_exists('exec') || ! is_file($docxAbsolutePath) || app()->runningUnitTests()) {
            return null;
        }

        $tempDir = rtrim(sys_get_temp_dir(), '/') . '/sap-lo-pdf-' . Str::random(8);
        @mkdir($tempDir, 0775, true);

        try {
            $userProfileDir = $tempDir . '/lo-profile';
            $cmd = sprintf(
                'soffice %s --headless --convert-to pdf --outdir %s %s 2>&1',
                escapeshellarg('-env:UserInstallation=file://' . $userProfileDir),
                escapeshellarg($tempDir),
                escapeshellarg($docxAbsolutePath)
            );
            @\exec($cmd, $out, $code);

            $pdfFiles = glob($tempDir . '/*.pdf') ?: [];
            if (! empty($pdfFiles) && is_file($pdfFiles[0])) {
                return file_get_contents($pdfFiles[0]);
            }

            return null;
        } finally {
            $this->cleanupDirectory($tempDir);
        }
    }

    protected function cleanupDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        foreach (scandir($dir) ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $path = $dir . '/' . $item;
            if (is_dir($path)) {
                $this->cleanupDirectory($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}

