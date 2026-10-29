<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:w='urn:schemas-microsoft-com:office:word' xmlns='http://www.w3.org/TR/REC-html40'>
<head>
    <meta charset="utf-8">
    <title>Buku Kas Umum - {{ $pengaturans['nama_situs'] ?? 'Pondok Pesantren Al-Falah Putak' }}</title>
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
        @page {
            size: 297mm 210mm; /* A4 Landscape murni */
            margin: 15mm 15mm 15mm 15mm;
            mso-page-orientation: landscape;
        }
        @page Section1 {
            size: 297mm 210mm;
            margin: 15mm 15mm 15mm 15mm;
            mso-header-margin: 36.0pt;
            mso-footer-margin: 36.0pt;
            mso-page-orientation: landscape;
        }
        div.Section1 {
            page: Section1;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #0f172a;
            line-height: 1.35;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            text-align: center;
            margin-bottom: 12px;
        }
        .judul-laporan {
            text-align: center;
            margin-bottom: 14px;
        }
        .judul-laporan h2 {
            margin: 0;
            font-size: 13pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .judul-laporan p {
            margin: 3px 0 0;
            font-size: 9pt;
            color: #475569;
        }
        table.tabel-kas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9pt;
        }
        table.tabel-kas th, table.tabel-kas td {
            border: 1px solid #475569;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }
        table.tabel-kas th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            text-align: center;
        }
        .bg-strip {
            background-color: #f8fafc;
        }
        .text-center { text-align: center !important; }
        .text-right { text-align: right !important; }
        .text-debit { color: #047857; font-weight: bold; }
        .text-kredit { color: #b91c1c; font-weight: bold; }
        .text-saldo { color: #0f172a; font-weight: bold; }
        .nowrap { white-space: nowrap !important; }
        .saldo-awal-row {
            background-color: #f0fdf4;
            font-weight: bold;
        }
        .total-row {
            background-color: #e2e8f0;
            font-weight: bold;
            font-size: 9.5pt;
        }
    </style>
</head>
<body>
<div class="Section1">

    <!-- JUDUL LAPORAN BUKU KAS -->
    <div class="judul-laporan">
        <h2>LAPORAN BUKU KAS</h2>
        <div style="font-size: 11pt; font-weight: bold; margin-top: 3px;">PONDOK PESANTREN AL-FALAH PUTRA</div>
        <p style="margin-top: 6px; font-weight: bold;">
            PERIODE :
            @if($dari && $sampai)
                {{ date('d/m/Y', strtotime($dari)) }} s.d. {{ date('d/m/Y', strtotime($sampai)) }}
            @elseif($dari)
                Mulai {{ date('d/m/Y', strtotime($dari)) }}
            @elseif($sampai)
                Sampai {{ date('d/m/Y', strtotime($sampai)) }}
            @else
                Semua Transaksi Pembukuan
            @endif
        </p>
    </div>

    <!-- TABEL BUKU KAS UMUM -->
    <table class="tabel-kas" border="1" bordercolor="#000000" cellpadding="6" cellspacing="0">
        <thead>
            <tr style="background-color: #f8fafc;">
                <th style="width: 5%; text-align: center; border: 1px solid #000000;">NO</th>
                <th style="width: 20%; text-align: center; border: 1px solid #000000;" class="nowrap">No. BUKTI</th>
                <th style="width: 27%; text-align: left; border: 1px solid #000000;">AKUN</th>
                <th style="width: 16%; text-align: right; border: 1px solid #000000;" class="nowrap">DEBIT</th>
                <th style="width: 16%; text-align: right; border: 1px solid #000000;" class="nowrap">KREDIT</th>
                <th style="width: 16%; text-align: right; border: 1px solid #000000;" class="nowrap">SALDO</th>
            </tr>
        </thead>
        <tbody>
            @php $currentSaldo = (float) $saldoAwal; @endphp

            @if($dari && $saldoAwal != 0)
            <tr class="saldo-awal-row">
                <td style="text-align: center; border: 1px solid #000000;">-</td>
                <td style="text-align: center; border: 1px solid #000000; font-family: monospace;">-</td>
                <td style="border: 1px solid #000000; font-weight: bold;"><em>SALDO AWAL KAS</em></td>
                <td style="text-align: right; border: 1px solid #000000;">-</td>
                <td style="text-align: right; border: 1px solid #000000;">-</td>
                <td style="text-align: right; border: 1px solid #000000; font-weight: bold;">{{ number_format($currentSaldo, 0, ',', '.') }}</td>
            </tr>
            @endif

            @forelse($transaksis as $idx => $t)
                @php
                    if ($t->jenis === 'debit') {
                        $currentSaldo += (float) $t->nominal;
                    } else {
                        $currentSaldo -= (float) $t->nominal;
                    }
                @endphp
                <tr>
                    <td style="text-align: center; border: 1px solid #000000;">{{ $idx + 1 }}</td>
                    <td style="text-align: center; border: 1px solid #000000; font-family: monospace; font-weight: bold; white-space: nowrap;">
                        {{ $t->kode_transaksi }}
                    </td>
                    <td style="border: 1px solid #000000;">{{ $t->kategori }}</td>
                    <td style="text-align: right; border: 1px solid #000000; white-space: nowrap;">
                        {{ $t->jenis === 'debit' ? number_format((float) $t->nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td style="text-align: right; border: 1px solid #000000; white-space: nowrap;">
                        {{ $t->jenis === 'kredit' ? number_format((float) $t->nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td style="text-align: right; border: 1px solid #000000; font-weight: bold; white-space: nowrap;">
                        {{ number_format((float) $currentSaldo, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; border: 1px solid #000000; padding: 18px; color: #64748b;">
                        Tidak ada data transaksi kas pada periode ini.
                    </td>
                </tr>
            @endforelse

            <!-- REKAPITULASI TOTAL MUTASI & SALDO AKHIR -->
            <tr style="font-weight: bold;">
                <td colspan="3" style="text-align: center; border: 1px solid #000000; font-weight: bold;">TOTAL MUTASI &amp; SALDO AKHIR</td>
                <td style="text-align: right; border: 1px solid #000000; font-weight: bold; white-space: nowrap;">{{ number_format((float) $totalDebit, 0, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000000; font-weight: bold; white-space: nowrap;">{{ number_format((float) $totalKredit, 0, ',', '.') }}</td>
                <td style="text-align: right; border: 1px solid #000000; font-weight: bold; white-space: nowrap;">{{ number_format((float) $currentSaldo, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN SESUAI TEMPLATE RESMI -->
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="width: 100%; margin-top: 35px; border-collapse: collapse; border: none;">
        <tr>
            <td width="50%" style="width: 50%; text-align: center; border: none; vertical-align: top;">
                <p style="margin: 0; font-size: 10pt;">Mengetahui,</p>
                <p style="margin: 3px 0 0; font-size: 10pt; font-weight: bold;">Ketua Umum</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0; font-size: 10pt; font-weight: bold;">Danil Ali Sabila</p>
            </td>
            <td width="50%" style="width: 50%; text-align: center; border: none; vertical-align: top;">
                <p style="margin: 0; font-size: 10pt;">&nbsp;</p>
                <p style="margin: 3px 0 0; font-size: 10pt; font-weight: bold;">Bendahara</p>
                <div style="height: 60px;"></div>
                <p style="margin: 0; font-size: 10pt; font-weight: bold;">Azwil Yoga Pratama</p>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
