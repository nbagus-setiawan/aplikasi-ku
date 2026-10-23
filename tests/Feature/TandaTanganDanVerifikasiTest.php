<?php

namespace Tests\Feature;

use App\Models\KategoriSurat;
use App\Models\Pengaturan;
use App\Models\SuratKeluar;
use App\Models\TandaTangan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class TandaTanganDanVerifikasiTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected KategoriSurat $kategori;
    protected SuratKeluar $suratKeluar;
    protected string $dummySignaturePng = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABkCAYAAAC9D2iBAAAgAElEQVR4Xu2dB5wdVfX/P3dur69tM9vzpvdOSCCFkEpRihQVxAqKFRsWFCuCv8WKDQsKKqJIEUQpCgqEIi2BBEghjfTelu312+7c/3Nn337fvHkvu2lZ3G/m+w8k2dy5c+4533K+53vOqQCj0Wg0Go1GozF9sHq9Ao1Go9FoNBrThkaHaTQajUaj0Wh0mEaj0Wh0mEaj0WgaHaTQajUaj0Wh0mEaj0Wh0mEaj0Wh0mEaj0WgaHaTQajUaj0Wh0';

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        Pengaturan::set('pola_nomor_surat', '{urut}/{jenis}/{lembaga}/{bulan}/{tahun}');
        Pengaturan::set('kode_lembaga', 'AF-PA');

        $this->kategori = KategoriSurat::create([
            'nama' => 'Surat Pemberitahuan',
            'kode' => 'SPb.01',
        ]);

        $this->suratKeluar = SuratKeluar::create([
            'no_surat' => '001/SPb.01/AF-PA/X/2026',
            'tgl_surat' => '2026-10-12',
            'tujuan' => 'Wali Santri Pondok Pesantren Al Falah Putra',
            'perihal' => 'Pemberitahuan Kegiatan Wisuda Santri',
            'kategori_id' => $this->kategori->id,
            'isi' => 'Diberitahukan kepada seluruh wali santri bahwa kegiatan wisuda akan dilaksanakan...',
            'nama_penandatangan' => 'KH. Syamsuni',
            'jabatan_penandatangan' => 'Pimpinan Pondok Pesantren',
            'status' => 'draft',
            'status_ttd' => 'belum',
            'kode_verifikasi' => Str::random(32),
            'user_id' => $this->user->id,
        ]);
    }

    public function test_sekretaris_dapat_menandatangani_surat_secara_digital_langsung(): void
    {
        Storage::fake('public');

        $response = $this->actingAs($this->user)
            ->post(route('admin.surat-keluar.ttd-digital', $this->suratKeluar), [
                'data_ttd' => $this->dummySignaturePng,
            ]);

        $response->assertRedirect(route('admin.surat-keluar.show', $this->suratKeluar));

        $this->suratKeluar->refresh();
        $this->assertEquals('final', $this->suratKeluar->status);
        $this->assertEquals('digital', $this->suratKeluar->metode_ttd);
        $this->assertEquals('sudah', $this->suratKeluar->status_ttd);
        $this->assertNotNull($this->suratKeluar->file_final);
        Storage::disk('public')->assertExists($this->suratKeluar->file_final);

        $this->assertDatabaseHas('tanda_tangans', [
            'surat_keluar_id' => $this->suratKeluar->id,
            'nama_penandatangan' => 'KH. Syamsuni',
        ]);
    }

    public function test_sekretaris_dapat_membuat_tautan_sekali_pakai_30_menit(): void
    {
        $response = $this->actingAs($this->user)
            ->post(route('admin.surat-keluar.tautan-ttd', $this->suratKeluar));

        $response->assertRedirect(route('admin.surat-keluar.show', $this->suratKeluar));
        $response->assertSessionHas('tautan_ttd');

        $tandaTangan = TandaTangan::where('surat_keluar_id', $this->suratKeluar->id)->first();
        $this->assertNotNull($tandaTangan);
        $this->assertNotNull($tandaTangan->token_sekali_pakai);
        $this->assertFalse($tandaTangan->isExpired());
    }

    public function test_halaman_tanda_tangan_publik_dapat_diakses_dengan_token_valid(): void
    {
        $tandaTangan = TandaTangan::create([
            'surat_keluar_id' => $this->suratKeluar->id,
            'nama_penandatangan' => $this->suratKeluar->nama_penandatangan,
            'jabatan_penandatangan' => $this->suratKeluar->jabatan_penandatangan,
            'token_sekali_pakai' => 'TOKEN1234567890',
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $this->suratKeluar->update([
            'isi' => '<p class="x12ofw9d"><!--comp--><!--for-->Sehubungan dengan peningkatan mutu...</p>',
        ]);

        $response = $this->get(route('publik.tanda-tangan.show', 'TOKEN1234567890'));

        $response->assertStatus(200);
        $response->assertSee('Konfirmasi Penandatanganan Dokumen');
        $response->assertSee('KH. Syamsuni');
        $response->assertSee($this->suratKeluar->no_surat);
        $response->assertSee('Sehubungan dengan peningkatan mutu');
        $response->assertDontSee('&lt;p');
        $response->assertDontSee('<!--comp-->');
    }

    public function test_penandatangan_dapat_menandatangani_lewat_tautan_publik(): void
    {
        Storage::fake('public');

        $tandaTangan = TandaTangan::create([
            'surat_keluar_id' => $this->suratKeluar->id,
            'nama_penandatangan' => $this->suratKeluar->nama_penandatangan,
            'jabatan_penandatangan' => $this->suratKeluar->jabatan_penandatangan,
            'token_sekali_pakai' => 'TOKENPUBLIK999',
            'expires_at' => Carbon::now()->addMinutes(30),
        ]);

        $response = $this->post(route('publik.tanda-tangan.sign', 'TOKENPUBLIK999'), [
            'data_ttd' => $this->dummySignaturePng,
        ]);

        $response->assertStatus(200);
        $response->assertSee('Dokumen Berhasil Ditandatangani');

        $this->suratKeluar->refresh();
        $this->assertEquals('final', $this->suratKeluar->status);
        $this->assertEquals('sudah', $this->suratKeluar->status_ttd);

        // Token harus hangus
        $tandaTangan->refresh();
        $this->assertNull($tandaTangan->token_sekali_pakai);
    }

    public function test_tautan_tanda_tangan_kedaluwarsa_setelah_30_menit_ditolak(): void
    {
        $tandaTangan = TandaTangan::create([
            'surat_keluar_id' => $this->suratKeluar->id,
            'nama_penandatangan' => $this->suratKeluar->nama_penandatangan,
            'jabatan_penandatangan' => $this->suratKeluar->jabatan_penandatangan,
            'token_sekali_pakai' => 'TOKEN_EXPIRED',
            'expires_at' => Carbon::now()->subMinute(), // Sudah lewat 30 menit
        ]);

        $responseShow = $this->get(route('publik.tanda-tangan.show', 'TOKEN_EXPIRED'));
        $responseShow->assertStatus(200);
        $responseShow->assertSee('Tautan Tidak Berlaku');

        $responsePost = $this->post(route('publik.tanda-tangan.sign', 'TOKEN_EXPIRED'), [
            'data_ttd' => $this->dummySignaturePng,
        ]);
        $responsePost->assertStatus(200);
        $responsePost->assertSee('Tautan Tidak Berlaku');

        $this->suratKeluar->refresh();
        $this->assertEquals('belum', $this->suratKeluar->status_ttd);
    }

    public function test_sekretaris_dapat_finalisasi_tanda_tangan_basah_dengan_unggahan_file(): void
    {
        Storage::fake('public');

        $fileScan = UploadedFile::fake()->create('scan-ttd.pdf', 800, 'application/pdf');

        $response = $this->actingAs($this->user)
            ->post(route('admin.surat-keluar.ttd-basah', $this->suratKeluar), [
                'file_final' => $fileScan,
            ]);

        $response->assertRedirect(route('admin.surat-keluar.show', $this->suratKeluar));

        $this->suratKeluar->refresh();
        $this->assertEquals('final', $this->suratKeluar->status);
        $this->assertEquals('basah', $this->suratKeluar->metode_ttd);
        $this->assertEquals('sudah', $this->suratKeluar->status_ttd);
        $this->assertNotNull($this->suratKeluar->file_final);
        Storage::disk('public')->assertExists($this->suratKeluar->file_final);
    }

    public function test_halaman_verifikasi_publik_menampilkan_keabsahan_surat(): void
    {
        $this->suratKeluar->update([
            'status' => 'final',
            'status_ttd' => 'sudah',
            'metode_ttd' => 'digital',
        ]);

        $response = $this->get(route('publik.verifikasi', $this->suratKeluar->kode_verifikasi));

        $response->assertStatus(200);
        $response->assertSee('DOKUMEN RESMI TERVERIFIKASI');
        $response->assertSee($this->suratKeluar->no_surat);
        $response->assertSee('KH. Syamsuni');
    }

    public function test_halaman_verifikasi_publik_menolak_kode_tidak_valid(): void
    {
        $response = $this->get(route('publik.verifikasi', 'KODE_PALSU_TIDAK_ADA'));

        $response->assertStatus(200);
        $response->assertSee('Dokumen Tidak Ditemukan');
    }

    public function test_publik_dapat_mengunduh_dokumen_resmi_terverifikasi_format_word_dan_pdf(): void
    {
        $this->suratKeluar->update([
            'status' => 'final',
            'status_ttd' => 'sudah',
            'metode_ttd' => 'digital',
        ]);

        // Unduh format Word (.docx)
        $respWord = $this->get(route('publik.verifikasi.unduh', [$this->suratKeluar->kode_verifikasi, 'format' => 'docx']));
        $respWord->assertStatus(200);
        $respWord->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        $this->assertStringContainsString('attachment', (string) $respWord->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.docx', (string) $respWord->headers->get('Content-Disposition'));

        // Unduh format PDF
        $respPdf = $this->get(route('publik.verifikasi.unduh', [$this->suratKeluar->kode_verifikasi, 'format' => 'pdf']));
        $respPdf->assertStatus(200);
        $respPdf->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringContainsString('attachment', (string) $respPdf->headers->get('Content-Disposition'));
        $this->assertStringContainsString('.pdf', (string) $respPdf->headers->get('Content-Disposition'));
    }
}
