<?php

namespace Tests\Feature;

use App\Models\Halaman;
use App\Models\HalamanRevisi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalamanTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'email' => 'sekretaris@alfalahputra.test',
        ]);
    }

    public function test_tamu_tidak_bisa_mengakses_editor_beranda()
    {
        $response = $this->get(route('admin.halaman.beranda'));
        $response->assertRedirect(route('login'));
    }

    public function test_sekretaris_bisa_membuka_editor_beranda()
    {
        $response = $this->actingAs($this->user)->get(route('admin.halaman.beranda'));
        $response->assertStatus(200);
        $response->assertSee('Editor Visual Beranda');
    }

    public function test_sekretaris_bisa_menyimpan_draf_beranda()
    {
        $payload = [
            'judul' => 'Beranda Utama Pondok',
            'status' => 'draft',
            'catatan_revisi' => 'Simpan draf awal',
            'blocks' => [
                [
                    'id' => 'hero_custom',
                    'type' => 'hero',
                    'data' => [
                        'judul' => 'Judul Hero Draf',
                        'subjudul' => 'Subjudul Hero Draf',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.halaman.beranda.update'), $payload);
        $response->assertRedirect(route('admin.halaman.beranda'));
        $response->assertSessionHas('success');

        $halaman = Halaman::beranda()->first();
        $this->assertNotNull($halaman);
        $this->assertEquals('draft', $halaman->status);
        $this->assertEquals('hero', $halaman->konten[0]['type']);
        $this->assertEquals('Judul Hero Draf', $halaman->konten[0]['data']['judul']);
    }

    public function test_sekretaris_bisa_menerbitkan_beranda()
    {
        $payload = [
            'judul' => 'Beranda Resmi',
            'status' => 'terbit',
            'blocks' => [
                [
                    'id' => 'hero_1',
                    'type' => 'hero',
                    'data' => [
                        'judul' => 'Selamat Datang Santri Baru',
                        'subjudul' => 'Generasi Emas Al-Falah',
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->user)->post(route('admin.halaman.beranda.update'), $payload);
        $response->assertRedirect(route('admin.halaman.beranda'));

        $halaman = Halaman::beranda()->first();
        $this->assertEquals('terbit', $halaman->status);
        $this->assertNotNull($halaman->published_at);
    }

    public function test_konten_blok_disanitasi_dari_skrip_berbahaya()
    {
        $payload = [
            'judul' => 'Judul <script>alert("xss")</script>Bersih',
            'status' => 'terbit',
            'blocks' => [
                [
                    'id' => 'hero_xss',
                    'type' => 'hero',
                    'data' => [
                        'judul' => 'Header <script>alert("hack")</script>Aman',
                        'subjudul' => 'Subjudul <img src=x onerror=alert(1)>bersih',
                    ],
                ],
            ],
        ];

        $this->actingAs($this->user)->post(route('admin.halaman.beranda.update'), $payload);

        $halaman = Halaman::beranda()->first();
        $this->assertStringNotContainsString('<script>', $halaman->judul);
        $this->assertStringNotContainsString('<script>', $halaman->konten[0]['data']['judul']);
        $this->assertStringNotContainsString('onerror', $halaman->konten[0]['data']['subjudul']);
    }

    public function test_riwayat_revisi_tersimpan_dan_bisa_dipulihkan()
    {
        // 1. Simpan versi awal
        $this->actingAs($this->user)->post(route('admin.halaman.beranda.update'), [
            'judul' => 'Versi Pertama',
            'status' => 'terbit',
            'blocks' => [
                ['id' => 'hero_v1', 'type' => 'hero', 'data' => ['judul' => 'Versi 1']],
            ],
        ]);

        $halaman = Halaman::beranda()->first();

        // 2. Simpan versi kedua (seharusnya memicu pembentukan revisi dari versi 1)
        $this->actingAs($this->user)->post(route('admin.halaman.beranda.update'), [
            'judul' => 'Versi Kedua',
            'status' => 'terbit',
            'catatan_revisi' => 'Update ke versi 2',
            'blocks' => [
                ['id' => 'hero_v2', 'type' => 'hero', 'data' => ['judul' => 'Versi 2']],
            ],
        ]);

        $revisi = HalamanRevisi::where('halaman_id', $halaman->id)->latest()->first();
        $this->assertNotNull($revisi);

        // 3. Pulihkan revisi versi 1
        $restoreResp = $this->actingAs($this->user)->post(route('admin.halaman.revisi.restore', [$halaman->id, $revisi->id]));
        $restoreResp->assertRedirect(route('admin.halaman.beranda'));

        $halaman->refresh();
        $this->assertEquals('Versi 1', $halaman->konten[0]['data']['judul']);
    }

    public function test_halaman_publik_beranda_menampilkan_blok_terbit()
    {
        Halaman::create([
            'slug' => 'beranda',
            'judul' => 'Beranda Publik',
            'konten' => [
                [
                    'id' => 'hero_pub',
                    'type' => 'hero',
                    'data' => [
                        'judul' => 'Pondok Al-Falah Putra Unggul',
                        'subjudul' => 'Pusat Keilmuan Islam',
                    ],
                ],
            ],
            'status' => 'terbit',
            'is_beranda' => true,
            'published_at' => now(),
            'user_id' => $this->user->id,
        ]);

        $response = $this->get(route('home'));
        $response->assertStatus(200);
        $response->assertSee('Pondok Al-Falah Putra Unggul');
        $response->assertSee('Pusat Keilmuan Islam');
        $response->assertSee('@2026 JAKASTRA OFFICIAL. All Reght Reserved');
        $response->assertSee('08.00 - 16.00 WIB');
    }

    public function test_halaman_layanan_konsultasi_dapat_diakses_publik_dan_memiliki_elemen_lengkap()
    {
        $response = $this->get(route('publik.konsultasi'));
        $response->assertStatus(200);
        $response->assertSee('Layanan Konsultasi');
        $response->assertSee('Konsultasikan Sekarang');
        $response->assertSee('Topik Konsultasi');
        $response->assertSee('Senin - Kamis');
        $response->assertSee('08.00 - 16.00 WIB');
        $response->assertSee('Sabtu - Minggu');
        $response->assertSee('07.30 - 15.00 WIB');
        $response->assertSee('@2026 JAKASTRA OFFICIAL. All Reght Reserved');
    }

    public function test_footer_halaman_selain_beranda_menampilkan_footer_minimalis()
    {
        $response = $this->get(route('publik.konsultasi'));
        $response->assertStatus(200);
        $response->assertSee('@2026 JAKASTRA OFFICIAL. All Reght Reserved');
        $response->assertDontSee('Layanan &amp; Waktu', false);
    }
}
