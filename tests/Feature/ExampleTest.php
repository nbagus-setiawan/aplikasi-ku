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
}
