@extends('layouts.public')

@section('title', 'Warta & Pengumuman Resmi')

@section('content')
<!-- Header Pengumuman -->
<section class="bg-gradient-to-b from-emerald-950 via-emerald-900 to-emerald-950 text-white py-9 sm:py-14 border-b border-emerald-800/60 relative overflow-hidden">
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#34d399_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-2.5 relative z-10">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-900/90 border border-emerald-700/80 text-emerald-300 text-[11px] sm:text-xs font-bold uppercase tracking-wider shadow-xs">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
            Kanal Informasi Resmi
        </span>
        <h1 class="text-2xl sm:text-3xl md:text-4xl font-extrabold tracking-tight text-white drop-shadow-xs">
            Pengumuman &amp; Warta Pesantren
        </h1>
        <p class="max-w-2xl mx-auto text-emerald-100/90 text-xs sm:text-sm md:text-base font-normal leading-relaxed px-2">
            Pusat maklumat, kalender akademik, dan kabar kegiatan santri Pondok Pesantren Al-Falah Putak.
        </p>
    </div>
</section>

<!-- Konten Daftar Pengumuman -->
<section class="py-8 sm:py-14 bg-slate-50 min-h-[550px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8">

        <!-- Form Filter & Pencarian (Sangat Rapi di HP & Laptop) -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl border border-slate-200/90 shadow-xs">
            <form action="{{ url('/pengumuman') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                <!-- Input Pencarian -->
                <div class="sm:col-span-6 lg:col-span-7 space-y-1.5">
                    <label for="q" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Cari Warta</label>
                    <div class="relative">
                        <input type="text"
                               name="q"
                               id="q"
                               value="{{ request('q') }}"
                               placeholder="Cari judul atau isi warta..."
                               class="w-full pl-10 pr-4 py-2.5 sm:py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3 sm:top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <!-- Dropdown Kategori -->
                <div class="sm:col-span-3 lg:col-span-3 space-y-1.5">
                    <label for="kategori" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Kategori</label>
                    <select name="kategori"
                            id="kategori"
                            class="w-full py-2.5 sm:py-3 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">Semua Kategori</option>
                        <option value="umum" {{ request('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="santri" {{ request('kategori') == 'santri' ? 'selected' : '' }}>Santri</option>
                        <option value="wali" {{ request('kategori') == 'wali' ? 'selected' : '' }}>Wali Santri</option>
                        <option value="akademik" {{ request('kategori') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                    </select>
                </div>

                <!-- Tombol Submit & Reset -->
                <div class="sm:col-span-3 lg:col-span-2 flex gap-2 pt-1 sm:pt-0">
                    <button type="submit"
                            class="flex-1 py-2.5 sm:py-3 px-4 rounded-xl font-bold text-sm text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <span>Cari</span>
                    </button>
                    @if(request()->filled('q') || request()->filled('kategori'))
                        <a href="{{ url('/pengumuman') }}"
                           class="py-2.5 sm:py-3 px-3.5 rounded-xl font-bold text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 transition flex items-center justify-center"
                           title="Hapus Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Daftar Kartu Pengumuman (Sangat Rapi & Proporsional di Layar HP) -->
        @if($pengumumans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-7">
                @foreach($pengumumans as $item)
                    <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs hover:border-emerald-300 hover:shadow-md transition duration-200 flex flex-col justify-between group">
                        <div>
                            @if($item->gambar)
                                <div class="w-full aspect-[16/9] overflow-hidden bg-slate-100 relative">
                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                         alt="{{ $item->judul }}"
                                         loading="lazy"
                                         class="w-full h-full object-cover group-hover:scale-103 transition duration-300"
                                         style="object-position: {{ $item->posisi_gambar ?? '50% 50%' }};">
                                    @if($item->is_pinned)
                                        <span class="absolute top-2.5 right-2.5 inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-amber-500/95 backdrop-blur-xs text-white text-[11px] font-bold shadow-xs">
                                            <span>📌</span>
                                            <span>Disematkan</span>
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="w-full h-32 bg-gradient-to-br from-emerald-900 to-emerald-950 p-5 flex items-start justify-between relative text-white">
                                    <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider bg-emerald-800/80 text-emerald-200 border border-emerald-700/60">
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

                            <div class="p-5 sm:p-6 space-y-2.5">
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <span class="inline-flex items-center gap-1 font-medium text-slate-500">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $item->formatted_tgl_tayang }}
                                    </span>
                                    <span>•</span>
                                    <span class="capitalize px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-100">
                                        {{ $item->kategori }}
                                    </span>
                                </div>

                                <h2 class="text-base sm:text-lg font-bold text-slate-900 leading-snug line-clamp-2 group-hover:text-emerald-800 transition">
                                    <a href="{{ url('/pengumuman/' . $item->slug) }}">
                                        {{ $item->judul }}
                                    </a>
                                </h2>

                                <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                    {{ Str::limit(strip_tags($item->isi), 125) }}
                                </p>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 pt-0 border-t border-slate-100 mt-2">
                            <div class="flex items-center justify-between pt-3">
                                <a href="{{ url('/pengumuman/' . $item->slug) }}"
                                   class="inline-flex items-center text-xs sm:text-sm font-bold text-emerald-800 hover:text-emerald-900 group/link">
                                    <span>Baca</span>
                                    <svg class="w-3.5 h-3.5 ml-1 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>

                                <!-- Indikator Like, Komentar, Share Nyata (Dinamis dari Database) -->
                                <div class="flex items-center gap-2.5 text-xs text-slate-500 font-medium">
                                    <span class="inline-flex items-center gap-1 hover:text-rose-600 transition" title="Disukai {{ $item->likes_count }} kali">
                                        <svg class="w-3.5 h-3.5 text-rose-500 fill-rose-500" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                        <span>{{ $item->likes_count }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 hover:text-emerald-700 transition" title="{{ $item->komentars_count }} Komentar">
                                        <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        <span>{{ $item->komentars_count }}</span>
                                    </span>
                                    <span class="inline-flex items-center gap-1 hover:text-sky-600 transition" title="Dibagikan {{ $item->shares_count }} kali">
                                        <svg class="w-3.5 h-3.5 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                                        <span>{{ $item->shares_count }}</span>
                                    </span>
                                    <a href="{{ url('/p/' . $item->id) }}"
                                       title="Tautan Singkat"
                                       class="text-[10px] font-mono text-slate-400 hover:text-emerald-700 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-200">
                                        /p/{{ $item->id }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Navigasi Paginasi Halaman -->
            <div class="pt-6">
                {{ $pengumumans->links() }}
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/90 p-8 space-y-3">
                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center text-xl">
                    📰
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Pengumuman Ditemukan</h3>
                <p class="text-slate-500 text-xs sm:text-sm max-w-sm mx-auto">
                    Tidak ditemukan pengumuman sesuai kata kunci atau kategori yang Anda pilih. Silakan gunakan kata kunci lain.
                </p>
                <div class="pt-2">
                    <a href="{{ url('/pengumuman') }}" class="inline-flex items-center px-4 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition">
                        Tampilkan Semua Pengumuman
                    </a>
                </div>
            </div>
        @endif

    </div>
</section>
@endsection
