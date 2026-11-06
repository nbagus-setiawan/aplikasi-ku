<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Laba Rugi - {{ $labelPeriode }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1.2cm 1.5cm 1.5cm 1.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 10pt;
            line-height: 1.35;
            color: #0f172a;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .header-kop {
            text-align: center;
            border-bottom: 2.5px solid #0f172a;
            padding-bottom: 6px;
            margin-bottom: 14px;
        }

        .header-kop table {
            width: 100%;
            border-collapse: collapse;
        }

        .judul-laporan {
            text-align: center;
            margin-bottom: 16px;
        }

        .judul-laporan h2 {
            font-size: 13pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .judul-laporan p {
            font-size: 9.5pt;
            margin: 3px 0 0;
            color: #334155;
        }

        .ringkasan-box {
            width: 100%;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .ringkasan-box td {
            padding: 8px 12px;
            font-size: 9.5pt;
            text-align: center;
            border-right: 1px solid #e2e8f0;
        }

        .ringkasan-box td:last-child {
            border-right: none;
        }

        .ringkasan-box .nilai {
            font-size: 11pt;
            font-weight: bold;
            font-family: 'Courier New', Courier, monospace;
            display: block;
            margin-top: 2px;
        }

        .text-surplus {
            color: #065f46;
        }

        .text-defisit {
            color: #9f1239;
        }

        table.tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 9pt;
        }

        table.tabel-data th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.3px;
            border: 1px solid #94a3b8;
            padding: 6px 8px;
            text-align: left;
        }

        table.tabel-data td {
            border: 1px solid #cbd5e1;
            padding: 5px 8px;
            vertical-align: middle;
        }

        table.tabel-data tr.section-header td {
            background-color: #e2e8f0;
            font-weight: bold;
            font-size: 9.5pt;
            color: #0f172a;
            border-top: 1.5px solid #64748b;
            border-bottom: 1.5px solid #64748b;
            padding: 6px 8px;
        }

        table.tabel-data tr.subtotal-row td {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 1.5px solid #94a3b8;
            border-bottom: 1.5px solid #94a3b8;
            padding: 6px 8px;
        }

        table.tabel-data tr.total-akhir-row td {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 10pt;
            border-top: 2px solid #0f172a;
            border-bottom: 2.5px double #0f172a;
            padding: 7px 8px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .nowrap { white-space: nowrap; }
        .font-mono { font-family: 'Courier New', Courier, monospace; }

        /* LEMBAR TANDA TANGAN (2 DI ATAS, 1 DI BAWAH TENGAH) */
        .pengesahan-container {
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .tabel-ttd {
            width: 100%;
            border-collapse: collapse;
        }

        .tabel-ttd td {
            border: none;
            padding: 0;
            line-height: 1.3;
        }

        .ttd-space {
            height: 55px;
        }

        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
            font-size: 9.5pt;
        }

        .ttd-jabatan {
            font-weight: bold;
            margin: 2px 0 0;
            font-size: 9pt;
        }

        .ttd-sub {
            margin: 0;
            font-size: 9pt;
        }

        /* Navigasi layar saat pratinjau browser */
        .no-print {
            display: none;
        }

        @media screen {
            .no-print {
                display: block;
                position: fixed;
                top: 10px;
                right: 10px;
                z-index: 9999;
                background: #0f172a;
                color: #ffffff;
                padding: 8px 14px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            .no-print button {
                background: #2563eb;
                color: #fff;
                border: none;
                padding: 6px 12px;
                border-radius: 6px;
                cursor: pointer;
                font-weight: bold;
            }
        }
    </style>
</head>
<body>

    @if(!empty($isHtmlPrint))
    <div class="no-print">
        <button onclick="window.print()">Cetak Dokumen</button>
        <a href="{{ route('admin.bendahara.laba-rugi') }}" style="color: #cbd5e1; text-decoration: none; margin-left: 10px; font-size: 9pt;">Kembali</a>
    </div>
    @endif

    <!-- KOP RESMI PONDOK PESANTREN AL-FALAH PUTRA -->
    <div class="header-kop">
        <table>
            <tr>
                @if(!empty($logo))
                    <td style="width: 70px; text-align: center; vertical-align: middle;">
                        <img src="{{ $logo }}" style="width: 60px; height: auto;" alt="Logo">
                    </td>
                @endif
                <td style="text-align: center; padding: 0 10px;">
                    <div style="font-size: 11pt; font-weight: bold; letter-spacing: 0.5px; margin: 0; text-transform: uppercase; color: #0f172a;">
                        YAYASAN PONDOK PESANTREN SALAFIYAH AL-FALAH
                    </div>
                    <div style="font-size: 14pt; font-weight: bold; letter-spacing: 0.8px; margin: 2px 0; text-transform: uppercase; color: #064e3b;">
                        PONDOK PUTRA
                    </div>
                    <div style="font-size: 8pt; margin: 1px 0; color: #334155;">
                        Dusun II Ds. Putak Kec. Gelumbang Kab. Muara Enim Sumatera Selatan Kode Pos 31171
                    </div>
                    <div style="font-size: 8pt; margin: 1px 0; color: #475569;">
                        Telepon: 6282118702717 &nbsp;&nbsp;&nbsp; E-mail: pengurusalfalahputra@gmail.com
                    </div>
                </td>
                @if(!empty($logo))
                    <td style="width: 70px;"></td>
                @endif
            </tr>
        </table>
    </div>

    <!-- JUDUL LAPORAN -->
    <div class="judul-laporan">
        <h2>LAPORAN LABA RUGI (SURPLUS / DEFISIT KAS)</h2>
        <p>Periode: <strong>{{ $labelPeriode }}</strong></p>
    </div>

    <!-- KOTAK IKHTISAR -->
    <table class="ringkasan-box">
        <tr>
            <td style="width: 33.3%;">
                <span style="color: #475569; font-size: 8.5pt; text-transform: uppercase;">Total Pendapatan (Debit)</span>
                <span class="nilai text-surplus">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </td>
            <td style="width: 33.3%;">
                <span style="color: #475569; font-size: 8.5pt; text-transform: uppercase;">Total Beban (Kredit)</span>
                <span class="nilai text-defisit">Rp {{ number_format($totalBeban, 0, ',', '.') }}</span>
            </td>
            <td style="width: 33.3%;">
                <span style="color: #475569; font-size: 8.5pt; text-transform: uppercase;">{{ $isSurplus ? 'Surplus Bersih' : 'Defisit Bersih' }}</span>
                <span class="nilai {{ $isSurplus ? 'text-surplus' : 'text-defisit' }}">
                    {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </span>
            </td>
        </tr>
    </table>

    <!-- TABEL UTAMA LAPORAN LABA RUGI -->
    <table class="tabel-data">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">NO</th>
                <th style="width: 54%;">KOMPONEN / POS AKUN KEUANGAN</th>
                <th style="width: 14%; text-align: center;">TRANSAKSI</th>
                <th style="width: 16%; text-align: right;">NOMINAL (RP)</th>
                <th style="width: 10%; text-align: right;">RASIO</th>
            </tr>
        </thead>
        <tbody>
            <!-- BAGIAN I: PENDAPATAN (PENERIMAAN KAS) -->
            <tr class="section-header">
                <td colspan="5">I. PENDAPATAN / PENERIMAAN KAS OPERASIONAL</td>
            </tr>
            @forelse($pendapatans as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td class="text-center">{{ $item->jumlah_transaksi }} transaksi</td>
                    <td class="text-right font-mono nowrap">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ $item->persentase }}%</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td colspan="4" style="font-style: italic; color: #64748b;">Tidak ada data penerimaan pada periode ini.</td>
                </tr>
            @endforelse
            <tr class="subtotal-row">
                <td colspan="3" class="text-right">JUMLAH PENDAPATAN (A)</td>
                <td class="text-right font-mono nowrap" style="color: #065f46;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td class="text-right font-mono">100.0%</td>
            </tr>

            <!-- BAGIAN II: BEBAN (PENGELUARAN KAS) -->
            <tr class="section-header">
                <td colspan="5">II. BEBAN / PENGELUARAN KAS OPERASIONAL</td>
            </tr>
            @forelse($bebans as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td>{{ $item->kategori }}</td>
                    <td class="text-center">{{ $item->jumlah_transaksi }} transaksi</td>
                    <td class="text-right font-mono nowrap">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ $item->persentase }}%</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td colspan="4" style="font-style: italic; color: #64748b;">Tidak ada data pengeluaran pada periode ini.</td>
                </tr>
            @endforelse
            <tr class="subtotal-row">
                <td colspan="3" class="text-right">JUMLAH BEBAN (B)</td>
                <td class="text-right font-mono nowrap" style="color: #9f1239;">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                <td class="text-right font-mono">100.0%</td>
            </tr>

            <!-- BAGIAN III: HASIL AKHIR (SURPLUS / DEFISIT BERSIH) -->
            <tr class="total-akhir-row">
                <td colspan="3" class="text-right">
                    HASIL BERSIH OPERASIONAL: {{ $isSurplus ? 'SURPLUS KAS (A - B)' : 'DEFISIT KAS (A - B)' }}
                </td>
                <td class="text-right font-mono nowrap {{ $isSurplus ? 'text-surplus' : 'text-defisit' }}" style="font-size: 10.5pt;">
                    {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </td>
                <td class="text-right font-mono {{ $isSurplus ? 'text-surplus' : 'text-defisit' }}">
                    {{ $marginSurplusDefisit }}%
                </td>
            </tr>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN 3 TANDA TANGAN (TIDAK SEJAJAR: 2 DI ATAS, 1 DI BAWAH TENGAH) -->
    <div class="pengesahan-container">
        <!-- BARIS 1: 2 TTD DI ATAS (KETUA UMUM AFPA & BENDAHARA AFPA) -->
        <table class="tabel-ttd">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p class="ttd-sub">Mengetahui,</p>
                    <p class="ttd-jabatan">Ketua Umum AFPA</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">Danil Ali Sabila</p>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p class="ttd-sub">Putak, {{ $tanggalAkhirRiil }}</p>
                    <p class="ttd-jabatan">Bendahara AFPA</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">Azwil Yoga Pratama</p>
                </td>
            </tr>
        </table>

        <!-- BARIS 2: 1 TTD DI BAWAH TENGAH (BENDAHARA PUSAT PPAF) -->
        <table class="tabel-ttd" style="margin-top: 14px;">
            <tr>
                <td style="width: 25%;"></td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p class="ttd-sub">Mengetahui,</p>
                    <p class="ttd-jabatan">Bendahara Pusat PPAF</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">Ust Imam Qusyairi</p>
                </td>
                <td style="width: 25%;"></td>
            </tr>
        </table>
    </div>

</body>
</html>
