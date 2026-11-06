@forelse($transaksis as $index => $item)
<tr id="transaksi-row-{{ $item->id }}"
    data-transaksi-id="{{ $item->id }}"
    class="hover:bg-slate-50/80 transition-colors">
    <td class="px-4 py-3.5 text-center font-medium text-slate-400">
        {{ $transaksis->firstItem() + $index }}
    </td>
    <td class="px-4 py-3.5 whitespace-nowrap">
        <span class="font-mono text-xs font-bold text-slate-800 block">{{ $item->kode_transaksi }}</span>
        <span class="text-[11px] text-slate-500 font-medium">{{ $item->formatted_tanggal }}</span>
    </td>
    <td class="px-4 py-3.5 whitespace-nowrap">
        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-bold border {{ $item->badge_class }}">
            {{ $item->kategori }}
        </span>
    </td>
    <td class="px-4 py-3.5">
        <p class="font-medium text-slate-800 line-clamp-2">{{ $item->keterangan }}</p>
        @if($item->user)
            <span class="text-[10px] text-slate-400 block mt-0.5">Dicatat: Bendahara Putra</span>
        @endif
    </td>
    <td class="px-4 py-3.5 whitespace-nowrap text-slate-600 font-medium">
        {{ $item->nama_pihak ?: '-' }}
    </td>
    <td class="px-4 py-3.5 text-right whitespace-nowrap font-bold font-mono text-emerald-600">
        @if($item->jenis === 'debit')
            Rp {{ number_format($item->nominal, 0, ',', '.') }}
        @else
            <span class="text-slate-300">-</span>
        @endif
    </td>
    <td class="px-4 py-3.5 text-right whitespace-nowrap font-bold font-mono text-rose-600">
        @if($item->jenis === 'kredit')
            Rp {{ number_format($item->nominal, 0, ',', '.') }}
        @else
            <span class="text-slate-300">-</span>
        @endif
    </td>
    <td class="px-4 py-3.5 text-center whitespace-nowrap">
        @if($item->lampiran)
            @php
                $isPdf = str_ends_with(strtolower($item->lampiran), '.pdf');
                $lampiranUrl = asset('storage/' . $item->lampiran);
            @endphp
            @if($isPdf)
                <a href="{{ $lampiranUrl }}" target="_blank" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 inline-block" title="Lihat Lampiran PDF">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </a>
            @else
                <button @click="openPreview({{ \Illuminate\Support\Js::from($lampiranUrl) }}, {{ \Illuminate\Support\Js::from('Bukti Nota ' . $item->kode_transaksi) }})"
                        type="button"
                        class="p-1.5 rounded-lg text-emerald-600 hover:bg-emerald-50"
                        title="Pratinjau Foto Nota">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </button>
            @endif
        @else
            <span class="text-slate-300 text-xs">-</span>
        @endif
    </td>
    <td class="px-4 py-3.5 text-center whitespace-nowrap">
        @php
            $tBulan = $item->tanggal ? $item->tanggal->format('Y-m') : '';
            $isRowLocked = in_array($tBulan, $tutupBulanList ?? []);
        @endphp

        @if($isRowLocked)
            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-[11px] font-semibold bg-slate-100 text-slate-500 border border-slate-200" title="Bulan telah ditutup buku (Terkunci)">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <span>Terkunci</span>
            </span>
        @else
            <div class="flex items-center justify-center gap-1.5">
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
                        class="p-1.5 rounded-lg text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition"
                        title="Ubah Transaksi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </button>

                <button @click="confirmDelete({{ $item->id }}, {{ \Illuminate\Support\Js::from($item->kode_transaksi) }}, {{ \Illuminate\Support\Js::from(number_format($item->nominal, 0, ',', '.')) }})"
                        type="button"
                        class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition"
                        title="Hapus Transaksi">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="9" class="px-4 py-12 text-center text-slate-500">
        <div class="max-w-sm mx-auto space-y-3">
            <span class="p-3 bg-emerald-50 text-emerald-600 rounded-2xl inline-block">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            <p class="font-bold text-slate-700">Belum Ada Transaksi Kas</p>
            <p class="text-xs text-slate-400">Silakan catat mutasi penerimaan debit atau pengeluaran kredit pertama.</p>
            <div class="flex justify-center gap-2 pt-1">
                <button @click="modalDebitOpen = true" type="button" class="px-3 py-1.5 rounded-xl bg-emerald-600 text-white font-semibold text-xs shadow-xs hover:bg-emerald-700">+ Catat Debit</button>
                <button @click="modalKreditOpen = true" type="button" class="px-3 py-1.5 rounded-xl bg-rose-600 text-white font-semibold text-xs shadow-xs hover:bg-rose-700">- Catat Kredit</button>
            </div>
        </div>
    </td>
</tr>
@endforelse
