@extends('layouts.app')

@section('title', 'Edit Pengumuman')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/cropperjs/cropper.min.css') }}">
<style>
    .cropper-view-box,
    .cropper-face {
        border-radius: 0.5rem;
    }
</style>
@endpush

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
            <a href="{{ route('admin.pengumuman.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-300 text-sm font-bold transition shadow-2xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
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

            <!-- FOTO BANNER & CROPPER INTERAKTIF -->
            <div x-data="pengumumanEditCropper({
                initialPreview: '{{ $pengumuman->gambar ? asset('storage/' . $pengumuman->gambar) : '' }}'
            })" class="space-y-4 bg-slate-50 p-4 sm:p-6 rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-xs font-bold text-gray-800 uppercase tracking-wider">
                            Foto / Banner Warta
                        </label>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Ganti atau potong ulang banner agar pas dengan bingkai kartu warta berita.
                        </p>
                    </div>
                    <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">
                        Fitur Crop Aktif
                    </span>
                </div>

                <!-- Input File Tersembunyi & Input Penanda Hapus Gambar -->
                <input type="file" name="gambar" id="gambar" accept="image/*" class="hidden"
                       @change="onFileSelect($event)">
                <input type="hidden" name="hapus_gambar" id="hapus_gambar" value="0">
                <input type="hidden" name="posisi_gambar" value="50% 50%">

                <!-- Area Jika Belum Ada Foto atau Foto Dihapus -->
                <div x-show="!previewUrl" class="border-2 border-dashed border-slate-300 hover:border-emerald-500 rounded-2xl p-6 text-center bg-white transition cursor-pointer"
                     @click="document.getElementById('gambar').click()">
                    <div class="w-12 h-12 mx-auto mb-3 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-sm font-semibold text-gray-800">Klik untuk memilih foto dari perangkat</p>
                    <p class="text-xs text-gray-500 mt-1">Mendukung format JPG, PNG, WEBP (Maksimal 5 MB)</p>
                    <button type="button" class="mt-3 px-4 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold rounded-xl shadow-xs transition">
                        Pilih & Potong Gambar
                    </button>
                </div>

                <!-- Kartu Pratinjau Banner Aktif -->
                <div x-show="previewUrl" x-cloak class="space-y-3 bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold text-gray-800 uppercase tracking-wider">Foto Banner Aktif</span>
                            <span x-show="fileSizeText" class="text-[11px] font-mono text-gray-500 bg-gray-100 px-2 py-0.5 rounded-md" x-text="fileSizeText"></span>
                            <span x-show="isNewFile" class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Foto Baru Terpilih</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" x-show="originalImageSrc" @click="openCropper()"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-xl transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                <span>Potong Ulang</span>
                            </button>
                            <button type="button" @click="document.getElementById('gambar').click()"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-bold text-amber-900 bg-amber-100 hover:bg-amber-200 border border-amber-300 rounded-xl transition shadow-2xs">
                                <span>Ganti Foto</span>
                            </button>
                            <button type="button" @click="removeImage()"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition shadow-2xs">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>

                    <!-- Bingkai Tampilan Banner Responsif -->
                    <div class="relative w-full rounded-xl overflow-hidden bg-slate-900 border border-slate-200 aspect-[16/9] max-h-72 flex items-center justify-center">
                        <img :src="previewUrl" alt="Pratinjau Banner" class="w-full h-full object-cover">
                        <div class="absolute bottom-2 left-2 bg-black/60 backdrop-blur-xs text-white text-[11px] px-2.5 py-1 rounded-md font-mono">
                            Pratinjau Tampilan Warta
                        </div>
                    </div>
                </div>

                <!-- MODAL CROPPER INTERAKTIF -->
                <div x-show="cropperModalOpen" x-cloak
                     class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-3 sm:p-6 bg-black/75 backdrop-blur-xs"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">

                    <div class="bg-white w-full max-w-4xl rounded-2xl shadow-2xl overflow-hidden border border-gray-100 flex flex-col max-h-[92vh]"
                         @click.away="closeCropper()">
                        
                        <!-- Header Modal -->
                        <div class="px-5 py-4 border-b border-gray-200 flex items-center justify-between bg-slate-50">
                            <div>
                                <h3 class="text-base font-bold text-gray-900 flex items-center gap-2">
                                    <span>✂️ Potong & Sesuaikan Foto</span>
                                </h3>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    Geser, perbesar, atau putar gambar agar pas dengan bingkai kartu warta berita.
                                </p>
                            </div>
                            <button type="button" @click="closeCropper()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-200 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Canvas Gambar Cropper -->
                        <div class="p-3 sm:p-4 bg-slate-900 flex-1 overflow-hidden flex items-center justify-center min-h-[260px] max-h-[55vh]">
                            <div class="w-full h-full max-h-[50vh] flex items-center justify-center">
                                <img x-ref="cropperImage" src="" alt="Cropper Workspace" class="max-w-full max-h-full block">
                            </div>
                        </div>

                        <!-- Kontrol Crop Toolbar -->
                        <div class="p-4 bg-white border-t border-gray-200 space-y-3">
                            <!-- Baris 1: Pilihan Aspek Rasio -->
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-gray-700 mr-1">Rasio:</span>
                                    <button type="button" @click="setAspectRatio(16/9)"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition"
                                            :class="currentAspectRatio === (16/9) ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100'">
                                        16:9 (Warta)
                                    </button>
                                    <button type="button" @click="setAspectRatio(4/3)"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition"
                                            :class="currentAspectRatio === (4/3) ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100'">
                                        4:3
                                    </button>
                                    <button type="button" @click="setAspectRatio(1)"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition"
                                            :class="currentAspectRatio === 1 ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100'">
                                        1:1 (Persegi)
                                    </button>
                                    <button type="button" @click="setAspectRatio(NaN)"
                                            class="px-2.5 py-1 text-xs font-semibold rounded-lg border transition"
                                            :class="isNaN(currentAspectRatio) ? 'bg-emerald-600 text-white border-emerald-600' : 'bg-gray-50 text-gray-700 border-gray-300 hover:bg-gray-100'">
                                        Bebas
                                    </button>
                                </div>

                                <!-- Alat Putar & Zoom -->
                                <div class="flex items-center gap-1">
                                    <button type="button" @click="zoom(0.1)" title="Perbesar" class="p-1.5 rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                    <button type="button" @click="zoom(-0.1)" title="Perkecil" class="p-1.5 rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <button type="button" @click="rotate(-90)" title="Putar Kiri 90°" class="p-1.5 rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a5 5 0 015 5v2m-15-7l4-4m-4 4l4 4"/></svg>
                                    </button>
                                    <button type="button" @click="rotate(90)" title="Putar Kanan 90°" class="p-1.5 rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 10H11a5 5 0 00-5 5v2m15-7l-4-4m4 4l-4 4"/></svg>
                                    </button>
                                    <button type="button" @click="resetCrop()" title="Kembalikan Awal" class="px-2.5 py-1 text-xs font-semibold rounded-lg border border-gray-300 hover:bg-gray-100 text-gray-700">
                                        Reset
                                    </button>
                                </div>
                            </div>

                            <!-- Baris 2: Tombol Batal & Terapkan -->
                            <div class="pt-2 border-t border-gray-100 flex items-center justify-end gap-2.5">
                                <button type="button" @click="closeCropper()"
                                        class="px-4 py-2 text-xs font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition shadow-2xs">
                                    Batal
                                </button>
                                <button type="button" @click="applyCrop()"
                                        class="px-5 py-2 text-xs font-bold text-white bg-emerald-700 hover:bg-emerald-800 rounded-xl shadow-2xs transition flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    <span>Terapkan Potongan</span>
                                </button>
                            </div>
                        </div>
                    </div>
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
                <a href="{{ route('admin.pengumuman.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 transition shadow-2xs">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-emerald-700 hover:bg-emerald-800 shadow-sm transition">
                    Perbarui Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('vendor/cropperjs/cropper.min.js') }}"></script>
