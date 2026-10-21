@props([
    'kategoriList' => collect(),
    'selectedId' => null,
    'name' => 'kategori_id',
    'required' => false,
    'label' => 'Jenis / Kategori Surat',
    'helperText' => 'Kode jenis surat menentukan format penomoran resmi pondok pesantren.'
])

@php
    $initialCategories = $kategoriList->map(function($k) {
        return [
            'id' => (string) $k->id,
            'nama' => $k->nama,
            'kode' => $k->kode,
            'keterangan' => $k->keterangan ?? '',
        ];
    })->values();

    $selectedCategory = $kategoriList->firstWhere('id', $selectedId);
@endphp

<div
    x-data="{
        name: '{{ $name }}',
        required: {{ $required ? 'true' : 'false' }},
        categories: {{ Js::from($initialCategories) }},
        selectedId: '{{ $selectedId ? (string)$selectedId : '' }}',
        search: '',
        openDropdown: false,
        modalTambah: false,
        formTambah: {
            nama: '',
            kode: '',
            keterangan: '',
            loading: false,
            error: ''
        },
        get selectedCategory() {
            return this.categories.find(c => String(c.id) === String(this.selectedId)) || null;
        },
        get filteredCategories() {
            if (!this.search.trim()) return this.categories;
            const q = this.search.toLowerCase();
            return this.categories.filter(c =>
                c.nama.toLowerCase().includes(q) ||
                c.kode.toLowerCase().includes(q) ||
                (c.keterangan && c.keterangan.toLowerCase().includes(q))
            );
        },
        selectCategory(cat) {
            this.selectedId = cat ? String(cat.id) : '';
            this.openDropdown = false;
            this.search = '';
        },
        bukaModalTambah() {
            this.formTambah.nama = this.search.trim();
            this.formTambah.kode = '';
            this.formTambah.keterangan = '';
            this.formTambah.error = '';
            this.openDropdown = false;
            this.modalTambah = true;
        },
        async submitTambahKategori() {
            if (!this.formTambah.nama.trim() || !this.formTambah.kode.trim()) {
                this.formTambah.error = 'Nama jenis surat dan kode wajib diisi.';
                return;
            }

            this.formTambah.loading = true;
            this.formTambah.error = '';

            try {
                const response = await fetch('{{ route('admin.kategori-surat.store') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        nama: this.formTambah.nama,
                        kode: this.formTambah.kode,
                        keterangan: this.formTambah.keterangan
                    })
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.message || 'Gagal menyimpan jenis surat baru.');
                }

                const newCat = {
                    id: String(data.data.id),
                    nama: data.data.nama,
                    kode: data.data.kode,
                    keterangan: data.data.keterangan || ''
                };

                this.categories.push(newCat);
                this.selectedId = newCat.id;
                this.modalTambah = false;
                this.search = '';
            } catch (err) {
                this.formTambah.error = err.message || 'Terjadi kesalahan saat menyimpan.';
            } finally {
                this.formTambah.loading = false;
            }
        }
    }"
    @click.outside="openDropdown = false"
    class="relative"
