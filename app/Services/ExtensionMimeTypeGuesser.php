<?php

namespace App\Services;

use Symfony\Component\Mime\MimeTypeGuesserInterface;

class ExtensionMimeTypeGuesser implements MimeTypeGuesserInterface
{
    /**
     * Selalu aktif sebagai fallback guesser jika server hosting tidak mengaktifkan ekstensi php_fileinfo.
     */
    public function isGuesserSupported(): bool
    {
        return true;
    }

    /**
     * Tebak MIME type berkas secara akurat tanpa bergantung pada ekstensi php_fileinfo.
     */
    public function guessMimeType(string $path): ?string
    {
        // 1. Coba deteksi gambar via getimagesize (built-in PHP GD/core, tanpa php_fileinfo)
        if (function_exists('getimagesize') && is_file($path) && is_readable($path)) {
            $imageInfo = @getimagesize($path);
            if ($imageInfo && !empty($imageInfo['mime'])) {
                return $imageInfo['mime'];
            }
        }

        // 2. Coba deteksi via magic bytes berkas (PDF, DOCX, ZIP, dll)
        if (is_file($path) && is_readable($path)) {
            $magicMime = $this->guessFromMagicBytes($path);
            if ($magicMime !== null && !in_array($magicMime, ['application/zip', 'application/x-ole-storage'])) {
                return $magicMime;
            }
        }

        // 3. Jika berkas berasal dari HTTP upload Laravel (/tmp/phpXXXXXX)
        if (function_exists('request')) {
            try {
                $clientExt = $this->getClientExtensionFromCurrentRequest($path);
                if ($clientExt) {
                    $mime = $this->mimeFromExtension($clientExt);
                    if ($mime) {
                        return $mime;
                    }
                }
            } catch (\Throwable) {
                // Abaikan jika di luar konteks HTTP (misal di console/seeder)
            }
        }

        // 4. Cek ekstensi dari path berkas langsung jika ada
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($ext !== '') {
            $mime = $this->mimeFromExtension($ext);
            if ($mime) {
                return $mime;
            }
        }

        // 5. Fallback jika merupakan arsip zip/docx
        if (isset($magicMime) && $magicMime === 'application/zip') {
            return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        }

        return 'application/octet-stream';
    }

    /**
     * Cari ekstensi asli file dari UploadedFile pada HTTP Request aktif.
     */
    protected function getClientExtensionFromCurrentRequest(string $path): ?string
    {
        $request = request();
        if (!$request) {
            return null;
        }

        $files = $request->allFiles();
        if (empty($files)) {
            return null;
        }

        $flattened = [];
        array_walk_recursive($files, function ($file) use (&$flattened) {
            if ($file instanceof \Symfony\Component\HttpFoundation\File\UploadedFile) {
                $flattened[] = $file;
            }
        });

        foreach ($flattened as $file) {
            if ($file->getRealPath() === $path || $file->getPathname() === $path) {
                return strtolower($file->getClientOriginalExtension());
            }
        }

        return null;
    }

    /**
     * Deteksi MIME type dari header magic bytes.
     */
    protected function guessFromMagicBytes(string $path): ?string
    {
        $handle = @fopen($path, 'rb');
        if (!$handle) {
            return null;
        }
        $bytes = @fread($handle, 16);
        @fclose($handle);

        if ($bytes === false || strlen($bytes) < 4) {
            return null;
        }

        // JPEG: FF D8 FF
        if (str_starts_with($bytes, "\xFF\xD8\xFF")) {
            return 'image/jpeg';
        }

        // PNG: 89 50 4E 47 0D 0A 1A 0A
        if (str_starts_with($bytes, "\x89PNG\r\n\x1a\n")) {
            return 'image/png';
        }

        // GIF: GIF87a atau GIF89a
        if (str_starts_with($bytes, 'GIF87a') || str_starts_with($bytes, 'GIF89a')) {
            return 'image/gif';
        }

        // WEBP: RIFF....WEBP
        if (str_starts_with($bytes, 'RIFF') && substr($bytes, 8, 4) === 'WEBP') {
            return 'image/webp';
        }

        // PDF: %PDF-
        if (str_starts_with($bytes, '%PDF-')) {
            return 'application/pdf';
        }

        // ZIP / DOCX / XLSX: PK\x03\x04
        if (str_starts_with($bytes, "PK\x03\x04")) {
            return 'application/zip';
        }

        // OLE Compound (DOC, XLS): D0 CF 11 E0 A1 B1 1A E1
        if (str_starts_with($bytes, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1")) {
            return 'application/x-ole-storage';
        }

        // BMP: BM
        if (str_starts_with($bytes, 'BM')) {
            return 'image/bmp';
        }

        return null;
    }

    /**
     * Peta ekstensi ke MIME type umum.
     */
    protected function mimeFromExtension(string $ext): ?string
    {
        return match ($ext) {
            // Gambar
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'bmp' => 'image/bmp',

            // Dokumen
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'doc' => 'application/msword',
            'pdf' => 'application/pdf',
            'rtf' => 'application/rtf',
            'txt' => 'text/plain',
            'html', 'htm' => 'text/html',

            // Spreadsheet & Arsip
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'xls' => 'application/vnd.ms-excel',
            'csv' => 'text/csv',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',

            default => null,
        };
    }
}
