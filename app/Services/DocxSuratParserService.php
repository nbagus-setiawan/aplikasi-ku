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

            $userProfileDir = $tempDir . '/lo-profile';
            $cmd = sprintf(
                'soffice -env:UserInstallation=file://%s --headless --convert-to pdf --outdir %s %s 2>&1',
                $userProfileDir,
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
            $options = new QROptions([
                'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
                'scale' => 6,
                'outputBase64' => false,
                'imageTransparent' => false,
            ]);
            $pngData = (new QRCode($options))->render($url);
            if (is_string($pngData) && strlen($pngData) > 0) {
                file_put_contents($targetPngPath, $pngData);
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
            $scale = 6;
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
     * Pastikan surat keluar yang berasal dari upload Word sudah memiliki gambar tampilan ber-QR dan file Word ber-QR.
     */
    public function ensureWordQrProcessed(SuratKeluar $suratKeluar): void
    {
        if (empty($suratKeluar->file_surat_asli)) {
            return;
        }

        $hasPngFinal = ! empty($suratKeluar->file_final)
            && str_ends_with(strtolower($suratKeluar->file_final), '.png')
            && Storage::disk('public')->exists($suratKeluar->file_final);

        $hasQrWord = str_contains($suratKeluar->file_surat_asli, 'word-qr-')
            && Storage::disk('public')->exists($suratKeluar->file_surat_asli);

        if ($hasPngFinal && $hasQrWord) {
            return;
        }

        $absSource = storage_path('app/public/' . $suratKeluar->file_surat_asli);
        if (! is_file($absSource)) {
            return;
        }

        $hasil = $this->processOriginalWordWithQr($suratKeluar, $absSource);
        $updated = false;

        if (! empty($hasil['file_word_qr'])) {
            $suratKeluar->file_surat_asli = $hasil['file_word_qr'];
            $updated = true;
        }
        if (! empty($hasil['file_tampilan'])) {
            $suratKeluar->file_final = $hasil['file_tampilan'];
            $updated = true;
        }
        if ($updated) {
            $suratKeluar->save();
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
            return ['file_word_qr' => null, 'file_tampilan' => null];
        }

        // Gunakan /tmp agar path bebas dari spasi atau karakter '|' saat dipanggil oleh LibreOffice & pdftoppm
        $tempDir = rtrim(sys_get_temp_dir(), '/') . '/sap-word-' . ($suratKeluar->id ?: 'new') . '-' . Str::random(8);
        if (! is_dir($tempDir)) {
            mkdir($tempDir, 0775, true);
        }

        try {
            $ext = strtolower(pathinfo($sourceAbsolutePath, PATHINFO_EXTENSION));
            $verifikasiUrl = url('/verifikasi/' . $suratKeluar->kode_verifikasi);
            $qrPngPath = $tempDir . '/qr.png';
            $this->generateQrPngFile($verifikasiUrl, $qrPngPath);

            // Render tampilan asli dari dokumen sumber terlebih dahulu, lalu tempelkan QR & Kode Verifikasi
            $savedImageRel = $this->renderWordAsOriginalImage($sourceAbsolutePath, $qrPngPath, $suratKeluar, $tempDir);

            $workingDocx = $tempDir . '/surat_ber_qr.docx';
            copy($sourceAbsolutePath, $workingDocx);

            $savedWordQrRel = null;
            if ($ext === 'docx' && is_file($qrPngPath)) {
                $this->injectQrIntoDocx($workingDocx, $qrPngPath, $suratKeluar->kode_verifikasi, $suratKeluar->no_surat);
                $savedWordQrRel = 'surat-keluar/word-qr-' . ($suratKeluar->id ?: time()) . '-' . Str::random(4) . '.docx';
                Storage::disk('public')->put($savedWordQrRel, file_get_contents($workingDocx));
            }

            return [
                'file_word_qr' => $savedWordQrRel,
                'file_tampilan' => $savedImageRel ?: $savedWordQrRel,
            ];
        } finally {
            $this->cleanupDirectory($tempDir);
        }
    }

    /**
     * Sisipkan QR Code PNG dan teks Kode Verifikasi langsung ke dalam file .docx asli
     * menggunakan PHP ZipArchive murni (tanpa python, tanpa exec/shell).
     */
    protected function injectQrIntoDocx(string $docxPath, string $qrPngPath, string $kodeVerifikasi, string $noSurat): bool
    {
        if (! class_exists(\ZipArchive::class) || ! is_file($docxPath) || ! is_file($qrPngPath)) {
            return false;
        }

        $zip = new \ZipArchive();
        if ($zip->open($docxPath) !== true) {
            return false;
        }

        // 1. Salin gambar QR ke word/media/qr_verifikasi.png
        $zip->addFile($qrPngPath, 'word/media/qr_verifikasi.png');

        // 2. Pastikan [Content_Types].xml mendukung png
        $ctXml = $zip->getFromName('[Content_Types].xml');
        if ($ctXml !== false && ! str_contains($ctXml, 'Extension="png"')) {
            $ctXml = str_replace('</Types>', '<Default Extension="png" ContentType="image/png"/></Types>', $ctXml);
            $zip->addFromString('[Content_Types].xml', $ctXml);
        }

        // 3. Tambahkan relationship rIdQrVerif99 di word/_rels/document.xml.rels
        $relsXml = $zip->getFromName('word/_rels/document.xml.rels');
        if ($relsXml !== false && ! str_contains($relsXml, 'rIdQrVerif99')) {
            $relTag = '<Relationship Id="rIdQrVerif99" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" Target="media/qr_verifikasi.png"/>';
            $relsXml = str_replace('</Relationships>', $relTag . '</Relationships>', $relsXml);
            $zip->addFromString('word/_rels/document.xml.rels', $relsXml);
        }

        // 4. Tambahkan blok QR & Kode Verifikasi ke word/document.xml
        $docXml = $zip->getFromName('word/document.xml');
        if ($docXml !== false && ! str_contains($docXml, 'rIdQrVerif99')) {
            $nsAdd = [
                'xmlns:r=' => 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"',
                'xmlns:wp=' => 'xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"',
                'xmlns:a=' => 'xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"',
                'xmlns:pic=' => 'xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"',
            ];
            foreach ($nsAdd as $prefix => $attr) {
                if (! str_contains($docXml, $prefix)) {
                    $docXml = preg_replace('/<w:document\s+/', '<w:document ' . $attr . ' ', $docXml, 1);
                }
            }

            $kodeEscaped = htmlspecialchars($kodeVerifikasi, ENT_XML1, 'UTF-8');
            $qrPara = '<w:p>' .
                '<w:pPr><w:jc w:val="left"/><w:spacing w:before="40" w:after="0"/></w:pPr>' .
                '<w:r>' .
                    '<w:drawing>' .
                        '<wp:inline distT="0" distB="0" distL="0" distR="0">' .
                            '<wp:extent cx="680000" cy="680000"/>' .
                            '<wp:docPr id="9991" name="QR Verifikasi"/>' .
                            '<a:graphic xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">' .
                                '<a:graphicData uri="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
                                    '<pic:pic xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">' .
                                        '<pic:nvPicPr>' .
                                            '<pic:cNvPr id="9991" name="qr_verifikasi.png"/>' .
                                            '<pic:cNvPicPr/>' .
                                        '</pic:nvPicPr>' .
                                        '<pic:blipFill>' .
                                            '<a:blip r:embed="rIdQrVerif99"/>' .
                                            '<a:stretch><a:fillRect/></a:stretch>' .
                                        '</pic:blipFill>' .
                                        '<pic:spPr>' .
                                            '<a:xfrm><a:off x="0" y="0"/><a:ext cx="680000" cy="680000"/></a:xfrm>' .
                                            '<a:prstGeom prst="rect"><a:avLst/></a:prstGeom>' .
                                        '</pic:spPr>' .
                                    '</pic:pic>' .
                                '</a:graphicData>' .
                            '</a:graphic>' .
                        '</wp:inline>' .
                    '</w:drawing>' .
                '</w:r>' .
            '</w:p>' .
            '<w:p>' .
                '<w:pPr><w:jc w:val="left"/><w:spacing w:before="0" w:after="0"/></w:pPr>' .
                '<w:r>' .
                    '<w:rPr><w:sz w:val="16"/><w:b/></w:rPr>' .
                    '<w:t>Kode Verifikasi: ' . $kodeEscaped . '</w:t>' .
                '</w:r>' .
            '</w:p>';

            if (str_contains($docXml, '<w:sectPr')) {
                $idx = strrpos($docXml, '<w:sectPr');
                $docXml = substr($docXml, 0, $idx) . $qrPara . substr($docXml, $idx);
            } else {
                $docXml = str_replace('</w:body>', $qrPara . '</w:body>', $docXml);
            }

            $zip->addFromString('word/document.xml', $docXml);
        }

        $zip->close();

        return true;
    }

    /**
     * Render tampilan asli Word menjadi gambar PNG beresolusi tinggi (seperti foto dokumen Word aslinya,
     * lengkap dari header sampai footer + QR & Kode Verifikasi).
     */
    protected function renderWordAsOriginalImage(string $sourcePath, string $qrPngPath, SuratKeluar $suratKeluar, string $tempDir): ?string
    {
        if (! function_exists('exec')) {
            return null;
        }

        $ext = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));
        $inputDoc = $tempDir . '/input_asli.' . ($ext ?: 'docx');
        copy($sourcePath, $inputDoc);

        $userProfileDir = $tempDir . '/lo-profile';
        $cmd = sprintf(
            'soffice -env:UserInstallation=file://%s --headless --convert-to pdf --outdir %s %s 2>&1',
            $userProfileDir,
            escapeshellarg($tempDir),
            escapeshellarg($inputDoc)
        );
        @\exec($cmd, $out, $code);

        $pdfCandidate = $tempDir . '/input_asli.pdf';
        if (! is_file($pdfCandidate)) {
            return null;
        }

        // Ubah PDF hasil render Word asli menjadi gambar PNG (150 DPI) menggunakan pdftoppm
        $imgPrefix = $tempDir . '/page';
        $ppmCmd = sprintf('pdftoppm -png -r 150 %s %s 2>&1', escapeshellarg($pdfCandidate), escapeshellarg($imgPrefix));
        @\exec($ppmCmd, $ppmOut, $ppmCode);

        $pages = glob($tempDir . '/page*.png') ?: [];
        sort($pages);
        if (empty($pages)) {
            return null;
        }

        $page1Path = $pages[0];

        // Tempelkan QR Code & Kode Verifikasi secara jelas di pojok kiri bawah halaman dokumen asli
        // (agar tidak menabrak tanda tangan yang umumnya di kanan bawah, serta selalu tampak jelas)
        if (function_exists('imagecreatefrompng') && is_file($qrPngPath)) {
            $docImg = @imagecreatefrompng($page1Path);
            $qrImg = @imagecreatefrompng($qrPngPath);
            if ($docImg && $qrImg) {
                $docW = imagesx($docImg);
                $docH = imagesy($docImg);
                $qrW = imagesx($qrImg);
                $qrH = imagesy($qrImg);

                $targetQrSize = (int) round($docW * 0.115);
                $destX = (int) round($docW * 0.065);
                $destY = $docH - $targetQrSize - (int) round($docH * 0.065);

                $white = imagecolorallocate($docImg, 255, 255, 255);
                $borderCol = imagecolorallocate($docImg, 16, 185, 129);
                $black = imagecolorallocate($docImg, 15, 23, 42);

                $boxX1 = $destX - 10;
                $boxY1 = $destY - 10;
                $boxX2 = $destX + $targetQrSize + 28;
                $boxY2 = $destY + $targetQrSize + 30;

                imagefilledrectangle($docImg, $boxX1, $boxY1, $boxX2, $boxY2, $white);
                imagerectangle($docImg, $boxX1, $boxY1, $boxX2, $boxY2, $borderCol);

                imagecopyresampled($docImg, $qrImg, $destX + 8, $destY, 0, 0, $targetQrSize, $targetQrSize, $qrW, $qrH);
                $label = 'Kode: ' . $suratKeluar->kode_verifikasi;
                imagestring($docImg, 3, $destX + 2, $destY + $targetQrSize + 7, $label, $black);

                imagepng($docImg, $page1Path);
                imagedestroy($docImg);
                imagedestroy($qrImg);
            }
        }

        $storageRel = 'surat-keluar/dokumen-' . ($suratKeluar->id ?: time()) . '-' . time() . '.png';
        Storage::disk('public')->put($storageRel, file_get_contents($page1Path));

        return $storageRel;
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

