<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class SekretarisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Kredensial akun Sekretaris diambil dari file .env (atau database/seeders/sekretaris.local.php)
     * yang masuk dalam .gitignore agar email & password asli TIDAK PERNAH terpublikasi ke GitHub.
     */
    public function run(): void
    {
        $localFile = __DIR__ . '/sekretaris.local.php';
        $localConfig = is_file($localFile) ? (array) require $localFile : [];

        $name = $localConfig['name'] ?? env('SEKRETARIS_NAME', 'Sekretaris Al-Falah Putra');
        $email = $localConfig['email'] ?? env('SEKRETARIS_EMAIL');
        $password = $localConfig['password'] ?? env('SEKRETARIS_PASSWORD');

        if (app()->environment('production') && (empty($email) || empty($password))) {
            throw new RuntimeException(
                'Kredensial akun Sekretaris belum diatur! Silakan isi SEKRETARIS_EMAIL dan SEKRETARIS_PASSWORD di file .env terlebih dahulu.'
            );
        }

        // Fallback khusus lingkungan pengujian otomatis (testing/local) jika .env belum diisi
        $email = !empty($email) ? $email : 'sekretaris@alfalahputak.test';
        $password = !empty($password) ? $password : 'password-lokal-testing';

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );
    }
}
