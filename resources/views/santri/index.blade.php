<x-app-layout>
    @section('title', 'Data Santri')

    <div class="space-y-6" x-data="{ modalImpor: false }">
        <!-- Header Judul & Tombol Aksi (Responsif di HP & Desktop) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-1">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Data Santri Pondok Pesantren</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Kelola data induk santri, filter jenjang kelas/kamar asrama, cetak rekapitulasi, dan impor berkas.</p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Tombol Cetak Rekap PDF (Pink Merah Jambu) -->
                <a href="{{ route('admin.santri.cetak', request()->query()) }}" target="_blank"
                   class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-pink-50 hover:bg-pink-100 text-pink-700 text-xs sm:text-sm font-bold rounded-xl border border-pink-300 shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>Cetak Rekap PDF</span>
                </a>

                <!-- Tombol Impor Excel/CSV -->
                <button type="button" @click="modalImpor = true"
                        class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-emerald-50 text-emerald-900 hover:bg-emerald-100 text-xs sm:text-sm font-bold rounded-xl border border-emerald-300 shadow-xs transition">
                    <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Impor Data</span>
                </button>

                <!-- Tombol Tambah Manual -->
                <a href="{{ route('admin.santri.create') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-xs transition active:scale-98">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Santri</span>
                </a>
            </div>
        </div>

        <!-- Laporan Hasil Impor (Jika Baru Selesai Impor) -->
        @if (session('import_result'))
            @php $res = session('import_result'); @endphp
            <div class="bg-white rounded-2xl border border-slate-200/90 shadow-xs p-5 space-y-3">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <h3 class="font-bold text-sm text-slate-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Laporan Hasil Impor File
                    </h3>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-800 font-bold rounded-full border border-emerald-200">
                            Sukses: {{ $res['sukses'] }}
                        </span>
                        <span class="px-2.5 py-1 bg-rose-50 text-rose-800 font-bold rounded-full border border-rose-200">
                            Gagal: {{ $res['gagal'] }}
                        </span>
                    </div>
                </div>

                @if (!empty($res['gagal_list']))
                    <div>
                        <p class="text-xs font-bold text-rose-800 mb-2">Daftar Baris yang Gagal Diproses:</p>
                        <div class="overflow-x-auto max-h-48 border border-rose-100 rounded-xl">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-rose-50 text-rose-900 font-bold">
                                    <tr>
                                        <th class="py-2.5 px-3">Baris</th>
                                        <th class="py-2.5 px-3">NIS</th>
                                        <th class="py-2.5 px-3">Nama</th>
                                        <th class="py-2.5 px-3">Alasan Gagal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-rose-100 text-slate-700">
                                    @foreach ($res['gagal_list'] as $gagal)
                                        <tr class="hover:bg-rose-50/50">
                                            <td class="py-2 px-3 font-mono font-bold">{{ $gagal['baris'] }}</td>
                                            <td class="py-2 px-3 font-mono">{{ $gagal['nis'] }}</td>
                                            <td class="py-2 px-3">{{ $gagal['nama'] }}</td>
                                            <td class="py-2 px-3 text-rose-600 font-medium">{{ $gagal['alasan'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Filter & Pencarian Santri -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 md:p-6 shadow-xs">
            <form method="GET" action="{{ route('admin.santri.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5 sm:gap-4 items-end">
                <!-- Search Input -->
                <div class="lg:col-span-4 space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Cari Nama / NIS</label>
                    <div class="relative">
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Ketik nama atau NIS santri..."
                               class="w-full text-sm rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 pl-10 py-2.5">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>

                <!-- Status Filter -->
                <div class="lg:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Status</label>
                    <select name="status" class="w-full text-sm rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 py-2.5">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                        <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                    </select>
                </div>

                <!-- Kelas Filter -->
                <div class="lg:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Kelas</label>
                    <select name="kelas" class="w-full text-sm rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 py-2.5">
                        <option value="">Semua Kelas</option>
                        @foreach ($listKelas as $k)
                            <option value="{{ $k }}" {{ request('kelas') == $k ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Kamar Filter -->
                <div class="lg:col-span-2 space-y-1.5">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-600">Kamar</label>
                    <select name="kamar" class="w-full text-sm rounded-xl border border-slate-200 focus:border-emerald-500 focus:ring-emerald-500 py-2.5">
                        <option value="">Semua Kamar</option>
                        @foreach ($listKamar as $km)
                            <option value="{{ $km }}" {{ request('kamar') == $km ? 'selected' : '' }}>{{ $km }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tombol Filter & Reset -->
                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit"
                            class="flex-1 py-2.5 px-4 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm rounded-xl transition shadow-xs">
                        Filter
                    </button>
                    @if (request()->hasAny(['q', 'status', 'kelas', 'kamar']))
                        <a href="{{ route('admin.santri.index') }}"
                           class="py-2.5 px-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs sm:text-sm rounded-xl transition" title="Reset Filter">
                            ✕
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Data Santri (Desktop) & Tampilan Kartu (Mobile) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <!-- 1. Tampilan Desktop Table -->
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 border-b border-slate-200 text-slate-700 font-bold uppercase text-xs tracking-wider">
                        <tr>
                            <th class="py-4 px-5">Santri</th>
                            <th class="py-4 px-5">NIS</th>
                            <th class="py-4 px-5">Kelas & Kamar</th>
                            <th class="py-4 px-5">Tahun Masuk</th>
                            <th class="py-4 px-5">Wali Santri</th>
                            <th class="py-4 px-5 text-center">Status</th>
                            <th class="py-4 px-5 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($santris as $santri)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-5">
                                    <div class="flex items-center gap-3">
                                        @if ($santri->foto && Storage::disk('public')->exists($santri->foto))
                                            <img src="{{ asset('storage/' . $santri->foto) }}" alt="{{ $santri->nama }}"
                                                 class="w-10 h-10 rounded-full object-cover border border-emerald-300 shadow-xs shrink-0">
                                        @else
                                            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm border border-emerald-200 shrink-0">
                                                {{ strtoupper(substr($santri->nama, 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <a href="{{ route('admin.santri.show', $santri->id) }}" class="font-bold text-slate-900 hover:text-emerald-800 hover:underline">
                                                {{ $santri->nama }}
                                            </a>
                                            <span class="block text-xs text-slate-500 font-normal mt-0.5">
                                                {{ $santri->tempat_lahir ? $santri->tempat_lahir . ', ' : '' }}{{ $santri->tanggal_lahir_formatted ?: '-' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-5 font-mono font-bold text-slate-800">{{ $santri->nis }}</td>
                                <td class="py-4 px-5">
                                    <span class="block font-bold text-slate-900">{{ $santri->kelas ?: '-' }}</span>
                                    <span class="block text-xs text-slate-500 font-normal">Kamar: {{ $santri->kamar ?: '-' }}</span>
                                </td>
                                <td class="py-4 px-5 font-bold text-slate-800">{{ $santri->tahun_masuk ?: '-' }}</td>
                                <td class="py-4 px-5">
                                    <span class="block font-bold text-slate-900">{{ $santri->nama_wali ?: '-' }}</span>
                                    <span class="block text-xs text-slate-500 font-normal">{{ $santri->hp_wali ?: '-' }}</span>
                                </td>
                                <td class="py-4 px-5 text-center">
                                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold border {{ $santri->status_badge }}">
                                        {{ ucfirst($santri->status) }}
                                    </span>
                                </td>
                                <td class="py-4 px-5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Cetak Biodata PDF (Pink Merah Jambu) -->
                                        <a href="{{ route('admin.santri.biodata', $santri->id) }}" target="_blank"
                                           class="p-2 text-pink-700 bg-pink-50 hover:bg-pink-100 border border-pink-200/80 rounded-xl transition shadow-2xs" title="Cetak Kartu Biodata">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                            </svg>
                                        </a>

                                        <!-- Detail (Biru) -->
                                        <a href="{{ route('admin.santri.show', $santri->id) }}"
                                           class="p-2 text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200/80 rounded-xl transition shadow-2xs" title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>

                                        <!-- Edit (Kuning) -->
                                        <a href="{{ route('admin.santri.edit', $santri->id) }}"
                                           class="p-2 text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl transition shadow-2xs" title="Ubah Data">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>

                                        <!-- Hapus (Merah) -->
                                        <form action="{{ route('admin.santri.destroy', $santri->id) }}" method="POST" class="inline"
                                              data-konfirmasi="Apakah Anda yakin ingin menghapus data santri ini? Data dapat dipulihkan lewat soft delete."
                                              data-judul="Hapus Data Santri"
                                              data-tombol="Ya, Hapus Data">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/80 rounded-xl transition shadow-2xs" title="Hapus Santri">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-slate-400 text-sm">
                                    Tidak ada data santri ditemukan. Silakan tambahkan santri baru atau lakukan impor berkas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- 2. Tampilan Mobile Cards (Khusus Layar HP) -->
            <div class="block md:hidden divide-y divide-slate-100">
                @forelse ($santris as $santri)
                    <div class="p-4 space-y-3">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                @if ($santri->foto && Storage::disk('public')->exists($santri->foto))
                                    <img src="{{ asset('storage/' . $santri->foto) }}" alt="{{ $santri->nama }}"
                                         class="w-11 h-11 rounded-full object-cover border border-emerald-300 shadow-xs shrink-0">
                                @else
                                    <div class="w-11 h-11 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold text-sm border border-emerald-200 shrink-0">
                                        {{ strtoupper(substr($santri->nama, 0, 1)) }}
                                    </div>
                                @endif
                                <div>
                                    <a href="{{ route('admin.santri.show', $santri->id) }}" class="font-bold text-slate-900 text-sm hover:underline">
                                        {{ $santri->nama }}
                                    </a>
                                    <span class="block text-xs font-mono text-slate-500">NIS: {{ $santri->nis }}</span>
                                </div>
                            </div>
                            <span class="px-2.5 py-0.5 rounded-lg text-[11px] font-bold border {{ $santri->status_badge }}">
                                {{ ucfirst($santri->status) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs bg-slate-50 p-2.5 rounded-xl text-slate-600">
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Kelas & Kamar</span>
                                <span class="font-bold text-slate-800">{{ $santri->kelas ?: '-' }} / {{ $santri->kamar ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-slate-400 block text-[10px] uppercase font-bold">Wali Santri</span>
                                <span class="font-bold text-slate-800 truncate block">{{ $santri->nama_wali ?: '-' }}</span>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-1.5 pt-1 flex-wrap">
                            <a href="{{ route('admin.santri.biodata', $santri->id) }}" target="_blank"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-pink-700 bg-pink-50 hover:bg-pink-100 border border-pink-300 rounded-xl text-xs font-bold transition shadow-2xs active:scale-95" title="Cetak Biodata PDF">
                                <svg class="w-3.5 h-3.5 text-pink-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                <span>PDF</span>
                            </a>
                            <a href="{{ route('admin.santri.show', $santri->id) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl text-xs font-bold transition shadow-2xs active:scale-95" title="Lihat Detail">
                                <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <span>Detail</span>
                            </a>
                            <a href="{{ route('admin.santri.edit', $santri->id) }}"
                               class="inline-flex items-center gap-1 px-3 py-1.5 text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl text-xs font-bold transition shadow-2xs active:scale-95" title="Ubah Data">
                                <svg class="w-3.5 h-3.5 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                <span>Edit</span>
                            </a>
                            <form action="{{ route('admin.santri.destroy', $santri->id) }}" method="POST" class="inline"
                                  data-konfirmasi="Apakah Anda yakin ingin menghapus data santri ini? Data dapat dipulihkan lewat soft delete."
                                  data-judul="Hapus Santri"
                                  data-tombol="Ya, Hapus Data">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl text-xs font-bold transition shadow-2xs active:scale-95" title="Hapus Santri">
                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-400 text-xs">
                        Tidak ada data santri ditemukan.
                    </div>
                @endforelse
            </div>

            <!-- Paginasi -->
            @if ($santris->hasPages())
                <div class="p-4 border-t border-slate-100">
                    {{ $santris->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Impor Santri (CSV / Excel) -->
        <div x-show="modalImpor" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="modalImpor" @click="modalImpor = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs transition-opacity"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
                <div x-show="modalImpor" class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Impor Data Santri
                        </h3>
                        <button type="button" @click="modalImpor = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
                    </div>

                    <div class="mt-4 space-y-4">
                        <div class="p-3.5 bg-emerald-50/80 rounded-xl border border-emerald-200 text-xs text-emerald-950 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                            <div>
                                <p class="font-bold">Gunakan Template Standar</p>
                                <p class="text-[11px] text-emerald-800 mt-0.5">Kolom: NIS, nama, tempat lahir, tanggal lahir, alamat, nama wali, no. HP, kamar, kelas, tahun, status.</p>
                            </div>
                            <a href="{{ route('admin.santri.template') }}"
                               class="px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-[11px] rounded-lg shadow-xs whitespace-nowrap transition">
                                Unduh Template
                            </a>
                        </div>

                        <form action="{{ route('admin.santri.impor') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="space-y-1">
                                <label class="block text-xs font-bold text-slate-700 uppercase">Pilih File CSV (.csv) <span class="text-rose-500">*</span></label>
                                <input type="file" name="file" accept=".csv,text/csv" required
                                       class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-800 hover:file:bg-emerald-100 cursor-pointer border border-slate-200 rounded-xl">
                                <p class="text-[11px] text-slate-400">Ukuran berkas maksimal 5 MB. Format CSV dengan pemisah koma (,).</p>
                            </div>

                            <div class="mt-5 pt-3 border-t border-slate-100 flex justify-end gap-2">
                                <button type="button" @click="modalImpor = false"
                                        class="px-4 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-xs transition">
                                    Mulai Impor
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
