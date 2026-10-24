<?php

namespace Tests\Feature;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_correct_santri_education_and_asrama_statistics(): void
    {
        $user = User::factory()->create();

        // 1. Santri MTs
        Santri::create([
            'nis' => '1001',
            'nama' => 'Santri MTs 7',
            'tempat_lahir' => 'Banjarmasin',
            'tanggal_lahir' => '2012-01-01',
            'kelas' => 'VII 1',
            'kamar' => 'AS-SIBYAN',
            'status' => 'aktif',
            'tahun_masuk' => 2024,
        ]);
        Santri::create([
            'nis' => '1002',
            'nama' => 'Santri MTs 8',
            'tempat_lahir' => 'Banjarbaru',
            'tanggal_lahir' => '2011-01-01',
            'kelas' => 'VIII 2',
            'kamar' => 'AS-SIBYAN',
            'status' => 'aktif',
            'tahun_masuk' => 2023,
        ]);
        Santri::create([
            'nis' => '1003',
            'nama' => 'Santri MTs 9',
            'tempat_lahir' => 'Martapura',
            'tanggal_lahir' => '2010-01-01',
            'kelas' => 'IX 3',
            'kamar' => 'BAROKAH 7',
            'status' => 'aktif',
            'tahun_masuk' => 2022,
        ]);

        // 2. Santri MA
        Santri::create([
            'nis' => '2001',
            'nama' => 'Santri MA 10',
            'tempat_lahir' => 'Banjarmasin',
            'tanggal_lahir' => '2009-01-01',
            'kelas' => 'X 1',
            'kamar' => 'BAROKAH 7',
            'status' => 'aktif',
            'tahun_masuk' => 2024,
        ]);
        Santri::create([
            'nis' => '2002',
            'nama' => 'Santri MA 11',
            'tempat_lahir' => 'Banjarbaru',
            'tanggal_lahir' => '2008-01-01',
            'kelas' => 'XI 2',
            'kamar' => 'BAROKAH 9',
            'status' => 'aktif',
            'tahun_masuk' => 2023,
        ]);
        Santri::create([
            'nis' => '2003',
            'nama' => 'Santri MA 12',
            'tempat_lahir' => 'Martapura',
            'tanggal_lahir' => '2007-01-01',
            'kelas' => 'XII 1',
            'kamar' => 'BAROKAH 9',
            'status' => 'aktif',
            'tahun_masuk' => 2022,
        ]);

        // 3. Mutakhorijin
        Santri::create([
            'nis' => '3001',
            'nama' => 'Santri Pengabdian',
            'tempat_lahir' => 'Pelaihari',
            'tanggal_lahir' => '2006-01-01',
            'kelas' => 'Mutakhorijin',
            'kamar' => 'AL-MUTTAQIN',
            'status' => 'aktif',
            'tahun_masuk' => 2021,
        ]);

        // 4. Mahasantri
        Santri::create([
            'nis' => '4001',
            'nama' => 'Mahasantri Kampus Luar',
            'tempat_lahir' => 'Barabai',
            'tanggal_lahir' => '2005-01-01',
            'kelas' => 'Mahasantri',
            'kamar' => 'AL-AMIN',
            'status' => 'aktif',
            'tahun_masuk' => 2020,
        ]);

        // 5. Santri Non-aktif (lulus / tidak boleh masuk hitungan santri aktif)
        Santri::create([
            'nis' => '5001',
            'nama' => 'Alumni Lulus',
            'tempat_lahir' => 'Kandangan',
            'tanggal_lahir' => '2004-01-01',
            'kelas' => 'XII 1',
            'kamar' => 'BAROKAH 9',
            'status' => 'lulus',
            'tahun_masuk' => 2019,
        ]);

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Sebaran Jenjang Pendidikan');
        $response->assertSee('Statistik Hunian Asrama');
        $response->assertSee('Santri MTs');
        $response->assertSee('Santri MA');
        $response->assertSee('Mutakhorijin');
        $response->assertSee('Mahasantri');

        // Check view data variables
        $response->assertViewHas('totalSantriAktif', 8);
        $response->assertViewHas('countMts', 3);
        $response->assertViewHas('countMtsVII', 1);
        $response->assertViewHas('countMtsVIII', 1);
        $response->assertViewHas('countMtsIX', 1);

        $response->assertViewHas('countMa', 3);
        $response->assertViewHas('countMaX', 1);
        $response->assertViewHas('countMaXI', 1);
        $response->assertViewHas('countMaXII', 1);

        $response->assertViewHas('countMutakhorijin', 1);
        $response->assertViewHas('countMahasantri', 1);

        // Kamar terisi: AS-SIBYAN (2), BAROKAH 7 (2), BAROKAH 9 (2), AL-MUTTAQIN (1), AL-AMIN (1) => 5 kamar
        $response->assertViewHas('totalKamarTerisi', 5);
        $response->assertViewHas('totalSantriBerasrama', 8);
        $response->assertViewHas('totalSantriTanpaKamar', 0);
    }
}
