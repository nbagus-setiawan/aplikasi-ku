<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Buku Kas Umum - {{ $pengaturan['nama_situs'] ?? $pengaturans['nama_situs'] ?? 'Pondok Pesantren Al-Falah Putak' }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
            margin: 0;
            padding: 10px;
        }
        .kop {
            text-align: center;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 8px;
            margin-bottom: 15px;
        }
        .kop h2 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #064e3b;
        }
        .kop h3 {
            margin: 2px 0;
            font-size: 12px;
            font-weight: bold;
            color: #334155;
            text-transform: uppercase;
        }
        .kop p {
            margin: 2px 0 0;
            font-size: 9px;
            color: #64748b;
        }
        .judul-laporan {
            text-align: center;
            margin-bottom: 12px;
        }
        .judul-laporan h3 {
            margin: 0;
            font-size: 13px;
            text-transform: uppercase;
            font-weight: bold;
            color: #0f172a;
            text-decoration: underline;
        }
        .judul-laporan p {
            margin: 2px 0 0;
            font-size: 9.5px;
            color: #475569;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            border: 1px solid #94a3b8;
            padding: 5px 6px;
            text-align: left;
            font-size: 9px;
        }
        th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-debit { color: #047857; font-weight: bold; }
        .text-kredit { color: #b91c1c; font-weight: bold; }
        .saldo-row {
            background-color: #f8fafc;
            font-weight: bold;
        }
        .ttd-container {
            width: 100%;
            margin-top: 25px;
        }
        .ttd-box {
            width: 45%;
            text-align: center;
            float: left;
            font-size: 10px;
        }
        .ttd-box.right {
            float: right;
        }
        .clear { clear: both; }
    </style>
</head>
<body>

    <!-- KOP RESMI LEMBAGA -->
    <div class="kop">
        <h2>{{ $pengaturan['kop_yayasan'] ?? $pengaturans['kop_yayasan'] ?? 'YAYASAN PONDOK PESANTREN AL-FALAH PUTAK' }}</h2>
        <h3>{{ $pengaturan['kop_nama'] ?? $pengaturans['kop_nama'] ?? 'PONDOK PESANTREN AL-FALAH PUTRA' }}</h3>
        <p>{{ $pengaturan['kop_alamat'] ?? $pengaturans['kop_alamat'] ?? 'Desa Putak, Kec. Gelumbang, Kab. Muara Enim, Sumatera Selatan' }} | Telp: {{ $pengaturan['telepon'] ?? $pengaturans['telepon'] ?? '0812-7890-3117' }}</p>
    </div>

    <!-- JUDUL LAPORAN BUKU KAS -->
    <div class="judul-laporan">
        <h3>BUKU KAS UMUM (JURNAL DEBIT & KREDIT)</h3>
        <p>
            Periode: 
            @if($dari && $sampai)
                {{ date('d/m/Y', strtotime($dari)) }} s.d. {{ date('d/m/Y', strtotime($sampai)) }}
            @elseif($dari)
                Mulai {{ date('d/m/Y', strtotime($dari)) }}
            @elseif($sampai)
                Sampai {{ date('d/m/Y', strtotime($sampai)) }}
            @else
                Seluruh Transaksi Pembukuan
            @endif
            @if(!empty($kategori)) | Pos Akun: {{ $kategori }} @endif
            @if(!empty($jenis)) | Jenis: {{ strtoupper($jenis) }} @endif
        </p>
    </div>

    <!-- TABEL BUKU KAS UMUM -->
    <table>
        <thead>
            <tr>
                <th class="text-center" style="width: 25px;">No</th>
                <th class="text-center" style="width: 55px;">Tanggal</th>
                <th style="width: 80px;">No. Bukti</th>
                <th style="width: 85px;">Pos Akun</th>
                <th>Uraian / Keterangan Transaksi</th>
                <th class="text-right" style="width: 75px;">Debit (Rp)</th>
                <th class="text-right" style="width: 75px;">Kredit (Rp)</th>
                <th class="text-right" style="width: 80px;">Saldo Kas (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @php $currentSaldo = (float) $saldoAwal; @endphp

            @if($dari && $saldoAwal != 0)
            <tr class="saldo-row">
                <td class="text-center">-</td>
                <td class="text-center">{{ date('d/m/Y', strtotime($dari)) }}</td>
                <td colspan="3"><em>SALDO AWAL KAS SEBELUM PERIODE</em></td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right font-bold">Rp {{ number_format($currentSaldo, 0, ',', '.') }}</td>
            </tr>
            @endif

            @forelse($transaksis as $idx => $t)
                @php
                    if ($t->jenis === 'debit') {
                        $currentSaldo += (float) $t->nominal;
                    } else {
                        $currentSaldo -= (float) $t->nominal;
                    }
                    $tglDisplay = $t->tanggal ? date('d-m-Y', strtotime($t->tanggal)) : '-';
                @endphp
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center">{{ $tglDisplay }}</td>
                    <td style="font-family: monospace;">{{ $t->kode_transaksi }}</td>
                    <td>{{ $t->kategori }}</td>
                    <td>
                        {{ $t->keterangan ?: '-' }}
                        @if($t->nama_pihak)
                            <br><small style="color: #64748b;">(Pihak: {{ $t->nama_pihak }})</small>
                        @endif
                    </td>
                    <td class="text-right text-debit">
                        {{ $t->jenis === 'debit' ? 'Rp ' . number_format((float) $t->nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right text-kredit">
                        {{ $t->jenis === 'kredit' ? 'Rp ' . number_format((float) $t->nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right" style="font-weight: bold;">
                        Rp {{ number_format((float) $currentSaldo, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">
                        Tidak ada transaksi kas yang tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse

            <!-- REKAPITULASI TOTAL AKHIR -->
            <tr style="background-color: #e2e8f0; font-weight: bold; font-size: 9.5px;">
                <td colspan="5" class="text-center" style="letter-spacing: 0.5px;">TOTAL MUTASI & SALDO AKHIR</td>
                <td class="text-right text-debit">Rp {{ number_format((float) $totalDebit, 0, ',', '.') }}</td>
                <td class="text-right text-kredit">Rp {{ number_format((float) $totalKredit, 0, ',', '.') }}</td>
                <td class="text-right" style="color: #0f172a; font-size: 10px;">Rp {{ number_format((float) $currentSaldo, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN TANDA TANGAN -->
    <div class="ttd-container">
        <div class="ttd-box">
            <p>Mengetahui,</p>
            <p style="font-weight: bold; margin-top: -5px;">Pengasuh Pondok Pesantren</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">K.H. Mursyidi</p>
            <p style="color: #64748b; font-size: 8.5px; margin: 0;">Pengasuh PP Al-Falah Putak</p>
        </div>

        <div class="ttd-box right">
            @php
                $namaBulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                $tglCetak = date('j') . ' ' . ($namaBulanIndo[(int) date('n')] ?? date('F')) . ' ' . date('Y');
            @endphp
            <p>Desa Putak, {{ $tglCetak }}</p>
            <p style="font-weight: bold; margin-top: -5px;">Bendahara Pesantren</p>
            <br><br><br>
            <p style="font-weight: bold; text-decoration: underline; margin-bottom: 2px;">( ......................................... )</p>
            <p style="color: #64748b; font-size: 8.5px; margin: 0;">Bagian Kebendaharaan</p>
        </div>
        <div class="clear"></div>
    </div>

</body>
</html>
