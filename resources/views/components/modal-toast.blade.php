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
            this.judul = judul;
            this.pesan = pesan;
            this.show = true;
            if (this.timer) clearTimeout(this.timer);
            this.timer = setTimeout(() => {
                this.show = false;
            }, 2000);
        },
        init() {
            if (this.show) {
                this.timer = setTimeout(() => {
                    this.show = false;
                }, 2000);
            }
            window.tampilNotifikasi = (opsi) => {
                const tipe = opsi.tipe || 'berhasil';
                const judul = opsi.judul || (tipe === 'berhasil' ? 'BERHASIL' : 'GAGAL');
                const pesan = opsi.pesan || '';
                this.buka(tipe, judul, pesan);
            };
        }
    }"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-950/45 backdrop-blur-xs transition-opacity"
    x-transition:enter="ease-out duration-250"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @click.self="tutup()"
    @keydown.escape.window="tutup()"
>
    <!-- Modal Card Tengah -->
    <div
        class="relative bg-white rounded-3xl p-6 sm:p-7 max-w-sm w-full text-center shadow-2xl border border-slate-100 flex flex-col items-center justify-center transform transition-all select-none"
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-85 translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-85 translate-y-2"
        @click.stop
    >
        <!-- Icon Berhasil dengan Animasi Ceklis -->
        <template x-if="tipe === 'berhasil'">
            <div class="w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mb-3 shadow-inner border border-emerald-100">
                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke-width="2" class="opacity-25" />
                    <path class="toast-check-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M6 12.5l4 4 8-8.5" />
                </svg>
            </div>
        </template>

        <!-- Icon Gagal dengan Animasi Silang -->
        <template x-if="tipe === 'gagal'">
            <div class="w-16 h-16 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mb-3 shadow-inner border border-rose-100">
                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke-width="2" class="opacity-25" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M8 8l8 8M16 8l-8 8" />
                </svg>
            </div>
        </template>

        <!-- Icon Peringatan -->
        <template x-if="tipe === 'peringatan'">
            <div class="w-16 h-16 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center mb-3 shadow-inner border border-amber-100">
                <svg class="w-9 h-9" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <circle cx="12" cy="12" r="10" stroke-width="2" class="opacity-25" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.8" d="M12 8v5m0 3h.01" />
                </svg>
            </div>
        </template>

        <!-- Judul Modal -->
        <h3
            class="text-base font-extrabold tracking-wide uppercase"
            :class="{
                'text-emerald-800': tipe === 'berhasil',
                'text-rose-700': tipe === 'gagal',
                'text-amber-700': tipe === 'peringatan'
            }"
            x-text="judul"
        ></h3>

        <!-- Deskripsi Pesan -->
        <p class="text-xs sm:text-sm font-semibold text-slate-700 mt-1.5 leading-relaxed break-words max-w-[280px]" x-text="pesan"></p>

        @if ($hasFormErrors && count($errors->all()) > 1)
            <div class="mt-2 text-left w-full max-h-32 overflow-y-auto bg-slate-50 p-2.5 rounded-xl border border-slate-200">
                <ul class="text-[11px] font-medium text-rose-700 space-y-1 list-disc list-inside">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Progress Bar Timer 2 Detik -->
        <div class="w-full bg-slate-100 h-1 rounded-full mt-4 overflow-hidden">
            <div
                class="h-full rounded-full toast-progress-bar"
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
        animation: toastTimerShrink 2s linear forwards;
    }
</style>
