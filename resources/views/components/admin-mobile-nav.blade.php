@php
    $isDashboard = request()->routeIs('dashboard');
    $isSantri = request()->is('admin/santri*') || request()->is('admin/santri-impor*');
    $isSurat = request()->is('admin/surat-*');
    $isAgenda = request()->is('admin/agenda*') || request()->is('admin/arsip*');
@endphp

<!-- Mobile Bottom Navigation (Khusus Panel Admin Sekretariat) -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-[0_-4px_20px_rgba(0,0,0,0.07)] pb-[max(0.5rem,env(safe-area-inset-bottom))] pt-1 px-2 select-none" aria-label="Navigasi Bawah Admin">
    <div class="max-w-md mx-auto grid grid-cols-5 gap-1 items-center">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group {{ $isDashboard ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-600' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-active:scale-90 {{ $isDashboard ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                @if($isDashboard)
                    <span class="absolute -bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Beranda</span>
        </a>

        <!-- Santri -->
        <a href="{{ route('admin.santri.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group {{ $isSantri ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-600' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-active:scale-90 {{ $isSantri ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                @if($isSantri)
                    <span class="absolute -bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Santri</span>
        </a>

        <!-- Surat (Masuk / Keluar) -->
        <a href="{{ route('admin.surat-masuk.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group {{ $isSurat ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-600' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-active:scale-90 {{ $isSurat ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                @if($isSurat)
                    <span class="absolute -bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Surat</span>
        </a>

        <!-- Agenda -->
        <a href="{{ route('admin.agenda.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group {{ $isAgenda ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-600' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-active:scale-90 {{ $isAgenda ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                @if($isAgenda)
                    <span class="absolute -bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Agenda</span>
        </a>

        <!-- Menu / Sidebar Toggle -->
        <button type="button" @click="sidebarOpen = true" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group text-slate-500 hover:text-emerald-600 active:scale-95">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 stroke-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Menu</span>
        </button>
    </div>
</nav>
