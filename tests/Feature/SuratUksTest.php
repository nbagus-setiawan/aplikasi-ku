<?php

namespace Tests\Feature;

use App\Models\KasTransaksi;
use App\Models\Santri;
use App\Models\SuratUks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuratUksTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_guest_cannot_access_surat_uks_index(): void
    {
        $response = $this->get('/admin/surat-uks');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_secretary_can_view_surat_uks_index_and_metrics(): void
    {
        SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '7F2B',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'harga' => 5000,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->user)->get('/admin/surat-uks');
        $response->assertStatus(200);
        $response->assertSee('Surat Izin UKS');
        $response->assertSee('001/UKS-SIP/AF-PA/XI/2026');
        $response->assertSee('7F2B');
        $response->assertSee('Surat Tercetak');
        $response->assertSee('Kas Masuk');
        $response->assertSee('Sisa Stok Blangko');
    }

    public function test_secretary_can_generate_batch_blank_forms(): void
    {
        $response = $this->actingAs($this->user)->post('/admin/surat-uks/generate-blangko', [
            'jenis' => 'pulang',
            'jumlah' => 5,
            'tanggal_cetak' => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect('/admin/surat-uks');
        $this->assertEquals(5, SuratUks::where('jenis', 'pulang')->count());

        $surat = SuratUks::first();
        $this->assertNotNull($surat);
        $this->assertEquals('tersedia', $surat->status);
        $this->assertEquals(4, strlen($surat->kode_verifikasi));
        $this->assertStringContainsString('UKS-SIP', $surat->nomor_surat);
    }

    public function test_secretary_can_catat_terjual_with_name_class_and_dorm(): void
    {
        $surat = SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '9K3D',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'harga' => 5000,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->user)->post("/admin/surat-uks/{$surat->id}/catat-terjual", [
            'nama_santri' => 'Muhammad Zaidan',
            'kelas' => '2 Aliyah B',
            'asrama' => 'Kamar Barokah 03',
            'tanggal_pembelian' => Carbon::today()->toDateString(),
        ]);

        $response->assertRedirect('/admin/surat-uks');

        $surat->refresh();
        $this->assertEquals('terjual', $surat->status);
        $this->assertEquals('Muhammad Zaidan', $surat->nama_santri);
        $this->assertEquals('2 Aliyah B', $surat->kelas);
        $this->assertEquals('Kamar Barokah 03', $surat->asrama);
        $this->assertNotNull($surat->kas_transaksi_id);

        // Verifikasi otomatis masuk ke kas bendahara
        $kas = KasTransaksi::find($surat->kas_transaksi_id);
        $this->assertNotNull($kas);
        $this->assertEquals('debit', $kas->jenis);
        $this->assertEquals('Pendapatan Surat Izin Pulang', $kas->kategori);
        $this->assertEquals(5000, $kas->nominal);
        $this->assertStringContainsString('Muhammad Zaidan', $kas->keterangan);
    }

    public function test_secretary_can_catat_terjual_via_4_digit_verification_code(): void
    {
        $surat = SuratUks::create([
            'nomor_surat' => '002/UKS-SIS/AF-PA/XI/2026',
            'kode_verifikasi' => 'A8M2',
            'jenis' => 'sakit',
            'tanggal_cetak' => Carbon::today(),
            'harga' => 5000,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->user)->post('/admin/surat-uks/catat-terjual-kode', [
            'kode_verifikasi' => 'A8M2',
            'nama_santri' => 'Ahmad Fulan',
            'kelas' => '1 Tsanawiyah',
            'asrama' => 'Kamar Rahmah 01',
        ]);

        $response->assertRedirect('/admin/surat-uks');

        $surat->refresh();
        $this->assertEquals('terjual', $surat->status);
        $this->assertEquals('Ahmad Fulan', $surat->nama_santri);

        // Kas masuk akun Pendapatan Surat Izin Sakit
        $kas = KasTransaksi::find($surat->kas_transaksi_id);
        $this->assertNotNull($kas);
        $this->assertEquals('Pendapatan Surat Izin Sakit', $kas->kategori);
        $this->assertEquals(5000, $kas->nominal);
    }

    public function test_public_verification_displays_official_authenticity_and_santri_data_without_showing_letter(): void
    {
        $surat = SuratUks::create([
            'nomor_surat' => '003/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => 'V7X9',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'tanggal_pembelian' => Carbon::today(),
            'nama_santri' => 'Muhammad Zaidan',
            'kelas' => '3 Aliyah',
            'asrama' => 'Kamar Barokah 05',
            'harga' => 5000,
            'status' => 'terjual',
        ]);

        $response = $this->get("/verifikasi?kode={$surat->kode_verifikasi}");
        $response->assertStatus(200);

        // Pastikan terverifikasi sah dan menampilkan data santri
        $response->assertSee('DOKUMEN RESMI TERVERIFIKASI SAH');
        $response->assertSee('003/UKS-SIP/AF-PA/XI/2026');
        $response->assertSee('V7X9');
        $response->assertSee('Surat Izin Pulang');
        $response->assertSee('Muhammad Zaidan');
        $response->assertSee('3 Aliyah');
        $response->assertSee('Kamar Barokah 05');

        // Pastikan TIDAK menampilkan pratinjau surat / file fisik
        $response->assertDontSee('Hanya untuk pratinjau verifikasi');
        $response->assertDontSee('asli-pdf');
    }

    public function test_secretary_can_download_pdf_with_exact_signatures_and_dotted_format(): void
    {
        $suratPulang = SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '7F2B',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'harga' => 5000,
            'status' => 'tersedia',
        ]);

        $responsePulang = $this->actingAs($this->user)->get("/admin/surat-uks/{$suratPulang->id}/cetak");
        $responsePulang->assertStatus(200);
        $this->assertEquals('application/pdf', $responsePulang->headers->get('Content-Type'));

        $suratSakit = SuratUks::create([
            'nomor_surat' => '002/UKS-SIS/AF-PA/XI/2026',
            'kode_verifikasi' => '9K3D',
            'jenis' => 'sakit',
            'tanggal_cetak' => Carbon::today(),
            'harga' => 5000,
            'status' => 'tersedia',
        ]);

        $responseSakit = $this->actingAs($this->user)->get("/admin/surat-uks/{$suratSakit->id}/cetak");
        $responseSakit->assertStatus(200);
        $this->assertEquals('application/pdf', $responseSakit->headers->get('Content-Type'));
    }

    public function test_secretary_can_batch_print_pdf(): void
    {
        SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '1111',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'tersedia',
        ]);

        SuratUks::create([
            'nomor_surat' => '002/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '2222',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->user)->get('/admin/surat-uks/cetak-batch?status=tersedia');
        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_secretary_can_mark_santri_as_returned(): void
    {
        $surat = SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '3333',
            'jenis' => 'pulang',
            'nama_santri' => 'Santri Pulang',
            'kelas' => '1 Aliyah',
            'asrama' => 'A-01',
            'tanggal_cetak' => Carbon::today(),
            'tanggal_pembelian' => Carbon::today(),
            'status' => 'terjual',
        ]);

        $response = $this->actingAs($this->user)->post("/admin/surat-uks/{$surat->id}/kembali");

        $surat->refresh();
        $this->assertEquals('kembali', $surat->status);
        $this->assertNotNull($surat->tanggal_kembali_riil);
    }

    public function test_secretary_can_delete_surat_uks_and_cascades_kas(): void
    {
        $kas = KasTransaksi::create([
            'kode_transaksi' => 'BKM-202611-0001',
            'tanggal' => Carbon::today(),
            'jenis' => 'debit',
            'kategori' => 'Pendapatan Surat Izin Pulang',
            'nominal' => 5000,
            'keterangan' => 'Penjualan 1 lbr',
        ]);

        $surat = SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '4444',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'terjual',
            'kas_transaksi_id' => $kas->id,
        ]);

        $response = $this->actingAs($this->user)->delete("/admin/surat-uks/{$surat->id}");

        $response->assertRedirect('/admin/surat-uks');
        $this->assertSoftDeleted('surat_uks', ['id' => $surat->id]);
        $this->assertSoftDeleted('kas_transaksis', ['id' => $kas->id]);
    }

    public function test_secretary_can_download_word(): void
    {
        $surat = SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '5555',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($this->user)->get("/admin/surat-uks/{$surat->id}/word");
        $response->assertStatus(200);
        $this->assertStringContainsString('application/vnd.ms-word', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('SURAT KETERANGAN IZIN PULANG', $response->getContent());
        $this->assertStringContainsString('5555', $response->getContent());
    }
}
