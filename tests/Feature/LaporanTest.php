<?php

namespace Tests\Feature;

use App\Models\Agenda;
use App\Models\KategoriSurat;
use App\Models\Pengaturan;
use App\Models\Santri;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'sekretaris@alfalahputra.test',
        ]);

        Pengaturan::set('nama_instansi', 'Pondok Pesantren Al-Falah Putra');
    }

    public function test_tamu_tidak_bisa_mengakses_laporan()
    {
        $response = $this->get(route('admin.laporan.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_bisa_melihat_halaman_rekap_laporan()
    {
        $response = $this->actingAs($this->user)->get(route('admin.laporan.index', ['tab' => 'surat-masuk']));
        $response->assertStatus(200);
        $response->assertSee('Rekapitulasi', false);
        $response->assertSee('Surat Masuk', false);
    }

    public function test_sekretaris_bisa_mencetak_pdf_rekap_surat_masuk()
    {
        SuratMasuk::create([
            'no_agenda' => '001/SM/2026',
            'no_surat' => '123/KEMENAG/X/2026',
            'pengirim' => 'Kementerian Agama',
            'perihal' => 'Undangan Rakor Pesantren',
            'tgl_surat' => Carbon::today()->toDateString(),
            'tgl_diterima' => Carbon::today()->toDateString(),
            'status_disposisi' => 'baru',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.laporan.cetak.surat-masuk', [
            'dari_tanggal' => Carbon::today()->subDays(5)->toDateString(),
            'sampai_tanggal' => Carbon::today()->addDays(5)->toDateString(),
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_sekretaris_bisa_mencetak_pdf_rekap_surat_keluar()
    {
        $kategori = KategoriSurat::create([
            'kode' => 'SPm',
            'nomor_urut_kategori' => 1,
            'nama' => 'Surat Permohonan',
        ]);

        SuratKeluar::create([
            'no_surat' => '001/SPm.01/AF-PA/X/2026',
            'kategori_id' => $kategori->id,
            'tujuan' => 'Bupati Muara Enim',
            'perihal' => 'Permohonan Bantuan Sarpras',
            'isi' => 'Isi surat keluar resmi.',
            'tgl_surat' => Carbon::today()->toDateString(),
            'nama_penandatangan' => 'Drs. H. Pimpinan',
            'jabatan_penandatangan' => 'Pimpinan Pondok',
            'status' => 'final',
            'metode_ttd' => 'digital',
            'status_ttd' => 'sudah',
            'kode_verifikasi' => 'AF-VERIF-12345',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.laporan.cetak.surat-keluar', [
            'dari_tanggal' => Carbon::today()->subDays(5)->toDateString(),
            'sampai_tanggal' => Carbon::today()->addDays(5)->toDateString(),
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_sekretaris_bisa_mencetak_pdf_rekap_santri()
    {
        Santri::create([
            'nis' => '2026001',
            'nama' => 'Ahmad Fulan',
            'tempat_lahir' => 'Gelumbang',
            'tanggal_lahir' => '2008-05-10',
            'alamat' => 'Jl. Mawar',
            'nama_wali' => 'Abdullah',
            'hp_wali' => '081234567890',
            'kamar' => 'Kamar 1',
            'kelas' => '1-A',
            'tahun_masuk' => 2026,
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.laporan.cetak.santri', [
            'status' => 'aktif',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_sekretaris_bisa_mencetak_pdf_rekap_agenda()
    {
        Agenda::create([
            'judul' => 'Rapat Kerja Pengurus',
            'lokasi' => 'Aula Pondok',
            'mulai' => Carbon::today(),
            'selesai' => Carbon::today()->addHours(2),
            'status' => 'rencana',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.laporan.cetak.agenda', [
            'dari_tanggal' => Carbon::today()->subDays(5)->toDateString(),
            'sampai_tanggal' => Carbon::today()->addDays(5)->toDateString(),
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
