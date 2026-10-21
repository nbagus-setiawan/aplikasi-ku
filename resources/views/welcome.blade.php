<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ \App\Models\Pengaturan::get('nama_situs', 'SAP | SEKRETARIS AL-FALAH PUTRA') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-50 text-gray-800 antialiased font-sans min-h-screen flex flex-col justify-between">
        <!-- Top Navbar Publik -->
        <header class="bg-emerald-900 text-white shadow-md sticky top-0 z-30">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <a href="{{ url('/') }}" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-600 flex items-center justify-center font-bold text-white text-lg shadow">
                        SAP
                    </div>
                    <div class="leading-tight">
                        <span class="block font-bold text-sm tracking-wide">PONDOK PESANTREN AL FALAH PUTRA</span>
                        <span class="block text-xs text-emerald-300">Sistem Administrasi Sekretariat</span>
                    </div>
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                            <span>Panel Admin</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-white text-emerald-900 hover:bg-emerald-50 text-xs font-semibold rounded-lg shadow-sm transition">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span>Login Sekretaris</span>
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex-1">
            <section class="bg-gradient-to-b from-emerald-900 to-emerald-800 text-white py-16 sm:py-24 px-4 text-center">
                <div class="max-w-3xl mx-auto space-y-5">
                    <span class="inline-block px-3.5 py-1 bg-emerald-700/60 text-emerald-200 text-xs font-medium rounded-full border border-emerald-600/50">
                        {{ \App\Models\Pengaturan::get('nama_lembaga', 'Pondok Pesantren Al Falah Putra') }}
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                        Sistem Administrasi Pesantren
                    </h1>
                    <p class="text-sm sm:text-base text-emerald-100/90 leading-relaxed max-w-2xl mx-auto">
                        Pusat layanan persuratan resmi, data induk santri, administrasi pengurus, serta publikasi agenda Pondok Pesantren Al Falah Putra di bawah naungan Pondok Pesantren Al-Falah Putak, Desa Putak, Kec. Gelumbang, Kab. Muara Enim.
                    </p>
                    <div class="pt-4 flex flex-wrap justify-center gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}"
                               class="px-6 py-3 bg-white text-emerald-900 font-bold text-sm rounded-xl shadow-md hover:bg-emerald-50 transition">
                                Buka Dashboard Sekretaris
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl shadow-md transition">
                                Masuk ke Panel Sekretariat
                            </a>
                        @endauth
                    </div>
                </div>
            </section>

            <!-- Fitur / Layanan Publik -->
            <section class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base mb-1">Verifikasi Surat Resmi</h3>
                        <p class="text-xs text-gray-500 leading-relaxed mb-3">
                            Setiap surat keluar resmi dari Al Falah Putra dilengkapi QR Code validasi keaslian dokumen tanda tangan digital dan basah.
                        </p>
                        <span class="text-[11px] font-semibold text-emerald-700">Validasi Terpusat</span>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-blue-50 text-blue-700 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base mb-1">Pengumuman & Maklumat</h3>
                        <p class="text-xs text-gray-500 leading-relaxed mb-3">
                            Informasi maklumat resmi, jadwal kepulangan/kedatangan santri, dan edaran kepengurusan pondok pesantren.
                        </p>
                        <span class="text-[11px] font-semibold text-blue-700">Informasi Resmi</span>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition">
                        <div class="w-12 h-12 bg-amber-50 text-amber-700 rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="font-bold text-gray-900 text-base mb-1">Agenda Pondok</h3>
                        <p class="text-xs text-gray-500 leading-relaxed mb-3">
                            Jadwal kegiatan belajar mengajar, pengajian kitab kuning, haul akbar, dan agenda tahunan pesantren Al Falah Putra.
                        </p>
                        <span class="text-[11px] font-semibold text-amber-700">Jadwal Terintegrasi</span>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-emerald-950 text-emerald-200 py-8 border-t border-emerald-900 text-xs">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="text-center sm:text-left">
                    <p class="font-bold text-white text-sm">{{ \App\Models\Pengaturan::get('kop_nama', 'PONDOK PESANTREN AL FALAH PUTRA') }}</p>
                    <p class="text-emerald-300/80 mt-0.5">{{ \App\Models\Pengaturan::get('kop_alamat', 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171') }}</p>
                    <p class="text-emerald-300/80">{{ \App\Models\Pengaturan::get('kop_kontak', 'Telp: 0812-7890-3117 | Email: sekretariat@alfalahputra.sch.id') }}</p>
                </div>
                <div class="text-center sm:text-right">
                    <p class="text-emerald-400 font-semibold">{{ \App\Models\Pengaturan::get('nama_situs', 'SAP | SEKRETARIS AL-FALAH PUTRA') }}</p>
                    <p class="text-emerald-500/70 text-[11px]">&copy; {{ date('Y') }} Hak Cipta Dilindungi.</p>
                </div>
            </div>
        </footer>
    </body>
</html>
