@extends('layouts.app')

@section('title', 'Laporan Laba Rugi - Bendahara Al-Falah Putra')

@section('content')
<div class="space-y-6" x-data="labaRugiApp()">

    <!-- ========================================================================= -->
    <!-- HEADER & AKSI UTAMA                                                       -->
    <!-- ========================================================================= -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div class="min-w-0">
            <div class="flex items-center gap-2 mb-1 flex-wrap">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                    Akuntansi & Keuangan
                </span>
                <span class="text-xs text-slate-400">&bull;</span>
                <span class="text-xs font-semibold text-slate-500">Pondok Pesantren Al-Falah Putra</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Laporan Laba Rugi (Surplus / Defisit)</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5 leading-relaxed">
                Ikhtisar komprehensif penerimaan, pengeluaran, dan hasil bersih kas operasional periode 
                <strong class="text-slate-800 font-semibold">{{ $labelPeriode }}</strong>.
            </p>
        </div>

        <!-- Tombol Aksi Header: Unduh PDF -->
        <div class="flex items-center gap-2.5 w-full sm:w-auto">
            <a href="{{ route('admin.bendahara.laba-rugi.cetak', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-4 py-2.5 rounded-xl bg-blue-700 hover:bg-blue-800 text-white font-bold text-xs sm:text-sm shadow-xs transition">
                <svg class="w-4 h-4 text-blue-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span>Unduh Laporan PDF</span>
            </a>
        </div>
    </div>

    <!-- TAB NAVIGASI SUB-MODUL BENDAHARA (RESPONSIF TOUCH & DESKTOP) -->
    <div class="overflow-x-auto scrollbar-none whitespace-nowrap border-b border-slate-200 -mx-4 px-4 sm:mx-0 sm:px-0">
        <div class="flex items-center gap-2 min-w-max">
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
    </div>

    <!-- ========================================================================= -->
    <!-- PANEL FILTER PERIODE LAPORAN                                              -->
    <!-- ========================================================================= -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200 shadow-xs space-y-4">
        <form method="GET" action="{{ route('admin.bendahara.laba-rugi') }}" class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-slate-100 text-slate-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </span>
                    <span class="text-xs sm:text-sm font-bold text-slate-800">Pilih Periode Pembukuan:</span>
                </div>

                <!-- 4 Pilihan Mode Filter Cepat -->
                <div class="grid grid-cols-2 sm:inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 w-full sm:w-auto gap-1 sm:gap-0">
                    <button type="submit" name="filter_tipe" value="bulan"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition text-center {{ $filterTipe === 'bulan' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Bulanan
                    </button>
                    <button type="submit" name="filter_tipe" value="tahun"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition text-center {{ $filterTipe === 'tahun' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Tahunan
                    </button>
                    <button type="submit" name="filter_tipe" value="kustom"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition text-center {{ $filterTipe === 'kustom' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                        Rentang Bebas
                    </button>
                    <button type="submit" name="filter_tipe" value="semua"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition text-center {{ $filterTipe === 'semua' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
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
    <!-- METRIK UTAMA: MOBILE OVERVIEW CARD (md:hidden) & DESKTOP 3-CARD (hidden md:grid) -->
    <!-- ========================================================================= -->

    <!-- 1. TAMPILAN KHUSUS MOBILE: SMART FINANCIAL OVERVIEW CARD -->
    <div class="md:hidden bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs space-y-3.5">
        <!-- Status & Nominal Bersih Utama -->
        <div class="flex items-center justify-between gap-3">
            <div class="min-w-0">
                <span class="text-xs font-bold uppercase tracking-wider {{ $isSurplus ? 'text-emerald-700' : 'text-rose-700' }} block">
                    {{ $isSurplus ? 'Surplus Kas Operasional' : 'Defisit Kas Operasional' }}
                </span>
                <div class="text-xl sm:text-2xl font-black font-mono tracking-tight {{ $isSurplus ? 'text-emerald-800' : 'text-rose-800' }} mt-0.5">
                    {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </div>
            </div>
            <div class="px-2.5 py-1 rounded-xl text-xs font-bold shrink-0 {{ $isSurplus ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-rose-100 text-rose-800 border border-rose-200' }}">
                Rasio: {{ $marginSurplusDefisit }}%
            </div>
        </div>

        <!-- 2 Kolom Mini Sejajar: Penerimaan vs Beban -->
        <div class="grid grid-cols-2 gap-3 pt-3 border-t border-slate-100">
            <div class="p-3 rounded-xl bg-emerald-50/70 border border-emerald-100">
                <span class="text-xs font-bold uppercase tracking-wide text-emerald-800 block truncate">Total Pendapatan (A)</span>
                <span class="text-sm font-bold font-mono text-emerald-700 block truncate mt-1">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </span>
                <span class="text-xs text-emerald-600 block truncate mt-0.5">Termasuk Saldo Lalu</span>
            </div>
            <div class="p-3 rounded-xl bg-rose-50/70 border border-rose-100">
                <span class="text-xs font-bold uppercase tracking-wide text-rose-800 block truncate">Total Beban (B)</span>
                <span class="text-sm font-bold font-mono text-rose-700 block truncate mt-1">
                    Rp {{ number_format($totalBeban, 0, ',', '.') }}
                </span>
                <span class="text-xs text-rose-600 block truncate mt-0.5">{{ $bebans->count() }} pos pengeluaran</span>
            </div>
        </div>
    </div>

    <!-- 2. TAMPILAN DESKTOP: 3 KARTU SEJAJAR LENGKAP -->
    <div class="hidden md:grid md:grid-cols-3 gap-4 sm:gap-5">

        <!-- KARTU 1: PENDAPATAN + SALDO PERIODE LALU -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block truncate">Total Pendapatan (Debit)</span>
                    <h3 class="text-xl sm:text-2xl font-black text-emerald-700 mt-1 font-mono tracking-tight">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 truncate">
                        Termasuk Saldo Lalu (Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }})
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                </div>
            </div>
            <div class="h-1 bg-emerald-500 absolute bottom-0 left-0 right-0"></div>
        </div>

        <!-- KARTU 2: BEBAN -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 block truncate">Total Beban (Kredit)</span>
                    <h3 class="text-xl sm:text-2xl font-black text-rose-700 mt-1 font-mono tracking-tight">
                        Rp {{ number_format($totalBeban, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 truncate">
                        {{ $bebans->count() }} pos kategori pengeluaran
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                </div>
            </div>
            <div class="h-1 bg-rose-500 absolute bottom-0 left-0 right-0"></div>
        </div>

        <!-- KARTU 3: SURPLUS / DEFISIT BERSIH -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-slate-200 shadow-xs relative overflow-hidden flex flex-col justify-between">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1">
                    <span class="text-xs font-bold uppercase tracking-wider {{ $isSurplus ? 'text-emerald-700' : 'text-rose-700' }} block truncate">
                        {{ $isSurplus ? 'Surplus Kas Operasional' : 'Defisit Kas Operasional' }}
                    </span>
                    <h3 class="text-xl sm:text-2xl font-black mt-1 font-mono tracking-tight {{ $isSurplus ? 'text-emerald-800' : 'text-rose-800' }}">
                        {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                    </h3>
                    <p class="text-xs text-slate-500 mt-1.5 truncate">
                        Rasio Sisa Kas: <strong>{{ $marginSurplusDefisit }}%</strong>
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 {{ $isSurplus ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
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
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 sm:gap-6 items-stretch">

        <!-- KOLOM KIRI: RINCIAN PENDAPATAN (PENERIMAAN + SALDO PERIODE LALU) -->
        <div class="bg-white rounded-2xl border border-emerald-200/80 shadow-xs overflow-hidden flex flex-col justify-between h-full">
            <div>
                <div class="p-4 sm:p-4.5 bg-emerald-50/70 border-b border-emerald-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-700 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 14l-7 7m0 0l-7-7m7 7V3"/></svg>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-xs sm:text-sm font-bold text-emerald-950 truncate">I. Pendapatan Kas (Penerimaan)</h2>
                            <p class="text-xs text-emerald-700 truncate mt-0.5">Saldo periode lalu + rincian penerimaan</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800 border border-emerald-200 shrink-0">
                        {{ $pendapatans->count() + 1 }} Komponen
                    </span>
                </div>

                <!-- Tampilan Desktop: Tabel Lengkap -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm divide-y divide-slate-100">
                        <thead class="bg-emerald-50/50 text-xs font-bold uppercase tracking-wider text-emerald-900 border-b border-emerald-100">
                            <tr>
                                <th class="px-3.5 py-3 text-center w-10">No</th>
                                <th class="px-3.5 py-3">Pos Akun / Kategori</th>
                                <th class="px-3 py-3 text-center w-20">Volume</th>
                                <th class="px-3.5 py-3 text-right">Nominal</th>
                                <th class="px-3.5 py-3 text-right w-16">Porsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <!-- Baris 1: Saldo Periode Sebelumnya (Bulan Lalu / Tahun Lalu) -->
                            <tr class="bg-emerald-50/30 hover:bg-emerald-50/60 transition">
                                <td class="px-3.5 py-3.5 text-center text-emerald-700 font-bold text-xs">1</td>
                                <td class="px-3.5 py-3.5 font-bold text-slate-800 leading-snug">
                                    {{ $labelSaldoAwal ?? (($filterTipe ?? 'bulan') === 'tahun' ? 'Saldo Kas Periode Sebelumnya (Tahun Lalu)' : 'Saldo Kas Periode Sebelumnya (Bulan Lalu)') }}
                                </td>
                                <td class="px-3 py-3.5 text-center text-xs text-slate-500 whitespace-nowrap">Saldo Awal</td>
                                <td class="px-3.5 py-3.5 text-right font-mono font-bold text-xs sm:text-sm text-emerald-700 whitespace-nowrap">
                                    Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="px-3.5 py-3.5 text-right font-mono text-xs text-slate-500 whitespace-nowrap">
                                    {{ $persentaseSaldoAwal ?? (($totalPendapatan > 0 && ($saldoAwal ?? 0) > 0) ? round((($saldoAwal ?? 0) / $totalPendapatan) * 100, 1) : 0) }}%
                                </td>
                            </tr>

                            <!-- Baris Berikutnya: Penerimaan Kas Periode Ini -->
                            @forelse($pendapatans as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-3.5 py-3.5 text-center text-slate-400 text-xs">{{ $idx + 2 }}</td>
                                    <td class="px-3.5 py-3.5 font-medium text-slate-800 leading-snug">{{ $item->kategori }}</td>
                                    <td class="px-3 py-3.5 text-center text-xs text-slate-500 whitespace-nowrap">{{ $item->jumlah_transaksi }}x</td>
                                    <td class="px-3.5 py-3.5 text-right font-mono font-bold text-xs sm:text-sm text-emerald-700 whitespace-nowrap">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3.5 py-3.5 text-right font-mono text-xs text-slate-500 whitespace-nowrap">
                                        {{ $item->persentase }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-3.5 py-3.5 text-center text-slate-400 text-xs">-</td>
                                    <td colspan="4" class="px-3.5 py-3.5 text-slate-400 text-xs italic">
                                        Tidak ada tambahan penerimaan kas baru pada periode ini (Rp 0).
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tampilan Mobile: List Item Finansial Bersih & Jelas -->
                <div class="md:hidden divide-y divide-slate-100 bg-white">
                    <!-- Baris Saldo Periode Sebelumnya -->
                    <div class="p-3.5 sm:p-4 bg-emerald-50/40 flex items-center justify-between gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-1.5 flex-wrap">
                                <span class="text-xs font-bold text-slate-900 leading-snug">
                                    {{ $labelSaldoAwal ?? (($filterTipe ?? 'bulan') === 'tahun' ? 'Saldo Kas Periode Sebelumnya (Tahun Lalu)' : 'Saldo Kas Periode Sebelumnya (Bulan Lalu)') }}
                                </span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 shrink-0">Saldo Awal</span>
                            </div>
                            <p class="text-xs text-slate-500 mt-1">
                                Porsi Kas: <span class="font-mono font-semibold">{{ $persentaseSaldoAwal ?? (($totalPendapatan > 0 && ($saldoAwal ?? 0) > 0) ? round((($saldoAwal ?? 0) / $totalPendapatan) * 100, 1) : 0) }}%</span>
                            </p>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="font-mono font-bold text-xs sm:text-sm text-emerald-700">
                                Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>

                    <!-- Baris Rincian Penerimaan Periode Ini -->
                    @forelse($pendapatans as $idx => $item)
                        <div class="p-3.5 sm:p-4 hover:bg-slate-50/80 transition flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="text-xs sm:text-sm font-bold text-slate-900 leading-snug truncate">{{ $item->kategori }}</div>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $item->jumlah_transaksi }}x transaksi &bull; Porsi <span class="font-mono font-semibold">{{ $item->persentase }}%</span>
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-mono font-bold text-xs sm:text-sm text-emerald-700">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-slate-400 text-xs italic">
                            Tidak ada tambahan penerimaan kas baru pada periode ini (Rp 0).
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Subtotal Pendapatan (Penerimaan + Saldo Periode Lalu) -->
            <div class="p-3.5 sm:p-4 bg-emerald-50 border-t border-emerald-200 flex items-center justify-between">
                <span class="text-xs font-black text-emerald-950 uppercase tracking-wide">Total Pendapatan (A):</span>
                <span class="font-mono font-black text-sm sm:text-base text-emerald-800 whitespace-nowrap">
                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- KOLOM KANAN: RINCIAN BEBAN (PENGELUARAN) -->
        <div class="bg-white rounded-2xl border border-rose-200/80 shadow-xs overflow-hidden flex flex-col justify-between h-full">
            <div>
                <div class="p-4 sm:p-4.5 bg-rose-50/70 border-b border-rose-100 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="p-1.5 rounded-lg bg-rose-100 text-rose-700 shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-xs sm:text-sm font-bold text-rose-900 truncate">II. Beban Kas (Pengeluaran)</h2>
                            <p class="text-xs text-rose-700 truncate mt-0.5">Rincian per pos biaya operasional</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-rose-100 text-rose-800 border border-rose-200 shrink-0">
                        {{ $bebans->count() }} Akun
                    </span>
                </div>

                <!-- Tampilan Desktop: Tabel Lengkap -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm divide-y divide-slate-100">
                        <thead class="bg-rose-50/50 text-xs font-bold uppercase tracking-wider text-rose-900 border-b border-rose-100">
                            <tr>
                                <th class="px-3.5 py-3 text-center w-10">No</th>
                                <th class="px-3.5 py-3">Pos Akun / Kategori</th>
                                <th class="px-3 py-3 text-center w-20">Volume</th>
                                <th class="px-3.5 py-3 text-right">Nominal</th>
                                <th class="px-3.5 py-3 text-right w-16">Porsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($bebans as $idx => $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-3.5 py-3.5 text-center text-slate-400 text-xs">{{ $idx + 1 }}</td>
                                    <td class="px-3.5 py-3.5 font-medium text-slate-800 leading-snug">{{ $item->kategori }}</td>
                                    <td class="px-3 py-3.5 text-center text-xs text-slate-500 whitespace-nowrap">{{ $item->jumlah_transaksi }}x</td>
                                    <td class="px-3.5 py-3.5 text-right font-mono font-bold text-xs sm:text-sm text-rose-700 whitespace-nowrap">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3.5 py-3.5 text-right font-mono text-xs text-slate-500 whitespace-nowrap">
                                        {{ $item->persentase }}%
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-3.5 py-4 text-center text-slate-400 text-xs italic">
                                        Tidak ada mutasi pengeluaran kas pada periode ini.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Tampilan Mobile: List Item Finansial Bersih & Jelas -->
                <div class="md:hidden divide-y divide-slate-100 bg-white">
                    @forelse($bebans as $idx => $item)
                        <div class="p-3.5 sm:p-4 hover:bg-slate-50/80 transition flex items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="text-xs sm:text-sm font-bold text-slate-900 leading-snug truncate">{{ $item->kategori }}</div>
                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $item->jumlah_transaksi }}x transaksi &bull; Porsi <span class="font-mono font-semibold">{{ $item->persentase }}%</span>
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="font-mono font-bold text-xs sm:text-sm text-rose-700">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 text-center text-slate-400 text-xs italic">
                            Tidak ada mutasi pengeluaran kas pada periode ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Subtotal Beban -->
            <div class="p-3.5 sm:p-4 bg-rose-50 border-t border-rose-200 flex items-center justify-between">
                <span class="text-xs font-black text-rose-900 uppercase tracking-wide">Total Beban (B):</span>
                <span class="font-mono font-black text-sm sm:text-base text-rose-800 whitespace-nowrap">
                    Rp {{ number_format($totalBeban, 0, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- KESIMPULAN REKAPITULASI HASIL BERSIH (A - B = SURPLUS / DEFISIT)           -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-5 rounded-xl {{ $isSurplus ? 'bg-emerald-50 border border-emerald-200' : 'bg-rose-50 border border-rose-200' }}">
            <div class="min-w-0 flex-1">
                <span class="text-xs font-black uppercase tracking-wider {{ $isSurplus ? 'text-emerald-800' : 'text-rose-800' }} block">
                    Hasil Akhir Pembukuan (A - B):
                </span>
                <h3 class="text-base sm:text-lg font-black {{ $isSurplus ? 'text-emerald-950' : 'text-rose-950' }} mt-0.5">
                    {{ $isSurplus ? 'SURPLUS KAS OPERASIONAL' : 'DEFISIT KAS OPERASIONAL' }}
                </h3>
                <p class="text-xs sm:text-sm {{ $isSurplus ? 'text-emerald-700' : 'text-rose-700' }} mt-1.5 leading-relaxed">
                    Pendapatan Kas (Rp {{ number_format($totalPendapatanPeriode ?? 0, 0, ',', '.') }}) + Saldo Periode Lalu (Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }}) dikurangi Beban Kas (Rp {{ number_format($totalBeban, 0, ',', '.') }}) menghasilkan <strong>{{ $isSurplus ? 'Surplus Kas' : 'Defisit Kas' }} sebesar Rp {{ number_format(abs($surplusDefisit), 0, ',', '.') }}</strong> (Sinkron Buku Kas &amp; Neraca).
                </p>
            </div>

            <div class="text-left sm:text-right shrink-0 pt-3 sm:pt-0 border-t sm:border-t-0 {{ $isSurplus ? 'border-emerald-200' : 'border-rose-200' }}">
                <span class="text-xs uppercase font-bold text-slate-500 block sm:hidden">Saldo Akhir:</span>
                <div class="text-xl sm:text-2xl font-black font-mono tracking-tight {{ $isSurplus ? 'text-emerald-800' : 'text-rose-800' }}">
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
