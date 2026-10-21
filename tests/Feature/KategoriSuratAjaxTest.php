<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KategoriSuratAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_secretary_can_create_kategori_surat_via_ajax(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('admin.kategori-surat.store'), [
                'nama' => 'Surat Rekomendasi Umrah',
                'kode' => 'SRU.23',
                'keterangan' => 'Rekomendasi ibadah umrah santri/asatidz',
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'nama' => 'Surat Rekomendasi Umrah',
                    'kode' => 'SRU.23',
                ],
            ]);

        $this->assertDatabaseHas('kategori_surats', [
            'nama' => 'Surat Rekomendasi Umrah',
            'kode' => 'SRU.23',
        ]);
    }
}
