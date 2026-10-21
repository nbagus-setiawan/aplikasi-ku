@php
    /*
     * Template lembar surat keluar.
     * Dipakai bersama oleh:
     *  - PDF (Dompdf)            -> $modeHtml = false
     *  - Pratinjau HTML (iframe) -> $modeHtml = true (tampilan identik, belum menjadi PDF)
     */
    $modeHtml = $modeHtml ?? false;

    $marginMap = [
        'ringkas' => ['1.5cm', '2cm', '1.5cm', '2cm'],
        'lebar' => ['2.5cm', '3cm', '2.5cm', '2.5cm'],
        'standar' => ['2cm', '2.5cm', '2cm', '2.5cm'],
    ];
    [$mTop, $mRight, $mBottom, $mLeft] = $marginMap[$surat->margin ?? 'standar'] ?? $marginMap['standar'];
    $isF4 = ($surat->ukuran_kertas ?? 'a4') === 'f4';
    $paperW = $isF4 ? '215mm' : '210mm';
    $paperH = $isF4 ? '330mm' : '297mm';

    $jml = max(1, min(3, (int) ($surat->jumlah_penandatangan ?? 1)));
    $daftar = $surat->penandatangans ?? [];
    $p1 = $daftar[0] ?? ['nama' => $surat->nama_penandatangan, 'jabatan' => $surat->jabatan_penandatangan];
    $p2 = $daftar[1] ?? ['nama' => '-', 'jabatan' => '-'];
    $p3 = $daftar[2] ?? ['nama' => '-', 'jabatan' => '-'];

    $tandaTangans = $tandaTangans ?? collect();
    $ttd1 = $tandaTangans[1] ?? null;
    $ttd2 = $tandaTangans[2] ?? null;
    $ttd3 = $tandaTangans[3] ?? null;

    $cleanIsi = preg_replace('/<!--.*?-->/s', '', $surat->isi ?? '');
    $cleanIsi = preg_replace('/<p[^>]*>(?:\s|&nbsp;|<br\s*\/?>)*<\/p>/i', '', $cleanIsi);
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        @page {
            margin: {{ $mTop }} {{ $mRight }} {{ $mBottom }} {{ $mLeft }};
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.45;
            color: #111;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 10px;
            border-collapse: collapse;
        }
        .meta-table td {
            vertical-align: top;
            padding: 2px 0;
            font-size: 10.5pt;
        }
        .meta-label { width: 15%; }
        .meta-sep { width: 3%; }
        .meta-val { width: 45%; }
        .meta-date { width: 37%; text-align: right; }
        .tujuan-box {
            margin-top: 10px;
            margin-bottom: 12px;
            font-size: 11pt;
        }
        .isi-surat {
            margin-bottom: 15px;
            font-size: 11pt;
            line-height: 1.45;
            text-align: justify;
        }
        .isi-surat p { margin: 0 0 6px 0; }
        .isi-surat table {
            width: 100%;
            border-collapse: collapse;
            margin: 12px 0;
        }
        .isi-surat table, .isi-surat th, .isi-surat td {
            border: 1px solid #333;
            padding: 6px 8px;
            font-size: 11pt;
        }
        .isi-surat th {
            background-color: #f3f4f6;
            font-weight: bold;
            text-align: center;
        }
        .isi-surat img { max-width: 100%; height: auto; }
        .isi-surat ul, .isi-surat ol {
            margin: 0 0 10px 24px;
            padding-left: 10px;
        }
        .isi-surat li { margin-bottom: 4px; }

        /* ===== Tata letak penandatangan =====
           Kotak tanda tangan berukuran tetap 3,9 cm x 2,6 cm (rasio 3:2),
           sama persis dengan rasio kanvas tanda tangan di website. */
        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            page-break-inside: avoid;
        }
        .ttd-table td {
            text-align: center;
            padding: 0 6px;
            border: none;
        }
        .ttd-label {
            font-style: italic;
            font-size: 10.5pt;
            vertical-align: bottom;
        }
        .ttd-jabatan {
            vertical-align: bottom;
            font-size: 11pt;
        }
        .ttd-kotak-cell {
            height: 2.6cm;
            vertical-align: middle;
        }
        .ttd-kotak {
            width: 3.9cm;
            height: 2.6cm;
            margin: 0 auto;
        }
        .ttd-kotak img {
            max-width: 3.9cm;
            max-height: 2.6cm;
            width: auto;
            height: auto;
            display: block;
            margin: 0 auto;
        }
        .ttd-nama {
            vertical-align: top;
            font-weight: bold;
            text-decoration: underline;
        }

        /* QR & kode verifikasi: selalu di pojok kanan paling bawah */
        .qr-box {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 6cm;
            font-size: 7.5pt;
            color: #333;
            line-height: 1.2;
            text-align: right;
        }
        .qr-box img {
            width: 58px;
            height: 58px;
            display: block;
            margin: 0 0 3px auto;
        }

        @if ($modeHtml)
        /* ===== Mode pratinjau HTML: tampilkan sebagai lembar kertas ===== */
        html { background: #e5e7eb; }
        body { padding: 16px 0; }
        .lembar {
            position: relative;
            box-sizing: border-box;
            width: {{ $paperW }};
            min-height: {{ $paperH }};
            margin: 0 auto;
            padding: {{ $mTop }} {{ $mRight }} {{ $mBottom }} {{ $mLeft }};
            background: #fff;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .18);
        }
        .lembar .qr-box {
            right: {{ $mRight }};
            bottom: {{ $mBottom }};
        }
        .halaman-lampiran {
            box-sizing: border-box;
            width: {{ $paperW }};
            margin: 16px auto 0;
            padding: {{ $mTop }} {{ $mRight }} {{ $mBottom }} {{ $mLeft }};
            background: #fff;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .18);
        }
        @endif
    </style>
</head>
<body>
<div class="lembar">
    <!-- KOP SURAT -->
    @if (!empty($kopGambar))
        <div style="text-align: center; margin-bottom: 15px; border-bottom: 2px solid #000; padding-bottom: 5px;">
            <img src="{{ $kopGambar }}" style="width: 100%; max-height: 130px; height: auto; display: block;" alt="Kop Surat" />
        </div>
    @else
        <div class="header-kop">
            <table style="width: 100%; border-collapse: collapse; border: none; margin: 0;">
                <tr>
                    @if (!empty($logo))
                        <td style="width: 80px; vertical-align: middle; text-align: center; border: none; padding: 0;">
                            <img src="{{ $logo }}" style="width: 75px; height: auto;" alt="Logo">
                        </td>
                    @endif
                    <td style="vertical-align: middle; text-align: center; border: none; padding: 0 10px;">
                        <div style="font-size: 12.5pt; font-weight: bold; letter-spacing: 1px; margin: 0; text-transform: uppercase;">
                            {{ $pengaturan['kop_yayasan'] ?? 'YAYASAN PONDOK PESANTREN AL-FALAH PUTAK' }}
                        </div>
                        <div style="font-size: 15pt; font-weight: bold; letter-spacing: 1.5px; margin: 2px 0; text-transform: uppercase;">
                            {{ $pengaturan['kop_nama'] ?? 'PONDOK PESANTREN AL-FALAH PUTRA' }}
                        </div>
                        <div style="font-size: 9pt; margin: 1px 0;">
                            {{ $pengaturan['kop_alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171' }}
                        </div>
                        <div style="font-size: 9pt; margin: 1px 0;">
                            {{ $pengaturan['kop_kontak'] ?? 'No. Telp/HP: 0812-7890-3117 | Email: sekretariat@alfalahputra.sch.id' }}
                        </div>
                    </td>
                    @if (!empty($logo))
                        <td style="width: 80px; border: none; padding: 0;"></td>
                    @endif
                </tr>
            </table>
        </div>
    @endif

    <!-- META DATA SURAT -->
    <table class="meta-table">
        <tr>
            <td class="meta-label">Nomor</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><strong>{{ $surat->no_surat }}</strong></td>
            <td class="meta-date">{{ $surat->tempat_surat ?: 'Putak' }}, {{ $surat->tgl_surat ? $surat->tgl_surat->isoFormat('D MMMM Y') : date('d-m-Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Lampiran</td>
            <td class="meta-sep">:</td>
            <td class="meta-val">
                @if (!empty($surat->lampiran_teks) && $surat->lampiran_teks !== '-')
                    {{ $surat->lampiran_teks }}
                @elseif (!empty($surat->file))
                    {{ $lampiranNama ?? '1 (Satu) Berkas' }}
                @else
                    -
                @endif
            </td>
            <td class="meta-date"></td>
        </tr>
        <tr>
            <td class="meta-label">Perihal</td>
            <td class="meta-sep">:</td>
            <td class="meta-val"><strong>{{ $surat->perihal }}</strong></td>
            <td class="meta-date"></td>
        </tr>
    </table>

    <!-- TUJUAN SURAT -->
    <div class="tujuan-box">
        <div>Kepada Yth.</div>
        <div style="font-weight: bold; margin-top: 2px;">{{ $surat->tujuan }}</div>
        <div>di - Tempat</div>
    </div>

    <!-- ISI SURAT -->
    <div class="isi-surat">
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

    <!-- TANDA TANGAN -->
    @if ($jml === 1)
        {{-- 1 penandatangan: di sisi kanan --}}
        <table class="ttd-table" style="margin-top: 14px;">
            <tr>
                <td style="width: 50%;"></td>
                <td class="ttd-jabatan" style="width: 50%;">{{ $p1['jabatan'] ?? '' }}</td>
            </tr>
            <tr>
                <td></td>
                <td class="ttd-kotak-cell">
                    <div class="ttd-kotak">@if (!empty($ttd1?->data_ttd))<img src="{{ $ttd1->data_ttd }}" alt="TTD">@endif</div>
                </td>
            </tr>
            <tr>
                <td></td>
                <td class="ttd-nama">{{ $p1['nama'] ?? '' }}</td>
            </tr>
        </table>

    @elseif ($jml === 2)
        {{-- 2 penandatangan: kiri (P1) dan kanan (P2), sejajar presisi per baris --}}
        <table class="ttd-table" style="margin-top: 14px;">
            <tr>
                <td class="ttd-jabatan" style="width: 50%;">{{ $p1['jabatan'] ?? '' }}</td>
                <td class="ttd-jabatan" style="width: 50%;">{{ $p2['jabatan'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="ttd-kotak-cell">
                    <div class="ttd-kotak">@if (!empty($ttd1?->data_ttd))<img src="{{ $ttd1->data_ttd }}" alt="TTD 1">@endif</div>
                </td>
                <td class="ttd-kotak-cell">
                    <div class="ttd-kotak">@if (!empty($ttd2?->data_ttd))<img src="{{ $ttd2->data_ttd }}" alt="TTD 2">@endif</div>
                </td>
            </tr>
            <tr>
                <td class="ttd-nama">{{ $p1['nama'] ?? '' }}</td>
                <td class="ttd-nama">{{ $p2['nama'] ?? '' }}</td>
            </tr>
        </table>

    @else
        {{-- 3 penandatangan: Penandatangan 1 (Mengetahui) di atas rata tengah, Penandatangan 2 bawah kiri, Penandatangan 3 bawah kanan --}}
        <table class="ttd-table" style="margin-top: 14px;">
            <tr>
                <td style="width: 20%;"></td>
                <td class="ttd-label" style="width: 60%;">Mengetahui,</td>
                <td style="width: 20%;"></td>
            </tr>
            <tr>
                <td></td>
                <td class="ttd-jabatan">{{ $p1['jabatan'] ?? '' }}</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td class="ttd-kotak-cell">
                    <div class="ttd-kotak">@if (!empty($ttd1?->data_ttd))<img src="{{ $ttd1->data_ttd }}" alt="TTD 1">@endif</div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td class="ttd-nama">{{ $p1['nama'] ?? '' }}</td>
                <td></td>
            </tr>
        </table>
        <table class="ttd-table" style="margin-top: 12px;">
            <tr>
                <td class="ttd-jabatan" style="width: 50%;">{{ $p2['jabatan'] ?? '' }}</td>
                <td class="ttd-jabatan" style="width: 50%;">{{ $p3['jabatan'] ?? '' }}</td>
            </tr>
            <tr>
                <td class="ttd-kotak-cell">
                    <div class="ttd-kotak">@if (!empty($ttd2?->data_ttd))<img src="{{ $ttd2->data_ttd }}" alt="TTD 2">@endif</div>
                </td>
                <td class="ttd-kotak-cell">
                    <div class="ttd-kotak">@if (!empty($ttd3?->data_ttd))<img src="{{ $ttd3->data_ttd }}" alt="TTD 3">@endif</div>
                </td>
            </tr>
            <tr>
                <td class="ttd-nama">{{ $p2['nama'] ?? '' }}</td>
                <td class="ttd-nama">{{ $p3['nama'] ?? '' }}</td>
            </tr>
        </table>
    @endif

    <!-- QR & KODE VERIFIKASI: SELALU DI POJOK KANAN PALING BAWAH HALAMAN -->
    <div class="qr-box">
        <img src="{{ $qrCode }}" alt="QR Verifikasi" />
        <div>Kode Verifikasi: <strong>{{ $surat->kode_verifikasi }}</strong></div>
    </div>
</div>

<!-- HALAMAN LAMPIRAN DOKUMEN (JIKA BERKAS LAMPIRAN BERUPA GAMBAR) -->
@if (!empty($lampiranGambar))
    <div class="halaman-lampiran" style="page-break-before: always; text-align: center;">
        <div style="border-bottom: 2px solid #333; padding-bottom: 8px; margin-bottom: 20px;">
            <h3 style="font-size: 13pt; margin: 0; text-transform: uppercase;">LAMPIRAN SURAT KELUAR</h3>
            <p style="font-size: 9.5pt; color: #555; margin: 3px 0 0 0;">Nomor Surat: {{ $surat->no_surat }} | Berkas: {{ $lampiranNama }}</p>
        </div>
        <div style="margin-top: 20px;">
            <img src="{{ $lampiranGambar }}" style="max-width: 95%; max-height: 22cm; height: auto; border: 1px solid #ccc; padding: 4px;" alt="Lampiran Dokumen" />
        </div>
    </div>
@endif
</body>
</html>