>
    <!-- Hidden Input untuk Form Submit Standar -->
    <input type="hidden" :name="name" :value="selectedId" :required="required">

    <!-- Header Label & Tombol Tambah Jenis Baru -->
    <div class="flex items-center justify-between mb-1.5">
        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        <button
            type="button"
            @click="bukaModalTambah()"
            class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 hover:text-emerald-800 bg-emerald-50 hover:bg-emerald-100/80 px-2 py-0.5 rounded-md border border-emerald-200 transition"
            title="Tambah jenis/kategori surat baru ke pangkalan data"
        >
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>+ Tambah Jenis Baru</span>
        </button>
    </div>

    <!-- Tombol Pemilih (Custom Combobox Trigger) -->
    <div
        @click="openDropdown = !openDropdown; if(openDropdown) $nextTick(() => $refs.searchInput.focus())"
        class="w-full min-h-[42px] px-3.5 py-2 bg-white rounded-xl border border-gray-300 hover:border-emerald-500 focus-within:border-emerald-600 focus-within:ring-2 focus-within:ring-emerald-500/20 shadow-xs cursor-pointer flex items-center justify-between transition gap-2"
        :class="{ 'border-emerald-600 ring-2 ring-emerald-500/20': openDropdown }"
    >
        <div class="flex-1 truncate">
            <template x-if="selectedCategory">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-md font-mono" x-text="selectedCategory.kode"></span>
                    <span class="text-sm font-bold text-gray-900" x-text="selectedCategory.nama"></span>
                </div>
            </template>
            <template x-if="!selectedCategory">
                <span class="text-sm text-gray-400 font-medium">Pilih jenis/kategori surat... (atau ketik untuk mencari)</span>
            </template>
        </div>

        <div class="flex items-center gap-1.5 flex-shrink-0">
            <template x-if="selectedCategory && !required">
                <button
                    type="button"
                    @click.stop="selectCategory(null)"
                    class="p-1 text-gray-400 hover:text-gray-600 rounded-full hover:bg-gray-100"
                    title="Kosongkan pilihan"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </template>
            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': openDropdown }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>

    <!-- Petunjuk Keterangan -->
    @if ($helperText)
        <p class="text-[11px] text-gray-400 mt-1">
            <template x-if="selectedCategory && selectedCategory.keterangan">
                <span class="text-emerald-700 font-medium" x-text="'Keterangan: ' + selectedCategory.keterangan"></span>
            </template>
            <template x-if="!selectedCategory || !selectedCategory.keterangan">
                <span>{{ $helperText }}</span>
            </template>
        </p>
    @endif

    <!-- Panel Dropdown List Pencarian (Floating) -->
    <div
        x-show="openDropdown"
        x-cloak
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-98"
        class="absolute left-0 right-0 mt-1.5 bg-white rounded-2xl shadow-xl border border-gray-200 z-50 overflow-hidden"
    >
        <!-- Kotak Pencarian Instan -->
        <div class="p-2.5 bg-gray-50 border-b border-gray-100">
            <div class="relative">
                <input
                    x-ref="searchInput"
                    type="text"
                    x-model="search"
                    @keydown.escape="openDropdown = false"
                    placeholder="Ketik untuk mencari jenis surat (misal: permohonan, tugas, izin)..."
                    class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 pl-8 pr-3 py-2"
                />
                <svg class="w-4 h-4 text-gray-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
        </div>

        <!-- Daftar Opsi Jenis Surat -->
        <div class="max-h-64 overflow-y-auto divide-y divide-gray-50 p-1">
            <template x-for="cat in filteredCategories" :key="cat.id">
                <button
                    type="button"
                    @click="selectCategory(cat)"
                    class="w-full px-3.5 py-2.5 text-left rounded-xl transition flex items-center justify-between gap-3 group"
                    :class="String(selectedId) === String(cat.id) ? 'bg-emerald-50 text-emerald-900 font-bold' : 'hover:bg-gray-50 text-gray-700'"
                >
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded text-[11px] font-bold font-mono transition"
                                  :class="String(selectedId) === String(cat.id) ? 'bg-emerald-200 text-emerald-900' : 'bg-gray-100 text-gray-600 group-hover:bg-emerald-100 group-hover:text-emerald-800'"
                                  x-text="cat.kode"></span>
                            <span class="text-xs sm:text-sm font-semibold truncate" x-text="cat.nama"></span>
                        </div>
                        <template x-if="cat.keterangan">
                            <p class="text-[11px] text-gray-400 truncate mt-0.5" x-text="cat.keterangan"></p>
                        </template>
                    </div>

                    <template x-if="String(selectedId) === String(cat.id)">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                </button>
            </template>

            <!-- Jika Pencarian Tidak Ditemukan -->
            <div x-show="filteredCategories.length === 0" class="p-4 text-center">
                <p class="text-xs text-gray-500">Jenis surat "<span class="font-bold text-gray-700" x-text="search"></span>" belum terdaftar.</p>
                <button
                    type="button"
                    @click="bukaModalTambah()"
                    class="mt-2.5 inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl transition shadow-xs"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>+ Tambah Jenis Ini Sekarang</span>
                </button>
            </div>
        </div>

        <!-- Footer Dropdown: Informasi Jumlah Kategori -->
        <div class="px-3.5 py-2 bg-gray-50/90 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
            <span>Tersedia <strong class="text-gray-600" x-text="categories.length"></strong> jenis surat</span>
            <button type="button" @click="bukaModalTambah()" class="text-emerald-700 font-bold hover:underline">
                + Tambah Jenis Lain
            </button>
        </div>
    </div>

    <!-- Modal Pop-up Tambah Kategori Baru Cepat (AJAX Tanpa Pindah Halaman) -->
    <div
        x-show="modalTambah"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto"
        role="dialog"
        aria-modal="true"
    >
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div
                x-show="modalTambah"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
                @click="modalTambah = false"
            ></div>

            <div
                x-show="modalTambah"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full p-6 border border-gray-100"
            >
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="text-base font-extrabold text-gray-900 flex items-center gap-2">
                        <span class="w-7 h-7 rounded-lg bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-sm">+</span>
                        Tambah Jenis / Kategori Surat
                    </h3>
                    <button type="button" @click="modalTambah = false" class="text-gray-400 hover:text-gray-600 text-lg">&times;</button>
                </div>

                <div class="mt-4 space-y-3.5">
                    <template x-if="formTambah.error">
                        <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-xl font-medium" x-text="formTambah.error"></div>
                    </template>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Jenis Surat <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            x-model="formTambah.nama"
                            placeholder="Contoh: Surat Rekomendasi Umrah"
                            class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kode Singkatan Surat <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            x-model="formTambah.kode"
                            placeholder="Contoh: SRU.23"
                            class="w-full text-xs font-mono rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5 uppercase"
                        />
                        <p class="text-[11px] text-gray-400 mt-1">Kode ini akan disisipkan dalam nomor surat resmi (misal: 001/<strong>SRU.23</strong>/AF-PA/X/2026).</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Keterangan / Fungsi (Opsional)</label>
                        <input
                            type="text"
                            x-model="formTambah.keterangan"
                            placeholder="Contoh: Untuk santri dan asatidz yang mengajukan ibadah umrah"
                            class="w-full text-xs rounded-xl border-gray-300 focus:border-emerald-600 focus:ring-emerald-600 py-2.5"
                        />
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-2.5 pt-3 border-t border-gray-100">
                    <button
                        type="button"
                        @click="modalTambah = false"
                        class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        @click="submitTambahKategori()"
                        :disabled="formTambah.loading"
                        class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-sm transition disabled:opacity-50"
                    >
                        <template x-if="formTambah.loading">
                            <span>Menyimpan...</span>
                        </template>
                        <template x-if="!formTambah.loading">
                            <span>Simpan & Pilih</span>
                        </template>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
