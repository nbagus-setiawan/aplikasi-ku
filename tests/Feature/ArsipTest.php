<?php

namespace Tests\Feature;

use App\Models\Arsip;
use App\Models\KategoriSurat;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArsipTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_arsip(): void
    {
        $response = $this->get(route('admin.arsip.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_dapat_melihat_daftar_dan_mencari_arsip(): void
    {
        Storage::fake('public');
        $filePath = 'arsip/sk-contoh.pdf';
        Storage::disk('public')->put($filePath, 'konten file dummy');

        Arsip::create([
            'judul' => 'Surat Keputusan Pengangkatan Guru',
            'kategori' => 'SK & Legalitas',
            'tahun' => 2026,
            'file' => $filePath,
            'keterangan' => 'SK Pengangkatan Dewan Asatidz',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.arsip.index', ['q' => 'Pengangkatan']));

        $response->assertStatus(200);
        $response->assertSee('Surat Keputusan Pengangkatan Guru');
        $response->assertSee('SK & Legalitas');
    }

    public function test_sekretaris_dapat_mengunggah_dokumen_arsip_baru(): void
    {
        Storage::fake('public');

        $dummyFile = UploadedFile::fake()->create('dokumen-legalitas.pdf', 1024, 'application/pdf');

        $payload = [
            'judul' => 'Akta Notaris Pendirian Yayasan',
            'kategori' => 'SK & Legalitas',
            'tahun' => 2026,
            'file' => $dummyFile,
            'keterangan' => 'Akta notaris asli tersimpan di brankas utama',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.arsip.store'), $payload);

        $this->assertDatabaseHas('arsips', [
            'judul' => 'Akta Notaris Pendirian Yayasan',
            'kategori' => 'SK & Legalitas',
            'tahun' => 2026,
        ]);

        $arsip = Arsip::first();
        $response->assertRedirect(route('admin.arsip.show', $arsip));
        Storage::disk('public')->assertExists($arsip->file);
    }

    public function test_sekretaris_dapat_menautkan_arsip_ke_surat_masuk_dan_surat_keluar(): void
    {
        Storage::fake('public');
        $dummyFile = UploadedFile::fake()->create('berkas-tautan.pdf', 500, 'application/pdf');

        $kategoriSurat = KategoriSurat::create([
            'nama' => 'Surat Permohonan',
            'kode' => 'SPm.01',
        ]);

        $suratMasuk = SuratMasuk::create([
            'no_agenda' => '001',
            'no_surat' => 'SM/001/2026',
            'tgl_surat' => '2026-10-10',
            'tgl_diterima' => '2026-10-11',
            'pengirim' => 'Kemenag',
            'perihal' => 'Undangan Rakor',
            'status_disposisi' => 'baru',
            'user_id' => $this->user->id,
        ]);

        $suratKeluar = SuratKeluar::create([
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Dinas Pendidikan',
            'perihal' => 'Pengajuan Bantuan',
            'kategori_id' => $kategoriSurat->id,
            'isi' => 'Isi surat permohonan bantuan...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $payload = [
            'judul' => 'Lampiran Berkas Pendukung Bantuan',
            'kategori' => 'Laporan & SPJ',
            'tahun' => 2026,
            'file' => $dummyFile,
            'surat_masuk_id' => $suratMasuk->id,
            'surat_keluar_id' => $suratKeluar->id,
        ];

        $response = $this->actingAs($this->user)->post(route('admin.arsip.store'), $payload);

        $this->assertDatabaseHas('arsips', [
            'judul' => 'Lampiran Berkas Pendukung Bantuan',
            'surat_masuk_id' => $suratMasuk->id,
            'surat_keluar_id' => $suratKeluar->id,
        ]);

        $arsip = Arsip::first();
        $this->assertEquals($suratMasuk->id, $arsip->suratMasuk->id);
        $this->assertEquals($suratKeluar->id, $arsip->suratKeluar->id);
    }

    public function test_sekretaris_dapat_mengunduh_berkas_arsip(): void
    {
        Storage::fake('public');
        $fileName = 'arsip/sertifikat-tanah.pdf';
        Storage::disk('public')->put($fileName, 'PDF dummy content sertifikat tanah wakaf');

        $arsip = Arsip::create([
            'judul' => 'Sertifikat Tanah Wakaf Pesantren',
            'kategori' => 'SK & Legalitas',
            'tahun' => 2026,
            'file' => $fileName,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.arsip.unduh', $arsip));

        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition');
    }

    public function test_sekretaris_dapat_mengubah_data_arsip(): void
    {
        Storage::fake('public');
        $fileName = 'arsip/file-lama.pdf';
        Storage::disk('public')->put($fileName, 'konten file lama');

        $arsip = Arsip::create([
            'judul' => 'Judul Lama',
            'kategori' => 'Kategori Lama',
            'tahun' => 2025,
            'file' => $fileName,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.arsip.update', $arsip), [
            'judul' => 'Judul Baru Diperbarui',
            'kategori' => 'Kategori Baru',
            'tahun' => 2026,
            'keterangan' => 'Keterangan diperbarui',
        ]);

        $response->assertRedirect(route('admin.arsip.show', $arsip));

        $this->assertDatabaseHas('arsips', [
            'id' => $arsip->id,
            'judul' => 'Judul Baru Diperbarui',
            'tahun' => 2026,
        ]);
    }

    public function test_sekretaris_dapat_menghapus_arsip_dengan_soft_delete(): void
    {
        Storage::fake('public');
        $fileName = 'arsip/file-hapus.pdf';
        Storage::disk('public')->put($fileName, 'konten file');

        $arsip = Arsip::create([
            'judul' => 'Arsip Akan Dihapus',
            'kategori' => 'Dokumentasi',
            'tahun' => 2026,
            'file' => $fileName,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.arsip.destroy', $arsip));

        $response->assertRedirect(route('admin.arsip.index'));
        $this->assertSoftDeleted('arsips', ['id' => $arsip->id]);
    }
}
