<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ \App\Models\Pengaturan::get('nama_situs', 'SAP | SEKRETARIS AL-FALAH PUTRA') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
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
    <body class="font-sans antialiased bg-gray-50 text-gray-900" x-data="{ sidebarOpen: false }">
        <x-page-loader />

        <div class="min-h-screen flex flex-col lg:flex-row">
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
            <div class="flex-1 flex flex-col min-w-0 min-h-screen">
                <!-- Top Navbar -->
                @include('layouts.navbar')

                <!-- Elegant Single System Notification Alerts -->
                <div class="max-w-screen-2xl w-full mx-auto px-4 sm:px-6 lg:px-8 pt-4">
                    @if (session('success'))
                        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                             class="mb-5 bg-white/95 backdrop-blur-sm border-l-4 border-l-emerald-600 border border-emerald-200/90 rounded-2xl p-4 sm:p-5 shadow-lg shadow-emerald-900/5 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                    </svg>
                                </div>
                                <div class="pt-0.5">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-900">Operasi Berhasil</h4>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                                </div>
                            </div>
                            <button type="button" @click="show = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition shrink-0" title="Tutup Notifikasi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div x-data="{ show: true }" x-show="show" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                             class="mb-5 bg-white/95 backdrop-blur-sm border-l-4 border-l-rose-600 border border-rose-200/90 rounded-2xl p-4 sm:p-5 shadow-lg shadow-rose-900/5 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="pt-0.5">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-rose-900">Pemberitahuan / Kendala</h4>
                                    <p class="text-sm font-semibold text-gray-800 mt-0.5 leading-relaxed">{{ session('error') }}</p>
                                </div>
                            </div>
                            <button type="button" @click="show = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition shrink-0" title="Tutup Notifikasi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div x-data="{ show: true }" x-show="show" class="mb-5 bg-white/95 backdrop-blur-sm border-l-4 border-l-amber-500 border border-amber-200/90 rounded-2xl p-4 sm:p-5 shadow-lg shadow-amber-900/5 flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <div class="w-9 h-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0 shadow-xs">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="pt-0.5">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-amber-900">Periksa Kembali Isian Formulir</h4>
                                    <ul class="list-disc list-inside space-y-1 text-xs text-gray-700 mt-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <button type="button" @click="show = false" class="p-1 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition shrink-0" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Page Content Slot -->
                <main class="flex-1 max-w-screen-2xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    @isset($slot)
                        {{ $slot }}
                    @else
                        @yield('content')
                    @endisset
                </main>

                <!-- Footer Bersih & Responsif -->
                <footer class="bg-white border-t border-gray-200/80 px-4 sm:px-6 lg:px-8 py-3.5 sm:py-4 mt-auto">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left text-xs text-gray-500">
                        <div class="flex items-center gap-1.5 flex-wrap justify-center sm:justify-start">
                            <span>&copy; {{ date('Y') }}</span>
                            <span class="font-bold text-gray-800">SAP Al-Falah Putra</span>
                            <span class="text-gray-300 hidden sm:inline">&bull;</span>
                            <span class="text-gray-500">Pondok Pesantren Al-Falah Putak</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[11px] text-gray-400 font-mono">
                            <span>v1.0 Produksi</span>
                            <span>&bull;</span>
                            <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                Sistem Aktif
                            </span>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Modal Konfirmasi Universal (Tailwind + Alpine.js) -->
        <div
            x-data="{
                open: false,
                judul: 'Konfirmasi',
                pesan: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                tombolTeks: 'Ya, Lanjutkan',
                tipe: 'danger',
                formTarget: null,
                actionCallback: null,
                init() {
                    window.bukaKonfirmasi = (options) => {
                        this.judul = options.judul || 'Konfirmasi Tindakan';
                        this.pesan = options.pesan || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                        this.tombolTeks = options.tombolTeks || 'Ya, Lanjutkan';
                        this.tipe = options.tipe || 'danger';
                        this.formTarget = options.form || null;
                        this.actionCallback = options.onConfirm || null;
                        this.open = true;
                    };
                },
                eksekusi() {
                    this.open = false;
                    if (this.formTarget) {
                        if (window.sapStartLoader && !this.formTarget.hasAttribute('data-no-loader')) {
                            const action = (this.formTarget.getAttribute('action') || '').toLowerCase();
                            if (!action.includes('/backup') && !action.includes('/pdf') && !action.includes('/unduh')) {
                                window.sapStartLoader('Sedang memproses data...');
                            }
                        }
                        this.formTarget.submit();
                    } else if (typeof this.actionCallback === 'function') {
                        this.actionCallback();
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
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
                    @click="open = false"
                ></div>

                <!-- Modal Panel -->
                <div
                    x-show="open"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-6 border border-gray-100"
                >
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-red-100 sm:mx-0 sm:h-11 sm:w-11">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900" id="modal-title" x-text="judul"></h3>
                            <div class="mt-2">
                                <p class="text-xs sm:text-sm text-gray-500 leading-relaxed" x-text="pesan"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 sm:flex sm:flex-row-reverse gap-3">
                        <button
                            type="button"
                            @click="eksekusi()"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white text-xs sm:text-sm font-semibold rounded-xl shadow-sm transition focus:outline-none"
                            x-text="tombolTeks"
                        ></button>
                        <button
                            type="button"
                            @click="open = false"
                            class="mt-2 sm:mt-0 w-full sm:w-auto inline-flex justify-center items-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl transition focus:outline-none"
                        >
                            Batal
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
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
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
                    class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-6 border border-gray-100"
                >
                    <div class="sm:flex sm:items-start gap-4">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-2xl bg-amber-100 sm:mx-0 sm:h-11 sm:w-11">
                            <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                            <h3 class="text-base sm:text-lg font-bold text-gray-900" id="modal-alert-title" x-text="judul"></h3>
                            <div class="mt-2">
                                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed" x-text="pesan"></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end">
                        <button
                            type="button"
                            @click="open = false"
                            class="w-full sm:w-auto inline-flex justify-center items-center px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-sm transition focus:outline-none"
                            x-text="tombolTeks"
                        ></button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Intersepsi formulir dengan atribut data-konfirmasi
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('form[data-konfirmasi]').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const pesan = this.getAttribute('data-konfirmasi') || 'Apakah Anda yakin ingin melanjutkan tindakan ini?';
                        const judul = this.getAttribute('data-judul') || 'Konfirmasi Tindakan';
                        const tombol = this.getAttribute('data-tombol') || 'Ya, Lanjutkan';
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

                @if (session('error'))
                    if (window.bukaPeringatan) {
                        window.bukaPeringatan({
                            judul: 'Pemberitahuan Sistem',
                            pesan: @json(session('error')),
                            tombolTeks: 'Saya Mengerti'
                        });
                    }
                @endif
            });

            // Pemeriksaan ukuran berkas global secara instan di sisi klien (semua input file)
            document.addEventListener('change', function(e) {
                if (e.target && e.target.type === 'file') {
                    const input = e.target;
                    const maxBytes = parseInt(input.getAttribute('data-max-bytes')) || (5 * 1024 * 1024); // Standar 5 MB sesuai aturan aplikasi
                    const maxMbText = (maxBytes / (1024 * 1024)).toFixed(0) + ' MB';

                    if (input.files && input.files.length > 0) {
                        for (let i = 0; i < input.files.length; i++) {
                            const file = input.files[i];
                            if (file.size > maxBytes) {
                                const fileSizeMb = (file.size / (1024 * 1024)).toFixed(2) + ' MB';
                                const fileName = file.name;

                                // Reset input seketika agar berkas berukuran raksasa tidak dikirimkan ke server
                                input.value = '';

                                if (window.bukaPeringatan) {
                                    window.bukaPeringatan({
                                        judul: 'Ukuran Berkas Terlalu Besar!',
                                        pesan: 'Berkas "' + fileName + '" berukuran ' + fileSizeMb + ', melebihi batas maksimal yang diperbolehkan (' + maxMbText + '). Silakan pilih berkas yang lebih kecil atau lakukan kompresi berkas terlebih dahulu.',
                                        tombolTeks: 'Saya Mengerti'
                                    });
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
