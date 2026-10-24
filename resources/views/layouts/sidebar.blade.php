<aside
    id="sidebar"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
    @keydown.window.escape="sidebarOpen = false"
    class="fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] sm:max-w-[280px] lg:max-w-none lg:w-72 bg-emerald-950 text-white transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen flex flex-col justify-between shadow-2xl overflow-hidden flex-shrink-0 border-r border-emerald-900/60"
>
    <!-- Logo & Identitas Lembaga -->
    <div class="h-18 px-5 flex items-center justify-between border-b border-emerald-900/60 bg-emerald-950 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Pondok Pesantren Al Falah Putra" class="w-10 h-10 object-contain shrink-0 drop-shadow-[0_2px_8px_rgba(0,0,0,0.35)] group-hover:scale-105 transition-transform duration-200">
            <div class="leading-tight">
                <div class="flex items-center gap-1.5">
                    <span class="block font-black text-sm tracking-wide text-white">AL-FALAH PUTRA</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" title="Sistem Aktif"></span>
                </div>
                <span class="block text-[11px] text-emerald-300/80 font-medium tracking-normal mt-0.5">Sekretariat Pesantren</span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden text-emerald-400 hover:text-white p-2 rounded-xl hover:bg-emerald-900/70 transition-colors focus:outline-none" title="Tutup Menu">
            <svg class="w-5 h-5 shrink-0" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    @php
        // Query ringan badge pemberitahuan
        $badgeSuratMasukBaru = \App\Models\SuratMasuk::where('status_disposisi', 'baru')->count();
        $badgeAgendaMendatang = \App\Models\Agenda::whereDate('mulai', '>=', now()->toDateString())
            ->whereDate('mulai', '<=', now()->addDays(2)->toDateString())
            ->where('status', 'terjadwal')
            ->count();
    @endphp

    <!-- Menu Navigasi (Scrollable) -->
    <nav class="flex-1 p-3 space-y-0.5 text-xs sm:text-[13px] overflow-y-auto custom-scrollbar min-h-0 select-none">
        
        <!-- SECTION 1: UTAMA -->
        <div class="px-3 pt-2 pb-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400/50">
            Utama
        </div>

        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="truncate">Dashboard</span>
        </a>

        <!-- SECTION 2: MASTER DATA -->
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400/50">
            Master Data
        </div>
        
        <a href="{{ route('admin.santri.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.santri.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.santri.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="truncate">Data Santri</span>
        </a>

        <a href="{{ route('admin.pengurus.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengurus.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengurus.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="truncate">Data Pengurus</span>
        </a>

        <!-- SECTION 3: ADMINISTRASI SURAT -->
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400/50">
            Administrasi Surat
        </div>
        
        <a href="{{ route('admin.surat-masuk.index') }}"
           class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.surat-masuk.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.surat-masuk.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <span class="truncate">Surat Masuk</span>
            </div>
            @if ($badgeSuratMasukBaru > 0)
                <span class="ml-2 px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ request()->routeIs('admin.surat-masuk.*') ? 'bg-white text-emerald-900' : 'bg-amber-400/20 text-amber-300 border border-amber-400/30' }} tabular-nums">
                    {{ $badgeSuratMasukBaru }}
                </span>
            @endif
        </a>

        <a href="{{ route('admin.surat-keluar.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.surat-keluar.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.surat-keluar.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            <span class="truncate">Surat Keluar</span>
        </a>

        <!-- SECTION 4: AGENDA & ARSIP -->
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400/50">
            Agenda & Dokumen
        </div>
        
        <a href="{{ route('admin.agenda.index') }}"
           class="group flex items-center justify-between px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.agenda.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.agenda.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="truncate">Agenda Kegiatan</span>
            </div>
            @if ($badgeAgendaMendatang > 0)
                <span class="ml-2 px-1.5 py-0.5 rounded-md text-[10px] font-bold {{ request()->routeIs('admin.agenda.*') ? 'bg-white text-emerald-900' : 'bg-purple-400/20 text-purple-300 border border-purple-400/30' }} tabular-nums">
                    {{ $badgeAgendaMendatang }}
                </span>
            @endif
        </a>

        <a href="{{ route('admin.arsip.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.arsip.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.arsip.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            <span class="truncate">Arsip Dokumen</span>
        </a>

        <!-- SECTION 5: PUBLIK & WEBSITE -->
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400/50">
            Publikasi & Web
        </div>
        
        <a href="{{ route('admin.halaman.beranda') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.halaman.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.halaman.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span class="truncate">Editor Beranda</span>
        </a>

        <a href="{{ route('admin.pengumuman.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengumuman.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengumuman.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span class="truncate">Warta Pengumuman</span>
        </a>

        <!-- SECTION 6: LAPORAN & SISTEM -->
        <div class="px-3 pt-4 pb-1 text-[10px] font-bold uppercase tracking-wider text-emerald-400/50">
            Laporan & Sistem
        </div>
        
        <a href="{{ route('admin.laporan.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.laporan.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.laporan.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="truncate">Rekap Laporan</span>
        </a>

        <a href="{{ route('admin.pengaturan.index') }}"
           class="group flex items-center gap-3 px-3 py-2 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengaturan.*') ? 'bg-emerald-600 text-white font-semibold shadow-xs ring-1 ring-emerald-400/30' : 'text-emerald-100/75 hover:bg-emerald-900/40 hover:text-white' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengaturan.*') ? 'text-white' : 'text-emerald-400/70 group-hover:text-emerald-200' }}" style="width: 20px; height: 20px; min-width: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate">Pengaturan Sistem</span>
        </a>

        <!-- Pintasan Lihat Beranda Publik -->
        <div class="pt-3 pb-1">
            <a href="{{ url('/') }}" target="_blank"
               class="group flex items-center justify-between px-3 py-2 rounded-xl bg-emerald-900/30 hover:bg-emerald-900/60 text-emerald-200/90 hover:text-white border border-emerald-800/40 transition-colors text-xs font-medium">
                <span class="flex items-center gap-2.5">
                    <svg class="w-4 h-4 shrink-0 text-emerald-400 group-hover:text-emerald-300 transition-colors" style="width: 16px; height: 16px; min-width: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Lihat Web Depan
                </span>
                <span class="text-[10px] text-emerald-400/80 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform font-mono">↗</span>
            </a>
        </div>
    </nav>

    <!-- Informasi Login & Tombol Aksi Cepat -->
    <div class="p-3 border-t border-emerald-900/60 bg-emerald-950/95 flex-shrink-0">
        <div class="flex items-center justify-between gap-2 p-1 rounded-xl">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 min-w-0 flex-1 group" title="Buka Profil Saya">
                <div class="w-8 h-8 rounded-lg bg-emerald-700/90 border border-emerald-500/30 flex items-center justify-center font-bold text-white text-xs shrink-0 shadow-2xs group-hover:bg-emerald-600 transition">
                    {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-white truncate group-hover:text-emerald-200 transition">{{ Auth::user()->name ?? 'Sekretaris' }}</p>
                    <p class="text-[10px] text-emerald-300/60 truncate font-mono">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </a>

            <!-- Tombol Keluar (Logout) Cepat -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="p-1.5 text-emerald-400/70 hover:text-rose-300 hover:bg-rose-950/40 rounded-lg transition-colors border border-transparent hover:border-rose-900/40"
                        title="Keluar dari Sistem (Log Out)">
                    <svg class="w-4 h-4 shrink-0" style="width: 16px; height: 16px; min-width: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
        
        <div class="mt-2 pt-1.5 border-t border-emerald-900/40 flex items-center justify-between text-[10px] text-emerald-400/40 font-mono px-1">
            <span>SAP Al-Falah Putra</span>
            <span class="text-emerald-400/70 font-semibold">v1.0 Produksi</span>
        </div>
    </div>
</aside>