<script>
function pengumumanEditCropper(config = {}) {
    return {
        previewUrl: config.initialPreview || null,
        originalImageSrc: null,
        cropperModalOpen: false,
        cropper: null,
        currentAspectRatio: 16 / 9,
        fileName: '',
        fileSizeText: '',
        isNewFile: false,

        onFileSelect(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                alert('Silakan pilih berkas gambar (JPG, PNG, atau WEBP).');
                return;
            }

            this.fileName = file.name;
            const reader = new FileReader();
            reader.onload = (event) => {
                this.originalImageSrc = event.target.result;
                this.openCropper();
            };
            reader.readAsDataURL(file);
        },

        openCropper() {
            if (!this.originalImageSrc) return;
            this.cropperModalOpen = true;
            this.$nextTick(() => {
                const imgElement = this.$refs.cropperImage;
                if (!imgElement) return;

                if (this.cropper) {
                    this.cropper.destroy();
                }

                imgElement.src = this.originalImageSrc;
                this.cropper = new Cropper(imgElement, {
                    aspectRatio: this.currentAspectRatio,
                    viewMode: 1,
                    dragMode: 'move',
                    autoCropArea: 0.95,
                    responsive: true,
                    restore: false,
                    guides: true,
                    center: true,
                    highlight: false,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                });
            });
        },

        setAspectRatio(ratio) {
            this.currentAspectRatio = ratio;
            if (this.cropper) {
                this.cropper.setAspectRatio(ratio);
            }
        },

        zoom(delta) {
            if (this.cropper) {
                this.cropper.zoom(delta);
            }
        },

        rotate(deg) {
            if (this.cropper) {
                this.cropper.rotate(deg);
            }
        },

        resetCrop() {
            if (this.cropper) {
                this.cropper.reset();
            }
        },

        closeCropper() {
            this.cropperModalOpen = false;
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
        },

        applyCrop() {
            if (!this.cropper) {
                this.closeCropper();
                return;
            }

            const canvas = this.cropper.getCroppedCanvas({
                maxWidth: 1600,
                maxHeight: 1200,
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            if (!canvas) {
                this.closeCropper();
                return;
            }

            canvas.toBlob((blob) => {
                if (!blob) {
                    this.closeCropper();
                    return;
                }

                const baseName = (this.fileName || 'banner').replace(/\.[^/.]+$/, '');
                const finalFile = new File([blob], baseName + '-cropped.jpg', {
                    type: 'image/jpeg',
                    lastModified: Date.now()
                });

                const dt = new DataTransfer();
                dt.items.add(finalFile);
                const fileInput = document.getElementById('gambar');
                if (fileInput) {
                    fileInput.files = dt.files;
                }

                const hapusGambarInput = document.getElementById('hapus_gambar');
                if (hapusGambarInput) {
                    hapusGambarInput.value = '0';
                }

                this.isNewFile = true;
                this.previewUrl = canvas.toDataURL('image/jpeg', 0.9);
                this.fileSizeText = (blob.size / 1024).toFixed(1) + ' KB';
                this.closeCropper();
            }, 'image/jpeg', 0.88);
        },

        removeImage() {
            this.previewUrl = null;
            this.originalImageSrc = null;
            this.fileSizeText = '';
            this.isNewFile = false;
            const fileInput = document.getElementById('gambar');
            if (fileInput) {
                fileInput.value = '';
            }
            const hapusGambarInput = document.getElementById('hapus_gambar');
            if (hapusGambarInput) {
                hapusGambarInput.value = '1';
            }
        }
    };
}
</script>
@endpush
