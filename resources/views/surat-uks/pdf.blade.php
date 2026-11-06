@php
    $alamatLembaga = $pengaturans['alamat_lembaga'] ?? 'Desa Putak, Kec. Gelumbang, Kab. Muara Enim, Sumatera Selatan 31171';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Blangko Surat UKS - Al-Falah Putra</title>
    <style>
        @page {
            size: 70mm 297mm portrait;
            margin: 5mm 4mm 5mm 4mm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            font-size: 11.5pt;
            @if (!empty($isHtmlPrint))
            background: #f1f5f9;
            padding-bottom: 30px;
            @endif
        }

        .page-container {
            page-break-after: always;
            box-sizing: border-box;
            width: 70mm;
            min-height: 297mm;
            @if (!empty($isHtmlPrint))
            background: #ffffff;
            margin: 15px auto;
            padding: 5mm 4mm;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 4px;
            @endif
        }
        .page-container:last-child {
            page-break-after: avoid;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: #ffffff !important; padding: 0 !important; margin: 0 !important; }
            .page-container {
                margin: 0 !important;
                padding: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
                width: 70mm !important;
                min-height: 297mm !important;
                page-break-after: always;
                page-break-inside: avoid;
            }
            .page-container:last-child {
                page-break-after: auto;
            }
        }

        .header-kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .kop-instansi {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            color: #064e3b;
            margin: 0;
        }
        .kop-pondok {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            margin: 2px 0 0 0;
            color: #111;
        }
        .kop-unit {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            margin: 2px 0 0 0;
            color: #111;
        }
        .kop-alamat {
            font-size: 9pt;
            color: #333;
            margin-top: 3px;
            line-height: 1.2;
        }

        .judul-surat {
            text-align: center;
            margin: 6px 0 8px 0;
        }
        .kode-box {
            text-align: center;
            margin-bottom: 6px;
            font-family: Arial, sans-serif;
        }
        .kode-tag {
            display: inline-block;
            border: 1.5px solid #064e3b;
            background: #f0fdf4;
            color: #064e3b;
            padding: 2px 8px;
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .kode-sub {
            font-size: 7.5pt;
            color: #444;
            margin-top: 2px;
            word-break: break-all;
        }
        .judul-surat h2 {
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 5px 0 2px 0;
            line-height: 1.25;
        }
        .judul-surat p {
            font-size: 11.5pt;
            font-weight: bold;
            margin: 0;
        }

        .paragraf {
            text-align: justify;
            margin: 8px 0 6px 0;
            font-size: 11.5pt;
            line-height: 1.35;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        .table-data td {
            vertical-align: top;
            padding: 3.5px 0;
            font-size: 11.5pt;
            line-height: 1.3;
        }
        .lbl { width: 34%; }
        .sep { width: 4%; text-align: center; }
        .val { width: 62%; font-weight: normal; }

        .titik-titik {
            display: block;
            border-bottom: 1px dotted #333;
            min-height: 16px;
            width: 100%;
        }

        .ttd-area {
            margin-top: 14px;
            font-size: 11.5pt;
        }
        .ttd-ruang {
            height: 48px;
        }
        .ttd-nama {
            font-weight: bold;
            font-size: 11.5pt;
        }
    </style>
</head>
<body>

@if (!empty($isHtmlPrint))
    <div class="no-print" style="position: sticky; top: 0; z-index: 9999; background: #064e3b; color: #ffffff; padding: 10px 14px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 8px; font-family: sans-serif; box-shadow: 0 2px 10px rgba(0,0,0,0.25);">
        <div style="flex: 1 1 240px;">
            <strong style="font-size: 13px; display: block; color: #ecfdf5;">Pratinjau Blangko Surat UKS (70 mm × 297 mm)</strong>
            <p style="margin: 2px 0 0; font-size: 10.5px; color: #a7f3d0;">Ukuran kertas A4 bagi 3. Gunakan Ctrl+P untuk mencetak.</p>
        </div>
        <div style="display: flex; gap: 6px; flex-shrink: 0;">
            <button onclick="window.print();" type="button" style="background: #10b981; color: white; border: none; padding: 7px 14px; border-radius: 6px; font-weight: bold; font-size: 11.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / PDF</span>
            </button>
            <a href="{{ route('admin.surat-uks.index') }}" style="background: rgba(255,255,255,0.18); color: white; text-decoration: none; padding: 7px 12px; border-radius: 6px; font-size: 11.5px; font-weight: 500;">
                &larr; Kembali
            </a>
        </div>
    </div>
    <script>
        window.addEventListener('load', function() {
            setTimeout(function() {
                window.print();
            }, 600);
        });
    </script>
@endif

@foreach ($suratList as $suratUk)
    <div class="page-container">
        <!-- Header Kop Surat Sesuai Permintaan -->
        <div class="header-kop">
            <div class="kop-instansi">YAYASAN PONDOK PESANTREN AL-FALAH</div>
            <div class="kop-pondok">PONDOK PUTRA</div>
            <div class="kop-unit">UNIT KESEHATAN SANTRI PUTRA</div>
            <div class="kop-alamat">{{ $alamatLembaga }}</div>
        </div>

        <!-- Judul, Nomor Surat & Kode Verifikasi -->
        <div class="judul-surat">
            <div class="kode-box">
                <div class="kode-tag">KODE: {{ $suratUk->kode_verifikasi }}</div>
                <div class="kode-sub">Verifikasi: https://ponpesalfalahputak.my.id/verifikasi</div>
            </div>
            <h2>{{ $suratUk->jenis === 'sakit' ? 'SURAT KETERANGAN IZIN SAKIT' : 'SURAT KETERANGAN IZIN PULANG' }}</h2>
            <p>Nomor: {{ $suratUk->nomor_surat }}</p>
        </div>

        <!-- Pembuka Surat -->
        <p class="paragraf">
            Yang bertanda tangan di bawah ini menerangkan bahwa santri tersebut di bawah ini:
        </p>

        <!-- Tabel Data Isian Santri -->
        <table class="table-data">
            <tr>
                <td class="lbl">Nama</td>
                <td class="sep">:</td>
                <td class="val">
                    @if (!empty($suratUk->nama_santri))
                        <strong>{{ strtoupper($suratUk->nama_santri) }}</strong>
                    @else
                        <span class="titik-titik"></span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="lbl">Kelas</td>
                <td class="sep">:</td>
                <td class="val">
                    @if (!empty($suratUk->kelas))
                        <strong>{{ $suratUk->kelas }}</strong>
                    @else
                        <span class="titik-titik"></span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="lbl">Asrama</td>
                <td class="sep">:</td>
                <td class="val">
                    @if (!empty($suratUk->asrama))
                        <strong>{{ $suratUk->asrama }}</strong>
                    @else
                        <span class="titik-titik"></span>
                    @endif
                </td>
            </tr>
            <tr>
                <td class="lbl">{{ $suratUk->jenis === 'sakit' ? 'Keluhan' : 'Alasan' }}</td>
                <td class="sep">:</td>
                <td class="val">
                    <span class="titik-titik"></span>
                </td>
            </tr>
            <tr>
                <td class="lbl">Masa Izin</td>
                <td class="sep">:</td>
                <td class="val">
                    Tgl. ........................................<br>s/d Tgl. ........................................
                </td>
            </tr>
        </table>

        <!-- Tanda Tangan Sesuai Ketentuan -->
        <div class="ttd-area">
            <div style="text-align: right; margin-bottom: 6px; font-size: 11.5pt;">
                Putak, .................................... {{ (int) date('Y') }}
            </div>

            @if ($suratUk->jenis === 'pulang')
                {{-- Surat Izin Pulang: TTD Pengasuh Pondok KH. MURSYIDI --}}
                <div style="text-align: center; margin-top: 14px; font-size: 11.5pt;">
                    <div>Pengasuh Pondok Pesantren,</div>
                    <div class="ttd-ruang"></div>
                    <div class="ttd-nama">KH. MURSYIDI</div>
                </div>
            @else
                {{-- Surat Izin Sakit: 2 TTD (Sekretaris Pondok Putra & Pengurus Kesehatan Putra) --}}
                <div style="text-align: center; margin-top: 12px; font-size: 11.5pt;">
                    <div>Sekretaris Pondok Putra,</div>
                    <div class="ttd-ruang"></div>
                    <div class="ttd-nama">( .................................... )</div>
                </div>

                <div style="text-align: center; margin-top: 18px; font-size: 11.5pt;">
                    <div>Pengurus Kesehatan Putra,</div>
                    <div class="ttd-ruang"></div>
                    <div class="ttd-nama">( .................................... )</div>
                </div>
            @endif
        </div>
    </div>
@endforeach

</body>
</html>
