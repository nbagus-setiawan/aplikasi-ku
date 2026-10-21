<?php

namespace Tests\Unit;

use App\Models\NomorSuratCounter;
use App\Models\Pengaturan;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NomorSuratServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NomorSuratService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NomorSuratService();

        Pengaturan::set('pola_nomor_surat', '{urut}/{jenis}/{lembaga}/{bulan}/{tahun}');
        Pengaturan::set('kode_lembaga', 'AF-PA');
    }

    public function test_konversi_bulan_ke_angka_romawi(): void
    {
        $this->assertEquals('I', $this->service->toRomanMonth(1));
        $this->assertEquals('II', $this->service->toRomanMonth(2));
        $this->assertEquals('III', $this->service->toRomanMonth(3));
        $this->assertEquals('IV', $this->service->toRomanMonth(4));
        $this->assertEquals('V', $this->service->toRomanMonth(5));
        $this->assertEquals('VI', $this->service->toRomanMonth(6));
        $this->assertEquals('VII', $this->service->toRomanMonth(7));
        $this->assertEquals('VIII', $this->service->toRomanMonth(8));
        $this->assertEquals('IX', $this->service->toRomanMonth(9));
        $this->assertEquals('X', $this->service->toRomanMonth(10));
        $this->assertEquals('XI', $this->service->toRomanMonth(11));
        $this->assertEquals('XII', $this->service->toRomanMonth(12));
    }

    public function test_format_nomor_surat_keluar_sesuai_pola(): void
    {
        $tanggal = Carbon::create(2026, 10, 12);
        $nomor = $this->service->formatNomorSurat(1, 'SPm.01', $tanggal);

        $this->assertEquals('001/SPm.01/AF-PA/X/2026', $nomor);
    }

    public function test_urutan_nomor_surat_bertambah_secara_otomatis(): void
    {
        $tanggal = Carbon::create(2026, 5, 10);

        $nomor1 = $this->service->generateNomorSuratKeluar('SPm.01', $tanggal);
        $nomor2 = $this->service->generateNomorSuratKeluar('SPm.01', $tanggal);
        $nomor3 = $this->service->generateNomorSuratKeluar('SPb.02', $tanggal);

        $this->assertEquals('001/SPm.01/AF-PA/V/2026', $nomor1);
        $this->assertEquals('002/SPm.01/AF-PA/V/2026', $nomor2);
        $this->assertEquals('003/SPb.02/AF-PA/V/2026', $nomor3);
    }

    public function test_urutan_nomor_surat_reset_setiap_tahun(): void
    {
        $tahun2026 = Carbon::create(2026, 12, 1);
        $tahun2027 = Carbon::create(2027, 1, 15);

        $nomor2026_1 = $this->service->generateNomorSuratKeluar('SPm.01', $tahun2026);
        $nomor2026_2 = $this->service->generateNomorSuratKeluar('SPm.01', $tahun2026);

        // Tahun baru 2027 harus reset mulai dari 001 kembali
        $nomor2027_1 = $this->service->generateNomorSuratKeluar('SPm.01', $tahun2027);

        $this->assertEquals('001/SPm.01/AF-PA/XII/2026', $nomor2026_1);
        $this->assertEquals('002/SPm.01/AF-PA/XII/2026', $nomor2026_2);
        $this->assertEquals('001/SPm.01/AF-PA/I/2027', $nomor2027_1);
    }

    public function test_tidak_ada_nomor_surat_ganda_secara_atomik(): void
    {
        $tanggal = Carbon::create(2026, 8, 17);
        $nomors = [];

        for ($i = 0; $i < 10; $i++) {
            $nomors[] = $this->service->generateNomorSuratKeluar('SPm.01', $tanggal);
        }

        $this->assertCount(10, array_unique($nomors));
        $this->assertEquals('010/SPm.01/AF-PA/VIII/2026', end($nomors));
    }

    public function test_urutan_agenda_surat_masuk_otomatis(): void
    {
        $tanggal = Carbon::create(2026, 3, 1);

        $agenda1 = $this->service->generateNomorAgenda($tanggal);
        $agenda2 = $this->service->generateNomorAgenda($tanggal);

        $this->assertEquals('001', $agenda1);
        $this->assertEquals('002', $agenda2);
    }
}
