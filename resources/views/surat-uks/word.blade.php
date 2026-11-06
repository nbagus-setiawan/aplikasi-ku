@php
    $alamatLembaga = $pengaturans['alamat_lembaga'] ?? 'Desa Putak, Kec. Gelumbang, Kab. Muara Enim, Sumatera Selatan 31171';
@endphp
<html xmlns:o='urn:schemas-microsoft-com:office:office'
      xmlns:w='urn:schemas-microsoft-com:office:word'
      xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>{{ $suratUk->nomor_surat }}</title>
    <!--[if gte mso 9]>
    <xml>
        <w:WordDocument>
            <w:View>Print</w:View>
            <w:Zoom>100</w:Zoom>
            <w:DoNotOptimizeForBrowser/>
        </w:WordDocument>
    </xml>
    <![endif]-->
    <style>
        @page Section1 {
            size: 7.0cm 29.7cm; /* A4 bagi 3 */
            margin: 0.8cm 0.6cm 0.8cm 0.6cm;
            mso-header-margin: 0.5cm;
            mso-footer-margin: 0.5cm;
            mso-paper-source: 0;
        }
        div.Section1 {
            page: Section1;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11.5pt;
            line-height: 1.3;
            color: #000;
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
            color: #064e3b;
            text-transform: uppercase;
            line-height: 1.2;
        }
        .kop-pondok {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            margin-top: 2px;
        }
        .kop-unit {
            font-size: 11.5pt;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.2;
            margin-top: 2px;
        }
        .kop-alamat {
            font-size: 9pt;
            color: #333;
            margin-top: 3px;
            line-height: 1.2;
        }
        .judul-surat {
            text-align: center;
            margin: 8px 0;
        }
        .kode-box {
            text-align: center;
            margin-bottom: 6px;
            font-family: Arial, sans-serif;
        }
        .kode-tag {
            font-size: 10pt;
            font-weight: bold;
            color: #064e3b;
        }
        .kode-sub {
            font-size: 7.5pt;
            color: #444;
            margin-top: 2px;
        }
        .judul-surat h2 {
            font-size: 11.5pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 4px 0 2px 0;
        }
        .judul-surat p {
            font-size: 11.5pt;
            margin: 0;
            font-weight: bold;
        }
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 6px 0;
        }
        table.meta-table td {
            padding: 3px 0;
            vertical-align: top;
            font-size: 11.5pt;
        }
        .ttd-area {
            margin-top: 15px;
            font-size: 11.5pt;
        }
        .ttd-ruang {
            height: 45px;
        }
        .ttd-nama {
            font-weight: bold;
            font-size: 11.5pt;
        }
    </style>
</head>
<body>
<div class="Section1">
    <!-- Kop Surat -->
    <div class="header-kop">
        <div class="kop-instansi">YAYASAN PONDOK PESANTREN AL-FALAH</div>
        <div class="kop-pondok">PONDOK PUTRA</div>
        <div class="kop-unit">UNIT KESEHATAN SANTRI PUTRA</div>
        <div class="kop-alamat">{{ $alamatLembaga }}</div>
    </div>

    <!-- Judul & Nomor Surat + Kode 4 Digit -->
    <div class="judul-surat">
        <div class="kode-box">
            <div class="kode-tag">KODE: {{ $suratUk->kode_verifikasi }}</div>
            <div class="kode-sub">Verifikasi: https://ponpesalfalahputak.my.id/verifikasi</div>
        </div>
        <h2>{{ $suratUk->jenis === 'sakit' ? 'SURAT KETERANGAN IZIN SAKIT' : 'SURAT KETERANGAN IZIN PULANG' }}</h2>
        <p>Nomor: {{ $suratUk->nomor_surat }}</p>
    </div>

    <p style="text-align: justify; margin: 6px 0; font-size: 11.5pt;">
        Yang bertanda tangan di bawah ini menerangkan bahwa santri tersebut di bawah ini:
    </p>

    <table class="meta-table">
        <tr>
            <td style="width: 32%;">Nama</td>
            <td style="width: 4%; text-align: center;">:</td>
            <td style="width: 64%;">
                @if (!empty($suratUk->nama_santri))
                    <strong>{{ strtoupper($suratUk->nama_santri) }}</strong>
                @else
                    ......................................................................
                @endif
            </td>
        </tr>
        <tr>
            <td>Kelas</td>
            <td style="text-align: center;">:</td>
            <td>
                @if (!empty($suratUk->kelas))
                    <strong>{{ $suratUk->kelas }}</strong>
                @else
                    ......................................................................
                @endif
            </td>
        </tr>
        <tr>
            <td>Asrama</td>
            <td style="text-align: center;">:</td>
            <td>
                @if (!empty($suratUk->asrama))
                    <strong>{{ $suratUk->asrama }}</strong>
                @else
                    ......................................................................
                @endif
            </td>
        </tr>
        <tr>
            <td>{{ $suratUk->jenis === 'sakit' ? 'Keluhan' : 'Alasan' }}</td>
            <td style="text-align: center;">:</td>
            <td>......................................................................</td>
        </tr>
        <tr>
            <td>Masa Izin</td>
            <td style="text-align: center;">:</td>
            <td>Tgl. ........................................<br>s/d Tgl. ........................................</td>
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
</body>
</html>
