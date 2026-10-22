<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Services\ExtensionMimeTypeGuesser;
use Symfony\Component\Mime\MimeTypes;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Daftarkan guesser mandiri agar upload berkas tidak error saat php_fileinfo nonaktif di hosting
        MimeTypes::getDefault()->registerGuesser(new ExtensionMimeTypeGuesser());
    }
}
