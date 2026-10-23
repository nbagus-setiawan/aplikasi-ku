@extends('layouts.app')

@section('title', 'Detail Pengumuman')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-xs text-gray-500 font-medium">
        <a href="{{ route('admin.pengumuman.index') }}" class="hover:text-emerald-700 transition">Pengumuman</a>
        <span>/</span>
        <span class="text-gray-800 font-bold truncate max-w-xs">{{ $pengumuman->judul }}</span>
    </nav>

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl font-black text-gray-900">Detail Pengumuman</h1>
                @if($pengumuman->status === 'terbit')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300">
                        Terbit
                    </span>
                @else
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800 border border-amber-300">
                        Draft
                    </span>
                @endif
                @if($pengumuman->is_pinned)
                    <span class="px-2.5 py-1 rounded-lg text-xs font-black bg-amber-100 text-amber-900 border border-amber-300 inline-flex items-center gap-1">
                        📌 Pinned
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-1 font-mono">Slug: {{ $pengumuman->slug }}</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.pengumuman.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 transition shadow-2xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali</span>
            </a>
            @if($pengumuman->status === 'terbit')
                <a href="{{ url('/pengumuman/' . $pengumuman->slug) }}" target="_blank" class="px-3.5 py-2 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 transition shadow-2xs inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    <span>Lihat di Publik</span>
                </a>
            @endif
            <a href="{{ route('admin.pengumuman.edit', $pengumuman) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 transition shadow-2xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                <span>Edit</span>
            </a>
            <form action="{{ route('admin.pengumuman.destroy', $pengumuman) }}" method="POST"
                  data-konfirmasi="Apakah Anda yakin ingin menghapus pengumuman ini secara permanen?"
                  data-judul="Hapus Pengumuman"
                  data-tombol="Ya, Hapus"
                  class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 rounded-xl text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 transition shadow-2xs inline-flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    <span>Hapus</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Konten Kartu -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
        @if($pengumuman->gambar)
            <div class="h-64 sm:h-80 overflow-hidden bg-gray-100">
                <img src="{{ asset('storage/' . $pengumuman->gambar) }}" alt="{{ $pengumuman->judul }}" class="w-full h-full object-cover">
            </div>
        @endif

        <div class="p-6 sm:p-8 space-y-6">
            <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 border-b border-gray-100 pb-4">
                <div>
                    <span class="text-gray-400 uppercase tracking-wider block text-[10px]">Kategori</span>
                    <span class="font-bold text-gray-800 capitalize">{{ $pengumuman->kategori }}</span>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider block text-[10px]">Tgl Tayang</span>
                    <span class="font-bold text-gray-800">{{ $pengumuman->formatted_tgl_tayang }}</span>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider block text-[10px]">Kedaluwarsa</span>
                    <span class="font-bold text-gray-800">{{ $pengumuman->formatted_tgl_kedaluwarsa }}</span>
                </div>
                <div>
                    <span class="text-gray-400 uppercase tracking-wider block text-[10px]">Dibuat Oleh</span>
                    <span class="font-bold text-gray-800">{{ $pengumuman->user->name ?? 'Sekretaris' }}</span>
                </div>
            </div>

            <h2 class="text-2xl font-extrabold text-gray-900 leading-snug">
                {{ $pengumuman->judul }}
            </h2>

            <div class="text-gray-700 text-sm leading-relaxed space-y-4">
                {!! nl2br(e($pengumuman->isi)) !!}
            </div>
        </div>
    </div>
</div>
@endsection
