<x-app-layout>
    @section('title', 'Tambah Agenda Kegiatan')

    <div class="max-w-4xl mx-auto space-y-6 pb-12">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <a href="{{ route('admin.agenda.index') }}" class="hover:text-emerald-700 transition">Agenda Kegiatan</a>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold">Tambah Agenda Baru</span>
                </nav>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Tambah Agenda Kegiatan Baru</h1>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Catat jadwal kegiatan pondok pesantren, penanggung jawab, dan lokasi acara.</p>
            </div>
            <div>
                <a href="{{ route('admin.agenda.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl shadow-xs transition active:scale-95">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>Kembali ke Daftar</span>
                </a>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="p-6 sm:p-8 border-b border-gray-100 bg-gradient-to-r from-emerald-50/60 via-white to-emerald-50/20">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-bold shadow-md shadow-emerald-700/20">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Formulir Jadwal Kegiatan</h2>
                        <p class="text-xs text-gray-500">Tanda bintang merah (<span class="text-red-500 font-bold">*</span>) wajib diisi dengan benar.</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('admin.agenda.store') }}" class="p-6 sm:p-8 space-y-6">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Judul Agenda -->
                    <div class="sm:col-span-2">
                        <label for="judul" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Judul Agenda Kegiatan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="judul" id="judul" value="{{ old('judul') }}" required
                               placeholder="Contoh: Rapat Koordinasi Pengurus Awal Semester"
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-semibold text-gray-900" />
                        @error('judul')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Waktu Mulai -->
                    <div>
                        <label for="mulai" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Waktu Mulai <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="mulai" id="mulai" value="{{ old('mulai') }}" required
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs" />
                        @error('mulai')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Waktu Selesai -->
                    <div>
                        <label for="selesai" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Waktu Selesai <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" name="selesai" id="selesai" value="{{ old('selesai') }}" required
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs" />
                        @error('selesai')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Lokasi -->
                    <div>
                        <label for="lokasi" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Lokasi / Tempat Acara <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="lokasi" id="lokasi" value="{{ old('lokasi') }}" required
                               placeholder="Contoh: Aula Utama Putra / Masjid Jami"
                               class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs" />
                        @error('lokasi')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Penanggung Jawab (PJ) -->
                    <div>
                        <label for="pj_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Penanggung Jawab (PJ Pengurus)
                        </label>
                        <select name="pj_id" id="pj_id"
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-medium">
                            <option value="">-- Pilih Penanggung Jawab (Opsional) --</option>
                            @foreach ($pengurusList as $pengurus)
                                <option value="{{ $pengurus->id }}" {{ old('pj_id') == $pengurus->id ? 'selected' : '' }}>
                                    {{ $pengurus->nama }} ({{ $pengurus->jabatan }})
                                </option>
                            @endforeach
                        </select>
                        @error('pj_id')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div class="sm:col-span-2">
                        <label for="status" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Status Agenda <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required
                                class="w-full sm:w-64 text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs font-medium">
                            <option value="rencana" {{ old('status', 'rencana') === 'rencana' ? 'selected' : '' }}>Rencana</option>
                            <option value="berjalan" {{ old('status') === 'berjalan' ? 'selected' : '' }}>Sedang Berjalan</option>
                            <option value="selesai" {{ old('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="batal" {{ old('status') === 'batal' ? 'selected' : '' }}>Batal</option>
                        </select>
                        @error('status')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div class="sm:col-span-2">
                        <label for="deskripsi" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                            Keterangan / Deskripsi Kegiatan
                        </label>
                        <textarea name="deskripsi" id="deskripsi" rows="4"
                                  placeholder="Rincian susunan acara, catatan logistik, atau peserta kegiatan..."
                                  class="w-full text-sm rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 shadow-2xs leading-relaxed">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-6 border-t border-gray-100">
                    <a href="{{ route('admin.agenda.index') }}"
                       class="px-5 py-2.5 text-xs sm:text-sm font-bold text-rose-700 hover:text-rose-800 bg-rose-50 hover:bg-rose-100 border border-rose-300 rounded-xl transition text-center shadow-2xs">
                        Batal
                    </a>
                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md shadow-emerald-700/20 hover:scale-[1.01] active:scale-[0.98] transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Simpan Agenda Kegiatan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
