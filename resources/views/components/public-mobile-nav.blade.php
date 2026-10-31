@php
    $isHome = request()->is('/');
    $isProfil = request()->is('profil*') || request()->is('program-pendidikan*');
    $isPendaftaran = request()->is('pendaftaran*');
    $isPengumuman = request()->is('pengumuman*') || request()->is('p/*');
    $isKontak = request()->is('kontak*') || request()->is('konsultasi*') || request()->is('verifikasi*');
@endphp

<!-- Mobile Bottom Navigation (Khusus Pengunjung / Publik) -->
<nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-[0_-4px_20px_rgba(0,0,0,0.07)] transition-all duration-300 pb-[max(0.5rem,env(safe-area-inset-bottom))] pt-1 px-2 select-none" aria-label="Navigasi Bawah Mobile">
    <div class="max-w-md mx-auto grid grid-cols-5 gap-1 items-center">
        <!-- Beranda -->
        <a href="{{ route('home') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group {{ $isHome ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-600' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-active:scale-90 {{ $isHome ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                @if($isHome)
                    <span class="absolute -bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Beranda</span>
        </a>

        <!-- Profil & Program -->
        <a href="{{ route('publik.profil') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group {{ $isProfil ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-600' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-active:scale-90 {{ $isProfil ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                @if($isProfil)
                    <span class="absolute -bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Profil</span>
        </a>

        <!-- Pendaftaran Santri (Tombol Menonjol / Highlight) -->
        <a href="{{ route('publik.pendaftaran') }}" class="flex flex-col items-center justify-center py-1 px-1 -mt-2.5 group">
            <div class="w-11 h-11 rounded-full flex items-center justify-center shadow-md transition-all group-active:scale-95 {{ $isPendaftaran ? 'bg-emerald-700 text-white shadow-emerald-700/40' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-emerald-600/30' }}">
                <svg class="w-5 h-5 stroke-[2.2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
            </div>
            <span class="text-[10px] mt-0.5 leading-none tracking-tight font-semibold {{ $isPendaftaran ? 'text-emerald-700' : 'text-slate-600' }}">Daftar</span>
        </a>

        <!-- Pengumuman -->
        <a href="{{ route('publik.pengumuman.index') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group {{ $isPengumuman ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-600' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-active:scale-90 {{ $isPengumuman ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                </svg>
                @if($isPengumuman)
                    <span class="absolute -bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Kabar</span>
        </a>

        <!-- Kontak / Bantuan -->
        <a href="{{ route('publik.kontak') }}" class="flex flex-col items-center justify-center py-1.5 px-1 rounded-xl transition group {{ $isKontak ? 'text-emerald-700 font-bold' : 'text-slate-500 hover:text-emerald-600' }}">
            <div class="relative flex items-center justify-center">
                <svg class="w-5 h-5 transition-transform group-active:scale-90 {{ $isKontak ? 'stroke-[2.5]' : 'stroke-2' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                @if($isKontak)
                    <span class="absolute -bottom-1 w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                @endif
            </div>
            <span class="text-[10px] mt-1 leading-none tracking-tight">Kontak</span>
        </a>
    </div>
</nav>
