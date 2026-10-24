<?php

namespace Tests\Feature;

use App\Models\KategoriSurat;
use App\Models\SuratMasuk;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SuratMasukTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected KategoriSurat $kategori;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->kategori = KategoriSurat::create([
            'nama' => 'Surat Undangan',
            'kode' => 'SU.01',
        ]);
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_surat_masuk(): void
    {
        $response = $this->get(route('admin.surat-masuk.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_dapat_melihat_daftar_surat_masuk(): void
    {
        SuratMasuk::create([
            'no_agenda' => '001',
            'no_surat' => '123/UND/2026',
            'tgl_surat' => '2026-10-10',
            'tgl_diterima' => '2026-10-12',
            'pengirim' => 'Kemenag Kab. Muara Enim',
            'perihal' => 'Undangan Rapat',
            'status_disposisi' => 'baru',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-masuk.index'));

        $response->assertStatus(200);
        $response->assertSee('123/UND/2026');
        $response->assertSee('Kemenag Kab. Muara Enim');
    }

    public function test_sekretaris_dapat_mencatat_surat_masuk_baru_dengan_nomor_agenda_otomatis(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('surat.pdf', 500, 'application/pdf');

        $payload = [
            'no_surat' => '456/BJB/X/2026',
            'tgl_surat' => '2026-10-11',
            'tgl_diterima' => '2026-10-12',
            'pengirim' => 'Dinas Pendidikan',
            'perihal' => 'Pemberitahuan Sosialisasi',
            'kategori_id' => $this->kategori->id,
            'status_disposisi' => 'baru',
            'file' => $file,
        ];

        $response = $this->actingAs($this->user)->post(route('admin.surat-masuk.store'), $payload);

        $this->assertDatabaseHas('surat_masuks', [
            'no_surat' => '456/BJB/X/2026',
            'no_agenda' => '001',
            'pengirim' => 'Dinas Pendidikan',
            'status_disposisi' => 'baru',
        ]);

        $surat = SuratMasuk::first();
        $response->assertRedirect(route('admin.surat-masuk.show', $surat));
        Storage::disk('public')->assertExists($surat->file);
    }

    public function test_sekretaris_dapat_memperbarui_disposisi_surat_masuk(): void
    {
        $surat = SuratMasuk::create([
            'no_agenda' => '001',
            'no_surat' => '789/TEST/2026',
            'tgl_surat' => '2026-10-10',
            'tgl_diterima' => '2026-10-12',
            'pengirim' => 'Pengurus Wilayah',
            'perihal' => 'Silaturahmi',
            'status_disposisi' => 'baru',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->patch(route('admin.surat-masuk.disposisi', $surat), [
                'disposisi' => 'Dihadiri oleh Koordinator Humas.',
                'status_disposisi' => 'diproses',
            ]);

        $response->assertRedirect(route('admin.surat-masuk.show', $surat));

        $this->assertDatabaseHas('surat_masuks', [
            'id' => $surat->id,
            'disposisi' => 'Dihadiri oleh Koordinator Humas.',
            'status_disposisi' => 'diproses',
        ]);
    }

    public function test_sekretaris_dapat_mengedit_data_surat_masuk(): void
    {
        $surat = SuratMasuk::create([
            'no_agenda' => '001',
            'no_surat' => 'OLD/001/2026',
            'tgl_surat' => '2026-10-01',
            'tgl_diterima' => '2026-10-02',
            'pengirim' => 'Pengirim Lama',
            'perihal' => 'Perihal Lama',
            'status_disposisi' => 'baru',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->put(route('admin.surat-masuk.update', $surat), [
                'no_surat' => 'NEW/001/2026',
                'tgl_surat' => '2026-10-01',
                'tgl_diterima' => '2026-10-02',
                'tempat_surat' => 'Gelumbang',
                'pengirim' => 'Pengirim Baru',
                'perihal' => 'Perihal Baru',
                'lampiran_teks' => '2 Berkas',
                'status_disposisi' => 'selesai',
            ]);

        $response->assertRedirect(route('admin.surat-masuk.show', $surat));

        $this->assertDatabaseHas('surat_masuks', [
            'id' => $surat->id,
            'no_surat' => 'NEW/001/2026',
            'pengirim' => 'Pengirim Baru',
            'tempat_surat' => 'Gelumbang',
            'lampiran_teks' => '2 Berkas',
            'status_disposisi' => 'selesai',
        ]);
    }

    public function test_sekretaris_dapat_menghapus_surat_masuk_dengan_soft_delete(): void
    {
        $surat = SuratMasuk::create([
            'no_agenda' => '001',
            'no_surat' => 'DEL/001/2026',
            'tgl_surat' => '2026-10-01',
            'tgl_diterima' => '2026-10-02',
            'pengirim' => 'Pengirim Hapus',
            'perihal' => 'Perihal Hapus',
            'status_disposisi' => 'baru',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->delete(route('admin.surat-masuk.destroy', $surat));

        $response->assertRedirect(route('admin.surat-masuk.index'));

        $this->assertSoftDeleted('surat_masuks', ['id' => $surat->id]);
    }

    public function test_sekretaris_dapat_upload_surat_masuk_beserta_lampiran_otomatis(): void
    {
        Storage::fake('public');

        $fileSuratAsli = UploadedFile::fake()->create('surat-masuk-kemenag.pdf', 300, 'application/pdf');
        $fileLampiran = UploadedFile::fake()->create('lampiran-jadwal.pdf', 200, 'application/pdf');

        $payload = [
            'no_surat' => 'B-889/Kk.06.05/PP.00/10/2026',
            'tgl_surat' => '2026-10-18',
            'tgl_diterima' => '2026-10-19',
            'tempat_surat' => 'Muara Enim',
            'pengirim' => 'Kementerian Agama Kab. Muara Enim',
            'perihal' => 'Undangan Rapat Koordinasi Pondok Pesantren',
            'lampiran_teks' => '1 Lembar',
            'status_disposisi' => 'baru',
            'file_surat_asli' => $fileSuratAsli,
            'file_lampiran' => $fileLampiran,
        ];

        $response = $this->actingAs($this->user)->post(route('admin.surat-masuk.store'), $payload);

        $this->assertDatabaseHas('surat_masuks', [
            'no_surat' => 'B-889/Kk.06.05/PP.00/10/2026',
            'pengirim' => 'Kementerian Agama Kab. Muara Enim',
            'perihal' => 'Undangan Rapat Koordinasi Pondok Pesantren',
            'lampiran_teks' => '1 Lembar',
            'tempat_surat' => 'Muara Enim',
        ]);

        $surat = SuratMasuk::where('no_surat', 'B-889/Kk.06.05/PP.00/10/2026')->firstOrFail();
        $response->assertRedirect(route('admin.surat-masuk.show', $surat));
        Storage::disk('public')->assertExists($surat->file_surat_asli);
        Storage::disk('public')->assertExists($surat->file_lampiran);
    }

    public function test_sekretaris_dapat_mengunduh_buku_induk_surat_masuk_csv(): void
    {
        SuratMasuk::create([
            'no_agenda' => '009',
            'no_surat' => '999/UND/2026',
            'tgl_surat' => '2026-10-20',
            'tgl_diterima' => '2026-10-21',
            'pengirim' => 'Dinas Pendidikan Kab. Muara Enim',
            'perihal' => 'Undangan Sosialisasi Kurikulum Pesantren',
            'status_disposisi' => 'baru',
            'kategori_id' => $this->kategori->id,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.surat-masuk.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $content = $response->streamedContent();
        $this->assertStringContainsString('Tanggal Pembuatan Surat', $content);
        $this->assertStringContainsString('Jenis Surat', $content);
        $this->assertStringContainsString('Perihal', $content);
        $this->assertStringContainsString('Nomor Surat', $content);
        $this->assertStringContainsString('Asal Surat', $content);
        $this->assertStringContainsString('999/UND/2026', $content);
        $this->assertStringContainsString('Undangan Sosialisasi Kurikulum Pesantren', $content);
        $this->assertStringContainsString('Dinas Pendidikan Kab. Muara Enim', $content);
        $this->assertStringContainsString('20-10-2026', $content);
    }

    public function test_tamu_tidak_dapat_mengunduh_buku_induk_surat_masuk_csv(): void
    {
        $response = $this->get(route('admin.surat-masuk.export'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_dapat_menginput_jenis_surat_secara_manual_pada_surat_masuk(): void
    {
        $payload = [
            'no_surat' => '100/DIKNAS/2026',
            'tgl_surat' => '2026-10-25',
            'tgl_diterima' => '2026-10-25',
            'pengirim' => 'Dinas Pendidikan',
            'perihal' => 'Pemberitahuan Bantuan Operasional',
            'jenis_surat' => 'Surat Pemberitahuan Khusus',
            'status_disposisi' => 'baru',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.surat-masuk.store'), $payload);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('kategori_surats', [
            'nama' => 'Surat Pemberitahuan Khusus',
        ]);

        $kategori = \App\Models\KategoriSurat::where('nama', 'Surat Pemberitahuan Khusus')->first();
        $this->assertNotNull($kategori);

        $this->assertDatabaseHas('surat_masuks', [
            'no_surat' => '100/DIKNAS/2026',
            'kategori_id' => $kategori->id,
            'perihal' => 'Pemberitahuan Bantuan Operasional',
        ]);
    }
}
