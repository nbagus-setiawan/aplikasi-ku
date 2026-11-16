<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>&#8203;</title>
    <style>
        @page {
            margin: 0.8cm 1cm 0.8cm 1cm;
            size: A4 landscape;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8.5pt;
            color: #1a1a1a;
            line-height: 1.3;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #111;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
            padding: 0;
        }
        .header-logo {
            width: 70px;
            text-align: left;
        }
        .header-logo img {
            max-width: 65px;
            max-height: 65px;
        }
        .header-text {
            text-align: center;
        }
        .header-text .yayasan {
            font-size: 10pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .header-text .instansi {
            font-size: 14pt;
            font-weight: bold;
            margin: 2px 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header-text .alamat {
            font-size: 8pt;
            color: #333;
            margin: 1px 0;
        }
        .title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin: 10px 0 2px 0;
            text-decoration: underline;
            text-transform: uppercase;
        }
        .subtitle {
            text-align: center;
            font-size: 9pt;
            font-weight: normal;
            margin-bottom: 12px;
            color: #444;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }
        .meta-table td {
            padding: 1px 0;
            border: none;
        }
        table.ringkasan {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8pt;
        }
        table.ringkasan th, table.ringkasan td {
            border: 1px solid #666;
            padding: 4px;
            text-align: center;
        }
        table.ringkasan th {
            background-color: #f1f5f9;
            font-weight: bold;
            text-transform: uppercase;
        }
        table.data {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
        }
        table.data th, table.data td {
            border: 1px solid #444;
            padding: 4px 5px;
        }
        table.data th {
            background-color: #f2f2f2;
            text-align: center;
            font-weight: bold;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .footer-ttd {
            margin-top: 25px;
            width: 100%;
            page-break-inside: avoid;
        }
        .ttd-box {
            float: right;
            width: 220px;
            text-align: center;
            font-size: 8.5pt;
        }
        .ttd-space {
            height: 55px;
        }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    @if(!empty($isHtmlPrint))
        <div style="background: #f1f5f9; padding: 12px; margin-bottom: 20px; border-radius: 8px; text-align: center; font-family: Arial, sans-serif;" class="no-print">
            <button onclick="window.print()" style="background: #059669; color: white; border: none; padding: 8px 18px; border-radius: 6px; font-weight: bold; cursor: pointer; font-size: 13px;">
                🖨️ Cetak / Simpan ke PDF Sekarang
            </button>
            <button onclick="window.close()" style="background: #64748b; color: white; border: none; padding: 8px 14px; border-radius: 6px; margin-left: 8px; cursor: pointer; color: white; font-size: 13px;">
                Tutup
            </button>
            <p style="font-size: 11px; color: #475569; margin: 6px 0 0;">
                💡 <strong>Tips Agar Bersih dari Tanggal/Tautan:</strong> Pada dialog cetak browser, buka <em>More settings / Setelan lainnya</em> lalu <strong>hilangkan centang "Headers and footers / Header dan footer"</strong> agar tanggal dan judul tidak muncul di atas dokumen.
            </p>
        </div>
    @endif

    {{-- Header Kop Surat --}}
    <table class="header-table">
        <tr>
            @if(!empty($logo))
                <td class="header-logo">
                    <img src="{{ $logo }}" alt="Logo">
                </td>
            @endif
            <td class="header-text">
                <div class="yayasan">{{ $pengaturans['kop_yayasan'] ?? 'YAYASAN PONDOK PESANTREN SALAFIYAH AL-FALAH' }}</div>
                <div class="instansi">{{ $pengaturans['kop_nama'] ?? 'SEKRETARIAT PONDOK PUTRA' }}</div>
                <div class="alamat">{{ $pengaturans['kop_alamat'] ?? 'Dusun II Ds. Putak Kec. Gelumbang Kab. Muara Enim Sumatera Selatan Kode Pos 31171' }}</div>
                <div class="alamat">{{ $pengaturans['kop_kontak'] ?? 'Telepon: 6282118702717 | E-mail: pengurusalfalahputra@gmail.com' }}</div>
            </td>
        </tr>
    </table>

    {{-- Judul Laporan --}}
    <div class="title">DAFTAR PRESENSI KORLAP SHOLAT JAMAAH</div>
    <div class="subtitle">Hari / Tanggal: {{ $labelTanggal }}</div>

    {{-- Ringkasan 5 Sholat --}}
    <table class="ringkasan">
        <thead>
            <tr>
                <th>Waktu Sholat</th>
                <th>Tepat Waktu</th>
                <th>Terlambat</th>
                <th>Total Hadir</th>
                <th>Sakit / Izin</th>
                <th>Alfa</th>
                <th>Total Terdata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ringkasan as $sholat => $r)
                <tr>
                    <td style="font-weight: bold;">{{ $r['nama'] }}</td>
                    <td style="color: #065f46;">{{ $r['hadir_tepat'] }}</td>
                    <td style="color: #d97706;">{{ $r['hadir_telat'] }}</td>
                    <td style="font-weight: bold; background-color: #f8fafc;">{{ $r['hadir_tepat'] + $r['hadir_telat'] }}</td>
                    <td>{{ $r['sakit'] + $r['izin'] }}</td>
                    <td style="{{ $r['alfa'] > 0 ? 'color: #dc2626; font-weight: bold;' : '' }}">{{ $r['alfa'] }}</td>
                    <td>{{ $r['total'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="meta-table">
        <tr>
            <td style="width: 50%;"><strong>Total Log Presensi:</strong> {{ $presensi->count() }} Data</td>
            <td style="width: 50%; text-align: right;"><strong>Tanggal Cetak:</strong> {{ $tanggalCetak }}</td>
        </tr>
    </table>

    {{-- Tabel Rincian Presensi --}}
    <table class="data">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th style="width: 60px;">Sholat</th>
                <th>Nama Petugas</th>
                <th style="width: 55px;">Jam</th>
                <th style="width: 55px;">Status</th>
                <th style="width: 75px;">Kategori</th>
                <th>Koordinator Piket</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($presensi as $index => $row)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td class="text-center font-bold" style="text-transform: uppercase;">{{ $row->waktu_sholat }}</td>
                    <td class="text-left font-semibold">
                        <strong>{{ $row->petugas->nama ?? '-' }}</strong>
                    </td>
                    <td class="text-center font-mono">{{ substr($row->jam_absen ?? '-', 0, 5) }}</td>
                    <td class="text-center">
                        <strong style="text-transform: uppercase; {{ $row->status === 'hadir' ? 'color: #065f46;' : ($row->status === 'alfa' ? 'color: #dc2626;' : 'color: #d97706;') }}">
                            {{ $row->status }}
                        </strong>
                    </td>
                    <td class="text-center">
                        @if($row->status === 'hadir')
                            <span style="{{ $row->kategori_waktu === 'tepat_waktu' ? 'color: #065f46;' : 'color: #d97706;' }}">
                                {{ $row->kategori_waktu === 'tepat_waktu' ? 'Tepat Waktu' : 'Terlambat' }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-left">{{ $row->koordinator->nama ?? '-' }}</td>
                    <td class="text-left" style="font-style: italic; color: #555;">{{ $row->keterangan ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #666;">
                        Belum ada data presensi yang masuk pada tanggal {{ $labelTanggal }}.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Keterangan Singkat & Tanda Tangan --}}
    <div class="footer-ttd">
        <div style="float: left; width: 300px; font-size: 7.5pt; color: #555; margin-top: 15px;">
            <p style="margin: 0 0 2px 0;"><strong>Catatan:</strong></p>
            <p style="margin: 0 0 2px 0;">- Dokumen ini adalah rekapan resmi presensi korlap sholat jamaah harian.</p>
            <p style="margin: 0;">- Tepat Waktu: Sebelum jam tepat; Terlambat: Setelah jam tepat sebelum jam tutup.</p>
        </div>

        <div class="ttd-box">
            <div>Gelumbang, {{ $tanggalCetak }}</div>
            <div style="margin-top: 2px;">Koordinator / Sekretaris,</div>
            <div class="ttd-space"></div>
            <div style="font-weight: bold; text-decoration: underline;">
                {{ $pengaturans['nama_sekretaris'] ?? 'Sekretaris Pondok' }}
            </div>
            <div style="color: #555; font-size: 8pt;">NIP/NIY: {{ $pengaturans['niy_sekretaris'] ?? '........................' }}</div>
        </div>
        <div style="clear: both;"></div>
    </div>
</body>
</html>
