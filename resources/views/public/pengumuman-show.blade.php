@extends('layouts.public')

@section('title', $pengumuman->judul)
@section('meta_description', Str::limit(strip_tags($pengumuman->isi), 155))

@section('content')
@php
    $shortUrl = url('/p/' . $pengumuman->id);
    $fullUrl = route('publik.pengumuman.show', $pengumuman->slug);
    $shareText = "*Warta Resmi Ponpes Al-Falah Putak*\n\n📌 *" . $pengumuman->judul . "*\n\nBaca selengkapnya di:\n" . $shortUrl;
@endphp

<!-- Header Navigasi Breadcrumb (Ringkas & Rapi di Mobile) -->
<div class="bg-slate-100/90 border-b border-slate-200/80 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs text-slate-500 font-medium overflow-x-auto whitespace-nowrap">
            <a href="{{ url('/') }}" class="hover:text-emerald-800 transition shrink-0">Beranda</a>
            <span class="text-slate-300">/</span>
            <a href="{{ url('/pengumuman') }}" class="hover:text-emerald-800 transition shrink-0">Pengumuman</a>
            <span class="text-slate-300">/</span>
            <span class="text-slate-800 font-semibold truncate">{{ $pengumuman->judul }}</span>
        </nav>
    </div>
</div>

