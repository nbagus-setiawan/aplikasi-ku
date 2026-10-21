<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Rekapitulasi Agenda Kegiatan</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .kop {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .kop h2 {
            margin: 0;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .kop h3 {
            margin: 2px 0;
            font-size: 13px;
            font-weight: normal;
        }
        .kop p {
            margin: 2px 0 0;
            font-size: 10px;
            color: #555;
        }
        .judul-laporan {
            text-align: center;
            margin-bottom: 15px;
        }
        .judul-laporan h3 {
            margin: 0;
            font-size: 13px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .judul-laporan p {
            margin: 3px 0 0;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer {
            margin-top: 30px;
            width: 100%;
        }
        .ttd-box {
            float: right;
            width: 250px;
            text-align: center;
        }
        .ttd-space {
            height: 60px;
        }
    </style>
</head>
<body>

    <!-- Kop Surat -->
    <div class="kop">
        <table style="width: 100%; border-collapse: collapse; border: none; margin: 0;">
            <tr>
                @if(extension_loaded('gd') && file_exists(public_path('images/logo.png')))
                    <td style="width: 70px; vertical-align: middle; text-align: center; border: none; padding: 0;">
                        <img src="{{ public_path('images/logo.png') }}" style="width: 65px; height: auto;" alt="Logo">
                    </td>
                @endif
                <td style="vertical-align: middle; text-align: center; border: none; padding: 0 10px;">
                    <h2 style="margin: 0; font-size: 15px; text-transform: uppercase;">{{ $pengaturan['nama_instansi'] ?? 'PONDOK PESANTREN AL-FALAH PUTRA' }}</h2>
                    <h3 style="margin: 2px 0; font-size: 12px; font-weight: normal;">{{ $pengaturan['kop_surat_baris_2'] ?? 'SEKRETARIAT ADMINISTRASI DAN PERSURATAN' }}</h3>
                    <p style="margin: 2px 0 0; font-size: 10px; color: #555;">{{ $pengaturan['kop_surat_baris_3'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171 | Telp: 0812-7890-3117' }}</p>
                </td>
                @if(extension_loaded('gd') && file_exists(public_path('images/logo.png')))
                    <td style="width: 70px; border: none; padding: 0;"></td>
                @endif
            </tr>
        </table>
    </div>

    <!-- Judul Rekapitulasi -->
    <div class="judul-laporan">
        <h3>Laporan Rekapitulasi Agenda Kegiatan</h3>
        <p>Periode: {{ \Carbon\Carbon::parse($dari)->format('d-m-Y') }} s.d. {{ \Carbon\Carbon::parse($sampai)->format('d-m-Y') }}</p>
    </div>

    <!-- Tabel Data -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;" class="text-center">No</th>
                <th style="width: 25%;">Nama Agenda</th>
                <th style="width: 20%;">Lokasi</th>
                <th style="width: 20%;">Waktu Pelaksanaan</th>
                <th style="width: 18%;">Penanggung Jawab</th>
                <th style="width: 12%;" class="text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($agendas as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td><strong>{{ $item->judul }}</strong></td>
                    <td>{{ $item->lokasi ?: '-' }}</td>
                    <td>{{ $item->mulai ? $item->mulai->format('d-m-Y H:i') : '-' }}</td>
                    <td>{{ $item->pj->nama ?? '-' }}</td>
                    <td class="text-center" style="text-transform: capitalize;">{{ $item->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada agenda kegiatan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <p style="font-size: 10px;"><em>Total Agenda Kegiatan: {{ $agendas->count() }} kegiatan</em></p>

    <!-- Tanda Tangan Pengesahan -->
    <div class="footer">
        <div class="ttd-box">
            <p>Putak, {{ date('d-m-Y') }}<br>Sekretariat Pondok,</p>
            <div class="ttd-space"></div>
            <p><strong>( ______________________ )</strong><br>Bagian Administrasi Kegiatan</p>
        </div>
        <div style="clear: both;"></div>
    </div>

</body>
</html>
