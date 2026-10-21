<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\Pengaturan::get('nama_situs', 'SAP | SEKRETARIS AL-FALAH PUTRA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-slate-100">
        <x-page-loader />

        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <div class="text-center mb-4">
                <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-20 w-auto object-contain drop-shadow-sm">
                    <div class="mt-1">
                        <span class="block font-bold text-gray-900 text-base tracking-wide uppercase">Pondok Pesantren Al Falah Putra</span>
                        <span class="block text-xs font-semibold text-emerald-700">Sistem Administrasi Sekretariat</span>
                    </div>
                </a>
            </div>

            <div class="w-full sm:max-w-md bg-white shadow-xl border border-gray-100 overflow-hidden rounded-2xl p-6 sm:p-8">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center text-xs text-gray-400">
                &copy; {{ date('Y') }} {{ \App\Models\Pengaturan::get('nama_lembaga', 'Pondok Pesantren Al Falah Putra') }}
            </div>
        </div>
    </body>
</html>
