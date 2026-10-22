<?php

require_once __DIR__ . '/finfo_polyfill.php';

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(function (\Illuminate\Http\Exceptions\PostTooLargeException $e, Request $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Ukuran berkas yang diunggah terlalu besar melebihi batas maksimal server (5 MB).',
                ], 413);
            }

            return back()->with('error', 'Ukuran berkas yang Anda unggah terlalu besar melebihi batas maksimal server (5 MB). Silakan perkecil atau kompres berkas Anda terlebih dahulu.');
        });
    })->create();
