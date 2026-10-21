@extends('layouts.public')

@section('title', $pengumuman->judul)

@section('content')
<!-- Header Navigasi Breadcrumb -->
<div class="bg-slate-100/80 border-b border-slate-200/80 py-3.5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium">
            <a href="{{ url('/') }}" class="hover:text-emerald-800 transition">Beranda</a>
            <span>/</span>
            <a href="{{ url('/pengumuman') }}" class="hover:text-emerald-800 transition">Pengumuman</a>
            <span>/</span>
            <span class="text-slate-800 font-semibold truncate max-w-xs sm:max-w-md">{{ $pengumuman->judul }}</span>
        </nav>
    </div>
</div>

<article class="py-10 sm:py-16 bg-white min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">

            <!-- Konten Utama Pengumuman -->
            <div class="lg:col-span-8 space-y-6">
                <header class="space-y-3.5">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">
                            {{ $pengumuman->kategori }}
                        </span>
                        @if($pengumuman->is_pinned)
                            <span class="px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wider bg-amber-100 text-amber-900">
                                Disematkan
                            </span>
                        @endif
                        <span class="text-xs text-slate-500">
                            Dipublikasikan pada {{ $pengumuman->formatted_tgl_tayang }}
                        </span>
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-slate-900 leading-tight">
                        {{ $pengumuman->judul }}
                    </h1>
                </header>

                @if($pengumuman->gambar)
                    <div class="rounded-2xl overflow-hidden border border-slate-200/80 shadow-xs bg-slate-100">
                        <img src="{{ asset('storage/' . $pengumuman->gambar) }}"
                             alt="{{ $pengumuman->judul }}"
                             class="w-full max-h-[460px] object-cover"
                             style="object-position: {{ $pengumuman->posisi_gambar ?? '50% 50%' }};">
                    </div>
                @endif

                <!-- Isi Artikel Pengumuman -->
                <div class="text-slate-700 leading-relaxed space-y-4 text-sm sm:text-base border-t border-slate-100 pt-6">
                    {!! nl2br(e($pengumuman->isi)) !!}
                </div>

                <div class="pt-8 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ url('/pengumuman') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Pengumuman</span>
                    </a>
                </div>
            </div>

            <!-- Sidebar Informasi & Pengumuman Terkait -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Kontak Sekretariat -->
                <div class="p-6 rounded-2xl bg-emerald-950 text-white space-y-3.5 shadow-sm">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Informasi Sekretariat</span>
                    </h3>
                    <p class="text-xs text-emerald-100/90 leading-relaxed">
                        Untuk klarifikasi pengumuman resmi atau konfirmasi jadwal kegiatan santri, silakan hubungi kontak sekretariat:
                    </p>
                    <div class="pt-2 text-xs space-y-1.5 text-emerald-200 border-t border-emerald-900">
                        <p><strong class="text-white">Telepon:</strong> {{ $pengaturan['kontak'] ?? '0851-8320-1627' }}</p>
                        <p><strong class="text-white">Alamat:</strong> {{ $pengaturan['alamat'] ?? 'Desa Putak, Kec. Gelumbang, Kab. Muara Enim' }}</p>
                        <p><strong class="text-white">Jam Layanan:</strong> Sabtu - Kamis (08:00 - 17:00 WIB)</p>
                    </div>
                </div>

                <!-- Pengumuman Lainnya -->
                @if(isset($terkait) && $terkait->count() > 0)
                    <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200/80 pb-2.5">
                            Pengumuman Lainnya
                        </h3>
                        <div class="space-y-3.5">
                            @foreach($terkait as $item)
                                <article class="space-y-1">
                                    <span class="text-[11px] font-semibold text-emerald-800 block">
                                        {{ $item->formatted_tgl_tayang }}
                                    </span>
                                    <h4 class="text-xs sm:text-sm font-bold text-slate-900 hover:text-emerald-800 leading-snug transition">
                                        <a href="{{ url('/pengumuman/' . $item->slug) }}">
                                            {{ $item->judul }}
                                        </a>
                                    </h4>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</article>
@endsection