<article class="py-6 sm:py-12 bg-white min-h-[600px]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">

            <!-- KONTEN UTAMA ARTIKEL PENGUMUMAN -->
            <div class="lg:col-span-8 space-y-6">

                <!-- Header Berita -->
                <header class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2 text-xs">
                        <span class="px-2.5 py-1 rounded-lg font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200/60">
                            {{ $pengumuman->kategori }}
                        </span>
                        @if($pengumuman->is_pinned)
                            <span class="px-2.5 py-1 rounded-lg font-bold uppercase tracking-wider bg-amber-100 text-amber-900 border border-amber-200 flex items-center gap-1">
                                <span>📌</span>
                                <span>Disematkan</span>
                            </span>
                        @endif
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-500 font-medium flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $pengumuman->formatted_tgl_tayang }}
                        </span>
                    </div>

                    <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-extrabold text-slate-900 leading-snug tracking-tight">
                        {{ $pengumuman->judul }}
                    </h1>
                </header>

                <!-- FOTO UTAMA / BANNER (Proporsional & Respect Posisi Gambar) -->
                @if($pengumuman->gambar)
                    <div class="rounded-2xl overflow-hidden border border-slate-200/90 shadow-xs bg-slate-100 relative">
                        <img src="{{ asset('storage/' . $pengumuman->gambar) }}"
                             alt="{{ $pengumuman->judul }}"
                             class="w-full max-h-[460px] object-cover"
                             style="object-position: {{ $pengumuman->posisi_gambar ?? '50% 50%' }};">
                    </div>
                @endif

                <!-- BAR INTERAKSI: LIKE, KOMENTAR & BAGIKAN (Responsive Mobile & Touch-Friendly) -->
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
                            alert('Teks & Tautan Pendek warta berhasil disalin ke clipboard! Silakan buka Instagram dan tempelkan di Story atau Pesan Anda.');
                            window.open('https://www.instagram.com/', '_blank');
                        });
                    }
                }" class="space-y-6">

                    <!-- Bar Tombol Aksi di Bawah Foto -->
                    <div class="bg-slate-50/95 rounded-2xl border border-slate-200/90 p-2.5 sm:p-3.5 flex items-center justify-between gap-2 shadow-xs">
                        <div class="flex items-center gap-1.5 sm:gap-2.5">
                            <!-- Tombol Like -->
                            <button
                                @click="toggleLike()"
                                :disabled="likeLoading"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-bold transition transform active:scale-95 border"
                                :class="isLiked ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-white text-slate-700 hover:text-rose-600 hover:bg-rose-50/60 border-slate-200'"
                                title="Sukai Warta Ini">
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
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-700 hover:text-emerald-800 hover:bg-emerald-50 border border-slate-200 transition transform active:scale-95"
                                title="Tulis Komentar & Doa">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span class="hidden xs:inline">Komentar</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-800"
                                      x-text="komentarsCount"></span>
                            </button>
                        </div>

                        <!-- Tombol Bagikan dengan Ikon Asli WhatsApp & Modal -->
                        <div class="flex items-center gap-1.5">
                            <!-- Share Cepat WhatsApp Langsung (Ikon Asli WhatsApp) -->
                            <a
                                href="https://api.whatsapp.com/send?text={{ rawurlencode($shareText) }}"
                                target="_blank"
                                @click="triggerShare('whatsapp')"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-bold bg-[#25D366] hover:bg-[#20ba59] text-white shadow-xs transition transform active:scale-95"
                                title="Bagikan ke WhatsApp">
                                <svg class="w-4 h-4 fill-white" viewBox="0 0 24 24">
                                    <path d="M12.031 2C6.514 2 2.02 6.495 2.02 12.012c0 1.954.563 3.782 1.536 5.342L2 22.25l5.064-1.523a9.98 9.98 0 004.967 1.306h.004c5.517 0 10.012-4.496 10.012-10.012 0-2.673-1.042-5.187-2.935-7.08A9.957 9.957 0 0012.031 2zm5.834 14.167c-.246.69-1.22 1.3-1.724 1.378-.456.07-1.046.104-3.376-.86-2.98-1.233-4.903-4.267-5.05-4.464-.146-.197-1.21-1.61-1.21-3.072 0-1.462.766-2.18 1.038-2.477.272-.296.592-.37.79-.37.198 0 .396.002.568.01.185.01.433-.07.677.518.247.592.84 2.052.914 2.2.074.148.123.32.024.518-.098.197-.148.32-.296.494-.148.172-.31.385-.444.518-.148.148-.303.308-.13.604.173.296.768 1.267 1.646 2.05 1.13 1.008 2.083 1.32 2.38 1.468.297.148.47.123.643-.074.173-.197.74-.863.938-1.16.197-.296.395-.246.666-.148.272.099 1.726.814 2.022.962.296.148.494.222.567.345.074.123.074.715-.172 1.405z"/>
                                </svg>
                                <span>WA</span>
                            </a>

                            <!-- Tombol Opsi Bagikan Lainnya -->
                            <button
                                @click="modalShareOpen = true"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-700 hover:text-blue-700 hover:bg-blue-50 border border-slate-200 shadow-xs transition transform active:scale-95"
                                title="Pilihan Bagikan Lainnya">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span class="hidden sm:inline">Bagikan</span>
                                <span class="px-1.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-800"
                                      x-text="sharesCount"></span>
                            </button>
                        </div>
                    </div>

                    <!-- ISI ARTIKEL PENGUMUMAN (Tipografi Rapi & Nyaman Dibaca) -->
                    <div class="text-slate-800 leading-relaxed space-y-4 text-sm sm:text-base border-t border-slate-100 pt-6 font-normal">
                        {!! nl2br(e($pengumuman->isi)) !!}
                    </div>

                    <!-- KOTAK BAGIKAN RESMI DI AKHIR WARTA (Lengkap dengan Ikon Asli WA, IG, FB, Telegram) -->
                    <div class="mt-8 p-5 sm:p-6 rounded-2xl bg-gradient-to-br from-slate-50 to-emerald-50/50 border border-emerald-100/80 space-y-3.5">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-1.5">
                                    <span>📢 Bagikan Warta Ini:</span>
                                </h4>
                                <p class="text-xs text-slate-500">Sebarkan maklumat dan informasi resmi pesantren ini kepada warga & wali santri.</p>
                            </div>
                            <span class="text-[11px] font-mono text-emerald-800 bg-emerald-100/80 px-2.5 py-1 rounded-md font-semibold">
                                {{ $shortUrl }}
                            </span>
                        </div>

                        <!-- Bar Tombol Media Sosial Resmi -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 pt-1">
                            <!-- WhatsApp Asli -->
                            <a
                                href="https://api.whatsapp.com/send?text={{ rawurlencode($shareText) }}"
                                target="_blank"
                                @click="triggerShare('whatsapp')"
                                class="flex items-center justify-center gap-2 p-2.5 rounded-xl bg-[#25D366] hover:bg-[#20ba59] text-white shadow-xs transition font-bold text-xs">
                                <svg class="w-4 h-4 fill-white shrink-0" viewBox="0 0 24 24">
                                    <path d="M12.031 2C6.514 2 2.02 6.495 2.02 12.012c0 1.954.563 3.782 1.536 5.342L2 22.25l5.064-1.523a9.98 9.98 0 004.967 1.306h.004c5.517 0 10.012-4.496 10.012-10.012 0-2.673-1.042-5.187-2.935-7.08A9.957 9.957 0 0012.031 2zm5.834 14.167c-.246.69-1.22 1.3-1.724 1.378-.456.07-1.046.104-3.376-.86-2.98-1.233-4.903-4.267-5.05-4.464-.146-.197-1.21-1.61-1.21-3.072 0-1.462.766-2.18 1.038-2.477.272-.296.592-.37.79-.37.198 0 .396.002.568.01.185.01.433-.07.677.518.247.592.84 2.052.914 2.2.074.148.123.32.024.518-.098.197-.148.32-.296.494-.148.172-.31.385-.444.518-.148.148-.303.308-.13.604.173.296.768 1.267 1.646 2.05 1.13 1.008 2.083 1.32 2.38 1.468.297.148.47.123.643-.074.173-.197.74-.863.938-1.16.197-.296.395-.246.666-.148.272.099 1.726.814 2.022.962.296.148.494.222.567.345.074.123.074.715-.172 1.405z"/>
                                </svg>
                                <span>WhatsApp</span>
                            </a>

                            <!-- Instagram Asli -->
                            <button
                                type="button"
                                @click="shareToInstagram()"
                                class="flex items-center justify-center gap-2 p-2.5 rounded-xl bg-gradient-to-tr from-[#f9ce34] via-[#ee2a7b] to-[#6228d7] text-white shadow-xs hover:opacity-90 transition font-bold text-xs">
                                <svg class="w-4 h-4 fill-white shrink-0" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                <span>Instagram</span>
                            </button>

                            <!-- Facebook Asli -->
                            <a
                                href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shortUrl) }}"
                                target="_blank"
                                @click="triggerShare('facebook')"
                                class="flex items-center justify-center gap-2 p-2.5 rounded-xl bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition font-bold text-xs">
                                <svg class="w-4 h-4 fill-white shrink-0" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Facebook</span>
                            </a>

                            <!-- Telegram Asli -->
                            <a
                                href="https://t.me/share/url?url={{ rawurlencode($shortUrl) }}&text={{ rawurlencode($pengumuman->judul) }}"
                                target="_blank"
                                @click="triggerShare('telegram')"
                                class="flex items-center justify-center gap-2 p-2.5 rounded-xl bg-[#229ED9] hover:bg-[#1e8ec3] text-white shadow-xs transition font-bold text-xs">
                                <svg class="w-4 h-4 fill-white shrink-0" viewBox="0 0 24 24">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                                </svg>
                                <span>Telegram</span>
                            </a>
                        </div>

                        <!-- Tombol Salin Tautan Singkat -->
                        <div class="pt-2">
                            <div class="flex items-center gap-2">
                                <input
                                    type="text"
                                    readonly
                                    value="{{ $shortUrl }}"
                                    class="w-full px-3 py-2 text-xs bg-white border border-slate-200 rounded-xl text-slate-700 font-mono select-all focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                <button
                                    type="button"
                                    @click="copyShortUrl()"
                                    class="shrink-0 inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs"
                                    :class="copied ? 'bg-emerald-600 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white'">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    <span x-text="copiedText"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION DAFTAR KOMENTAR & DOA WARGA PESANTREN -->
                    <div class="pt-8 border-t border-slate-200/80 space-y-5">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm">
                                    💬
                                </div>
                                <h3 class="text-base sm:text-lg font-bold text-slate-900">
                                    Komentar & Doa (<span x-text="komentarsCount"></span>)
                                </h3>
                            </div>
                            <button
                                @click="modalKomentarOpen = true"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition shadow-xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tulis Komentar</span>
                            </button>
                        </div>

                        <!-- Daftar Komentar (Realtime, Bersih & Nyaman di HP) -->
                        <div class="space-y-3">
                            <template x-if="komentars.length === 0">
                                <div class="text-center py-7 px-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Belum ada komentar untuk warta ini.</p>
                                    <button @click="modalKomentarOpen = true" class="mt-1.5 text-xs font-bold text-emerald-800 hover:underline">
                                        Jadilah yang pertama memberikan doa atau tanggapan!
                                    </button>
                                </div>
                            </template>

                            <template x-for="item in komentars" :key="item.id">
                                <div class="p-4 rounded-2xl bg-slate-50/90 border border-slate-200/70 space-y-1.5 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-xs uppercase"
                                                 x-text="item.inisial"></div>
                                            <span class="text-xs sm:text-sm font-bold text-slate-800" x-text="item.nama"></span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 font-medium" x-text="item.waktu"></span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-700 leading-relaxed pl-9 whitespace-pre-line" x-text="item.isi"></p>
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
                                            <h3 class="text-base font-bold text-slate-900">Beri Komentar & Tanggapan</h3>
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

                    <!-- MODAL POP-UP BAGIKAN / SHARE (Lengkap dengan Ikon Asli Media Sosial) -->
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
                                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-base">
                                            ↗️
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900">Bagikan Warta</h3>
                                            <p class="text-xs text-slate-500">Pilih media untuk menyebarkan informasi ini</p>
                                        </div>
                                    </div>
                                    <button @click="modalShareOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-2.5 pt-1">
                                    <!-- WhatsApp Asli -->
                                    <a
                                        href="https://api.whatsapp.com/send?text={{ rawurlencode($shareText) }}"
                                        target="_blank"
                                        @click="triggerShare('whatsapp'); modalShareOpen = false"
                                        class="flex items-center gap-2.5 p-3 rounded-2xl bg-[#25D366] hover:bg-[#20ba59] text-white shadow-xs transition font-bold text-xs">
                                        <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 24 24">
                                            <path d="M12.031 2C6.514 2 2.02 6.495 2.02 12.012c0 1.954.563 3.782 1.536 5.342L2 22.25l5.064-1.523a9.98 9.98 0 004.967 1.306h.004c5.517 0 10.012-4.496 10.012-10.012 0-2.673-1.042-5.187-2.935-7.08A9.957 9.957 0 0012.031 2zm5.834 14.167c-.246.69-1.22 1.3-1.724 1.378-.456.07-1.046.104-3.376-.86-2.98-1.233-4.903-4.267-5.05-4.464-.146-.197-1.21-1.61-1.21-3.072 0-1.462.766-2.18 1.038-2.477.272-.296.592-.37.79-.37.198 0 .396.002.568.01.185.01.433-.07.677.518.247.592.84 2.052.914 2.2.074.148.123.32.024.518-.098.197-.148.32-.296.494-.148.172-.31.385-.444.518-.148.148-.303.308-.13.604.173.296.768 1.267 1.646 2.05 1.13 1.008 2.083 1.32 2.38 1.468.297.148.47.123.643-.074.173-.197.74-.863.938-1.16.197-.296.395-.246.666-.148.272.099 1.726.814 2.022.962.296.148.494.222.567.345.074.123.074.715-.172 1.405z"/>
                                        </svg>
                                        <span>WhatsApp</span>
                                    </a>

                                    <!-- Instagram Asli -->
                                    <button
                                        type="button"
                                        @click="shareToInstagram(); modalShareOpen = false"
                                        class="flex items-center gap-2.5 p-3 rounded-2xl bg-gradient-to-tr from-[#f9ce34] via-[#ee2a7b] to-[#6228d7] text-white shadow-xs hover:opacity-90 transition font-bold text-xs text-left">
                                        <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        </svg>
                                        <span>Instagram</span>
                                    </button>

                                    <!-- Facebook Asli -->
                                    <a
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode($shortUrl) }}"
                                        target="_blank"
                                        @click="triggerShare('facebook'); modalShareOpen = false"
                                        class="flex items-center gap-2.5 p-3 rounded-2xl bg-[#1877F2] hover:bg-[#166fe5] text-white shadow-xs transition font-bold text-xs">
                                        <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        <span>Facebook</span>
                                    </a>

                                    <!-- Telegram Asli -->
                                    <a
                                        href="https://t.me/share/url?url={{ rawurlencode($shortUrl) }}&text={{ rawurlencode($pengumuman->judul) }}"
                                        target="_blank"
                                        @click="triggerShare('telegram'); modalShareOpen = false"
                                        class="flex items-center gap-2.5 p-3 rounded-2xl bg-[#229ED9] hover:bg-[#1e8ec3] text-white shadow-xs transition font-bold text-xs">
                                        <svg class="w-5 h-5 fill-white shrink-0" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm4.64 6.8c-.15 1.58-.8 5.42-1.13 7.19-.14.75-.42 1-.68 1.03-.58.05-1.02-.38-1.58-.75-.88-.58-1.38-.94-2.23-1.5-.99-.65-.35-1.01.22-1.59.15-.15 2.71-2.48 2.76-2.69a.2.2 0 00-.05-.18c-.06-.05-.14-.03-.21-.02-.09.02-1.49.95-4.22 2.79-.4.27-.76.41-1.08.4-.36-.01-1.04-.2-1.55-.37-.63-.2-1.12-.31-1.08-.66.02-.18.27-.36.74-.55 2.92-1.27 4.86-2.11 5.83-2.51 2.78-1.16 3.35-1.36 3.73-1.36.08 0 .27.02.39.12.1.08.13.19.14.27-.01.06.01.24 0 .38z"/>
                                        </svg>
                                        <span>Telegram</span>
                                    </a>
                                </div>

                                <!-- Salin Tautan Singkat -->
                                <div class="pt-3 border-t border-slate-100 space-y-1.5">
                                    <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-wider">Tautan Pendek:</label>
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="text"
                                            readonly
                                            value="{{ $shortUrl }}"
                                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-700 font-mono select-all focus:outline-none">
                                        <button
                                            @click="copyShortUrl()"
                                            class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition shadow-xs"
                                            :class="copied ? 'bg-emerald-600 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white'">
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
