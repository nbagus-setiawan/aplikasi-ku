@forelse($transaksis as $index => $item)
<div id="transaksi-card-{{ $item->id }}"
     data-transaksi-id="{{ $item->id }}"
     class="p-3.5 space-y-2.5 hover:bg-slate-50 transition-colors {{ $item->jenis === 'debit' ? 'border-l-4 border-l-emerald-500 bg-emerald-50/15' : 'border-l-4 border-l-rose-500 bg-rose-50/15' }}">
    <!-- Baris Atas: Kategori & Nominal -->
    <div class="flex items-center justify-between gap-2">
        <span class="inline-block px-2 py-0.5 rounded-md text-[11px] font-bold border {{ $item->badge_class }}">
            {{ $item->kategori }}
        </span>
        <span class="text-sm font-mono font-black {{ $item->jenis === 'debit' ? 'text-emerald-700' : 'text-rose-700' }}">
            {{ $item->jenis === 'debit' ? '+' : '-' }} Rp {{ number_format($item->nominal, 0, ',', '.') }}
        </span>
    </div>

    <!-- Baris Tengah: Uraian Keterangan & Pihak Terkait -->
    <div>
        <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $item->keterangan }}</p>
        @if($item->nama_pihak)
            <p class="text-[11px] text-slate-500 mt-1">
                <span class="text-slate-400 font-medium">{{ $item->jenis === 'debit' ? 'Dari:' : 'Kepada:' }}</span>
                <span class="font-semibold text-slate-700">{{ $item->nama_pihak }}</span>
            </p>
        @endif
    </div>

    <!-- Baris Bawah: No Bukti, Tanggal & Tombol Aksi -->
    <div class="pt-2 border-t border-slate-100/80 flex items-center justify-between text-[11px] text-slate-500">
        <div class="flex items-center gap-1.5 font-mono text-[10px] text-slate-600">
            <span class="font-bold">{{ $item->kode_transaksi }}</span>
            <span>•</span>
            <span>{{ $item->formatted_tanggal }}</span>
        </div>

        <div class="flex items-center gap-1">
            @if($item->lampiran)
                @php
                    $isPdf = str_ends_with(strtolower($item->lampiran), '.pdf');
                    $lampiranUrl = asset('storage/' . $item->lampiran);
                @endphp
                @if($isPdf)
                    <a href="{{ $lampiranUrl }}" target="_blank" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50" title="Lampiran PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    </a>
                @else
                    <button @click="openPreview({{ \Illuminate\Support\Js::from($lampiranUrl) }}, {{ \Illuminate\Support\Js::from('Bukti ' . $item->kode_transaksi) }})"
                            type="button"
                            class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50"
                            title="Foto Bukti">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </button>
                @endif
            @endif

            <button @click="openEdit({{ \Illuminate\Support\Js::from([
                'id' => $item->id,
                'kode_transaksi' => $item->kode_transaksi,
                'tanggal' => $item->tanggal->format('Y-m-d'),
                'jenis' => $item->jenis,
                'kategori' => $item->kategori,
                'nominal' => (int) $item->nominal,
                'keterangan' => $item->keterangan,
                'nama_pihak' => $item->nama_pihak,
                'lampiran' => $item->lampiran,
            ]) }})"
                    type="button"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50"
                    title="Ubah Transaksi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
            </button>

            <button @click="confirmDelete({{ $item->id }}, {{ \Illuminate\Support\Js::from($item->kode_transaksi) }}, {{ \Illuminate\Support\Js::from(number_format($item->nominal, 0, ',', '.')) }})"
                    type="button"
                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50"
                    title="Hapus Transaksi">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
        </div>
    </div>
</div>
@empty
<div class="p-8 text-center text-slate-500 space-y-3">
    <span class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl inline-block">
        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
    </span>
    <p class="text-xs font-bold text-slate-700">Belum Ada Transaksi Kas</p>
    <div class="flex justify-center gap-2">
        <button @click="modalDebitOpen = true" type="button" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs">+ Debit</button>
        <button @click="modalKreditOpen = true" type="button" class="px-3 py-1.5 rounded-xl bg-rose-600 text-white font-semibold text-xs">- Kredit</button>
    </div>
</div>
@endforelse
