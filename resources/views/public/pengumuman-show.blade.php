@extends('layouts.public')

@section('title', $pengumuman->judul)
@section('meta_description', Str::limit(strip_tags($pengumuman->isi), 170))
@section('og_image', $pengumuman->gambar ? asset('storage/' . $pengumuman->gambar) : asset('images/og-alfalah-putak.jpg'))

@section('extra_schema')
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "NewsArticle",
    "mainEntityOfPage": {
        "@@type": "WebPage",
        "@@id": "{{ url()->current() }}"
    },
    "headline": {{ json_encode($pengumuman->judul) }},
    "description": {{ json_encode(Str::limit(strip_tags($pengumuman->isi), 170)) }},
    "image": [
        "{{ $pengumuman->gambar ? asset('storage/' . $pengumuman->gambar) : asset('images/og-alfalah-putak.jpg') }}"
    ],
    "datePublished": "{{ $pengumuman->tgl_tayang ? $pengumuman->tgl_tayang->toIso8601String() : $pengumuman->created_at->toIso8601String() }}",
    "dateModified": "{{ $pengumuman->updated_at->toIso8601String() }}",
    "author": {
        "@@type": "Organization",
        "name": "Sekretariat Ponpes Al-Falah Putak",
        "url": "{{ url('/') }}"
    },
    "publisher": {
        "@@type": "EducationalOrganization",
        "name": "Pondok Pesantren Salafiyah Al-Falah Putak",
        "logo": {
            "@@type": "ImageObject",
            "url": "{{ asset('favicon-192x192.png') }}"
        }
    }
}
</script>
@endsection

@section('content')
@php
    $shortUrl = url('/p/' . $pengumuman->id);
    $shareText = "*Warta Resmi Ponpes Al-Falah Putak*\n\n📌 *" . $pengumuman->judul . "*\n\nBaca selengkapnya di:\n" . $shortUrl;
@endphp

<!-- Header Navigasi Breadcrumb (Ringkas & Bersih di HP) -->
<div class="bg-slate-50 border-b border-slate-200/80 py-2.5 sm:py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-1.5 sm:gap-2 text-xs text-slate-500 font-medium overflow-x-auto whitespace-nowrap">
            <a href="{{ url('/') }}" class="hover:text-emerald-800 transition shrink-0">Beranda</a>
            <span class="text-slate-300">/</span>
            <a href="{{ url('/pengumuman') }}" class="hover:text-emerald-800 transition shrink-0">Pengumuman</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-700 font-semibold truncate">{{ Str::limit($pengumuman->judul, 45) }}</span>
        </nav>
    </div>
</div>

