@props([
    'name' => 'lampiran_berkas[]',
    'label' => 'Unggah Berkas / Lampiran Dokumen (Multi-File)',
    'hint' => 'Bisa pilih banyak berkas sekaligus (Tahan Ctrl / Shift saat memilih). Format: PDF, Word (.docx), Gambar (.jpg, .png). Maks 10 MB per berkas.',
    'required' => false,
])

<div class="space-y-2" x-data="{
    isDragging: false,
    selectedFiles: [],
    handleFiles(fileList) {
        if (!fileList || fileList.length === 0) return;
        this.selectedFiles = Array.from(fileList);
    },
    handleDrop(event) {
        this.isDragging = false;
        const dt = event.dataTransfer;
        if (!dt || !dt.files || dt.files.length === 0) return;
        if (this.$refs.fileInput) {
            this.$refs.fileInput.files = dt.files;
        }
        this.selectedFiles = Array.from(dt.files);
    },
    clearFiles() {
        this.selectedFiles = [];
        if (this.$refs.fileInput) {
            this.$refs.fileInput.value = '';
        }
    },
    formatSize(bytes) {
        if (!bytes) return '0 B';
        if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
        if (bytes >= 1024) return Math.round(bytes / 1024) + ' KB';
        return bytes + ' B';
    },
    getExtBadge(name) {
        const ext = (name.split('.').pop() || '').toLowerCase();
        if (ext === 'pdf') return 'PDF';
        if (ext === 'doc' || ext === 'docx') return 'DOCX';
        if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) return 'GAMBAR';
        return ext.toUpperCase() || 'FILE';
    },
    getBadgeClass(name) {
        const ext = (name.split('.').pop() || '').toLowerCase();
        if (ext === 'pdf') return 'bg-rose-100 text-rose-700';
        if (ext === 'doc' || ext === 'docx') return 'bg-blue-100 text-blue-700';
        if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) return 'bg-emerald-100 text-emerald-700';
        return 'bg-slate-200 text-slate-700';
    }
}">
    <label class="block text-sm font-semibold text-slate-700">
        {{ $label }}
        @if($required) <span class="text-rose-500">*</span> @endif
    </label>

    <div 
        @dragover.prevent="isDragging = true"
        @dragleave.prevent="isDragging = false"
        @drop.prevent="handleDrop($event)"
        :class="isDragging ? 'border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-400/30' : 'border-slate-300 hover:border-emerald-400 bg-slate-50/50 hover:bg-white'"
        class="relative border-2 border-dashed rounded-xl p-5 transition-all cursor-pointer text-center group">
        
        <input 
            type="file" 
            name="{{ $name }}" 
            x-ref="fileInput"
            multiple 
            @if($required) required @endif
            accept=".pdf,.docx,.doc,.jpg,.jpeg,.png,.webp"
            @change="handleFiles($event.target.files)"
            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />

        <div class="flex flex-col items-center justify-center space-y-2 pointer-events-none">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-slate-800">
                    <span class="text-emerald-700 underline font-bold">Pilih Berkas</span> atau Seret File ke Sini
                </p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $hint }}</p>
            </div>
        </div>
    </div>

    <!-- Pratinjau Daftar Berkas yang Terpilih -->
    <template x-if="selectedFiles.length > 0">
        <div class="mt-3 bg-white border border-slate-200 rounded-xl p-3 shadow-sm">
            <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 text-xs font-semibold text-slate-600">
                <span class="font-bold text-emerald-800" x-text="'✓ ' + selectedFiles.length + ' berkas dipilih siap diunggah'"></span>
                <button type="button" @click="clearFiles()" class="text-rose-600 hover:text-rose-800 font-bold hover:underline">
                    Reset Pilihan
                </button>
            </div>
            <ul class="space-y-1.5 max-h-48 overflow-y-auto pr-1">
                <template x-for="(f, idx) in selectedFiles" :key="idx">
                    <li class="flex items-center justify-between py-1.5 px-2.5 rounded-lg bg-slate-50 hover:bg-emerald-50/50 border border-slate-150 text-xs text-slate-700 transition-colors">
                        <div class="flex items-center space-x-2 truncate">
                            <span class="px-1.5 py-0.5 text-[10px] font-bold rounded" :class="getBadgeClass(f.name)" x-text="getExtBadge(f.name)"></span>
                            <span class="truncate font-medium text-slate-800" x-text="f.name"></span>
                            <span class="text-[11px] text-slate-600 font-medium" x-text="'(' + formatSize(f.size) + ')'"></span>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
    </template>
</div>
