<x-app-layout>
    @section('title', 'Surat Izin UKS - SAP Al-Falah Putra')

    <div class="space-y-6">
        <!-- Header & Navigasi Aksi -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center shadow-xs">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m-7 4h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </span>
                    <span>Surat Izin UKS (Pulang & Sakit)</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Manajemen blangko resmi, nomor urut terpadu, penjualan Rp 5.000/lembar, dan integrasi kas otomatis ke Bendahara.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <button type="button"
                        onclick="openModalGenerate()"
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    <span>+ Cetak / Buat Blangko</span>
                </button>
                <button type="button"
                        onclick="openModalCatatKode()"
                        class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-amber-600 hover:bg-amber-700 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                    <span>Catat Terjual (Kode 4-Digit)</span>
                </button>
                <a href="{{ route('admin.surat-uks.cetak-batch', ['status' => 'tersedia']) }}"
                   target="_blank"
                   class="inline-flex items-center gap-2 px-3.5 py-2.5 bg-slate-800 hover:bg-slate-900 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                    <span>Cetak Batch PDF Stok</span>
                </a>
                <form action="{{ route('admin.surat-uks.sync-kas') }}" method="POST" class="inline" onsubmit="return confirm('Sinkronkan seluruh akumulasi kas bulanan UKS ke Buku Kas Bendahara?')">
                    @csrf
                    <button type="submit"
                            title="Sinkronkan akumulasi kas bulanan ke Bendahara"
                            class="inline-flex items-center gap-1.5 px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs sm:text-sm font-bold rounded-xl border border-slate-300 transition active:scale-95">
                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        <span>Sinkron Kas Bulanan</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- 3 KARTU LAPORAN UTAMA (Sesuai Permintaan) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- 1. Laporan Surat Tercetak -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">1. Surat Tercetak</span>
                    <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-xs">
                        📄
                    </span>
                </div>
                <div class="mt-3">
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-slate-900 tabular-nums">{{ number_format($totalTercetak) }}</h3>
                        <span class="text-xs text-slate-500 font-medium">lembar total</span>
                    </div>
                    <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-600">Surat Izin Pulang: <strong class="text-emerald-700">{{ number_format($totalCetakPulang) }}</strong></span>
                        <span class="text-slate-600">Surat Izin Sakit: <strong class="text-blue-700">{{ number_format($totalCetakSakit) }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- 2. Laporan Kas Masuk (Otomatis Masuk Bendahara) -->
            <div class="bg-white rounded-2xl p-5 border border-emerald-300 shadow-xs flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-white to-emerald-50/30">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-emerald-800 uppercase tracking-wider">2. Kas Masuk (Rp 5.000 / lbr)</span>
                        <span class="block text-[10px] text-emerald-600 font-medium">Sinkron Otomatis ke Buku Kas Bendahara</span>
                    </div>
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-xs">
                        💰
                    </span>
                </div>
                <div class="mt-3">
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-emerald-700 tabular-nums">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</h3>
                        <span class="text-xs text-emerald-600 font-semibold">({{ number_format($jumlahTerjual) }} lbr terjual)</span>
                    </div>
                    <div class="mt-2.5 pt-2.5 border-t border-emerald-100 flex items-center justify-between text-[11px]">
                        <span class="text-slate-600">Pendapatan SIP: <strong class="text-emerald-700">Rp {{ number_format($kasMasukPulang, 0, ',', '.') }}</strong></span>
                        <span class="text-slate-600">Pendapatan SIS: <strong class="text-blue-700">Rp {{ number_format($kasMasukSakit, 0, ',', '.') }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- 3. Laporan Sisa Stok Blangko -->
            <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex flex-col justify-between relative overflow-hidden">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">3. Sisa Stok Blangko</span>
                    <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center font-bold text-xs">
                        📦
                    </span>
                </div>
                <div class="mt-3">
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-3xl font-black text-amber-600 tabular-nums">{{ number_format($totalSisaStok) }}</h3>
                        <span class="text-xs text-slate-500 font-medium">lembar tersedia</span>
                    </div>
                    <div class="mt-2.5 pt-2.5 border-t border-slate-100 flex items-center justify-between text-xs">
                        <span class="text-slate-600">Stok Pulang: <strong class="text-emerald-700">{{ number_format($stokPulang) }}</strong></span>
                        <span class="text-slate-600">Stok Sakit: <strong class="text-blue-700">{{ number_format($stokSakit) }}</strong></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Tab Pencarian -->
        <div class="bg-white rounded-2xl p-4 sm:p-5 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-3">
                <!-- Filter Tab Status -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 scrollbar-none">
                    <a href="{{ route('admin.surat-uks.index') }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ !request('status') && !request('jenis') ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Dokumen
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['status' => 'tersedia']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'tersedia' ? 'bg-amber-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Stok Tersedia ({{ $totalSisaStok }})
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['status' => 'terjual']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'terjual' ? 'bg-emerald-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Terjual / Terpakai
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['status' => 'kembali']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('status') === 'kembali' ? 'bg-purple-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Telah Kembali
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['jenis' => 'pulang']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('jenis') === 'pulang' ? 'bg-emerald-800 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Izin Pulang
                    </a>
                    <a href="{{ route('admin.surat-uks.index', ['jenis' => 'sakit']) }}"
                       class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap {{ request('jenis') === 'sakit' ? 'bg-blue-700 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Izin Sakit
                    </a>
                </div>

                <!-- Form Search -->
                <form action="{{ route('admin.surat-uks.index') }}" method="GET" class="flex items-center gap-2">
                    @if (request('jenis')) <input type="hidden" name="jenis" value="{{ request('jenis') }}"> @endif
                    @if (request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    <div class="relative w-full md:w-64">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Cari kode/nama/surat..."
                               class="w-full text-xs rounded-xl border-slate-200 pl-8 pr-3 py-2 focus:ring-emerald-500 focus:border-emerald-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <button type="submit" class="px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl transition">
                        Cari
                    </button>
                    @if (request('search') || request('jenis') || request('status'))
                        <a href="{{ route('admin.surat-uks.index') }}" class="px-2.5 py-2 text-slate-500 hover:text-slate-800 text-xs font-medium">Reset</a>
                    @endif
                </form>
            </div>
        </div>

        <!-- Tabel Data (Sesuai Kolom PRD & Instruksi User) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold uppercase tracking-wider text-[11px]">
                            <th class="py-3 px-4">Tgl. Cetak</th>
                            <th class="py-3 px-4">Tgl. Beli</th>
                            <th class="py-3 px-4">Nama Santri</th>
                            <th class="py-3 px-4">Nomor Surat & Kode</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Asrama</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($suratUks as $item)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3 px-4 whitespace-nowrap text-slate-500 font-medium">
                                    {{ $item->tanggal_cetak ? $item->tanggal_cetak->format('d-m-Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($item->tanggal_pembelian)
                                        <span class="font-semibold text-emerald-800">{{ $item->tanggal_pembelian->format('d-m-Y') }}</span>
                                    @else
                                        <span class="text-slate-400 italic">Belum Dibeli</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    @if ($item->nama_santri)
                                        <span class="font-bold text-slate-900 block text-xs">{{ $item->nama_santri }}</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-medium bg-slate-100 text-slate-500 italic">
                                            (Blangko Stok Belum Terjual)
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4">
                                    <div class="space-y-0.5">
                                        <span class="font-mono font-bold text-slate-900 block text-[11px]">{{ $item->nomor_surat }}</span>
                                        <div class="flex items-center gap-1.5">
                                            <span class="px-1.5 py-0.2 rounded bg-emerald-50 border border-emerald-200 text-emerald-800 font-mono text-[10px] font-bold">
                                                Kode: {{ $item->kode_verifikasi }}
                                            </span>
                                            <span class="text-[10px] font-semibold {{ $item->jenis === 'sakit' ? 'text-blue-700' : 'text-emerald-700' }}">
                                                &bull; {{ $item->jenis_label }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-medium">
                                    {{ $item->kelas ?: '-' }}
                                </td>
                                <td class="py-3 px-4 text-slate-600 font-medium">
                                    {{ $item->asrama ?: '-' }}
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap">
                                    @if ($item->status === 'tersedia')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            Tersedia (Stok)
                                        </span>
                                    @elseif ($item->status === 'terjual')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                            Terjual (Rp 5.000)
                                        </span>
                                    @elseif ($item->status === 'kembali')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                            Telah Kembali
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 whitespace-nowrap text-center">
                                    <div class="inline-flex items-center gap-1.5">
                                        @if ($item->status === 'tersedia')
                                            <!-- Tombol Catat Terjual: Hanya isi Nama, Kelas, Asrama -->
                                            <button type="button"
                                                    onclick="openModalCatatId('{{ $item->id }}', '{{ $item->nomor_surat }}', '{{ $item->kode_verifikasi }}', '{{ $item->jenis_label }}')"
                                                    class="px-2.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-[11px] shadow-2xs transition active:scale-95 flex items-center gap-1">
                                                <span>Catat Terjual</span>
                                            </button>
                                        @elseif ($item->status === 'terjual')
                                            <!-- Tombol Tandai Kembali -->
                                            <form action="{{ route('admin.surat-uks.kembali', $item) }}" method="POST" class="inline" onsubmit="return confirm('Tandai santri ini telah kembali ke pondok?')">
                                                @csrf
                                                <button type="submit"
                                                        class="px-2.5 py-1.5 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-bold text-[11px] shadow-2xs transition active:scale-95">
                                                    Tandai Kembali
                                                </button>
                                            </form>
                                        @endif

                                        <!-- Cetak PDF -->
                                        <a href="{{ route('admin.surat-uks.cetak', $item) }}"
                                           target="_blank"
                                           title="Cetak PDF Fisik"
                                           class="p-1.5 text-slate-600 hover:text-emerald-700 hover:bg-slate-100 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                        </a>

                                        <!-- Unduh Word -->
                                        <a href="{{ route('admin.surat-uks.word', $item) }}"
                                           title="Unduh File Word (.doc)"
                                           class="p-1.5 text-slate-600 hover:text-blue-700 hover:bg-slate-100 rounded-lg transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                        </a>

                                        <!-- Hapus -->
                                        <form action="{{ route('admin.surat-uks.destroy', $item) }}"
                                              method="POST"
                                              class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus surat {{ $item->nomor_surat }}? Transaksi kas terkait juga akan disesuaikan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    title="Hapus Surat"
                                                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-slate-100 rounded-lg transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-12 text-center text-slate-400 text-xs">
                                    Belum ada data blangko surat UKS. Silakan klik tombol <strong>"+ Cetak / Buat Blangko"</strong> untuk mencetak blangko fisik dengan nomor urut resmi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($suratUks->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $suratUks->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- MODAL 1: BUAT / CETAK BLANGKO BARU -->
    <div id="modal-generate" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center text-xs">📄</span>
                    <span>Cetak / Terbitkan Blangko UKS</span>
                </h3>
                <button type="button" onclick="closeModalGenerate()" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>

            <form action="{{ route('admin.surat-uks.generate-blangko') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Surat Izin</label>
                    <select name="jenis" required class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="pulang">Surat Izin Pulang (TTD KH. Mursyidi)</option>
                        <option value="sakit">Surat Izin Sakit (TTD Sekretaris & Kesehatan)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jumlah Lembar Yang Dibuat</label>
                    <input type="number" name="jumlah" value="10" min="1" max="100" required
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    <p class="text-[11px] text-slate-500 mt-1">Setiap lembar akan memiliki nomor urut resmi dan kode verifikasi 4-digit unik.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Cetak</label>
                    <input type="date" name="tanggal_cetak" value="{{ date('Y-m-d') }}" required
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalGenerate()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Terbitkan & Tambah ke Stok
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 2: CATAT TERJUAL PER ROW (HANYA NAMA, KELAS, ASRAMA) -->
    <div id="modal-catat-id" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Catat Surat Terjual / Terpakai</h3>
                    <p id="catat-info" class="text-xs text-emerald-800 font-mono font-semibold mt-0.5"></p>
                </div>
                <button type="button" onclick="closeModalCatatId()" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>

            <form id="form-catat-id" method="POST" class="space-y-3.5">
                @csrf
                <div class="bg-emerald-50/80 border border-emerald-200 rounded-xl p-3 text-xs text-emerald-900 space-y-1">
                    <span class="font-bold flex items-center gap-1.5">
                        <span>💵</span> Tarif Resmi: Rp 5.000,-
                    </span>
                    <p class="text-[11px] leading-relaxed">
                        Saat disimpan, sistem otomatis membuat Kas Masuk (Debit Rp 5.000) di modul Bendahara.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Santri *</label>
                    <input type="text" name="nama_santri" required placeholder="Contoh: Muhammad Ali"
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kelas *</label>
                        <input type="text" name="kelas" required placeholder="Contoh: 3 Aliyah"
                               class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Asrama / Kamar *</label>
                        <input type="text" name="asrama" required placeholder="Contoh: Kamar B-04"
                               class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tanggal Pembelian</label>
                    <input type="date" name="tanggal_pembelian" value="{{ date('Y-m-d') }}"
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalCatatId()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Simpan & Masukkan Kas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL 3: CATAT TERJUAL DENGAN INPUT KODE VERIFIKASI 4-DIGIT -->
    <div id="modal-catat-kode" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 flex items-center justify-center text-xs">🏷️</span>
                    <span>Catat Terjual Via Kode 4-Digit</span>
                </h3>
                <button type="button" onclick="closeModalCatatKode()" class="text-slate-400 hover:text-slate-600 text-sm">✕</button>
            </div>

            <form action="{{ route('admin.surat-uks.catat-terjual-kode') }}" method="POST" class="space-y-3.5">
                @csrf
                <div class="bg-amber-50/80 border border-amber-200 rounded-xl p-3 text-xs text-amber-900">
                    Masukkan 4 karakter kode verifikasi yang tertera di surat fisik (misal: <strong>7F2B</strong>).
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kode Verifikasi (4 Digit) *</label>
                    <input type="text" name="kode_verifikasi" maxlength="4" required placeholder="Contoh: 7F2B"
                           class="w-full text-base font-mono font-bold uppercase rounded-xl border-slate-300 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Santri *</label>
                    <input type="text" name="nama_santri" required placeholder="Contoh: Muhammad Ali"
                           class="w-full text-sm rounded-xl border-slate-300 focus:ring-amber-500 focus:border-amber-500">
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kelas *</label>
                        <input type="text" name="kelas" required placeholder="Contoh: 3 Aliyah"
                               class="w-full text-sm rounded-xl border-slate-300 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Asrama / Kamar *</label>
                        <input type="text" name="asrama" required placeholder="Contoh: Kamar B-04"
                               class="w-full text-sm rounded-xl border-slate-300 focus:ring-amber-500 focus:border-amber-500">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="closeModalCatatKode()" class="px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs">
                        Catat & Masukkan Kas
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModalGenerate() {
            document.getElementById('modal-generate').classList.remove('hidden');
        }
        function closeModalGenerate() {
            document.getElementById('modal-generate').classList.add('hidden');
        }

        function openModalCatatId(id, noSurat, kode, jenisLabel) {
            const form = document.getElementById('form-catat-id');
            form.action = `/admin/surat-uks/${id}/catat-terjual`;
            document.getElementById('catat-info').innerText = `${jenisLabel} No: ${noSurat} (Kode: ${kode})`;
            document.getElementById('modal-catat-id').classList.remove('hidden');
        }
        function closeModalCatatId() {
            document.getElementById('modal-catat-id').classList.add('hidden');
        }

        function openModalCatatKode() {
            document.getElementById('modal-catat-kode').classList.remove('hidden');
        }
        function closeModalCatatKode() {
            document.getElementById('modal-catat-kode').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>
