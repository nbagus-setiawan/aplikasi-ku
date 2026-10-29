<?php

namespace Tests\Feature;

use App\Models\KasTransaksi;
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
        $this->assertSoftDeleted('kas_transaksis', ['id' => $transaksi->id]);
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
}
