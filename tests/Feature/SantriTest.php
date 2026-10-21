<?php

namespace Tests\Feature;

use App\Models\Santri;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SantriTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_secretary_can_view_santri_index(): void
    {
        $user = User::factory()->create();
        Santri::create([
            'nis' => '2026001',
            'nama' => 'Santri Percobaan',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get('/admin/santri');

        $response->assertStatus(200);
        $response->assertSee('2026001');
        $response->assertSee('Santri Percobaan');
    }

    public function test_secretary_can_create_new_santri(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/santri', [
            'nis' => '2026100',
            'nama' => 'Ahmad Santri Baru',
            'tempat_lahir' => 'Gelumbang',
            'tanggal_lahir' => '2010-01-01',
            'status' => 'aktif',
            'kelas' => '1 Ula',
            'kamar' => 'Abu Bakar 01',
            'nama_wali' => 'H. Abdullah',
            'hp_wali' => '081234567890',
        ]);

        $response->assertRedirect('/admin/santri');
        $this->assertDatabaseHas('santris', [
            'nis' => '2026100',
            'nama' => 'Ahmad Santri Baru',
        ]);
    }

    public function test_santri_nis_must_be_unique(): void
    {
        $user = User::factory()->create();

        Santri::create([
            'nis' => '2026100',
            'nama' => 'Santri Lama',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->post('/admin/santri', [
            'nis' => '2026100',
            'nama' => 'Santri Baru Duplikat',
            'status' => 'aktif',
        ]);

        $response->assertSessionHasErrors(['nis']);
    }

    public function test_secretary_can_update_santri(): void
    {
        $user = User::factory()->create();

        $santri = Santri::create([
            'nis' => '2026200',
            'nama' => 'Santri Awal',
            'status' => 'aktif',
            'kelas' => '1 Ula',
        ]);

        $response = $this->actingAs($user)->put("/admin/santri/{$santri->id}", [
            'nis' => '2026200',
            'nama' => 'Santri Terupdate',
            'status' => 'lulus',
            'kelas' => '3 Ulya',
        ]);

        $response->assertRedirect('/admin/santri');
        $this->assertDatabaseHas('santris', [
            'id' => $santri->id,
            'nama' => 'Santri Terupdate',
            'status' => 'lulus',
            'kelas' => '3 Ulya',
        ]);
    }

    public function test_secretary_can_soft_delete_santri(): void
    {
        $user = User::factory()->create();

        $santri = Santri::create([
            'nis' => '2026300',
            'nama' => 'Santri Dihapus',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->delete("/admin/santri/{$santri->id}");

        $response->assertRedirect('/admin/santri');

        // Pastikan masih ada di database (soft delete) tapi deleted_at terisi
        $this->assertSoftDeleted('santris', [
            'id' => $santri->id,
            'nis' => '2026300',
        ]);
    }

    public function test_secretary_can_filter_and_search_santri(): void
    {
        $user = User::factory()->create();

        Santri::create([
            'nis' => '2026001',
            'nama' => 'Ali Gelumbang',
            'status' => 'aktif',
            'kelas' => '1 Ula',
            'kamar' => 'A1',
        ]);

        Santri::create([
            'nis' => '2026002',
            'nama' => 'Budi Muara Enim',
            'status' => 'lulus',
            'kelas' => '2 Wustho',
            'kamar' => 'B2',
        ]);

        // Cari 'Ali'
        $response = $this->actingAs($user)->get('/admin/santri?q=Ali');
        $response->assertSee('Ali Gelumbang');
        $response->assertDontSee('Budi Muara Enim');

        // Filter status 'lulus'
        $response2 = $this->actingAs($user)->get('/admin/santri?status=lulus');
        $response2->assertSee('Budi Muara Enim');
        $response2->assertDontSee('Ali Gelumbang');
    }

    public function test_secretary_can_print_santri_list_pdf(): void
    {
        $user = User::factory()->create();

        Santri::create([
            'nis' => '2026001',
            'nama' => 'Santri Cetak List',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get('/admin/santri/cetak');

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }

    public function test_secretary_can_print_santri_biodata_pdf(): void
    {
        $user = User::factory()->create();

        $santri = Santri::create([
            'nis' => '2026001',
            'nama' => 'Santri Cetak Biodata',
            'status' => 'aktif',
        ]);

        $response = $this->actingAs($user)->get("/admin/santri/{$santri->id}/cetak");

        $response->assertStatus(200);
        $this->assertEquals('application/pdf', $response->headers->get('content-type'));
    }
}
