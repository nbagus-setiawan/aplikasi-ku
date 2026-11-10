<?php

namespace Tests\Feature;

use App\Models\KasTransaksi;
use App\Models\KasTutupBuku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BendaharaTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_tamu_tidak_bisa_mengakses_halaman_bendahara(): void
    {
        $response = $this->get(route('admin.bendahara.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_user_terautentikasi_bisa_membuka_buku_kas_bendahara(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.bendahara.index'));
        $response->assertStatus(200);
        $response->assertSee('Buku Kas Bendahara');
        $response->assertSee('Saldo Kas Riil');
        $response->assertSee('Penerimaan (Debit)');
        $response->assertSee('Pengeluaran (Kredit)');
    }

    public function test_bisa_mencatat_transaksi_debit_kas_masuk(): void
    {
        $payload = [
            'tanggal' => now()->toDateString(),
            'jenis' => 'debit',
            'kategori' => 'Infaq & Shodaqoh',
            'nominal' => 750000,
            'keterangan' => 'Infaq pembangunan asrama santri baru',
            'nama_pihak' => 'Hamba Allah',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.bendahara.store'), $payload);

        $response->assertRedirect(route('admin.bendahara.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kas_transaksis', [
            'jenis' => 'debit',
            'kategori' => 'Infaq & Shodaqoh',
            'nominal' => 750000,
            'nama_pihak' => 'Hamba Allah',
        ]);

        $transaksi = KasTransaksi::first();
        $this->assertStringStartsWith('BKM-', $transaksi->kode_transaksi);
    }

    public function test_bisa_mencatat_transaksi_kredit_kas_keluar(): void
    {
        $payload = [
            'tanggal' => now()->toDateString(),
            'jenis' => 'kredit',
            'kategori' => 'ATK & Kesekretariatan',
            'nominal' => 200000,
            'keterangan' => 'Pembelian kertas HVS dan tinta printer kantor',
            'nama_pihak' => 'Toko ATK Al-Huda',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.bendahara.store'), $payload);

        $response->assertRedirect(route('admin.bendahara.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kas_transaksis', [
            'jenis' => 'kredit',
            'kategori' => 'ATK & Kesekretariatan',
            'nominal' => 200000,
            'nama_pihak' => 'Toko ATK Al-Huda',
        ]);

        $transaksi = KasTransaksi::first();
        $this->assertStringStartsWith('BKK-', $transaksi->kode_transaksi);
    }

    public function test_bisa_mencatat_transaksi_kas_tanpa_keterangan_opsional(): void
    {
        $payload = [
            'tanggal' => now()->toDateString(),
            'jenis' => 'debit',
            'kategori' => 'Donasi & Bantuan',
            'nominal' => 300000,
            // keterangan sengaja dikosongkan (opsional)
        ];

        $response = $this->actingAs($this->user)->post(route('admin.bendahara.store'), $payload);

        $response->assertRedirect(route('admin.bendahara.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kas_transaksis', [
            'jenis' => 'debit',
            'kategori' => 'Donasi & Bantuan',
            'nominal' => 300000,
            'keterangan' => null,
        ]);
    }

    public function test_perhitungan_saldo_kas_debit_dan_kredit_akurat(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => now()->toDateString(),
            'jenis' => 'debit',
            'kategori' => 'Infaq & Shodaqoh',
            'nominal' => 1000000,
            'keterangan' => 'Pemasukan Kas 1',
            'user_id' => $this->user->id,
        ]);

        KasTransaksi::create([
            'kode_transaksi' => 'BKK-202610-0001',
            'tanggal' => now()->toDateString(),
            'jenis' => 'kredit',
            'kategori' => 'Operasional',
            'nominal' => 350000,
            'keterangan' => 'Pengeluaran Kas 1',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.index'));

        $response->assertStatus(200);
        // Saldo akhir harus 650.000
        $response->assertSee('650.000');
    }

    public function test_bisa_memperbarui_transaksi_kas(): void
    {
        $transaksi = KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => now()->toDateString(),
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 500000,
            'keterangan' => 'Keterangan awal',
            'user_id' => $this->user->id,
        ]);

        $updatePayload = [
            'tanggal' => now()->toDateString(),
            'jenis' => 'debit',
            'kategori' => 'Donasi & Bantuan',
            'nominal' => 700000,
            'keterangan' => 'Keterangan setelah diedit',
            'nama_pihak' => 'Bpk. Ridwan',
        ];

        $response = $this->actingAs($this->user)
            ->put(route('admin.bendahara.update', $transaksi->id), $updatePayload);

        $response->assertRedirect(route('admin.bendahara.index'));
        $this->assertDatabaseHas('kas_transaksis', [
            'id' => $transaksi->id,
            'nominal' => 700000,
            'kategori' => 'Donasi & Bantuan',
            'keterangan' => 'Keterangan setelah diedit',
        ]);
    }

    public function test_bisa_menghapus_transaksi_kas(): void
    {
        $transaksi = KasTransaksi::create([
            'kode_transaksi' => 'BKK-202610-0001',
            'tanggal' => now()->toDateString(),
            'jenis' => 'kredit',
            'kategori' => 'Konsumsi',
            'nominal' => 150000,
            'keterangan' => 'Snack rapat pengurus',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('admin.bendahara.destroy', $transaksi->id));

        $response->assertRedirect(route('admin.bendahara.index'));
        $this->assertDatabaseMissing('kas_transaksis', ['id' => $transaksi->id]);
    }

    public function test_bisa_mencetak_buku_kas_umum_pdf(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => now()->toDateString(),
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 500000,
            'keterangan' => 'Infaq Donatur',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.cetak'));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_cetak_buku_kas_tidak_menampilkan_teks_kepada_dan_kop_proporsional(): void
    {
        $transaksi = KasTransaksi::create([
            'kode_transaksi' => 'BKK-202610-0001',
            'tanggal' => '2026-10-01',
            'jenis' => 'kredit',
            'kategori' => 'Beban rapat',
            'nominal' => 15000,
            'keterangan' => 'Aqua kecil',
            'nama_pihak' => 'Toko sinar raya',
            'user_id' => $this->user->id,
        ]);

        $html = view('bendahara.cetak', [
            'transaksis' => collect([$transaksi]),
            'saldoAwal' => 100000,
            'totalDebit' => 0,
            'totalKredit' => 15000,
            'saldoAkhir' => 85000,
            'dari' => '2026-10-01',
            'sampai' => '2026-10-29',
            'jenis' => null,
            'kategori' => null,
            'periode' => 'rentang_bulan',
            'q' => null,
            'labelPeriode' => '01 Oktober s.d 29 Oktober 2026',
            'tanggal_awal_teks' => '01 Oktober',
            'tanggal_akhir_teks' => '29 Oktober 2026',
            'pengaturan' => [],
            'pengaturans' => [],
            'logo' => null,
            'kopGambar' => 'data:image/png;base64,sample',
            'isHtmlPrint' => true,
        ])->render();

        $this->assertStringContainsString('Aqua kecil', $html);
        $this->assertStringNotContainsString('Kepada:', $html);
        $this->assertStringNotContainsString('Toko sinar raya', $html);
        $this->assertStringContainsString('Danil Ali Sabila', $html);
        $this->assertStringContainsString('Ketua Umum', $html);
        $this->assertStringContainsString('Azwil Yoga Pratama', $html);
        $this->assertStringContainsString('Bendahara', $html);
        $this->assertStringNotContainsString('Mursyidi', $html);
        $this->assertStringContainsString('replaceState', $html);
    }

    public function test_bisa_mengunduh_buku_kas_umum_format_word_doc(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0002',
            'tanggal' => now()->toDateString(),
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 750000,
            'keterangan' => 'Infaq Hamba Allah',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.word'));

        $response->assertStatus(200);
        $contentType = $response->headers->get('Content-Type');
        $this->assertTrue(
            str_contains($contentType, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') ||
            str_contains($contentType, 'application/vnd.ms-word')
        );
        $this->assertStringContainsString('attachment; filename=', $response->headers->get('Content-Disposition'));
        $this->assertTrue(strlen($response->getContent()) > 0);
    }

    public function test_halaman_index_merender_modal_dan_atribut_alpine_dengan_aman(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0003',
            'tanggal' => now()->toDateString(),
            'jenis' => 'debit',
            'kategori' => 'Infaq "Khusus"',
            'nominal' => 1250000,
            'keterangan' => 'Keterangan dengan "tanda kutip" & karakter khusus',
            'nama_pihak' => 'H. "Fulan"',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.index'));

        $response->assertStatus(200);
        $content = $response->getContent();

        // Pastikan tidak ada raw json_encode yang memecah atribut @click="openEdit({"id":
        $this->assertStringNotContainsString('@click="openEdit({"id":', $content);
        $this->assertStringContainsString('modalDebitOpen', $content);
        $this->assertStringContainsString('modalKreditOpen', $content);
        $this->assertStringContainsString('Catat Debit', $content);
        $this->assertStringContainsString('Catat Kredit', $content);
    }

    public function test_ekspor_word_menggunakan_template_buku_kas_docx_dengan_data_terisi_otomatis(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0010',
            'tanggal' => '2026-10-15',
            'jenis' => 'debit',
            'kategori' => 'Penerimaan Sumbangan',
            'nominal' => 2000000,
            'keterangan' => 'Donasi operasional pondok',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.word', [
            'periode' => 'kustom',
            'dari_tanggal' => '2026-10-01',
            'sampai_tanggal' => '2026-10-31',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $response->headers->get('Content-Type'));

        // Baca zip docx hasil unduhan menggunakan parser bawaan
        $binary = $response->getContent();
        $tempFile = tempnam(sys_get_temp_dir(), 'test_docx_');
        file_put_contents($tempFile, $binary);

        $parser = app(\App\Services\DocxSuratParserService::class);
        $entries = $parser->readZipEntries($tempFile);
        $docXml = $entries['word/document.xml'] ?? '';
        @unlink($tempFile);

        $this->assertNotEmpty($docXml);
        // Pastikan placeholder periode terisi tanggal riil bahasa Indonesia (bukan kata Awal/Sekarang)
        $this->assertStringContainsString('Oktober 2026', $docXml);
        $this->assertStringNotContainsString('Awal', $docXml);
        $this->assertStringNotContainsString('Sekarang', $docXml);
        $this->assertStringContainsString('BKM-202610-0010', $docXml);
        $this->assertStringContainsString('Penerimaan Sumbangan', $docXml);
        $this->assertStringContainsString('Donasi operasional pondok', $docXml);
        $this->assertStringContainsString('2.000.000', $docXml);
        // Pastikan tidak ada placeholder yang belum terganti
        $this->assertStringNotContainsString('{No.}', $docXml);
        $this->assertStringNotContainsString('{tanggal_transaksi}', $docXml);
        $this->assertStringNotContainsString('{No.bukti}', $docXml);
        $this->assertStringNotContainsString('{akun}', $docXml);
        $this->assertStringNotContainsString('{keterangan}', $docXml);
        $this->assertStringNotContainsString('{debit}', $docXml);
        $this->assertStringNotContainsString('{kredit}', $docXml);
        $this->assertStringNotContainsString('{saldo}', $docXml);
        $this->assertStringNotContainsString('{jumlah_debit}', $docXml);
        $this->assertStringNotContainsString('{jumlah_kredit}', $docXml);
    }

    public function test_urutan_transaksi_tanpa_filter_descending_dan_dengan_filter_ascending(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-03',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 100000,
            'keterangan' => 'Transaksi awal bulan',
            'user_id' => $this->user->id,
        ]);

        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0002',
            'tanggal' => '2026-10-20',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 200000,
            'keterangan' => 'Transaksi akhir periode',
            'user_id' => $this->user->id,
        ]);

        // 1. Tanpa Filter (Mode Semua): Urutan Descending (terbaru ke terlama: 20 Oktober dulu, baru 3 Oktober)
        $responseNoFilter = $this->actingAs($this->user)->get(route('admin.bendahara.index', ['filter_tipe' => 'semua']));
        $responseNoFilter->assertStatus(200);
        $transaksisNoFilter = $responseNoFilter->viewData('transaksis');
        $this->assertEquals('BKM-202610-0002', $transaksisNoFilter->first()->kode_transaksi);
        $this->assertEquals('BKM-202610-0001', $transaksisNoFilter->last()->kode_transaksi);

        // 2. Dengan Filter (Mode Bulan Tercatat): Urutan Ascending kronologis buku kas (03 Oktober dulu, baru 20 Oktober)
        $responseFiltered = $this->actingAs($this->user)->get(route('admin.bendahara.index', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => '2026-10',
        ]));
        $responseFiltered->assertStatus(200);
        $transaksisFiltered = $responseFiltered->viewData('transaksis');
        $this->assertEquals('BKM-202610-0001', $transaksisFiltered->first()->kode_transaksi);
        $this->assertEquals('BKM-202610-0002', $transaksisFiltered->last()->kode_transaksi);
    }

    public function test_periode_dinamis_berdasarkan_transaksi_riil(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-03',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 500000,
            'keterangan' => 'Catatan tgl 3',
            'user_id' => $this->user->id,
        ]);

        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0002',
            'tanggal' => '2026-10-20',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 300000,
            'keterangan' => 'Catatan tgl 20',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.index', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => '2026-10',
        ]));

        $response->assertStatus(200);
        // Periode harus menampilkan rentang riil 03 Oktober s.d. 20 Oktober 2026
        $labelPeriode = $response->viewData('labelPeriode');
        $this->assertStringContainsString('03 Oktober', $labelPeriode);
        $this->assertStringContainsString('20 Oktober 2026', $labelPeriode);
        $this->assertStringNotContainsString('Awal', $labelPeriode);
        $this->assertStringNotContainsString('Sekarang', $labelPeriode);
    }

    public function test_ajax_store_update_destroy_dan_filter(): void
    {
        // 1. Simpan Transaksi via AJAX
        $storeRes = $this->actingAs($this->user)->postJson(route('admin.bendahara.store'), [
            'tanggal' => '2026-10-05',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 450000,
            'keterangan' => 'Simpan AJAX',
        ]);

        $storeRes->assertStatus(200);
        $storeRes->assertJsonStructure(['success', 'message', 'transaksi_id']);
        $transaksiId = $storeRes->json('transaksi_id');

        // 2. Update via AJAX
        $updateRes = $this->actingAs($this->user)->putJson(route('admin.bendahara.update', $transaksiId), [
            'tanggal' => '2026-10-06',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 600000,
            'keterangan' => 'Update AJAX',
        ]);

        $updateRes->assertStatus(200);
        $updateRes->assertJsonStructure(['success', 'message', 'transaksi_id']);

        // 3. Filter via AJAX
        $filterRes = $this->actingAs($this->user)->getJson(route('admin.bendahara.index', ['filter_tipe' => 'semua']));
        $filterRes->assertStatus(200);
        $filterRes->assertJsonStructure([
            'success',
            'html_cards' => ['saldo_kas', 'total_debit', 'total_kredit'],
            'html_mobile',
            'html_desktop',
        ]);

        // 4. Hapus via AJAX
        $deleteRes = $this->actingAs($this->user)->deleteJson(route('admin.bendahara.destroy', $transaksiId));
        $deleteRes->assertStatus(200);
        $deleteRes->assertJsonStructure(['success', 'message']);
        $this->assertDatabaseMissing('kas_transaksis', ['id' => $transaksiId]);
    }

    public function test_unduh_laporan_dengan_rentang_bulan_dari_modal(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202608-0001',
            'tanggal' => '2026-08-10',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 1000000,
            'keterangan' => 'Transaksi Agustus',
            'user_id' => $this->user->id,
        ]);

        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-15',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 2000000,
            'keterangan' => 'Transaksi Oktober',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.word', [
            'mode' => 'rentang_bulan',
            'bulan_dari' => '2026-08',
            'bulan_sampai' => '2026-10',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/vnd.openxmlformats-officedocument.wordprocessingml.document', $response->headers->get('Content-Type'));
    }

    public function test_bisa_tutup_buku_dan_mengunci_transaksi_bulan_tersebut(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-10',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 1500000,
            'keterangan' => 'Infaq bulan Oktober',
            'user_id' => $this->user->id,
        ]);

        KasTransaksi::create([
            'kode_transaksi' => 'BKK-202610-0001',
            'tanggal' => '2026-10-25',
            'jenis' => 'kredit',
            'kategori' => 'Operasional',
            'nominal' => 500000,
            'keterangan' => 'Beli ATK',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('admin.bendahara.tutup-buku'), [
            'tahun_bulan' => '2026-10',
            'catatan' => 'Tutup buku resmi Oktober',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('kas_tutup_bukus', [
            'tahun_bulan' => '2026-10',
            'total_debit' => 1500000,
            'total_kredit' => 500000,
            'saldo_akhir' => 1000000,
            'status' => 'tutup',
        ]);

        $this->assertTrue(KasTutupBuku::isTutup('2026-10'));
    }

    public function test_transaksi_baru_pada_bulan_tertutup_ditolak(): void
    {
        KasTutupBuku::create([
            'tahun_bulan' => '2026-10',
            'tanggal_tutup' => '2026-10-31',
            'saldo_awal' => 0,
            'total_debit' => 1000000,
            'total_kredit' => 0,
            'saldo_akhir' => 1000000,
            'status' => 'tutup',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->postJson(route('admin.bendahara.store'), [
            'tanggal' => '2026-10-15',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 250000,
            'keterangan' => 'Coba tambah transaksi di bulan yang sudah ditutup',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tanggal']);
    }

    public function test_edit_transaksi_pada_bulan_tertutup_ditolak(): void
    {
        $transaksi = KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-05',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 500000,
            'keterangan' => 'Transaksi awal',
            'user_id' => $this->user->id,
        ]);

        KasTutupBuku::create([
            'tahun_bulan' => '2026-10',
            'tanggal_tutup' => '2026-10-31',
            'saldo_awal' => 0,
            'total_debit' => 500000,
            'total_kredit' => 0,
            'saldo_akhir' => 500000,
            'status' => 'tutup',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->putJson(route('admin.bendahara.update', $transaksi->id), [
            'tanggal' => '2026-10-05',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 800000,
            'keterangan' => 'Coba edit',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['tanggal']);
    }

    public function test_hapus_transaksi_pada_bulan_tertutup_ditolak(): void
    {
        $transaksi = KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-05',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 500000,
            'keterangan' => 'Transaksi awal',
            'user_id' => $this->user->id,
        ]);

        KasTutupBuku::create([
            'tahun_bulan' => '2026-10',
            'tanggal_tutup' => '2026-10-31',
            'saldo_awal' => 0,
            'total_debit' => 500000,
            'total_kredit' => 0,
            'saldo_akhir' => 500000,
            'status' => 'tutup',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->deleteJson(route('admin.bendahara.destroy', $transaksi->id));

        $response->assertStatus(422);
        $this->assertDatabaseHas('kas_transaksis', ['id' => $transaksi->id]);
    }

    public function test_bisa_buka_kunci_buku_kas_darurat(): void
    {
        KasTutupBuku::create([
            'tahun_bulan' => '2026-10',
            'tanggal_tutup' => '2026-10-31',
            'saldo_awal' => 0,
            'total_debit' => 500000,
            'total_kredit' => 0,
            'saldo_akhir' => 500000,
            'status' => 'tutup',
            'user_id' => $this->user->id,
        ]);

        $this->assertTrue(KasTutupBuku::isTutup('2026-10'));

        $response = $this->actingAs($this->user)->postJson(route('admin.bendahara.buka-kunci'), [
            'tahun_bulan' => '2026-10',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertFalse(KasTutupBuku::isTutup('2026-10'));
    }

    public function test_cetak_pdf_sesuai_template_resmi_dan_saldo_awal(): void
    {
        $transaksi = KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-15',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 500000,
            'keterangan' => 'Infaq Santri',
            'user_id' => $this->user->id,
        ]);

        $html = view('bendahara.cetak', [
            'transaksis' => collect([$transaksi]),
            'saldoAwal' => 2500000,
            'totalDebit' => 500000,
            'totalKredit' => 0,
            'saldoAkhir' => 3000000,
            'dari' => '2026-10-01',
            'sampai' => '2026-10-31',
            'jenis' => null,
            'kategori' => null,
            'periode' => 'satu_bulan',
            'q' => null,
            'labelPeriode' => 'Oktober 2026',
            'tanggal_awal_teks' => '01 Oktober',
            'tanggal_akhir_teks' => '31 Oktober 2026',
            'tanggalAkhirRiil' => '31 Oktober 2026',
            'pengaturan' => [],
            'pengaturans' => [],
            'logo' => null,
            'kopGambar' => 'data:image/png;base64,sample',
            'isHtmlPrint' => true,
        ])->render();

        // 1. Saldo Awal baris pertama di tabel
        $this->assertStringContainsString('Saldo awal sebelum periode', $html);
        $this->assertStringContainsString('2.500.000', $html);

        // 2. Judul dan kolom sesuai template resmi
        $this->assertStringContainsString('LAPORAN BUKU KAS BULANAN', $html);
        $this->assertStringContainsString('TANGGAL TRANSAKSI', $html);
        $this->assertStringContainsString('TOTAL MUTASI &amp; SALDO AKHIR', $html);

        // 3. Tiga tanda tangan (2 di atas, 1 di tengah bawah)
        $this->assertStringContainsString('Danil Ali Sabila', $html);
        $this->assertStringContainsString('Ketua Umum AFPA', $html);
        $this->assertStringContainsString('Azwil Yoga Pratama', $html);
        $this->assertStringContainsString('Bendahara AFPA', $html);
        $this->assertStringContainsString('Ust Imam Qusyairi', $html);
        $this->assertStringContainsString('Bendahara Pusat PPAF', $html);
        $this->assertStringContainsString('BARIS 1: 2 TTD DI ATAS', $html);
        $this->assertStringContainsString('BARIS 2: 1 TTD DI BAWAH TENGAH', $html);
    }

    public function test_user_bisa_membuka_halaman_laba_rugi(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.bendahara.laba-rugi'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Laba Rugi');
        $response->assertSee('Total Pendapatan (Debit)');
        $response->assertSee('Total Beban (Kredit)');
    }

    public function test_laba_rugi_menghitung_pendapatan_beban_dan_surplus_defisit_dengan_benar(): void
    {
        // 1. Catat 2 Pendapatan
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-05',
            'jenis' => 'debit',
            'kategori' => 'Infaq & Shodaqoh',
            'nominal' => 2000000,
            'keterangan' => 'Infaq',
            'user_id' => $this->user->id,
        ]);
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0002',
            'tanggal' => '2026-10-10',
            'jenis' => 'debit',
            'kategori' => 'Donasi & Bantuan',
            'nominal' => 1000000,
            'keterangan' => 'Donasi',
            'user_id' => $this->user->id,
        ]);

        // 2. Catat 1 Beban
        KasTransaksi::create([
            'kode_transaksi' => 'BKK-202610-0001',
            'tanggal' => '2026-10-12',
            'jenis' => 'kredit',
            'kategori' => 'Operasional',
            'nominal' => 500000,
            'keterangan' => 'Listrik',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.laba-rugi', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => '2026-10',
        ]));

        $response->assertStatus(200);
        $response->assertSee('3.000.000'); // Total Pendapatan
        $response->assertSee('500.000');   // Total Beban
        $response->assertSee('2.500.000'); // Surplus Kas
        $response->assertSee('SURPLUS KAS OPERASIONAL');
    }

    public function test_laba_rugi_menambahkan_saldo_periode_lalu_ke_pendapatan_sehingga_menghasilkan_surplus(): void
    {
        // Bulan September (Periode Lalu): Sisa Saldo = 1.342.000
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202609-0001',
            'tanggal' => '2026-09-20',
            'jenis' => 'debit',
            'kategori' => 'Infaq & Shodaqoh',
            'nominal' => 1342000,
            'keterangan' => 'Sisa kas bulan September',
            'user_id' => $this->user->id,
        ]);

        // Bulan Oktober (Periode Ini): Tidak ada pemasukan baru (0), Beban = 973.000
        KasTransaksi::create([
            'kode_transaksi' => 'BKK-202610-0001',
            'tanggal' => '2026-10-10',
            'jenis' => 'kredit',
            'kategori' => 'Perlengkapan kantor',
            'nominal' => 973000,
            'keterangan' => 'Beban Oktober',
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.laba-rugi', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => '2026-10',
        ]));

        $response->assertStatus(200);
        // Total Pendapatan (A) = 0 + 1.342.000 = 1.342.000
        $this->assertEquals(1342000, $response->viewData('totalPendapatan'));
        // Total Beban (B) = 973.000
        $this->assertEquals(973000, $response->viewData('totalBeban'));
        // Hasil A - B = +369.000 (Surplus Kas Operasional)
        $this->assertEquals(369000, $response->viewData('surplusDefisit'));
        $this->assertTrue($response->viewData('isSurplus'));
        $response->assertSee('Saldo Kas Periode Sebelumnya (Bulan Lalu)');
        $response->assertSee('1.342.000');
        $response->assertSee('369.000');
        $response->assertSee('SURPLUS KAS OPERASIONAL');
    }

    public function test_bisa_mencetak_laporan_laba_rugi_pdf(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-05',
            'jenis' => 'debit',
            'kategori' => 'Infaq & Shodaqoh',
            'nominal' => 1000000,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.laba-rugi.cetak', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => '2026-10',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_cetak_laba_rugi_pdf_memuat_3_tanda_tangan_resmi(): void
    {
        $html = view('bendahara.laba_rugi_pdf', [
            'filterTipe' => 'bulan',
            'filterBulan' => '2026-10',
            'filterTahun' => '2026',
            'dari' => '2026-10-01',
            'sampai' => '2026-10-31',
            'labelPeriode' => 'Oktober 2026',
            'tanggalAkhirRiil' => '31 Oktober 2026',
            'bulanTersedia' => collect([]),
            'tahunTersedia' => collect([]),
            'pendapatans' => collect([
                (object) ['kategori' => 'Infaq & Shodaqoh', 'total' => 2000000, 'jumlah_transaksi' => 2, 'persentase' => 100],
            ]),
            'bebans' => collect([
                (object) ['kategori' => 'Operasional', 'total' => 500000, 'jumlah_transaksi' => 1, 'persentase' => 100],
            ]),
            'totalPendapatan' => 2000000,
            'totalBeban' => 500000,
            'surplusDefisit' => 1500000,
            'isSurplus' => true,
            'marginSurplusDefisit' => 75.0,
            'pengaturan' => [],
            'pengaturans' => [],
            'logo' => null,
            'isHtmlPrint' => true,
        ])->render();

        $this->assertStringContainsString('LAPORAN LABA RUGI', $html);
        $this->assertStringContainsString('Infaq &amp; Shodaqoh', $html);
        $this->assertStringContainsString('Operasional', $html);
        $this->assertStringContainsString('1.500.000', $html);

        // 3 Tanda Tangan: 2 di atas, 1 di bawah tengah
        $this->assertStringContainsString('Danil Ali Sabila', $html);
        $this->assertStringContainsString('Ketua Umum AFPA', $html);

        $this->assertStringContainsString('Azwil Yoga Pratama', $html);
        $this->assertStringContainsString('Bendahara AFPA', $html);

        $this->assertStringContainsString('Ust Imam Qusyairi', $html);
        $this->assertStringContainsString('Bendahara Pusat PPAF', $html);
        $this->assertStringContainsString('Saldo Kas Periode Sebelumnya (Bulan Lalu)', $html);
        $this->assertStringContainsString('SURPLUS KAS OPERASIONAL (A - B)', $html);
    }

    public function test_user_bisa_membuka_halaman_neraca(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.bendahara.neraca'));
        $response->assertStatus(200);
        $response->assertSee('Laporan Neraca');
        $response->assertSee('SISI AKTIVA (ASET)');
        $response->assertSee('SISI PASIVA');
        $response->assertSee('SEIMBANG (BALANCE)');
        // Pastikan pos kosong yang tidak ada inputannya sudah tidak tampil
        $response->assertDontSee('Rekening Bank Pesantren');
        $response->assertDontSee('Piutang & Uang Muka Operasional');
        $response->assertDontSee('Inventaris & Peralatan Kantor Sekretariat');
        $response->assertDontSee('Utang Operasional Jangka Pendek');
    }

    public function test_neraca_menghitung_aset_kewajiban_ekuitas_dan_selalu_balance(): void
    {
        // Pemasukan 5.000.000
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-05',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 5000000,
            'user_id' => $this->user->id,
        ]);

        // Pengeluaran 1.200.000
        KasTransaksi::create([
            'kode_transaksi' => 'BKK-202610-0001',
            'tanggal' => '2026-10-15',
            'jenis' => 'kredit',
            'kategori' => 'Operasional',
            'nominal' => 1200000,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.neraca', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => '2026-10',
        ]));

        $response->assertStatus(200);
        // Sisa Kas Riil = 3.800.000
        $response->assertSee('3.800.000');
        $response->assertSee('Saldo Kas Periode Sebelumnya (Bulan Lalu)');
        $response->assertSee('Penerimaan Kas Periode Ini (Debit)');
        $response->assertSee('Beban Pengeluaran Kas (Kredit)');
        $response->assertSee('Surplus Kas Operasional (Saldo Dana):');
        $totalAset = $response->viewData('totalAset');
        $totalPasiva = $response->viewData('totalPasiva');
        $isBalance = $response->viewData('isBalance');

        $this->assertEquals(3800000, $totalAset);
        $this->assertEquals(3800000, $totalPasiva);
        $this->assertTrue($isBalance);
    }

    public function test_bisa_mencetak_laporan_neraca_pdf(): void
    {
        KasTransaksi::create([
            'kode_transaksi' => 'BKM-202610-0001',
            'tanggal' => '2026-10-05',
            'jenis' => 'debit',
            'kategori' => 'Infaq',
            'nominal' => 1000000,
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('admin.bendahara.neraca.cetak', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => '2026-10',
        ]));

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('Content-Type'));
    }

    public function test_cetak_neraca_pdf_memuat_3_tanda_tangan_resmi(): void
    {
        $html = view('bendahara.neraca_pdf', [
            'filterTipe' => 'bulan',
            'filterBulan' => '2026-10',
            'filterTahun' => '2026',
            'dari' => '2026-10-01',
            'sampai' => '2026-10-31',
            'labelPeriode' => 'Per 31 Oktober 2026',
            'tanggalAkhirRiil' => '31 Oktober 2026',
            'bulanTersedia' => collect([]),
            'tahunTersedia' => collect([]),
            'saldoAwal' => 0,
            'totalDebitPeriode' => 5000000,
            'totalKreditPeriode' => 1000000,
            'surplusDefisitPeriode' => 4000000,
            'saldoAkhirKas' => 4000000,
            'asetLancar' => [
                ['nama' => 'Kas Tunai Pesantren (Buku Kas Umum)', 'nominal' => 4000000],
            ],
            'totalAsetLancar' => 4000000,
            'asetTetap' => [],
            'totalAsetTetap' => 0,
            'totalAset' => 4000000,
            'kewajiban' => [],
            'totalKewajiban' => 0,
            'ekuitas' => [
                ['nama' => 'Saldo Awal Dana Kas (Sebelum Periode)', 'nominal' => 0],
                ['nama' => 'Surplus Kas Periode Berjalan', 'nominal' => 4000000],
            ],
            'totalEkuitas' => 4000000,
            'totalPasiva' => 4000000,
            'isBalance' => true,
            'pengaturan' => [],
            'pengaturans' => [],
            'logo' => null,
            'isHtmlPrint' => true,
        ])->render();

        $this->assertStringContainsString('LAPORAN NERACA', $html);
        $this->assertStringContainsString('Kas Tunai Pesantren', $html);
        $this->assertStringContainsString('4.000.000', $html);
        $this->assertStringContainsString('SEIMBANG (BALANCE)', $html);
        $this->assertStringNotContainsString('II. ASET TETAP (INVENTARIS)', $html);
        $this->assertStringNotContainsString('I. KEWAJIBAN (LIABILITAS)', $html);

        // 3 Tanda Tangan: 2 di atas, 1 di bawah tengah
        $this->assertStringContainsString('Danil Ali Sabila', $html);
        $this->assertStringContainsString('Ketua Umum AFPA', $html);

        $this->assertStringContainsString('Azwil Yoga Pratama', $html);
        $this->assertStringContainsString('Bendahara AFPA', $html);

        $this->assertStringContainsString('Ust Imam Qusyairi', $html);
        $this->assertStringContainsString('Bendahara Pusat PPAF', $html);
    }
}
