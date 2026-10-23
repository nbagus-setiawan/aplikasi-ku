<x-app-layout>
    @section('title', 'Data Pengurus')

    <div class="space-y-6 pb-12" x-data="{
        tab: 'tabel',
        modalTambah: false,
        modalEdit: false,
        tambahFotoPreview: null,
        editFotoPreview: null,
        editData: { id: '', nama: '', jabatan: '', divisi: '', foto_url: '', no_hp: '', periode_mulai: '', periode_selesai: '', is_aktif: true, actionUrl: '' }
    }">
        <!-- Header Judul & Tombol Aksi -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <span>Master Data</span>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Pengurus Pondok</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Manajemen Pengurus Pondok</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola data kepengurusan pesantren Al-Falah Putak, periode masa bakti, dan struktur divisi.</p>
            </div>

            <div class="flex items-center gap-2">
                <button @click="modalTambah = true; tambahFotoPreview = null;"
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs sm:text-sm font-bold rounded-xl shadow-md shadow-emerald-700/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Pengurus</span>
                </button>
            </div>
        </div>

        <!-- Tab Navigasi: Tabel & Struktur Organisasi -->
        <div class="flex items-center border-b border-gray-200 pb-2">
            <div class="inline-flex p-1 bg-gray-100 rounded-xl gap-1">
                <button @click="tab = 'tabel'"
                        :class="tab === 'tabel' ? 'bg-white text-emerald-800 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-bold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    <span>Daftar Pengurus</span>
                </button>
                <button @click="tab = 'struktur'"
                        :class="tab === 'struktur' ? 'bg-white text-emerald-800 shadow-xs' : 'text-gray-600 hover:text-gray-900'"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs sm:text-sm font-bold rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span>Struktur Divisi</span>
                </button>
            </div>
        </div>

        <!-- TAMPILAN 1: TABEL PENGURUS -->
        <div x-show="tab === 'tabel'"
             x-data="liveFilter({
                 hasActiveFilters: {{ request()->hasAny(['is_aktif', 'periode']) ? 'true' : 'false' }},
                 activeCount: {{ (request('is_aktif') !== null && request('is_aktif') !== '' ? 1 : 0) + (request('periode') ? 1 : 0) }}
             })"
             class="space-y-4">
            <!-- Filter & Pencarian (Live & Collapsible) -->
            <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs space-y-4">
                <form x-ref="filterForm" method="GET" action="{{ route('admin.pengurus.index') }}" @submit.prevent="applyLive()">
                    <!-- Baris Utama: Pencarian Global + Tombol Filter Lanjutan + Tombol Reset -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5">
                        <!-- Search Bar Global -->
                        <div class="relative flex-1">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama pengurus, jabatan, divisi..."
                                   @input="applyLive()"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 pl-10 pr-10 py-2.5 shadow-2xs">
                            <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                            <div x-show="loading" x-cloak class="absolute right-3.5 top-3">
                                <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Tombol Toggle Filter Lanjutan -->
                        <button type="button" @click="showFilters = !showFilters"
                                :class="showFilters || activeCount > 0 ? 'bg-emerald-50 text-emerald-800 border-emerald-300' : 'bg-white text-gray-700 border-gray-200 hover:bg-gray-50'"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border font-bold text-xs sm:text-sm shadow-2xs transition shrink-0">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            <span>Filter Lanjutan</span>
                            <template x-if="activeCount > 0">
                                <span class="px-1.5 py-0.5 text-[11px] font-black rounded-full bg-emerald-700 text-white" x-text="activeCount"></span>
                            </template>
                            <svg class="w-3.5 h-3.5 transition-transform duration-200" :class="showFilters ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Tombol Reset Filter -->
                        <button type="button" @click="resetFilters()"
                                class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 font-bold text-xs sm:text-sm shadow-2xs transition shrink-0"
                                title="Bersihkan Semua Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <span class="hidden sm:inline">Reset</span>
                        </button>
                    </div>

                    <!-- Panel Filter Lanjutan (Disembunyikan secara default) -->
                    <div x-show="showFilters" x-cloak
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="pt-4 mt-4 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">Status Keaktifan</label>
                            <select name="is_aktif" @change="applyLive()" class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2">
                                <option value="">Semua Status</option>
                                <option value="1" {{ request('is_aktif') === '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ request('is_aktif') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500">Periode Mulai</label>
                            <select name="periode" @change="applyLive()" class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2 font-mono">
                                <option value="">Semua Periode</option>
                                @foreach ($listPeriode as $p)
                                    <option value="{{ $p }}" {{ request('periode') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <div id="data-container" class="relative space-y-4">
                <!-- Overlay Indikator Saat Memuat Data -->
                <div x-show="loading" x-cloak class="absolute inset-0 bg-white/60 z-10 backdrop-blur-[1px] flex items-center justify-center transition-opacity rounded-2xl">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white shadow-md border border-gray-200 text-xs font-bold text-emerald-800">
                        <svg class="animate-spin w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Menyaring data pengurus...</span>
                    </div>
                </div>

            <!-- Tampilan Desktop: Table -->
            <div class="hidden md:block bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-50/90 border-b border-gray-200 text-gray-700 font-bold uppercase text-xs tracking-wider">
                            <tr>
                                <th class="py-3.5 px-5">Nama Pengurus</th>
                                <th class="py-3.5 px-5">Jabatan</th>
                                <th class="py-3.5 px-5">Divisi / Bidang</th>
                                <th class="py-3.5 px-5">Periode</th>
                                <th class="py-3.5 px-5">Nomor HP</th>
                                <th class="py-3.5 px-5 text-center">Status</th>
                                <th class="py-3.5 px-5 text-center w-28">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($penguruses as $pengurus)
                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="py-4 px-5">
                                        <div class="flex items-center gap-3">
                                            @if ($pengurus->foto)
                                                <img src="{{ asset('storage/' . $pengurus->foto) }}" alt="{{ $pengurus->nama }}"
                                                     class="w-10 h-10 rounded-xl object-cover border border-gray-200 shrink-0 shadow-2xs">
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-xs shrink-0 border border-emerald-200">
                                                    {{ strtoupper(substr($pengurus->nama, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-extrabold text-gray-900">{{ $pengurus->nama }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-5">
                                        <span class="font-bold text-emerald-800">{{ $pengurus->jabatan }}</span>
                                    </td>
                                    <td class="py-4 px-5 font-medium text-gray-700">
                                        {{ $pengurus->divisi ?: '-' }}
                                    </td>
                                    <td class="py-4 px-5 font-mono text-xs font-semibold text-gray-700">
                                        {{ $pengurus->periode_teks }}
                                    </td>
                                    <td class="py-4 px-5 font-mono text-xs text-gray-600">
                                        {{ $pengurus->no_hp ?: '-' }}
                                    </td>
                                    <td class="py-4 px-5 text-center">
                                        @if ($pengurus->is_aktif)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-600">
                                                Non-Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-5 text-center whitespace-nowrap">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <!-- Edit Trigger (Kuning) -->
                                            <button @click="
                                                editFotoPreview = null;
                                                editData = {
                                                    id: '{{ $pengurus->id }}',
                                                    nama: '{{ addslashes($pengurus->nama) }}',
                                                    jabatan: '{{ addslashes($pengurus->jabatan) }}',
                                                    divisi: '{{ addslashes($pengurus->divisi ?? '') }}',
                                                    foto_url: '{{ $pengurus->foto ? asset('storage/' . $pengurus->foto) : '' }}',
                                                    no_hp: '{{ addslashes($pengurus->no_hp ?? '') }}',
                                                    periode_mulai: '{{ addslashes($pengurus->periode_mulai ?? '') }}',
                                                    periode_selesai: '{{ addslashes($pengurus->periode_selesai ?? '') }}',
                                                    is_aktif: {{ $pengurus->is_aktif ? 'true' : 'false' }},
                                                    actionUrl: '{{ route('admin.pengurus.update', $pengurus->id) }}'
                                                };
                                                modalEdit = true;
                                            " class="p-2 text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl transition shadow-2xs" title="Ubah Data">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <!-- Hapus (Merah) -->
                                            <form action="{{ route('admin.pengurus.destroy', $pengurus->id) }}" method="POST" class="inline"
                                                  data-konfirmasi="Apakah Anda yakin ingin menghapus data pengurus ini?"
                                                  data-judul="Hapus Data Pengurus"
                                                  data-tombol="Ya, Hapus Data">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200/60 rounded-xl transition shadow-2xs" title="Hapus Pengurus">
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
                                    <td colspan="7" class="py-12 text-center text-gray-400">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-10 h-10 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="font-medium text-sm text-gray-500">Belum ada data pengurus terdaftar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tampilan Mobile: Cards (Agar Nyaman di Layar HP) -->
            <div class="block md:hidden space-y-3">
                @forelse ($penguruses as $pengurus)
                    <div class="bg-white rounded-2xl border border-gray-200 p-4 shadow-xs space-y-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                @if ($pengurus->foto)
                                    <img src="{{ asset('storage/' . $pengurus->foto) }}" alt="{{ $pengurus->nama }}"
                                         class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0 shadow-2xs">
                                @else
                                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-sm shrink-0 border border-emerald-200">
                                        {{ strtoupper(substr($pengurus->nama, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <h3 class="font-extrabold text-gray-900 text-sm leading-tight truncate">{{ $pengurus->nama }}</h3>
                                    <p class="text-xs font-bold text-emerald-800 mt-0.5">{{ $pengurus->jabatan }}</p>
                                </div>
                            </div>
                            <div>
                                @if ($pengurus->is_aktif)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">
                                        Non-Aktif
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 bg-gray-50 p-2.5 rounded-xl border border-gray-100">
                            <div>
                                <span class="text-[10px] uppercase font-bold text-gray-400 block">Divisi:</span>
                                <span class="font-medium text-gray-800">{{ $pengurus->divisi ?: '-' }}</span>
                            </div>
                            <div>
                                <span class="text-[10px] uppercase font-bold text-gray-400 block">Periode:</span>
                                <span class="font-mono font-semibold text-gray-800">{{ $pengurus->periode_teks }}</span>
                            </div>
                            @if ($pengurus->no_hp)
                                <div class="col-span-2 pt-1 border-t border-gray-200/60 flex items-center justify-between">
                                    <span class="text-[10px] uppercase font-bold text-gray-400">Kontak:</span>
                                    <span class="font-mono text-emerald-800 font-bold">{{ $pengurus->no_hp }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Tombol Aksi Mobile -->
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-gray-100">
                            <button @click="
                                editFotoPreview = null;
                                editData = {
                                    id: '{{ $pengurus->id }}',
                                    nama: '{{ addslashes($pengurus->nama) }}',
                                    jabatan: '{{ addslashes($pengurus->jabatan) }}',
                                    divisi: '{{ addslashes($pengurus->divisi ?? '') }}',
                                    foto_url: '{{ $pengurus->foto ? asset('storage/' . $pengurus->foto) : '' }}',
                                    no_hp: '{{ addslashes($pengurus->no_hp ?? '') }}',
                                    periode_mulai: '{{ addslashes($pengurus->periode_mulai ?? '') }}',
                                    periode_selesai: '{{ addslashes($pengurus->periode_selesai ?? '') }}',
                                    is_aktif: {{ $pengurus->is_aktif ? 'true' : 'false' }},
                                    actionUrl: '{{ route('admin.pengurus.update', $pengurus->id) }}'
                                };
                                modalEdit = true;
                            " class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl transition shadow-2xs active:scale-95">
                                <svg class="w-3.5 h-3.5 text-amber-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                <span>Ubah</span>
                            </button>

                            <form action="{{ route('admin.pengurus.destroy', $pengurus->id) }}" method="POST" class="inline"
                                  data-konfirmasi="Apakah Anda yakin ingin menghapus data pengurus ini?"
                                  data-judul="Hapus Data Pengurus"
                                  data-tombol="Ya, Hapus Data">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition shadow-2xs active:scale-95">
                                    <svg class="w-3.5 h-3.5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl border border-gray-200 p-8 text-center text-gray-400">
                        <p class="font-medium text-sm text-gray-500">Belum ada data pengurus terdaftar.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($penguruses->hasPages())
                <div class="p-4 bg-white rounded-2xl border border-gray-200 shadow-xs">
                    {{ $penguruses->links() }}
                </div>
            @endif
            </div>
        </div>

        <!-- TAMPILAN 2: STRUKTUR KEPENGURUSAN PER DIVISI -->
        <div x-show="tab === 'struktur'" x-cloak class="space-y-6">
            <!-- Header Ringkasan Struktur -->
            <div class="bg-white rounded-2xl border border-gray-200/90 p-5 sm:p-6 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold shadow-md shadow-emerald-700/20 shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-extrabold text-gray-900 tracking-tight">
                            Bagan Struktur Organisasi & Divisi Aktif
                        </h3>
                        <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Susunan amanah dan pembagian tugas kepengurusan Pondok Pesantren Al Falah Putra.</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                        <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                        {{ $strukturPengurus->count() }} Divisi
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-blue-50 text-blue-800 border border-blue-200/70">
                        <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                        {{ $totalAktif }} Pengurus Aktif
                    </span>
                </div>
            </div>

            <!-- Kontainer Blok Divisi (Masonry Alami: Tinggi Mengikuti Jumlah Anggota Masing-Masing, Tidak Memanjang Mengikuti Yang Banyak) -->
            <div class="columns-1 md:columns-2 xl:columns-3 gap-6 space-y-6">
                @forelse ($strukturPengurus as $divisi => $items)
                    <div class="break-inside-avoid bg-white rounded-2xl border border-gray-200/90 shadow-xs hover:border-emerald-300 hover:shadow-md transition-all duration-200 overflow-hidden">
                        <!-- Header Divisi -->
                        <div class="bg-gradient-to-r from-emerald-800 to-emerald-900 text-white px-5 py-3.5 flex items-center justify-between">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-white/15 flex items-center justify-center shrink-0 text-white">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <h4 class="font-extrabold text-sm sm:text-base text-white tracking-wide truncate">
                                    {{ $divisi }}
                                </h4>
                            </div>
                            <span class="text-xs bg-emerald-950/70 text-emerald-200 font-bold px-2.5 py-1 rounded-full shrink-0 border border-emerald-700/60 shadow-2xs">
                                {{ $items->count() }} Orang
                            </span>
                        </div>

                        <!-- Daftar Anggota Divisi (Tinggi Secukupnya, Sangat Rapi & Jelas Dibaca) -->
                        <div class="divide-y divide-gray-100">
                            @foreach ($items as $item)
                                <div class="p-4 sm:p-4.5 hover:bg-gray-50/70 transition-colors">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <!-- Avatar Foto / Inisial -->
                                            @if ($item->foto)
                                                <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}"
                                                     class="w-11 h-11 rounded-xl object-cover border border-emerald-200 shadow-2xs shrink-0">
                                            @else
                                                <div class="w-11 h-11 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold flex items-center justify-center text-sm shrink-0 border border-emerald-200 shadow-2xs">
                                                    {{ strtoupper(substr($item->nama, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="font-bold text-sm sm:text-base text-gray-900 leading-snug truncate">
                                                    {{ $item->nama }}
                                                </p>
                                                <div class="mt-1">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                                        {{ $item->jabatan }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tombol Edit Cepat -->
                                        <button type="button" @click="
                                            editFotoPreview = null;
                                            editData = {
                                                id: '{{ $item->id }}',
                                                nama: '{{ addslashes($item->nama) }}',
                                                jabatan: '{{ addslashes($item->jabatan) }}',
                                                divisi: '{{ addslashes($item->divisi ?? '') }}',
                                                foto_url: '{{ $item->foto ? asset('storage/' . $item->foto) : '' }}',
                                                no_hp: '{{ addslashes($item->no_hp ?? '') }}',
                                                periode_mulai: '{{ addslashes($item->periode_mulai ?? '') }}',
                                                periode_selesai: '{{ addslashes($item->periode_selesai ?? '') }}',
                                                is_aktif: {{ $item->is_aktif ? 'true' : 'false' }},
                                                actionUrl: '{{ route('admin.pengurus.update', $item->id) }}'
                                            };
                                            modalEdit = true;
                                        " class="p-1.5 text-gray-400 hover:text-emerald-700 hover:bg-emerald-50 rounded-lg transition shrink-0" title="Ubah Data Pengurus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Informasi Masa Bakti & Kontak -->
                                    <div class="mt-3 pt-2.5 border-t border-gray-100 flex flex-wrap items-center justify-between gap-2 text-xs">
                                        <span class="text-gray-500 flex items-center gap-1.5 font-medium">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Masa Bakti: <strong class="text-gray-700 font-mono">{{ $item->periode_teks }}</strong>
                                        </span>

                                        @if ($item->no_hp)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_hp) }}" target="_blank"
                                               class="inline-flex items-center gap-1.5 text-emerald-700 hover:text-emerald-800 font-semibold font-mono hover:underline" title="Kirim Pesan WhatsApp">
                                                <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                                <span>{{ $item->no_hp }}</span>
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-[11px] italic">Tanpa kontak HP</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-2xl border border-gray-200 p-12 text-center text-gray-400">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        <p class="text-sm font-bold text-gray-600">Belum Ada Struktur Kepengurusan Aktif</p>
                        <p class="text-xs text-gray-400 mt-1">Tambahkan pengurus dan tentukan divisinya agar muncul di bagan struktur ini.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- MODAL TAMBAH PENGURUS -->
        <div x-show="modalTambah" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div x-show="modalTambah"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="modalTambah = false"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

                <div x-show="modalTambah"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom sm:align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full p-6 sm:p-8">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-gray-900">Tambah Pengurus Baru</h3>
                        </div>
                        <button type="button" @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            ✕
                        </button>
                    </div>

                    <form action="{{ route('admin.pengurus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap Pengurus <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" required placeholder="Contoh: Ustadz Ahmad Fauzi, S.Pd.I"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                        </div>

                        <!-- Unggah Foto Pengurus -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Foto Profil Pengurus (Opsional)
                            </label>
                            <div class="flex items-center gap-3">
                                <template x-if="tambahFotoPreview">
                                    <img :src="tambahFotoPreview" class="w-14 h-14 rounded-2xl object-cover border-2 border-emerald-500 shadow-sm shrink-0">
                                </template>
                                <template x-if="!tambahFotoPreview">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </template>
                                <div class="flex-1">
                                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                                           @change="const file = $event.target.files[0]; if (file) tambahFotoPreview = URL.createObjectURL(file);"
                                           class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 bg-white border border-gray-300 rounded-xl cursor-pointer">
                                    <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG, WEBP. Maksimal 5 MB.</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="jabatan" required placeholder="Contoh: Ketua / Sekretaris"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Divisi / Bagian
                                </label>
                                <input type="text" name="divisi" placeholder="Contoh: Kesantrian / Keamanan"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nomor HP / WhatsApp</label>
                            <input type="text" name="no_hp" placeholder="Contoh: 081234567890"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-mono">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Periode Mulai</label>
                                <input type="text" name="periode_mulai" placeholder="Contoh: 2025"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Periode Selesai</label>
                                <input type="text" name="periode_selesai" placeholder="Contoh: 2027"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-mono">
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_aktif" value="1" checked
                                       class="rounded-lg border-gray-300 text-emerald-700 shadow-xs focus:ring-emerald-600">
                                <span class="ms-2.5 text-xs font-bold text-gray-700">Status Aktif Menjabat</span>
                            </label>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="modalTambah = false"
                                    class="px-4 py-2.5 text-xs sm:text-sm font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition shadow-xs active:scale-95">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl shadow-md shadow-emerald-700/20 active:scale-95 transition-all">
                                Simpan Pengurus
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL EDIT PENGURUS -->
        <div x-show="modalEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end sm:items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div x-show="modalEdit"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="modalEdit = false"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"></div>

                <div x-show="modalEdit"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="inline-block align-bottom sm:align-middle bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-lg w-full p-6 sm:p-8">
                    
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100 mb-5">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-800 flex items-center justify-center font-bold">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </div>
                            <h3 class="text-base sm:text-lg font-extrabold text-gray-900">Ubah Data Pengurus</h3>
                        </div>
                        <button type="button" @click="modalEdit = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                            ✕
                        </button>
                    </div>

                    <form :action="editData.actionUrl" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap Pengurus <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" x-model="editData.nama" required
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                        </div>

                        <!-- Unggah Foto Pengurus (Edit) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Foto Profil Pengurus
                            </label>
                            <div class="flex items-center gap-3">
                                <template x-if="editFotoPreview">
                                    <img :src="editFotoPreview" class="w-14 h-14 rounded-2xl object-cover border-2 border-blue-500 shadow-sm shrink-0">
                                </template>
                                <template x-if="!editFotoPreview && editData.foto_url">
                                    <img :src="editData.foto_url" class="w-14 h-14 rounded-2xl object-cover border border-gray-300 shadow-sm shrink-0">
                                </template>
                                <template x-if="!editFotoPreview && !editData.foto_url">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 border border-dashed border-gray-300 flex items-center justify-center text-gray-400 shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                </template>
                                <div class="flex-1">
                                    <input type="file" name="foto" accept="image/jpeg,image/png,image/jpg,image/webp"
                                           @change="const file = $event.target.files[0]; if (file) editFotoPreview = URL.createObjectURL(file);"
                                           class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 bg-white border border-gray-300 rounded-xl cursor-pointer">
                                    <p class="text-[11px] text-gray-400 mt-1">Pilih gambar baru untuk mengganti foto (Maks 5 MB).</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Jabatan <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="jabatan" x-model="editData.jabatan" required
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                    Divisi / Bagian
                                </label>
                                <input type="text" name="divisi" x-model="editData.divisi"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nomor HP / WhatsApp</label>
                            <input type="text" name="no_hp" x-model="editData.no_hp"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-mono">
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Periode Mulai</label>
                                <input type="text" name="periode_mulai" x-model="editData.periode_mulai"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Periode Selesai</label>
                                <input type="text" name="periode_selesai" x-model="editData.periode_selesai"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-mono">
                            </div>
                        </div>

                        <div class="pt-2">
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_aktif" value="1" x-model="editData.is_aktif"
                                       class="rounded-lg border-gray-300 text-emerald-700 shadow-xs focus:ring-emerald-600">
                                <span class="ms-2.5 text-xs font-bold text-gray-700">Status Aktif Menjabat</span>
                            </label>
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-end gap-2.5">
                            <button type="button" @click="modalEdit = false"
                                    class="px-4 py-2.5 text-xs sm:text-sm font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition shadow-xs active:scale-95">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 text-xs sm:text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl shadow-md shadow-emerald-700/20 active:scale-95 transition-all">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
