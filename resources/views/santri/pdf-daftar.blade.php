<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Rekapitulasi Santri</title>
    <style>
        @page { margin: 1.5cm 1cm 1cm 1cm; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #1a1a1a;
            line-height: 1.3;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header h1 {
            font-size: 14pt;
            margin: 0;
            font-weight: bold;
            letter-spacing: 0.5px;
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
            margin: 10px 0 4px 0;
            text-decoration: underline;
        }
        .meta {
            font-size: 8.5pt;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        table.data th, table.data td {
            border: 1px solid #444;
            padding: 5px 6px;
        }
        table.data th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer-ttd {
            margin-top: 25px;
            float: right;
            width: 250px;
            text-align: center;
            font-size: 9pt;
        }
        .ttd-space { height: 55px; }
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

    <div class="title">DAFTAR REKAPITULASI DATA SANTRI</div>
    <div class="meta">
        <span>Tanggal Cetak: {{ date('d-m-Y H:i') }}</span>
        <span style="float: right;">Total Data: {{ $santris->count() }} Santri</span>
    </div>
    <div style="clear: both;"></div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 70px;">NIS</th>
                <th>Nama Santri</th>
                <th>Tempat, Tanggal Lahir</th>
                <th style="width: 60px;">Kelas</th>
                <th style="width: 80px;">Kamar</th>
                <th style="width: 45px;">Masuk</th>
                <th>Nama & No. HP Wali</th>
                <th style="width: 50px;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($santris as $index => $santri)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center" style="font-family: monospace;">{{ $santri->nis }}</td>
                    <td><strong>{{ $santri->nama }}</strong></td>
                    <td>{{ $santri->tempat_lahir ? $santri->tempat_lahir . ', ' : '' }}{{ $santri->tanggal_lahir_formatted ?: '-' }}</td>
                    <td class="text-center">{{ $santri->kelas ?: '-' }}</td>
                    <td class="text-center">{{ $santri->kamar ?: '-' }}</td>
                    <td class="text-center">{{ $santri->tahun_masuk ?: '-' }}</td>
                    <td>{{ $santri->nama_wali ?: '-' }}{{ $santri->hp_wali ? ' (' . $santri->hp_wali . ')' : '' }}</td>
                    <td class="text-center">{{ ucfirst($santri->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px;">Tidak ada data santri.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-ttd">
        <p>Putak, {{ date('d-m-Y') }}<br>Sekretariat Pondok,</p>
        <div class="ttd-space"></div>
        <p><strong>( _______________________ )</strong></p>
    </div>
</body>
</html>
