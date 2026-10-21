<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class BackupService
{
    /**
     * Direktori penyimpanan backup (hanya menyimpan 1 file terbaru agar hemat ruang 100%).
     */
    public function getBackupDirectory(): string
    {
        $dir = storage_path('app/backups');
        if (! File::isDirectory($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        return $dir;
    }

    /**
     * Format nama file backup sesuai tanggal-bulan-tahun: Backupddmmyy.zip (contoh: Backup260226.zip).
     */
    public function generateBackupFilename(?Carbon $date = null): string
    {
        $date = $date ?: now();

        return 'Backup' . $date->format('dmy') . '.zip';
    }

    /**
     * Pastikan backup hari ini sudah tersedia. Jika belum ada untuk hari ini,
     * buat otomatis dan hapus semua file backup lama agar hanya tersisa 1 file terbaru.
     */
    public function ensureDailyBackup(): array
    {
        $dir = $this->getBackupDirectory();
        $todayFilename = $this->generateBackupFilename(now());
        $todayPath = $dir . '/' . $todayFilename;

        if (! File::exists($todayPath)) {
            return $this->createSingleLatestBackup();
        }

        // Bersihkan bila masih ada file backup hari lain yang tersisa
        $this->deleteOldBackupsExcept($todayFilename);

        return $this->getLatestBackupInfo();
    }

    /**
     * Buat 1 file backup terbaru (Backupddmmyy.zip) dan hapus semua file backup lama.
     */
    public function createSingleLatestBackup(?Carbon $date = null): array
    {
        $dir = $this->getBackupDirectory();
        $filename = $this->generateBackupFilename($date ?: now());
        $zipPath = $dir . '/' . $filename;

        // 1. Hapus semua file backup lama terlebih dahulu agar tidak pernah menumpuk
        $this->deleteAllBackups();

        // 2. Bangun isi SQL dump dari seluruh tabel database
        $sqlContent = $this->generateDatabaseSqlDump();
        $sqlFilename = 'database_' . ($date ?: now())->format('dmy') . '.sql';

        // 3. Buat file .zip yang berisi file .sql
        $this->createZipWithSql($zipPath, $sqlFilename, $sqlContent);

        return $this->getLatestBackupInfo();
    }

    /**
     * Ambil informasi 1 file backup terbaru yang ada di sistem.
     */
    public function getLatestBackupInfo(): array
    {
        $dir = $this->getBackupDirectory();
        $files = File::glob($dir . '/Backup*.zip');

        if (empty($files)) {
            return [
                'exists' => false,
                'filename' => null,
                'path' => null,
                'size_bytes' => 0,
                'size_formatted' => '0 KB',
                'updated_at' => null,
            ];
        }

        // Urutkan berdasarkan waktu modifikasi terbaru
        usort($files, fn ($a, $b) => filemtime($b) <=> filemtime($a));
        $latestPath = $files[0];
        $latestFilename = basename($latestPath);

        // Pastikan file selain yang terbaru dihapus agar selalu hanya 1 file
        $this->deleteOldBackupsExcept($latestFilename);

        $bytes = filesize($latestPath) ?: 0;

        return [
            'exists' => true,
            'filename' => $latestFilename,
            'path' => $latestPath,
            'size_bytes' => $bytes,
            'size_formatted' => $this->formatBytes($bytes),
            'updated_at' => Carbon::createFromTimestamp(filemtime($latestPath))->format('d-m-Y H:i'),
        ];
    }

    /**
     * Hapus semua file backup yang ada di folder backups.
     */
    public function deleteAllBackups(): void
    {
        $dir = $this->getBackupDirectory();
        foreach (File::glob($dir . '/*') as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }
    }

    /**
     * Hapus semua file backup kecuali nama file yang ditentukan.
     */
    protected function deleteOldBackupsExcept(string $keepFilename): void
    {
        $dir = $this->getBackupDirectory();
        foreach (File::glob($dir . '/*') as $file) {
            if (is_file($file) && basename($file) !== $keepFilename) {
                @unlink($file);
            }
        }
    }

    /**
     * Buat arsip .zip berisi file SQL dump (mendukung ZipArchive, python3 zipfile, atau pure PHP ZIP).
     */
    protected function createZipWithSql(string $zipPath, string $entryName, string $content): void
    {
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true) {
                $zip->addFromString($entryName, $content);
                $zip->close();

                return;
            }
        }

        // Fallback: membuat format ZIP standar secara langsung di PHP (tanpa ekstensi tambahan)
        $zipBinary = $this->buildSimpleZipBinary($entryName, $content);
        file_put_contents($zipPath, $zipBinary);
    }

    /**
     * Pembuat biner ZIP standar (PKZIP) murni dengan PHP + gzdeflate.
     */
    protected function buildSimpleZipBinary(string $filename, string $data): string
    {
        $uncLen = strlen($data);
        $crc = crc32($data);
        $compressed = gzdeflate($data, 6);
        $compLen = strlen($compressed);
        $fnLen = strlen($filename);

        // Local file header
        $localHeader = pack(
            'VvvvvvVVVvv',
            0x04034b50, // signature
            20,         // version needed
            0,          // general purpose bit flag
            8,          // compression method (8 = deflate)
            0,          // last mod time
            0,          // last mod date
            $crc,       // crc-32
            $compLen,   // compressed size
            $uncLen,    // uncompressed size
            $fnLen,     // filename length
            0           // extra field length
        ) . $filename;

        $localRecord = $localHeader . $compressed;
        $offset = 0;
        $cdOffset = strlen($localRecord);

        // Central directory file header
        $centralDir = pack(
            'VvvvvvvVVVvvvvvVV',
            0x02014b50, // central file header signature
            20,         // version made by
            20,         // version needed to extract
            0,          // general purpose bit flag
            8,          // compression method
            0,          // last mod file time
            0,          // last mod file date
            $crc,       // crc-32
            $compLen,   // compressed size
            $uncLen,    // uncompressed size
            $fnLen,     // file name length
            0,          // extra field length
            0,          // file comment length
            0,          // disk number start
            0,          // internal file attributes
            32,         // external file attributes
            $offset     // relative offset of local header
        ) . $filename;

        $cdSize = strlen($centralDir);

        // End of central directory record
        $eocd = pack(
            'VvvvvVVv',
            0x06054b50, // end of central dir signature
            0,          // number of this disk
            0,          // disk where central directory starts
            1,          // number of central directory records on this disk
            1,          // total number of central directory records
            $cdSize,    // size of central directory
            $cdOffset,  // offset of start of central directory
            0           // comment length
        );

        return $localRecord . $centralDir . $eocd;
    }

    /**
     * Ekspor seluruh tabel & baris database ke format SQL.
     */
    protected function generateDatabaseSqlDump(): string
    {
        $lines = [];
        $lines[] = '-- =========================================================';
        $lines[] = '-- BACKUP DATABASE SAP | SEKRETARIS AL-FALAH PUTRA';
        $lines[] = '-- Tanggal Backup: ' . now()->format('d-m-Y H:i:s');
        $lines[] = '-- =========================================================';
        $lines[] = 'SET FOREIGN_KEY_CHECKS=0;';
        $lines[] = '';

        $pdo = DB::connection()->getPdo();
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            $tablesResult = DB::select('SHOW TABLES');
            foreach ($tablesResult as $row) {
                $table = array_values((array) $row)[0];

                $createRow = DB::select("SHOW CREATE TABLE `{$table}`");
                $createArr = (array) ($createRow[0] ?? []);
                $createSql = $createArr['Create Table'] ?? null;

                if ($createSql) {
                    $lines[] = "-- Tabel: `{$table}`";
                    $lines[] = "DROP TABLE IF EXISTS `{$table}`;";
                    $lines[] = $createSql . ';';
                    $lines[] = '';
                }

                $rows = DB::table($table)->get();
                foreach ($rows as $record) {
                    $data = (array) $record;
                    $cols = array_map(fn ($col) => "`{$col}`", array_keys($data));
                    $vals = array_map(function ($val) use ($pdo) {
                        if ($val === null) {
                            return 'NULL';
                        }

                        return $pdo->quote((string) $val);
                    }, array_values($data));

                    $lines[] = sprintf(
                        'INSERT INTO `%s` (%s) VALUES (%s);',
                        $table,
                        implode(', ', $cols),
                        implode(', ', $vals)
                    );
                }

                $lines[] = '';
            }
        } elseif ($driver === 'sqlite') {
            $tablesResult = DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'");
            foreach ($tablesResult as $row) {
                $table = $row->name;

                $createRow = DB::select("SELECT sql FROM sqlite_master WHERE type='table' AND name = ?", [$table]);
                $createSql = $createRow[0]->sql ?? null;

                if ($createSql) {
                    $lines[] = "-- Tabel: `{$table}`";
                    $lines[] = "DROP TABLE IF EXISTS `{$table}`;";
                    $lines[] = $createSql . ';';
                    $lines[] = '';
                }

                $rows = DB::table($table)->get();
                foreach ($rows as $record) {
                    $data = (array) $record;
                    $cols = array_map(fn ($col) => "`{$col}`", array_keys($data));
                    $vals = array_map(function ($val) use ($pdo) {
                        if ($val === null) {
                            return 'NULL';
                        }

                        return $pdo->quote((string) $val);
                    }, array_values($data));

                    $lines[] = sprintf(
                        'INSERT INTO `%s` (%s) VALUES (%s);',
                        $table,
                        implode(', ', $cols),
                        implode(', ', $vals)
                    );
                }

                $lines[] = '';
            }
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS=1;';

        return implode("\n", $lines);
    }

    /**
     * Pulihkan database langsung dari file backup terbaru yang tersimpan di server.
     */
    public function restoreFromLatestBackup(): void
    {
        $latest = $this->getLatestBackupInfo();
        if (! $latest['exists'] || ! $latest['path'] || ! File::exists($latest['path'])) {
            throw new \RuntimeException('Tidak ditemukan file backup terbaru di server.');
        }

        $this->restoreFromFile($latest['path'], 'zip');
    }

    /**
     * Pulihkan database dari file path (.zip atau .sql).
     */
    public function restoreFromFile(string $filePath, string $originalExtension = ''): void
    {
        $ext = strtolower($originalExtension ?: pathinfo($filePath, PATHINFO_EXTENSION));

        if ($ext === 'zip') {
            $sql = $this->extractSqlFromZip($filePath);
        } else {
            $sql = File::get($filePath);
        }

        if (empty($sql) || strlen(trim($sql)) < 10) {
            throw new \InvalidArgumentException('File backup kosong atau tidak dapat dibaca.');
        }

        // Validasi dasar isi SQL
        $upperSql = strtoupper($sql);
        if (! str_contains($upperSql, 'CREATE TABLE') && ! str_contains($upperSql, 'INSERT INTO')) {
            throw new \InvalidArgumentException('File cadangan tidak valid atau tidak berisi skrip SQL database.');
        }

        $this->restoreFromSql($sql);
    }

    /**
     * Eksekusi pemulihan data dari script SQL ke database.
     */
    public function restoreFromSql(string $sqlContent): void
    {
        $driver = DB::connection()->getDriverName();
        $pdo = DB::connection()->getPdo();

        if ($driver === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            try {
                DB::unprepared($sqlContent);
            } finally {
                DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            }
        } else {
            // Driver SQLite (pengujian / lokal)
            DB::statement('PRAGMA foreign_keys = OFF;');
            try {
                // Hapus instruksi MySQL-specific yang tidak valid di SQLite
                $cleanedSql = preg_replace('/SET\s+FOREIGN_KEY_CHECKS\s*=\s*[01]\s*;/i', '', $sqlContent);
                $pdo->exec($cleanedSql);
            } finally {
                DB::statement('PRAGMA foreign_keys = ON;');
            }
        }
    }

    /**
     * Ekstrak isi file SQL dari file ZIP.
     */
    public function extractSqlFromZip(string $zipPath): string
    {
        // 1. Coba ZipArchive jika ekstensi zip aktif
        if (class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive();
            if ($zip->open($zipPath) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $stat = $zip->statIndex($i);
                    if ($stat && str_ends_with(strtolower($stat['name']), '.sql')) {
                        $content = $zip->getFromIndex($i);
                        $zip->close();
                        if ($content !== false) {
                            return $content;
                        }
                    }
                }
                if ($zip->numFiles === 1) {
                    $content = $zip->getFromIndex(0);
                    $zip->close();
                    if ($content !== false) {
                        return $content;
                    }
                }
                $zip->close();
            }
        }

        // 2. Coba menggunakan utilitas unzip bawaan sistem jika tersedia
        if (function_exists('shell_exec')) {
            $escaped = escapeshellarg($zipPath);
            $out = @shell_exec("unzip -p {$escaped} 2>/dev/null");
            if ($out !== null && strlen(trim($out)) > 0) {
                return $out;
            }
        }

        // 3. Fallback: parser PKZIP biner sederhana pure PHP (kompatibel dengan buildSimpleZipBinary)
        $binaryContent = $this->extractSqlFromSimpleZipBinary($zipPath);
        if ($binaryContent !== null) {
            return $binaryContent;
        }

        throw new \RuntimeException('Gagal mengekstrak file SQL dari arsip ZIP. Pastikan file backup valid.');
    }

    /**
     * Ekstrak konten deflated dari ZIP sederhana murni di PHP.
     */
    protected function extractSqlFromSimpleZipBinary(string $zipPath): ?string
    {
        $raw = @file_get_contents($zipPath);
        if (! $raw || strlen($raw) < 30) {
            return null;
        }

        // Header signature PK\x03\x04
        if (substr($raw, 0, 4) !== "\x50\x4b\x03\x04") {
            return null;
        }

        $header = unpack('vversion/vflag/vmethod/vmodtime/vmoddate/Vcrc/VcompLen/VuncLen/vfnLen/vextraLen', substr($raw, 4, 26));
        if (! $header) {
            return null;
        }

        $method = $header['method'];
        $compLen = $header['compLen'];
        $fnLen = $header['fnLen'];
        $extraLen = $header['extraLen'];

        $dataOffset = 30 + $fnLen + $extraLen;
        $compressed = substr($raw, $dataOffset, $compLen);

        if ($method === 8) { // DEFLATE
            $uncompressed = @gzinflate($compressed);
            if ($uncompressed === false) {
                $uncompressed = @gzuncompress($compressed);
            }

            return $uncompressed ?: null;
        } elseif ($method === 0) { // STORE (no compression)
            return $compressed;
        }

        return null;
    }

    protected function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . ' B';
        }
        $kb = $bytes / 1024;
        if ($kb < 1024) {
            return number_format($kb, 1, ',', '.') . ' KB';
        }
        $mb = $kb / 1024;

        return number_format($mb, 2, ',', '.') . ' MB';
    }
}
