@extends('layouts.public')

@section('title', 'Warta & Pengumuman Resmi')

@section('content')
<!-- Header Pengumuman -->
<section class="bg-emerald-950 text-white py-12 sm:py-16 border-b border-emerald-900/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-3">
        <span class="inline-block px-3.5 py-1 rounded-full bg-emerald-900/80 border border-emerald-700/60 text-emerald-300 text-xs font-semibold uppercase tracking-wider">
            Informasi Sekretariat
        </span>
        <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
            Pengumuman & Warta Pesantren
        </h1>
        <p class="max-w-2xl mx-auto text-emerald-100/90 text-xs sm:text-sm md:text-base leading-relaxed">
            Informasi resmi seputar maklumat pondok, kalender akademik, dan kegiatan santri Pondok Pesantren Al-Falah Putra.
        </p>
    </div>
</section>

<!-- Konten Daftar Pengumuman -->
<section class="py-10 sm:py-14 bg-slate-50 min-h-[550px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- Form Filter & Pencarian (Responsif di HP & Desktop) -->
        <div class="bg-white p-5 sm:p-6 rounded-2xl border border-slate-200/80 shadow-xs">
            <form action="{{ url('/pengumuman') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 sm:gap-4 items-end">
                <!-- Input Pencarian -->
                <div class="sm:col-span-6 lg:col-span-7 space-y-1.5">
                    <label for="q" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Kata Kunci</label>
                    <div class="relative">
                        <input type="text"
                               name="q"
                               id="q"
                               value="{{ request('q') }}"
                               placeholder="Cari judul atau isi pengumuman..."
                               class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <!-- Dropdown Kategori -->
                <div class="sm:col-span-3 lg:col-span-3 space-y-1.5">
                    <label for="kategori" class="block text-xs font-bold text-slate-600 uppercase tracking-wider">Kategori</label>
                    <select name="kategori"
                            id="kategori"
                            class="w-full py-2.5 rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                        <option value="">Semua Kategori</option>
                        <option value="umum" {{ request('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="santri" {{ request('kategori') == 'santri' ? 'selected' : '' }}>Santri</option>
                        <option value="wali" {{ request('kategori') == 'wali' ? 'selected' : '' }}>Wali Santri</option>
                        <option value="akademik" {{ request('kategori') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                    </select>
                </div>

                <!-- Tombol Submit & Reset -->
                <div class="sm:col-span-3 lg:col-span-2 flex gap-2">
                    <button type="submit"
                            class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition">
                        Cari
                    </button>
                    @if(request()->filled('q') || request()->filled('kategori'))
                        <a href="{{ url('/pengumuman') }}"
                           class="py-2.5 px-3 rounded-xl font-bold text-xs text-slate-600 bg-slate-100 hover:bg-slate-200 transition flex items-center justify-center"
                           title="Hapus Filter">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Daftar Kartu Pengumuman -->
        @if($pengumumans->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($pengumumans as $item)
                    <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-xs hover:border-emerald-300 hover:shadow-md transition duration-200 flex flex-col justify-between">
                        <div>
                            @if($item->gambar)
                                <div class="h-44 sm:h-48 overflow-hidden bg-slate-100 relative">
                                    <img src="{{ asset('storage/' . $item->gambar) }}"
                                         alt="{{ $item->judul }}"
                                         class="w-full h-full object-cover hover:scale-103 transition duration-300"
                                         style="object-position: {{ $item->posisi_gambar ?? '50% 50%' }};">
                                    @if($item->is_pinned)
                                        <span class="absolute top-3 right-3 px-2.5 py-1 rounded-lg bg-amber-500 text-white text-[11px] font-bold shadow-xs">
                                            Disematkan
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="h-28 bg-emerald-900 p-5 flex items-start justify-between relative text-white">
                                    <span class="text-emerald-200 text-xs font-bold uppercase tracking-wider">
                                        {{ ucfirst($item->kategori) }}
                                    </span>
                                    @if($item->is_pinned)
                                        <span class="px-2 py-0.5 rounded bg-amber-400 text-amber-950 text-[10px] font-bold">
                                            Disematkan
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div class="p-5 sm:p-6">
                                <div class="flex items-center gap-2 text-xs text-slate-500 mb-2.5">
                                    <span>{{ $item->formatted_tgl_tayang }}</span>
                                    <span>•</span>
                                    <span class="capitalize text-emerald-800 font-semibold">{{ $item->kategori }}</span>
                                </div>

                                <h2 class="text-base sm:text-lg font-bold text-slate-900 mb-2 line-clamp-2 hover:text-emerald-800 transition">
                                    <a href="{{ url('/pengumuman/' . $item->slug) }}">
                                        {{ $item->judul }}
                                    </a>
                                </h2>

                                <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                    {{ Str::limit(strip_tags($item->isi), 130) }}
                                </p>
                            </div>
                        </div>

                        <div class="p-5 sm:p-6 pt-0">
                            <a href="{{ url('/pengumuman/' . $item->slug) }}"
                               class="inline-flex items-center text-xs font-bold text-emerald-800 hover:text-emerald-900 group">
                                <span>Baca Selengkapnya</span>
                                <svg class="w-3.5 h-3.5 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
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
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80 p-8 space-y-3">
                <svg class="w-12 h-12 text-slate-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
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
