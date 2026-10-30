@extends('layouts.app')

@section('title', 'Bendahara - Buku Kas Umum (Debit & Kredit)')

@section('content')
<div x-data="{
    modalDebitOpen: false,
    modalKreditOpen: false,
    modalEditOpen: false,
    modalPreviewOpen: false,
    nominalDebit: '',
    nominalKredit: '',
    previewUrl: '',
    previewTitle: '',
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
    openEdit(item) {
        this.editData = {
            id: item.id,
            kode_transaksi: item.kode_transaksi,
            tanggal: item.tanggal,
            jenis: item.jenis,
            kategori: item.kategori,
            nominal: item.nominal,
            keterangan: item.keterangan,
            nama_pihak: item.nama_pihak || '',
            lampiran: item.lampiran || ''
        };
        this.modalEditOpen = true;
    },
    openPreview(url, title) {
        this.previewUrl = url;
        this.previewTitle = title;
        this.modalPreviewOpen = true;
    }
}" class="space-y-4 sm:space-y-6">

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
                <p class="text-xs sm:text-sm text-slate-500 font-medium truncate">Mutasi Kas Masuk (Debit) & Kas Keluar (Kredit)</p>
            </div>
        </div>

        <!-- Tombol Aksi Kas (Grid 2 Kolom di Mobile, Sebaris di Laptop) -->
        <div class="grid grid-cols-2 sm:flex sm:items-center gap-2 pt-1 sm:pt-0">
            <button @click="modalDebitOpen = true"
                    type="button"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 sm:px-4 sm:py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-xs transition transform active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                <span>Catat Debit</span>
            </button>

            <button @click="modalKreditOpen = true"
                    type="button"
                    class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 sm:px-4 sm:py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs sm:text-sm shadow-xs transition transform active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                <span>Catat Kredit</span>
            </button>

            <a href="{{ route('admin.bendahara.word', request()->query()) }}"
               class="col-span-2 sm:col-span-1 inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 sm:px-4 sm:py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs sm:text-sm shadow-xs transition transform active:scale-95"
               title="Unduh format dokumen Microsoft Word (.docx) resmi">
                <svg class="w-4 h-4 text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <span>Unduh Word (.docx)</span>
            </a>
        </div>
    </div>

    <!-- 4 KARTU RINGKASAN AKUNTANSI (WARNA VIBRAN & RESPONSIF) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
        <!-- 1. Saldo Kas Riil (Lebar Penuh di HP agar Sangat Menonjol) -->
        <div class="col-span-2 sm:col-span-1 lg:col-span-1 bg-gradient-to-br from-emerald-800 via-emerald-900 to-teal-950 text-white p-4 sm:p-5 rounded-2xl shadow-sm border border-emerald-700/80 flex flex-col justify-between relative overflow-hidden">
            <div class="absolute -right-8 -bottom-8 w-28 h-28 bg-emerald-500/10 rounded-full blur-xl pointer-events-none"></div>
            <div class="relative z-10 flex items-center justify-between">
                <span class="text-[11px] sm:text-xs font-bold uppercase tracking-wider text-emerald-200">Saldo Kas Riil</span>
                <span class="p-1.5 sm:p-2 rounded-xl bg-emerald-700/80 text-emerald-200 border border-emerald-600/50 shadow-xs">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </span>
            </div>
            <div class="relative z-10 mt-2.5 sm:mt-3">
                <div class="text-xl sm:text-2xl font-black tracking-tight truncate {{ $saldoKasKeseluruhan < 0 ? 'text-rose-300' : 'text-white' }}">
                    Rp {{ number_format($saldoKasKeseluruhan, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-emerald-200/80 mt-0.5 font-medium">Sisa saldo kas berjalan</p>
            </div>
        </div>

        <!-- 2. Total Penerimaan (Debit) - Hijau Segar -->
        <div class="col-span-1 bg-gradient-to-br from-emerald-50 via-teal-50/60 to-emerald-100/40 p-3.5 sm:p-5 rounded-2xl shadow-xs border-2 border-emerald-200/90 hover:border-emerald-300 transition flex flex-col justify-between">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-emerald-900 truncate">Penerimaan (Debit)</span>
                <span class="p-1.5 sm:p-2 rounded-xl bg-emerald-600 text-white shadow-xs shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl lg:text-2xl font-black text-emerald-700 tracking-tight truncate">
                    Rp {{ number_format($totalDebitFiltered, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-emerald-800/80 mt-0.5 font-semibold truncate">Kas masuk periode ini</p>
            </div>
        </div>

        <!-- 3. Total Pengeluaran (Kredit) - Merah Mawar Segar -->
        <div class="col-span-1 bg-gradient-to-br from-rose-50 via-red-50/60 to-rose-100/40 p-3.5 sm:p-5 rounded-2xl shadow-xs border-2 border-rose-200/90 hover:border-rose-300 transition flex flex-col justify-between">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-wider text-rose-900 truncate">Pengeluaran (Kredit)</span>
                <span class="p-1.5 sm:p-2 rounded-xl bg-rose-600 text-white shadow-xs shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl lg:text-2xl font-black text-rose-700 tracking-tight truncate">
                    Rp {{ number_format($totalKreditFiltered, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] text-rose-800/80 mt-0.5 font-semibold truncate">Kas keluar periode ini</p>
            </div>
        </div>

        <!-- 4. Surplus / Defisit - Biru / Amber -->
        <div class="col-span-2 sm:col-span-1 lg:col-span-1 {{ $surplusDefisitFiltered >= 0 ? 'bg-gradient-to-br from-blue-50 via-indigo-50/60 to-sky-100/40 border-blue-200/90' : 'bg-gradient-to-br from-amber-50 via-orange-50/60 to-amber-100/40 border-amber-200/90' }} p-3.5 sm:p-5 rounded-2xl shadow-xs border-2 hover:shadow-sm transition flex flex-col justify-between">
            <div class="flex items-center justify-between gap-1">
                <span class="text-[11px] sm:text-xs font-extrabold uppercase tracking-wider {{ $surplusDefisitFiltered >= 0 ? 'text-blue-900' : 'text-amber-900' }}">Surplus / (Defisit)</span>
                <span class="p-1.5 sm:p-2 rounded-xl {{ $surplusDefisitFiltered >= 0 ? 'bg-blue-600 text-white' : 'bg-amber-600 text-white' }} shadow-xs shrink-0">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
            </div>
            <div class="mt-2 sm:mt-3">
                <div class="text-base sm:text-xl lg:text-2xl font-black tracking-tight truncate {{ $surplusDefisitFiltered >= 0 ? 'text-blue-700' : 'text-amber-700' }}">
                    {{ $surplusDefisitFiltered >= 0 ? '+' : '' }}Rp {{ number_format($surplusDefisitFiltered, 0, ',', '.') }}
                </div>
                <p class="text-[10px] sm:text-[11px] {{ $surplusDefisitFiltered >= 0 ? 'text-blue-800/80' : 'text-amber-800/80' }} mt-0.5 font-semibold">{{ $totalCountFiltered }} transaksi periode ini</p>
            </div>
        </div>
    </div>

    <!-- FILTER & PENCARIAN MUTASI -->
    <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200 shadow-xs">
        <form method="GET" action="{{ route('admin.bendahara.index') }}" class="space-y-3">
            <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-emerald-100 text-emerald-800">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </span>
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Filter & Pencarian Jurnal</span>
                </div>
                @if($jenis || $kategori || $q || $periode !== 'bulan-ini')
                <a href="{{ route('admin.bendahara.index') }}" class="text-xs text-rose-600 font-bold hover:underline flex items-center gap-1">
                    <span>Reset Filter</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
                @endif
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-12 gap-2.5 sm:gap-3">
                @php
                    $daftarBulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $bulanIniLabel = ($daftarBulan[(int) date('n')] ?? date('F')) . ' ' . date('Y');
                @endphp

                <!-- Filter Periode Cepat -->
                <div class="col-span-1 lg:col-span-3">
                    <label class="flex items-center gap-1 text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        <span>Periode</span>
                    </label>
                    <select name="periode"
                            onchange="this.form.submit()"
                            class="w-full text-xs sm:text-sm rounded-xl border-slate-300 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="bulan-ini" {{ $periode === 'bulan-ini' ? 'selected' : '' }}>Bulan Ini ({{ $bulanIniLabel }})</option>
                        <option value="tahun-ini" {{ $periode === 'tahun-ini' ? 'selected' : '' }}>Tahun Ini ({{ date('Y') }})</option>
                        <option value="semua" {{ $periode === 'semua' ? 'selected' : '' }}>Semua Transaksi</option>
                        <option value="custom" {{ $periode === 'custom' ? 'selected' : '' }}>Rentang Tanggal Kustom...</option>
                    </select>
                </div>

                <!-- Filter Jenis (Debit / Kredit) -->
                <div class="col-span-1 lg:col-span-2">
                    <label class="flex items-center gap-1 text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        <span>Jenis Akun</span>
                    </label>
                    <select name="jenis"
                            onchange="this.form.submit()"
                            class="w-full text-xs sm:text-sm rounded-xl border-slate-300 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Jenis</option>
                        <option value="debit" {{ $jenis === 'debit' ? 'selected' : '' }}>Debit (Penerimaan)</option>
                        <option value="kredit" {{ $jenis === 'kredit' ? 'selected' : '' }}>Kredit (Pengeluaran)</option>
                    </select>
                </div>

                <!-- Filter Kategori Pos Akun -->
                <div class="col-span-2 sm:col-span-1 lg:col-span-3">
                    <label class="flex items-center gap-1 text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        <span>Pos Akun / Kategori</span>
                    </label>
                    <select name="kategori"
                            onchange="this.form.submit()"
                            class="w-full text-xs sm:text-sm rounded-xl border-slate-300 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                        <option value="">Semua Pos Akun</option>
                        @foreach($kategoriList as $kat)
                            <option value="{{ $kat }}" {{ $kategori === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Pencarian Teks -->
                <div class="col-span-2 sm:col-span-1 lg:col-span-4">
                    <label class="flex items-center gap-1 text-[11px] sm:text-xs font-bold text-slate-700 uppercase mb-1">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                        <span>Pencarian Kata Kunci</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                               name="q"
                               value="{{ $q }}"
                               placeholder="Cari uraian, no. bukti, pihak..."
                               class="w-full text-xs sm:text-sm rounded-xl border-slate-300 pl-9 pr-4 bg-slate-50/50 hover:bg-white focus:bg-white focus:border-emerald-500 focus:ring-emerald-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Rentang Tanggal Kustom (Jika dipilih) -->
            @if($periode === 'custom')
            <div class="pt-2 flex flex-wrap items-center gap-2.5 border-t border-slate-100">
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-semibold">Dari:</span>
                    <input type="date" name="dari_tanggal" value="{{ $dari }}" class="text-xs rounded-xl border-slate-300">
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-slate-500 font-semibold">Sampai:</span>
                    <input type="date" name="sampai_tanggal" value="{{ $sampai }}" class="text-xs rounded-xl border-slate-300">
                </div>
                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-700">Terapkan</button>
            </div>
            @endif
        </form>
    </div>

    <!-- KONTEN MUTASI: DUA TAMPILAN (CARD FEED DI MOBILE, TABEL LENGKAP DI DESKTOP) -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-4 py-3.5 sm:px-5 sm:py-4 border-b border-slate-200 flex items-center justify-between gap-2 bg-gradient-to-r from-slate-50 to-emerald-50/40">
            <div>
                <h2 class="text-sm sm:text-base font-bold text-slate-900">Jurnal Mutasi Kas</h2>
                <p class="text-[11px] sm:text-xs text-slate-500">Urut dari transaksi terbaru</p>
            </div>
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] sm:text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                {{ $transaksis->total() }} Transaksi
            </span>
        </div>

        <!-- 1. TAMPILAN MOBILE: KARTU MUTASI ELEGAN (Khusus Layar HP < 768px) -->
        <div class="block md:hidden divide-y divide-slate-100">
            @forelse($transaksis as $index => $item)
            <div class="p-3.5 space-y-2.5 hover:bg-slate-50 transition-colors {{ $item->jenis === 'debit' ? 'border-l-4 border-l-emerald-500 bg-emerald-50/15' : 'border-l-4 border-l-rose-500 bg-rose-50/15' }}">
                <!-- Baris Atas: Kategori & Nominal -->
                <div class="flex items-center justify-between gap-2">
                    <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $item->badge_class }}">
                        {{ $item->kategori }}
                    </span>
                    <span class="text-sm font-mono font-black {{ $item->jenis === 'debit' ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $item->jenis === 'debit' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
                    </span>
                </div>

                <!-- Baris Tengah: Uraian Keterangan & Pihak Terkait -->
                <div>
                    <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $item->keterangan }}</p>
                    @if($item->nama_pihak)
                        <p class="text-[11px] text-slate-500 mt-1">
                            <span class="text-slate-400 font-medium">{{ $item->jenis === 'debit' ? 'Dari:' : 'Kepada:' }}</span>
                            <span class="font-semibold text-slate-700">{{ $item->nama_pihak }}</span>
                        </p>
                    @endif
                </div>

                <!-- Baris Bawah: No Bukti, Tanggal & Tombol Aksi -->
                <div class="pt-2 border-t border-slate-100/80 flex items-center justify-between text-[11px] text-slate-500">
                    <div class="flex items-center gap-1.5 font-mono text-[10px] text-slate-600">
                        <span class="font-bold">{{ $item->kode_transaksi }}</span>
                        <span>•</span>
                        <span>{{ $item->formatted_tanggal }}</span>
                    </div>

                    <div class="flex items-center gap-1">
                        @if($item->lampiran)
                            @php
                                $isPdf = str_ends_with(strtolower($item->lampiran), '.pdf');
                                $lampiranUrl = asset('storage/' . $item->lampiran);
                            @endphp
                            @if($isPdf)
                                <a href="{{ $lampiranUrl }}" target="_blank" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50" title="Lampiran PDF">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </a>
                            @else
                                <button @click="openPreview({{ \Illuminate\Support\Js::from($lampiranUrl) }}, {{ \Illuminate\Support\Js::from('Bukti ' . $item->kode_transaksi) }})"
                                        type="button"
                                        class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50"
                                        title="Foto Bukti">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </button>
                            @endif
                        @endif

                        <button @click="openEdit({{ \Illuminate\Support\Js::from([
                            'id' => $item->id,
                            'kode_transaksi' => $item->kode_transaksi,
                            'tanggal' => $item->tanggal->format('Y-m-d'),
                            'jenis' => $item->jenis,
                            'kategori' => $item->kategori,
                            'nominal' => (int) $item->nominal,
                            'keterangan' => $item->keterangan,
                            'nama_pihak' => $item->nama_pihak,
                            'lampiran' => $item->lampiran,
                        ]) }})"
                                type="button"
                                class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50"
                                title="Ubah Transaksi">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>

                        <form method="POST"
                              action="{{ route('admin.bendahara.destroy', $item->id) }}"
                              onsubmit="return confirm('Hapus transaksi {{ $item->kode_transaksi }} sebesar Rp {{ number_format($item->nominal, 0, ',', '.') }}?');"
                              class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-slate-500 space-y-3">
                <span class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl inline-block">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                </span>
                <p class="text-xs font-bold text-slate-700">Belum Ada Transaksi Kas</p>
                <div class="flex justify-center gap-2">
                    <button @click="modalDebitOpen = true" type="button" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs">+ Debit</button>
                    <button @click="modalKreditOpen = true" type="button" class="px-3 py-1.5 rounded-xl bg-rose-600 text-white font-semibold text-xs">- Kredit</button>
                </div>
            </div>
            @endforelse
        </div>

        <!-- 2. TAMPILAN DESKTOP: TABEL AKUNTANSI LENGKAP (Khusus Layar Desktop/Laptop >= 768px) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm divide-y divide-slate-200">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wider text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5">No. Bukti & Tanggal</th>
                        <th class="px-4 py-3.5">Pos Akun / Kategori</th>
                        <th class="px-4 py-3.5">Uraian / Keterangan</th>
                        <th class="px-4 py-3.5">Pihak Terkait</th>
                        <th class="px-4 py-3.5 text-right text-emerald-700">Debit (Masuk)</th>
                        <th class="px-4 py-3.5 text-right text-rose-700">Kredit (Keluar)</th>
                        <th class="px-4 py-3.5 text-center">Bukti</th>
                        <th class="px-4 py-3.5 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    @forelse($transaksis as $index => $item)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-4 py-3.5 text-center font-medium text-slate-400">
                            {{ $transaksis->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="font-mono text-xs font-bold text-slate-800 block">{{ $item->kode_transaksi }}</span>
                            <span class="text-[11px] text-slate-500 font-medium">{{ $item->formatted_tanggal }}</span>
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap">
                            <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border {{ $item->badge_class }}">
                                {{ $item->kategori }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5">
                            <p class="font-medium text-slate-800 line-clamp-2">{{ $item->keterangan }}</p>
                            @if($item->user)
                                <span class="text-[10px] text-slate-400 block mt-0.5">Dicatat: {{ $item->user->name }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 whitespace-nowrap text-slate-600 font-medium">
                            {{ $item->nama_pihak ?: '-' }}
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap font-bold font-mono text-emerald-600">
                            @if($item->jenis === 'debit')
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-right whitespace-nowrap font-bold font-mono text-rose-600">
                            @if($item->jenis === 'kredit')
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            @else
                                <span class="text-slate-300">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            @if($item->lampiran)
                                @php
                                    $isPdf = str_ends_with(strtolower($item->lampiran), '.pdf');
                                    $lampiranUrl = asset('storage/' . $item->lampiran);
                                @endphp
                                @if($isPdf)
                                    <a href="{{ $lampiranUrl }}" target="_blank" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 inline-block" title="Lihat Lampiran PDF">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                    </a>
                                @else
                                    <button @click="openPreview({{ \Illuminate\Support\Js::from($lampiranUrl) }}, {{ \Illuminate\Support\Js::from('Bukti Nota ' . $item->kode_transaksi) }})"
                                            type="button"
                                            class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50"
                                            title="Pratinjau Foto Nota">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    </button>
                                @endif
                            @else
                                <span class="text-slate-300 text-xs">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3.5 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <button @click="openEdit({{ \Illuminate\Support\Js::from([
                                    'id' => $item->id,
                                    'kode_transaksi' => $item->kode_transaksi,
                                    'tanggal' => $item->tanggal->format('Y-m-d'),
                                    'jenis' => $item->jenis,
                                    'kategori' => $item->kategori,
                                    'nominal' => (int) $item->nominal,
                                    'keterangan' => $item->keterangan,
                                    'nama_pihak' => $item->nama_pihak,
                                    'lampiran' => $item->lampiran,
                                ]) }})"
                                        type="button"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition"
                                        title="Ubah Transaksi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.bendahara.destroy', $item->id) }}"
                                      onsubmit="return confirm('Hapus transaksi {{ $item->kode_transaksi }} sebesar Rp {{ number_format($item->nominal, 0, ',', '.') }}?');"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition"
                                            title="Hapus Transaksi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-6 py-12 text-center text-slate-500">
                            <div class="max-w-sm mx-auto space-y-3">
                                <span class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl inline-block">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </span>
                                <h3 class="text-base font-bold text-slate-800">Belum Ada Transaksi Kas</h3>
                                <p class="text-xs text-slate-500">Mulai catat transaksi uang masuk (debit) atau uang keluar (kredit) untuk membukukan kas pondok.</p>
                                <div class="pt-2 flex justify-center gap-2">
                                    <button @click="modalDebitOpen = true" type="button" class="px-3.5 py-2 rounded-xl bg-emerald-600 text-white font-semibold text-xs shadow-xs hover:bg-emerald-700">+ Catat Debit</button>
                                    <button @click="modalKreditOpen = true" type="button" class="px-3.5 py-2 rounded-xl bg-rose-600 text-white font-semibold text-xs shadow-xs hover:bg-rose-700">- Catat Kredit</button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
        <div class="px-4 py-3.5 sm:px-5 sm:py-4 border-t border-slate-200">
            {{ $transaksis->links() }}
        </div>
        @endif
    </div>

    <!-- MODAL 1: CATAT DEBIT (PEMASUKAN KAS) -->
    <div x-show="modalDebitOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         @click.self="modalDebitOpen = false">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 sm:p-6 max-h-[90vh] flex flex-col my-auto"
             @click.stop>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-emerald-100 text-emerald-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    </span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">Catat Debit (Penerimaan Kas)</h3>
                        <p class="text-[11px] sm:text-xs text-slate-500">Mencatat uang masuk ke kas pesantren</p>
                    </div>
                </div>
                <button @click="modalDebitOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.bendahara.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3.5 overflow-y-auto pr-1">
                @csrf
                <input type="hidden" name="jenis" value="debit">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400 select-none">Rp</span>
                            <input type="number"
                                   name="nominal"
                                   x-model="nominalDebit"
                                   placeholder="0"
                                   min="1"
                                   step="any"
                                   required
                                   class="w-full text-xs sm:text-sm rounded-xl border-slate-300 pl-10 pr-4 font-mono font-bold text-emerald-700 focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <p x-show="nominalDebit > 0" class="text-xs text-emerald-700 font-bold mt-1 font-mono" x-text="'Format: Rp ' + Number(nominalDebit || 0).toLocaleString('id-ID')"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pos Akun / Kategori <span class="text-rose-500">*</span></label>
                    <input type="text"
                           name="kategori"
                           list="listKategoriDebit"
                           placeholder="Pilih atau ketik kategori penerimaan..."
                           required
                           class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <datalist id="listKategoriDebit">
                        @foreach($kategoriDebit as $kat)
                            <option value="{{ $kat }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Diterima Dari (Donatur / Pihak Pembayar)</label>
                    <input type="text" name="nama_pihak" placeholder="Contoh: Hamba Allah / Bpk. H. Ahmad / Wali Santri" class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Uraian / Keterangan Transaksi <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                    <textarea name="keterangan" rows="3" placeholder="(Opsional) Tuliskan keterangan detail penerimaan kas..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bukti Kwitansi / Nota (Opsional)</label>
                    <input type="file" name="lampiran" accept="image/jpeg,image/png,application/pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <span class="text-[10px] text-slate-400 block mt-1">Maksimal 5 MB (Format: JPG, PNG, atau PDF)</span>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 shrink-0">
                    <button @click="modalDebitOpen = false" type="button" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-xs hover:bg-emerald-700">Simpan Debit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: CATAT KREDIT (PENGELUARAN KAS) -->
    <div x-show="modalKreditOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         @click.self="modalKreditOpen = false">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 sm:p-6 max-h-[90vh] flex flex-col my-auto"
             @click.stop>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
                <div class="flex items-center gap-2.5">
                    <span class="p-2 rounded-xl bg-rose-100 text-rose-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                    </span>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900">Catat Kredit (Pengeluaran Kas)</h3>
                        <p class="text-[11px] sm:text-xs text-slate-500">Mencatat uang keluar dari kas pesantren</p>
                    </div>
                </div>
                <button @click="modalKreditOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form method="POST" action="{{ route('admin.bendahara.store') }}" enctype="multipart/form-data" class="mt-4 space-y-3.5 overflow-y-auto pr-1">
                @csrf
                <input type="hidden" name="jenis" value="kredit">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400 select-none">Rp</span>
                            <input type="number"
                                   name="nominal"
                                   x-model="nominalKredit"
                                   placeholder="0"
                                   min="1"
                                   step="any"
                                   required
                                   class="w-full text-xs sm:text-sm rounded-xl border-slate-300 pl-10 pr-4 font-mono font-bold text-rose-700 focus:border-rose-500 focus:ring-rose-500">
                        </div>
                        <p x-show="nominalKredit > 0" class="text-xs text-rose-700 font-bold mt-1 font-mono" x-text="'Format: Rp ' + Number(nominalKredit || 0).toLocaleString('id-ID')"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pos Akun / Kategori <span class="text-rose-500">*</span></label>
                    <input type="text"
                           name="kategori"
                           list="listKategoriKredit"
                           placeholder="Pilih atau ketik kategori pengeluaran..."
                           required
                           class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500">
                    <datalist id="listKategoriKredit">
                        @foreach($kategoriKredit as $kat)
                            <option value="{{ $kat }}"></option>
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Dibayarkan Kepada (Penerima Kas)</label>
                    <input type="text" name="nama_pihak" placeholder="Contoh: Toko ATK Berkah / Toko Bangunan / PLN" class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Uraian / Keterangan Transaksi <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                    <textarea name="keterangan" rows="3" placeholder="(Opsional) Tuliskan rincian barang/kebutuhan yang dibelanjakan..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300 focus:border-rose-500 focus:ring-rose-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Bukti Nota / Struk Pembelian (Opsional)</label>
                    <input type="file" name="lampiran" accept="image/jpeg,image/png,application/pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                    <span class="text-[10px] text-slate-400 block mt-1">Maksimal 5 MB (Format: JPG, PNG, atau PDF)</span>
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 shrink-0">
                    <button @click="modalKreditOpen = false" type="button" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 text-white font-bold text-xs shadow-xs hover:bg-rose-700">Simpan Kredit</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: UBAH TRANSAKSI KAS -->
    <div x-show="modalEditOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         @click.self="modalEditOpen = false">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 sm:p-6 max-h-[90vh] flex flex-col my-auto"
             @click.stop>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100 shrink-0">
                <div>
                    <h3 class="text-sm sm:text-base font-bold text-slate-900">Ubah Transaksi Kas</h3>
                    <p class="text-[11px] sm:text-xs text-slate-500">Edit <span class="font-mono font-bold text-slate-700" x-text="editData.kode_transaksi"></span></p>
                </div>
                <button @click="modalEditOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form :action="'{{ url('admin/bendahara') }}/' + editData.id" method="POST" enctype="multipart/form-data" class="mt-4 space-y-3.5 overflow-y-auto pr-1">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Jenis Transaksi <span class="text-rose-500">*</span></label>
                        <select name="jenis" x-model="editData.jenis" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                            <option value="debit">Debit (Penerimaan)</option>
                            <option value="kredit">Kredit (Pengeluaran)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal" x-model="editData.tanggal" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pos Akun / Kategori <span class="text-rose-500">*</span></label>
                        <input type="text" name="kategori" x-model="editData.kategori" required class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400 select-none">Rp</span>
                            <input type="number"
                                   name="nominal"
                                   x-model="editData.nominal"
                                   min="1"
                                   step="any"
                                   required
                                   class="w-full text-xs sm:text-sm rounded-xl border-slate-300 pl-10 pr-4 font-mono font-bold focus:border-emerald-500 focus:ring-emerald-500">
                        </div>
                        <p x-show="editData.nominal > 0" class="text-xs text-slate-700 font-bold mt-1 font-mono" x-text="'Format: Rp ' + Number(editData.nominal || 0).toLocaleString('id-ID')"></p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Pihak Terkait (Diterima dari / Kepada)</label>
                    <input type="text" name="nama_pihak" x-model="editData.nama_pihak" class="w-full text-xs sm:text-sm rounded-xl border-slate-300">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Uraian Keterangan <span class="text-xs text-slate-400 font-normal">(Opsional)</span></label>
                    <textarea name="keterangan" x-model="editData.keterangan" rows="3" placeholder="(Opsional) Keterangan transaksi..." class="w-full text-xs sm:text-sm rounded-xl border-slate-300"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Ganti Bukti Nota (Opsional)</label>
                    <input type="file" name="lampiran" accept="image/jpeg,image/png,application/pdf" class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                </div>

                <div class="pt-3 flex justify-end gap-2 border-t border-slate-100 shrink-0">
                    <button @click="modalEditOpen = false" type="button" class="px-4 py-2 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-xs hover:bg-emerald-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 4: PRATINJAU FOTO BUKTI NOTA -->
    <div x-show="modalPreviewOpen"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/80 backdrop-blur-xs flex items-center justify-center p-3 sm:p-4"
         @click.self="modalPreviewOpen = false">
        <div class="relative w-full max-w-2xl bg-white rounded-2xl shadow-2xl border border-slate-200 p-4 sm:p-5 my-auto"
             @click.stop>
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-xs sm:text-sm font-bold text-slate-900 truncate" x-text="previewTitle"></h3>
                <div class="flex items-center gap-2 shrink-0">
                    <a :href="previewUrl" target="_blank" class="text-xs text-emerald-600 font-bold hover:underline">Buka Asli</a>
                    <button @click="modalPreviewOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="mt-3 max-h-[70vh] overflow-auto flex items-center justify-center bg-slate-50 rounded-xl p-2 border border-slate-100">
                <img :src="previewUrl" alt="Foto Bukti Nota" class="max-w-full h-auto object-contain rounded-lg">
            </div>
        </div>
    </div>

</div>
@endsection
