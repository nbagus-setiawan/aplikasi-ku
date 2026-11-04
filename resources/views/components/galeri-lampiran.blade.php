@props([
    'model',
    'type', // 'surat-masuk', 'surat-keluar', 'arsip'
    'title' => 'Berkas & Lampiran Dokumen',
])

@php
    $lampirans = $model->lampiranBerkas ?? collect();
    
    // Kumpulkan juga berkas utama lama yang ada pada record
    $berkasUtamas = [];
    if ($type === 'surat-masuk') {
        if ($model->file_surat_asli) {
            $berkasUtamas[] = [
                'nama' => 'Naskah Surat Masuk Asli',
                'path' => $model->file_surat_asli,
                'is_legacy' => true,
                'label' => 'Surat Masuk Asli',
            ];
        }
        if ($model->file_lampiran) {
            $berkasUtamas[] = [
                'nama' => 'Lampiran Fisik Surat',
                'path' => $model->file_lampiran,
                'is_legacy' => true,
                'label' => 'Lampiran Surat',
            ];
        } elseif ($model->file && $model->file !== $model->file_surat_asli) {
            $berkasUtamas[] = [
                'nama' => 'Berkas Pindaian / Tampilan',
                'path' => $model->file,
                'is_legacy' => true,
                'label' => 'Pindaian Surat',
            ];
        }
    } elseif ($type === 'surat-keluar') {
        if ($model->file_final) {
            $berkasUtamas[] = [
                'nama' => 'Naskah Final Ber-QR Code',
                'path' => $model->file_final,
                'is_legacy' => true,
                'label' => 'Dokumen Final',
            ];
        }
        if ($model->file_surat_asli) {
            $berkasUtamas[] = [
                'nama' => 'Naskah Dokumen Word (.docx) Asli',
                'path' => $model->file_surat_asli,
                'is_legacy' => true,
                'label' => 'File Word Asli',
            ];
        }
        if ($model->file && $model->file !== $model->file_final && $model->file !== $model->file_surat_asli) {
            $berkasUtamas[] = [
                'nama' => 'Lampiran Tambahan Surat',
                'path' => $model->file,
                'is_legacy' => true,
                'label' => 'Lampiran',
            ];
        }
    } elseif ($type === 'arsip') {
        if ($model->file) {
            $berkasUtamas[] = [
                'nama' => 'Dokumen Berkas Utama',
                'path' => $model->file,
                'is_legacy' => true,
                'label' => 'Berkas Arsip',
            ];
        }
    }

    $totalBerkas = count($berkasUtamas) + $lampirans->count();
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden" x-data="galeriLampiranHandler()">
    <!-- Header Galeri -->
    <div class="px-6 py-4 bg-gradient-to-r from-slate-50 to-emerald-50/30 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </div>
            <div>
                <h3 class="text-base font-bold text-slate-800">{{ $title }}</h3>
                <p class="text-xs text-slate-500">
                    Total <span class="font-bold text-emerald-700" id="counter-total-lampiran">{{ $totalBerkas }}</span> berkas terlampir pada dokumen ini
                </p>
            </div>
        </div>

        <div class="flex items-center space-x-2">
            @if($totalBerkas > 0)
                <a href="{{ route('admin.dokumen-lampiran.unduh-semua', ['type' => $type, 'id' => $model->id]) }}" 
                   title="Unduh semua berkas dalam 1 arsip ZIP"
                   class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-emerald-700 hover:border-emerald-300 shadow-sm transition-all">
                    <svg class="w-4 h-4 mr-1.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Unduh Semua (ZIP)
                </a>
            @endif

            <button type="button" @click="showUploadModal = true"
                    class="inline-flex items-center px-3.5 py-2 text-xs font-bold text-white bg-emerald-600 rounded-xl hover:bg-emerald-700 shadow-sm hover:shadow transition-all">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Berkas
            </button>
        </div>
    </div>

    <!-- Konten Galeri / Daftar Berkas -->
    <div class="p-6">
        @if($totalBerkas === 0)
            <div class="text-center py-8 px-4 border-2 border-dashed border-slate-200 rounded-2xl bg-slate-50/50" id="empty-state-lampiran">
                <div class="w-12 h-12 mx-auto rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-700">Belum Ada Berkas Lampiran</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">
                    Anda dapat mengunggah satu atau banyak berkas fisik (PDF, Word, atau Gambar) sekaligus untuk melengkapi dokumen ini.
                </p>
                <button type="button" @click="showUploadModal = true"
                        class="mt-3.5 inline-flex items-center px-3.5 py-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg hover:bg-emerald-100 transition-colors">
                    + Unggah Berkas Sekarang
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3" id="daftar-lampiran-container">
                {{-- 1. Tampilkan Berkas Utama / Dokumen Fisik Awal --}}
                @foreach($berkasUtamas as $bu)
                    @php
                        $ext = strtolower(pathinfo($bu['path'], PATHINFO_EXTENSION));
                        $isPdf = ($ext === 'pdf');
                        $isWord = in_array($ext, ['doc', 'docx']);
                        $isImg = in_array($ext, ['jpg', 'jpeg', 'png', 'webp']);
                        $url = asset('storage/' . $bu['path']);
                    @endphp
                    <div class="flex items-center justify-between p-3.5 rounded-xl border border-emerald-100 bg-emerald-50/30 hover:bg-emerald-50/60 transition-colors group">
                        <div class="flex items-center space-x-3 min-w-0 pr-2">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 
                                {{ $isPdf ? 'bg-rose-100 text-rose-700' : ($isWord ? 'bg-blue-100 text-blue-700' : ($isImg ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700')) }}">
                                @if($isPdf)
                                    <span class="text-[10px] font-black uppercase">PDF</span>
                                @elseif($isWord)
                                    <span class="text-[10px] font-black uppercase">DOCX</span>
                                @elseif($isImg)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-emerald-600 text-white">Utama</span>
                                    <span class="text-xs font-bold text-slate-800 truncate" title="{{ $bu['nama'] }}">{{ $bu['nama'] }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ basename($bu['path']) }}</p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-1 shrink-0">
                            @if($isPdf || $isImg)
                                <button type="button" @click="openPreview('{{ $url }}', '{{ $bu['nama'] }}', '{{ $ext }}')"
                                        title="Pratinjau langsung"
                                        class="p-2 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-white border border-transparent hover:border-slate-200 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            @endif

                            <a href="{{ $url }}" download="{{ $bu['nama'] . '.' . $ext }}"
                               title="Unduh berkas"
                               class="p-2 rounded-lg text-slate-600 hover:text-blue-700 hover:bg-white border border-transparent hover:border-slate-200 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    </div>
                @endforeach

                {{-- 2. Tampilkan Berkas Lampiran Multi-File dari tabel dokumen_lampirans --}}
                @foreach($lampirans as $lamp)
                    @php
                        $ext = $lamp->ekstensi;
                        $isPdf = $lamp->isPdf();
                        $isWord = $lamp->isWord();
                        $isImg = $lamp->isImage();
                        $badge = $lamp->tipe_badge;
                    @endphp
                    <div id="lampiran-row-{{ $lamp->id }}" class="flex items-center justify-between p-3.5 rounded-xl border border-slate-200 bg-white hover:border-emerald-300 hover:shadow-sm transition-all group">
                        <div class="flex items-center space-x-3 min-w-0 pr-2">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center shrink-0 
                                {{ $isPdf ? 'bg-rose-100 text-rose-700' : ($isWord ? 'bg-blue-100 text-blue-700' : ($isImg ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700')) }}">
                                @if($isPdf)
                                    <span class="text-[10px] font-black uppercase">PDF</span>
                                @elseif($isWord)
                                    <span class="text-[10px] font-black uppercase">DOCX</span>
                                @elseif($isImg)
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                @endif
                            </div>
                            <div class="min-w-0">
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-[10px] font-bold px-1.5 py-0.5 rounded {{ $badge['class'] }}">{{ $badge['label'] }}</span>
                                    <span class="text-xs font-bold text-slate-800 truncate" title="{{ $lamp->nama_berkas }}">{{ $lamp->nama_berkas }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ $lamp->ukuran_formatted }}
                                    @if($lamp->keterangan)
                                        • <span class="italic">{{ $lamp->keterangan }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center space-x-1 shrink-0">
                            @if($isPdf || $isImg)
                                <button type="button" @click="openPreview('{{ $lamp->url }}', '{{ addslashes($lamp->nama_berkas) }}', '{{ $ext }}')"
                                        title="Pratinjau langsung"
                                        class="p-2 rounded-lg text-slate-600 hover:text-emerald-700 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                            @endif

                            <a href="{{ route('admin.dokumen-lampiran.unduh', $lamp) }}" 
                               title="Unduh berkas lampiran ini"
                               class="p-2 rounded-lg text-slate-600 hover:text-blue-700 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>

                            <button type="button" 
                                    @click="konfirmasiHapusLampiran('{{ route('admin.dokumen-lampiran.destroy', $lamp) }}', '{{ addslashes($lamp->nama_berkas) }}', 'lampiran-row-{{ $lamp->id }}')"
                                    title="Hapus berkas lampiran ini"
                                    class="p-2 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 border border-transparent hover:border-rose-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modal Upload Berkas Lampiran Tambahan Langsung -->
    <div x-show="showUploadModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden"
             @click.away="if(!isUploading) showUploadModal = false">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 flex items-center justify-between">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Unggah Lampiran Berkas Baru</h3>
                </div>
                <button type="button" @click="if(!isUploading) showUploadModal = false" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <form action="{{ route('admin.dokumen-lampiran.store', ['type' => $type, 'id' => $model->id]) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  @submit.prevent="submitUploadForm($event)"
                  class="p-6 space-y-4">
                @csrf

                <x-multi-file-upload 
                    name="lampiran_berkas[]" 
                    id="modal_lampiran_berkas_input"
                    label="Pilih Berkas yang Ingin Ditambahkan"
                    hint="Bisa pilih banyak berkas sekaligus (PDF, Word, atau Gambar). Maks 10 MB per berkas."
                    :required="true" />

                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Catatan Tambahan (Opsional)</label>
                    <input type="text" name="keterangan" placeholder="Contoh: Bukti Transfer / Lampiran Naskah Lampiran 2"
                           class="w-full text-xs rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm" />
                </div>

                <div class="pt-2 flex items-center justify-end space-x-2 border-t border-slate-100">
                    <button type="button" @click="showUploadModal = false" :disabled="isUploading"
                            class="px-4 py-2 text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                        Batal
                    </button>
                    <button type="submit" :disabled="isUploading"
                            class="inline-flex items-center px-4 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm transition-all disabled:opacity-50">
                        <template x-if="isUploading">
                            <span class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Sedang Mengunggah...
                            </span>
                        </template>
                        <template x-if="!isUploading">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Mulai Unggah Berkas
                            </span>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Pratinjau Dokumen Universal -->
    <div x-show="showPreviewModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/75 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <div class="relative w-full max-w-4xl h-[88vh] bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col"
             @click.away="showPreviewModal = false">
            
            <div class="px-5 py-3.5 bg-slate-900 text-white flex items-center justify-between shrink-0">
                <div class="flex items-center space-x-2.5 truncate pr-4">
                    <span class="text-xs font-bold px-2 py-0.5 rounded bg-emerald-500 text-white uppercase" x-text="previewExt"></span>
                    <span class="text-sm font-semibold truncate text-slate-100" x-text="previewTitle"></span>
                </div>
                <div class="flex items-center space-x-2 shrink-0">
                    <a :href="previewUrl" target="_blank" download
                       class="px-3 py-1.5 text-xs font-bold bg-white/10 hover:bg-white/20 rounded-lg text-white transition-colors">
                        Buka Tab Baru / Unduh
                    </a>
                    <button type="button" @click="showPreviewModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <div class="flex-1 bg-slate-100 overflow-auto flex items-center justify-center p-2">
                <template x-if="isPdfPreview">
                    <iframe :src="previewUrl" class="w-full h-full rounded-lg border-0 bg-white" title="Pratinjau PDF"></iframe>
                </template>
                <template x-if="isImagePreview">
                    <div class="max-w-full max-h-full overflow-auto p-4 flex items-center justify-center">
                        <img :src="previewUrl" :alt="previewTitle" class="max-w-full max-h-[80vh] object-contain rounded-lg shadow-md bg-white border border-slate-200" />
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>

@once
<script>
    function galeriLampiranHandler() {
        return {
            showUploadModal: false,
            showPreviewModal: false,
            isUploading: false,
            previewUrl: '',
            previewTitle: '',
            previewExt: '',
            get isPdfPreview() {
                return (this.previewExt || '').toLowerCase() === 'pdf';
            },
            get isImagePreview() {
                return ['jpg', 'jpeg', 'png', 'webp'].includes((this.previewExt || '').toLowerCase());
            },
            openPreview(url, title, ext) {
                this.previewUrl = url;
                this.previewTitle = title;
                this.previewExt = ext;
                this.showPreviewModal = true;
            },
            submitUploadForm(event) {
                const form = event.target;
                const formData = new FormData(form);
                this.isUploading = true;

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    this.isUploading = false;
                    if (data.success) {
                        this.showUploadModal = false;
                        if (window.sapToast) {
                            window.sapToast(data.message || 'Berkas lampiran berhasil diunggah.', 'success');
                        }
                        // Refresh halaman untuk memperbarui galeri dan tautan zip
                        setTimeout(() => window.location.reload(), 600);
                    } else {
                        if (window.sapToast) {
                            window.sapToast(data.message || 'Gagal mengunggah berkas.', 'error');
                        }
                    }
                })
                .catch(err => {
                    this.isUploading = false;
                    // Jika fallback form normal submit
                    form.submit();
                });
            },
            konfirmasiHapusLampiran(url, namaBerkas, rowId) {
                // Terapkan modal konfirmasi hapus universal (Psikologi Warna Merah Tegas & Abu-abu Netral)
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 z-[999999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm animate-in fade-in duration-200';
                modal.innerHTML = `
                    <div class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all p-6 text-center">
                        <div class="w-14 h-14 mx-auto mb-4 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-800">Hapus Berkas Lampiran?</h3>
                        <p class="text-sm text-slate-600 mt-2">
                            Berkas <span class="font-bold text-slate-900">"${namaBerkas}"</span> akan dihapus permanen dari sistem.
                        </p>
                        <div class="mt-6 flex items-center justify-center space-x-3">
                            <button type="button" id="btn-batal-lampiran" class="px-5 py-2.5 text-sm font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-colors">
                                Batal
                            </button>
                            <button type="button" id="btn-hapus-lampiran" class="px-5 py-2.5 text-sm font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-sm transition-all flex items-center">
                                Ya, Hapus Berkas
                            </button>
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);

                const closeModal = () => modal.remove();
                modal.querySelector('#btn-batal-lampiran').onclick = closeModal;

                modal.querySelector('#btn-hapus-lampiran').onclick = function() {
                    const btn = this;
                    btn.disabled = true;
                    btn.innerHTML = `<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menghapus...`;

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        closeModal();
                        if (data.success) {
                            const row = document.getElementById(rowId);
                            if (row) {
                                row.classList.add('transition-all', 'duration-300', 'opacity-0', 'scale-95');
                                setTimeout(() => {
                                    row.remove();
                                    const counter = document.getElementById('counter-total-lampiran');
                                    if (counter) {
                                        let current = parseInt(counter.textContent) || 1;
                                        counter.textContent = Math.max(0, current - 1);
                                    }
                                }, 300);
                            }
                            if (window.sapToast) {
                                window.sapToast(data.message || 'Berkas lampiran berhasil dihapus.', 'success');
                            }
                        } else {
                            if (window.sapToast) {
                                window.sapToast(data.message || 'Gagal menghapus berkas lampiran.', 'error');
                            }
                        }
                    })
                    .catch(err => {
                        closeModal();
                        if (window.sapToast) {
                            window.sapToast('Terjadi kesalahan saat menghapus berkas.', 'error');
                        }
                    });
                };
            }
        };
    }
</script>
@endonce
