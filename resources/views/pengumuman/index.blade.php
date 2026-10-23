@extends('layouts.app')

@section('title', 'Kelola Pengumuman')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2">
        <div>
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                <span>Publikasi & Web</span>
                <span>/</span>
                <span class="text-emerald-800 font-semibold">Warta Pengumuman</span>
            </nav>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Pengumuman & Warta Resmi</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                Kelola informasi, berita, dan pengumuman resmi yang tayang di portal website pesantren.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.pengumuman.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-700 hover:bg-emerald-800 shadow-md shadow-emerald-700/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Buat Pengumuman Baru</span>
            </a>
        </div>
    </div>

    <!-- 4 Kartu Ringkasan Pengumuman & Warta -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
        <!-- Total Warta -->
        <div class="bg-white rounded-2xl border border-gray-200/90 p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Warta</span>
                <div class="text-2xl sm:text-3xl font-black text-gray-900 mt-0.5">{{ $statistik['total'] ?? 0 }}</div>
                <span class="text-[11px] text-gray-500 font-medium">Seluruh warta</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-800 border border-emerald-200/60 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                </svg>
            </div>
        </div>

        <!-- Terbit -->
        <div class="bg-white rounded-2xl border border-emerald-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Terbit</span>
                <div class="text-2xl sm:text-3xl font-black text-emerald-950 mt-0.5">{{ $statistik['terbit'] ?? 0 }}</div>
                <span class="text-[11px] text-emerald-800 font-medium">Tayang di publik</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <!-- Draft -->
        <div class="bg-white rounded-2xl border border-amber-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Draft</span>
                <div class="text-2xl sm:text-3xl font-black text-amber-950 mt-0.5">{{ $statistik['draft'] ?? 0 }}</div>
                <span class="text-[11px] text-amber-800 font-medium">Belum tayang</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-700 border border-amber-300 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
        </div>

        <!-- Disematkan (Pinned) -->
        <div class="bg-white rounded-2xl border border-purple-200/80 p-4 sm:p-5 shadow-xs flex items-center justify-between">
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-purple-700">Disematkan</span>
                <div class="text-2xl sm:text-3xl font-black text-purple-950 mt-0.5">{{ $statistik['disematkan'] ?? 0 }}</div>
                <span class="text-[11px] text-purple-800 font-medium">Warta unggulan</span>
            </div>
            <div class="w-11 h-11 rounded-2xl bg-purple-50 text-purple-700 border border-purple-200 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-gray-200/90 shadow-xs">
        <form action="{{ route('admin.pengumuman.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 sm:gap-4 items-end">
            <!-- Kata Kunci -->
            <div class="lg:col-span-4">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Cari Pengumuman</label>
                <div class="relative">
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="Judul atau isi pengumuman..." class="w-full rounded-xl border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600 pl-10 py-2.5 shadow-2xs">
                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
            </div>

            <!-- Kategori -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Kategori</label>
                <select name="kategori" class="w-full rounded-xl border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600 py-2.5">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList ?? ['umum', 'santri', 'wali', 'akademik'] as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') == $kat ? 'selected' : '' }}>{{ ucfirst($kat) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Status</label>
                <select name="status" class="w-full rounded-xl border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600 py-2.5">
                    <option value="">Semua Status</option>
                    <option value="terbit" {{ request('status') == 'terbit' ? 'selected' : '' }}>Terbit</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <!-- Mulai Tanggal -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-600 mb-1.5">Dari Tanggal</label>
                <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai') }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-emerald-600 focus:ring-emerald-600 py-2.5">
            </div>

            <!-- Tombol Filter -->
            <div class="lg:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs sm:text-sm text-white bg-emerald-700 hover:bg-emerald-800 shadow-xs transition">
                    Saring
                </button>
                @if (request()->hasAny(['q', 'kategori', 'status', 'tgl_mulai', 'tgl_selesai']))
                    <a href="{{ route('admin.pengumuman.index') }}" class="py-2.5 px-3 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold border border-rose-300 text-xs sm:text-sm rounded-xl transition shadow-2xs" title="Reset Filter">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- List Pengumuman (Responsive Mobile Cards & Desktop Table) -->
    <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden">
        <!-- Desktop Table (md:block) -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/90 border-b border-gray-200 text-xs font-bold text-gray-700 uppercase tracking-wider">
                    <tr>
                        <th class="py-4 px-5">Judul Pengumuman</th>
                        <th class="py-4 px-5">Kategori</th>
                        <th class="py-4 px-5">Tgl Tayang</th>
                        <th class="py-4 px-5 text-center">Status</th>
                        <th class="py-4 px-5 text-center">Pinned</th>
                        <th class="py-4 px-5 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengumumans as $item)
                        <tr class="hover:bg-gray-50/80 transition">
                            <td class="py-4 px-5">
                                <div class="flex items-center gap-3">
                                    @if($item->gambar)
                                        <img src="{{ asset('storage/' . $item->gambar) }}" alt="Thumb" class="w-12 h-12 rounded-xl object-cover border border-gray-200 shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm uppercase shrink-0 border border-emerald-200">
                                            {{ substr($item->kategori, 0, 2) }}
                                        </div>
                                    @endif
                                    <div>
                                        <a href="{{ route('admin.pengumuman.show', $item) }}" class="font-extrabold text-gray-900 hover:text-emerald-700 transition line-clamp-1">
                                            {{ $item->judul }}
                                        </a>
                                        <span class="text-xs text-gray-400 block font-medium mt-0.5">{{ $item->slug }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-5 capitalize">
                                <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70">
                                    {{ $item->kategori }}
                                </span>
                            </td>
                            <td class="py-4 px-5">
                                <span class="text-sm font-bold text-gray-800 block">{{ $item->formatted_tgl_tayang }}</span>
                                @if($item->tgl_kedaluwarsa)
                                    <span class="text-xs text-gray-500 block font-medium">s.d. {{ $item->formatted_tgl_kedaluwarsa }}</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($item->status === 'terbit')
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Terbit
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center">
                                @if($item->is_pinned)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-amber-100 text-amber-900 border border-amber-300 rounded-lg text-xs font-black">📌 Ya</span>
                                @else
                                    <span class="text-gray-400 text-xs font-semibold">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.pengumuman.show', $item) }}" class="p-2 text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl transition shadow-2xs font-bold" title="Detail">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ route('admin.pengumuman.edit', $item) }}" class="p-2 text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl transition shadow-2xs font-bold" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form action="{{ route('admin.pengumuman.destroy', $item) }}" method="POST"
                                          data-konfirmasi="Apakah Anda yakin ingin menghapus pengumuman ini?"
                                          data-judul="Hapus Pengumuman"
                                          data-tombol="Ya, Hapus"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition shadow-2xs font-bold" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400">
                                Tidak ada data pengumuman ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View (block md:hidden) -->
        <div class="block md:hidden divide-y divide-gray-100">
            @forelse($pengumumans as $item)
                <div class="p-4 space-y-3 hover:bg-gray-50/50 transition">
                    <div class="flex items-start gap-3">
                        @if($item->gambar)
                            <img src="{{ asset('storage/' . $item->gambar) }}" alt="Thumb" class="w-14 h-14 rounded-xl object-cover border border-gray-200 shrink-0">
                        @else
                            <div class="w-14 h-14 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm uppercase shrink-0 border border-emerald-200">
                                {{ substr($item->kategori, 0, 2) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5 mb-1">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200/70 capitalize">
                                    {{ $item->kategori }}
                                </span>
                                @if($item->status === 'terbit')
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                                        Terbit
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-300">
                                        Draft
                                    </span>
                                @endif
                                @if($item->is_pinned)
                                    <span class="px-2 py-0.5 rounded-md text-[11px] font-black bg-amber-100 text-amber-900 border border-amber-300">
                                        📌 Pinned
                                    </span>
                                @endif
                            </div>
                            <a href="{{ route('admin.pengumuman.show', $item) }}" class="font-bold text-gray-900 hover:text-emerald-700 transition line-clamp-2 text-sm leading-snug">
                                {{ $item->judul }}
                            </a>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-500 font-medium">
                                <span>Tayang: {{ $item->formatted_tgl_tayang }}</span>
                                @if($item->tgl_kedaluwarsa)
                                    <span>• s.d. {{ $item->formatted_tgl_kedaluwarsa }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 pt-2 border-t border-gray-100">
                        <a href="{{ route('admin.pengumuman.show', $item) }}" class="py-2 px-2 text-center text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-xl font-bold text-xs flex items-center justify-center gap-1 transition shadow-2xs">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <span>Detail</span>
                        </a>
                        <a href="{{ route('admin.pengumuman.edit', $item) }}" class="py-2 px-2 text-center text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl font-bold text-xs flex items-center justify-center gap-1 transition shadow-2xs">
                            <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            <span>Edit</span>
                        </a>
                        <form action="{{ route('admin.pengumuman.destroy', $item) }}" method="POST"
                              data-konfirmasi="Apakah Anda yakin ingin menghapus pengumuman ini?"
                              data-judul="Hapus Pengumuman"
                              data-tombol="Ya, Hapus"
                              class="inline w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full py-2 px-2 text-center text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl font-bold text-xs flex items-center justify-center gap-1 transition shadow-2xs">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                <span>Hapus</span>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center text-gray-400 text-sm">
                    Tidak ada data pengumuman ditemukan.
                </div>
            @endforelse
        </div>

        @if($pengumumans->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $pengumumans->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
