<aside
    id="sidebar"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
    class="fixed inset-y-0 left-0 z-50 w-64 md:w-72 bg-emerald-950 text-white transition-transform duration-300 ease-in-out md:translate-x-0 md:sticky md:top-0 md:h-screen flex flex-col justify-between shadow-2xl overflow-hidden flex-shrink-0 border-r border-emerald-900/60"
>
    <!-- Logo / Identitas Lembaga -->
    <div class="h-20 px-5 flex items-center justify-between border-b border-emerald-900/80 bg-emerald-950 flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-12 h-12 rounded-2xl bg-white p-1.5 shadow-md shadow-emerald-950/50 flex items-center justify-center shrink-0 border border-emerald-200/40 group-hover:scale-105 transition-transform">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Pondok Pesantren Al Falah Putra" class="w-full h-full object-contain">
            </div>
            <div class="leading-tight">
                <div class="flex items-center gap-1.5">
                    <span class="block font-black text-sm md:text-[15px] tracking-wider text-white">AL-FALAH PUTRA</span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse" title="Sistem Aktif"></span>
                </div>
                <span class="block text-[11px] text-emerald-300/90 font-medium tracking-normal mt-0.5">Sekretariat Pesantren</span>
            </div>
        </a>
        <button @click="sidebarOpen = false" class="md:hidden text-emerald-400 hover:text-white p-1.5 rounded-xl hover:bg-emerald-900 transition-colors focus:outline-none" title="Tutup Menu">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Menu Navigasi (Scrollable) -->
    <nav class="flex-1 p-3.5 md:p-4 space-y-1 text-sm overflow-y-auto no-scrollbar min-h-0">
        
        <!-- SECTION 1: UTAMA -->
        <div class="px-3 pt-2 pb-1 text-[11px] font-extrabold uppercase tracking-widest text-emerald-400/80 flex items-center gap-2">
            <span>Utama</span>
        </div>

        <a href="{{ route('dashboard') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('dashboard') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span>Dashboard</span>
        </a>

        <!-- SECTION 2: MASTER DATA -->
        <div class="pt-4 px-3 pb-1 text-[11px] font-extrabold uppercase tracking-widest text-emerald-400/80">Master Data</div>
        
        <a href="{{ route('admin.santri.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.santri.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.santri.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span>Data Santri</span>
        </a>

        <a href="{{ route('admin.pengurus.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengurus.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.pengurus.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span>Data Pengurus</span>
        </a>

        <!-- SECTION 3: ADMINISTRASI SURAT -->
        <div class="pt-4 px-3 pb-1 text-[11px] font-extrabold uppercase tracking-widest text-emerald-400/80">Administrasi Surat</div>
        
        <a href="{{ route('admin.surat-masuk.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.surat-masuk.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.surat-masuk.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <span>Surat Masuk</span>
        </a>

        <a href="{{ route('admin.surat-keluar.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.surat-keluar.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.surat-keluar.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            <span>Surat Keluar</span>
        </a>

        <!-- SECTION 4: AGENDA & ARSIP -->
        <div class="pt-4 px-3 pb-1 text-[11px] font-extrabold uppercase tracking-widest text-emerald-400/80">Agenda & Dokumen</div>
        
        <a href="{{ route('admin.agenda.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.agenda.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.agenda.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <span>Agenda Kegiatan</span>
        </a>

        <a href="{{ route('admin.arsip.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.arsip.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.arsip.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            <span>Arsip Dokumen</span>
        </a>

        <!-- SECTION 5: PUBLIK & WEBSITE -->
        <div class="pt-4 px-3 pb-1 text-[11px] font-extrabold uppercase tracking-widest text-emerald-400/80">Publikasi & Web</div>
        
        <a href="{{ route('admin.halaman.beranda') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.halaman.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.halaman.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Editor Beranda</span>
        </a>

        <a href="{{ route('admin.pengumuman.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengumuman.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.pengumuman.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span>Warta Pengumuman</span>
        </a>

        <!-- SECTION 6: LAPORAN & SISTEM -->
        <div class="pt-4 px-3 pb-1 text-[11px] font-extrabold uppercase tracking-widest text-emerald-400/80">Laporan & Sistem</div>
        
        <a href="{{ route('admin.laporan.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.laporan.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.laporan.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Rekap Laporan</span>
        </a>

        <a href="{{ route('admin.pengaturan.index') }}"
           class="flex items-center gap-3.5 px-3.5 py-2.5 rounded-xl transition-all duration-150 {{ request()->routeIs('admin.pengaturan.*') ? 'bg-emerald-800 text-white font-bold shadow-sm border-l-4 border-l-emerald-400 pl-3' : 'text-emerald-100/90 hover:bg-emerald-900/80 hover:text-white' }}">
            <svg class="w-5 h-5 flex-shrink-0 {{ request()->routeIs('admin.pengaturan.*') ? 'text-emerald-300' : 'text-emerald-400/80' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Pengaturan Sistem</span>
        </a>

        <!-- Pintasan Lihat Beranda Publik -->
        <div class="pt-4">
            <a href="{{ url('/') }}" target="_blank"
               class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-emerald-900/60 hover:bg-emerald-900 text-emerald-200 hover:text-white border border-emerald-800/60 transition-colors text-xs font-semibold group">
                <span class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400 group-hover:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    Lihat Web Depan
                </span>
                <span class="text-[10px] bg-emerald-950 px-2 py-0.5 rounded text-emerald-300 font-mono">↗</span>
            </a>
        </div>
    </nav>

    <!-- Informasi Login & Tombol Aksi Cepat -->
    <div class="p-3.5 border-t border-emerald-900/80 bg-emerald-950/95 flex-shrink-0">
        <div class="flex items-center justify-between gap-2.5">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 min-w-0 flex-1 hover:opacity-90 transition-opacity" title="Buka Profil Saya">
                <div class="w-9 h-9 rounded-xl bg-emerald-700 border border-emerald-500/40 flex items-center justify-center font-extrabold text-white text-xs shrink-0 shadow-2xs">
                    {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Sekretaris' }}</p>
                    <p class="text-[11px] text-emerald-300/80 truncate font-mono">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </a>

            <!-- Tombol Keluar (Logout) Cepat -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit"
                        class="p-2 text-emerald-300 hover:text-rose-300 hover:bg-rose-950/50 rounded-xl transition-colors border border-transparent hover:border-rose-900/40"
                        title="Keluar dari Sistem (Log Out)">
                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>
        </div>
        
        <div class="mt-2.5 pt-2 border-t border-emerald-900/50 flex items-center justify-between text-[10px] text-emerald-400/60 font-mono">
            <span>SAP Al-Falah Putra</span>
            <span class="text-emerald-400/80 font-bold">v1.0 Produksi</span>
        </div>
    </div>
</aside>
