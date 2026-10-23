<x-app-layout>
    @section('title', 'Ubah Data Santri: ' . $santri->nama)

    <div class="space-y-6 max-w-5xl mx-auto pb-10" x-data="{
        fotoPreview: '{{ $santri->foto && Storage::disk('public')->exists($santri->foto) ? asset('storage/' . $santri->foto) : '' }}',
        handleFileSelect(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.fotoPreview = e.target.result; };
                reader.readAsDataURL(file);
            }
        }
    }">
        <!-- Top Navigation & Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <a href="{{ route('admin.santri.index') }}" class="hover:text-emerald-700 transition">Santri</a>
                    <span>/</span>
                    <a href="{{ route('admin.santri.show', $santri->id) }}" class="hover:text-emerald-700 transition">{{ $santri->nama }}</a>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Ubah Data</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Perbarui Biodata Santri</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">NIS: <span class="font-mono font-bold text-emerald-800">{{ $santri->nis }}</span> &bull; {{ $santri->nama }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.santri.show', $santri->id) }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>Lihat Biodata</span>
                </a>
                <a href="{{ route('admin.santri.index') }}"
                   class="inline-flex items-center gap-1.5 px-3.5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl transition">
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Form Card Container -->
        <div class="bg-white rounded-2xl border border-gray-200/90 shadow-sm overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-emerald-50/60 via-white to-emerald-50/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold shadow-md shadow-emerald-700/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Formulir Pembaruan Data Santri</h2>
                        <p class="text-xs text-gray-500">Pastikan data induk telah sesuai dengan dokumen resmi santri.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.santri.update', $santri->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-8">
                @csrf
                @method('PUT')

                <!-- 1. DATA POKOK -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center justify-center">1</span>
                        <h3 class="text-xs sm:text-sm font-bold text-gray-900 uppercase tracking-wider">Identitas Pokok Santri</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <!-- NIS -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                NIS (Nomor Induk Santri) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nis" value="{{ old('nis', $santri->nis) }}" required
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-mono font-bold text-gray-900">
                            <x-input-error :messages="$errors->get('nis')" class="mt-1.5" />
                        </div>

                        <!-- Nama Lengkap -->
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Nama Lengkap Santri <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="nama" value="{{ old('nama', $santri->nama) }}" required
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs text-gray-900 font-semibold">
                            <x-input-error :messages="$errors->get('nama')" class="mt-1.5" />
                        </div>

                        <!-- Tempat Lahir -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tempat Lahir</label>
                            <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir', $santri->tempat_lahir) }}"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                        </div>

                        <!-- Tanggal Lahir -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir', $santri->tanggal_lahir ? $santri->tanggal_lahir->format('Y-m-d') : '') }}"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                        </div>

                        <!-- Status Santri -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                                Status Keberadaan <span class="text-red-500">*</span>
                            </label>
                            <select name="status" required class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-medium">
                                <option value="aktif" {{ old('status', $santri->status) == 'aktif' ? 'selected' : '' }}>Aktif Menuntut Ilmu</option>
                                <option value="lulus" {{ old('status', $santri->status) == 'lulus' ? 'selected' : '' }}>Lulus / Alumni</option>
                                <option value="keluar" {{ old('status', $santri->status) == 'keluar' ? 'selected' : '' }}>Keluar / Berhenti</option>
                                <option value="pindah" {{ old('status', $santri->status) == 'pindah' ? 'selected' : '' }}>Pindah Pesantren</option>
                            </select>
                        </div>

                        <!-- Alamat Asal -->
                        <div class="sm:col-span-2 lg:col-span-3">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alamat Asal / Domisili Lengkap</label>
                            <textarea name="alamat" rows="2"
                                      class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">{{ old('alamat', $santri->alamat) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. PENEMPATAN & AKADEMIK -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center justify-center">2</span>
                        <h3 class="text-xs sm:text-sm font-bold text-gray-900 uppercase tracking-wider">Penempatan Asrama & Kelas</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <!-- Kamar / Asrama -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kamar / Asrama</label>
                            <input type="text" name="kamar" value="{{ old('kamar', $santri->kamar) }}"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                        </div>

                        <!-- Kelas / Jenjang -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Kelas / Jenjang</label>
                            <input type="text" name="kelas" value="{{ old('kelas', $santri->kelas) }}"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                        </div>

                        <!-- Tahun Masuk -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tahun Masuk</label>
                            <input type="text" name="tahun_masuk" value="{{ old('tahun_masuk', $santri->tahun_masuk) }}"
                                   class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs font-mono font-bold">
                        </div>
                    </div>
                </div>

                <!-- 3. DATA WALI & FOTO SANTRI -->
                <div class="space-y-4">
                    <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-800 text-xs font-bold flex items-center justify-center">3</span>
                        <h3 class="text-xs sm:text-sm font-bold text-gray-900 uppercase tracking-wider">Data Wali & Foto Santri</h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Kolom Data Wali -->
                        <div class="lg:col-span-2 space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nama Orang Tua / Wali</label>
                                <input type="text" name="nama_wali" value="{{ old('nama_wali', $santri->nama_wali) }}"
                                       class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Nomor HP / WhatsApp Wali</label>
                                <div class="relative">
                                    <input type="text" name="hp_wali" value="{{ old('hp_wali', $santri->hp_wali) }}"
                                           class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-xs pl-10 font-mono">
                                    <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                                <p class="text-[11px] text-gray-400 mt-1">Digunakan untuk keperluan administrasi dan pemberitahuan pondok.</p>
                            </div>
                        </div>

                        <!-- Kolom Upload & Preview Foto -->
                        <div class="bg-gray-50/80 rounded-2xl p-5 border border-gray-200/80 flex flex-col items-center justify-center text-center">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-3">Pasfoto Santri</label>
                            
                            <!-- Box Preview -->
                            <div class="w-28 h-36 rounded-xl border-2 border-dashed border-gray-300 bg-white flex items-center justify-center overflow-hidden mb-3 shadow-2xs relative">
                                <template x-if="fotoPreview">
                                    <img :src="fotoPreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!fotoPreview">
                                    <div class="text-gray-400 flex flex-col items-center p-2">
                                        <svg class="w-8 h-8 text-gray-300 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span class="text-[10px] font-medium text-gray-400">Belum ada foto</span>
                                    </div>
                                </template>
                            </div>

                            <input type="file" name="foto" id="foto_santri_edit" accept="image/*" @change="handleFileSelect"
                                   class="hidden">
                            <label for="foto_santri_edit"
                                   class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 hover:bg-gray-100 text-gray-700 rounded-lg text-xs font-bold transition shadow-2xs">
                                <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                <span>Ganti Pasfoto</span>
                            </label>
                            <p class="text-[10px] text-gray-400 mt-2">Kosongkan jika tidak mengganti foto.</p>
                            <x-input-error :messages="$errors->get('foto')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- TOMBOL SIMPAN / BATAL (Sesuai Aturan Warna Desain) -->
                <div class="pt-6 border-t border-gray-100 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <a href="{{ route('admin.santri.index') }}"
                       class="px-5 py-2.5 text-xs sm:text-sm font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition text-center shadow-xs active:scale-95">
                        Batalkan
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-emerald-700/20 active:scale-95 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
