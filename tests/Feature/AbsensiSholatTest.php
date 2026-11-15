<?php

namespace Tests\Feature;

use App\Models\AbsensiPetugas;
use App\Models\AbsensiSholat;
use App\Models\AbsensiSholatJadwal;
use App\Models\Pengurus;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbsensiSholatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Inisialisasi 5 jadwal sholat default
        AbsensiSholatJadwal::inisialisasiDefault();
    }

    public function test_guest_can_access_absensi_sholat_public_page(): void
    {
        $koordinator = AbsensiPetugas::create([
            'nama' => 'Ahmad Zaki',
            'bagian' => 'koordinator',
            'status' => 'aktif',
        ]);

        $anggota = AbsensiPetugas::create([
            'nama' => 'Budi Santoso',
            'bagian' => 'anggota',
            'status' => 'aktif',
        ]);

        // 1. Saat form sedang dibuka (misal dzuhur buka manual)
        AbsensiSholatJadwal::where('nama_sholat', 'dzuhur')->update([
            'is_buka_manual' => true,
        ]);

        $responseOpen = $this->get('/absensi-sholat');
        $responseOpen->assertStatus(200);
        $responseOpen->assertSee('Presensi Korlap Sholat Jamaah');
        $responseOpen->assertSee('Ahmad Zaki');
        $responseOpen->assertSee('Budi Santoso');

        // 2. Saat form sedang ditutup
        AbsensiSholatJadwal::query()->update(['is_buka_manual' => false]);
        $responseClosed = $this->get('/absensi-sholat');
        $responseClosed->assertStatus(200);
        $responseClosed->assertSee('Presensi Sedang Ditutup');
        $responseClosed->assertSee('Hitungan Mundur Menuju Buka');
    }

    public function test_guest_is_redirected_when_accessing_admin_absensi_sholat(): void
    {
        $response = $this->get('/admin/absensi-sholat');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_secretary_can_access_admin_absensi_sholat(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/absensi-sholat');

        $response->assertStatus(200);
        $response->assertSee('Absensi Korlap');
        $response->assertSee('Monitoring 5 Sholat Jamaah');

        // Akses tab grafik
        $responseGrafik = $this->actingAs($user)->get('/admin/absensi-sholat?tab=grafik');
        $responseGrafik->assertStatus(200);
        $responseGrafik->assertSee('Grafik Tren Kehadiran Sholat Jamaah');
    }

    public function test_secretary_can_add_petugas_manually(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/absensi-sholat/petugas', [
            'sumber_nama' => 'manual',
            'nama' => 'Muhammad Rizki',
            'bagian' => 'koordinator',
            'status' => 'aktif',
            'keterangan' => 'Korlap Lantai 2',
        ]);

        $response->assertRedirect(route('admin.absensi-sholat.index', ['tab' => 'petugas']));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('absensi_petugas', [
            'nama' => 'Muhammad Rizki',
            'bagian' => 'koordinator',
            'status' => 'aktif',
            'keterangan' => 'Korlap Lantai 2',
            'pengurus_id' => null,
        ]);
    }

    public function test_secretary_can_add_petugas_from_pengurus(): void
    {
        $user = User::factory()->create();

        $pengurus = Pengurus::create([
            'nama' => 'Ust. Abdurrahman',
            'jabatan' => 'Biro Keamanan',
            'divisi' => 'Keamanan',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($user)->post('/admin/absensi-sholat/petugas', [
            'sumber_nama' => 'pengurus',
            'pengurus_id' => $pengurus->id,
            'bagian' => 'anggota',
            'status' => 'aktif',
        ]);

        $response->assertRedirect(route('admin.absensi-sholat.index', ['tab' => 'petugas']));

        $this->assertDatabaseHas('absensi_petugas', [
            'nama' => 'Ust. Abdurrahman',
            'bagian' => 'anggota',
            'status' => 'aktif',
            'pengurus_id' => $pengurus->id,
        ]);
    }

    public function test_secretary_can_update_and_delete_petugas(): void
    {
        $user = User::factory()->create();

        $petugas = AbsensiPetugas::create([
            'nama' => 'Fulan bin Fulan',
            'bagian' => 'anggota',
            'status' => 'aktif',
        ]);

        // Update
        $responseUpdate = $this->actingAs($user)->put("/admin/absensi-sholat/petugas/{$petugas->id}", [
            'nama' => 'Fulan bin Fulan Update',
            'bagian' => 'koordinator',
            'status' => 'nonaktif',
            'keterangan' => 'Pindah tugas',
        ]);

        $responseUpdate->assertRedirect(route('admin.absensi-sholat.index', ['tab' => 'petugas']));

        $this->assertDatabaseHas('absensi_petugas', [
            'id' => $petugas->id,
            'nama' => 'Fulan bin Fulan Update',
            'bagian' => 'koordinator',
            'status' => 'nonaktif',
        ]);

        // Delete (Soft Delete)
        $responseDelete = $this->actingAs($user)->delete("/admin/absensi-sholat/petugas/{$petugas->id}");
        $responseDelete->assertRedirect(route('admin.absensi-sholat.index', ['tab' => 'petugas']));

        $this->assertSoftDeleted('absensi_petugas', [
            'id' => $petugas->id,
        ]);
    }

    public function test_secretary_can_update_jadwal_sholat(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put('/admin/absensi-sholat/jadwal', [
            'jadwals' => [
                'subuh' => [
                    'jam_buka' => '04:15',
                    'jam_tepat' => '04:45',
                    'jam_tutup' => '05:40',
                    'is_buka_manual' => 'otomatis',
                ],
                'dzuhur' => [
                    'jam_buka' => '11:45',
                    'jam_tepat' => '12:15',
                    'jam_tutup' => '13:00',
                    'is_buka_manual' => 'buka',
                ],
                'ashar' => [
                    'jam_buka' => '15:00',
                    'jam_tepat' => '15:30',
                    'jam_tutup' => '16:15',
                    'is_buka_manual' => 'otomatis',
                ],
                'maghrib' => [
                    'jam_buka' => '17:45',
                    'jam_tepat' => '18:15',
                    'jam_tutup' => '18:50',
                    'is_buka_manual' => 'otomatis',
                ],
                'isya' => [
                    'jam_buka' => '19:00',
                    'jam_tepat' => '19:30',
                    'jam_tutup' => '20:30',
                    'is_buka_manual' => 'tutup',
                ],
            ],
        ]);

        $response->assertRedirect(route('admin.absensi-sholat.index', ['tab' => 'jadwal']));

        $this->assertDatabaseHas('absensi_sholat_jadwals', [
            'nama_sholat' => 'subuh',
            'jam_buka' => '04:15:00',
            'jam_tepat' => '04:45:00',
            'jam_tutup' => '05:40:00',
            'is_buka_manual' => null,
        ]);

        $this->assertDatabaseHas('absensi_sholat_jadwals', [
            'nama_sholat' => 'dzuhur',
            'is_buka_manual' => 1,
        ]);

        $this->assertDatabaseHas('absensi_sholat_jadwals', [
            'nama_sholat' => 'isya',
            'is_buka_manual' => 0,
        ]);
    }

    public function test_koordinator_can_submit_presensi_from_public_page(): void
    {
        // Set waktu sholat dzuhur buka manual agar bisa absen kapan saja saat pengujian
        AbsensiSholatJadwal::where('nama_sholat', 'dzuhur')->update([
            'is_buka_manual' => true,
            'jam_tepat' => '23:59:00',
        ]);

        $koordinator = AbsensiPetugas::create([
            'nama' => 'Ust. Hamdan',
            'bagian' => 'koordinator',
            'status' => 'aktif',
        ]);

        $anggota1 = AbsensiPetugas::create([
            'nama' => 'Santri A',
            'bagian' => 'anggota',
            'status' => 'aktif',
        ]);

        $anggota2 = AbsensiPetugas::create([
            'nama' => 'Santri B',
            'bagian' => 'anggota',
            'status' => 'aktif',
        ]);

        $response = $this->post('/absensi-sholat', [
            'waktu_sholat' => 'dzuhur',
            'koordinator_id' => $koordinator->id,
            'presensi' => [
                $anggota1->id => [
                    'status' => 'hadir',
                    'keterangan' => '',
                ],
                $anggota2->id => [
                    'status' => 'sakit',
                    'keterangan' => 'Demam di UKS',
                ],
            ],
        ]);

        $response->assertRedirect(route('absensi-sholat.index', ['sholat' => 'dzuhur']));
        $response->assertSessionHas('success');

        // Koordinator otomatis tercatat Hadir
        $this->assertDatabaseHas('absensi_sholats', [
            'petugas_id' => $koordinator->id,
            'waktu_sholat' => 'dzuhur',
            'status' => 'hadir',
        ]);

        // Anggota 1 Hadir
        $this->assertDatabaseHas('absensi_sholats', [
            'petugas_id' => $anggota1->id,
            'waktu_sholat' => 'dzuhur',
            'status' => 'hadir',
        ]);

        // Anggota 2 Sakit
        $this->assertDatabaseHas('absensi_sholats', [
            'petugas_id' => $anggota2->id,
            'waktu_sholat' => 'dzuhur',
            'status' => 'sakit',
            'keterangan' => 'Demam di UKS',
        ]);
    }

    public function test_submit_fails_when_jadwal_sholat_is_closed(): void
    {
        // Tutup manual jadwal subuh
        AbsensiSholatJadwal::where('nama_sholat', 'subuh')->update([
            'is_buka_manual' => false,
        ]);

        $koordinator = AbsensiPetugas::create([
            'nama' => 'Ust. Hamdan',
            'bagian' => 'koordinator',
            'status' => 'aktif',
        ]);

        $anggota = AbsensiPetugas::create([
            'nama' => 'Santri A',
            'bagian' => 'anggota',
            'status' => 'aktif',
        ]);

        $response = $this->post('/absensi-sholat', [
            'waktu_sholat' => 'subuh',
            'koordinator_id' => $koordinator->id,
            'presensi' => [
                $anggota->id => [
                    'status' => 'hadir',
                ],
            ],
        ]);

        $response->assertSessionHas('error');
    }

    public function test_secretary_can_update_log_status_manually(): void
    {
        $user = User::factory()->create();

        $petugas = AbsensiPetugas::create([
            'nama' => 'Santri C',
            'bagian' => 'anggota',
            'status' => 'aktif',
        ]);

        $log = AbsensiSholat::create([
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_sholat' => 'ashar',
            'petugas_id' => $petugas->id,
            'jam_absen' => '15:10:00',
            'status' => 'alfa',
            'kategori_waktu' => null,
            'keterangan' => 'Belum ada kabar',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->put("/admin/absensi-sholat/log/{$log->id}", [
            'status' => 'hadir',
            'kategori_waktu' => 'tepat_waktu',
            'keterangan' => 'Koreksi sekretaris, hadir di shaf depan',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('absensi_sholats', [
            'id' => $log->id,
            'status' => 'hadir',
            'kategori_waktu' => 'tepat_waktu',
            'keterangan' => 'Koreksi sekretaris, hadir di shaf depan',
        ]);
    }

    public function test_secretary_can_print_rekap_pdf(): void
    {
        $user = User::factory()->create();

        $petugas = AbsensiPetugas::create([
            'nama' => 'Ust. Hamdan',
            'bagian' => 'koordinator',
            'status' => 'aktif',
        ]);

        AbsensiSholat::create([
            'tanggal' => Carbon::today()->toDateString(),
            'waktu_sholat' => 'subuh',
            'petugas_id' => $petugas->id,
            'jam_absen' => '04:30:00',
            'status' => 'hadir',
            'kategori_waktu' => 'tepat_waktu',
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->get('/admin/absensi-sholat/cetak-rekap?bulan=' . Carbon::today()->format('Y-m'));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type', ''), 'application/pdf') ||
            $response->assertSee('REKAPITULASI PRESENSI SHOLAT JAMAAH KORLAP')
        );
    }

    public function test_kondisi_presensi_countdown_and_nearest_sholat(): void
    {
        // 1. Kondisi jam 05:32 WIB (Subuh tutup 05:30) -> Sholat berikutnya adalah Dzuhur (buka 12:00)
        $simulatedNow = Carbon::parse('2026-11-16 05:32:00');
        $kondisi = AbsensiSholatJadwal::getKondisiPresensi($simulatedNow);

        $this->assertEquals('dzuhur', $kondisi['sholat']->nama_sholat);
        $this->assertFalse($kondisi['is_bisa_absen']);
        $this->assertEquals('tutup', $kondisi['fase']);
        $this->assertEquals('2026-11-16 12:00:00', $kondisi['target_waktu']->format('Y-m-d H:i:s'));

        // 2. Kondisi jam 12:15 WIB (Dzuhur buka 12:00, tepat 12:30, tutup 13:00) -> Dzuhur sedang buka (tepat)
        $simulatedDzuhur = Carbon::parse('2026-11-16 12:15:00');
        $kondisiDzuhur = AbsensiSholatJadwal::getKondisiPresensi($simulatedDzuhur);

        $this->assertEquals('dzuhur', $kondisiDzuhur['sholat']->nama_sholat);
        $this->assertTrue($kondisiDzuhur['is_bisa_absen']);
        $this->assertEquals('tepat', $kondisiDzuhur['fase']);
        $this->assertEquals('2026-11-16 12:30:00', $kondisiDzuhur['target_waktu']->format('Y-m-d H:i:s'));

        // 3. Kondisi jam 12:45 WIB -> Dzuhur sedang buka (terlambat, menuju tutup 13:00)
        $simulatedTelat = Carbon::parse('2026-11-16 12:45:00');
        $kondisiTelat = AbsensiSholatJadwal::getKondisiPresensi($simulatedTelat);

        $this->assertEquals('dzuhur', $kondisiTelat['sholat']->nama_sholat);
        $this->assertTrue($kondisiTelat['is_bisa_absen']);
        $this->assertEquals('telat', $kondisiTelat['fase']);
        $this->assertEquals('2026-11-16 13:00:00', $kondisiTelat['target_waktu']->format('Y-m-d H:i:s'));
    }
}
