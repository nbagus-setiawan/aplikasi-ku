<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Otomatisasi tanda Alfa presensi sholat korlap setiap 10 menit
Schedule::command('absensi:auto-alfa')->everyTenMinutes();

