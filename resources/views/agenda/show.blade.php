<x-app-layout>
    @section('title', 'Detail Agenda ' . $agenda->judul)

    <div class="max-w-4xl mx-auto space-y-6 pb-12">
        <!-- Top Navigation & Action Buttons -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs text-gray-500 mb-1.5 font-medium">
                    <a href="{{ route('admin.agenda.index') }}" class="hover:text-emerald-700 transition">Agenda Kegiatan</a>
                    <span>/</span>
                    <span class="text-emerald-800 font-semibold truncate max-w-xs">Detail Agenda</span>
                </nav>
                <div class="flex flex-wrap items-center gap-3">
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">{{ $agenda->judul }}</h1>
                    @if ($agenda->status === 'rencana')
                        <span class="px-2.5 py-0.5 bg-emerald-50 text-emerald-800 border border-emerald-300 font-bold rounded-full text-xs">Rencana</span>
                    @elseif ($agenda->status === 'berjalan')
                        <span class="px-2.5 py-0.5 bg-blue-50 text-blue-800 border border-blue-300 font-bold rounded-full text-xs">Sedang Berjalan</span>
                    @elseif ($agenda->status === 'selesai')
                        <span class="px-2.5 py-0.5 bg-gray-100 text-gray-700 font-bold rounded-full text-xs">Selesai</span>
                    @else
                        <span class="px-2.5 py-0.5 bg-red-50 text-red-800 border border-red-300 font-bold rounded-full text-xs">Batal</span>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.agenda.edit', $agenda) }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-700 hover:bg-gray-50 text-xs sm:text-sm font-semibold rounded-xl border border-gray-300 shadow-2xs transition active:scale-95">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>Ubah</span>
                </a>

                <form method="POST" action="{{ route('admin.agenda.destroy', $agenda) }}"
                      data-konfirmasi="Apakah Anda yakin ingin menghapus agenda kegiatan ini?"
                      data-judul="Hapus Agenda Kegiatan"
                      data-tombol="Ya, Hapus Agenda">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-50 text-red-700 hover:bg-red-100 text-xs sm:text-sm font-semibold rounded-xl border border-red-200 shadow-2xs transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>Hapus</span>
                    </button>
                </form>

                <a href="{{ route('admin.agenda.index') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs sm:text-sm font-semibold rounded-xl transition active:scale-95">
                    <span>Kembali</span>
                </a>
            </div>
        </div>

        <!-- Detail Box -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
            <div class="px-6 py-4 bg-gray-50/80 border-b border-gray-100 font-extrabold text-xs text-gray-800 uppercase tracking-wider">
                Informasi Pelaksanaan Kegiatan
            </div>
            <div class="p-6 sm:p-8 grid grid-cols-1 md:grid-cols-2 gap-6 text-xs sm:text-sm">
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Waktu Mulai</span>
                    <span class="block text-sm sm:text-base font-bold text-gray-900 mt-1">
                        {{ $agenda->mulai ? $agenda->mulai->isoFormat('dddd, D MMMM Y - HH:mm') : '-' }} WIB
                    </span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Waktu Selesai</span>
                    <span class="block text-sm sm:text-base font-bold text-gray-900 mt-1">
                        {{ $agenda->selesai ? $agenda->selesai->isoFormat('dddd, D MMMM Y - HH:mm') : '-' }} WIB
                    </span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Lokasi / Tempat Acara</span>
                    <span class="block text-sm font-bold text-gray-900 mt-1 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-700 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>{{ $agenda->lokasi }}</span>
                    </span>
                </div>
                <div>
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider">Penanggung Jawab (PJ)</span>
                    @if ($agenda->pj)
                        <span class="block text-sm font-bold text-gray-900 mt-1">{{ $agenda->pj->nama }}</span>
                        <span class="block text-xs text-gray-500">{{ $agenda->pj->jabatan }} (Divisi: {{ $agenda->pj->divisi ?? '-' }})</span>
                        @if ($agenda->pj->no_hp)
                            @php
                                $cleanHp = preg_replace('/[^0-9]/', '', $agenda->pj->no_hp);
                                if (str_starts_with($cleanHp, '0')) {
                                    $cleanHp = '62' . substr($cleanHp, 1);
                                }
                            @endphp
                            <a href="https://wa.me/{{ $cleanHp }}" target="_blank"
                               class="inline-flex items-center gap-1 font-mono text-xs text-emerald-800 font-bold hover:underline mt-1">
                                <span>No. WhatsApp: {{ $agenda->pj->no_hp }}</span>
                                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.087.087-.177.181-.076.355.101.173.449.741.964 1.201.662.591 1.221.774 1.394.86.174.086.275.072.376-.043.101-.116.433-.506.549-.68.116-.173.231-.144.39-.086s1.011.477 1.184.564.289.13.332.202c.043.073.043.419-.101.824z"/></svg>
                            </a>
                        @endif
                    @else
                        <span class="block text-sm text-gray-400 italic mt-1">- Belum Ditentukan -</span>
                    @endif
                </div>

                <div class="md:col-span-2 pt-2 border-t border-gray-100">
                    <span class="block text-gray-400 text-xs uppercase font-bold tracking-wider mb-2">Deskripsi / Susunan Kegiatan</span>
                    <div class="p-4 bg-gray-50/80 rounded-xl border border-gray-200 text-gray-800 leading-relaxed whitespace-pre-line text-xs sm:text-sm">
                        {{ $agenda->deskripsi ?: 'Tidak ada catatan deskripsi tambahan untuk kegiatan ini.' }}
                    </div>
                </div>
            </div>
            <div class="px-6 sm:px-8 py-3 bg-gray-50/60 border-t border-gray-100 text-xs text-gray-500">
                Dicatat oleh: <span class="font-semibold text-gray-700">{{ $agenda->user ? $agenda->user->name : 'Sekretaris' }}</span> &bull; {{ $agenda->created_at->format('d-m-Y H:i') }} WIB
            </div>
        </div>
    </div>
</x-app-layout>
