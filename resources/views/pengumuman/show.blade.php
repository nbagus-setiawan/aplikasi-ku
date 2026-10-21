@extends('layouts.app')

@section('title', 'Detail Pengumuman')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-gray-900">Detail Pengumuman</h1>
                @if($pengumuman->status === 'terbit')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        Terbit
                    </span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        Draft
                    </span>
                @endif
                @if($pengumuman->is_pinned)
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-800">
                        📌 Pinned
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-400 mt-1">Slug: {{ $pengumuman->slug }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.pengumuman.index') }}" class="px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition">
                Kembali
            </a>
            @if($pengumuman->status === 'terbit')
                <a href="{{ url('/pengumuman/' . $pengumuman->slug) }}" target="_blank" class="px-4 py-2 rounded-xl border border-emerald-300 text-emerald-700 hover:bg-emerald-50 text-sm font-semibold transition">
                    Lihat di Publik ↗
                </a>
            @endif
            <a href="{{ route('admin.pengumuman.edit', $pengumuman) }}" class="px-4 py-2 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                Edit
            </a>
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