<article class="py-6 sm:py-12 bg-white min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            <!-- KONTEN UTAMA ARTIKEL PENGUMUMAN -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Header Judul Berita -->
                <header class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="px-3 py-1 rounded-full font-bold uppercase tracking-wider text-[11px] bg-emerald-100 text-emerald-800 border border-emerald-200/80">
                            {{ $pengumuman->kategori }}
                        </span>
                        @if($pengumuman->is_pinned)
                            <span class="px-2.5 py-1 rounded-full font-bold uppercase tracking-wider text-[11px] bg-amber-100 text-amber-900 border border-amber-200 flex items-center gap-1">
                                <span>📌</span>
                                <span>Disematkan</span>
                            </span>
                        @endif
                        <span class="text-slate-300">•</span>
                        <span class="text-slate-500 font-medium flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $pengumuman->formatted_tgl_tayang }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-3xl md:text-4xl font-extrabold text-slate-900 leading-snug tracking-tight">
                        {{ $pengumuman->judul }}
                    </h1>
                </header>

                <!-- FOTO UTAMA / BANNER (Proporsional & Mematuhi Posisi Gambar) -->
                @if($pengumuman->gambar)
                    <div class="rounded-2xl sm:rounded-3xl overflow-hidden border border-slate-200/90 shadow-xs bg-slate-100 relative">
                        <img src="{{ asset('storage/' . $pengumuman->gambar) }}"
                             alt="{{ $pengumuman->judul }}"
                             class="w-full max-h-[460px] object-cover"
                             style="object-position: {{ $pengumuman->posisi_gambar ?? '50% 50%' }};">
                    </div>
                @endif

                <!-- BAR INTERAKSI: LIKE, KOMENTAR & BAGIKAN -->
                <div x-data="{
                    isLiked: {{ $isLiked ? 'true' : 'false' }},
                    likesCount: {{ (int) $likesCount }},
                    komentarsCount: {{ (int) $komentarsCount }},
                    sharesCount: {{ (int) $sharesCount }},
                    likeLoading: false,

                    // Modal Komentar Pop-up
                    modalKomentarOpen: false,
                    nama: '',
                    isi: '',
                    komentarLoading: false,
                    komentarSuccess: '',
                    komentarError: '',

                    // Modal Bagikan Pop-up
                    modalShareOpen: false,
                    copied: false,
                    copiedText: 'Salin Tautan',

                    // Daftar Komentar Realtime
                    komentars: @js($komentars->map(fn($k) => [
                        'id' => $k->id,
                        'nama' => $k->nama_tampil,
                        'inisial' => strtoupper(substr($k->nama_tampil, 0, 1)),
                        'isi' => e($k->isi),
                        'waktu' => $k->created_at->diffForHumans()
                    ])->values()),

                    // Batasan Komentar: Hanya Tampilkan 3 Terbaru secara Default
                    showAllKomentar: false,
                    get displayedKomentars() {
                        return this.showAllKomentar ? this.komentars : this.komentars.slice(0, 3);
                    },

                    async toggleLike() {
                        if (this.likeLoading) return;
                        this.likeLoading = true;
                        try {
                            const res = await fetch('{{ route('publik.pengumuman.like', $pengumuman->slug) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.isLiked = data.liked;
                                this.likesCount = data.likes_count;
                            }
                        } catch (e) {
                            console.error(e);
                        } finally {
                            this.likeLoading = false;
                        }
                    },

                    async submitKomentar() {
                        if (!this.isi.trim()) {
                            this.komentarError = 'Isi komentar wajib diisi.';
                            return;
                        }
                        this.komentarLoading = true;
                        this.komentarError = '';
                        this.komentarSuccess = '';
                        try {
                            const res = await fetch('{{ route('publik.pengumuman.komentar', $pengumuman->slug) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    nama: this.nama,
                                    isi: this.isi
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.komentarSuccess = data.message;
                                this.komentarsCount = data.komentars_count;
                                this.komentars.unshift(data.komentar);
                                this.nama = '';
                                this.isi = '';
                                setTimeout(() => {
                                    this.modalKomentarOpen = false;
                                    this.komentarSuccess = '';
                                }, 1500);
                            } else {
                                this.komentarError = data.message || 'Gagal mengirim komentar.';
                            }
                        } catch (e) {
                            this.komentarError = 'Terjadi kesalahan sistem. Silakan coba kembali.';
                        } finally {
                            this.komentarLoading = false;
                        }
                    },

                    async triggerShare(platform) {
                        try {
                            const res = await fetch('{{ route('publik.pengumuman.share', $pengumuman->slug) }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ platform })
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.sharesCount = data.shares_count;
                            }
                        } catch (e) {}
                    },

                    copyShortUrl() {
                        const url = '{{ $shortUrl }}';
                        navigator.clipboard.writeText(url).then(() => {
                            this.copied = true;
                            this.copiedText = 'Tersalin!';
                            this.triggerShare('salin_tautan');
                            setTimeout(() => {
                                this.copied = false;
                                this.copiedText = 'Salin Tautan';
                            }, 2500);
                        });
                    },

                    shareToInstagram() {
                        const text = @js($shareText);
                        navigator.clipboard.writeText(text).then(() => {
                            this.triggerShare('instagram');
                            alert('Teks & Tautan Pendek warta berhasil disalin! Anda dapat membagikannya ke Instagram Story atau Pesan (DM).');
                            window.open('https://www.instagram.com/', '_blank');
                        });
                    }
                }" class="space-y-6">

                    <!-- Bar Tombol Interaksi Tepat di Bawah Banner -->
                    <div class="bg-slate-50/90 rounded-2xl border border-slate-200/90 p-2.5 sm:p-3 flex items-center justify-between gap-2 shadow-xs">
                        <div class="flex items-center gap-1.5 sm:gap-2">
                            <!-- Tombol Like -->
                            <button
                                @click="toggleLike()"
                                :disabled="likeLoading"
                                class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold transition transform active:scale-95 border"
                                :class="isLiked ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-white text-slate-700 hover:text-rose-600 hover:bg-rose-50/60 border-slate-200'"
                                title="Sukai Warta">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform" :class="{'scale-110 fill-rose-600 text-rose-600': isLiked, 'fill-none text-current': !isLiked}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span class="hidden xs:inline" x-text="isLiked ? 'Disukai' : 'Suka'"></span>
                                <span class="px-1.5 py-0.5 rounded-full text-[11px] font-semibold"
                                      :class="isLiked ? 'bg-rose-200/70 text-rose-800' : 'bg-slate-100 text-slate-600'"
                                      x-text="likesCount"></span>
                            </button>

                            <!-- Tombol Komentar -->
                            <button
                                @click="modalKomentarOpen = true"
                                class="inline-flex items-center gap-1.5 min-h-[44px] px-3.5 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-700 hover:text-emerald-800 hover:bg-emerald-50 border border-slate-200 transition transform active:scale-95"
                                title="Tulis Komentar & Doa">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span class="hidden xs:inline">Komentar</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800"
                                      x-text="komentarsCount"></span>
                            </button>
                        </div>

                        <!-- Tombol Cepat Bagikan -->
                        <div class="flex items-center gap-1.5">
                            <!-- Tombol Langsung WhatsApp -->
                            <a
                                href="https://api.whatsapp.com/send?text={{ rawurlencode($shareText) }}"
                                target="_blank"
                                @click="triggerShare('whatsapp')"
                                style="background-color: #25D366; color: #ffffff;"
                                class="inline-flex items-center gap-1.5 min-h-[44px] px-3 py-2 rounded-xl text-xs sm:text-sm font-bold shadow-xs hover:opacity-95 transition transform active:scale-95"
                                title="Bagikan ke WhatsApp">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>Bagikan</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[11px] font-semibold bg-white/20 text-white"
                                      x-text="sharesCount"></span>
                            </a>

                            <!-- Tombol Buka Modal Share -->
                            <button
                                @click="modalShareOpen = true"
                                class="inline-flex items-center justify-center min-h-[44px] min-w-[44px] px-3 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-700 hover:text-emerald-800 hover:bg-slate-100 border border-slate-200 transition transform active:scale-95"
                                title="Pilihan Media Lainnya">
                                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span class="hidden sm:inline ml-1">Lainnya</span>
                            </button>
                        </div>
                    </div>

                    <!-- ISI ARTIKEL WARTA PENGUMUMAN -->
                    <div class="text-slate-800 leading-relaxed sm:leading-loose space-y-4 sm:space-y-5 text-sm sm:text-base md:text-[17px] border-t border-slate-100 pt-6">
                        {!! $pengumuman->formatted_isi !!}
                    </div>

                    <!-- BAGIKAN WARTA (Simpel, Rapi, & Hemat Tempat) -->
                    <div class="mt-8 pt-5 border-t border-slate-200/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-500 uppercase tracking-wider">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                            </svg>
                            <span>Bagikan warta (<span x-text="sharesCount"></span>):</span>
                        </div>

                        <!-- Barisan Tombol Ikon Media Sosial Sederhana -->
                        <div class="flex items-center flex-wrap gap-2">
                            <!-- WhatsApp -->
                            <a
                                href="https://api.whatsapp.com/send?text={{ rawurlencode($shareText) }}"
                                target="_blank"
                                @click="triggerShare('whatsapp')"
                                style="background-color: #25D366; color: #ffffff;"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-xs hover:opacity-90 active:scale-95 transition"
                                title="Bagikan ke WhatsApp">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                </svg>
                                <span>WhatsApp</span>
                            </a>

                            <!-- Instagram -->
                            <button
                                type="button"
                                @click="shareToInstagram()"
                                style="background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045); color: #ffffff;"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold shadow-xs hover:opacity-90 active:scale-95 transition"
                                title="Bagikan ke Instagram">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                <span>Instagram</span>
                            </button>

                            <!-- Facebook -->
                            <a
                                href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shortUrl) }}"
                                target="_blank"
                                @click="triggerShare('facebook')"
                                style="background-color: #1877F2; color: #ffffff;"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl shadow-xs hover:opacity-90 active:scale-95 transition"
                                title="Bagikan ke Facebook">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                            </a>

                            <!-- Telegram -->
                            <a
                                href="https://t.me/share/url?url={{ rawurlencode($shortUrl) }}&text={{ rawurlencode($pengumuman->judul) }}"
                                target="_blank"
                                @click="triggerShare('telegram')"
                                style="background-color: #229ED9; color: #ffffff;"
                                class="inline-flex items-center justify-center w-8 h-8 rounded-xl shadow-xs hover:opacity-90 active:scale-95 transition"
                                title="Bagikan ke Telegram">
                                <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                                </svg>
                            </a>

                            <!-- Salin Tautan Singkat -->
                            <button
                                type="button"
                                @click="copyShortUrl()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border transition shadow-xs active:scale-95"
                                :class="copied ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-slate-100 text-slate-700 hover:bg-slate-200 border-slate-200'"
                                title="Salin tautan: {{ $shortUrl }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copiedText"></span>
                            </button>
                        </div>
                    </div>

                    <!-- SECTION DAFTAR KOMENTAR & DOA (Hanya 3 Terbaru) -->
                    <div class="pt-8 border-t border-slate-200/80 space-y-4">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                                    💬
                                </div>
                                <div>
                                    <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-tight">
                                        Komentar & Doa Terbaru (<span x-text="komentarsCount"></span>)
                                    </h3>
                                    <template x-if="komentars.length > 3 && !showAllKomentar">
                                        <p class="text-[11px] text-slate-400">Menampilkan 3 komentar terbaru</p>
                                    </template>
                                </div>
                            </div>
                            <button
                                @click="modalKomentarOpen = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tulis Komentar</span>
                            </button>
                        </div>

                        <!-- Daftar Komentar (Realtime, Hanya 3 Terbaru secara Default) -->
                        <div class="space-y-3">
                            <template x-if="komentars.length === 0">
                                <div class="text-center py-7 px-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Belum ada komentar untuk warta ini.</p>
                                    <button @click="modalKomentarOpen = true" class="mt-1.5 text-xs font-bold text-emerald-800 hover:underline">
                                        Jadilah yang pertama memberikan doa atau tanggapan!
                                    </button>
                                </div>
                            </template>

                            <template x-for="item in displayedKomentars" :key="item.id">
                                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-50/90 border border-slate-200/70 space-y-1.5 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-xs uppercase shrink-0"
                                                 x-text="item.inisial"></div>
                                            <span class="text-xs sm:text-sm font-bold text-slate-800" x-text="item.nama"></span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 font-medium" x-text="item.waktu"></span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed pl-9 whitespace-pre-line" x-text="item.isi"></p>
                                </div>
                            </template>

                            <!-- Tombol Selengkapnya Jika Lebih dari 3 Komentar -->
                            <template x-if="komentars.length > 3">
                                <div class="text-center pt-2">
                                    <button
                                        type="button"
                                        @click="showAllKomentar = !showAllKomentar"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition border border-emerald-200/80 shadow-2xs">
                                        <span x-text="showAllKomentar ? 'Tampilkan 3 Komentar Saja' : 'Lihat Semua Komentar (' + komentars.length + ')'"></span>
                                        <svg class="w-3.5 h-3.5 transition-transform" :class="showAllKomentar ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- MODAL POP-UP KOMENTAR (Mobile Responsive) -->
                    <div x-show="modalKomentarOpen"
                         x-cloak
                         class="fixed inset-0 z-50 overflow-y-auto"
                         role="dialog"
                         aria-modal="true"
                         style="display: none;">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                             @click="modalKomentarOpen = false"></div>

                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 p-5 sm:p-7 space-y-4"
                                 @click.stop>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-base">
                                            💬
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900">Beri Komentar & Doa</h3>
                                            <p class="text-xs text-slate-500">Tanggapan publik untuk warta ini</p>
                                        </div>
                                    </div>
                                    <button @click="modalKomentarOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <template x-if="komentarSuccess">
                                    <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold"
                                         x-text="komentarSuccess"></div>
                                </template>

                                <template x-if="komentarError">
                                    <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold"
                                         x-text="komentarError"></div>
                                </template>

                                <form @submit.prevent="submitKomentar()" class="space-y-3.5">
                                    <div>
                                        <label for="nama_komentar" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                            Nama Anda (Opsional)
                                        </label>
                                        <input
                                            type="text"
                                            id="nama_komentar"
                                            x-model="nama"
                                            placeholder="Contoh: Hamba Allah / Wali Santri"
                                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500">
                                    </div>

                                    <div>
                                        <label for="isi_komentar" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                            Isi Komentar / Doa <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea
                                            id="isi_komentar"
                                            rows="4"
                                            x-model="isi"
                                            required
                                            placeholder="Tuliskan tanggapan, doa, atau pertanyaan Anda..."
                                            class="w-full px-3.5 py-2.5 text-xs sm:text-sm rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 leading-relaxed"></textarea>
                                    </div>

                                    <div class="pt-2 flex items-center justify-end gap-2.5 border-t border-slate-100">
                                        <button
                                            type="button"
                                            @click="modalKomentarOpen = false"
                                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 transition">
                                            Batal
                                        </button>
                                        <button
                                            type="submit"
                                            :disabled="komentarLoading"
                                            class="inline-flex items-center gap-2 px-5 py-2 rounded-xl text-xs sm:text-sm font-bold text-white bg-emerald-800 hover:bg-emerald-900 transition disabled:opacity-50 shadow-xs">
                                            <svg x-show="komentarLoading" x-cloak class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                                            <span x-text="komentarLoading ? 'Mengirim...' : 'Kirim Komentar'"></span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL POP-UP BAGIKAN / SHARE (Sangat Mewah, Berwarna Resmi & Rapi di Mobile) -->
                    <div x-show="modalShareOpen"
                         x-cloak
                         class="fixed inset-0 z-50 overflow-y-auto"
                         role="dialog"
                         aria-modal="true"
                         style="display: none;">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                             @click="modalShareOpen = false"></div>

                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 p-5 sm:p-6 space-y-4"
                                 @click.stop>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-base font-bold">
                                            ↗️
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900">Bagikan Warta Ini</h3>
                                            <p class="text-xs text-slate-500">Pilih media sosial resmi di bawah</p>
                                        </div>
                                    </div>
                                    <button @click="modalShareOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <!-- 4 Kartu Media Sosial Resmi Berwarna Cerah & Jelas -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                    <!-- WhatsApp Asli -->
                                    <a
                                        href="https://api.whatsapp.com/send?text={{ rawurlencode($shareText) }}"
                                        target="_blank"
                                        @click="triggerShare('whatsapp'); modalShareOpen = false"
                                        style="background-color: #25D366; color: #ffffff;"
                                        class="flex items-center gap-3 p-3 rounded-2xl shadow-xs hover:opacity-95 transition font-bold text-xs active:scale-98">
                                        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="20" height="20" fill="#ffffff">
                                                <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-bold text-xs sm:text-sm">WhatsApp</div>
                                            <div class="text-[10px] text-emerald-100">Kirim pesan langsung</div>
                                        </div>
                                    </a>

                                    <!-- Instagram Asli -->
                                    <button
                                        type="button"
                                        @click="shareToInstagram(); modalShareOpen = false"
                                        style="background: linear-gradient(135deg, #833ab4, #fd1d1d, #fcb045); color: #ffffff;"
                                        class="flex items-center gap-3 p-3 rounded-2xl shadow-xs hover:opacity-95 transition font-bold text-xs text-left active:scale-98">
                                        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="20" height="20" fill="#ffffff">
                                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-bold text-xs sm:text-sm">Instagram</div>
                                            <div class="text-[10px] text-pink-100">Salin & buka IG</div>
                                        </div>
                                    </button>

                                    <!-- Facebook Asli -->
                                    <a
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shortUrl) }}"
                                        target="_blank"
                                        @click="triggerShare('facebook'); modalShareOpen = false"
                                        style="background-color: #1877F2; color: #ffffff;"
                                        class="flex items-center gap-3 p-3 rounded-2xl shadow-xs hover:opacity-95 transition font-bold text-xs active:scale-98">
                                        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="20" height="20" fill="#ffffff">
                                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-bold text-xs sm:text-sm">Facebook</div>
                                            <div class="text-[10px] text-blue-100">Berbagi ke Beranda</div>
                                        </div>
                                    </a>

                                    <!-- Telegram Asli -->
                                    <a
                                        href="https://t.me/share/url?url={{ rawurlencode($shortUrl) }}&text={{ rawurlencode($pengumuman->judul) }}"
                                        target="_blank"
                                        @click="triggerShare('telegram'); modalShareOpen = false"
                                        style="background-color: #229ED9; color: #ffffff;"
                                        class="flex items-center gap-3 p-3 rounded-2xl shadow-xs hover:opacity-95 transition font-bold text-xs active:scale-98">
                                        <div class="w-9 h-9 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                                            <svg viewBox="0 0 24 24" width="20" height="20" fill="#ffffff">
                                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                                            </svg>
                                        </div>
                                        <div class="text-left">
                                            <div class="font-bold text-xs sm:text-sm">Telegram</div>
                                            <div class="text-[10px] text-sky-100">Kirim ke kontak/grup</div>
                                        </div>
                                    </a>
                                </div>

                                <!-- Salin Tautan Pendek -->
                                <div class="pt-3 border-t border-slate-100 space-y-1.5">
                                    <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider">Tautan Pendek Resmi:</label>
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $shortUrl }}"
                                            class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-800 font-mono font-semibold select-all focus:outline-none">
                                        <button
                                            @click="copyShortUrl()"
                                            style="background-color: #065f46; color: #ffffff;"
                                            class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-xs active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Tombol Kembali -->
                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ url('/pengumuman') }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs sm:text-sm font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shadow-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        <span>Kembali ke Daftar Warta</span>
                    </a>
                </div>
            </div>

            <!-- SIDEBAR INFORMASI & WARTA LAINNYA -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Kontak Sekretariat -->
                <div class="p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-emerald-950 to-emerald-900 text-white space-y-3.5 shadow-sm border border-emerald-800/80">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span>Informasi Sekretariat</span>
                    </h3>
                    <p class="text-xs text-emerald-100/90 leading-relaxed">
                        Untuk klarifikasi maklumat atau konfirmasi kegiatan santri, silakan hubungi saluran resmi sekretariat:
                    </p>
                    <div class="pt-2 text-xs space-y-2 text-emerald-200 border-t border-emerald-900">
                        <p class="flex items-start gap-2">
                            <span class="text-white font-bold shrink-0">Telepon/WA:</span>
                            <span>{{ $pengaturan['kontak'] ?? '0851-8320-1627' }}</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-white font-bold shrink-0">Alamat:</span>
                            <span class="leading-relaxed">{{ $pengaturan['alamat'] ?? 'Desa Putak, Kec. Gelumbang, Kab. Muara Enim' }}</span>
                        </p>
                        <p class="flex items-start gap-2">
                            <span class="text-white font-bold shrink-0">Jam Layanan:</span>
                            <span>Sabtu – Kamis (08:00 – 17:00 WIB)</span>
                        </p>
                    </div>
                </div>

                <!-- Warta Terkait Lainnya (Thumbnail Rapi) -->
                @if(isset($terkait) && $terkait->count() > 0)
                    <div class="p-5 sm:p-6 rounded-2xl bg-slate-50 border border-slate-200/90 space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 border-b border-slate-200/80 pb-2.5 flex items-center justify-between">
                            <span>Warta Lainnya</span>
                            <a href="{{ url('/pengumuman') }}" class="text-[11px] font-bold text-emerald-800 hover:underline">Lihat Semua →</a>
                        </h3>
                        <div class="space-y-3.5">
                            @foreach($terkait as $item)
                                <article class="flex items-start gap-3 group">
                                    @if($item->gambar)
                                        <div class="w-16 h-14 rounded-xl overflow-hidden bg-slate-200 shrink-0">
                                            <img src="{{ asset('storage/' . $item->gambar) }}"
                                                 alt="{{ $item->judul }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition"
                                                 style="object-position: {{ $item->posisi_gambar ?? '50% 50%' }};">
                                        </div>
                                    @else
                                        <div class="w-16 h-14 rounded-xl bg-emerald-900 text-white flex items-center justify-center text-xs font-bold shrink-0">
                                            📰
                                        </div>
                                    @endif
                                    <div class="space-y-1">
                                        <span class="text-[11px] font-medium text-slate-400 block">
                                            {{ $item->formatted_tgl_tayang }}
                                        </span>
                                        <h4 class="text-xs sm:text-sm font-bold text-slate-800 group-hover:text-emerald-800 leading-snug transition line-clamp-2">
                                            <a href="{{ url('/pengumuman/' . $item->slug) }}">
                                                {{ $item->judul }}
                                            </a>
                                        </h4>
                                    </div>
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
