@extends('layouts.app')

@section('title', 'Laporan Laba Rugi - Bendahara Al-Falah Putra')

@section('content')
<div class="space-y-6" x-data="labaRugiApp()">

    <!-- ========================================================================= -->
    <!-- HEADER & TAB NAVIGASI MODUL BENDAHARA                                     -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-slate-200/80 shadow-xs">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Akuntansi & Keuangan
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs font-semibold text-slate-500">Pondok Pesantren Al-Falah Putra</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Laba Rugi (Surplus / Defisit)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Ikhtisar komprehensif penerimaan, pengeluaran, dan hasil bersih kas operasional periode 
                <strong class="text-slate-800">{{ $labelPeriode }}</strong>.
            </p>
        </div>

        <!-- Tombol Aksi Header: Unduh PDF -->
        <div class="flex items-center gap-2.5 self-start md:self-center">
            <a href="{{ route('admin.bendahara.laba-rugi.cetak', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-xs transition">
                <svg class="w-4 h-4 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Laporan PDF</span>
            </a>
        </div>
    </div>

    <!-- TAB NAVIGASI SUB-MODUL BENDAHARA (3 TAB LENGKAP) -->
    <div class="flex items-center gap-2 border-b border-slate-200">
        <a href="{{ route('admin.bendahara.index') }}"
           class="px-4 py-3 text-xs sm:text-sm font-bold text-slate-600 hover:text-emerald-700 border-b-2 border-transparent hover:border-slate-300 transition flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span>Buku Kas Umum</span>
        </a>

        <a href="{{ route('admin.bendahara.laba-rugi') }}"
           class="px-4 py-3 text-xs sm:text-sm font-bold text-emerald-800 border-b-2 border-emerald-600 flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            <span>Laporan Laba Rugi</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">Aktif</span>
        </a>

        <a href="{{ route('admin.bendahara.neraca') }}"
           class="px-4 py-3 text-xs sm:text-sm font-bold text-slate-600 hover:text-emerald-700 border-b-2 border-transparent hover:border-slate-300 transition flex items-center gap-2">
            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            <span>Laporan Neraca</span>
        </a>
    </div>

    <!-- ========================================================================= -->
    <!-- PANEL FILTER PERIODE LAPORAN                                              -->
    <!-- ========================================================================= -->
    <div class="bg-white p-4 sm:p-5 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
        <form method="GET" action="{{ route('admin.bendahara.laba-rugi') }}" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">Pilih Periode Pembukuan:</span>
                </div>

                <!-- 4 Pilihan Mode Filter Cepat -->
                <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 self-start sm:self-auto">
                    <button type="submit" name="filter_tipe" value="bulan"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $filterTipe === 'bulan' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Bulanan
                    </button>
                    <button type="submit" name="filter_tipe" value="tahun"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $filterTipe === 'tahun' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Tahunan
                    </button>
                    <button type="submit" name="filter_tipe" value="kustom"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $filterTipe === 'kustom' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Rentang Bebas
                    </button>
                    <button type="submit" name="filter_tipe" value="semua"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition {{ $filterTipe === 'semua' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Semua
                    </button>
                </div>
            </div>

            <!-- Kontrol Input Berdasarkan Tipe Filter -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end">
                @if($filterTipe === 'bulan')
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Pilih Bulan & Tahun:</label>
                        <select name="filter_bulan" onchange="this.form.submit()" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                            @foreach($bulanTersedia as $b)
                                <option value="{{ $b['ym'] }}" {{ $filterBulan === $b['ym'] ? 'selected' : '' }}>
                                    {{ $b['nama_bulan'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif($filterTipe === 'tahun')
                    <div class="col-span-1 sm:col-span-2">
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Pilih Tahun Anggaran:</label>
                        <select name="filter_tahun" onchange="this.form.submit()" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                            @foreach($tahunTersedia as $t)
                                <option value="{{ $t['tahun'] }}" {{ $filterTahun == $t['tahun'] ? 'selected' : '' }}>
                                    {{ $t['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @elseif($filterTipe === 'kustom')
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Dari Tanggal:</label>
                        <input type="date" name="dari_tanggal" value="{{ $dari }}" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase mb-1">Sampai Tanggal:</label>
                        <input type="date" name="sampai_tanggal" value="{{ $sampai }}" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                    <div>
                        <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition">
                            Terapkan Periode
                        </button>
                    </div>
                @else
                    <div class="col-span-1 sm:col-span-2">
                        <p class="text-xs text-slate-500 py-2">
                            Menampilkan akumulasi seluruh data pembukuan kas sejak awal hingga sekarang.
                        </p>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 3 KARTU METRIK: TOTAL PENDAPATAN, TOTAL BEBAN & SURPLUS/DEFISIT           -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <!-- KARTU 1: PENDAPATAN -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Pendapatan (Debit)</span>
                    <h3 class="text-2xl font-black text-emerald-700 mt-1 font-mono">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $pendapatans->count() }} pos kategori penerimaan
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </div>
            </div>
            <div class="h-1 bg-emerald-500 absolute bottom-0 left-0 right-0"></div>
        </div>

        <!-- KARTU 2: BEBAN -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Beban (Kredit)</span>
                    <h3 class="text-2xl font-black text-rose-700 mt-1 font-mono">
                        Rp {{ number_format($totalBeban, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $bebans->count() }} pos kategori pengeluaran
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </div>
            </div>
            <div class="h-1 bg-rose-500 absolute bottom-0 left-0 right-0"></div>
        </div>

        <!-- KARTU 3: SURPLUS / DEFISIT BERSIH -->
        <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isSurplus ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $isSurplus ? 'Surplus Bersih Kas' : 'Defisit Bersih Kas' }}
                    </span>
                    <h3 class="text-2xl font-black mt-1 font-mono {{ $isSurplus ? 'text-emerald-800' : 'text-rose-800' }}">
                        {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Rasio Operasional: <strong>{{ $marginSurplusDefisit }}%</strong>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 {{ $isSurplus ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($isSurplus)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        @endif
                    </svg>
                </div>
            </div>
            <div class="h-1 absolute bottom-0 left-0 right-0 {{ $isSurplus ? 'bg-emerald-600' : 'bg-rose-600' }}"></div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- TABEL RINCIAN PENDAPATAN & BEBAN (DUA SISI / SEBELAH-MENYEBELAH DI DESKTOP) -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- KOLOM KIRI: RINCIAN PENDAPATAN (PENERIMAAN) -->
        <div class="bg-white rounded-3xl border border-emerald-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:p-5 bg-emerald-50/60 border-b border-emerald-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                    </span>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-emerald-950">I. Pendapatan Kas (Penerimaan)</h2>
                        <p class="text-[11px] text-emerald-700">Rincian per pos akun pemasukan</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200">
                    {{ $pendapatans->count() }} Akun
                </span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs sm:text-sm divide-y divide-slate-100">
                    <thead class="bg-emerald-50/50 text-[11px] font-bold uppercase text-emerald-900 border-b border-emerald-100">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">No</th>
                            <th class="px-4 py-3">Pos Akun / Kategori</th>
                            <th class="px-4 py-3 text-center">Volume</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3 text-right w-16">Porsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($pendapatans as $idx => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 text-center text-slate-400 text-xs">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $item->kategori }}</td>
                                <td class="px-4 py-3 text-center text-xs text-slate-500">{{ $item->jumlah_transaksi }}x</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-emerald-700">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-slate-500">
                                    {{ $item->persentase }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 text-xs italic">
                                    Tidak ada mutasi penerimaan kas pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Subtotal Pendapatan -->
            <div class="p-4 bg-emerald-50 border-t-2 border-emerald-500 flex items-center justify-between">
                <span class="text-xs font-black text-emerald-950 uppercase">Total Pendapatan (A):</span>
                <span class="font-mono font-black text-base text-emerald-800">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- KOLOM KANAN: RINCIAN BEBAN (PENGELUARAN) -->
        <div class="bg-white rounded-3xl border border-rose-200/80 shadow-xs overflow-hidden flex flex-col">
            <div class="p-4 sm:p-5 bg-rose-50/60 border-b border-rose-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-xl bg-rose-100 text-rose-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                    </span>
                    <div>
                        <h2 class="text-sm sm:text-base font-bold text-rose-900">II. Beban Kas (Pengeluaran)</h2>
                        <p class="text-[11px] text-rose-700">Rincian per pos kategori biaya operasional</p>
                    </div>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-200">
                    {{ $bebans->count() }} Akun
                </span>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left text-xs sm:text-sm divide-y divide-slate-100">
                    <thead class="bg-rose-50/50 text-[11px] font-bold uppercase text-rose-900 border-b border-rose-100">
                        <tr>
                            <th class="px-4 py-3 text-center w-10">No</th>
                            <th class="px-4 py-3">Pos Akun / Kategori</th>
                            <th class="px-4 py-3 text-center">Volume</th>
                            <th class="px-4 py-3 text-right">Nominal</th>
                            <th class="px-4 py-3 text-right w-16">Porsi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($bebans as $idx => $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="px-4 py-3 text-center text-slate-400 text-xs">{{ $idx + 1 }}</td>
                                <td class="px-4 py-3 font-semibold text-slate-800">{{ $item->kategori }}</td>
                                <td class="px-4 py-3 text-center text-xs text-slate-500">{{ $item->jumlah_transaksi }}x</td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-rose-700">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-right font-mono text-xs text-slate-500">
                                    {{ $item->persentase }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400 text-xs italic">
                                    Tidak ada mutasi pengeluaran kas pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Subtotal Beban -->
            <div class="p-4 bg-rose-50 border-t-2 border-rose-500 flex items-center justify-between">
                <span class="text-xs font-black text-rose-900 uppercase">Total Beban (B):</span>
                <span class="font-mono font-black text-base text-rose-800">
                    Rp {{ number_format($totalBeban, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- KESIMPULAN REKAPITULASI HASIL BERSIH (SURPLUS / DEFISIT)                 -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-3xl p-5 sm:p-6 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-2xl {{ $isSurplus ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
            <div>
                <span class="text-xs font-black uppercase tracking-wider {{ $isSurplus ? 'text-emerald-800' : 'text-rose-800' }}">
                    Hasil Akhir Pembukuan (A - B):
                </span>
                <h3 class="text-lg sm:text-xl font-black {{ $isSurplus ? 'text-emerald-900' : 'text-rose-900' }} mt-0.5">
                    {{ $isSurplus ? 'SURPLUS KAS OPERASIONAL' : 'DEFISIT KAS OPERASIONAL' }}
                </h3>
                <p class="text-xs {{ $isSurplus ? 'text-emerald-700' : 'text-rose-700' }} mt-1">
                    @if($isSurplus)
                        Total penerimaan kas melebihi total beban operasional sebesar <strong>Rp {{ number_format($surplusDefisit, 0, ',', '.') }}</strong> (Margin surplus: {{ $marginSurplusDefisit }}%).
                    @else
                        Total beban operasional melebihi penerimaan kas sebesar <strong>Rp {{ number_format(abs($surplusDefisit), 0, ',', '.') }}</strong> (Margin defisit: {{ $marginSurplusDefisit }}%).
                    @endif
                </p>
            </div>

            <div class="text-right">
                <div class="text-2xl sm:text-3xl font-black font-mono {{ $isSurplus ? 'text-emerald-800' : 'text-rose-800' }}">
                    {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </div>
            </div>
        </div>
    </div>

</div>

<script>
function labaRugiApp() {
    return {
        // Alpine logic placeholder jika diperlukan interaksi client-side
    };
}
</script>
@endsection
