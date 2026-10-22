@extends('layouts.app')

@section('title', 'Buat Pengumuman Baru')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Pengumuman Baru</h1>
            <p class="text-sm text-gray-500 mt-1">
                Tulis warta atau pengumuman resmi pesantren untuk dipublikasikan.
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
        <form action="{{ route('admin.pengumuman.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="judul" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Judul Pengumuman <span class="text-rose-500">*</span></label>
                <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required placeholder="Contoh: Jadwal Kedatangan Santri Baru Tahun Ajaran 1448 H" class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="kategori" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kategori <span class="text-rose-500">*</span></label>
                    <select name="kategori" id="kategori" required class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="umum" {{ old('kategori') == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="santri" {{ old('kategori') == 'santri' ? 'selected' : '' }}>Santri</option>
                        <option value="wali" {{ old('kategori') == 'wali' ? 'selected' : '' }}>Wali Santri</option>
                        <option value="akademik" {{ old('kategori') == 'akademik' ? 'selected' : '' }}>Akademik</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Publikasi <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                        <option value="terbit" {{ old('status', 'terbit') == 'terbit' ? 'selected' : '' }}>Terbitkan Langsung</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Simpan sebagai Draf</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="tgl_tayang" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Tayang <span class="text-rose-500">*</span></label>
                    <input type="date" name="tgl_tayang" id="tgl_tayang" value="{{ old('tgl_tayang', date('Y-m-d')) }}" required class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                </div>

                <div>
                    <label for="tgl_kedaluwarsa" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Kedaluwarsa (Opsional)</label>
                    <input type="date" name="tgl_kedaluwarsa" id="tgl_kedaluwarsa" value="{{ old('tgl_kedaluwarsa') }}" class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                    <span class="text-[11px] text-gray-400 mt-1 block">Kosongkan jika berlaku seterusnya tanpa batas waktu.</span>
                </div>
            </div>

            <!-- FOTO BANNER & PENGATURAN POSISI BINGKAI -->
            <div x-data="{
                previewUrl: null,
                posY: 50,
                isCompressing: false,
                async onFileChange(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    // Jika ukuran file di atas 1MB, otomatis kompres di browser agar tidak tertolak batas server
                    if (file.size > 1024 * 1024 && file.type.startsWith('image/')) {
                        this.isCompressing = true;
                        try {
                            const compressed = await this.compressImage(file);
                            const dt = new DataTransfer();
                            dt.items.add(compressed);
                            e.target.files = dt.files;
                        } catch (err) {
                            console.warn('Kompresi otomatis dilewati:', err);
                        } finally {
                            this.isCompressing = false;
                        }
                    }

                    if (e.target.files[0]) {
                        this.previewUrl = URL.createObjectURL(e.target.files[0]);
                    }
                },
                compressImage(file) {
                    return new Promise((resolve) => {
                        const reader = new FileReader();
                        reader.onload = (event) => {
                            const img = new Image();
                            img.onload = () => {
                                const maxWidth = 1600;
                                const maxHeight = 1600;
                                let width = img.width;
                                let height = img.height;
                                if (width > maxWidth || height > maxHeight) {
                                    if (width > height) {
                                        height = Math.round((height * maxWidth) / width);
                                        width = maxWidth;
                                    } else {
                                        width = Math.round((width * maxHeight) / height);
                                        height = maxHeight;
                                    }
                                }
                                const canvas = document.createElement('canvas');
                                canvas.width = width;
                                canvas.height = height;
                                const ctx = canvas.getContext('2d');
                                ctx.drawImage(img, 0, 0, width, height);
                                canvas.toBlob((blob) => {
                                    if (!blob || blob.size >= file.size) {
                                        resolve(file);
                                    } else {
                                        const newFile = new File([blob], file.name.replace(/\.[^/.]+$/, '') + '.jpg', {
                                            type: 'image/jpeg',
                                            lastModified: Date.now()
                                        });
                                        resolve(newFile);
                                    }
                                }, 'image/jpeg', 0.82);
                            };
                            img.src = event.target.result;
                        };
                        reader.readAsDataURL(file);
                    });
                }
            }" class="space-y-4 bg-gray-50/70 p-5 rounded-2xl border border-gray-200">
                <div>
                    <label for="gambar" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                        Foto / Thumbnail Warta (Opsional)
                    </label>
                    <input type="file" name="gambar" id="gambar" accept="image/*"
                           @change="onFileChange($event)"
                           class="w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 bg-white border border-gray-300 rounded-xl cursor-pointer">
                    <span class="text-[11px] text-gray-400 mt-1 block">Format: JPG, PNG, WEBP. Maksimal 5 MB.</span>
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
                <textarea name="isi" id="isi" rows="8" required placeholder="Tuliskan isi pengumuman secara rinci..." class="w-full rounded-xl border-gray-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 leading-relaxed">{{ old('isi') }}</textarea>
            </div>

            <div class="p-4 rounded-xl bg-gray-50 border border-gray-200">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="checkbox" name="is_pinned" value="1" {{ old('is_pinned') ? 'checked' : '' }} class="rounded text-emerald-600 focus:ring-emerald-500 h-4 w-4">
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
                    Simpan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
