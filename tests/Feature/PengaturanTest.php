<?php

namespace Tests\Feature;

use App\Models\KategoriSurat;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PengaturanTest extends TestCase
{
    use RefreshDatabase;

    public function test_secretary_can_update_system_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/pengaturan', [
            'nama_situs' => 'SAP | SEKRETARIS AL-FALAH PUTRA',
            'nama_lembaga' => 'Pondok Pesantren Al Falah Putra',
            'kode_lembaga' => 'AF-PA',
            'pola_nomor_surat' => '{urut}/{jenis}/{lembaga}/{bulan}/{tahun}',
            'kop_nama' => 'PONDOK PESANTREN AL FALAH PUTRA',
            'kop_alamat' => 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171',
            'kop_kontak' => 'Telp: 0812-7890-3117',
            'telepon' => '0812-7890-3117',
            'email' => 'sekretariat@alfalahputra.sch.id',
            'alamat' => 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('success');

        $this->assertEquals('AF-PA', Pengaturan::get('kode_lembaga'));
        $this->assertEquals('{urut}/{jenis}/{lembaga}/{bulan}/{tahun}', Pengaturan::get('pola_nomor_surat'));
    }

    public function test_settings_validation_requires_mandatory_fields(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/pengaturan', [
            'nama_situs' => '',
            'nama_lembaga' => '',
            'kode_lembaga' => '',
            'pola_nomor_surat' => '',
        ]);

        $response->assertSessionHasErrors(['nama_situs', 'nama_lembaga', 'kode_lembaga', 'pola_nomor_surat']);
    }

    public function test_secretary_can_create_kategori_surat(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/kategori-surat', [
            'nama' => 'Surat Permohonan',
            'kode' => 'SPm.01',
            'keterangan' => 'Surat permohonan resmi',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kategori_surats', [
            'kode' => 'SPm.01',
            'nama' => 'Surat Permohonan',
        ]);
    }

    public function test_kategori_surat_kode_must_be_unique(): void
    {
        $user = User::factory()->create();

        KategoriSurat::create([
            'nama' => 'Surat Permohonan',
            'kode' => 'SPm.01',
        ]);

        $response = $this->actingAs($user)->post('/admin/kategori-surat', [
            'nama' => 'Surat Permohonan Lain',
            'kode' => 'SPm.01',
        ]);

        $response->assertSessionHasErrors(['kode']);
    }

    public function test_secretary_can_update_kategori_surat(): void
    {
        $user = User::factory()->create();

        $kategori = KategoriSurat::create([
            'nama' => 'Surat Permohonan',
            'kode' => 'SPm.01',
        ]);

        $response = $this->actingAs($user)->put("/admin/kategori-surat/{$kategori->id}", [
            'nama' => 'Surat Permohonan Diperbarui',
            'kode' => 'SPm.01',
            'keterangan' => 'Keterangan baru',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $this->assertDatabaseHas('kategori_surats', [
            'id' => $kategori->id,
            'nama' => 'Surat Permohonan Diperbarui',
        ]);
    }

    public function test_secretary_can_delete_kategori_surat(): void
    {
        $user = User::factory()->create();

        $kategori = KategoriSurat::create([
            'nama' => 'Surat Hapus',
            'kode' => 'SH.99',
        ]);

        $response = $this->actingAs($user)->delete("/admin/kategori-surat/{$kategori->id}");

        $response->assertRedirect('/admin/pengaturan');
        $this->assertDatabaseMissing('kategori_surats', [
            'id' => $kategori->id,
        ]);
    }

    public function test_secretary_can_update_paper_size_and_orientation_settings(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/pengaturan', [
            'nama_situs' => 'SAP | SEKRETARIS AL-FALAH PUTRA',
            'nama_lembaga' => 'Pondok Pesantren Al Falah Putra',
            'kode_lembaga' => 'AF-PA',
            'pola_nomor_surat' => '{urut}/{jenis}/{lembaga}/{bulan}/{tahun}',
            'ukuran_kertas' => 'f4',
            'orientasi_kertas' => 'portrait',
            'margin_kertas' => 'standar',
        ]);

        $response->assertRedirect('/admin/pengaturan');
        $this->assertEquals('f4', Pengaturan::get('ukuran_kertas'));
        $this->assertEquals('portrait', Pengaturan::get('orientasi_kertas'));
    }

    public function test_backup_creates_single_zip_with_ddmmyy_format_and_deletes_old_backups(): void
    {
        $user = User::factory()->create();
        $backupService = app(\App\Services\BackupService::class);

        // Buat simulasi backup lama (misal 26 Februari 2026 -> Backup260226.zip)
        $oldInfo = $backupService->createSingleLatestBackup(\Carbon\Carbon::create(2026, 2, 26));
        $this->assertEquals('Backup260226.zip', $oldInfo['filename']);
        $this->assertFileExists($oldInfo['path']);

        // Saat tombol backup baru ditekan hari ini, backup lama harus terhapus otomatis dan hanya tersisa 1 file terbaru
        $response = $this->actingAs($user)->post(route('admin.pengaturan.backup'));
        $response->assertRedirect(route('admin.pengaturan.index'));

        $expectedTodayName = 'Backup' . now()->format('dmy') . '.zip';
        $files = glob($backupService->getBackupDirectory() . '/Backup*.zip');

        $this->assertCount(1, $files);
        $this->assertEquals($expectedTodayName, basename($files[0]));

        // Uji unduh file backup terbaru
        $downloadResp = $this->actingAs($user)->get(route('admin.pengaturan.backup.download'));
        $downloadResp->assertStatus(200);
    }

    public function test_siap_publikasi_command_cleans_dummy_data_and_keeps_core_settings(): void
    {
        $this->seed(\Database\Seeders\DatabaseSeeder::class);
        $this->assertGreaterThan(0, \App\Models\Santri::count());

        $this->artisan('sap:siap-publikasi --force')
            ->assertExitCode(0);

        $this->assertEquals(0, \App\Models\Santri::withTrashed()->count());
        $this->assertEquals(0, \App\Models\Pengurus::count());
        $this->assertEquals(0, \App\Models\SuratMasuk::count());
        $this->assertEquals(0, \App\Models\SuratKeluar::count());
        $this->assertEquals(0, \App\Models\Agenda::count());
        $this->assertEquals(0, \App\Models\Arsip::count());
        $this->assertEquals(0, \App\Models\Pengumuman::count());
        $this->assertEquals(0, \App\Models\NomorSuratCounter::count());

        // Pastikan pengaturan, kategori surat, template, beranda, dan akun sekretaris tetap utuh
        $this->assertGreaterThan(0, \App\Models\User::count());
        $this->assertGreaterThan(0, \App\Models\KategoriSurat::count());
        $this->assertGreaterThan(0, \App\Models\TemplateSurat::count());
        $this->assertEquals('AF-PA', Pengaturan::get('kode_lembaga'));
    }

    public function test_guest_cannot_restore_database(): void
    {
        $response = $this->post(route('admin.pengaturan.restore'), [
            'sumber' => 'server',
        ]);

        $response->assertRedirect('/login');
    }

    public function test_secretary_can_restore_database_from_uploaded_sql(): void
    {
        $user = User::factory()->create();
        Pengaturan::set('nama_lembaga', 'Lembaga Asli');

        $sqlContent = "-- BACKUP TEST\nDROP TABLE IF EXISTS `pengaturans`;\nCREATE TABLE `pengaturans` (`id` INTEGER PRIMARY KEY AUTOINCREMENT, `key` VARCHAR(255) UNIQUE NOT NULL, `value` TEXT NULL, `created_at` DATETIME NULL, `updated_at` DATETIME NULL);\nINSERT INTO `pengaturans` (`key`, `value`) VALUES ('nama_lembaga', 'Lembaga Dipulihkan');";
        $file = UploadedFile::fake()->createWithContent('restore_data.sql', $sqlContent);

        $response = $this->actingAs($user)->post(route('admin.pengaturan.restore'), [
            'file_backup' => $file,
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));
        $response->assertSessionHas('success');
        $this->assertEquals('Lembaga Dipulihkan', Pengaturan::get('nama_lembaga'));
    }

    public function test_secretary_can_restore_database_from_uploaded_zip(): void
    {
        $user = User::factory()->create();
        $backupService = app(\App\Services\BackupService::class);

        Pengaturan::set('nama_lembaga', 'Lembaga Sebelum Backup');
        $backupInfo = $backupService->createSingleLatestBackup();

        // Ubah data setelah backup dibuat
        Pengaturan::set('nama_lembaga', 'Lembaga Setelah Dirubah');
        $this->assertEquals('Lembaga Setelah Dirubah', Pengaturan::get('nama_lembaga'));

        // Unggah file zip backup untuk dipulihkan
        $file = new UploadedFile($backupInfo['path'], $backupInfo['filename'], 'application/zip', null, true);

        $response = $this->actingAs($user)->post(route('admin.pengaturan.restore'), [
            'file_backup' => $file,
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));
        $response->assertSessionHas('success');
        $this->assertEquals('Lembaga Sebelum Backup', Pengaturan::get('nama_lembaga'));
    }

    public function test_secretary_can_restore_database_from_latest_server_backup(): void
    {
        $user = User::factory()->create();
        $backupService = app(\App\Services\BackupService::class);

        Pengaturan::set('nama_situs', 'Situs Al-Falah Sebelum Backup');
        $backupService->createSingleLatestBackup();

        // Ubah data setelah backup
        Pengaturan::set('nama_situs', 'Situs Berubah');
        $this->assertEquals('Situs Berubah', Pengaturan::get('nama_situs'));

        // Pulihkan dari backup server
        $response = $this->actingAs($user)->post(route('admin.pengaturan.restore'), [
            'sumber' => 'server',
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));
        $response->assertSessionHas('success');
        $this->assertEquals('Situs Al-Falah Sebelum Backup', Pengaturan::get('nama_situs'));
    }

    public function test_restore_fails_when_invalid_file_uploaded(): void
    {
        $user = User::factory()->create();

        $invalidFile = UploadedFile::fake()->createWithContent('bukan_sql.txt', 'Ini teks biasa tanpa SQL dump');

        $response = $this->actingAs($user)->post(route('admin.pengaturan.restore'), [
            'file_backup' => $invalidFile,
        ]);

        $response->assertRedirect(route('admin.pengaturan.index'));
        $response->assertSessionHas('error');
    }
}
