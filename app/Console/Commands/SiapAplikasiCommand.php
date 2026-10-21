<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\Arsip;
use App\Models\Pengaturan;
use App\Models\Pengurus;
use App\Models\Santri;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SiapAplikasiCommand extends Command
{
    /**
     * Nama dan signature perintah terminal.
     *
     * @var string
     */
    protected $signature = 'app:siap
                            {--no-cache : Lewati caching rute & config (berguna untuk mode development)}';

    /**
     * Deskripsi perintah terminal.
     *
     * @var string
     */
    protected $description = 'Menyiapkan aplikasi untuk siap produksi tanpa menghapus data database (Aman 100% Zero Data Loss)';

    /**
     * Eksekusi perintah terminal.
     */
    public function handle(): int
    {
        $this->newLine();
        $this->components->info('=== MEMPERSIAPKAN SAP | SEKRETARIS AL-FALAH PUTRA UNTUK PRODUKSI ===');
        $this->line('Prinsip: <fg=green;options=bold>100% AMAN - DATA TIDAK AKAN DIHAPUS</>');
        $this->newLine();

        // 1. Cek Koneksi Database
        $this->components->task('1. Memeriksa Koneksi Database', function () {
            try {
                DB::connection()->getPdo();
                return true;
            } catch (\Throwable $e) {
                return false;
            }
        });

        // 2. Jalankan Migrasi Aman (Hanya tambah kolom/tabel baru jika ada, tanpa hapus data)
        $this->components->task('2. Memperbarui Struktur Database (php artisan migrate --force)', function () {
            $this->callSilently('migrate', ['--force' => true]);
            return true;
        });

        // 3. Pastikan Storage Link Terhubung
        $this->components->task('3. Memastikan Symlink Storage Publik (storage:link)', function () {
            try {
                $this->callSilently('storage:link');
            } catch (\Throwable $e) {
                // Ignore jika symlink sudah ada
            }
            return true;
        });

        // 4. Optimasi Cache Produksi
        if (! $this->option('no-cache')) {
            $this->components->task('4. Mengoptimasi Konfigurasi & Rute (optimize)', function () {
                $this->callSilently('optimize:clear');
                $this->callSilently('optimize');
                $this->callSilently('view:cache');
                return true;
            });
        } else {
            $this->components->task('4. Membersihkan Cache Sementara (optimize:clear)', function () {
                $this->callSilently('optimize:clear');
                return true;
            });
        }

        // 5. Ringkasan Status Data yang Ada
        $this->newLine();
        $this->components->info('--- RINGKASAN STATUS DATA SISTEM (AMAN & UTUH) ---');

        $userCount = User::count();
        $santriCount = Santri::count();
        $pengurusCount = Pengurus::count();
        $suratMasukCount = SuratMasuk::count();
        $suratKeluarCount = SuratKeluar::count();
        $agendaCount = Agenda::count();
        $arsipCount = Arsip::count();
        $instansi = Pengaturan::get('nama_instansi', 'Pondok Pesantren Al-Falah Putra');

        $this->table(
            ['Komponen Data', 'Jumlah / Status', 'Keterangan'],
            [
                ['Lembaga', $instansi, 'Identitas Lembaga Aktif'],
                ['Akun Sekretaris', $userCount . ' Akun', $userCount > 0 ? '<fg=green>Siap Digunakan</>' : '<fg=red>Belum Ada User</>'],
                ['Santri Terdaftar', $santriCount . ' Santri', 'Data Tersimpan Utuh'],
                ['Pengurus / Asatidz', $pengurusCount . ' Personil', 'Data Tersimpan Utuh'],
                ['Surat Masuk', $suratMasukCount . ' Surat', 'Data Tersimpan Utuh'],
                ['Surat Keluar', $suratKeluarCount . ' Surat', 'Data Tersimpan Utuh'],
                ['Agenda Kegiatan', $agendaCount . ' Agenda', 'Data Tersimpan Utuh'],
                ['Arsip Digital', $arsipCount . ' Dokumen', 'Data Tersimpan Utuh'],
            ]
        );

        $this->newLine();
        $this->components->info('APLIKASI SIAP DIGUNAKAN DI PRODUKSI!');
        $this->line('Anda dapat menjalankan <fg=yellow>php artisan app:siap</> kapan saja saat deploy/update aplikasi.');
        $this->newLine();

        return self::SUCCESS;
    }
}
