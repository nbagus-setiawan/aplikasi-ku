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
}
