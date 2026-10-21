<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Tests\TestCase;

class PostTooLargeExceptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_post_too_large_exception_redirects_back_with_friendly_error_message(): void
    {
        $user = User::factory()->create();

        // Simulasikan request yang melempar PostTooLargeException
        $this->actingAs($user);

        // Uji callback penanganan exception di bootstrap/app.php
        $request = \Illuminate\Http\Request::create('/admin/pengumuman/1', 'POST');
        $exception = new PostTooLargeException('Data POST terlalu besar.');

        $handler = app(\Illuminate\Contracts\Debug\ExceptionHandler::class);
        $response = $handler->render($request, $exception);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertTrue(session()->has('error'));
        $this->assertStringContainsString('Ukuran berkas yang Anda unggah terlalu besar', session('error'));
    }

    public function test_post_too_large_exception_returns_json_when_requested(): void
    {
        $request = \Illuminate\Http\Request::create('/api/test', 'POST');
        $request->headers->set('Accept', 'application/json');
        $exception = new PostTooLargeException('Data POST terlalu besar.');

        $handler = app(\Illuminate\Contracts\Debug\ExceptionHandler::class);
        $response = $handler->render($request, $exception);

        $this->assertEquals(413, $response->getStatusCode());
        $this->assertStringContainsString('Ukuran berkas yang diunggah terlalu besar', $response->getContent());
    }
}
