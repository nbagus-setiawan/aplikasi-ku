<?php

namespace Tests\Feature;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class SantriImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_secretary_can_download_import_template(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/santri-impor/template');

        $response->assertStatus(200);
        $this->assertTrue(str_contains($response->headers->get('content-disposition'), 'template-impor-santri.csv'));

        // Pastikan header kolom sesuai standar PRD
        $content = $response->streamedContent();
        $this->assertStringContainsString('nis,nama,tempat_lahir,tanggal_lahir,alamat,nama_wali,hp_wali,kamar,kelas,tahun_masuk,status', $content);
    }

    public function test_secretary_can_import_valid_santri_rows(): void
    {
        $user = User::factory()->create();

        $csvContent = "nis,nama,tempat_lahir,tanggal_lahir,alamat,nama_wali,hp_wali,kamar,kelas,tahun_masuk,status\n"
                    . "2026501,Imam Syafi'i,Gelumbang,2010-03-01,Desa Putak,H. Zaini,081234567891,A1,1 Ula,2026,aktif\n"
                    . "2026502,Malik Ibrahim,Muara Enim,2010-04-02,Jl. Melati,H. Wahab,081234567892,A2,1 Ula,2026,aktif\n";

        $file = UploadedFile::fake()->createWithContent('santri.csv', $csvContent);

        $response = $this->actingAs($user)->post('/admin/santri-impor', [
            'file' => $file,
        ]);

        $response->assertRedirect('/admin/santri');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('santris', [
            'nis' => '2026501',
            'nama' => "Imam Syafi'i",
        ]);

        $this->assertDatabaseHas('santris', [
            'nis' => '2026502',
            'nama' => 'Malik Ibrahim',
        ]);
    }

    public function test_import_reports_failed_rows_and_inserts_valid_ones(): void
    {
        $user = User::factory()->create();

        // Siapkan santri yang sudah ada agar terjadi kasus NIS duplikat
        Santri::create([
            'nis' => '2026777',
            'nama' => 'Santri Sudah Ada',
            'status' => 'aktif',
        ]);

        // Baris 1: valid
        // Baris 2: NIS kosong (gagal)
        // Baris 3: NIS duplikat 2026777 (gagal)
        // Baris 4: valid
        $csvContent = "nis,nama,tempat_lahir,tanggal_lahir,alamat,nama_wali,hp_wali,kamar,kelas,tahun_masuk,status\n"
                    . "2026801,Santri Sukses Satu,Gelumbang,2010-01-01,Alamat 1,Wali 1,08123,K1,1 Ula,2026,aktif\n"
                    . ",Santri Tanpa NIS,Gelumbang,2010-01-01,Alamat 2,Wali 2,08124,K1,1 Ula,2026,aktif\n"
                    . "2026777,Santri Duplikat,Gelumbang,2010-01-01,Alamat 3,Wali 3,08125,K1,1 Ula,2026,aktif\n"
                    . "2026802,Santri Sukses Dua,Gelumbang,2010-01-01,Alamat 4,Wali 4,08126,K2,1 Ula,2026,aktif\n";

        $file = UploadedFile::fake()->createWithContent('santri_campur.csv', $csvContent);

        $response = $this->actingAs($user)->post('/admin/santri-impor', [
            'file' => $file,
        ]);

        $response->assertRedirect('/admin/santri');

        // Periksa laporan hasil impor pada session
        $result = session('import_result');
        $this->assertNotNull($result);
        $this->assertEquals(2, $result['sukses']);
        $this->assertEquals(2, $result['gagal']);
        $this->assertCount(2, $result['gagal_list']);

        // Pastikan baris yang valid berhasil disimpan
        $this->assertDatabaseHas('santris', ['nis' => '2026801']);
        $this->assertDatabaseHas('santris', ['nis' => '2026802']);
    }
}
