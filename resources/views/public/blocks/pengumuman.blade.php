@php
    $data = $block['data'] ?? [];
    $judul = $data['judul'] ?? 'Warta & Pengumuman Terbaru';
    $subjudul = $data['subjudul'] ?? 'Informasi resmi sekretariat untuk wali santri, asatidz, dan masyarakat';
@endphp

<section class="py-10 sm:py-14 bg-slate-50/60 border-b border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-8 gap-3">
            <div class="space-y-2">
                <span class="inline-block text-xs font-semibold uppercase tracking-wider text-emerald-800 bg-emerald-50 px-3.5 py-1 rounded-full border border-emerald-100">
                    Warta Sekretariat
                </span>
                <h2 class="text-xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 tracking-tight">
                    {{ $judul }}
                </h2>
                <p class="text-slate-600 text-xs sm:text-sm md:text-base leading-relaxed">
                    {{ $subjudul }}
                </p>
            </div>
            <div>
                <a href="{{ url('/pengumuman') }}" class="inline-flex items-center gap-1.5 min-h-[44px] text-xs sm:text-sm font-bold text-emerald-800 hover:text-emerald-900 group">
                    <span>Lihat Seluruh Pengumuman</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
        </div>

        @if(isset($pengumumanTerbaru) && $pengumumanTerbaru->count() > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
                @foreach($pengumumanTerbaru as $item)
                    <article class="bg-white rounded-2xl overflow-hidden border border-slate-200/80 shadow-xs hover:border-emerald-300 hover:shadow-sm transition duration-200 flex flex-col justify-between h-full">
                        <div>
                            @if($item->gambar)
                                <div class="h-44 sm:h-48 overflow-hidden bg-slate-100 relative">
                                    <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->judul }}"
                                         loading="lazy"
                                         decoding="async"
                                         class="w-full h-full object-cover hover:scale-103 transition duration-300"
                                         style="object-position: {{ $item->posisi_gambar ?? '50% 50%' }};">
                                    @if($item->is_pinned)
                                        <span class="absolute top-2.5 right-2.5 px-2.5 py-0.5 rounded-md bg-amber-500 text-white text-xs font-semibold shadow-xs">
                                            Disematkan
                                        </span>
                                    @endif
                                </div>
                            @else
                                <div class="h-28 bg-emerald-900 p-5 flex items-start justify-between relative text-white">
                                    <span class="text-emerald-200 text-xs font-semibold uppercase tracking-wider">
                                        {{ ucfirst($item->kategori) }}
                                    </span>
                                    @if($item->is_pinned)
                                        <span class="px-2.5 py-0.5 rounded-md bg-amber-400 text-amber-950 text-xs font-semibold shadow-xs">
                                            Disematkan
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <div class="p-5">
                                <div class="flex items-center gap-2 text-xs text-slate-500 mb-2">
                                    <span>{{ $item->formatted_tgl_tayang }}</span>
                                    <span>•</span>
                                    <span class="capitalize text-emerald-800 font-semibold">{{ $item->kategori }}</span>
                                </div>

                                <h3 class="text-sm sm:text-base font-bold text-slate-900 mb-2 line-clamp-2 hover:text-emerald-800 transition">
                                    <a href="{{ url('/pengumuman/' . $item->slug) }}">
                                        {{ $item->judul }}
                                    </a>
                                </h3>

                                <p class="text-slate-600 text-xs sm:text-sm line-clamp-3 leading-relaxed">
                                    {{ Str::limit(strip_tags($item->isi), 120) }}
                                </p>
                            </div>
                        </div>

                        <div class="p-5 pt-0">
                            <a href="{{ url('/pengumuman/' . $item->slug) }}" class="inline-flex items-center min-h-[44px] text-xs sm:text-sm font-bold text-emerald-800 hover:text-emerald-900 group">
                                <span>Baca Pengumuman</span>
                                <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-2xl border border-slate-200/80">
                <p class="text-slate-500 text-sm sm:text-base font-medium">Belum ada pengumuman publik yang dipublikasikan saat ini.</p>
            </div>
        @endif
    </div>
</section>
