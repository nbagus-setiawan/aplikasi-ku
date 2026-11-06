@extends('layouts.app')

@section('title', 'Bendahara - Buku Kas Umum (Debit & Kredit)')

@section('content')
<div x-data="bukuKasApp()" x-init="initApp()" class="space-y-4 sm:space-y-6">

    <!-- TOAST NOTIFIKASI SUKSES / ERROR (MUNCUL OTOMATIS TANPA RELOAD) -->
    <div x-show="toast.show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-20 right-4 z-50 max-w-md w-full pointer-events-auto"
         style="display: none;">
        <div class="rounded-2xl p-4 shadow-2xl border-2 flex items-start gap-3 bg-white"
             :class="toast.type === 'error' ? 'border-rose-500 border-l-[6px] border-l-rose-600' : 'border-emerald-500 border-l-[6px] border-l-emerald-600'">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                 :class="toast.type === 'error' ? 'bg-rose-600 text-white' : 'bg-emerald-600 text-white'">
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <template x-if="toast.type !== 'error'">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                </template>
            </div>
            <div class="flex-1 min-w-0 pt-0.5">
                <p class="text-xs font-black uppercase tracking-wider"
                   :class="toast.type === 'error' ? 'text-rose-900' : 'text-slate-900'"
                   x-text="toast.title || (toast.type === 'error' ? 'GAGAL' : 'BERHASIL')"></p>
                <p class="text-xs font-bold text-slate-800 mt-0.5 leading-snug" x-text="toast.message"></p>
            </div>
            <button @click="toast.show = false" type="button" class="text-slate-400 hover:text-slate-700 p-1 font-bold text-base leading-none">
                &times;
            </button>
        </div>
    </div>

    <!-- HEADER & AKSI UTAMA (RESPONSIF MOBILE & DESKTOP) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5 sm:space-y-0 sm:flex sm:items-center sm:justify-between sm:gap-4">
        <div class="flex items-center gap-3">
            <span class="p-2 sm:p-2.5 rounded-xl bg-emerald-100 text-emerald-800 shrink-0">
                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </span>
            <div class="min-w-0">
                <h1 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight truncate">Buku Kas Bendahara</h1>
                <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">
                    <span>Periode aktif: </span>
                    <strong class="text-emerald-700" x-text="cards.periode_label || '{{ $labelPeriode }}'">{{ $labelPeriode }}</strong>
                </p>
            </div>
        </div>

        <!-- Tombol Aksi Kas & Pop-up Unduh Laporan -->
        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 pt-1 sm:pt-0">
            <button @click="openDebitModal()"
                    type="button"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 sm:px-4 sm:py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-xs transition transform active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Debit</span>
            </button>

            <button @click="openKreditModal()"
                    type="button"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 sm:px-4 sm:py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm shadow-xs transition transform active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                <span>Catat Kredit</span>
            </button>

            <!-- Tombol Buka Pop-up Unduh Laporan PDF -->
            <button @click="modalDownloadOpen = true"
                    type="button"
                    class="col-span-2 sm:col-span-1 inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 sm:px-4 sm:py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-xs transition transform active:scale-95"
                    title="Pilih periode dan unduh laporan kas PDF">
                <svg class="w-4 h-4 text-blue-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Unduh Laporan PDF</span>
            </button>
        </div>
    </div>

    <!-- 4 KARTU RINGKASAN AKUNTANSI (DINAMIS TERUPDATE VIA AJAX) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <!-- 1. Saldo Kas Riil -->
        <div class="col-span-2 sm:col-span-1 lg:col-span-1 bg-gradient-to-br from-emerald-800 via-emerald-900 to-teal-950 text-white p-4 sm:p-5 rounded-2xl shadow-sm border border-emerald-700/80 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-28 h-28 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-emerald-200">Saldo Kas Riil</span>
                <span class="p-1.5 sm:p-2 rounded-xl bg-emerald-700/80 text-emerald-200 border border-emerald-600/50 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
            </div>
            <div class="relative z-10 mt-2.5 sm:mt-3">
                <div class="text-xl sm:text-2xl font-black tracking-tight truncate"
                     :class="(cards.saldo_kas_raw !== undefined ? cards.saldo_kas_raw < 0 : {{ $saldoKasKeseluruhan < 0 ? 'true' : 'false' }}) ? 'text-rose-300' : 'text-white'"
                     x-text="cards.saldo_kas || 'Rp {{ number_format($saldoKasKeseluruhan, 0, ',', '.') }}'">
                    Rp {{ number_format($saldoKasKeseluruhan, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-emerald-200/80 mt-0.5 font-medium">Sisa saldo kas berjalan</p>
            </div>
        </div>

        <!-- 2. Total Penerimaan (Debit) -->
        <div class="col-span-1 bg-gradient-to-br from-emerald-50 via-teal-50/60 to-emerald-100/40 p-3.5 sm:p-5 rounded-2xl shadow-xs border-2 border-emerald-200/90 hover:border-emerald-300 transition flex flex-col justify-between">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-emerald-900 truncate">Penerimaan (Debit)</span>
                <span class="p-1.5 sm:p-2 rounded-xl bg-emerald-600 text-white shadow-xs shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl lg:text-2xl font-black text-emerald-700 tracking-tight truncate"
                     x-text="cards.total_debit || 'Rp {{ number_format($totalDebitFiltered, 0, ',', '.') }}'">
                    Rp {{ number_format($totalDebitFiltered, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-emerald-800/80 mt-0.5 font-semibold truncate">Kas masuk periode ini</p>
            </div>
        </div>

        <!-- 3. Total Pengeluaran (Kredit) -->
        <div class="col-span-1 bg-gradient-to-br from-rose-50 via-red-50/60 to-rose-100/40 p-3.5 sm:p-5 rounded-2xl shadow-xs border-2 border-rose-200/90 hover:border-rose-300 transition flex flex-col justify-between">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-rose-900 truncate">Pengeluaran (Kredit)</span>
                <span class="p-1.5 sm:p-2 rounded-xl bg-rose-600 text-white shadow-xs shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl lg:text-2xl font-black text-rose-700 tracking-tight truncate"
                     x-text="cards.total_kredit || 'Rp {{ number_format($totalKreditFiltered, 0, ',', '.') }}'">
                    Rp {{ number_format($totalKreditFiltered, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-rose-800/80 mt-0.5 font-semibold truncate">Kas keluar periode ini</p>
            </div>
        </div>

        <!-- 4. Surplus / Defisit -->
        <div class="col-span-2 sm:col-span-1 lg:col-span-1 p-3.5 sm:p-5 rounded-2xl shadow-xs border-2 transition flex flex-col justify-between"
             :class="(cards.is_surplus !== undefined ? cards.is_surplus : {{ $surplusDefisitFiltered >= 0 ? 'true' : 'false' }}) ? 'bg-gradient-to-br from-blue-50 via-indigo-50/60 to-sky-100/40 border-blue-200/90' : 'bg-gradient-to-br from-amber-50 via-orange-50/60 to-amber-100/40 border-amber-200/90'">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-extrabold uppercase tracking-wider"
                      :class="(cards.is_surplus !== undefined ? cards.is_surplus : {{ $surplusDefisitFiltered >= 0 ? 'true' : 'false' }}) ? 'text-blue-900' : 'text-amber-900'">
                    Surplus / (Defisit)
                </span>
                <span class="p-1.5 sm:p-2 rounded-xl shadow-xs shrink-0"
                      :class="(cards.is_surplus !== undefined ? cards.is_surplus : {{ $surplusDefisitFiltered >= 0 ? 'true' : 'false' }}) ? 'bg-blue-600 text-white' : 'bg-amber-600 text-white'">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl lg:text-2xl font-black tracking-tight truncate"
                     :class="(cards.is_surplus !== undefined ? cards.is_surplus : {{ $surplusDefisitFiltered >= 0 ? 'true' : 'false' }}) ? 'text-blue-700' : 'text-amber-700'"
                     x-text="cards.surplus_defisit || '{{ $surplusDefisitFiltered >= 0 ? '+' : '' }}Rp {{ number_format($surplusDefisitFiltered, 0, ',', '.') }}'">
                    {{ $surplusDefisitFiltered >= 0 ? '+' : '' }}Rp {{ number_format($surplusDefisitFiltered, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] mt-0.5 font-semibold"
                   :class="(cards.is_surplus !== undefined ? cards.is_surplus : {{ $surplusDefisitFiltered >= 0 ? 'true' : 'false' }}) ? 'text-blue-800/80' : 'text-amber-800/80'"
                   x-text="(cards.total_count !== undefined ? cards.total_count : {{ $totalCountFiltered }}) + ' transaksi periode ini'">
                    {{ $totalCountFiltered }} transaksi periode ini
                </p>
            </div>
        </div>
    </div>

    <!-- 3 JENIS FILTER BERDASARKAN DATA TERCATAT (AJAX SEAMLESS TANPA RELOAD) -->
    <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-3.5">
        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                </span>
                <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">3 Pilihan Filter Riwayat Kas</span>
            </div>
            
            <button @click="resetFilter()"
                    type="button"
                    class="text-xs text-rose-600 font-bold hover:underline flex items-center gap-1">
                <span>Reset Filter</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- 3 Tab Mode Filter: 1. Semua Data | 2. Bulan Tercatat | 3. Tahun Tercatat -->
        <div class="grid grid-cols-3 gap-2 p-1 bg-slate-100 rounded-xl max-w-lg">
            <button type="button"
                    @click="setFilterTipe('semua')"
                    class="py-2 text-xs font-bold rounded-lg transition text-center"
                    :class="filter.tipe === 'semua' ? 'bg-white text-emerald-800 shadow-xs ring-1 ring-slate-200' : 'text-slate-600 hover:text-slate-900'">
                Semua Data
            </button>
            <button type="button"
                    @click="setFilterTipe('bulan')"
                    class="py-2 text-xs font-bold rounded-lg transition text-center"
                    :class="filter.tipe === 'bulan' ? 'bg-white text-emerald-800 shadow-xs ring-1 ring-slate-200' : 'text-slate-600 hover:text-slate-900'">
                Bulan Tercatat
            </button>
            <button type="button"
                    @click="setFilterTipe('tahun')"
                    class="py-2 text-xs font-bold rounded-lg transition text-center"
                    :class="filter.tipe === 'tahun' ? 'bg-white text-emerald-800 shadow-xs ring-1 ring-slate-200' : 'text-slate-600 hover:text-slate-900'">
                Tahun Tercatat
            </button>
        </div>

        <!-- Kontrol Filter Dinamis -->
        <div class="grid grid-cols-2 lg:grid-cols-12 gap-2.5 sm:gap-3 items-end">
            <!-- Pilihan Bulan Tercatat (Hanya Tampil Jika Mode Bulan) -->
            <div x-show="filter.tipe === 'bulan'" class="col-span-2 sm:col-span-1 lg:col-span-4">
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                    Pilih Bulan (Awal s.d Akhir Tercatat)
                </label>
                <select x-model="filter.bulan"
                        @change="applyFilter()"
                        class="w-full text-xs sm:text-sm rounded-xl border-slate-300 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                    @forelse($bulanTersedia as $b)
                        <option value="{{ $b['ym'] }}" {{ $filterBulan === $b['ym'] ? 'selected' : '' }}>
                            {{ $b['label'] }} ({{ $b['total'] }} data)
                        </option>
                    @empty
                        <option value="">Belum ada transaksi</option>
                    @endforelse
                </select>
            </div>

            <!-- Pilihan Tahun Tercatat (Hanya Tampil Jika Mode Tahun) -->
            <div x-show="filter.tipe === 'tahun'" class="col-span-2 sm:col-span-1 lg:col-span-4" style="display: none;">
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                    Pilih Tahun (Rentang Riil)
                </label>
                <select x-model="filter.tahun"
                        @change="applyFilter()"
                        class="w-full text-xs sm:text-sm rounded-xl border-slate-300 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                    @forelse($tahunTersedia as $t)
                        <option value="{{ $t['tahun'] }}" {{ $filterTahun === $t['tahun'] ? 'selected' : '' }}>
                            {{ $t['label'] }} ({{ $t['total'] }} data)
                        </option>
                    @empty
                        <option value="">Belum ada transaksi</option>
                    @endforelse
                </select>
            </div>

            <!-- Filter Jenis Akun (Semua / Debit / Kredit) -->
            <div class="col-span-1 lg:col-span-2">
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                    Jenis Akun
                </label>
                <select x-model="filter.jenis"
                        @change="applyFilter()"
                        class="w-full text-xs sm:text-sm rounded-xl border-slate-300 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Jenis</option>
                    <option value="debit">Debit (Penerimaan)</option>
                    <option value="kredit">Kredit (Pengeluaran)</option>
                </select>
            </div>

            <!-- Filter Pos Akun / Kategori -->
            <div class="col-span-1 lg:col-span-3">
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                    Pos Akun
                </label>
                <select x-model="filter.kategori"
                        @change="applyFilter()"
                        class="w-full text-xs sm:text-sm rounded-xl border-slate-300 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Pos Akun</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}">{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Pencarian Kata Kunci (Debounced Search) -->
            <div class="col-span-2 lg:col-span-3">
                <label class="block text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                    Pencarian
                </label>
                <div class="relative">
                    <input type="text"
                           x-model="filter.q"
                           @input.debounce.350ms="applyFilter()"
                           placeholder="Cari uraian, no bukti, pihak..."
                           class="w-full text-xs sm:text-sm rounded-xl border-slate-300 pl-9 pr-4 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>
        </div>

        <!-- PANEL STATUS PEMBUKUAN & TOMBOL TUTUP BUKU (HANYA MUNCUL DI MODE FILTER BULAN) -->
        <div x-show="filter.tipe === 'bulan'" class="pt-3 border-t border-slate-100 space-y-2.5" style="display: none;">
            <!-- KONDISI 1: JIKA BULAN INI TELAH DITUTUP BUKU (TERKUNCI) -->
            <div x-show="isTutupBuku" class="p-3.5 rounded-2xl bg-slate-900 text-white flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-sm border border-slate-800">
                <div class="flex items-start sm:items-center gap-3 min-w-0">
                    <span class="p-2 rounded-xl bg-emerald-500/20 text-emerald-400 shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs sm:text-sm font-black text-emerald-300">STATUS: BUKU KAS DITUTUP (TERKUNCI)</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-950 text-emerald-300 border border-emerald-800" x-text="filter.bulan"></span>
                        </div>
                        <p class="text-[11px] text-slate-300 mt-0.5">
                            Saldo akhir resmi dibukukan sebagai Saldo Awal bulan berikutnya. Seluruh transaksi di bulan ini dikunci dari perubahan.
                        </p>
                    </div>
                </div>
                <button @click="openModalBukaKunci()"
                        type="button"
                        class="w-full sm:w-auto shrink-0 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 hover:text-amber-200 font-bold text-xs border border-slate-700 shadow-xs transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                    <span>Buka Kunci Buku</span>
                </button>
            </div>

            <!-- KONDISI 2: JIKA BULAN INI BELUM DITUTUP BUKU (TERBUKA) -->
            <div x-show="!isTutupBuku" class="p-3.5 rounded-2xl bg-gradient-to-r from-emerald-50 via-teal-50/50 to-white border border-emerald-200 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-2xs">
                <div class="flex items-start sm:items-center gap-3 min-w-0">
                    <span class="p-2 rounded-xl bg-emerald-600 text-white shadow-xs shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </span>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs sm:text-sm font-black text-emerald-950">PEMBUKUAN BULAN INI TERBUKA</span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-white text-emerald-800 border border-emerald-300" x-text="filter.bulan"></span>
                        </div>
                        <p class="text-[11px] text-emerald-700 mt-0.5">
                            Saldo Awal Kas: <strong class="font-mono text-emerald-950" x-text="cards.saldo_awal || 'Rp 0'">Rp 0</strong> &bull; Saldo Akhir Kas: <strong class="font-mono text-emerald-950" x-text="cards.saldo_akhir_filtered || 'Rp 0'">Rp 0</strong>
                        </p>
                    </div>
                </div>
                <button @click="openModalTutupBuku()"
                        type="button"
                        class="w-full sm:w-auto shrink-0 px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition transform active:scale-95 flex items-center justify-center gap-1.5 whitespace-nowrap">
                    <svg class="w-3.5 h-3.5 text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    <span>Tutup Buku Bulan Ini</span>
                </button>
            </div>
        </div>
    </div>

    <!-- KONTEN MUTASI: DUA TAMPILAN (CARD FEED DI MOBILE, TABEL LENGKAP DI DESKTOP) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden relative">
        <div class="px-4 py-3.5 sm:px-5 sm:py-4 border-b border-slate-200 flex items-center justify-between gap-2 bg-gradient-to-r from-slate-50 to-emerald-50/40">
            <div class="flex items-center gap-2">
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Jurnal Mutasi Kas</h2>
                <!-- Spinner indikator saat loading AJAX -->
                <span x-show="loading"
                      class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 animate-pulse"
                      style="display: none;">
                    <svg class="w-3 h-3 animate-spin text-emerald-600" viewBox="0 0 24 24" fill="none">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span>Memuat data...</span>
                </span>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                <span x-text="totalTransaksi !== undefined ? totalTransaksi : {{ $transaksis->total() }}">{{ $transaksis->total() }}</span>&nbsp;Transaksi
            </span>
        </div>

        <!-- SKELETON LOADER (Tampil Saat AJAX Sedang Mengambil Data) -->
        <div x-show="loading" class="p-4 space-y-3" style="display: none;">
            <div class="animate-pulse space-y-3">
                <div class="h-10 bg-slate-100 rounded-xl"></div>
                <div class="h-14 bg-slate-100 rounded-xl"></div>
                <div class="h-14 bg-slate-100 rounded-xl"></div>
                <div class="h-14 bg-slate-100 rounded-xl"></div>
                <div class="h-14 bg-slate-100 rounded-xl"></div>
            </div>
        </div>

        <!-- 1. TAMPILAN MOBILE: KARTU MUTASI ELEGAN (Khusus Layar HP < 768px) -->
        <div x-show="!loading" class="block md:hidden divide-y divide-slate-100" id="container-mobile-cards">
            @include('bendahara.partials.card_feed', ['transaksis' => $transaksis])
        </div>

        <!-- 2. TAMPILAN DESKTOP: TABEL AKUNTANSI LENGKAP (Khusus Layar Desktop/Laptop >= 768px) -->
        <div x-show="!loading" class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm divide-y divide-slate-200">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5">No. Bukti & Tanggal</th>
                        <th class="px-4 py-3.5">Akun</th>
                        <th class="px-4 py-3.5">Keterangan</th>
                        <th class="px-4 py-3.5">Pihak Terkait</th>
                        <th class="px-4 py-3.5 text-right text-emerald-700">Debit</th>
                        <th class="px-4 py-3.5 text-right text-rose-700">Kredit</th>
                        <th class="px-4 py-3.5 text-center">Bukti</th>
                        <th class="px-4 py-3.5 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white" id="container-desktop-tbody">
                    @include('bendahara.partials.table_rows', ['transaksis' => $transaksis])
                </tbody>
            </table>
        </div>

        <!-- PAGINATION -->
        <div id="container-pagination" class="p-3 border-t border-slate-100">
            @if($transaksis->hasPages())
                {{ $transaksis->links() }}
            @endif
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POP-UP 1: PILIH PERIODE & UNDUH LAPORAN (WORD / PDF)                 -->
    <!-- ========================================================================= -->
    <div x-show="modalDownloadOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div @click.away="modalDownloadOpen = false"
             class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-blue-100 text-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Unduh Laporan Buku Kas</h3>
                        <p class="text-xs text-slate-500">Pilih rentang bulan atau periode yang ingin dicetak</p>
                    </div>
                </div>
                <button @click="modalDownloadOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Pilihan Mode Periode Unduh -->
            <div class="space-y-3">
                <label class="block text-xs font-bold text-slate-700 uppercase">Pilih Mode Periode:</label>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button"
                            @click="downloadMode = 'rentang_bulan'"
                            class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center gap-2"
                            :class="downloadMode === 'rentang_bulan' ? 'border-blue-600 bg-blue-50/70 text-blue-900 ring-1 ring-blue-500' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0"
                              :class="downloadMode === 'rentang_bulan' ? 'border-blue-600 bg-blue-600' : 'border-slate-400'">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        </span>
                        <span>Rentang Bulan (Dari - Sampai)</span>
                    </button>

                    <button type="button"
                            @click="downloadMode = 'satu_bulan'"
                            class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center gap-2"
                            :class="downloadMode === 'satu_bulan' ? 'border-blue-600 bg-blue-50/70 text-blue-900 ring-1 ring-blue-500' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0"
                              :class="downloadMode === 'satu_bulan' ? 'border-blue-600 bg-blue-600' : 'border-slate-400'">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        </span>
                        <span>Satu Bulan Penuh</span>
                    </button>

                    <button type="button"
                            @click="downloadMode = 'satu_tahun'"
                            class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center gap-2"
                            :class="downloadMode === 'satu_tahun' ? 'border-blue-600 bg-blue-50/70 text-blue-900 ring-1 ring-blue-500' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0"
                              :class="downloadMode === 'satu_tahun' ? 'border-blue-600 bg-blue-600' : 'border-slate-400'">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        </span>
                        <span>Satu Tahun Penuh</span>
                    </button>

                    <button type="button"
                            @click="downloadMode = 'semua'"
                            class="p-2.5 rounded-xl border text-left text-xs font-bold transition flex items-center gap-2"
                            :class="downloadMode === 'semua' ? 'border-blue-600 bg-blue-50/70 text-blue-900 ring-1 ring-blue-500' : 'border-slate-200 text-slate-600 hover:bg-slate-50'">
                        <span class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0"
                              :class="downloadMode === 'semua' ? 'border-blue-600 bg-blue-600' : 'border-slate-400'">
                            <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                        </span>
                        <span>Semua Transaksi</span>
                    </button>
                </div>

                <!-- Input Pilihan Rentang Bulan -->
                <div x-show="downloadMode === 'rentang_bulan'" class="grid grid-cols-2 gap-2.5 pt-2">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Dari Bulan:</label>
                        <select x-model="downloadBulanDari" class="w-full text-xs rounded-xl border-slate-300">
                            @foreach($bulanTersedia as $b)
                                <option value="{{ $b['ym'] }}">{{ $b['nama_bulan'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Sampai Bulan:</label>
                        <select x-model="downloadBulanSampai" class="w-full text-xs rounded-xl border-slate-300">
                            @foreach($bulanTersedia as $b)
                                <option value="{{ $b['ym'] }}">{{ $b['nama_bulan'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Input Pilihan Satu Bulan Penuh -->
                <div x-show="downloadMode === 'satu_bulan'" class="pt-2" style="display: none;">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Pilih Bulan:</label>
                    <select x-model="downloadBulanTunggal" class="w-full text-xs rounded-xl border-slate-300">
                        @foreach($bulanTersedia as $b)
                            <option value="{{ $b['ym'] }}">{{ $b['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Pilihan Satu Tahun Penuh -->
                <div x-show="downloadMode === 'satu_tahun'" class="pt-2" style="display: none;">
                    <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Pilih Tahun:</label>
                    <select x-model="downloadTahun" class="w-full text-xs rounded-xl border-slate-300">
                        @foreach($tahunTersedia as $t)
                            <option value="{{ $t['tahun'] }}">{{ $t['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Tombol Aksi Unduh PDF -->
            <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-2">
                <button @click="modalDownloadOpen = false"
                        type="button"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100 transition order-2 sm:order-1">
                    Batal
                </button>

                <button @click="triggerDownload('pdf')"
                        type="button"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs shadow-xs transition order-1 sm:order-2">
                    <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Unduh Laporan PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POP-UP: TUTUP BUKU AKHIR BULAN (DATA LOCKING & RESMIKAN SALDO AWAL) -->
    <!-- ========================================================================= -->
    <div x-show="modalTutupBukuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div @click.away="modalTutupBukuOpen = false"
             class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-emerald-100 text-emerald-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Tutup Buku Akhir Bulan</h3>
                        <p class="text-xs text-slate-500" x-text="'Kunci pembukuan periode ' + (cards.periode_label || filter.bulan)"></p>
                    </div>
                </div>
                <button @click="modalTutupBukuOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Ringkasan Angka yang Akan Dibukukan -->
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200 space-y-2.5 text-xs">
                <div class="flex justify-between items-center text-slate-600">
                    <span>1. Saldo Awal Kas (Sebelum Periode):</span>
                    <strong class="font-mono text-slate-900" x-text="cards.saldo_awal || 'Rp 0'">Rp 0</strong>
                </div>
                <div class="flex justify-between items-center text-emerald-700">
                    <span>2. Total Penerimaan (Debit):</span>
                    <strong class="font-mono" x-text="cards.total_debit || 'Rp 0'">Rp 0</strong>
                </div>
                <div class="flex justify-between items-center text-rose-700">
                    <span>3. Total Pengeluaran (Kredit):</span>
                    <strong class="font-mono" x-text="cards.total_kredit || 'Rp 0'">Rp 0</strong>
                </div>
                <div class="pt-2 border-t border-slate-200 flex justify-between items-center text-slate-900 font-bold text-sm">
                    <span>Saldo Akhir Kas:</span>
                    <strong class="font-mono text-emerald-800 text-base" x-text="cards.saldo_akhir_filtered || 'Rp 0'">Rp 0</strong>
                </div>
                <p class="text-[11px] text-slate-500 leading-relaxed pt-1">
                    *Saldo Akhir ini akan resmi dibukukan dan otomatis menjadi <strong>Saldo Awal Kas</strong> pada tanggal 1 bulan berikutnya. Seluruh transaksi di bulan ini akan terkunci (tidak bisa diedit atau dihapus).
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Catatan Penutupan Buku (Opsional)</label>
                <textarea x-model="tutupBukuCatatan" rows="2" placeholder="Contoh: Selesai diverifikasi kas fisik dan buku kas klop..." class="w-full text-xs rounded-xl border-slate-300"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button @click="modalTutupBukuOpen = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100">Batal</button>
                <button @click="executeTutupBuku()" type="button" :disabled="submittingTutupBuku" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs disabled:opacity-50">
                    <template x-if="submittingTutupBuku">
                        <svg class="w-3.5 h-3.5 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span x-text="submittingTutupBuku ? 'Memproses...' : 'Ya, Tutup Buku & Kunci'">Ya, Tutup Buku & Kunci</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POP-UP: BUKA KUNCI BUKU KAS (PERBAIKAN DARURAT)                     -->
    <!-- ========================================================================= -->
    <div x-show="modalBukaKunciOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div @click.away="modalBukaKunciOpen = false"
             class="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border border-slate-200 text-center space-y-4">
            
            <div class="w-14 h-14 mx-auto rounded-full bg-amber-100 text-amber-700 flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
            </div>

            <div>
                <h3 class="text-base sm:text-lg font-black text-slate-900">Buka Kunci Buku Kas?</h3>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed">
                    Kunci pembukuan periode <span class="font-bold text-slate-800" x-text="cards.periode_label || filter.bulan"></span> akan dibuka kembali. Transaksi pada bulan ini dapat diedit atau ditambah kembali jika diperlukan perbaikan.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-2.5 pt-2">
                <button @click="modalBukaKunciOpen = false"
                        type="button"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>

                <button @click="executeBukaKunci()"
                        type="button"
                        :disabled="submittingBukaKunci"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-xs transition disabled:opacity-50">
                    <template x-if="submittingBukaKunci">
                        <svg class="w-3.5 h-3.5 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </template>
                    <span x-text="submittingBukaKunci ? 'Membuka...' : 'Ya, Buka Kunci'">Ya, Buka Kunci</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL POP-UP 2: KONFIRMASI HAPUS PERMANEN (WARNA MERAH TEGAS & BATAL NETRAL) -->
    <!-- ========================================================================= -->
    <div x-show="modalDeleteOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div @click.away="modalDeleteOpen = false"
             class="bg-white rounded-3xl max-w-sm w-full p-5 sm:p-6 shadow-2xl border border-slate-200 text-center space-y-4">
            
            <!-- Ikon Tempat Sampah Merah Bulat Elegan -->
            <div class="w-14 h-14 mx-auto rounded-full bg-rose-100 text-rose-600 flex items-center justify-center shadow-inner">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>

            <div>
                <h3 class="text-base sm:text-lg font-black text-slate-900">Hapus Transaksi Kas?</h3>
                <p class="text-xs text-slate-500 mt-1">
                    Transaksi <span class="font-bold text-slate-800" x-text="deleteItem.kode"></span>
                    sebesar <span class="font-bold text-rose-600" x-text="'Rp ' + deleteItem.nominal"></span>
                    akan dihapus permanen dari database pembukuan.
                </p>
            </div>

            <!-- Tombol Sesuai Psikologi Warna: Merah Hapus, Abu Netral Batal -->
            <div class="grid grid-cols-2 gap-2.5 pt-2">
                <button @click="modalDeleteOpen = false"
                        type="button"
                        class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition">
                    Batal
                </button>

                <button @click="executeDelete()"
                        type="button"
                        :disabled="deleting"
                        class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition disabled:opacity-50">
                    <template x-if="deleting">
                        <svg class="w-3.5 h-3.5 animate-spin text-white" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </template>
                    <span x-text="deleting ? 'Menghapus...' : 'Ya, Hapus'">Ya, Hapus</span>
                </button>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 3: CATAT DEBIT (PEMASUKAN)                                          -->
    <!-- ========================================================================= -->
    <div x-show="modalDebitOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div @click.away="modalDebitOpen = false"
             class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Catat Kas Masuk (Debit)</h3>
                        <p class="text-xs text-slate-500">Bukukan penerimaan dana kas pesantren</p>
                    </div>
                </div>
                <button @click="modalDebitOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="submitForm($event, 'debit')" enctype="multipart/form-data" class="space-y-3.5">
                <input type="hidden" name="jenis" value="debit">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pos Akun / Kategori <span class="text-rose-500">*</span></label>
                        <input list="kategoriDebitList" name="kategori" required placeholder="Pilih atau ketik pos..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                        <datalist id="kategoriDebitList">
                            @foreach($kategoriDebit as $kd)
                                <option value="{{ $kd }}"></option>
                            @endforeach
                            @foreach($kategoriList as $kl)
                                <option value="{{ $kl }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal Penerimaan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="nominal" min="1" step="any" required placeholder="Contoh: 500000" class="w-full text-sm font-mono font-bold rounded-xl border-slate-300 text-emerald-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Uraian / Keterangan</label>
                    <textarea name="keterangan" rows="2" placeholder="Tulis rincian peruntukan kas masuk..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Diterima Dari (Pihak)</label>
                        <input type="text" name="nama_pihak" placeholder="Nama donatur, santri, instansi..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Foto Bukti / Nota (Maks 5MB)</label>
                        <input type="file" name="lampiran" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs rounded-xl border border-slate-300 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="modalDebitOpen = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100">Batal</button>
                    <button type="submit" :disabled="submitting" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs disabled:opacity-50">
                        <template x-if="submitting">
                            <svg class="w-3.5 h-3.5 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Kas Masuk'">Simpan Kas Masuk</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 4: CATAT KREDIT (PENGELUARAN)                                       -->
    <!-- ========================================================================= -->
    <div x-show="modalKreditOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div @click.away="modalKreditOpen = false"
             class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-rose-100 text-rose-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Catat Kas Keluar (Kredit)</h3>
                        <p class="text-xs text-slate-500">Bukukan belanja atau pengeluaran operasional</p>
                    </div>
                </div>
                <button @click="modalKreditOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="submitForm($event, 'kredit')" enctype="multipart/form-data" class="space-y-3.5">
                <input type="hidden" name="jenis" value="kredit">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pos Akun / Kategori <span class="text-rose-500">*</span></label>
                        <input list="kategoriKreditList" name="kategori" required placeholder="Pilih atau ketik pos..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                        <datalist id="kategoriKreditList">
                            @foreach($kategoriKredit as $kk)
                                <option value="{{ $kk }}"></option>
                            @endforeach
                            @foreach($kategoriList as $kl)
                                <option value="{{ $kl }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal Pengeluaran (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="nominal" min="1" step="any" required placeholder="Contoh: 150000" class="w-full text-sm font-mono font-bold rounded-xl border-slate-300 text-rose-700">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Uraian / Keterangan</label>
                    <textarea name="keterangan" rows="2" placeholder="Tulis rincian belanja kas keluar..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Dibayarkan Kepada (Pihak)</label>
                        <input type="text" name="nama_pihak" placeholder="Nama toko, rekanan, pengurus..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Foto Bukti / Nota (Maks 5MB)</label>
                        <input type="file" name="lampiran" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs rounded-xl border border-slate-300 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="modalKreditOpen = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100">Batal</button>
                    <button type="submit" :disabled="submitting" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs disabled:opacity-50">
                        <template x-if="submitting">
                            <svg class="w-3.5 h-3.5 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="submitting ? 'Menyimpan...' : 'Simpan Kas Keluar'">Simpan Kas Keluar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 5: EDIT TRANSAKSI KAS                                               -->
    <!-- ========================================================================= -->
    <div x-show="modalEditOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div @click.away="modalEditOpen = false"
             class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl border border-slate-200 space-y-4">
            
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-amber-100 text-amber-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </span>
                    <div>
                        <h3 class="text-base sm:text-lg font-black text-slate-900">Ubah Transaksi Kas</h3>
                        <p class="text-xs text-slate-500 font-mono font-bold" x-text="editData.kode_transaksi"></p>
                    </div>
                </div>
                <button @click="modalEditOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form @submit.prevent="submitEditForm($event)" enctype="multipart/form-data" class="space-y-3.5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" x-model="editData.tanggal" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jenis Akun <span class="text-rose-500">*</span></label>
                        <select name="jenis" x-model="editData.jenis" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                            <option value="debit">Debit (Penerimaan)</option>
                            <option value="kredit">Kredit (Pengeluaran)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pos Akun / Kategori <span class="text-rose-500">*</span></label>
                        <input type="text" name="kategori" x-model="editData.kategori" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="nominal" x-model="editData.nominal" min="1" step="any" required class="w-full text-sm font-mono font-bold rounded-xl border-slate-300">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Uraian / Keterangan</label>
                    <textarea name="keterangan" x-model="editData.keterangan" rows="2" class="w-full text-xs sm:text-sm rounded-xl border-slate-300"></textarea>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pihak Terkait</label>
                        <input type="text" name="nama_pihak" x-model="editData.nama_pihak" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ganti Nota / Bukti (Opsional)</label>
                        <input type="file" name="lampiran" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs rounded-xl border border-slate-300 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100">
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button @click="modalEditOpen = false" type="button" class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 font-bold text-xs hover:bg-slate-100">Batal</button>
                    <button type="submit" :disabled="submitting" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-xs disabled:opacity-50">
                        <template x-if="submitting">
                            <svg class="w-3.5 h-3.5 animate-spin text-white" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        </template>
                        <span x-text="submitting ? 'Memperbarui...' : 'Simpan Perubahan'">Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- MODAL 6: PRATINJAU BUKTI FOTO                                             -->
    <!-- ========================================================================= -->
    <div x-show="modalPreviewOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         style="display: none;">
        <div @click.away="modalPreviewOpen = false"
             class="bg-white rounded-3xl max-w-2xl w-full p-4 sm:p-5 shadow-2xl border border-slate-200 space-y-3">
            <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                <h3 class="text-sm sm:text-base font-bold text-slate-800 truncate" x-text="previewTitle"></h3>
                <button @click="modalPreviewOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="bg-slate-100 rounded-2xl p-2 flex items-center justify-center max-h-[75vh] overflow-auto">
                <img :src="previewUrl" alt="Bukti Transaksi" class="max-w-full h-auto rounded-xl shadow-xs">
            </div>
            <div class="flex justify-end pt-1">
                <button @click="modalPreviewOpen = false" type="button" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200">Tutup</button>
            </div>
        </div>
    </div>

</div>

<!-- SCRIPT ALPINE.JS LOGIKA TANPA RELOAD & MODAL UNDUH -->
<script>
function bukuKasApp() {
    return {
        loading: false,
        submitting: false,
        deleting: false,
        modalDebitOpen: false,
        modalKreditOpen: false,
        modalEditOpen: false,
        modalPreviewOpen: false,
        modalDownloadOpen: false,
        modalDeleteOpen: false,
        modalTutupBukuOpen: false,
        modalBukaKunciOpen: false,
        tutupBukuCatatan: '',
        submittingTutupBuku: false,
        submittingBukaKunci: false,
        isTutupBuku: {{ $isTutupBuku ? 'true' : 'false' }},
        
        // Pilihan Unduh Laporan
        downloadMode: 'rentang_bulan',
        downloadBulanDari: '{{ $bulanTersedia->first()["ym"] ?? date("Y-m") }}',
        downloadBulanSampai: '{{ $bulanTersedia->first()["ym"] ?? date("Y-m") }}',
        downloadBulanTunggal: '{{ $filterBulan ?: ($bulanTersedia->first()["ym"] ?? date("Y-m")) }}',
        downloadTahun: '{{ $filterTahun ?: ($tahunTersedia->first()["tahun"] ?? date("Y")) }}',

        // Data Hapus
        deleteItem: {
            id: null,
            kode: '',
            nominal: ''
        },

        // Data Edit
        editData: {
            id: '',
            kode_transaksi: '',
            tanggal: '',
            jenis: 'debit',
            kategori: '',
            nominal: '',
            keterangan: '',
            nama_pihak: '',
            lampiran: ''
        },

        previewUrl: '',
        previewTitle: '',

        // 3 Filter State
        filter: {
            tipe: '{{ $filterTipe }}',
            bulan: '{{ $filterBulan }}',
            tahun: '{{ $filterTahun }}',
            jenis: '{{ $jenis }}',
            kategori: '{{ $kategori }}',
            q: '{{ $q }}'
        },

        // Ringkasan Kartu
        cards: {
            saldo_kas: 'Rp {{ number_format($saldoKasKeseluruhan, 0, ",", ".") }}',
            saldo_kas_raw: {{ $saldoKasKeseluruhan }},
            total_debit: 'Rp {{ number_format($totalDebitFiltered, 0, ",", ".") }}',
            total_kredit: 'Rp {{ number_format($totalKreditFiltered, 0, ",", ".") }}',
            surplus_defisit: '{{ $surplusDefisitFiltered >= 0 ? "+" : "" }}Rp {{ number_format($surplusDefisitFiltered, 0, ",", ".") }}',
            is_surplus: {{ $surplusDefisitFiltered >= 0 ? "true" : "false" }},
            total_count: {{ $totalCountFiltered }},
            periode_label: '{{ $labelPeriode }}',
            saldo_awal: 'Rp {{ number_format($saldoAwal, 0, ",", ".") }}',
            saldo_akhir_filtered: 'Rp {{ number_format($saldoAkhirFiltered, 0, ",", ".") }}'
        },

        totalTransaksi: {{ $transaksis->total() }},

        // Toast State
        toast: {
            show: false,
            type: 'success',
            title: '',
            message: ''
        },

        initApp() {
            // Cek jika ada flash session dari controller sebelumnya
            @if(session('success'))
                this.showToast('success', 'Berhasil', '{{ session('success') }}');
            @endif
            @if(session('error'))
                this.showToast('error', 'Gagal', '{{ session('error') }}');
            @endif
        },

        showToast(type, title, message) {
            this.toast = { show: true, type, title, message };
            setTimeout(() => { this.toast.show = false; }, 4000);
        },

        setFilterTipe(tipe) {
            this.filter.tipe = tipe;
            this.applyFilter();
        },

        resetFilter() {
            this.filter.tipe = 'semua';
            this.filter.bulan = '';
            this.filter.tahun = '';
            this.filter.jenis = '';
            this.filter.kategori = '';
            this.filter.q = '';
            this.applyFilter();
        },

        applyFilter(page = 1) {
            this.loading = true;
            const params = new URLSearchParams();
            params.set('filter_tipe', this.filter.tipe);
            if (this.filter.tipe === 'bulan' && this.filter.bulan) params.set('filter_bulan', this.filter.bulan);
            if (this.filter.tipe === 'tahun' && this.filter.tahun) params.set('filter_tahun', this.filter.tahun);
            if (this.filter.jenis) params.set('jenis', this.filter.jenis);
            if (this.filter.kategori) params.set('kategori', this.filter.kategori);
            if (this.filter.q) params.set('q', this.filter.q);
            if (page > 1) params.set('page', page);

            const url = '{{ route("admin.bendahara.index") }}?' + params.toString();
            window.history.replaceState({}, '', url);

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.cards = data.html_cards;
                    this.totalTransaksi = data.total_transaksi;
                    this.isTutupBuku = !!data.is_tutup_buku;
                    document.getElementById('container-mobile-cards').innerHTML = data.html_mobile;
                    document.getElementById('container-desktop-tbody').innerHTML = data.html_desktop;
                    document.getElementById('container-pagination').innerHTML = data.html_pagination;
                }
            })
            .catch(err => {
                console.error(err);
                this.showToast('error', 'Gagal Memuat Data', 'Terjadi kesalahan saat memfilter data.');
            })
            .finally(() => {
                this.loading = false;
            });
        },

        openDebitModal() {
            this.modalDebitOpen = true;
        },

        openKreditModal() {
            this.modalKreditOpen = true;
        },

        openEdit(item) {
            this.editData = {
                id: item.id,
                kode_transaksi: item.kode_transaksi,
                tanggal: item.tanggal,
                jenis: item.jenis,
                kategori: item.kategori,
                nominal: item.nominal,
                keterangan: item.keterangan || '',
                nama_pihak: item.nama_pihak || '',
                lampiran: item.lampiran || ''
            };
            this.modalEditOpen = true;
        },

        openPreview(url, title) {
            this.previewUrl = url;
            this.previewTitle = title;
            this.modalPreviewOpen = true;
        },

        confirmDelete(id, kode, nominal) {
            this.deleteItem = { id, kode, nominal };
            this.modalDeleteOpen = true;
        },

        executeDelete() {
            if (!this.deleteItem.id) return;
            this.deleting = true;
            const url = '{{ url("admin/bendahara") }}/' + this.deleteItem.id;

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                this.modalDeleteOpen = false;
                this.showToast('success', 'Terhapus', data.message || 'Transaksi berhasil dihapus secara permanen.');
                
                // Animasi fade out baris yang dihapus sebelum reload tabel
                const rowDesktop = document.getElementById('transaksi-row-' + this.deleteItem.id);
                const cardMobile = document.getElementById('transaksi-card-' + this.deleteItem.id);
                if (rowDesktop) rowDesktop.classList.add('opacity-0', 'transition-opacity', 'duration-300');
                if (cardMobile) cardMobile.classList.add('opacity-0', 'transition-opacity', 'duration-300');

                setTimeout(() => {
                    this.applyFilter();
                }, 300);
            })
            .catch(err => {
                console.error(err);
                this.showToast('error', 'Gagal', 'Gagal menghapus transaksi kas.');
            })
            .finally(() => {
                this.deleting = false;
            });
        },

        submitForm(e, jenis) {
            this.submitting = true;
            const form = e.target;
            const formData = new FormData(form);

            fetch('{{ route("admin.bendahara.store") }}', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) return res.json().then(err => { throw err; });
                return res.json();
            })
            .then(data => {
                if (jenis === 'debit') this.modalDebitOpen = false;
                if (jenis === 'kredit') this.modalKreditOpen = false;
                form.reset();

                this.showToast('success', 'Berhasil', data.message);
                this.applyFilter();

                // Highlight baris baru setelah filter dimuat
                if (data.transaksi_id) {
                    setTimeout(() => {
                        this.highlightRow(data.transaksi_id);
                    }, 400);
                }
            })
            .catch(err => {
                console.error(err);
                const msg = err.message || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Terjadi kesalahan saat menyimpan transaksi.');
                this.showToast('error', 'Validasi Gagal', msg);
            })
            .finally(() => {
                this.submitting = false;
            });
        },

        submitEditForm(e) {
            this.submitting = true;
            const form = e.target;
            const formData = new FormData(form);
            formData.append('_method', 'PUT');

            const url = '{{ url("admin/bendahara") }}/' + this.editData.id;

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(res => {
                if (!res.ok) return res.json().then(err => { throw err; });
                return res.json();
            })
            .then(data => {
                this.modalEditOpen = false;
                this.showToast('success', 'Berhasil Diperbarui', data.message);
                const editedId = this.editData.id;
                this.applyFilter();

                setTimeout(() => {
                    this.highlightRow(editedId);
                }, 400);
            })
            .catch(err => {
                console.error(err);
                const msg = err.message || (err.errors ? Object.values(err.errors).flat().join('<br>') : 'Gagal memperbarui transaksi.');
                this.showToast('error', 'Validasi Gagal', msg);
            })
            .finally(() => {
                this.submitting = false;
            });
        },

        highlightRow(id) {
            const rowDesktop = document.getElementById('transaksi-row-' + id);
            const cardMobile = document.getElementById('transaksi-card-' + id);
            
            [rowDesktop, cardMobile].forEach(el => {
                if (el) {
                    el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    el.classList.add('bg-emerald-100', 'ring-2', 'ring-emerald-500', 'transition', 'duration-1000');
                    setTimeout(() => {
                        el.classList.remove('bg-emerald-100', 'ring-2', 'ring-emerald-500');
                    }, 2500);
                }
            });
        },

        openModalTutupBuku() {
            this.tutupBukuCatatan = '';
            this.modalTutupBukuOpen = true;
        },

        openModalBukaKunci() {
            this.modalBukaKunciOpen = true;
        },

        executeTutupBuku() {
            if (!this.filter.bulan) {
                this.showToast('error', 'Gagal', 'Pilih bulan yang ingin ditutup buku terlebih dahulu.');
                return;
            }
            this.submittingTutupBuku = true;

            fetch('{{ route("admin.bendahara.tutup-buku") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tahun_bulan: this.filter.bulan,
                    catatan: this.tutupBukuCatatan
                })
            })
            .then(res => {
                if (!res.ok) return res.json().then(err => { throw err; });
                return res.json();
            })
            .then(data => {
                this.modalTutupBukuOpen = false;
                this.isTutupBuku = true;
                this.showToast('success', 'Buku Kas Ditutup', data.message);
                this.applyFilter();
            })
            .catch(err => {
                console.error(err);
                this.showToast('error', 'Gagal Tutup Buku', err.message || 'Terjadi kesalahan saat menutup buku.');
            })
            .finally(() => {
                this.submittingTutupBuku = false;
            });
        },

        executeBukaKunci() {
            if (!this.filter.bulan) {
                this.showToast('error', 'Gagal', 'Pilih bulan yang ingin dibuka kuncinya terlebih dahulu.');
                return;
            }
            this.submittingBukaKunci = true;

            fetch('{{ route("admin.bendahara.buka-kunci") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    tahun_bulan: this.filter.bulan
                })
            })
            .then(res => {
                if (!res.ok) return res.json().then(err => { throw err; });
                return res.json();
            })
            .then(data => {
                this.modalBukaKunciOpen = false;
                this.isTutupBuku = false;
                this.showToast('success', 'Kunci Dibuka', data.message);
                this.applyFilter();
            })
            .catch(err => {
                console.error(err);
                this.showToast('error', 'Gagal Buka Kunci', err.message || 'Terjadi kesalahan saat membuka kunci.');
            })
            .finally(() => {
                this.submittingBukaKunci = false;
            });
        },

        triggerDownload(format = 'pdf') {
            const params = new URLSearchParams();
            params.set('mode', this.downloadMode);

            if (this.downloadMode === 'rentang_bulan') {
                params.set('bulan_dari', this.downloadBulanDari);
                params.set('bulan_sampai', this.downloadBulanSampai);
            } else if (this.downloadMode === 'satu_bulan') {
                params.set('bulan', this.downloadBulanTunggal);
            } else if (this.downloadMode === 'satu_tahun') {
                params.set('tahun', this.downloadTahun);
            }

            if (this.filter.jenis) params.set('jenis', this.filter.jenis);
            if (this.filter.kategori) params.set('kategori', this.filter.kategori);
            if (this.filter.q) params.set('q', this.filter.q);

            const targetUrl = '{{ route("admin.bendahara.cetak") }}?' + params.toString();

            this.modalDownloadOpen = false;
            window.open(targetUrl, '_blank');
        }
    };
}
</script>
@endsection
