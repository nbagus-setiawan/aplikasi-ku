<?php

namespace Tests\Feature;

use App\Models\Pengurus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengurusTest extends TestCase
{
    use RefreshDatabase;

    public function test_secretary_can_view_pengurus_index_and_structure(): void
    {
        $user = User::factory()->create();

        Pengurus::create([
            'nama' => 'Ust. Fauzi',
            'jabatan' => 'Ketua Pondok',
            'divisi' => 'Pimpinan',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($user)->get('/admin/pengurus');

        $response->assertStatus(200);
        $response->assertSee('Ust. Fauzi');
        $response->assertSee('Ketua Pondok');
        $response->assertSee('Pimpinan');
    }

    public function test_secretary_can_create_new_pengurus(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/pengurus', [
            'nama' => 'Ust. Muhammad Zaki',
            'jabatan' => 'Sekretaris II',
            'divisi' => 'Sekretariat',
            'no_hp' => '081234567890',
            'periode_mulai' => '2025',
            'periode_selesai' => '2027',
            'is_aktif' => '1',
        ]);

        $response->assertRedirect('/admin/pengurus');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penguruses', [
            'nama' => 'Ust. Muhammad Zaki',
            'jabatan' => 'Sekretaris II',
            'is_aktif' => 1,
        ]);
    }

    public function test_secretary_can_update_pengurus(): void
    {
        $user = User::factory()->create();

        $pengurus = Pengurus::create([
            'nama' => 'Ust. Ahmad',
            'jabatan' => 'Staf Tata Usaha',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($user)->put("/admin/pengurus/{$pengurus->id}", [
            'nama' => 'Ust. Ahmad M.Pd',
            'jabatan' => 'Kepala Tata Usaha',
            'divisi' => 'Tata Usaha',
            'is_aktif' => '1',
        ]);

        $response->assertRedirect('/admin/pengurus');
        $this->assertDatabaseHas('penguruses', [
            'id' => $pengurus->id,
            'nama' => 'Ust. Ahmad M.Pd',
            'jabatan' => 'Kepala Tata Usaha',
        ]);
    }

    public function test_secretary_can_delete_pengurus(): void
    {
        $user = User::factory()->create();

        $pengurus = Pengurus::create([
            'nama' => 'Ust. Hapus',
            'jabatan' => 'Staf Sementara',
        ]);

        $response = $this->actingAs($user)->delete("/admin/pengurus/{$pengurus->id}");

        $response->assertRedirect('/admin/pengurus');
        $this->assertDatabaseMissing('penguruses', [
            'id' => $pengurus->id,
        ]);
    }

    public function test_secretary_can_create_pengurus_with_photo(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create();
        $file = \Illuminate\Http\UploadedFile::fake()->image('pengurus.jpg');

        $response = $this->actingAs($user)->post('/admin/pengurus', [
            'nama' => 'Ust. Umar Faruq',
            'jabatan' => 'Bendahara Pondok',
            'divisi' => 'Keuangan',
            'foto' => $file,
            'is_aktif' => '1',
        ]);

        $response->assertRedirect('/admin/pengurus');

        $pengurus = Pengurus::where('nama', 'Ust. Umar Faruq')->first();
        $this->assertNotNull($pengurus);
        $this->assertNotNull($pengurus->foto);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($pengurus->foto);
    }

    public function test_secretary_can_update_pengurus_photo_and_old_photo_is_deleted(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $user = User::factory()->create();
        $oldFile = \Illuminate\Http\UploadedFile::fake()->image('old_photo.jpg');
        $oldPath = $oldFile->store('foto-pengurus', 'public');

        $pengurus = Pengurus::create([
            'nama' => 'Ust. Bilal',
            'jabatan' => 'Keamanan',
            'foto' => $oldPath,
            'is_aktif' => true,
        ]);

        $newFile = \Illuminate\Http\UploadedFile::fake()->image('new_photo.jpg');

        $response = $this->actingAs($user)->put("/admin/pengurus/{$pengurus->id}", [
            'nama' => 'Ust. Bilal Al-Habasyi',
            'jabatan' => 'Koordinator Keamanan',
            'foto' => $newFile,
            'is_aktif' => '1',
        ]);

        $response->assertRedirect('/admin/pengurus');

        $pengurus->refresh();
        $this->assertNotEquals($oldPath, $pengurus->foto);
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($oldPath);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($pengurus->foto);
    }

    public function test_secretary_can_export_pengurus_csv(): void
    {
        $user = User::factory()->create();

        Pengurus::create([
            'nama' => 'Ust. Ekspor Pengurus',
            'jabatan' => 'Ketua Pengurus',
            'divisi' => 'Pimpinan',
            'no_hp' => '081234567890',
            'periode_mulai' => '2026',
            'periode_selesai' => '2030',
            'is_aktif' => true,
        ]);

        $response = $this->actingAs($user)->get(route('admin.pengurus.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('data-pengurus-', (string) $response->headers->get('content-disposition'));
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('Ust. Ekspor Pengurus', $content);
        $this->assertStringContainsString('Ketua Pengurus', $content);
        $this->assertStringContainsString('Pimpinan', $content);
    }

    public function test_secretary_can_download_pengurus_template_csv(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.pengurus.template'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('template-impor-pengurus.csv', (string) $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('nama', $content);
        $this->assertStringContainsString('jabatan', $content);
        $this->assertStringContainsString('divisi', $content);
    }

    public function test_secretary_can_import_pengurus_csv_with_valid_and_invalid_rows(): void
    {
        $user = User::factory()->create();

        $csvContent = "\xEF\xBB\xBF" .
            "nama,jabatan,divisi,no_hp,periode_mulai,periode_selesai,is_aktif\n" .
            "Ust. Impor Satu,Sekretaris,Sekretariat,081211112222,2026,2030,aktif\n" .
            ",Bendahara,Keuangan,081233334444,2026,2030,aktif\n" . // Baris gagal: nama kosong
            "Ust. Impor Dua,Keamanan,Keamanan,081255556666,2026,2030,nonaktif\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('pengurus.csv', $csvContent);

        $response = $this->actingAs($user)->post(route('admin.pengurus.impor'), [
            'file' => $file,
        ]);

        $response->assertRedirect(route('admin.pengurus.index'));
        $response->assertSessionHas('import_result');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('penguruses', [
            'nama' => 'Ust. Impor Satu',
            'jabatan' => 'Sekretaris',
            'is_aktif' => true,
        ]);

        $this->assertDatabaseHas('penguruses', [
            'nama' => 'Ust. Impor Dua',
            'jabatan' => 'Keamanan',
            'is_aktif' => false,
        ]);

        $result = session('import_result');
        $this->assertEquals(2, $result['sukses']);
        $this->assertEquals(1, $result['gagal']);
    }
}
