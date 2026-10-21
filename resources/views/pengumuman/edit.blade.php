@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Pengumuman</h1>
            <p class="text-sm text-gray-500 mt-1">
                Perbarui rincian pengumuman resmi.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.pengumuman.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-300 text-gray-700 hover:bg-gray-50 text-sm font-semibold transition">
                Kembali
            </a>
        </div>
    </div>

    <!-- Error Validation Alert -->
    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            <p class="font-bold mb-1">Terdapat kesalahan pada formulir:</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
        <form action="{{ route('admin.pengumuman.update', $pengumuman) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="judul" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Judul Pengumuman <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" id="judul" value="{{ old('judul', $pengumuman->judul) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kategori" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kategori <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="kategori" required class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="umum" {{ old('kategori', $pengumuman->kategori) == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="santri" {{ old('kategori', $pengumuman->kategori) == 'santri' ? 'selected' : '' }}>Santri</option>
                        <option value="wali" {{ old('kategori', $pengumuman->kategori) == 'wali' ? 'selected' : '' }}>Wali Santri</option>
                        <option value="akademik" {{ old('kategori', $pengumuman->kategori) == 'akademik' ? 'selected' : '' }}>Akademik</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="terbit" {{ old('status', $pengumuman->status) == 'terbit' ? 'selected' : '' }}>Terbitkan Langsung</option>
                        <option value="draft" {{ old('status', $pengumuman->status) == 'draft' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tgl_tayang" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Tayang <span class="text-rose-500">*</span></label>
                    <input type="date" name="tgl_tayang" id="tgl_tayang" value="{{ old('tgl_tayang', $pengumuman->tgl_tayang ? $pengumuman->tgl_tayang->format('Y-m-d') : '') }}" required class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="tgl_kedaluwarsa" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Kedaluwarsa (Opsional)</label>
                    <input type="date" name="tgl_kedaluwarsa" id="tgl_kedaluwarsa" value="{{ old('tgl_kedaluwarsa', $pengumuman->tgl_kedaluwarsa ? $pengumuman->tgl_kedaluwarsa->format('Y-m-d') : '') }}" class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>
            </div>

            @php
                $initialPos = old('posisi_gambar', $pengumuman->posisi_gambar ?? '50% 50%');
                preg_match('/(\d+)%$/', $initialPos, $matches);
                $initialY = isset($matches[1]) ? (int)$matches[1] : 50;
            @endphp

            <!-- FOTO BANNER & PENGATURAN POSISI BINGKAI -->
            <div x-data="{
                previewUrl: '{{ $pengumuman->gambar ? asset('storage/' . $pengumuman->gambar) : '' }}',
                posY: {{ $initialY }},
                onFileChange(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.previewUrl = URL.createObjectURL(file);
                    }
                }
            }" class="space-y-4 bg-gray-50/70 p-5 rounded-2xl border border-gray-200">
                <div>
                    <label for="gambar" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Foto / Thumbnail Warta (Ganti Berkas)
                    </label>
                    <input type="file" name="gambar" id="gambar" accept="image/jpeg,image/png,image/jpg,image/webp"
                           @change="onFileChange($event)"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 bg-white border border-gray-300 rounded-xl cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1.5 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Format: JPG, PNG, WEBP. Ukuran maksimal <strong>5 MB</strong>. Kosongkan jika tidak mengganti gambar.
                    </p>
                </div>

                <!-- Live Preview Bingkai & Slider Penyesuaian Bagian Yang Terpotong -->
                <div x-show="previewUrl" x-cloak class="space-y-3 pt-3 border-t border-gray-200">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🖼️ Pratinjau Bingkai Banner:</span>
                            <span class="text-emerald-700 font-semibold text-xs lowercase">(bagian yang tampil di kartu berita)</span>
                        </label>
                        <span class="text-xs font-mono font-bold text-emerald-800 bg-emerald-100 px-2.5 py-0.5 rounded-full" x-text="'Posisi Vertikal: ' + posY + '%'"></span>
                    </div>

                    <!-- Kotak Bingkai Rasio Standar Kartu Warta Berita -->
                    <div class="w-full max-w-xl h-52 sm:h-60 rounded-2xl overflow-hidden border-2 border-emerald-400 bg-gray-900 shadow-md relative">
                        <img :src="previewUrl" alt="Pratinjau"
                             class="w-full h-full object-cover transition-all duration-150"
                             :style="'object-position: 50% ' + posY + '%;'">
                        <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-xs text-white text-[10px] px-2 py-0.5 rounded-md font-mono">
                            Simulasi Ukuran Bingkai Kartu Berita
                        </div>
                    </div>

                    <!-- Kontrol Geser Manual Bagian Foto -->
                    <div class="bg-white p-4 rounded-xl border border-gray-200 space-y-3 max-w-xl">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-gray-700">Atur Bagian Foto Yang Tampil:</span>
                            <div class="inline-flex gap-1.5">
                                <button type="button" @click="posY = 0"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border hover:bg-gray-50 transition"
                                        :class="posY === 0 ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'text-gray-600 border-gray-200'">
                                    Atas (0%)
                                </button>
                                <button type="button" @click="posY = 50"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border hover:bg-gray-50 transition"
                                        :class="posY === 50 ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'text-gray-600 border-gray-200'">
                                    Tengah (50%)
                                </button>
                                <button type="button" @click="posY = 100"
                                        class="px-2.5 py-1 text-xs font-semibold rounded-lg border hover:bg-gray-50 transition"
                                        :class="posY === 100 ? 'bg-emerald-100 text-emerald-800 border-emerald-300' : 'text-gray-600 border-gray-200'">
                                    Bawah (100%)
                                </button>
                            </div>
                        </div>

                        <div>
                            <input type="range" min="0" max="100" step="1" x-model="posY"
                                   class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-emerald-600">
                            <div class="flex justify-between text-[10px] text-gray-400 font-mono mt-1">
                                <span>↑ Bagian Atas Foto</span>
                                <span>Tengah</span>
                                <span>Bagian Bawah Foto ↓</span>
                            </div>
                        </div>
                        <p class="text-[11px] text-gray-500">
                            Geser slider di atas untuk menaikkan/menurunkan fokus gambar agar bagian yang terpotong pas dengan bingkai sesuai keinginan Anda.
                        </p>
                    </div>

                    <input type="hidden" name="posisi_gambar" :value="'50% ' + posY + '%'">
                </div>
            </div>

            <div>
                <label for="isi" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Isi Pengumuman <span class="text-rose-500">*</span></label>
                <textarea name="isi" id="isi" rows="8" required class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 leading-relaxed">{{ old('isi', $pengumuman->isi) }}</textarea>
            </div>

            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned', $pengumuman->is_pinned) ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                    <div>
                        <span class="text-sm font-bold text-gray-800">📌 Sematkan Pengumuman Ini (Pinned)</span>
                        <p class="text-xs text-gray-500">Pengumuman akan selalu tampil di baris teratas daftar.</p>
                    </div>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-200 flex items-center justify-end gap-3">
                <a href="{{ route('admin.pengumuman.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition">
                    Perbarui Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
