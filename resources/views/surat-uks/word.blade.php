@php
    $namaLembaga = $pengaturans['nama_lembaga'] ?? 'Pondok Pesantren Al-Falah Putra';
    $alamatLembaga = $pengaturans['alamat_lembaga'] ?? 'Desa Putak, Kec. Gelumbang, Kab. Muara Enim, Sumatera Selatan 31171';
    $kontakLembaga = $pengaturans['kontak_lembaga'] ?? 'Telp/WA: 0812-3456-7890 | Website: alfalahputra.ponpes.id';
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
            size: 21.0cm 29.7cm; /* A4 */
            margin: 1.5cm 2.0cm 1.5cm 2.0cm;
            mso-header-margin: 1.0cm;
            mso-footer-margin: 1.0cm;
            mso-paper-source: 0;
        }
        div.Section1 {
            page: Section1;
        }
        body {
            font-family: 'Times New Roman', serif;
            font-size: 11pt;
            line-height: 1.35;
            color: #000;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 6px;
            margin-bottom: 12px;
        }
        .kop-instansi {
            font-size: 13pt;
            font-weight: bold;
            color: #064e3b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .kop-unit {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .kop-alamat {
            font-size: 8.5pt;
            color: #333;
            margin-top: 3px;
        }
        .judul-surat {
            text-align: center;
            margin: 10px 0 14px 0;
        }
        .judul-surat h2 {
            font-size: 12pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0;
        }
        .judul-surat p {
            font-size: 10pt;
            margin: 3px 0 0 0;
            font-weight: bold;
        }
        table.meta-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
        }
        table.meta-table td {
            padding: 4px 0;
            vertical-align: top;
            font-size: 10.5pt;
        }
        .kotak-catatan {
            border: 1px solid #64748b;
            background: #f8fafc;
            padding: 6px 10px;
            margin: 10px 0;
            font-size: 9pt;
        }
        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table.ttd-table td {
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
            width: 33.33%;
        }
    </style>
</head>
<body>
<div class="Section1">
    <!-- Kop Surat -->
    <div class="header-kop">
        <div class="kop-instansi">{{ $namaLembaga }}</div>
        <div class="kop-unit">UNIT KESEHATAN SANTRI & KESEKRETARIATAN</div>
        <div class="kop-alamat">{{ $alamatLembaga }} &bull; {{ $kontakLembaga }}</div>
    </div>

    <!-- Judul & Nomor Surat + Kode 4 Digit -->
    <div class="judul-surat">
        <div style="text-align: right; font-size: 9pt; font-family: Arial; font-weight: bold; color: #064e3b;">
            KODE VERIFIKASI: [ {{ $suratUk->kode_verifikasi }} ]
        </div>
        <h2>{{ $suratUk->jenis === 'sakit' ? 'SURAT KETERANGAN IZIN SAKIT' : 'SURAT KETERANGAN IZIN PULANG' }}</h2>
        <p>Nomor: {{ $suratUk->nomor_surat }}</p>
    </div>

    <p style="text-align: justify; margin: 6px 0;">
        Yang bertanda tangan di bawah ini menerangkan bahwa santri tersebut di bawah ini:
    </p>

    <table class="meta-table">
        <tr>
            <td style="width: 25%;">Nama Santri</td>
            <td style="width: 3%; text-align: center;">:</td>
            <td style="width: 72%;">
                @if (!empty($suratUk->nama_santri))
                    <strong>{{ strtoupper($suratUk->nama_santri) }}</strong>
                @else
                    ....................................................................................................
                @endif
            </td>
        </tr>
        <tr>
            <td>Kelas / Tingkat</td>
            <td style="text-align: center;">:</td>
            <td>
                @if (!empty($suratUk->kelas))
                    <strong>{{ $suratUk->kelas }}</strong>
                @else
                    ....................................................................................................
                @endif
            </td>
        </tr>
        <tr>
            <td>Kamar / Asrama</td>
            <td style="text-align: center;">:</td>
            <td>
                @if (!empty($suratUk->asrama))
                    <strong>{{ $suratUk->asrama }}</strong>
                @else
                    ....................................................................................................
                @endif
            </td>
        </tr>
        <tr>
            <td>{{ $suratUk->jenis === 'sakit' ? 'Keluhan / Sakit' : 'Alasan / Keperluan' }}</td>
            <td style="text-align: center;">:</td>
            <td>....................................................................................................</td>
        </tr>
        <tr>
            <td>Masa Izin</td>
            <td style="text-align: center;">:</td>
            <td>Tgl. ........................................ s/d Tgl. ........................................</td>
        </tr>
    </table>

    <div class="kotak-catatan">
        <strong>Ketentuan:</strong>
        Surat ini wajib diserahkan kepada Pos Keamanan saat keluar pondok dan dibawa kembali saat tiba di pondok. Keterlambatan tanpa keterangan akan dikenakan sanksi disiplin pondok.
    </div>

    <!-- Tanda Tangan -->
    <table class="ttd-table">
        @if ($suratUk->jenis === 'pulang')
            <tr>
                <td>
                    Mengetahui,<br>Orang Tua / Wali Santri<br><br><br><br><br>
                    <strong><u>( .................................... )</u></strong>
                </td>
                <td>
                    Pos Keamanan / Satpam<br><br><br><br><br><br>
                    <strong><u>( .................................... )</u></strong>
                </td>
                <td>
                    Putak, ....................................<br>
                    Pengasuh Pondok Pesantren,<br><br><br><br><br>
                    <strong><u>KH. MURSYIDI</u></strong>
                </td>
            </tr>
        @else
            <tr>
                <td>
                    Pengurus Kesehatan Putra<br><br><br><br><br><br>
                    <strong><u>( .................................... )</u></strong>
                </td>
                <td>
                    Pos Keamanan / Satpam<br><br><br><br><br><br>
                    <strong><u>( .................................... )</u></strong>
                </td>
                <td>
                    Putak, ....................................<br>
                    Sekretaris Pondok Putra,<br><br><br><br><br>
                    <strong><u>( .................................... )</u></strong>
                </td>
            </tr>
        @endif
    </table>
</div>
</body>
</html>
