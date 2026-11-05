<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="robots" content="noindex, nofollow, noarchive">

        <title>{{ \App\Models\Pengaturan::get('nama_situs', 'SAP | SEKRETARIS AL-FALAH PUTRA') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')

        <style>
            [x-cloak] { display: none !important; }
            @media print {
                @page {
                    margin: 0 !important; /* Menghilangkan header (tanggal, jam, judul SAP | SEKRETARIS AL-FALAH PUTRA) dan footer otomatis browser */
                    size: auto;
                }
                body {
                    margin: 0 !important;
                    background: #ffffff !important;
                    color: #000000 !important;
                }
                .no-print, header, footer, aside, nav, button {
                    display: none !important;
                }
            }
        </style>
    </head>
    <body class="font-sans antialiased text-slate-800 overflow-x-hidden min-h-screen" style="background-color: #f0f5f2;" x-data="{ sidebarOpen: false }">
        <x-page-loader />

        <div class="min-h-screen flex flex-col lg:flex-row overflow-x-hidden">
            <!-- Sidebar -->
            @include('layouts.sidebar')

            <!-- Overlay for mobile and tablet sidebar -->
            <div
                x-show="sidebarOpen"
                @click="sidebarOpen = false"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-950/60 z-40 lg:hidden backdrop-blur-xs"
            ></div>

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 min-h-screen overflow-x-hidden">
                <!-- Top Navbar -->
                @include('layouts.navbar')

                <!-- Modal Toast Terpusat di Tengah dengan Animasi Ceklis & Timer 2 Detik -->
                <x-modal-toast />

                <!-- Page Content Slot -->
                <main class="flex-1 max-w-7xl 2xl:max-w-screen-2xl w-full mx-auto px-3.5 sm:px-6 lg:px-8 py-4 sm:py-6">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </main>

                <!-- Footer Bersih & Responsif -->
                <footer class="bg-transparent border-t border-slate-200/60 px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4 mt-auto">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left text-xs text-slate-500">
                        <div class="flex items-center gap-1.5 flex-wrap justify-center sm:justify-start">
                            <span>&copy; {{ date('Y') }}</span>
                            <span class="font-bold text-slate-800">SAP Al-Falah Putra</span>
                            <span class="text-slate-300 hidden sm:inline">&bull;</span>
                            <span class="text-slate-500">Pondok Pesantren Al-Falah Putra</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[11px] text-slate-400 font-mono">
                            <span>v1.0 Produksi</span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Sistem Aktif
                            </span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- TOAST NOTIFIKASI SOLID KONTRAST TINGGI (100% SOLID, BUKAN TRANSPARAN, SANGAT JELAS TERBACA) -->
        <div id="sap-toast-container" class="fixed top-5 right-5 z-[99999] max-w-sm sm:max-w-md w-full pointer-events-none space-y-3"></div>

        <!-- Modal Konfirmasi Universal (Tailwind + Alpine.js Sesuai Psikologi Warna & AJAX Seamless) -->
        <div
            x-data="{
                open: false,
                loading: false,
                judul: 'Konfirmasi Hapus Data',
                pesan: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                tombolTeks: 'Ya, Hapus',
                tipe: 'danger',
                formTarget: null,
                actionCallback: null,
                triggerElement: null,
                init() {
                    window.bukaKonfirmasi = (options) => {
                        this.judul = options.judul || 'Konfirmasi Hapus Data';
                        this.pesan = options.pesan || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                        this.tombolTeks = options.tombolTeks || 'Ya, Hapus';
                        this.tipe = options.tipe || 'danger';
                        this.formTarget = options.form || null;
                        this.actionCallback = options.onConfirm || null;
                        this.triggerElement = options.triggerElement || (options.form ? options.form.querySelector('button[type=submit], button') : null);
                        this.loading = false;
                        this.open = true;
                    };
                },
                eksekusi() {
                    if (this.loading) return;
                    this.loading = true;

                    if (this.formTarget) {
                        const form = this.formTarget;
                        const methodInput = form.querySelector('input[name=_method]');
                        const isDelete = (methodInput && methodInput.value.toUpperCase() === 'DELETE') || (form.method && form.method.toUpperCase() === 'DELETE');

                        // Jika form DELETE, lakukan penghapusan mulus via AJAX tanpa reload browser
                        if (isDelete) {
                            const formData = new FormData(form);
                            if (!formData.has('_method')) formData.append('_method', 'DELETE');

                            fetch(form.action, {
                                method: 'POST',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: formData
                            })
                            .then(res => {
                                if (res.ok) {
                                    return res.json().catch(() => ({ success: true, message: 'Data berhasil dihapus.' }));
                                }
                                throw new Error('Gagal menghapus data di server.');
                            })
                            .then(data => {
                                this.open = false;
                                this.loading = false;

                                // Cari elemen baris (tr atau card) terdekat dari form yang dihapus
                                const row = form.closest('tr') || form.closest('[data-item-card]') || form.closest('.divide-y > div');
                                if (row) {
                                    row.classList.add('opacity-0', 'scale-95', 'transition-all', 'duration-300');
                                    setTimeout(() => row.remove(), 320);
                                }

                                // Tampilkan notifikasi toast solid jelas
                                if (window.sapToast) {
                                    window.sapToast('success', 'Berhasil Dihapus', data.message || 'Data berhasil dihapus secara permanen.');
                                }
                            })
                            .catch(err => {
                                console.warn('Fallback submit biasa:', err);
                                this.open = false;
                                this.loading = false;
                                form.submit();
                            });
                            return;
                        }

                        // Form biasa non-delete
                        this.open = false;
                        this.loading = false;
                        form.submit();
                    } else if (typeof this.actionCallback === 'function') {
                        this.actionCallback();
                        this.open = false;
                        this.loading = false;
                    } else {
                        this.open = false;
                        this.loading = false;
                    }
                }
            }"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Backdrop -->
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                    @click="if (!loading) open = false"
                ></div>

                <!-- Modal Panel Sesuai Psikologi Warna Referensi -->
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-6 border border-slate-200 text-center sm:text-left"
                >
                    <div class="sm:flex sm:items-start gap-4">
                        <!-- Ikon Tempat Sampah Merah Bulat Elegan -->
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-rose-100 sm:mx-0 sm:h-11 sm:w-11 text-rose-600 shadow-inner">
                            <svg class="h-6 w-6 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-base sm:text-lg font-black text-slate-900" id="modal-title" x-text="judul"></h3>
                            <div class="mt-1.5">
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="pesan"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Tombol Aksi Sesuai Psikologi Warna: Merah Hapus, Abu Netral Batal -->
                    <div class="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2.5">
                        <button
                            type="button"
                            @click="open = false"
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs sm:text-sm rounded-xl transition focus:outline-none"
                        >
                            Batal
                        </button>

                        <button
                            type="button"
                            @click="eksekusi()"
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex justify-center items-center gap-1.5 px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm rounded-xl shadow-xs transition focus:outline-none disabled:opacity-50"
                        >
                            <template x-if="loading">
                                <svg class="w-4 h-4 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="loading ? 'Menghapus...' : tombolTeks">Ya, Hapus</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Peringatan / Alert Interaktif (Tailwind + Alpine.js) -->
        <div
            x-data="{
                open: false,
                judul: 'Peringatan',
                pesan: '',
                tombolTeks: 'Saya Mengerti',
                tipe: 'warning',
                init() {
                    window.bukaPeringatan = (options) => {
                        this.judul = options.judul || 'Peringatan Sistem';
                        this.pesan = options.pesan || '';
                        this.tombolTeks = options.tombolTeks || 'Saya Mengerti';
                        this.tipe = options.tipe || 'warning';
                        this.open = true;
                    };
                }
            }"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-alert-title"
            role="dialog"
            aria-modal="true"
        >
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                <!-- Backdrop -->
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity"
                    @click="open = false"
                ></div>

                <!-- Alert Panel -->
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-6 border border-slate-200"
                >
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-amber-100 sm:mx-0 sm:h-11 sm:w-11 text-amber-600">
                            <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-base sm:text-lg font-black text-slate-900" id="modal-alert-title" x-text="judul"></h3>
                            <div class="mt-2">
                                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed" x-text="pesan"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button
                            type="button"
                            @click="open = false"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition focus:outline-none"
                            x-text="tombolTeks"
                        ></button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // SISTEM TOAST NOTIFIKASI SOLID KONTRAST TINGGI (100% SOLID PUTIH, SANGAT JELAS TERBACA)
            window.sapToast = function(type, title, message) {
                const container = document.getElementById('sap-toast-container');
                if (!container) return;

                const isError = type === 'error';
                const toastId = 'toast-' + Math.random().toString(36).substring(2, 9);

                const iconSvg = isError
                    ? '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>'
                    : '<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';

                const toast = document.createElement('div');
                toast.id = toastId;
                toast.className = `pointer-events-auto rounded-2xl p-4 shadow-2xl border-2 transition-all duration-300 transform translate-y-2 opacity-0 flex items-start gap-3 bg-white ${isError ? 'border-rose-500 border-l-[6px] border-l-rose-600' : 'border-emerald-500 border-l-[6px] border-l-emerald-600'}`;

                toast.innerHTML = `
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 ${isError ? 'bg-rose-600' : 'bg-emerald-600'}">
                        ${iconSvg}
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <p class="text-xs font-black uppercase tracking-wider ${isError ? 'text-rose-900' : 'text-slate-900'}">${title || (isError ? 'GAGAL' : 'BERHASIL')}</p>
                        <p class="text-xs font-bold text-slate-800 mt-0.5 leading-snug">${message || ''}</p>
                    </div>
                    <button type="button" onclick="document.getElementById('${toastId}').remove()" class="text-slate-400 hover:text-slate-700 p-1 shrink-0 font-bold text-base leading-none">
                        &times;
                    </button>
                `;

                container.appendChild(toast);

                // Animasi muncul
                requestAnimationFrame(() => {
                    toast.classList.remove('translate-y-2', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                });

                // Otomatis hilang setelah 4.5 detik
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'translate-x-4');
                    setTimeout(() => toast.remove(), 300);
                }, 4500);
            };

            // Intersepsi formulir dengan atribut data-konfirmasi
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('form[data-konfirmasi]').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const pesan = this.getAttribute('data-konfirmasi') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                        const judul = this.getAttribute('data-judul') || 'Konfirmasi Hapus Data';
                        const tombol = this.getAttribute('data-tombol') || 'Ya, Hapus Data';
                        if (window.bukaKonfirmasi) {
                            window.bukaKonfirmasi({
                                judul: judul,
                                pesan: pesan,
                                tombolTeks: tombol,
                                form: this
                            });
                        } else {
                            this.submit();
                        }
                    });
                });

                // Toast otomatis dari Flash Session
                @if (session('success'))
                    window.sapToast('success', 'Berhasil', @json(session('success')));
                @endif
                @if (session('error'))
                    window.sapToast('error', 'Terjadi Kesalahan', @json(session('error')));
                @endif
                @if (session('warning'))
                    window.sapToast('error', 'Perhatian', @json(session('warning')));
                @endif

                // Penanda Baris yang Baru Diubah (Highlight Kilauan Hijau Otomatis di Semua Halaman)
                const highlightId = @json(session('highlight_id')) || new URLSearchParams(window.location.search).get('highlight');
                if (highlightId) {
                    setTimeout(() => {
                        const selector = `#santri-${highlightId}, #pengurus-${highlightId}, #surat-masuk-${highlightId}, #surat-keluar-${highlightId}, #agenda-${highlightId}, #arsip-${highlightId}, #pengumuman-${highlightId}, #item-row-${highlightId}, #transaksi-row-${highlightId}, [data-id="${highlightId}"]`;
                        const targets = document.querySelectorAll(selector);
                        targets.forEach(el => {
                            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            el.classList.add('bg-emerald-100', 'ring-2', 'ring-emerald-500', 'transition', 'duration-1000');
                            setTimeout(() => {
                                el.classList.remove('bg-emerald-100', 'ring-2', 'ring-emerald-500');
                            }, 3000);
                        });
                    }, 400);
                }
            });

            // Pemeriksaan ukuran berkas global secara instan di sisi klien (semua input file)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.type === 'file') {
                    const input = e.target;
                    const maxBytes = parseInt(input.getAttribute('data-max-bytes')) || (5 * 1024 * 1024);
                    const maxMbText = (maxBytes / (1024 * 1024)).toFixed(0) + ' MB';

                    if (input.files && input.files.length > 0) {
                        for (let i = 0; i < input.files.length; i++) {
                            const file = input.files[i];
                            if (file.size > maxBytes) {
                                const fileSizeMb = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                                const fileName = file.name;
                                input.value = '';

                                if (window.sapToast) {
                                    window.sapToast('error', 'Ukuran Berkas Terlalu Besar', 'Berkas "' + fileName + '" (' + fileSizeMb + ') melebihi batas ' + maxMbText + '. Silakan pilih berkas yang lebih kecil.');
                                } else {
                                    alert('Ukuran berkas "' + fileName + '" (' + fileSizeMb + ') melebihi batas maksimal ' + maxMbText + '!');
                                }
                                return;
                            }
                        }
                    }
                }
            });
        </script>

        @stack('scripts')
    </body>
</html>
