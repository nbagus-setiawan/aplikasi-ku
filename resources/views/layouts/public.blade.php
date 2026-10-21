<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Pondok Pesantren Al-Falah Putra') — {{ $pengaturan['nama_instansi'] ?? 'SAP Al-Falah Putra' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-slate-800 bg-slate-50 min-h-screen flex flex-col justify-between selection:bg-emerald-600 selection:text-white">
    <x-page-loader />

    <!-- Navbar Publik Responsif (Desktop & Mobile) -->
    <header x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-emerald-950/10 shadow-xs transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo & Identitas Lembaga -->
                <a href="{{ url('/') }}" class="flex items-center gap-3.5 group py-1">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 border border-emerald-100 flex items-center justify-center p-1.5 shadow-xs shrink-0 group-hover:scale-105 transition-transform duration-200">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo Pondok Pesantren Al-Falah Putra" class="w-full h-full object-contain">
                    </div>
                    <div class="leading-tight">
                        <span class="block text-base sm:text-lg font-extrabold text-emerald-950 tracking-tight group-hover:text-emerald-700 transition">
                            {{ $pengaturan['nama_instansi'] ?? 'AL-FALAH PUTRA' }}
                        </span>
                        <span class="block text-[11px] sm:text-xs font-semibold text-emerald-700">
                            Pondok Pesantren Salafiyah Al-Falah Putak
                        </span>
                    </div>
                </a>

                <!-- Navigasi Desktop -->
                <nav class="hidden md:flex items-center gap-1.5">
                    <a href="{{ url('/') }}"
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->is('/') ? 'text-emerald-800 bg-emerald-50 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-slate-100/70' }}">
                        Beranda
                    </a>
                    <a href="{{ url('/pengumuman') }}"
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->is('pengumuman*') ? 'text-emerald-800 bg-emerald-50 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-slate-100/70' }}">
                        Pengumuman
                    </a>
                    <a href="{{ url('/verifikasi') }}"
                       class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ request()->is('verifikasi*') ? 'text-emerald-800 bg-emerald-50 font-bold shadow-xs' : 'text-slate-600 hover:text-emerald-800 hover:bg-slate-100/70' }}">
                        Verifikasi Dokumen
                    </a>
                </nav>

                <!-- Tombol Aksi Kanan (Desktop) -->
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm hover:shadow transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Panel Admin</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-emerald-900 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span>Masuk Sekretariat</span>
                        </a>
                    @endauth
                </div>

                <!-- Tombol Hamburger Mobile -->
                <div class="flex md:hidden items-center">
                    <button type="button"
                            @click="mobileOpen = !mobileOpen"
                            class="p-2.5 rounded-xl text-slate-700 hover:text-emerald-800 hover:bg-emerald-50 transition border border-slate-200/80 focus:outline-none"
                            aria-label="Buka Menu Navigasi"
                            :aria-expanded="mobileOpen">
                        <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Panel Menu Mobile Dropdown (Sentuhan Halus & Rapi) -->
            <div x-show="mobileOpen"
                 x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-3"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-3"
                 class="md:hidden py-4 border-t border-slate-100 space-y-1">
                <a href="{{ url('/') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->is('/') ? 'text-emerald-900 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Beranda Utama</span>
                </a>
                <a href="{{ url('/pengumuman') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->is('pengumuman*') ? 'text-emerald-900 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                    <span>Pengumuman & Warta</span>
                </a>
                <a href="{{ url('/verifikasi') }}"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition {{ request()->is('verifikasi*') ? 'text-emerald-900 bg-emerald-50' : 'text-slate-700 hover:bg-slate-100/70' }}">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    <span>Verifikasi Keaslian Surat</span>
                </a>

                <div class="pt-3 pb-1 border-t border-slate-100">
                    @auth
                        <a href="{{ route('dashboard') }}" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-white bg-emerald-700 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            <span>Buka Panel Admin</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-sm font-bold text-emerald-950 bg-emerald-50 border border-emerald-200">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                            <span>Masuk Portal Sekretariat</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Konten Utama Halaman -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer Publik yang Berwibawa & Rapi -->
    <footer class="bg-emerald-950 text-slate-300 pt-16 pb-12 border-t border-emerald-900/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 pb-12 border-b border-emerald-900">
                <!-- Kolom 1: Profil & Identitas Lembaga -->
                <div class="md:col-span-6 space-y-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-emerald-900/80 border border-emerald-700/50 flex items-center justify-center p-1.5 shrink-0 shadow-sm">
                            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-full h-full object-contain">
                        </div>
                        <div>
                            <span class="text-lg font-bold text-white tracking-wide block">
                                {{ $pengaturan['nama_instansi'] ?? 'Pondok Pesantren Al-Falah Putra' }}
                            </span>
                            <span class="text-xs text-emerald-400 font-semibold block">
                                Unit Santri Putra — Pondok Pesantren Al-Falah Putak
                            </span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-300/90 leading-relaxed max-w-lg">
                        Lembaga pendidikan Islam berlandaskan Al-Qur'an dan Sunnah menurut pemahaman Ahlussunnah wal Jama'ah. Berdiri pada 5 Maret 2005 (10 Muharram) oleh K.H. Mursyidi dan Ny. Hj. Aniswatuzzuhriyah di Desa Putak, Kec. Gelumbang, Kab. Muara Enim.
                    </p>
                    <div class="pt-2 text-xs text-slate-300 space-y-2">
                        <p class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                            <span>{{ $pengaturan['alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171' }}</span>
                        </p>
                        <p class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <span>{{ $pengaturan['kontak'] ?? '0851-8320-1627' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Kolom 2: Tautan Navigasi Cepat -->
                <div class="md:col-span-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-300 mb-4">Navigasi Utama</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-emerald-400 transition">Beranda Utama</a></li>
                        <li><a href="{{ url('/pengumuman') }}" class="hover:text-emerald-400 transition">Pengumuman & Warta</a></li>
                        <li><a href="{{ url('/verifikasi') }}" class="hover:text-emerald-400 transition">Validasi QR Dokumen</a></li>
                        <li><a href="{{ route('login') }}" class="text-slate-400 hover:text-white transition flex items-center gap-1.5"><span>Login Sekretaris</span></a></li>
                    </ul>
                </div>

                <!-- Kolom 3: Layanan Sekretariat -->
                <div class="md:col-span-3">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-300 mb-4">Layanan Sekretariat</h4>
                    <ul class="space-y-2.5 text-xs text-slate-300/80 leading-relaxed">
                        <li>Administrasi & Induk Santri</li>
                        <li>Legalisir & Pengesahan Surat</li>
                        <li>Arsip Dokumen & Piagam Ijazah</li>
                        <li class="pt-1 text-emerald-400 font-semibold">Jam Pelayanan: Sabtu - Kamis (08:00 - 17:00 WIB)</li>
                    </ul>
                </div>
            </div>

            <!-- Hak Cipta Resmi -->
            <div class="pt-8 text-center text-xs text-slate-400 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} Pondok Pesantren Al-Falah Putra. Seluruh Hak Cipta Dilindungi.</p>
                <p class="text-emerald-400/80">Di bawah naungan Pondok Pesantren Al-Falah Putak, Muara Enim</p>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
