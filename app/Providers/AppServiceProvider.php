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

        // Daftarkan binding rute untuk model SuratUks
        \Illuminate\Support\Facades\Route::model('suratUk', \App\Models\SuratUks::class);
        \Illuminate\Support\Facades\Route::model('surat_uk', \App\Models\SuratUks::class);
    }
}
