@extends('layouts.public')

@section('title', 'Warta & Pengumuman Resmi')

@section('content')
<!-- Header Sederhana & Bersih (Tanpa Efek Terpotong) -->
<section class="bg-emerald-900 text-white py-8 sm:py-12 border-b border-emerald-800">
    <div class="max-w-4xl mx-auto px-4 text-center space-y-2">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-800/90 text-emerald-200 text-[11px] font-bold uppercase tracking-wider">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
            Kanal Informasi Resmi
        </span>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">
            Pengumuman &amp; Warta Pesantren
        </h1>
        <p class="text-emerald-100/90 text-xs sm:text-sm max-w-lg mx-auto leading-relaxed">
            Pusat maklumat, kalender akademik, dan kabar kegiatan santri Pondok Pesantren Al-Falah Putak.
        </p>
    </div>
</section>

<!-- Konten Daftar Pengumuman -->
<section class="py-6 sm:py-10 bg-slate-50 min-h-[600px]">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 space-y-6">

        <!-- Area Pencarian & Filter Kategori yang Ringkas & Rapi -->
        <div class="space-y-4 max-w-xl mx-auto">
            <form action="{{ url('/pengumuman') }}" method="GET" class="w-full">
                @if(request('kategori'))
                    <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                @endif
                <div class="relative w-full flex items-center">
                    <!-- Icon Search di Kiri -->
                    <div style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;" class="text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>

                    <!-- Input Search -->
                    <input type="text"
                           name="q"
                           value="{{ request('q') }}"
                           placeholder="Cari warta pesantren..."
                           class="w-full pl-10 pr-24 py-2.5 sm:py-3 rounded-full border border-slate-300 bg-white shadow-xs focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-sm placeholder:text-slate-400">

                    <!-- Tombol Aksi di Kanan -->
                    <div style="position: absolute; right: 6px; top: 50%; transform: translateY(-50%); display: flex; align-items: center; gap: 4px;">
                        @if(request('q'))
                            <a href="{{ url('/pengumuman' . (request('kategori') ? '?kategori=' . request('kategori') : '')) }}"
                               class="text-xs text-slate-400 hover:text-slate-600 px-1.5 py-1" title="Hapus pencarian">
                                ✕
                            </a>
                        @endif
                        <button type="submit"
                                class="px-4 py-1.5 rounded-full text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-2xs whitespace-nowrap">
                            Cari
                        </button>
                    </div>
                </div>
            </form>

            <!-- Kategori Tabs (Pills Horizontal Rapi & Mudah Dipilih di HP) -->
            <div class="flex items-center justify-start sm:justify-center gap-2 overflow-x-auto pb-1 no-scrollbar text-xs">
                <a href="{{ url('/pengumuman' . (request('q') ? '?q=' . urlencode(request('q')) : '')) }}"
                   class="px-4 py-1.5 rounded-full font-semibold whitespace-nowrap transition shadow-2xs {{ !request('kategori') ? 'bg-emerald-800 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                    Semua
                </a>
                @foreach(['umum' => 'Umum', 'santri' => 'Santri', 'wali' => 'Wali Santri', 'akademik' => 'Akademik'] as $slug => $label)
                    <a href="{{ url('/pengumuman?kategori=' . $slug . (request('q') ? '&q=' . urlencode(request('q')) : '')) }}"
                       class="px-4 py-1.5 rounded-full font-semibold whitespace-nowrap transition shadow-2xs {{ request('kategori') === $slug ? 'bg-emerald-800 text-white' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>
        </div>

        <!-- Daftar Kartu Pengumuman Bersih & Elegan -->
        @if($pengumumans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 pt-2">
                @foreach($pengumumans as $item)
                    <article class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-xs hover:shadow-md hover:border-emerald-300 transition duration-200 flex flex-col justify-between group">
                        <div>
                            @if($item->gambar)
                                <div class="w-full aspect-[16/9] overflow-hidden bg-slate-100 relative">
                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                         alt="{{ $item->judul }}"
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-103 transition duration-300"
                                         style="object-position: {{ $item->posisi_gambar ?? '50% 50%' }};">
                                    @if($item->is_pinned)
                                        <span class="absolute top-2.5 right-2.5 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-500 text-white text-[11px] font-bold shadow-xs">
                                            <span>📌</span>
                                            <span>Disematkan</span>
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="w-full h-28 bg-gradient-to-br from-emerald-800 to-emerald-950 p-4 flex items-start justify-between relative text-white">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-emerald-700/80 text-emerald-100 border border-emerald-600/50">
                                        {{ ucfirst($item->kategori) }}
                                    </span>
                                    @if($item->is_pinned)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-400 text-amber-950 text-[11px] font-bold shadow-xs">
                                            <span>📌</span>
                                            <span>Disematkan</span>
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div class="p-5 space-y-2">
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <span>{{ $item->formatted_tgl_tayang }}</span>
                                    <span>•</span>
                                    <span class="capitalize font-semibold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md">
                                        {{ $item->kategori }}
                                    </span>
                                </div>

                                <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug line-clamp-2 group-hover:text-emerald-800 transition">
                                    <a href="{{ url('/pengumuman/' . $item->slug) }}">
                                        {{ $item->judul }}
                                    </a>
                                </h2>

                                <p class="text-slate-600 text-xs sm:text-sm line-clamp-2 leading-relaxed">
                                    {{ Str::limit(strip_tags($item->isi), 110) }}
                                </p>
                            </div>
                        </div>

                        <!-- Footer Kartu Sederhana & Bersih (Tanpa Tumpukan Angka Statis) -->
                        <div class="p-5 pt-0">
                            <a href="{{ url('/pengumuman/' . $item->slug) }}"
                               class="inline-flex items-center text-xs sm:text-sm font-bold text-emerald-800 hover:text-emerald-900 group/link">
                                <span>Baca Selengkapnya</span>
                                <svg class="w-4 h-4 ml-1.5 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Navigasi Paginasi Halaman -->
            <div class="pt-6">
                {{ $pengumumans->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200 p-8 space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl">
                    📰
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Warta Ditemukan</h3>
                <p class="text-slate-500 text-xs sm:text-sm max-w-sm mx-auto">
                    Tidak ditemukan pengumuman sesuai kata kunci atau kategori yang Anda pilih.
                </p>
                <div class="pt-2">
                    <a href="{{ url('/pengumuman') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition">
                        Tampilkan Semua Warta
                    </a>
                </div>
            </div>
        @endif

    </div>
</section>
@endsection
