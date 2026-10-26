<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Halaman publik beranda dapat diakses dengan sukses.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    /**
     * Memastikan 7 URL crawler warisan dialihkan permanen (301) ke beranda untuk validasi Google Search Console.
     */
    public function test_rute_warisan_crawler_dialihkan_301_ke_beranda(): void
    {
        $urls = ['/cetak', '/template', '/backup', '/pdf', '/unduh', '/export', '/download'];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(301);
            $response->assertRedirect('/');
        }
    }

    /**
     * Memastikan parameter template search_term_string dialihkan 301 ke URL kanonikal bersih.
     */
    public function test_parameter_template_search_term_string_dialihkan_301(): void
    {
        $response = $this->get('/pengumuman?kategori=santri&q={search_term_string}');
        $response->assertStatus(301);
        $response->assertRedirect('/pengumuman?kategori=santri');

        $responseClean = $this->get('/pengumuman?q={search_term_string}');
        $responseClean->assertStatus(301);
        $responseClean->assertRedirect(route('publik.pengumuman.index'));
    }
}
