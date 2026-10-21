<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Biodata Santri - {{ $santri->nis }}</title>
    <style>
        @page { margin: 1.5cm 1.5cm 1.5cm 1.5cm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            margin: 2px 0 0 0;
            font-size: 8pt;
            color: #333;
        }
        .title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 12px 0 18px 0;
            text-decoration: underline;
            letter-spacing: 0.5px;
        }
        table.biodata {
            width: 100%;
            border-collapse: collapse;
            font-size: 9.5pt;
        }
        table.biodata td {
            padding: 6px 8px;
            vertical-align: top;
        }
        .foto-box {
            width: 3cm;
            height: 4cm;
            border: 1px dashed #666;
            text-align: center;
            line-height: 4cm;
            font-size: 8pt;
            color: #999;
            float: right;
            margin-bottom: 10px;
        }
        .section-title {
            background-color: #f0fdf4;
            font-weight: bold;
            color: #065f46;
            border-left: 3px solid #059669;
            padding: 4px 8px;
            margin-top: 15px;
            margin-bottom: 8px;
            font-size: 9pt;
            text-transform: uppercase;
        }
        .footer-ttd {
            margin-top: 35px;
            float: right;
            width: 230px;
            text-align: center;
            font-size: 9.5pt;
        }
        .ttd-space { height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <div style="font-size: 11pt; font-weight: bold; letter-spacing: 0.5px; text-transform: uppercase;">
            {{ $pengaturans['kop_yayasan'] ?? 'YAYASAN PONDOK PESANTREN SALAFIYAH AL-FALAH' }}
        </div>
        <h1 style="font-size: 15pt; font-weight: bold; margin: 2px 0; letter-spacing: 1px; text-transform: uppercase;">
            {{ $pengaturans['kop_nama'] ?? 'PONDOK PUTRA' }}
        </h1>
        <p style="margin: 2px 0 0 0; font-size: 8.5pt;">
            {{ $pengaturans['kop_alamat'] ?? 'Dusun II Ds. Putak Kec. Gelumbang Kab. Muara Enim Sumatera Selatan Kode Pos 31171' }}
        </p>
        <p style="margin: 2px 0 0 0; font-size: 8.5pt;">
            {{ $pengaturans['kop_kontak'] ?? 'Telepon: 6282118702717   E-mail: pengurusalfalahputra@gmail.com' }}
        </p>
    </div>

    <div class="title">LEMBAR BIODATA RESMI SANTRI</div>

    <div class="foto-box">
        Pasfoto 3x4
    </div>

    <div style="width: 70%;">
        <div class="section-title">A. Data Pribadi</div>
        <table class="biodata">
            <tr>
                <td style="width: 35%;">Nomor Induk Santri (NIS)</td>
                <td style="width: 5%;">:</td>
                <td><strong>{{ $santri->nis }}</strong></td>
            </tr>
            <tr>
                <td>Nama Lengkap</td>
                <td>:</td>
                <td><strong>{{ $santri->nama }}</strong></td>
            </tr>
            <tr>
                <td>Tempat, Tanggal Lahir</td>
                <td>:</td>
                <td>{{ $santri->tempat_lahir ? $santri->tempat_lahir . ', ' : '' }}{{ $santri->tanggal_lahir_formatted ?: '-' }}</td>
            </tr>
            <tr>
                <td>Alamat Asal</td>
                <td>:</td>
                <td>{{ $santri->alamat ?: '-' }}</td>
            </tr>
            <tr>
                <td>Status Santri</td>
                <td>:</td>
                <td><strong>{{ ucfirst($santri->status) }}</strong></td>
            </tr>
        </table>
    </div>

    <div style="clear: both;"></div>

    <div class="section-title">B. Penempatan & Riwayat Pondok</div>
    <table class="biodata">
        <tr>
            <td style="width: 25%;">Kelas / Jenjang</td>
            <td style="width: 3%;">:</td>
            <td>{{ $santri->kelas ?: '-' }}</td>
        </tr>
        <tr>
            <td>Kamar / Asrama</td>
            <td>:</td>
            <td>{{ $santri->kamar ?: '-' }}</td>
        </tr>
        <tr>
            <td>Tahun Masuk</td>
            <td>:</td>
            <td>{{ $santri->tahun_masuk ?: '-' }}</td>
        </tr>
    </table>

    <div class="section-title">C. Data Orang Tua / Wali</div>
    <table class="biodata">
        <tr>
            <td style="width: 25%;">Nama Wali</td>
            <td style="width: 3%;">:</td>
            <td>{{ $santri->nama_wali ?: '-' }}</td>
        </tr>
        <tr>
            <td>No. HP / Kontak Wali</td>
            <td>:</td>
            <td>{{ $santri->hp_wali ?: '-' }}</td>
        </tr>
    </table>

    <div class="footer-ttd">
        <p>Putak, {{ date('d-m-Y') }}<br>Sekretaris Pondok,</p>
        <div class="ttd-space"></div>
        <p><strong>( _______________________ )</strong></p>
    </div>
</body>
</html>
