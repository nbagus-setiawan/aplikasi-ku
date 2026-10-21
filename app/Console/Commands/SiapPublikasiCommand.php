<?php

namespace App\Console\Commands;

use App\Models\Agenda;
use App\Models\Arsip;
use App\Models\NomorSuratCounter;
use App\Models\Pengumuman;
use App\Models\Pengurus;
use App\Models\Santri;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\TandaTangan;
use App\Models\User;
use Database\Seeders\ProduksiSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SiapPublikasiCommand extends Command
{
    /**
     * Nama dan signature perintah terminal.
     *
     * @var string
     */
    protected $signature = 'sap:siap-publikasi
                            {--force : Jalankan langsung tanpa konfirmasi interaktif}
                            {--email= : Atur email akun Sekretaris baru (opsional)}
                            {--password= : Atur password akun Sekretaris baru (opsional)}
                            {--name= : Atur nama akun Sekretaris baru (opsional)}';

    /**
     * Deskripsi perintah terminal.
     *
     * @var string
     */
    protected $description = 'Bersihkan seluruh data contoh (dummy) agar aplikasi siap dipublikasikan dengan data asli, tanpa menghapus pengaturan & template surat.';

    /**
     * Eksekusi perintah terminal.
     */
    public function handle(): int
    {
        if (! $this->option('force')) {
            $confirmed = $this->confirm(
                'Perintah ini akan MENGHAPUS seluruh data contoh (Santri, Pengurus, Surat Masuk/Keluar, Agenda, Arsip, Pengumuman) dan mereset urutan nomor surat ke 001. Pengaturan Pesantren, Kategori Surat, Template Surat, Beranda, dan Akun Sekretaris TETAP AMAN. Lanjutkan?',
                true
            );

            if (! $confirmed) {
                $this->warn('Proses dibatalkan.');
                return self::SUCCESS;
            }
        }

        $this->info('Membersihkan seluruh data contoh (dummy)...');

        Schema::disableForeignKeyConstraints();

        try {
            DB::table('tanda_tangans')->delete();
            DB::table('arsips')->delete();
            DB::table('surat_keluars')->delete();
            DB::table('surat_masuks')->delete();
            DB::table('nomor_surat_counters')->delete();
            DB::table('agendas')->delete();
            DB::table('pengumumans')->delete();
            DB::table('penguruses')->delete();
            DB::table('santris')->delete();
        } finally {
            Schema::enableForeignKeyConstraints();
        }

        // Pastikan data inti (Pengaturan Pesantren, Kategori Surat, Template Surat, Beranda, Akun Sekretaris) tetap lengkap
        $this->call('db:seed', [
            '--class' => ProduksiSeeder::class,
            '--force' => true,
        ]);

        // Jika pengguna memberikan opsi --email atau --password, perbarui akun Sekretaris secara langsung
        $customEmail = $this->option('email');
        $customPassword = $this->option('password');
        $customName = $this->option('name');

        if ($customEmail || $customPassword || $customName) {
            $user = User::query()->first();
            if ($user) {
                if ($customName) {
                    $user->name = $customName;
                }
                if ($customEmail) {
                    $user->email = $customEmail;
                }
                if ($customPassword) {
                    $user->password = Hash::make($customPassword);
                }
                $user->save();
            } else {
                User::create([
                    'name' => $customName ?: env('SEKRETARIS_NAME', 'Sekretaris Al-Falah Putra'),
                    'email' => $customEmail ?: env('SEKRETARIS_EMAIL', 'sekretaris@alfalahputak.sch.id'),
                    'password' => Hash::make($customPassword ?: env('SEKRETARIS_PASSWORD', 'password')),
                    'email_verified_at' => now(),
                ]);
            }
            $this->info('Akun Sekretaris berhasil diperbarui.');
        }

        $this->newLine();
        $this->components->info('Aplikasi SAP | SEKRETARIS AL-FALAH PUTRA kini BERSIH dari data contoh dan SIAP DIPUBLIKASIKAN!');
        $this->line('  - Santri, Pengurus, Surat Masuk/Keluar, Agenda, Arsip, Pengumuman : <fg=green>0 data contoh (Bersih)</>');
        $this->line('  - Urutan Nomor Surat Keluar                                        : <fg=green>Reset kembali ke 001</>');
        $this->line('  - Identitas Pesantren, Kategori & Template Surat, Desain Beranda  : <fg=green>Tetap Aman & Siap Pakai</>');
        $this->line('  - Kredensial Akun Sekretaris                                       : <fg=green>Aman di .env (Tidak masuk GitHub)</>');

        return self::SUCCESS;
    }
}
