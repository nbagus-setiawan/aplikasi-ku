<?php

namespace Tests\Feature;

use App\Models\KategoriSurat;
use App\Models\Pengaturan;
use App\Models\SuratKeluar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SuratKeluarTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected KategoriSurat $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        Pengaturan::set('pola_nomor_surat', '{urut}/{jenis}/{lembaga}/{bulan}/{tahun}');
        Pengaturan::set('kode_lembaga', 'AF-PA');

        $this->kategori = KategoriSurat::create([
            'nama' => 'Surat Permohonan',
            'kode' => 'SPm.01',
        ]);
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_surat_keluar(): void
    {
        $response = $this->get(route('admin.surat-keluar.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_dapat_melihat_daftar_surat_keluar(): void
    {
        SuratKeluar::create([
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Kemenag',
            'perihal' => 'Permohonan Bantuan',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi surat...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'status_ttd' => 'belum',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.index'));

        $response->assertStatus(200);
        $response->assertSee('001/SPm.01/AF-PA/X/2026');
        $response->assertSee('Permohonan Bantuan');
    }

    public function test_sekretaris_dapat_membuat_surat_keluar_dengan_nomor_otomatis(): void
    {
        $payload = [
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Kantor Wilayah Kemenag Sumatera Selatan',
            'perihal' => 'Permohonan Narasumber Bahtsul Masail',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Dengan hormat, sehubungan dengan agenda Bahtsul Masail...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan Pesantren',
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), $payload);

        $this->assertDatabaseHas('surat_keluars', [
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'perihal' => 'Permohonan Narasumber Bahtsul Masail',
            'status' => 'draft',
        ]);

        $surat = SuratKeluar::first();
        $this->assertNotNull($surat->kode_verifikasi);
        $response->assertRedirect(route('admin.surat-keluar.show', $surat));
    }

    public function test_sekretaris_dapat_mencetak_pdf_surat_keluar(): void
    {
        $surat = SuratKeluar::create([
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Kemenag',
            'perihal' => 'Permohonan Bantuan',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi surat...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'status_ttd' => 'belum',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.cetak', $surat));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_surat_berstatus_final_terkunci_dan_tidak_dapat_diubah(): void
    {
        $surat = SuratKeluar::create([
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Kemenag',
            'perihal' => 'Permohonan Bantuan',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi asli...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'final', // Final terkunci
            'status_ttd' => 'sudah',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        // Coba akses form edit
        $responseEdit = $this->actingAs($this->user)->get(route('admin.surat-keluar.edit', $surat));
        $responseEdit->assertRedirect(route('admin.surat-keluar.show', $surat));
        $responseEdit->assertSessionHas('error');

        // Coba kirim update via PUT
        $responseUpdate = $this->actingAs($this->user)->put(route('admin.surat-keluar.update', $surat), [
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Kemenag Ubah',
            'perihal' => 'Perihal Ubah',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi diubah...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
        ]);

        $responseUpdate->assertRedirect(route('admin.surat-keluar.show', $surat));
        $this->assertDatabaseHas('surat_keluars', [
            'id' => $surat->id,
            'isi' => 'Isi asli...', // Tidak berubah
        ]);
    }

    public function test_sekretaris_dapat_menghapus_surat_keluar_dengan_soft_delete(): void
    {
        $surat = SuratKeluar::create([
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Kemenag',
            'perihal' => 'Permohonan Bantuan',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'status_ttd' => 'belum',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.surat-keluar.destroy', $surat));

        $response->assertRedirect(route('admin.surat-keluar.index'));
        $this->assertSoftDeleted('surat_keluars', ['id' => $surat->id]);
    }

    public function test_sekretaris_dapat_membuat_surat_keluar_dengan_ketik_manual_kode_jenis_surat(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), [
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Kantor Wilayah Kemenag',
            'perihal' => 'Undangan Wisuda Santri',
            'kode_jenis_surat' => 'SU.01',
            'isi' => '<p>Dengan hormat, kami mengundang bapak/ibu...</p>',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan Pondok Pesantren',
            'kode_verifikasi' => 'AF123456',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('surat_keluars', [
            'kode_verifikasi' => 'AF123456',
            'tujuan' => 'Kantor Wilayah Kemenag',
        ]);

        $surat = SuratKeluar::where('kode_verifikasi', 'AF123456')->first();
        $this->assertNotNull($surat);
        $this->assertStringContainsString('SU.01', $surat->no_surat);
    }

    public function test_sekretaris_dapat_membuat_surat_keluar_dengan_kertas_f4_dan_tiga_penandatangan(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), [
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Masyarakat Luas',
            'perihal' => 'Pemberitahuan Kelulusan',
            'kode_jenis_surat' => 'SPb.02',
            'ukuran_kertas' => 'f4',
            'margin' => 'ringkas',
            'jumlah_penandatangan' => 3,
            'nama_penandatangan' => 'Ust. Fauzi, S.Pd.I',
            'jabatan_penandatangan' => 'Sekretaris Panitia',
            'penandatangan_2_nama' => 'Ust. Zulkifli, M.Ag',
            'penandatangan_2_jabatan' => 'Ketua Panitia',
            'penandatangan_3_nama' => 'KH. Syamsuni',
            'penandatangan_3_jabatan' => 'Pimpinan Pondok Pesantren',
            'isi' => '<p>Diumumkan kepada seluruh santri...</p>',
            'kode_verifikasi' => 'F4KERTAS',
        ]);

        $response->assertSessionHasNoErrors();
        $surat = SuratKeluar::where('kode_verifikasi', 'F4KERTAS')->first();
        $this->assertNotNull($surat);
        $this->assertEquals('f4', $surat->ukuran_kertas);
        $this->assertEquals(3, $surat->jumlah_penandatangan);
        $this->assertCount(3, $surat->penandatangans);
    }

    public function test_sekretaris_dapat_menandatangani_langsung_pada_kanvas_form_surat(): void
    {
        $dummySig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABkCAYAAAC9D2iBAAAgAElEQVR4Xu2dB5wdVfX/P3dur69tM9vzpvdOSCCFkEpRihQVxAqKFRsWFCuCv8WKDQsKKqJIEUQpCgqEIi2BBEghjfTelu312+7c/3Nn337fvHkvu2lZ3G/m+w8k2dy5c+4533K+53vOqQCj0Wg0Go1GozF9sHq9Ao1Go9FoNBrThkaHaTQajUaj0Wh0mEaj0Wh0mEaj0WgaHaTQajUaj0Wh0mEaj0Wh0mEaj0Wh0mEaj0WgaHaTQajUaj0Wh0';

        $response = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), [
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Dinas Pendidikan',
            'perihal' => 'Surat Rekomendasi',
            'kode_jenis_surat' => 'SRk.01',
            'isi' => '<p>Rekomendasi beasiswa santri...</p>',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan Pondok',
            'data_ttd_langsung' => $dummySig,
            'kode_verifikasi' => 'TTDASLI1',
        ]);

        $response->assertSessionHasNoErrors();
        $surat = SuratKeluar::where('kode_verifikasi', 'TTDASLI1')->first();
        $this->assertNotNull($surat);
        $this->assertEquals('digital', $surat->metode_ttd);
        $this->assertEquals('sudah', $surat->status_ttd);

        $this->assertDatabaseHas('tanda_tangans', [
            'surat_keluar_id' => $surat->id,
            'urutan' => 1,
            'nama_penandatangan' => 'KH. Syamsuni',
        ]);
    }

    public function test_sekretaris_dapat_melihat_halaman_edit_surat_keluar(): void
    {
        $surat = SuratKeluar::create([
            'no_surat' => '002/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Kemenag Kab. Muara Enim',
            'perihal' => 'Validasi Data',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi surat...',
            'nama_penandatangan' => 'H. M. Arsyad',
            'jabatan_penandatangan' => 'Sekretaris',
            'status' => 'draft',
            'status_ttd' => 'belum',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.edit', $surat));

        $response->assertStatus(200);
        $response->assertSee('Ubah Surat Keluar');
        $response->assertSee('002/SPm.01/AF-PA/X/2026');
    }

    public function test_sekretaris_dapat_membuat_surat_dengan_tempat_lampiran_dan_aksi_siap_kirim_serta_ttd_multi_penandatangan(): void
    {
        $dummySig = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABkCAYAAAC9D2iBAAAgAElEQVR4Xu2dB5wdVfX/P3dur69tM9vzpvdOSCCFkEpRihQVxAqKFRsWFCuCv8WKDQsKKqJIEUQpCgqEIi2BBEghjfTelu312+7c/3Nn337fvHkvu2lZ3G/m+w8k2dy5c+4533K+53vOqQCj0Wg0Go1GozF9sHq9Ao1Go9FoNBrThkaHaTQajUaj0Wh0mEaj0Wh0mEaj0WgaHaTQajUaj0Wh0mEaj0Wh0mEaj0Wh0mEaj0WgaHaTQajUaj0Wh0';

        $response = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), [
            'tgl_surat' => '2026-10-18',
            'tempat_surat' => 'Putak',
            'lampiran_teks' => '2 (Dua) Lembar',
            'tujuan' => 'Wali Santri',
            'perihal' => 'Undangan Haflah',
            'kode_jenis_surat' => 'SU.02',
            'jumlah_penandatangan' => 2,
            'nama_penandatangan' => 'Ust. Ahmad',
            'jabatan_penandatangan' => 'Ketua Panitia',
            'penandatangan_2_nama' => 'K.H. Mursyidi',
            'penandatangan_2_jabatan' => 'Pengasuh Pondok',
            'metode_ttd_pilihan' => 'digital',
            'data_ttd_langsung' => $dummySig,
            'data_ttd_2' => $dummySig,
            'aksi_simpan' => 'siap_kirim',
            'isi' => '<p>Undangan resmi menghadiri Haflah...</p>',
            'kode_verifikasi' => 'SIAP1234',
        ]);

        $response->assertSessionHasNoErrors();
        $surat = SuratKeluar::where('kode_verifikasi', 'SIAP1234')->first();
        $this->assertNotNull($surat);
        $this->assertEquals('Putak', $surat->tempat_surat);
        $this->assertEquals('2 (Dua) Lembar', $surat->lampiran_teks);
        $this->assertEquals('terkirim', $surat->status);
        $this->assertEquals('sudah', $surat->status_ttd);
        $this->assertDatabaseHas('tanda_tangans', [
            'surat_keluar_id' => $surat->id,
            'urutan' => 2,
            'nama_penandatangan' => 'K.H. Mursyidi',
        ]);
    }

    public function test_deteksi_otomatis_dari_upload_file_dan_verifikasi_menampilkan_file(): void
    {
        $naskah = "Nomor : .../SPm.01/AF-PA/X/2026\n"
            . "Lampiran : 2 Lembar\n"
            . "Perihal : Permohonan Izin Kegiatan Santri\n"
            . "Putak, 19 Oktober 2026\n"
            . "Kepada Yth. Kepala Kementerian Agama Kabupaten Muara Enim\n"
            . "di - Tempat\n"
            . "Assalamu'alaikum Warahmatullahi Wabarakatuh.";

        $uploadedFile = \Illuminate\Http\UploadedFile::fake()->createWithContent('surat_permohonan.txt', $naskah);

        $parseResp = $this->actingAs($this->user)->postJson(route('admin.surat-keluar.parse-docx'), [
            'file_surat' => $uploadedFile,
        ]);

        $parseResp->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.is_nomor_lengkap', false)
            ->assertJsonPath('data.lampiran_teks', '2 Lembar')
            ->assertJsonPath('data.ada_lampiran', true)
            ->assertJsonPath('data.perihal', 'Permohonan Izin Kegiatan Santri')
            ->assertJsonPath('data.tujuan', 'Kepala Kementerian Agama Kabupaten Muara Enim')
            ->assertJsonPath('data.tempat_surat', 'Putak')
            ->assertJsonPath('data.tgl_surat', '2026-10-19');

        // Sekarang uji store dari hasil upload
        $fileForStore = \Illuminate\Http\UploadedFile::fake()->createWithContent('surat_permohonan.txt', $naskah);
        $lampiranFile = \Illuminate\Http\UploadedFile::fake()->create('lampiran_2_lembar.pdf', 100, 'application/pdf');

        $storeResp = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), [
            'file_surat_asli' => $fileForStore,
            'file' => $lampiranFile,
            'no_surat_manual' => '005/SPm.01/AF-PA/X/2026',
            'kode_verifikasi' => 'WORDQR99',
        ]);

        $storeResp->assertRedirect(route('admin.surat-keluar.index'));
        $this->assertDatabaseHas('surat_keluars', [
            'no_surat' => '005/SPm.01/AF-PA/X/2026',
            'perihal' => 'Permohonan Izin Kegiatan Santri',
            'lampiran_teks' => '2 Lembar',
            'tujuan' => 'Kepala Kementerian Agama Kabupaten Muara Enim',
            'kode_verifikasi' => 'WORDQR99',
        ]);

        // Pastikan fungsi pembuat QR PNG berjalan murni dan menghasilkan file PNG valid
        $parser = app(\App\Services\DocxSuratParserService::class);
        $tmpQr = sys_get_temp_dir() . '/test_qr_' . uniqid() . '.png';
        $this->assertTrue($parser->generateQrPngFile(url('/verifikasi/WORDQR99'), $tmpQr));
        $this->assertFileExists($tmpQr);
        $this->assertGreaterThan(100, filesize($tmpQr));
        @unlink($tmpQr);

        // Pastikan saat QR di-scan / kode diketik di halaman verifikasi, file asli beserta QR ditampilkan
        $verifResp = $this->get(route('publik.verifikasi', 'WORDQR99'));
        $verifResp->assertStatus(200);
        $verifResp->assertSee('005/SPm.01/AF-PA/X/2026');
        $verifResp->assertSee('2 Lembar');
        $verifResp->assertSee('WORDQR99');
        $verifResp->assertSee('Dokumen Surat Asli Resmi (Ber-QR Code)');
    }

    public function test_sekretaris_dapat_mengunduh_surat_keluar_format_docx_ber_qr(): void
    {
        $surat = SuratKeluar::create([
            'no_surat' => '006/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-22',
            'tujuan' => 'Pengurus Ponpes',
            'perihal' => 'Undangan Rapat Evaluasi',
            'kategori_id' => $this->kategori->id,
            'isi' => '<p>Sehubungan dengan evaluasi triwulan...</p>',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'status_ttd' => 'belum',
            'kode_verifikasi' => 'DOCXTEST123',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.cetak', [$surat, 'format' => 'docx']));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.docx', (string) $response->headers->get('Content-Disposition'));

        $binary = $response->getContent();
        $this->assertNotEmpty($binary);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_docx_');
        file_put_contents($tempFile, $binary);

        /** @var \App\Services\DocxSuratParserService $docxService */
        $docxService = app(\App\Services\DocxSuratParserService::class);
        $reflection = new \ReflectionClass($docxService);
        $method = $reflection->getMethod('readZipEntries');
        $method->setAccessible(true);
        $entries = $method->invoke($docxService, $tempFile);

        @unlink($tempFile);

        $this->assertIsArray($entries);
        $this->assertArrayHasKey('word/document.xml', $entries);
        $this->assertStringContainsString('Kode Verifikasi: DOCXTEST123', $entries['word/document.xml']);
        $this->assertStringContainsString('w:sz w:val="22"', $entries['word/document.xml']);
        $this->assertStringContainsString('w:jc w:val="end"', $entries['word/document.xml']);
    }

    public function test_sekretaris_dapat_mengunduh_surat_keluar_format_pdf_dengan_qr_dan_kode_verifikasi(): void
    {
        $surat = SuratKeluar::create([
            'no_surat' => '007/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-22',
            'tujuan' => 'Dinas Pendidikan',
            'perihal' => 'Pemberitahuan Kegiatan',
            'kategori_id' => $this->kategori->id,
            'isi' => '<p>Isi surat pemberitahuan...</p>',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'status_ttd' => 'belum',
            'kode_verifikasi' => 'PDFTEST456',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.cetak', [$surat, 'format' => 'pdf', 'download' => 1]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment', (string) $response->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.pdf', (string) $response->headers->get('Content-Disposition'));
    }

    public function test_halaman_show_surat_keluar_menampilkan_pratinjau_surat_dan_modal_unduh(): void
    {
        $surat = SuratKeluar::create([
            'no_surat' => '008/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-22',
            'tujuan' => 'Kemenag',
            'perihal' => 'Permohonan Bantuan Buku',
            'kategori_id' => $this->kategori->id,
            'isi' => '<p>Isi permohonan bantuan buku...</p>',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'status_ttd' => 'belum',
            'kode_verifikasi' => 'SHOWTEST789',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.show', $surat));

        $response->assertStatus(200);
        $response->assertSee('Pratinjau Lembar Surat Resmi');
        $response->assertSee('Unduh PDF');
        $response->assertSee('Unduh Word (.docx)');
        $response->assertSee('Yakin Ingin Mengunduh?');
        $response->assertSee('SHOWTEST789');
        $response->assertDontSee('window.open(');
    }

    public function test_halaman_show_surat_keluar_menampilkan_pratinjau_docx_asli_tanpa_iframe_html(): void
    {
        $surat = SuratKeluar::create([
            'no_surat' => '009/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-22',
            'tujuan' => 'Kemenag',
            'perihal' => 'Naskah Asli Word',
            'kategori_id' => $this->kategori->id,
            'isi' => '<p>Isi surat...</p>',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'file_surat_asli' => 'surat-keluar/word-qr-1-test.docx',
            'status' => 'final',
            'status_ttd' => 'sudah',
            'kode_verifikasi' => 'WORDASLI99',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.show', $surat));

        $response->assertStatus(200);
        $response->assertSee('docx-render-target');
        $response->assertSee('docx-loading');
        $response->assertSee('Cetak / Simpan PDF');
        $response->assertSee('Unduh Word (.docx)');
        $response->assertDontSee('srcdoc=');
    }

    public function test_unduhan_word_surat_keluar_asli_memuat_qr_dan_kode_verifikasi_setelah_proses(): void
    {
        /** @var \App\Services\DocxSuratParserService $docxService */
        $docxService = app(\App\Services\DocxSuratParserService::class);
        $reflection = new \ReflectionClass($docxService);
        $writeMethod = $reflection->getMethod('writeZipEntries');
        $writeMethod->setAccessible(true);
        $readMethod = $reflection->getMethod('readZipEntries');
        $readMethod->setAccessible(true);

        $initialDocXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
            '<w:body><w:p><w:r><w:t>Naskah Asli Dari Word</w:t></w:r></w:p><w:sectPr/></w:body></w:document>';

        $initialRels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"/>';

        $initialTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '</Types>';

        \Illuminate\Support\Facades\Storage::disk('public')->makeDirectory('surat-keluar-word');
        $rawWordRel = 'surat-keluar-word/test-asli-' . Str::random(6) . '.docx';
        $absRaw = storage_path('app/public/' . $rawWordRel);

        $writeMethod->invoke($docxService, $absRaw, [
            '[Content_Types].xml' => $initialTypes,
            'word/document.xml' => $initialDocXml,
            'word/_rels/document.xml.rels' => $initialRels,
        ]);

        $this->assertFileExists($absRaw);

        $surat = SuratKeluar::create([
            'no_surat' => '010/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-22',
            'tujuan' => 'Kemenag',
            'perihal' => 'Naskah Asli Test',
            'kategori_id' => $this->kategori->id,
            'isi' => '<p>Isi surat...</p>',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'file_surat_asli' => $rawWordRel,
            'status' => 'final',
            'status_ttd' => 'sudah',
            'kode_verifikasi' => 'INJECTQR88',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.cetak', [$surat, 'format' => 'docx', 'download' => 1]));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $surat->refresh();
        $this->assertStringContainsString('word-qr-', $surat->file_surat_asli);

        $binary = $response->getContent();
        $this->assertNotEmpty($binary);

        $tempDownloaded = tempnam(sys_get_temp_dir(), 'test_dl_');
        file_put_contents($tempDownloaded, $binary);

        $entries = $readMethod->invoke($docxService, $tempDownloaded);
        @unlink($tempDownloaded);
        @unlink($absRaw);

        $this->assertIsArray($entries);
        $this->assertArrayHasKey('word/document.xml', $entries);

        $this->assertStringContainsString('Kode Verifikasi: INJECTQR88', $entries['word/document.xml']);
        $this->assertStringContainsString('w:sz w:val="22"', $entries['word/document.xml']);
        $this->assertStringContainsString('w:jc w:val="end"', $entries['word/document.xml']);
        $this->assertStringContainsString('Naskah Asli Dari Word', $entries['word/document.xml']);
    }

    public function test_sekretaris_dapat_mengunduh_buku_induk_surat_keluar_csv(): void
    {
        SuratKeluar::create([
            'no_surat' => '005/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Kemenag Wilayah',
            'perihal' => 'Permohonan Dana Bantuan Operasional',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi surat...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'final',
            'status_ttd' => 'sudah',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Tanggal Pembuatan Surat', $content);
        $this->assertStringContainsString('Jenis Surat', $content);
        $this->assertStringContainsString('Perihal', $content);
        $this->assertStringContainsString('Nomor Surat', $content);
        $this->assertStringContainsString('Tujuan Surat', $content);
        $this->assertStringContainsString('005/SPm.01/AF-PA/X/2026', $content);
        $this->assertStringContainsString('Permohonan Dana Bantuan Operasional', $content);
        $this->assertStringContainsString('Kemenag Wilayah', $content);
        $this->assertStringContainsString('25-10-2026', $content);
    }

    public function test_tamu_tidak_dapat_mengunduh_buku_induk_surat_keluar_csv(): void
    {
        $response = $this->get(route('admin.surat-keluar.export'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_dapat_menginput_jenis_surat_secara_manual_pada_surat_keluar(): void
    {
        $payload = [
            'no_surat_manual' => '099/SKM.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Pimpinan Lembaga',
            'perihal' => 'Surat Keterangan Mengajar Asatidz',
            'jenis_surat' => 'Surat Keterangan Mengajar',
            'isi' => '<p>Menerangkan bahwa...</p>',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.surat-keluar.store'), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kategori_surats', [
            'nama' => 'Surat Keterangan Mengajar',
        ]);

        $kategori = \App\Models\KategoriSurat::where('nama', 'Surat Keterangan Mengajar')->first();
        $this->assertNotNull($kategori);

        $this->assertDatabaseHas('surat_keluars', [
            'no_surat' => '099/SKM.01/AF-PA/X/2026',
            'kategori_id' => $kategori->id,
            'perihal' => 'Surat Keterangan Mengajar Asatidz',
        ]);
    }

    public function test_filter_kategori_surat_keluar_hanya_menampilkan_kategori_yang_ada_di_indeks(): void
    {
        $katAktif = KategoriSurat::create([
            'nama' => 'Kategori Khusus Aktif',
            'kode' => 'KKA.01',
        ]);

        $katKosong = KategoriSurat::create([
            'nama' => 'Kategori Tanpa Surat',
            'kode' => 'KTS.02',
        ]);

        $katTerhapus = KategoriSurat::create([
            'nama' => 'Kategori Surat Terhapus',
            'kode' => 'KST.03',
        ]);

        // Surat aktif
        SuratKeluar::create([
            'no_surat' => '101/KKA.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Kemenag',
            'perihal' => 'Perihal Surat Aktif',
            'kategori_id' => $katAktif->id,
            'isi' => 'Isi',
            'nama_penandatangan' => 'Pimpinan',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        // Surat yang sudah terhapus (soft delete)
        $suratTerhapus = SuratKeluar::create([
            'no_surat' => '102/KST.03/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Kemenag',
            'perihal' => 'Perihal Surat Dihapus',
            'kategori_id' => $katTerhapus->id,
            'isi' => 'Isi',
            'nama_penandatangan' => 'Pimpinan',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);
        $suratTerhapus->delete();

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.index'));

        $response->assertStatus(200);
        $response->assertSee('Kategori Khusus Aktif');
        $response->assertDontSee('Kategori Tanpa Surat');
        $response->assertDontSee('Kategori Surat Terhapus');
    }

    public function test_unduh_surat_menggunakan_format_penamaan_nomor_surat_strip_jenis_surat_sesuai_kapitalisasi(): void
    {
        $kategoriCustom = KategoriSurat::create([
            'nama' => 'SURAT TUGAS PENGABDIAN ASATIDZ',
            'kode' => 'STPA.01',
        ]);

        $surat = SuratKeluar::create([
            'no_surat' => '055/STPA.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Pengasuh Pesantren',
            'perihal' => 'Tugas Pengabdian',
            'kategori_id' => $kategoriCustom->id,
            'isi' => 'Isi Surat Tugas',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'draft',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.cetak', [
            $surat,
            'unduh' => 1,
        ]));

        $response->assertStatus(200);
        $contentDisposition = $response->headers->get('Content-Disposition');
        $this->assertNotNull($contentDisposition);
        $this->assertStringContainsString('055-STPA.01-AF-PA-X-2026 - SURAT TUGAS PENGABDIAN ASATIDZ.pdf', $contentDisposition);
    }

    public function test_indeks_mengurutkan_surat_berdasarkan_nomor_urut_terbesar_ke_terkecil(): void
    {
        SuratKeluar::create([
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Tujuan 1',
            'perihal' => 'Perihal Surat Urut Satu',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi',
            'nama_penandatangan' => 'Pimpinan',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'final',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        SuratKeluar::create([
            'no_surat' => '002/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Tujuan 2',
            'perihal' => 'Perihal Surat Urut Dua',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi',
            'nama_penandatangan' => 'Pimpinan',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'final',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.index'));
        $response->assertStatus(200);

        $content = $response->getContent();
        $pos002 = strpos($content, '002/SPm.01/AF-PA/X/2026');
        $pos001 = strpos($content, '001/SPm.01/AF-PA/X/2026');

        $this->assertNotFalse($pos002);
        $this->assertNotFalse($pos001);
        $this->assertTrue($pos002 < $pos001, 'Nomor 002 harus tampil sebelum nomor 001 di indeks');
    }

    public function test_unduh_csv_mengurutkan_surat_berdasarkan_nomor_urut_terkecil_ke_terbesar(): void
    {
        SuratKeluar::create([
            'no_surat' => '002/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Tujuan 2',
            'perihal' => 'Perihal Surat Urut Dua',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi',
            'nama_penandatangan' => 'Pimpinan',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'final',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        SuratKeluar::create([
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-25',
            'tujuan' => 'Tujuan 1',
            'perihal' => 'Perihal Surat Urut Satu',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Isi',
            'nama_penandatangan' => 'Pimpinan',
            'jabatan_penandatangan' => 'Pimpinan',
            'status' => 'final',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-keluar.export'));
        $response->assertStatus(200);

        $content = $response->streamedContent();
        $pos001 = strpos($content, '001/SPm.01/AF-PA/X/2026');
        $pos002 = strpos($content, '002/SPm.01/AF-PA/X/2026');

        $this->assertNotFalse($pos001);
        $this->assertNotFalse($pos002);
        $this->assertTrue($pos001 < $pos002, 'Nomor 001 harus muncul sebelum nomor 002 di unduh CSV');
    }
}


