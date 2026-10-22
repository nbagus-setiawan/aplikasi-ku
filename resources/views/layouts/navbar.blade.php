<header class="h-16 bg-white border-b border-gray-200 px-4 sm:px-6 flex items-center justify-between sticky top-0 z-30 shadow-xs">
    <div class="flex items-center gap-3">
        <!-- Toggle button mobile & tablet -->
        <button
            @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2 rounded-xl text-gray-600 hover:text-emerald-800 hover:bg-emerald-50 border border-gray-200/80 focus:outline-none transition-colors"
            title="Buka Menu Navigasi"
            aria-label="Buka Navigasi"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>

        <!-- Judul / Info Singkat Halaman -->
        <div class="flex items-center gap-3">
            <span class="text-sm sm:text-base font-bold text-gray-800 tracking-tight">
                @yield('title', 'Panel Sekretariat')
            </span>
            <span class="hidden sm:inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 border border-emerald-200">
                Pondok Pesantren Al-Falah Putra
            </span>
        </div>
    </div>

    <!-- Sisi Kanan Navbar -->
    <div class="flex items-center gap-3">
        <!-- Tautan ke Beranda Publik -->
        <a href="{{ url('/') }}" target="_blank"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 rounded-xl border border-emerald-200/80 shadow-xs transition">
            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
            </svg>
            <span class="hidden sm:inline">Lihat Web Publik</span>
        </a>

        <!-- LONCENG NOTIFIKASI AKTIVITAS PUBLIK (LIKE, KOMENTAR, SHARE) -->
        <div class="relative"
             x-data="{
                notifOpen: false,
                unreadCount: 0,
                notifikasis: [],
                loading: false,

                // Mode Seleksi / Pilih untuk Hapus
                selectMode: false,
                selectedIds: [],
                deleteLoading: false,

                // Modal Pop-Up Pratinjau Pesan
                previewOpen: false,
                activeNotif: null,

                async fetchNotif() {
                    try {
                        const res = await fetch('{{ route('admin.notifikasi.index') }}');
                        const data = await res.json();
                        if (data.success) {
                            this.unreadCount = data.unread_count;
                            this.notifikasis = data.notifikasis;
                        }
                    } catch (e) {
                        console.error('Gagal mengambil notifikasi:', e);
                    }
                },

                async openPreview(item) {
                    this.activeNotif = item;
                    this.previewOpen = true;
                    this.notifOpen = false;

                    // Tandai dibaca via AJAX jika belum
                    if (!item.is_dibaca) {
                        try {
                            const res = await fetch(`/admin/notifikasi/${item.id}/baca`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                item.is_dibaca = true;
                                this.unreadCount = data.unread_count;
                            }
                        } catch (e) {
                            console.error(e);
                        }
                    }
                },

                async markAllRead() {
                    try {
                        const res = await fetch('{{ route('admin.notifikasi.baca-semua') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.unreadCount = 0;
                            this.notifikasis.forEach(n => n.is_dibaca = true);
                        }
                    } catch (e) {
                        console.error(e);
                    }
                },

                toggleSelect(id) {
                    const idx = this.selectedIds.indexOf(id);
                    if (idx > -1) {
                        this.selectedIds.splice(idx, 1);
                    } else {
                        this.selectedIds.push(id);
                    }
                },

                toggleSelectAll() {
                    if (this.selectedIds.length === this.notifikasis.length) {
                        this.selectedIds = [];
                    } else {
                        this.selectedIds = this.notifikasis.map(n => n.id);
                    }
                },

                async deleteSingle(item, event) {
                    if (event) event.stopPropagation();
                    if (!confirm('Hapus notifikasi ini?')) return;
                    this.deleteLoading = true;
                    try {
                        const res = await fetch(`/admin/notifikasi/${item.id}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifikasis = this.notifikasis.filter(n => n.id !== item.id);
                            this.unreadCount = data.unread_count;
                            this.selectedIds = this.selectedIds.filter(id => id !== item.id);
                            if (this.previewOpen && this.activeNotif?.id === item.id) {
                                this.previewOpen = false;
                                this.activeNotif = null;
                            }
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.deleteLoading = false;
                    }
                },

                async deleteBatch() {
                    if (this.selectedIds.length === 0) return;
                    if (!confirm(`Hapus ${this.selectedIds.length} notifikasi yang dipilih?`)) return;
                    this.deleteLoading = true;
                    try {
                        const res = await fetch('{{ route('admin.notifikasi.hapus-banyak') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifikasis = this.notifikasis.filter(n => !this.selectedIds.includes(n.id));
                            this.unreadCount = data.unread_count;
                            this.selectedIds = [];
                            this.selectMode = false;
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.deleteLoading = false;
                    }
                },

                async deleteAll() {
                    if (this.notifikasis.length === 0) return;
                    if (!confirm('Yakin ingin menghapus seluruh notifikasi?')) return;
                    this.deleteLoading = true;
                    try {
                        const res = await fetch('{{ route('admin.notifikasi.hapus-semua') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.notifikasis = [];
                            this.unreadCount = 0;
                            this.selectedIds = [];
                            this.selectMode = false;
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.deleteLoading = false;
                    }
                },

                init() {
                    this.fetchNotif();
                    // Polling data notifikasi setiap 25 detik
                    setInterval(() => this.fetchNotif(), 25000);
                }
             }"
             @click.outside="notifOpen = false">

            <!-- Tombol Lonceng dengan Badge Angka -->
            <button
                @click="notifOpen = !notifOpen"
                class="relative p-2 rounded-xl text-gray-600 hover:text-emerald-800 hover:bg-emerald-50/80 border border-gray-200/80 transition focus:outline-none"
                title="Notifikasi Aktivitas">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>

                <!-- Badge Angka Saja -->
                <span
                    x-show="unreadCount > 0"
                    x-cloak
                    class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 text-[10px] font-extrabold text-white bg-rose-600 rounded-full shadow-sm animate-pulse"
                    x-text="unreadCount > 99 ? '99+' : unreadCount">
                </span>
            </button>

            <!-- Dropdown Ringkasan Notifikasi -->
            <div
                x-show="notifOpen"
                x-cloak
                class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 z-50 overflow-hidden"
                style="display: none;">

                <!-- Header Dropdown -->
                <div class="px-4 py-2.5 border-b border-gray-100 flex items-center justify-between gap-2 bg-gray-50/60">
                    <!-- Tampilan Mode Normal -->
                    <template x-if="!selectMode">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-gray-900">Notifikasi Publik</span>
                                <template x-if="unreadCount > 0">
                                    <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-full bg-rose-100 text-rose-700"
                                          x-text="unreadCount + ' Baru'"></span>
                                </template>
                            </div>
                            <div class="flex items-center gap-2">
                                <template x-if="unreadCount > 0">
                                    <button
                                        @click="markAllRead()"
                                        class="text-[11px] font-semibold text-emerald-700 hover:text-emerald-900 hover:underline">
                                        Tandai Dibaca
                                    </button>
                                </template>
                                <template x-if="notifikasis.length > 0">
                                    <button
                                        type="button"
                                        @click="selectMode = true; selectedIds = []"
                                        class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-700 hover:text-slate-900 bg-white hover:bg-slate-100 px-2.5 py-1 rounded-lg border border-slate-200 transition shadow-2xs">
                                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <span>Pilih</span>
                                    </button>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Tampilan Mode Seleksi / Pilih Hapus -->
                    <template x-if="selectMode">
                        <div class="flex items-center justify-between w-full">
                            <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-700">
                                <input
                                    type="checkbox"
                                    :checked="selectedIds.length === notifikasis.length && notifikasis.length > 0"
                                    @change="toggleSelectAll()"
                                    class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-gray-300">
                                <span>Pilih Semua (<span x-text="selectedIds.length"></span>)</span>
                            </label>
                            <div class="flex items-center gap-1.5">
                                <button
                                    type="button"
                                    @click="deleteBatch()"
                                    :disabled="selectedIds.length === 0 || deleteLoading"
                                    class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 disabled:opacity-40 transition shadow-2xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span x-text="'Hapus (' + selectedIds.length + ')'"></span>
                                </button>
                                <button
                                    type="button"
                                    @click="selectMode = false; selectedIds = []"
                                    class="px-2 py-1 rounded-lg text-xs font-semibold text-slate-600 bg-white hover:bg-slate-100 border border-slate-200 transition">
                                    Batal
                                </button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- List Notifikasi -->
                <div class="max-h-80 overflow-y-auto divide-y divide-gray-50">
                    <template x-if="notifikasis.length === 0">
                        <div class="p-6 text-center text-xs text-gray-400">
                            <svg class="w-8 h-8 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            Belum ada notifikasi aktivitas publik saat ini.
                        </div>
                    </template>

                    <template x-for="item in notifikasis" :key="item.id">
                        <div
                            @click="selectMode ? toggleSelect(item.id) : openPreview(item)"
                            class="p-3 hover:bg-gray-50 transition cursor-pointer flex items-start gap-2.5 relative group"
                            :class="{
                                'bg-emerald-50/40': !item.is_dibaca && !selectedIds.includes(item.id),
                                'bg-rose-50/60 ring-1 ring-inset ring-rose-200': selectMode && selectedIds.includes(item.id)
                            }">

                            <!-- Checkbox Seleksi -->
                            <template x-if="selectMode">
                                <div class="shrink-0 pt-1.5" @click.stop="toggleSelect(item.id)">
                                    <input
                                        type="checkbox"
                                        :value="item.id"
                                        :checked="selectedIds.includes(item.id)"
                                        class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500 border-gray-300 pointer-events-none">
                                </div>
                            </template>

                            <!-- Ikon Tipe -->
                            <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center text-sm shadow-xs mt-0.5"
                                 :class="{
                                    'bg-rose-100 text-rose-600': item.tipe === 'like',
                                    'bg-emerald-100 text-emerald-800': item.tipe === 'komentar',
                                    'bg-blue-100 text-blue-700': item.tipe === 'share',
                                    'bg-gray-100 text-gray-700': !['like', 'komentar', 'share'].includes(item.tipe)
                                 }">
                                <template x-if="item.tipe === 'like'"><span>❤️</span></template>
                                <template x-if="item.tipe === 'komentar'"><span>💬</span></template>
                                <template x-if="item.tipe === 'share'"><span>↗️</span></template>
                                <template x-if="!['like', 'komentar', 'share'].includes(item.tipe)"><span>🔔</span></template>
                            </div>

                            <!-- Konten Ringkas -->
                            <div class="flex-1 min-w-0 pr-1">
                                <div class="flex items-center justify-between gap-1 mb-0.5">
                                    <h4 class="text-xs font-bold text-gray-900 truncate" x-text="item.judul"></h4>
                                </div>
                                <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed" x-text="item.pesan_singkat"></p>
                                <div class="flex items-center justify-between mt-1.5">
                                    <span class="text-[10px] text-gray-400" x-text="item.waktu"></span>
                                    <template x-if="!selectMode">
                                        <span class="text-[10px] text-emerald-700 font-semibold group-hover:underline">Buka →</span>
                                    </template>
                                </div>
                            </div>

                            <!-- Indikator Belum Dibaca & Tombol Hapus Satuan Cepat -->
                            <template x-if="!selectMode">
                                <div class="shrink-0 flex items-center gap-1 pt-0.5">
                                    <template x-if="!item.is_dibaca">
                                        <span class="w-2 h-2 rounded-full bg-rose-600 block"></span>
                                    </template>
                                    <button
                                        type="button"
                                        @click="deleteSingle(item, $event)"
                                        title="Hapus notifikasi ini"
                                        class="p-1 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 transition opacity-80 sm:opacity-0 group-hover:opacity-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>

                <!-- Footer Dropdown Ringkasan -->
                <template x-if="notifikasis.length > 0 && !selectMode">
                    <div class="px-4 py-2 border-t border-gray-100 bg-gray-50/50 flex items-center justify-between text-[11px]">
                        <span class="text-gray-500">Total: <b x-text="notifikasis.length"></b> notifikasi</span>
                        <button
                            type="button"
                            @click="deleteAll()"
                            class="text-rose-600 hover:text-rose-800 font-semibold hover:underline">
                            Bersihkan Semua
                        </button>
                    </div>
                </template>
            </div>

            <!-- MODAL POP-UP PRATINJAU PESAN LENGKAP -->
            <div x-show="previewOpen"
                 x-cloak
                 class="fixed inset-0 z-50 overflow-y-auto"
                 role="dialog"
                 aria-modal="true"
                 style="display: none;">
                <!-- Backdrop -->
                <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-xs transition-opacity"
                     @click="previewOpen = false"></div>

                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                    <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100 p-6 sm:p-7 space-y-4"
                         @click.stop>

                        <!-- Header Modal Pratinjau -->
                        <div class="flex items-start justify-between border-b border-gray-100 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shadow-xs shrink-0"
                                     :class="{
                                        'bg-rose-100 text-rose-600': activeNotif?.tipe === 'like',
                                        'bg-emerald-100 text-emerald-800': activeNotif?.tipe === 'komentar',
                                        'bg-blue-100 text-blue-700': activeNotif?.tipe === 'share',
                                        'bg-gray-100 text-gray-700': !['like', 'komentar', 'share'].includes(activeNotif?.tipe)
                                     }">
                                    <template x-if="activeNotif?.tipe === 'like'"><span>❤️</span></template>
                                    <template x-if="activeNotif?.tipe === 'komentar'"><span>💬</span></template>
                                    <template x-if="activeNotif?.tipe === 'share'"><span>↗️</span></template>
                                    <template x-if="!['like', 'komentar', 'share'].includes(activeNotif?.tipe)"><span>🔔</span></template>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md"
                                          :class="{
                                            'bg-rose-100 text-rose-700': activeNotif?.tipe === 'like',
                                            'bg-emerald-100 text-emerald-800': activeNotif?.tipe === 'komentar',
                                            'bg-blue-100 text-blue-700': activeNotif?.tipe === 'share',
                                            'bg-gray-100 text-gray-700': !['like', 'komentar', 'share'].includes(activeNotif?.tipe)
                                          }"
                                          x-text="activeNotif?.tipe === 'like' ? 'Suka Baru' : (activeNotif?.tipe === 'komentar' ? 'Komentar Publik' : (activeNotif?.tipe === 'share' ? 'Berita Dibagikan' : 'Notifikasi'))">
                                    </span>
                                    <h3 class="text-base font-extrabold text-gray-900 mt-1 leading-snug" x-text="activeNotif?.judul"></h3>
                                </div>
                            </div>

                            <button @click="previewOpen = false" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Metadata Pengirim & Waktu -->
                        <div class="grid grid-cols-2 gap-3 p-3 bg-gray-50 rounded-2xl text-xs border border-gray-100">
                            <div>
                                <span class="text-gray-400 block text-[10px] font-semibold uppercase">Pengirim / User</span>
                                <span class="font-bold text-gray-800" x-text="activeNotif?.data_tambahan?.pengirim || activeNotif?.data_tambahan?.nama || 'User Publik'"></span>
                            </div>
                            <div>
                                <span class="text-gray-400 block text-[10px] font-semibold uppercase">Waktu Masuk</span>
                                <span class="font-semibold text-gray-700" x-text="activeNotif?.waktu_lengkap || activeNotif?.waktu"></span>
                            </div>
                        </div>

                        <!-- Terkait Pengumuman -->
                        <template x-if="activeNotif?.data_tambahan?.pengumuman_judul">
                            <div class="p-3 bg-emerald-50/70 border border-emerald-200/80 rounded-2xl text-xs space-y-1">
                                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wide">Pengumuman / Berita Terkait</span>
                                <p class="font-bold text-emerald-950 text-xs sm:text-sm" x-text="activeNotif?.data_tambahan?.pengumuman_judul"></p>
                            </div>
                        </template>

                        <!-- Kotak Isi Lengkap Pesan -->
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold text-gray-700">Isi Pesan / Tanggapan Lengkap:</label>
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs sm:text-sm text-gray-800 leading-relaxed whitespace-pre-line shadow-xs font-normal"
                                 x-text="activeNotif?.pesan">
                            </div>
                        </div>

                        <!-- Footer Modal Pratinjau -->
                        <div class="flex items-center justify-between gap-2.5 pt-3 border-t border-gray-100 flex-wrap">
                            <button
                                type="button"
                                @click="deleteSingle(activeNotif)"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-rose-600 hover:bg-rose-50 border border-rose-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Hapus</span>
                            </button>

                            <div class="flex items-center gap-2 ml-auto">
                                <template x-if="activeNotif?.link">
                                    <a :href="activeNotif.link"
                                       target="_blank"
                                       class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" /></svg>
                                        <span>Buka Halaman</span>
                                    </a>
                                </template>
                                <button
                                    type="button"
                                    @click="previewOpen = false"
                                    class="px-5 py-2 rounded-xl text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                                    Tutup
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Profil & Logout Dropdown -->
        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
            <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-emerald-800 focus:outline-none py-1 px-2 rounded-lg hover:bg-gray-50">
                <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                    {{ strtoupper(substr(Auth::user()->name ?? 'S', 0, 1)) }}
                </div>
                <span class="hidden md:inline font-medium text-gray-700">{{ Auth::user()->name ?? 'Sekretaris' }}</span>
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" x-cloak
                 class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-1 z-50">
                <a href="{{ route('profile.edit') }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profil Saya
                </a>
                <a href="{{ route('admin.pengaturan.index') }}"
                   class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-700">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    </svg>
                    Pengaturan
                </a>
                <div class="border-t border-gray-100 my-1"></div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left flex items-center gap-2 px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
