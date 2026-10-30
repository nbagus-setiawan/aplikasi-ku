<?php

namespace Tests\Feature;

use App\Models\Halaman;
use App\Models\Pengumuman;
use App\Models\User;
use App\Services\HalamanBlockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Pengujian halaman informasi publik dan SEO (PRD 6.12).
 */
class HalamanPublikSeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_informasi_bisa_diakses_tamu_dengan_meta_seo(): void
    {
        $halamans = [
            'profil' => ['/profil', 'Profil Pondok Pesantren Al-Falah Putak'],
            'program' => ['/program-pendidikan', 'Program Pendidikan Pondok Pesantren Al-Falah Putak'],
            'pendaftaran' => ['/pendaftaran-santri', 'Pendaftaran Santri Baru Pondok Pesantren Al-Falah Putak'],
            'kontak' => ['/kontak', 'Kontak dan Lokasi Pondok Pesantren Al-Falah Putak'],
        ];

        foreach ($halamans as [$url, $h1]) {
            $response = $this->get($url);

            $response->assertOk();
            $response->assertSee($h1);
            $response->assertSee('<link rel="canonical" href="' . url($url) . '">', false);
            $response->assertSee('<meta name="description"', false);
            $response->assertDontSee('<meta name="keywords"', false);
            $this->assertSame(1, substr_count($response->getContent(), '<h1'), "Halaman {$url} harus memiliki tepat satu H1.");
        }
    }

    public function test_isi_profil_mengikuti_blok_beranda_yang_terbit(): void
    {
        $user = User::factory()->create();
        $blocks = app(HalamanBlockService::class)->getDefaultBerandaBlocks();
        foreach ($blocks as &$block) {
            if ($block['type'] === 'profil') {
                $block['data']['visi'] = 'Visi uji yang diubah sekretaris lewat editor';
            }
        }

        Halaman::create([
            'slug' => 'beranda',
            'judul' => 'Beranda',
            'konten' => $blocks,
            'status' => 'terbit',
            'is_beranda' => true,
            'published_at' => now(),
            'user_id' => $user->id,
        ]);

        $this->get('/profil')->assertOk()->assertSee('Visi uji yang diubah sekretaris lewat editor');
    }

    public function test_pendaftaran_menampilkan_pengumuman_pendaftaran(): void
    {
        $user = User::factory()->create();
        Pengumuman::create([
            'judul' => 'Pendaftaran Santri Baru Tahun Ajaran Baru',
            'slug' => 'pendaftaran-santri-baru-uji',
            'isi' => 'Isi pengumuman pendaftaran.',
            'kategori' => 'kegiatan',
            'tgl_tayang' => now()->subDay(),
            'status' => 'terbit',
            'user_id' => $user->id,
        ]);

        $this->get('/pendaftaran-santri')
            ->assertOk()
            ->assertSee('Pendaftaran Santri Baru Tahun Ajaran Baru')
            ->assertSee(route('publik.pengumuman.show', 'pendaftaran-santri-baru-uji'), false);
    }

    public function test_sitemap_dinamis_memuat_halaman_publik_dan_pengumuman(): void
    {
        $user = User::factory()->create();
        Pengumuman::create([
            'judul' => 'Warta Uji Sitemap',
            'slug' => 'warta-uji-sitemap',
            'isi' => 'Isi warta.',
            'kategori' => 'kegiatan',
            'tgl_tayang' => now()->subDay(),
            'status' => 'terbit',
            'user_id' => $user->id,
        ]);

        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $this->assertStringContainsString('application/xml', $response->headers->get('Content-Type'));
        $this->assertNull($response->headers->get('X-Robots-Tag'));

        $xml = simplexml_load_string($response->getContent());
        $this->assertNotFalse($xml, 'Sitemap harus berupa XML yang valid.');

        $isi = $response->getContent();
        foreach (['/profil', '/program-pendidikan', '/pendaftaran-santri', '/kontak', '/pengumuman', '/pengumuman/warta-uji-sitemap'] as $path) {
            $this->assertStringContainsString($path . '</loc>', $isi);
        }

        $this->assertFileDoesNotExist(public_path('sitemap.xml'), 'Sitemap tidak boleh ditulis sebagai berkas statis.');
    }

    public function test_navigasi_publik_memuat_tautan_halaman_baru(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        foreach (['/profil', '/program-pendidikan', '/pendaftaran-santri', '/kontak'] as $path) {
            $response->assertSee('href="' . url($path) . '"', false);
        }
    }
}

