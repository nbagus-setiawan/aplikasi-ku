@extends('layouts.public')

@section('title', $pengumuman->judul)
@section('meta_description', Str::limit(strip_tags($pengumuman->isi), 155))

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

                <!-- BAR INTERAKSI: LIKE, KOMENTAR, SHARE (Tepat di Bawah Foto) -->
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

                    copyLink() {
                        navigator.clipboard.writeText(window.location.href);
                        this.copied = true;
                        this.triggerShare('salin_tautan');
                        setTimeout(() => this.copied = false, 2500);
                    }
                }" class="space-y-6">

                    <!-- Bar Tombol Like, Komentar, Share -->
                    <div class="bg-slate-50/90 rounded-2xl border border-slate-200/90 p-3 sm:p-4 flex flex-wrap items-center justify-between gap-3 shadow-xs">
                        <div class="flex items-center gap-2 sm:gap-3 flex-wrap">
                            <!-- Tombol Like -->
                            <button
                                @click="toggleLike()"
                                :disabled="likeLoading"
                                class="inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition transform active:scale-95 border"
                                :class="isLiked ? 'bg-rose-50 text-rose-600 border-rose-200 shadow-xs' : 'bg-white text-slate-700 hover:text-rose-600 hover:bg-rose-50/60 border-slate-200/80'"
                                title="Sukai Berita Ini">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 transition-transform" :class="{'scale-110 fill-rose-600 text-rose-600': isLiked, 'fill-none text-current': !isLiked}" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                                <span x-text="isLiked ? 'Disukai' : 'Suka'"></span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold"
                                      :class="isLiked ? 'bg-rose-200/70 text-rose-800' : 'bg-slate-100 text-slate-600'"
                                      x-text="likesCount"></span>
                            </button>

                            <!-- Tombol Komentar (Buka Pop-up) -->
                            <button
                                @click="modalKomentarOpen = true"
                                class="inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-700 hover:text-emerald-800 hover:bg-emerald-50/70 border border-slate-200/80 shadow-xs transition transform active:scale-95"
                                title="Tulis Komentar Pop-up">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                                <span>Komentar</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800"
                                      x-text="komentarsCount"></span>
                            </button>
                        </div>

                        <!-- Tombol Bagikan / Share (Buka Pop-up) -->
                        <div>
                            <button
                                @click="modalShareOpen = true"
                                class="inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 rounded-xl text-xs sm:text-sm font-bold bg-white text-slate-700 hover:text-blue-700 hover:bg-blue-50/70 border border-slate-200/80 shadow-xs transition transform active:scale-95"
                                title="Bagikan Berita Ini">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                                </svg>
                                <span>Bagikan</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800"
                                      x-text="sharesCount"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Isi Artikel Pengumuman -->
                    <div class="text-slate-700 leading-relaxed space-y-4 text-sm sm:text-base border-t border-slate-100 pt-6">
                        {!! nl2br(e($pengumuman->isi)) !!}
                    </div>

                    <!-- SECTION DAFTAR KOMENTAR PUBLIK & WARGA PESANTREN -->
                    <div class="pt-8 border-t border-slate-200/80 space-y-6">
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
                                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                <span>Tulis Komentar</span>
                            </button>
                        </div>

                        <!-- Daftar Komentar (Realtime & Bersih) -->
                        <div class="space-y-3.5">
                            <template x-if="komentars.length === 0">
                                <div class="text-center py-8 px-4 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                                    <p class="text-xs sm:text-sm text-slate-500 font-medium">Belum ada komentar untuk pengumuman ini.</p>
                                    <button @click="modalKomentarOpen = true" class="mt-2 text-xs font-bold text-emerald-800 hover:underline">
                                        Jadilah yang pertama memberikan komentar & doa!
                                    </button>
                                </div>
                            </template>

                            <template x-for="item in komentars" :key="item.id">
                                <div class="p-4 rounded-2xl bg-slate-50/80 border border-slate-200/70 space-y-2 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-7 h-7 rounded-full bg-emerald-700 text-white flex items-center justify-center font-bold text-xs uppercase"
                                                 x-text="item.inisial"></div>
                                            <span class="text-xs sm:text-sm font-bold text-slate-800" x-text="item.nama"></span>
                                        </div>
                                        <span class="text-[11px] text-slate-400 font-medium" x-text="item.waktu"></span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed pl-9 whitespace-pre-line" x-text="item.isi"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- MODAL POP-UP KOMENTAR (Tanpa Refresh Halaman) -->
                    <div x-show="modalKomentarOpen"
                         x-cloak
                         class="fixed inset-0 z-50 overflow-y-auto"
                         role="dialog"
                         aria-modal="true"
                         style="display: none;">
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                             @click="modalKomentarOpen = false"></div>

                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-100 p-6 sm:p-7 space-y-4"
                                 @click.stop>
                                <!-- Header Modal -->
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-base">
                                            💬
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900">Beri Komentar & Tanggapan</h3>
                                            <p class="text-xs text-slate-500">Tanggapan publik untuk: {{ Str::limit($pengumuman->judul, 35) }}</p>
                                        </div>
                                    </div>
                                    <button @click="modalKomentarOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <!-- Alert Sukses -->
                                <div x-show="komentarSuccess" x-cloak class="p-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-xs font-semibold flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span x-text="komentarSuccess"></span>
                                </div>

                                <!-- Alert Error -->
                                <div x-show="komentarError" x-cloak class="p-3 bg-rose-50 border border-rose-200 text-rose-700 rounded-xl text-xs font-semibold flex items-center gap-2">
                                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <span x-text="komentarError"></span>
                                </div>

                                <!-- Form Komentar -->
                                <form @submit.prevent="submitKomentar()" class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Nama Anda <span class="text-slate-400 font-normal">(Opsional - default: User Publik)</span>
                                        </label>
                                        <input
                                            type="text"
                                            x-model="nama"
                                            placeholder="Contoh: Ahmad / Alumni / Wali Santri"
                                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-800 focus:border-emerald-800 transition">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-slate-700 mb-1">
                                            Komentar / Doa Anda <span class="text-rose-500">*</span>
                                        </label>
                                        <textarea
                                            x-model="isi"
                                            rows="4"
                                            required
                                            placeholder="Tuliskan komentar atau doa kebaikan Anda di sini..."
                                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm focus:ring-2 focus:ring-emerald-800 focus:border-emerald-800 transition"></textarea>
                                    </div>

                                    <div class="flex items-center justify-end gap-2.5 pt-2 border-t border-slate-100">
                                        <button
                                            type="button"
                                            @click="modalKomentarOpen = false"
                                            class="px-4 py-2 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
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

                    <!-- MODAL POP-UP BAGIKAN / SHARE -->
                    <div x-show="modalShareOpen"
                         x-cloak
                         class="fixed inset-0 z-50 overflow-y-auto"
                         role="dialog"
                         aria-modal="true"
                         style="display: none;">
                        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                             @click="modalShareOpen = false"></div>

                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-100 p-6 space-y-4"
                                 @click.stop>
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-base">
                                            ↗️
                                        </div>
                                        <div>
                                            <h3 class="text-base font-bold text-slate-900">Bagikan Berita</h3>
                                            <p class="text-xs text-slate-500">Sebarkan informasi kebaikan ini</p>
                                        </div>
                                    </div>
                                    <button @click="modalShareOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>

                                <div class="grid grid-cols-2 gap-3 pt-2">
                                    <!-- WhatsApp -->
                                    <a
                                        href="https://api.whatsapp.com/send?text={{ rawurlencode($pengumuman->judul . ' - ' . url()->current()) }}"
                                        target="_blank"
                                        @click="triggerShare('whatsapp'); modalShareOpen = false"
                                        class="flex items-center gap-3 p-3 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-900 border border-emerald-200 transition font-bold text-xs">
                                        <span class="text-xl">💬</span>
                                        <span>WhatsApp</span>
                                    </a>

                                    <!-- Facebook -->
                                    <a
                                        href="https://www.facebook.com/sharer/sharer.php?u={{ rawurlencode(url()->current()) }}"
                                        target="_blank"
                                        @click="triggerShare('facebook'); modalShareOpen = false"
                                        class="flex items-center gap-3 p-3 rounded-2xl bg-blue-50 hover:bg-blue-100 text-blue-900 border border-blue-200 transition font-bold text-xs">
                                        <span class="text-xl">📘</span>
                                        <span>Facebook</span>
                                    </a>

                                    <!-- Twitter / X -->
                                    <a
                                        href="https://twitter.com/intent/tweet?text={{ rawurlencode($pengumuman->judul) }}&url={{ rawurlencode(url()->current()) }}"
                                        target="_blank"
                                        @click="triggerShare('twitter'); modalShareOpen = false"
                                        class="flex items-center gap-3 p-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-900 border border-slate-200 transition font-bold text-xs">
                                        <span class="text-xl">🐦</span>
                                        <span>Twitter / X</span>
                                    </a>

                                    <!-- Telegram -->
                                    <a
                                        href="https://t.me/share/url?url={{ rawurlencode(url()->current()) }}&text={{ rawurlencode($pengumuman->judul) }}"
                                        target="_blank"
                                        @click="triggerShare('telegram'); modalShareOpen = false"
                                        class="flex items-center gap-3 p-3 rounded-2xl bg-sky-50 hover:bg-sky-100 text-sky-900 border border-sky-200 transition font-bold text-xs">
                                        <span class="text-xl">✈️</span>
                                        <span>Telegram</span>
                                    </a>
                                </div>

                                <!-- Salin Tautan -->
                                <div class="pt-3 border-t border-slate-100">
                                    <div class="flex items-center gap-2">
                                        <input
                                            type="text"
                                            readonly
                                            value="{{ url()->current() }}"
                                            class="w-full px-3 py-2 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-600 truncate focus:outline-none">
                                        <button
                                            @click="copyLink()"
                                            class="shrink-0 px-4 py-2 rounded-xl text-xs font-bold transition"
                                            :class="copied ? 'bg-emerald-600 text-white' : 'bg-slate-900 hover:bg-slate-800 text-white'">
                                            <span x-text="copied ? 'Tersalin!' : 'Salin'"></span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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
                                    <span class="text-xs font-semibold text-emerald-800 block">
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
