<?php

namespace Tests\Feature;

use App\Models\Arsip;
use App\Models\DokumenLampiran;
use App\Models\KategoriSurat;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DokumenLampiranTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        Storage::fake('public');
    }

    public function test_surat_masuk_bisa_upload_multi_file_lampiran(): void
    {
        $kategori = KategoriSurat::create(['kode' => 'SM.01', 'nama' => 'Surat Masuk Dinas']);

        $file1 = UploadedFile::fake()->create('lampiran1.pdf', 100, 'application/pdf');
        $file2 = UploadedFile::fake()->create('lampiran2.docx', 50, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->user)->post(route('admin.surat-masuk.store'), [
            'no_surat' => '001/SM/2026',
            'tgl_surat' => '2026-10-10',
            'tgl_diterima' => '2026-10-12',
            'pengirim' => 'Kemenag',
            'perihal' => 'Undangan Penting',
            'kategori_id' => $kategori->id,
            'status_disposisi' => 'baru',
            'lampiran_berkas' => [$file1, $file2],
        ]);

        $response->assertSessionHasNoErrors();
        $suratMasuk = SuratMasuk::latest('id')->first();
        $this->assertNotNull($suratMasuk);
        $this->assertCount(2, $suratMasuk->lampiranBerkas);
        $this->assertEquals('lampiran1.pdf', $suratMasuk->lampiranBerkas[0]->nama_berkas);
        $this->assertEquals('lampiran2.docx', $suratMasuk->lampiranBerkas[1]->nama_berkas);
    }

    public function test_surat_keluar_bisa_upload_multi_file_lampiran(): void
    {
        $kategori = KategoriSurat::create(['kode' => 'SPm.01', 'nama' => 'Surat Permohonan']);

        $file1 = UploadedFile::fake()->create('lampiran_sk1.pdf', 80, 'application/pdf');
        $file2 = UploadedFile::fake()->image('lampiran_sk2.jpg');

        $response = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), [
            'kategori_id' => $kategori->id,
            'tgl_surat' => '2026-10-15',
            'tujuan' => 'Bupati',
            'perihal' => 'Permohonan Bantuan',
            'isi' => '<p>Isi surat keluar</p>',
            'nama_penandatangan' => 'Ketua Yayasan',
            'jabatan_penandatangan' => 'Pimpinan',
            'lampiran_berkas' => [$file1, $file2],
        ]);

        $response->assertSessionHasNoErrors();
        $suratKeluar = SuratKeluar::latest('id')->first();
        $this->assertNotNull($suratKeluar);
        $this->assertCount(2, $suratKeluar->lampiranBerkas);
        $this->assertTrue($suratKeluar->lampiranBerkas[0]->isPdf());
        $this->assertTrue($suratKeluar->lampiranBerkas[1]->isImage());
    }

    public function test_arsip_bisa_upload_multi_file_lampiran(): void
    {
        $file1 = UploadedFile::fake()->create('dokumen_arsip1.pdf', 120, 'application/pdf');
        $file2 = UploadedFile::fake()->create('dokumen_arsip2.pdf', 150, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('admin.arsip.store'), [
            'judul' => 'Sertifikat Tanah Wakaf',
            'kategori' => 'SK & Legalitas',
            'tahun' => 2026,
            'lampiran_berkas' => [$file1, $file2],
        ]);

        $response->assertSessionHasNoErrors();
        $arsip = Arsip::latest('id')->first();
        $this->assertNotNull($arsip);
        $this->assertNotEmpty($arsip->file);
        $this->assertCount(2, $arsip->lampiranBerkas);
    }

    public function test_tambah_lampiran_langsung_pada_halaman_detail_via_ajax(): void
    {
        $arsip = Arsip::create([
            'judul' => 'Arsip Izin Operasional',
            'kategori' => 'SK & Legalitas',
            'tahun' => 2026,
            'file' => 'arsip/dummy.pdf',
            'user_id' => $this->user->id,
        ]);

        $fileUpload = UploadedFile::fake()->create('lampiran_tambahan.pdf', 90, 'application/pdf');

        $response = $this->actingAs($this->user)->postJson(
            route('admin.dokumen-lampiran.store', ['type' => 'arsip', 'id' => $arsip->id]),
            [
                'lampiran_berkas' => [$fileUpload],
                'keterangan' => 'Tambahan Berita Acara',
            ]
        );

        $response->assertOk();
        $response->assertJson(['success' => true]);

        $arsip->refresh();
        $this->assertCount(1, $arsip->lampiranBerkas);
        $this->assertEquals('lampiran_tambahan.pdf', $arsip->lampiranBerkas->first()->nama_berkas);
        $this->assertEquals('Tambahan Berita Acara', $arsip->lampiranBerkas->first()->keterangan);
    }

    public function test_hapus_lampiran_satuan_via_ajax(): void
    {
        $arsip = Arsip::create([
            'judul' => 'Arsip Berkas',
            'kategori' => 'Dokumentasi',
            'tahun' => 2026,
            'file' => 'arsip/dummy.pdf',
            'user_id' => $this->user->id,
        ]);

        Storage::disk('public')->put('arsip-lampiran/test.pdf', 'dummy content');

        $lampiran = $arsip->lampiranBerkas()->create([
            'nama_berkas' => 'test.pdf',
            'path_berkas' => 'arsip-lampiran/test.pdf',
            'mime_type' => 'application/pdf',
            'ukuran_byte' => 1024,
        ]);

        $response = $this->actingAs($this->user)->deleteJson(route('admin.dokumen-lampiran.destroy', $lampiran));

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('dokumen_lampirans', ['id' => $lampiran->id]);
        Storage::disk('public')->assertMissing('arsip-lampiran/test.pdf');
    }

    public function test_unduh_semua_lampiran_dalam_format_zip(): void
    {
        $arsip = Arsip::create([
            'judul' => 'Arsip Laporan',
            'kategori' => 'Laporan & SPJ',
            'tahun' => 2026,
            'file' => 'arsip/utama.pdf',
            'user_id' => $this->user->id,
        ]);

        Storage::disk('public')->put('arsip/utama.pdf', 'konten utama');
        Storage::disk('public')->put('arsip-lampiran/lamp1.pdf', 'konten lampiran 1');

        $arsip->lampiranBerkas()->create([
            'nama_berkas' => 'lamp1.pdf',
            'path_berkas' => 'arsip-lampiran/lamp1.pdf',
            'mime_type' => 'application/pdf',
            'ukuran_byte' => 500,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.dokumen-lampiran.unduh-semua', ['type' => 'arsip', 'id' => $arsip->id]));

        $response->assertOk();
        $this->assertStringContainsString('application/zip', $response->headers->get('content-type') ?: 'application/zip');
    }

    public function test_surat_masuk_bisa_upload_multi_file_langsung_dari_kotak_utama_tahap_1(): void
    {
        $kategori = KategoriSurat::create(['kode' => 'SM.02', 'nama' => 'Pemberitahuan']);

        $mainDoc = UploadedFile::fake()->create('surat_utama.pdf', 150, 'application/pdf');
        $att1 = UploadedFile::fake()->create('lampiran_tahap1_a.pdf', 80, 'application/pdf');
        $att2 = UploadedFile::fake()->image('lampiran_tahap1_b.png');

        $response = $this->actingAs($this->user)->post(route('admin.surat-masuk.store'), [
            'no_surat' => '123/SM/2026',
            'tgl_surat' => '2026-10-10',
            'tgl_diterima' => '2026-10-12',
            'pengirim' => 'Kemenag Wilayah',
            'perihal' => 'Undangan Rapat Akbar',
            'kategori_id' => $kategori->id,
            'status_disposisi' => 'baru',
            'file_surat_asli' => [$mainDoc, $att1, $att2],
        ]);

        $response->assertSessionHasNoErrors();
        $suratMasuk = SuratMasuk::latest('id')->first();
        $this->assertNotNull($suratMasuk);
        $this->assertNotNull($suratMasuk->file_surat_asli);
        $this->assertCount(2, $suratMasuk->lampiranBerkas);
        $this->assertEquals('lampiran_tahap1_a.pdf', $suratMasuk->lampiranBerkas[0]->nama_berkas);
        $this->assertEquals('lampiran_tahap1_b.png', $suratMasuk->lampiranBerkas[1]->nama_berkas);
    }

    public function test_surat_keluar_bisa_upload_multi_file_langsung_dari_kotak_utama_tahap_1(): void
    {
        $kategori = KategoriSurat::create(['kode' => 'SK.02', 'nama' => 'Pemberitahuan Keluar']);

        $mainDoc = UploadedFile::fake()->create('surat_keluar_utama.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $att1 = UploadedFile::fake()->create('lampiran_sk_a.pdf', 70, 'application/pdf');
        $att2 = UploadedFile::fake()->create('lampiran_sk_b.pdf', 90, 'application/pdf');

        $response = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), [
            'kategori_id' => $kategori->id,
            'tgl_surat' => '2026-10-15',
            'tujuan' => 'Seluruh Santri',
            'perihal' => 'Pemberitahuan Libur',
            'isi' => '<p>Libur Maulid</p>',
            'nama_penandatangan' => 'Sekretaris',
            'jabatan_penandatangan' => 'Sekretariat',
            'file_surat_asli' => [$mainDoc, $att1, $att2],
        ]);

        $response->assertSessionHasNoErrors();
        $suratKeluar = SuratKeluar::latest('id')->first();
        $this->assertNotNull($suratKeluar);
        $this->assertNotNull($suratKeluar->file_surat_asli);
        $this->assertCount(2, $suratKeluar->lampiranBerkas);
        $this->assertEquals('lampiran_sk_a.pdf', $suratKeluar->lampiranBerkas[0]->nama_berkas);
        $this->assertEquals('lampiran_sk_b.pdf', $suratKeluar->lampiranBerkas[1]->nama_berkas);
    }
}
