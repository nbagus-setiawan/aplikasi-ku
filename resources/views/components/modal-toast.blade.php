@php
    $hasSuccess = session()->has('success');
    $hasError = session()->has('error');
    $hasWarning = session()->has('warning');
    $hasFormErrors = isset($errors) && $errors->any();
    $isActive = $hasSuccess || $hasError || $hasWarning || $hasFormErrors;

    $tipeAwal = 'berhasil';
    $judulAwal = 'BERHASIL';
    $pesanAwal = '';

    if ($hasSuccess) {
        $tipeAwal = 'berhasil';
        $judulAwal = 'BERHASIL';
        $pesanAwal = session('success');
    } elseif ($hasError) {
        $tipeAwal = 'gagal';
        $judulAwal = 'GAGAL';
        $pesanAwal = session('error');
    } elseif ($hasWarning) {
        $tipeAwal = 'peringatan';
        $judulAwal = 'PERINGATAN';
        $pesanAwal = session('warning');
    } elseif ($hasFormErrors) {
        $tipeAwal = 'gagal';
        $judulAwal = 'PERIKSA FORMULIR';
        $pesanAwal = $errors->first();
    }
@endphp

{{-- NOTIFIKASI TOAST MENGAMBANG DI ATAS (TOP FLOATING TOAST) - TANPA MODAL TENGAH & TANPA BACKDROP GELAP --}}
<div x-data="{
        show: {{ $isActive ? 'true' : 'false' }},
        tipe: '{{ $tipeAwal }}',
        judul: '{{ addslashes($judulAwal) }}',
        pesan: '{{ addslashes($pesanAwal) }}',
        timer: null,
        tutup() {
            this.show = false;
            if (this.timer) clearTimeout(this.timer);
        },
        buka(tipe, judul, pesan) {
            this.tipe = tipe;
            this.judul = judul || (tipe === 'berhasil' ? 'BERHASIL' : (tipe === 'peringatan' ? 'PERINGATAN' : 'GAGAL'));
            this.pesan = pesan || '';
            this.show = true;
            if (this.timer) clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.tutup();
            }, 4000);
        },
        init() {
            if (this.show) {
                this.timer = setTimeout(() => {
                    this.tutup();
                }, 4000);
            }
            window.tampilNotifikasi = (opsi) => {
                const tipe = opsi.tipe || 'berhasil';
                const judul = opsi.judul || (tipe === 'berhasil' ? 'BERHASIL' : 'GAGAL');
                const pesan = opsi.pesan || '';
                this.buka(tipe, judul, pesan);
            };
            window.sapToast = (type, title, message) => {
                const mappedType = (type === 'error' ? 'gagal' : (type === 'warning' ? 'peringatan' : 'berhasil'));
                this.buka(mappedType, title, message);
            };
        }
    }"
    x-show="show"
    x-cloak
    class="fixed top-4 sm:top-5 left-1/2 -translate-x-1/2 z-[100000] w-[calc(100%-2rem)] max-w-lg pointer-events-none no-print"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="-translate-y-8 opacity-0 scale-95"
    x-transition:enter-end="translate-y-0 opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="translate-y-0 opacity-100 scale-100"
    x-transition:leave-end="-translate-y-6 opacity-0 scale-95"
    @keydown.escape.window="tutup()"
>
    <!-- Kartu Toast Mengambang di Atas (Solid Putih, Bersih & Kontras Tinggi) -->
    <div
        class="pointer-events-auto relative bg-white rounded-2xl p-4 sm:p-4.5 shadow-2xl border-2 flex items-start gap-3.5 transition-all overflow-hidden"
        :class="{
            'border-emerald-500 border-l-[6px] border-l-emerald-600': tipe === 'berhasil',
            'border-rose-500 border-l-[6px] border-l-rose-600': tipe === 'gagal',
            'border-amber-500 border-l-[6px] border-l-amber-600': tipe === 'peringatan'
        }"
        @click.stop
    >
        <!-- Ikon Status -->
        <template x-if="tipe === 'berhasil'">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path class="toast-check-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </template>

        <template x-if="tipe === 'gagal'">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
        </template>

        <template x-if="tipe === 'peringatan'">
            <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 shadow-xs">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </template>

        <!-- Konten Teks -->
        <div class="flex-1 min-w-0 pt-0.5">
            <h4
                class="text-xs font-black tracking-wider uppercase"
                :class="{
                    'text-emerald-900': tipe === 'berhasil',
                    'text-rose-900': tipe === 'gagal',
                    'text-amber-900': tipe === 'peringatan'
                }"
                x-text="judul"
            ></h4>

            <p class="text-xs sm:text-sm font-semibold text-slate-800 mt-0.5 leading-snug break-words" x-text="pesan"></p>

            @if ($hasFormErrors && count($errors->all()) > 1)
                <div class="mt-2 text-left w-full max-h-28 overflow-y-auto bg-slate-50 p-2 rounded-lg border border-slate-200">
                    <ul class="text-[11px] font-medium text-rose-700 space-y-0.5 list-disc list-inside">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <!-- Tombol Tutup Manual -->
        <button
            type="button"
            @click="tutup()"
            class="text-slate-400 hover:text-slate-700 hover:bg-slate-100 p-1 rounded-lg shrink-0 font-bold text-lg leading-none transition"
            aria-label="Tutup Notifikasi"
        >
            &times;
        </button>

        <!-- Progress Bar Timer Otomatis 4 Detik -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100 overflow-hidden">
            <div
                class="h-full toast-progress-bar"
                :class="{
                    'bg-emerald-500': tipe === 'berhasil',
                    'bg-rose-500': tipe === 'gagal',
                    'bg-amber-500': tipe === 'peringatan'
                }"
            ></div>
        </div>
    </div>
</div>

<style>
    @keyframes toastCheckmarkDraw {
        0% {
            stroke-dasharray: 30;
            stroke-dashoffset: 30;
        }
        100% {
            stroke-dasharray: 30;
            stroke-dashoffset: 0;
        }
    }
    @keyframes toastTimerShrink {
        from { width: 100%; }
        to { width: 0%; }
    }
    .toast-check-path {
        animation: toastCheckmarkDraw 0.45s ease-out forwards;
    }
    .toast-progress-bar {
        animation: toastTimerShrink 4s linear forwards;
    }
</style>
