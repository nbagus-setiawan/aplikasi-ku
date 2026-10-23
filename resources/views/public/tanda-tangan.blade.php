<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tanda Tangan Dokumen Elektronik &mdash; {{ $pengaturan['nama_lembaga'] ?? 'Pondok Pesantren Al Falah Putra' }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Standar tipografi lembar surat resmi */
        .font-surat {
            font-family: 'Times New Roman', Times, 'Liberation Serif', serif;
        }
        .isi-surat p {
            margin-bottom: 0.75rem;
            text-align: justify;
            text-justify: inter-word;
            line-height: 1.6;
        }
        .isi-surat table {
            width: 100%;
            border-collapse: collapse;
            margin: 0.75rem 0;
        }
        .isi-surat td, .isi-surat th {
            padding: 4px 8px;
            border: 1px solid #333;
        }
    </style>
</head>
<body class="bg-slate-200/90 text-gray-900 min-h-screen flex flex-col justify-between antialiased selection:bg-emerald-600 selection:text-white"
      x-data="{ showModalTtd: false }">

    @if ($status === 'active')
        <!-- Top Toolbar Mengambang (Bersih, Rapi & Tidak Mengganggu Lembar Surat) -->
        <header class="sticky top-0 z-40 bg-slate-900/95 backdrop-blur-md text-white border-b border-slate-700 shadow-lg px-3 py-2.5 sm:px-6">
            <div class="max-w-4xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2.5">
                <div class="flex items-center gap-2.5 text-center sm:text-left">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                    <div>
                        <div class="text-xs sm:text-sm font-bold tracking-wide flex items-center gap-1.5 justify-center sm:justify-start">
                            <span>Konfirmasi Penandatanganan Dokumen</span>
                            <span class="text-[10px] bg-emerald-800 text-emerald-200 px-1.5 py-0.2 rounded font-mono font-semibold">{{ $surat->no_surat }}</span>
                        </div>
                        <div class="text-[10.5px] text-slate-300">
                            Penandatangan: <strong class="text-white">{{ $tandaTangan->nama_penandatangan }}</strong> ({{ $tandaTangan->jabatan_penandatangan }})
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-center sm:justify-end">
                    <span class="text-[11px] text-emerald-300 bg-emerald-950/80 border border-emerald-700/60 px-2.5 py-1 rounded-lg font-mono shrink-0">
                        ⏱️ Berlaku 30 Menit
                    </span>
                    <button type="button"
                            @click="showModalTtd = true; setTimeout(() => initSignaturePad(), 120)"
                            class="min-h-[44px] px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs sm:text-sm rounded-xl shadow-md transition flex items-center gap-1.5 active:scale-95 shrink-0">
                        <span>✍️ Bubuhkan Tanda Tangan</span>
                    </button>
                </div>
            </div>
        </header>

        <!-- AREA LEMBAR SURAT RESMI (TAMPILAN PERSIS SEPERTI CETAK PDF) -->
        <main class="flex-1 max-w-4xl w-full mx-auto p-2 sm:p-6 my-2 sm:my-4">
            
            <div class="bg-white shadow-2xl rounded-sm border border-gray-300 mx-auto font-surat text-gray-900 relative flex flex-col justify-between p-4 sm:p-8 md:p-12 lg:p-[2cm] max-w-full sm:max-w-[21cm] min-h-[auto] sm:min-h-[29.7cm]">

                <div>
                    <!-- KOP SURAT RESMI -->
                    @if (!empty($kopGambar))
                        <div class="text-center pb-3 border-b-2 border-black mb-5">
                            <img src="{{ $kopGambar }}" alt="Kop Surat" class="w-full max-h-32 object-contain mx-auto" />
                        </div>
                    @else
                        <div class="pb-3 mb-4 text-center">
                            <table class="w-full border-collapse border-0 m-0">
                                <tr>
                                    @if (!empty($logo))
                                        <td class="w-20 align-middle text-center p-0 border-0">
                                            <img src="{{ $logo }}" class="w-18 h-auto mx-auto object-contain" alt="Logo">
                                        </td>
                                    @endif
                                    <td class="align-middle text-center px-2 border-0">
                                        <div class="text-xs sm:text-sm font-bold uppercase tracking-wider text-gray-900 leading-tight">
                                            {{ $pengaturan['kop_yayasan'] ?? 'YAYASAN PONDOK PESANTREN AL-FALAH PUTAK' }}
                                        </div>
                                        <div class="text-base sm:text-xl font-black uppercase tracking-wide text-gray-900 my-0.5 leading-tight">
                                            {{ $pengaturan['kop_nama'] ?? 'PONDOK PESANTREN AL-FALAH PUTRA' }}
                                        </div>
                                        <div class="text-[10px] sm:text-[11px] text-gray-700 leading-tight">
                                            {{ $pengaturan['kop_alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171' }}
                                        </div>
                                        <div class="text-[10px] sm:text-[11px] text-gray-700 leading-tight">
                                            {{ $pengaturan['kop_kontak'] ?? 'No. Telp/HP: 0812-7890-3117 | Email: sekretariat@alfalahputra.sch.id' }}
                                        </div>
                                    </td>
                                    @if (!empty($logo))
                                        <td class="w-20 border-0 p-0"></td>
                                    @endif
                                </tr>
                            </table>
                            <!-- Garis Ganda Baku Kop Surat -->
                            <div class="border-b-2 border-black mt-3 mb-0.5"></div>
                            <div class="border-b border-black"></div>
                        </div>
                    @endif

                    <!-- META DATA SURAT -->
                    <div class="grid grid-cols-2 gap-4 text-xs font-surat mb-4 pb-2">
                        <div class="space-y-1">
                            <div class="flex"><span class="w-20 text-gray-600">Nomor</span><span>: <strong>{{ $surat->no_surat }}</strong></span></div>
                            <div class="flex"><span class="w-20 text-gray-600">Lampiran</span><span>: {{ !empty($surat->file) ? ($lampiranNama ?? '1 (Satu) Berkas') : '-' }}</span></div>
                            <div class="flex"><span class="w-20 text-gray-600">Perihal</span><span>: <strong>{{ $surat->perihal }}</strong></span></div>
                        </div>
                        <div class="text-right font-surat text-xs">
                            <span>Putak, {{ $surat->tgl_surat ? $surat->tgl_surat->isoFormat('D MMMM Y') : date('d-m-Y') }}</span>
                        </div>
                    </div>

                    <!-- TUJUAN SURAT -->
                    <div class="mb-5 text-xs sm:text-sm font-surat">
                        <div>Kepada Yth.</div>
                        <div class="font-bold text-gray-900 mt-0.5">{{ $surat->tujuan }}</div>
                        <div>di - Tempat</div>
                    </div>

                    <!-- ISI SURAT -->
                    <div class="isi-surat font-surat text-xs sm:text-sm text-gray-800 leading-relaxed mb-6 space-y-2">
                        @php
                            $cleanIsi = preg_replace('/<!--.*?-->/s', '', $surat->isi ?? '');
                            $cleanIsi = preg_replace('/<p[^>]*>(?:\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $cleanIsi);
                        @endphp
                        @if (\Illuminate\Support\Str::contains($cleanIsi, ['<p', '<table', '<div', '<br', '<h', '<ul', '<ol']))
                            {!! $cleanIsi !!}
                        @else
                            <p><em>Assalamu'alaikum Warahmatullahi Wabarakatuh,</em></p>
                            @foreach (explode("\n", str_replace("\r", "", $cleanIsi)) as $paragraf)
                                @if (trim($paragraf) !== '')
                                    <p>{{ trim($paragraf) }}</p>
                                @endif
                            @endforeach
                            <p>Demikian surat ini kami sampaikan, atas perhatian dan kerja samanya kami ucapkan terima kasih.</p>
                            <p><em>Wassalamu'alaikum Warahmatullahi Wabarakatuh.</em></p>
                        @endif
                    </div>
                </div>

                <!-- BLOK PENANDATANGAN RESMI -->
                @php
                    $jml = max(1, min(3, (int) ($surat->jumlah_penandatangan ?? 1)));
                    $penList = $surat->penandatangans ?? [];
                    $p1 = $penList[0] ?? ['nama' => $surat->nama_penandatangan, 'jabatan' => $surat->jabatan_penandatangan];
                    $p2 = $penList[1] ?? ['nama' => '-', 'jabatan' => '-'];
                    $p3 = $penList[2] ?? ['nama' => '-', 'jabatan' => '-'];

                    $ttd1 = $tandaTangans[1] ?? null;
                    $ttd2 = $tandaTangans[2] ?? null;
                    $ttd3 = $tandaTangans[3] ?? null;
                    $myUrutan = (int) $tandaTangan->urutan;
                @endphp

                <div class="mt-6 pt-2 pb-16">
                    @if ($jml === 1)
                        <!-- 1 PENANDATANGAN DI SISI KANAN -->
                        <div class="grid grid-cols-2 gap-4 text-center font-surat text-xs">
                            <div></div>
                            <div>
                                <div class="text-xs sm:text-sm">{{ $p1['jabatan'] ?? $surat->jabatan_penandatangan }}</div>
                                
                                <!-- Kotak TTD Ukuran Tetap 3.9cm x 2.6cm (Rasio 3:2 Presisi) -->
                                <div class="w-[3.9cm] h-[2.6cm] mx-auto my-1.5 flex items-center justify-center p-1 rounded-sm {{ !empty($ttd1?->data_ttd) ? '' : 'border border-dashed border-emerald-500 bg-emerald-50/50 hover:bg-emerald-100/70 cursor-pointer transition' }}"
                                     @if(empty($ttd1?->data_ttd)) @click="showModalTtd = true; setTimeout(() => initSignaturePad(), 120)" @endif>
                                    @if (!empty($ttd1?->data_ttd))
                                        <img src="{{ $ttd1->data_ttd }}" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" alt="TTD 1">
                                    @else
                                        <div class="text-center font-sans">
                                            <span class="text-[11px] font-bold text-emerald-800 block">✍️ Klik di Sini</span>
                                            <span class="text-[9.5px] text-emerald-600 block">untuk Tanda Tangan</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="font-bold underline text-xs sm:text-sm">{{ $p1['nama'] ?? $surat->nama_penandatangan }}</div>
                            </div>
                        </div>

                    @elseif ($jml === 2)
                        <!-- 2 PENANDATANGAN SEJAJAR KIRI & KANAN -->
                        <div class="grid grid-cols-2 gap-4 text-center font-surat text-xs">
                            <!-- Penandatangan 1 (Kiri) -->
                            <div>
                                <div class="text-xs sm:text-sm">{{ $p1['jabatan'] ?? '' }}</div>
                                <div class="w-[3.9cm] h-[2.6cm] mx-auto my-1.5 flex items-center justify-center p-1 rounded-sm {{ !empty($ttd1?->data_ttd) ? '' : ($myUrutan === 1 ? 'border border-dashed border-emerald-500 bg-emerald-50/50 hover:bg-emerald-100/70 cursor-pointer transition' : 'border border-dashed border-gray-300 bg-gray-50/40') }}"
                                     @if($myUrutan === 1 && empty($ttd1?->data_ttd)) @click="showModalTtd = true; setTimeout(() => initSignaturePad(), 120)" @endif>
                                    @if (!empty($ttd1?->data_ttd))
                                        <img src="{{ $ttd1->data_ttd }}" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" alt="TTD 1">
                                    @elseif ($myUrutan === 1)
                                        <div class="text-center font-sans">
                                            <span class="text-[11px] font-bold text-emerald-800 block">✍️ Klik di Sini</span>
                                            <span class="text-[9.5px] text-emerald-600 block">untuk Tanda Tangan</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray-400 font-sans italic">[Menunggu Tanda Tangan]</span>
                                    @endif
                                </div>
                                <div class="font-bold underline text-xs sm:text-sm">{{ $p1['nama'] ?? '' }}</div>
                            </div>

                            <!-- Penandatangan 2 (Kanan) -->
                            <div>
                                <div class="text-xs sm:text-sm">{{ $p2['jabatan'] ?? '' }}</div>
                                <div class="w-[3.9cm] h-[2.6cm] mx-auto my-1.5 flex items-center justify-center p-1 rounded-sm {{ !empty($ttd2?->data_ttd) ? '' : ($myUrutan === 2 ? 'border border-dashed border-emerald-500 bg-emerald-50/50 hover:bg-emerald-100/70 cursor-pointer transition' : 'border border-dashed border-gray-300 bg-gray-50/40') }}"
                                     @if($myUrutan === 2 && empty($ttd2?->data_ttd)) @click="showModalTtd = true; setTimeout(() => initSignaturePad(), 120)" @endif>
                                    @if (!empty($ttd2?->data_ttd))
                                        <img src="{{ $ttd2->data_ttd }}" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" alt="TTD 2">
                                    @elseif ($myUrutan === 2)
                                        <div class="text-center font-sans">
                                            <span class="text-[11px] font-bold text-emerald-800 block">✍️ Klik di Sini</span>
                                            <span class="text-[9.5px] text-emerald-600 block">untuk Tanda Tangan</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray-400 font-sans italic">[Menunggu Tanda Tangan]</span>
                                    @endif
                                </div>
                                <div class="font-bold underline text-xs sm:text-sm">{{ $p2['nama'] ?? '' }}</div>
                            </div>
                        </div>

                    @else
                        <!-- 3 PENANDATANGAN SESUAI KETENTUAN BAKU:
                             PENANDATANGAN 1 MENGETAHUI DI ATAS RATA TENGAH
                             PENANDATANGAN 2 BAWAH KIRI
                             PENANDATANGAN 3 BAWAH KANAN -->
                        <div class="space-y-4">
                            <!-- Penandatangan 1 (Mengetahui di Atas Rata Tengah) -->
                            <div class="text-center font-surat text-xs">
                                <div class="italic text-[11px] text-gray-600 mb-0.5">Mengetahui,</div>
                                <div class="text-xs sm:text-sm font-semibold">{{ $p1['jabatan'] ?? '' }}</div>
                                
                                <div class="w-[3.9cm] h-[2.6cm] mx-auto my-1.5 flex items-center justify-center p-1 rounded-sm {{ !empty($ttd1?->data_ttd) ? '' : ($myUrutan === 1 ? 'border border-dashed border-emerald-500 bg-emerald-50/50 hover:bg-emerald-100/70 cursor-pointer transition' : 'border border-dashed border-gray-300 bg-gray-50/40') }}"
                                     @if($myUrutan === 1 && empty($ttd1?->data_ttd)) @click="showModalTtd = true; setTimeout(() => initSignaturePad(), 120)" @endif>
                                    @if (!empty($ttd1?->data_ttd))
                                        <img src="{{ $ttd1->data_ttd }}" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" alt="TTD 1">
                                    @elseif ($myUrutan === 1)
                                        <div class="text-center font-sans">
                                            <span class="text-[11px] font-bold text-emerald-800 block">✍️ Klik di Sini</span>
                                            <span class="text-[9.5px] text-emerald-600 block">untuk Tanda Tangan</span>
                                        </div>
                                    @else
                                        <span class="text-[10px] text-gray-400 font-sans italic">[Menunggu Tanda Tangan]</span>
                                    @endif
                                </div>
                                
                                <div class="font-bold underline text-xs sm:text-sm">{{ $p1['nama'] ?? '' }}</div>
                            </div>

                            <!-- Penandatangan 2 (Bawah Kiri) & Penandatangan 3 (Bawah Kanan) -->
                            <div class="grid grid-cols-2 gap-4 text-center font-surat text-xs pt-1">
                                <!-- Penandatangan 2: Bawah Kiri -->
                                <div>
                                    <div class="text-xs sm:text-sm">{{ $p2['jabatan'] ?? '' }}</div>
                                    <div class="w-[3.9cm] h-[2.6cm] mx-auto my-1.5 flex items-center justify-center p-1 rounded-sm {{ !empty($ttd2?->data_ttd) ? '' : ($myUrutan === 2 ? 'border border-dashed border-emerald-500 bg-emerald-50/50 hover:bg-emerald-100/70 cursor-pointer transition' : 'border border-dashed border-gray-300 bg-gray-50/40') }}"
                                         @if($myUrutan === 2 && empty($ttd2?->data_ttd)) @click="showModalTtd = true; setTimeout(() => initSignaturePad(), 120)" @endif>
                                        @if (!empty($ttd2?->data_ttd))
                                            <img src="{{ $ttd2->data_ttd }}" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" alt="TTD 2">
                                        @elseif ($myUrutan === 2)
                                            <div class="text-center font-sans">
                                                <span class="text-[11px] font-bold text-emerald-800 block">✍️ Klik di Sini</span>
                                                <span class="text-[9.5px] text-emerald-600 block">untuk Tanda Tangan</span>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-gray-400 font-sans italic">[Menunggu Tanda Tangan]</span>
                                        @endif
                                    </div>
                                    <div class="font-bold underline text-xs sm:text-sm">{{ $p2['nama'] ?? '' }}</div>
                                </div>

                                <!-- Penandatangan 3: Bawah Kanan -->
                                <div>
                                    <div class="text-xs sm:text-sm">{{ $p3['jabatan'] ?? '' }}</div>
                                    <div class="w-[3.9cm] h-[2.6cm] mx-auto my-1.5 flex items-center justify-center p-1 rounded-sm {{ !empty($ttd3?->data_ttd) ? '' : ($myUrutan === 3 ? 'border border-dashed border-emerald-500 bg-emerald-50/50 hover:bg-emerald-100/70 cursor-pointer transition' : 'border border-dashed border-gray-300 bg-gray-50/40') }}"
                                         @if($myUrutan === 3 && empty($ttd3?->data_ttd)) @click="showModalTtd = true; setTimeout(() => initSignaturePad(), 120)" @endif>
                                        @if (!empty($ttd3?->data_ttd))
                                            <img src="{{ $ttd3->data_ttd }}" class="max-w-[3.9cm] max-h-[2.6cm] object-contain" alt="TTD 3">
                                        @elseif ($myUrutan === 3)
                                            <div class="text-center font-sans">
                                                <span class="text-[11px] font-bold text-emerald-800 block">✍️ Klik di Sini</span>
                                                <span class="text-[9.5px] text-emerald-600 block">untuk Tanda Tangan</span>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-gray-400 font-sans italic">[Menunggu Tanda Tangan]</span>
                                        @endif
                                    </div>
                                    <div class="font-bold underline text-xs sm:text-sm">{{ $p3['nama'] ?? '' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- QR CODE & KODE VERIFIKASI: SELALU DI POJOK KANAN PALING BAWAH LEMBAR SURAT -->
                <div class="absolute right-8 sm:right-12 bottom-6 sm:bottom-8 text-right font-sans text-gray-700">
                    <div class="inline-block text-right">
                        @if (!empty($qrCode))
                            <img src="{{ $qrCode }}" alt="QR Verifikasi" class="w-14 h-14 ml-auto mb-1 border border-gray-300 rounded p-0.5 bg-white shadow-2xs" />
                        @endif
                        <div class="text-[9px] text-gray-600 leading-tight">
                            Kode Verifikasi: <strong class="font-mono text-gray-900">{{ $surat->kode_verifikasi }}</strong>
                        </div>
                    </div>
                </div>

            </div>
        </main>

        <!-- MODAL KANVAS TANDA TANGAN (KOTAK PROPORSIONAL RASIO 3:2 PERSIS SESUAI KOTAK PDF) -->
        <div x-show="showModalTtd" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4">
            <div class="bg-white rounded-2xl max-w-lg w-full p-5 sm:p-6 space-y-4 shadow-2xl relative border border-gray-200"
                 @click.away="showModalTtd = false">
                
                <div class="flex items-center justify-between border-b pb-3">
                    <div>
                        <h3 class="font-extrabold text-sm sm:text-base text-gray-900 flex items-center gap-1.5">
                            <span>✍️ Konfirmasi Penandatanganan Dokumen</span>
                        </h3>
                        <p class="text-xs text-gray-600 mt-0.5">
                            Yth. <strong class="text-gray-900">{{ $tandaTangan->nama_penandatangan }}</strong> &bull; {{ $tandaTangan->jabatan_penandatangan }}
                        </p>
                    </div>
                    <button type="button" @click="showModalTtd = false" class="text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="space-y-3">
                    <p class="text-xs text-gray-600 text-center">
                        Bubuhkan goresan tanda tangan Anda menggunakan jari di layar sentuh atau mouse pada kotak proporsional (rasio 3:2) berikut:
                    </p>

                    <!-- KOTAK KANVAS PROPORSIONAL RASIO 3:2 (380x253 / 300x200) -->
                    <div class="flex justify-center">
                        <div class="w-[300px] sm:w-[380px] h-[200px] sm:h-[253px] border-2 border-dashed border-emerald-500 rounded-xl bg-white overflow-hidden shadow-inner flex items-center justify-center relative ring-4 ring-emerald-50">
                            <canvas id="publicSignatureCanvas" width="380" height="253" class="touch-none bg-white w-full h-full cursor-crosshair"></canvas>
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-2 text-xs">
                        <span class="text-[11px] text-gray-400 font-sans">Ukuran rasio 3:2 presisi sesuai PDF</span>
                        <button type="button" id="clearPublicCanvasBtn" class="text-rose-600 hover:text-rose-800 font-bold underline">
                            Bersihkan Kanvas
                        </button>
                    </div>
                </div>

                <!-- FORM SUBMIT -->
                <form method="POST" action="{{ route('publik.tanda-tangan.sign', $token) }}" id="formPublicSign">
                    @csrf
                    <input type="hidden" name="data_ttd" id="publicInputDataTtd">

                    <div class="flex items-center justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showModalTtd = false"
                                class="px-4 py-2 border border-gray-300 text-gray-700 text-xs font-semibold rounded-lg hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="button" id="submitPublicSignBtn"
                                class="px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs rounded-lg shadow-sm transition">
                            Simpan & Bubuhkan ke Surat
                        </button>
                    </div>
                </form>
            </div>
        </div>

    @elseif ($status === 'success')
        <div class="flex-1 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-emerald-200 shadow-2xl p-6 sm:p-8 text-center space-y-4 max-w-md w-full">
                <div class="w-16 h-16 mx-auto bg-emerald-100 text-emerald-700 rounded-full flex items-center justify-center text-3xl font-black shadow-inner">
                    ✓
                </div>
                <h2 class="text-xl font-black text-gray-900">Alhamdulillah, Dokumen Berhasil Ditandatangani</h2>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                    {{ $pesan }} Surat dengan nomor <strong>{{ $surat->no_surat }}</strong> kini telah resmi dilengkapi tanda tangan Anda dan QR Code verifikasi keaslian.
                </p>
                <div class="pt-2">
                    <a href="{{ route('publik.verifikasi', $surat->kode_verifikasi) }}"
                       class="inline-block px-5 py-2.5 bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-sm transition">
                        Buka Halaman Verifikasi Surat
                    </a>
                </div>
            </div>
        </div>
    @else
        <!-- Expired / Invalid -->
        <div class="flex-1 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl border border-amber-200 shadow-2xl p-6 sm:p-8 text-center space-y-4 max-w-md w-full">
                <div class="w-16 h-16 mx-auto bg-amber-100 text-amber-700 rounded-full flex items-center justify-center text-3xl font-black shadow-inner">
                    ⏱️
                </div>
                <h2 class="text-xl font-black text-gray-900">Tautan Tidak Berlaku</h2>
                <p class="text-xs sm:text-sm text-gray-600 leading-relaxed">
                    {{ $pesan }}
                </p>
                <div class="pt-2">
                    <p class="text-[11px] text-gray-400">Silakan hubungi bagian sekretariat pondok pesantren untuk meminta tautan baru.</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Footer Lembar -->
    <footer class="bg-white/80 border-t border-gray-300 py-3 text-center text-[11px] text-gray-500">
        &copy; {{ date('Y') }} {{ $pengaturan['nama_lembaga'] ?? 'Pondok Pesantren Al Falah Putra' }} &mdash; Dokumen Administrasi Elektronik
    </footer>

    @if ($status === 'active')
    <script src="{{ asset('js/signature_pad.umd.min.js') }}"></script>
    <script>
        let publicSignaturePad = null;

        function initSignaturePad() {
            setTimeout(() => {
                const canvas = document.getElementById('publicSignatureCanvas');
                if (!canvas) return;

                const rect = canvas.getBoundingClientRect();
                const displayWidth = Math.round(rect.width) || 380;
                const displayHeight = Math.round(rect.height) || 253;
                const ratio = Math.max(window.devicePixelRatio || 1, 1);

                canvas.width = displayWidth * ratio;
                canvas.height = displayHeight * ratio;
                const ctx = canvas.getContext('2d');
                ctx.scale(ratio, ratio);

                if (!publicSignaturePad) {
                    publicSignaturePad = new SignaturePad(canvas, {
                        backgroundColor: 'rgb(255, 255, 255)',
                        penColor: 'rgb(15, 23, 42)',
                        minWidth: 1.5,
                        maxWidth: 3.5
                    });
                } else {
                    publicSignaturePad.clear();
                }
            }, 100);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const clearBtn = document.getElementById('clearPublicCanvasBtn');
            if (clearBtn) {
                clearBtn.addEventListener('click', () => {
                    if (publicSignaturePad) publicSignaturePad.clear();
                });
            }

            const submitBtn = document.getElementById('submitPublicSignBtn');
            if (submitBtn) {
                submitBtn.addEventListener('click', () => {
                    if (!publicSignaturePad || publicSignaturePad.isEmpty()) {
                        alert('Mohon bubuhkan goresan tanda tangan terlebih dahulu pada kanvas sebelum menyimpan.');
                        return;
                    }
                    const dataUrl = publicSignaturePad.toDataURL('image/png');
                    document.getElementById('publicInputDataTtd').value = dataUrl;
                    document.getElementById('formPublicSign').submit();
                });
            }
        });
    </script>
    @endif
</body>
</html>
