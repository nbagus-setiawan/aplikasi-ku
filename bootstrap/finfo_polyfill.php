<?php

/**
 * Polyfill untuk ekstensi php_fileinfo (kelas finfo dan fungsi-fungsinya).
 * Diperlukan pada lingkungan server hosting cPanel yang menonaktifkan atau mengunci ekstensi php_fileinfo.
 * Memastikan library seperti Flysystem (league/mime-type-detection) dan Symfony Mime tidak error saat upload berkas.
 */

if (!defined('FILEINFO_NONE')) {
    define('FILEINFO_NONE', 0);
}
if (!defined('FILEINFO_SYMLINK')) {
    define('FILEINFO_SYMLINK', 2);
}
if (!defined('FILEINFO_MIME_TYPE')) {
    define('FILEINFO_MIME_TYPE', 16);
}
if (!defined('FILEINFO_CONTINUE')) {
    define('FILEINFO_CONTINUE', 32);
}
if (!defined('FILEINFO_PRESERVE_ATIME')) {
    define('FILEINFO_PRESERVE_ATIME', 128);
}
if (!defined('FILEINFO_RAW')) {
    define('FILEINFO_RAW', 256);
}
if (!defined('FILEINFO_MIME_ENCODING')) {
    define('FILEINFO_MIME_ENCODING', 1024);
}
if (!defined('FILEINFO_MIME')) {
    define('FILEINFO_MIME', 1040); // 1024 | 16
}
if (!defined('FILEINFO_EXTENSION')) {
    define('FILEINFO_EXTENSION', 2097152);
}

if (!class_exists('finfo')) {
    class finfo
    {
        private int $flags;

        public function __construct(int $flags = FILEINFO_MIME_TYPE, ?string $magicFile = null)
        {
            $this->flags = $flags;
        }

        public function set_flags(int $flags): bool
        {
            $this->flags = $flags;
            return true;
        }

        public function file(string $filename = '', int $flags = FILEINFO_NONE, $context = null): string|false
        {
            if (!is_file($filename) || !is_readable($filename)) {
                return false;
            }

            // 1. Deteksi gambar via getimagesize (built-in PHP GD)
            if (function_exists('getimagesize')) {
                $img = @getimagesize($filename);
                if ($img && !empty($img['mime'])) {
                    return $img['mime'];
                }
            }

            // 2. Deteksi via header berkas (magic bytes)
            $handle = @fopen($filename, 'rb');
            if ($handle) {
                $sample = @fread($handle, 16);
                @fclose($handle);
                if ($sample !== false) {
                    $mime = $this->detectMagicBytes($sample);
                    if ($mime) {
                        return $mime;
                    }
                }
            }

            // 3. Deteksi via ekstensi nama file
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            return $this->mimeFromExtension($ext) ?: 'application/octet-stream';
        }

        public function buffer(string $string = '', int $flags = FILEINFO_NONE, $context = null): string|false
        {
            if ($string === '') {
                return false;
            }

            $mime = $this->detectMagicBytes($string);
            return $mime ?: false;
        }

        private function detectMagicBytes(string $sample): ?string
        {
            if (str_starts_with($sample, "\xFF\xD8\xFF")) {
                return 'image/jpeg';
            }
            if (str_starts_with($sample, "\x89PNG\r\n\x1a\n")) {
                return 'image/png';
            }
            if (str_starts_with($sample, 'GIF87a') || str_starts_with($sample, 'GIF89a')) {
                return 'image/gif';
            }
            if (str_starts_with($sample, 'RIFF') && substr($sample, 8, 4) === 'WEBP') {
                return 'image/webp';
            }
            if (str_starts_with($sample, '%PDF-')) {
                return 'application/pdf';
            }
            if (str_starts_with($sample, "PK\x03\x04")) {
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            }
            if (str_starts_with($sample, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1")) {
                return 'application/msword';
            }
            if (str_starts_with($sample, 'BM')) {
                return 'image/bmp';
            }

            return null;
        }

        private function mimeFromExtension(string $ext): ?string
        {
            return match ($ext) {
                'jpg', 'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif',
                'webp' => 'image/webp',
                'svg' => 'image/svg+xml',
                'ico' => 'image/x-icon',
                'pdf' => 'application/pdf',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'doc' => 'application/msword',
                'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'xls' => 'application/vnd.ms-excel',
                'csv' => 'text/csv',
                'txt' => 'text/plain',
                'html', 'htm' => 'text/html',
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                '7z' => 'application/x-7z-compressed',
                default => null,
            };
        }
    }
}

if (!function_exists('finfo_open')) {
    function finfo_open(int $flags = FILEINFO_MIME_TYPE, ?string $magicFile = null)
    {
        return new finfo($flags, $magicFile);
    }
}

if (!function_exists('finfo_file')) {
    function finfo_file($finfo, string $filename = '', int $flags = FILEINFO_NONE, $context = null)
    {
        return $finfo instanceof finfo ? $finfo->file($filename, $flags, $context) : false;
    }
}

if (!function_exists('finfo_buffer')) {
    function finfo_buffer($finfo, string $string = '', int $flags = FILEINFO_NONE, $context = null)
    {
        return $finfo instanceof finfo ? $finfo->buffer($string, $flags, $context) : false;
    }
}

if (!function_exists('finfo_close')) {
    function finfo_close($finfo): bool
    {
        return true;
    }
}

if (!function_exists('finfo_set_flags')) {
    function finfo_set_flags($finfo, int $flags): bool
    {
        return $finfo instanceof finfo ? $finfo->set_flags($flags) : false;
    }
}

if (!function_exists('mime_content_type')) {
    function mime_content_type($filename)
    {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $result = $finfo->file($filename);
        return $result !== false ? $result : 'application/octet-stream';
    }
}
