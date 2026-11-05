<aside
    id="sidebar"
    :class="{ 'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen }"
    @keydown.window.escape="sidebarOpen = false"
    class="fixed inset-y-0 left-0 z-50 w-64 max-w-[85vw] sm:max-w-[270px] lg:max-w-none lg:w-64 bg-white text-slate-700 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:sticky lg:top-0 lg:h-screen flex flex-col justify-between shadow-xs lg:shadow-none border-r border-slate-100 flex-shrink-0"
>
    <!-- Logo & Identitas Lembaga (Sesuai Referensi Finora: Bersih Putih dengan Logo Hijau) -->
    <div class="px-5 py-5 flex items-center justify-between flex-shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group min-w-0">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 border border-emerald-100 shadow-2xs">
                <img src="{{ asset('images/logo.png') }}"
                     alt="Logo Pondok Pesantren Al Falah Putra"
                     class="w-7 h-7 object-contain drop-shadow-2xs">
            </div>
            <div class="leading-tight min-w-0">
                <span class="block font-black text-base tracking-tight text-slate-900 group-hover:text-emerald-900 transition truncate">Al-Falah Putra</span>
                <span class="block text-[11px] text-slate-400 font-medium tracking-tight mt-0.5 truncate">Sekretariat Pesantren</span>
            </div>
        </a>
        <button @click="sidebarOpen = false"
                class="lg:hidden text-slate-400 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100 transition focus:outline-none shrink-0"
                title="Tutup Navigasi">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Menu Navigasi (Sesuai Finora: Pill Hijau Botol #064e3b untuk Menu Aktif, Teks Abu-abu Rapi untuk Inaktif) -->
    <nav class="flex-1 px-4 py-2 space-y-1.5 text-sm overflow-y-auto custom-scrollbar min-h-0 select-none">
        
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('dashboard') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Dashboard</span>
        </a>

        <!-- Surat Masuk -->
        <a href="{{ route('admin.surat-masuk.index') }}"
           class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.surat-masuk.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.surat-masuk.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <span class="truncate text-xs sm:text-[13px]">Surat Masuk</span>
            </div>
            @if ($badgeSuratMasukBaru > 0)
                <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.surat-masuk.*') ? 'bg-white text-[#064e3b]' : 'bg-rose-100 text-rose-700' }} tabular-nums">
                    {{ $badgeSuratMasukBaru }}
                </span>
            @endif
        </a>

        <!-- Surat Keluar -->
        <a href="{{ route('admin.surat-keluar.index') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.surat-keluar.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.surat-keluar.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Surat Keluar</span>
        </a>

        <!-- Buku Kas Bendahara -->
        <a href="{{ route('admin.bendahara.index') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.bendahara.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.bendahara.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Buku Kas</span>
        </a>

        <!-- Agenda Pondok -->
        <a href="{{ route('admin.agenda.index') }}"
           class="group flex items-center justify-between px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.agenda.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <div class="flex items-center gap-3 min-w-0">
                <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.agenda.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="truncate text-xs sm:text-[13px]">Agenda Pondok</span>
            </div>
            @if ($badgeAgendaMendatang > 0)
                <span class="ml-2 px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.agenda.*') ? 'bg-white text-[#064e3b]' : 'bg-emerald-100 text-emerald-800' }} tabular-nums">
                    {{ $badgeAgendaMendatang }}
                </span>
            @endif
        </a>

        <!-- Data Santri -->
        <a href="{{ route('admin.santri.index') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.santri.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.santri.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Data Santri</span>
        </a>

        <!-- Data Pengurus -->
        <a href="{{ route('admin.pengurus.index') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.pengurus.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengurus.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Data Pengurus</span>
        </a>

        <!-- Arsip Dokumen -->
        <a href="{{ route('admin.arsip.index') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.arsip.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.arsip.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Arsip Dokumen</span>
        </a>

        <!-- Warta & Publikasi -->
        <a href="{{ route('admin.pengumuman.index') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.pengumuman.*') || request()->routeIs('admin.halaman.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengumuman.*') || request()->routeIs('admin.halaman.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Warta Pondok</span>
        </a>

        <!-- Rekap Laporan -->
        <a href="{{ route('admin.laporan.index') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.laporan.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.laporan.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Rekap Laporan</span>
        </a>

        <!-- Pengaturan Sistem -->
        <a href="{{ route('admin.pengaturan.index') }}"
           class="group flex items-center gap-3 px-3.5 py-2.5 rounded-2xl transition-all duration-150 {{ request()->routeIs('admin.pengaturan.*') ? 'bg-[#064e3b] text-white font-semibold shadow-xs' : 'text-slate-500 hover:text-slate-900 hover:bg-emerald-50/60 font-medium' }}">
            <svg class="w-5 h-5 shrink-0 {{ request()->routeIs('admin.pengaturan.*') ? 'text-white' : 'text-slate-400 group-hover:text-[#064e3b]' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span class="truncate text-xs sm:text-[13px]">Pengaturan Sistem</span>
        </a>
    </nav>

    <!-- Kartu Widget Bawah (Persis seperti Widget 'Small steps build big wealth' di Finora) -->
    <div class="p-4 flex-shrink-0">
        <div class="bg-[#e8f5e9] rounded-3xl p-4 text-center border border-emerald-100 shadow-2xs relative overflow-hidden">
            <!-- Icon Daun / Tunas Rapi -->
            <div class="w-12 h-12 mx-auto mb-2 text-[#064e3b] flex items-center justify-center">
                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3v18m0-18C8 3 4 7 4 12c0 4 3 7 8 7m0-14c4 0 8 4 8 9 0 4-3 7-8 7" />
                </svg>
            </div>
            <h4 class="font-extrabold text-slate-900 text-xs tracking-tight leading-snug">SAP Al-Falah Putra</h4>
            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Tertib berkas, amanah khidmah santri.</p>
            <a href="{{ url('/') }}" target="_blank"
               class="mt-3 block w-full py-2 px-3 bg-[#064e3b] hover:bg-[#043d2e] text-white text-xs font-semibold rounded-xl transition text-center shadow-xs">
                Lihat Web Depan &rarr;
            </a>
        </div>
    </div>
</aside>
