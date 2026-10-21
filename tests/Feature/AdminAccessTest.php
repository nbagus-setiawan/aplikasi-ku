<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_from_admin_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_guests_are_redirected_from_admin_pengaturan(): void
    {
        $response = $this->get('/admin/pengaturan');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_secretary_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Sekretariat');
    }

    public function test_authenticated_secretary_can_access_admin_pengaturan(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/pengaturan');

        $response->assertStatus(200);
        $response->assertSee('Pengaturan Sistem');
    }
}
