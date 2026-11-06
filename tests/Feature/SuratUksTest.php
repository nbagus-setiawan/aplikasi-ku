<?php

namespace Tests\Feature;

use App\Models\KasTransaksi;
use App\Models\Santri;
use App\Models\SuratUks;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        $response->assertSee('7F2B');
        $response->assertSee('Surat Terdaftar');
        $response->assertSee('Kas Masuk');
        $response->assertSee('Sisa Stok Tersedia');
        $response->assertViewHas('santris');
        $response->assertViewHas('santriJson');
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
        $this->assertEquals($surat->kode_verifikasi, $surat->nomor_surat);
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

        // Verifikasi otomatis masuk ke kas bendahara secara terakumulasi per bulan
        $kas = KasTransaksi::find($surat->kas_transaksi_id);
        $this->assertNotNull($kas);
        $this->assertEquals('debit', $kas->jenis);
        $this->assertEquals('Pendapatan SIP', $kas->kategori);
        $this->assertEquals(5000, $kas->nominal);
        $this->assertStringContainsString('1 lbr x Rp 5.000', $kas->keterangan);
    }

    public function test_cash_is_consolidated_per_month_in_bendahara_with_count_times_five_thousand(): void
    {
        $surat1 = SuratUks::create([
            'nomor_surat' => '010/UKS-SIP/AF-PA/X/2026',
            'kode_verifikasi' => 'OCT1',
            'jenis' => 'pulang',
            'tanggal_cetak' => '2026-10-01',
            'status' => 'tersedia',
        ]);

        $surat2 = SuratUks::create([
            'nomor_surat' => '011/UKS-SIP/AF-PA/X/2026',
            'kode_verifikasi' => 'OCT2',
            'jenis' => 'pulang',
            'tanggal_cetak' => '2026-10-02',
            'status' => 'tersedia',
        ]);

        // Jual surat 1 di bulan Oktober
        $this->actingAs($this->user)->post("/admin/surat-uks/{$surat1->id}/catat-terjual", [
            'nama_santri' => 'Santri Satu',
            'kelas' => '1 Aliyah',
            'asrama' => 'A-01',
            'tanggal_pembelian' => '2026-10-10',
        ]);

        // Jual surat 2 di bulan Oktober
        $this->actingAs($this->user)->post("/admin/surat-uks/{$surat2->id}/catat-terjual", [
            'nama_santri' => 'Santri Dua',
            'kelas' => '2 Aliyah',
            'asrama' => 'A-02',
            'tanggal_pembelian' => '2026-10-15',
        ]);

        // Pastikan hanya ada 1 baris transaksi kas di bulan Oktober untuk Pendapatan SIP
        $transaksis = KasTransaksi::where('kategori', 'Pendapatan SIP')
            ->whereYear('tanggal', 2026)
            ->whereMonth('tanggal', 10)
            ->get();

        $this->assertCount(1, $transaksis);
        $kas = $transaksis->first();

        // Nominal terakumulasi: 2 lembar x Rp 5.000 = Rp 10.000
        $this->assertEquals(10000, $kas->nominal);
        $this->assertStringContainsString('2 lbr x Rp 5.000', $kas->keterangan);

        // Jika 1 surat dihapus, nominal otomatis menyusut menjadi 1 lbr x Rp 5.000 = Rp 5.000
        $this->actingAs($this->user)->delete("/admin/surat-uks/{$surat2->id}");
        $kas->refresh();
        $this->assertEquals(5000, $kas->nominal);
        $this->assertStringContainsString('1 lbr x Rp 5.000', $kas->keterangan);
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

        // Kas masuk akun Pendapatan SIS
        $kas = KasTransaksi::find($surat->kas_transaksi_id);
        $this->assertNotNull($kas);
        $this->assertEquals('Pendapatan SIS', $kas->kategori);
        $this->assertEquals(5000, $kas->nominal);
        $this->assertStringContainsString('1 lbr x Rp 5.000', $kas->keterangan);
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
        $response->assertSee('Kode Surat');
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
            'kategori' => 'Pendapatan SIP',
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

    public function test_deleted_surat_uks_number_can_be_reused_immediately(): void
    {
        // 1. Buat surat 001
        $surat1 = SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '1234',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::create(2026, 11, 1),
            'status' => 'tersedia',
        ]);

        // 2. Buat surat 002
        $surat2 = SuratUks::create([
            'nomor_surat' => '002/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '5678',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::create(2026, 11, 1),
            'status' => 'tersedia',
        ]);

        // 3. Hapus surat 001
        $this->actingAs($this->user)->delete("/admin/surat-uks/{$surat1->id}");

        // 4. Generate nomor baru berikutnya, harus mengulang mengambil 001
        $nomorBerikutnya = app(\App\Services\NomorSuratService::class)->generateNomorSuratUks('pulang', Carbon::create(2026, 11, 1));
        $this->assertEquals('001/UKS-SIP/AF-PA/XI/2026', $nomorBerikutnya);

        // 5. Bisa simpan surat baru bernomor 001 tanpa error duplicate
        $suratBaru = SuratUks::create([
            'nomor_surat' => $nomorBerikutnya,
            'kode_verifikasi' => '9999',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::create(2026, 11, 1),
            'status' => 'tersedia',
        ]);
        $this->assertDatabaseHas('surat_uks', [
            'id' => $suratBaru->id,
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
        ]);
    }

    public function test_pdf_template_has_new_kop_and_exact_signatures_without_satpam(): void
    {
        $suratPulang = SuratUks::create([
            'nomor_surat' => '001/UKS-SIP/AF-PA/XI/2026',
            'kode_verifikasi' => '7F2B',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'tersedia',
        ]);

        $htmlPulang = view('surat-uks.pdf', [
            'suratList' => collect([$suratPulang]),
            'pengaturans' => \App\Models\Pengaturan::getAllKeyValues(),
            'isHtmlPrint' => true,
        ])->render();

        $this->assertStringContainsString('YAYASAN PONDOK PESANTREN AL-FALAH', $htmlPulang);
        $this->assertStringContainsString('PONDOK PUTRA', $htmlPulang);
        $this->assertStringContainsString('UNIT KESEHATAN SANTRI PUTRA', $htmlPulang);
        $this->assertStringContainsString('https://ponpesalfalahputak.my.id/verifikasi', $htmlPulang);
        $this->assertStringContainsString('KH. MURSYIDI', $htmlPulang);
        $this->assertStringNotContainsString('LEMBAR SANTRI / SATPAM', $htmlPulang);
        $this->assertStringNotContainsString('Pos Keamanan / Satpam', $htmlPulang);
        $this->assertStringNotContainsString('Orang Tua / Wali Santri', $htmlPulang);
        $this->assertStringNotContainsString('Ketentuan: Surat ini wajib diserahkan kepada Pos Keamanan', $htmlPulang);

        $suratSakit = SuratUks::create([
            'nomor_surat' => '002/UKS-SIS/AF-PA/XI/2026',
            'kode_verifikasi' => '9K3D',
            'jenis' => 'sakit',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'tersedia',
        ]);

        $htmlSakit = view('surat-uks.pdf', [
            'suratList' => collect([$suratSakit]),
            'pengaturans' => \App\Models\Pengaturan::getAllKeyValues(),
            'isHtmlPrint' => true,
        ])->render();

        $this->assertStringContainsString('Sekretaris Pondok Putra', $htmlSakit);
        $this->assertStringContainsString('Pengurus Kesehatan Putra', $htmlSakit);
        $this->assertStringNotContainsString('Pos Keamanan / Satpam', $htmlSakit);
    }

    public function test_secretary_can_upload_multi_files_for_surat_uks(): void
    {
        Storage::fake('public');

        $filePdf = UploadedFile::fake()->create('surat1.pdf', 200, 'application/pdf');
        $fileJpg = UploadedFile::fake()->image('surat2.jpg', 600, 800);
        $fileDoc = UploadedFile::fake()->create('surat3.docx', 150, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

        $response = $this->actingAs($this->user)->post('/admin/surat-uks/generate-blangko', [
            'jenis' => 'pulang',
            'jumlah' => 0,
            'berkas_surat' => [$filePdf, $fileJpg, $fileDoc],
        ]);

        $response->assertRedirect('/admin/surat-uks');
        $response->assertSessionHas('success');

        $surats = SuratUks::latest('id')->take(3)->get();
        $this->assertCount(3, $surats);

        foreach ($surats as $surat) {
            $this->assertEquals('pulang', $surat->jenis);
            $this->assertEquals('tersedia', $surat->status);
            $this->assertNotNull($surat->file_surat);
            $this->assertEquals(4, strlen($surat->kode_verifikasi));
            Storage::disk('public')->assertExists($surat->file_surat);
        }
    }

    public function test_generate_blangko_fails_if_no_files_and_jumlah_is_zero(): void
    {
        $response = $this->actingAs($this->user)->post('/admin/surat-uks/generate-blangko', [
            'jenis' => 'pulang',
            'jumlah' => 0,
        ]);

        $response->assertSessionHasErrors(['jumlah']);
    }

    public function test_cek_kode_endpoint_returns_realtime_verification_status(): void
    {
        // 1. Kode belum terdaftar
        $resNotFound = $this->actingAs($this->user)->getJson('/admin/surat-uks/cek-kode?kode=NON9');
        $resNotFound->assertStatus(200)
            ->assertJson([
                'found' => false,
                'status' => 'tidak_ada',
            ]);

        // 2. Kode terdaftar & berstatus tersedia
        $suratTersedia = SuratUks::create([
            'nomor_surat' => 'OK01',
            'kode_verifikasi' => 'OK01',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'tersedia',
        ]);

        $resTersedia = $this->actingAs($this->user)->getJson("/admin/surat-uks/cek-kode?kode={$suratTersedia->kode_verifikasi}");
        $resTersedia->assertStatus(200)
            ->assertJson([
                'found' => true,
                'status' => 'tersedia',
                'jenis' => 'pulang',
            ]);

        // 3. Kode terdaftar & berstatus terjual
        $suratTerjual = SuratUks::create([
            'nomor_surat' => 'OK02',
            'kode_verifikasi' => 'OK02',
            'jenis' => 'sakit',
            'tanggal_cetak' => Carbon::today(),
            'nama_santri' => 'Ahmad Santri',
            'kelas' => '2 Aliyah',
            'asrama' => 'Kamar 01',
            'status' => 'terjual',
        ]);

        $resTerjual = $this->actingAs($this->user)->getJson("/admin/surat-uks/cek-kode?kode={$suratTerjual->kode_verifikasi}");
        $resTerjual->assertStatus(200)
            ->assertJson([
                'found' => true,
                'status' => 'terjual',
                'nama_santri' => 'Ahmad Santri',
                'kelas' => '2 Aliyah',
                'asrama' => 'Kamar 01',
            ]);
    }

    public function test_secretary_can_get_realtime_stats_json(): void
    {
        SuratUks::create([
            'nomor_surat' => 'ST01',
            'kode_verifikasi' => 'ST01',
            'jenis' => 'pulang',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'tersedia',
        ]);

        SuratUks::create([
            'nomor_surat' => 'ST02',
            'kode_verifikasi' => 'ST02',
            'jenis' => 'sakit',
            'tanggal_cetak' => Carbon::today(),
            'status' => 'terjual',
            'harga' => 5000,
        ]);

        $response = $this->actingAs($this->user)->getJson('/admin/surat-uks/stats');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_tercetak',
                'total_cetak_pulang',
                'total_cetak_sakit',
                'jumlah_terjual',
                'total_kas_masuk',
                'kas_masuk_pulang',
                'kas_masuk_sakit',
                'total_sisa_stok',
                'stok_pulang',
                'stok_sakit',
            ]);

        $this->assertEquals(2, $response->json('total_tercetak'));
        $this->assertEquals(1, $response->json('total_cetak_pulang'));
        $this->assertEquals(1, $response->json('total_cetak_sakit'));
        $this->assertEquals(1, $response->json('jumlah_terjual'));
        $this->assertEquals(5000, $response->json('total_kas_masuk'));
        $this->assertEquals(1, $response->json('total_sisa_stok'));
    }
}


