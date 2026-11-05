<aside
    id="sidebar"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
    @keydown.window.escape="sidebarOpen = false"
    class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] sm:max-w-[300px] lg:max-w-none lg:w-72 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen flex flex-col justify-between shadow-2xl overflow-hidden flex-shrink-0 border-r border-emerald-700/70"
    style="background-color: #065f46; background-image: linear-gradient(180deg, #065f46 0%, #064e3b 100%);"
>
    <!-- Logo & Identitas Lembaga (Lega, Bersahaja & Khas Al-Falah) -->
    <div class="px-5 py-3.5 min-h-[72px] flex items-center justify-between border-b border-emerald-700/60 bg-emerald-900/40 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0">
            <img src="{{ asset('images/logo.png') }}"
                 alt="Logo Pondok Pesantren Al Falah Putra"
                 class="w-11 h-11 object-contain shrink-0 drop-shadow-[0_2px_8px_rgba(0,0,0,0.3)] group-hover:scale-105 transition-transform duration-200"
                 style="width: 44px; height: 44px; min-width: 44px;">
            <div class="leading-tight min-w-0">
                <div class="flex items-center gap-1.5">
                    <span class="block font-black text-[15px] tracking-wider text-white truncate">AL-FALAH PUTRA</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse shrink-0" title="Sistem Aktif"></span>
                </div>
                <span class="block text-xs text-emerald-100 font-medium tracking-normal mt-0.5 truncate">Sekretariat Pesantren</span>
            </div>
        </a>
        <button @click="sidebarOpen = false"
                class="lg:hidden text-emerald-200 hover:text-white p-2 rounded-xl hover:bg-emerald-700/70 transition-colors focus:outline-none shrink-0 ml-1"
                title="Tutup Menu">
            <svg class="w-5 h-5 shrink-0" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    @php
        // Query badge status persuratan dan agenda
        $badgeSuratMasukBaru = \App\Models\SuratMasuk::where('status_disposisi', 'baru')->count();
        $badgeAgendaMendatang = \App\Models\Agenda::whereDate('mulai', '>=', now()->toDateString())
            ->whereDate('mulai', '<=', now()->addDays(2)->toDateString())
            ->where('status', 'terjadwal')
            ->count();
    @endphp

    <!-- Menu Navigasi (Tersusun Ergonomis: 3 Pilar Utama, Anti-Scroll, Indikator Aktif Tegas) -->
    <nav class="flex-1 px-3 py-2 space-y-0.5 text-sm overflow-y-auto custom-scrollbar min-h-0 select-none">
        
        <!-- PILAR 1: PELAYANAN & ADMINISTRASI HARIAN -->
        <div class="px-3 pt-2 pb-1 text-[10px] font-black uppercase tracking-wider text-emerald-300/80">
            Pelayanan Harian
        </div>

        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="truncate text-[13px]">Dashboard</span>
        </a>

        <!-- Surat Masuk -->
        <a href="{{ route('admin.surat-masuk.index') }}"
           class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.surat-masuk.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.surat-masuk.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <span class="truncate text-[13px]">Surat Masuk</span>
            </div>
            @if ($badgeSuratMasukBaru > 0)
                <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-black {{ request()->routeIs('admin.surat-masuk.*') ? 'bg-amber-300 text-amber-950' : 'bg-amber-400 text-amber-950 shadow-xs' }} tabular-nums">
                    {{ $badgeSuratMasukBaru }}
                </span>
            @endif
        </a>

        <!-- Surat Keluar -->
        <a href="{{ route('admin.surat-keluar.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.surat-keluar.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.surat-keluar.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            <span class="truncate text-[13px]">Surat Keluar</span>
        </a>

        <!-- Agenda Kegiatan -->
        <a href="{{ route('admin.agenda.index') }}"
           class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.agenda.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.agenda.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="truncate text-[13px]">Agenda Kegiatan</span>
            </div>
            @if ($badgeAgendaMendatang > 0)
                <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-black {{ request()->routeIs('admin.agenda.*') ? 'bg-emerald-200 text-emerald-950' : 'bg-emerald-300 text-emerald-950 shadow-xs' }} tabular-nums">
                    {{ $badgeAgendaMendatang }}
                </span>
            @endif
        </a>

        <!-- Buku Kas Bendahara -->
        <a href="{{ route('admin.bendahara.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.bendahara.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.bendahara.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="truncate text-[13px]">Buku Kas Bendahara</span>
        </a>

        <!-- PILAR 2: DATABASE & KEARSIPAN -->
        <div class="px-3 pt-3 pb-1 text-[10px] font-black uppercase tracking-wider text-emerald-300/80 border-t border-emerald-700/40 mt-1.5">
            Database & Dokumen
        </div>
        
        <!-- Data Santri -->
        <a href="{{ route('admin.santri.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.santri.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.santri.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="truncate text-[13px]">Data Santri</span>
        </a>

        <!-- Data Pengurus -->
        <a href="{{ route('admin.pengurus.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengurus.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengurus.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="truncate text-[13px]">Data Pengurus</span>
        </a>

        <!-- Arsip Dokumen -->
        <a href="{{ route('admin.arsip.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.arsip.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.arsip.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            <span class="truncate text-[13px]">Arsip Dokumen</span>
        </a>

        <!-- PILAR 3: PUBLIKASI & SISTEM -->
        <div class="px-3 pt-3 pb-1 text-[10px] font-black uppercase tracking-wider text-emerald-300/80 border-t border-emerald-700/40 mt-1.5">
            Publikasi & Sistem
        </div>
        
        <!-- Editor Beranda -->
        <a href="{{ route('admin.halaman.beranda') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.halaman.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.halaman.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="truncate text-[13px]">Editor Beranda</span>
        </a>

        <!-- Warta Pengumuman -->
        <a href="{{ route('admin.pengumuman.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengumuman.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengumuman.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span class="truncate text-[13px]">Warta Pengumuman</span>
        </a>

        <!-- Rekap Laporan -->
        <a href="{{ route('admin.laporan.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.laporan.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.laporan.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="truncate text-[13px]">Rekap Laporan</span>
        </a>

        <!-- Pengaturan Sistem -->
        <a href="{{ route('admin.pengaturan.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengaturan.*') ? 'bg-emerald-800 text-white font-bold shadow-xs border-l-4 border-amber-300 pl-2.5 rounded-l-none' : 'text-emerald-100 hover:bg-emerald-700/60 hover:text-white border-l-4 border-transparent' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengaturan.*') ? 'text-amber-300' : 'text-emerald-200 group-hover:text-white' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate text-[13px]">Pengaturan Sistem</span>
        </a>

        <!-- Pintasan Lihat Beranda Publik -->
        <div class="pt-2 pb-1">
            <a href="{{ url('/') }}" target="_blank"
               class="group flex items-center justify-between px-3 py-1.5 rounded-xl bg-emerald-900/60 hover:bg-emerald-900 text-emerald-200 hover:text-white border border-emerald-600/40 transition-colors text-xs font-semibold shadow-2xs">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0 text-amber-300" style="width: 16px; height: 16px; min-width: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span>Lihat Web Depan</span>
                </span>
                <span class="text-[11px] text-emerald-300 group-hover:text-amber-300 font-mono transition-transform group-hover:translate-x-0.5">↗</span>
            </a>
        </div>
    </nav>

    <!-- Informasi Login & Tombol Keluar (Rapi, Terbaca Jelas, Nyaman) -->
    <div class="p-3 border-t border-emerald-700/60 bg-emerald-950/40 flex-shrink-0">
        <div class="flex items-center justify-between gap-2 p-1 rounded-xl">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 min-w-0 flex-1 group" title="Buka Profil Saya">
                <div class="w-8 h-8 rounded-lg bg-emerald-600 border border-emerald-400/40 flex items-center justify-center font-bold text-white text-xs shrink-0 shadow-2xs group-hover:bg-emerald-500 transition" style="width: 32px; height: 32px; min-width: 32px;">
                    {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1 leading-tight">
                    <p class="text-[13px] font-bold text-white truncate group-hover:text-amber-200 transition">{{ Auth::user()->name ?? 'Sekretaris' }}</p>
                    <p class="text-[10px] text-emerald-200/90 truncate font-mono mt-0.5">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </a>

            <!-- Tombol Keluar (Logout) Cepat -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="p-1.5 text-emerald-200 hover:text-white hover:bg-rose-900/60 rounded-lg transition-colors border border-transparent hover:border-rose-700/50"
                        title="Keluar dari Sistem (Log Out)">
                    <svg class="w-4 h-4 shrink-0" style="width: 18px; height: 18px; min-width: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
        
        <div class="mt-2 pt-1.5 border-t border-emerald-700/30 flex items-center justify-between text-[10px] text-emerald-200/70 font-mono px-1">
            <span>SAP Al-Falah Putra</span>
            <span class="text-white/90 font-bold">v1.0 Produksi</span>
        </div>
    </div>
</aside>
