<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>&#8203;</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1cm 1.3cm 1.2cm 1.3cm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000000;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* ===== KOP (SESUAI TEMPLATE RESMI) ===== */
        .header-kop {
            width: 100%;
            border-bottom: 2pt solid #000000;
            padding-bottom: 6px;
            margin-bottom: 16px;
        }
        .header-kop table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-kop td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }
        .kop-yayasan {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11.5pt;
            letter-spacing: 0.2px;
            margin: 0;
        }
        .kop-pondok {
            font-family: 'Times New Roman', Times, serif;
            font-size: 19pt;
            font-weight: bold;
            line-height: 1.05;
            margin: 0;
        }
        .kop-alamat {
            font-size: 8pt;
            font-weight: bold;
            margin: 2px 0 0;
        }
        .kop-kontak {
            font-size: 7.5pt;
            font-weight: bold;
            margin: 1px 0 0;
        }

        /* ===== JUDUL ===== */
        .judul-laporan {
            text-align: center;
            margin-bottom: 14px;
        }
        .judul-laporan .judul {
            font-size: 11pt;
            font-weight: bold;
            margin: 0;
        }
        .judul-laporan .periode {
            font-size: 8.5pt;
            font-weight: bold;
            margin: 6px 0 0;
        }

        /* ===== KOTAK IKHTISAR ===== */
        table.ringkasan-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 8.5pt;
        }
        table.ringkasan-box th {
            background-color: #a6a6a6;
            border: 1pt solid #000000;
            padding: 5px 8px;
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
        }
        table.ringkasan-box td {
            border: 1pt solid #000000;
            padding: 7px 8px;
            font-size: 9.5pt;
            font-weight: bold;
            text-align: center;
        }
        table.ringkasan-box td.saldo-akhir {
            background-color: #d9d9d9;
        }

        /* ===== TABEL UTAMA ===== */
        table.tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 8.5pt;
        }

        table.tabel-data th,
        table.tabel-data td {
            border: 1pt solid #000000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        table.tabel-data thead th {
            background-color: #a6a6a6;
            color: #000000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            padding: 7px 6px;
            text-align: center;
        }

        table.tabel-data tr.section-header td {
            background-color: #d9d9d9;
            font-weight: bold;
            font-size: 8.5pt;
            color: #000000;
            padding: 6px 7px;
        }

        table.tabel-data tr.subtotal-row td {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 6px 7px;
        }

        table.tabel-data tr.total-akhir-row td {
            font-weight: bold;
            font-size: 9pt;
            padding: 7px 7px;
        }

        table.tabel-data tr.total-akhir-row td.highlight-cell {
            background-color: #a6a6a6;
        }

        .text-center { text-align: center !important; }
        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .nowrap { white-space: nowrap; }

        /* ===== TANDA TANGAN (2 DI ATAS, 1 DI TENGAH BAWAH) ===== */
        .pengesahan {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }
        .pengesahan table {
            width: 100%;
            border-collapse: collapse;
        }
        .pengesahan td {
            border: none;
            padding: 0;
            text-align: center;
            vertical-align: top;
            font-size: 9pt;
        }
        .ttd-space { height: 55px; }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        .ttd-jabatan {
            font-style: italic;
            margin: 1px 0 0;
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

        @media print {
            @page {
                size: A4 portrait;
                margin-top: 0cm !important;
                margin-bottom: 1.2cm !important;
                margin-left: 1.3cm !important;
                margin-right: 1.3cm !important;
            }
            .no-print { display: none !important; }
            body {
                padding-top: 1cm !important;
                padding-bottom: 0 !important;
                padding-left: 0 !important;
                padding-right: 0 !important;
            }
        }
    </style>
</head>
<body>

    @if(!empty($isHtmlPrint))
    <div class="no-print">
        <button onclick="sesuaikanUrlCetak(); window.print();">Cetak Dokumen</button>
        <a href="{{ route('admin.bendahara.laba-rugi') }}" style="color: #cbd5e1; text-decoration: none; margin-left: 10px; font-size: 9pt;">Kembali</a>
    </div>
    @endif

    <script>
        function sesuaikanUrlCetak() {
            try {
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, '\u200B', '/');
                }
                document.title = '\u200B';
            } catch (e) {}
        }
        sesuaikanUrlCetak();
        window.addEventListener('beforeprint', sesuaikanUrlCetak);
        window.addEventListener('DOMContentLoaded', function() {
            sesuaikanUrlCetak();
            @if(!empty($isHtmlPrint))
            setTimeout(function() {
                sesuaikanUrlCetak();
                window.print();
            }, 400);
            @endif
        });
    </script>

    <!-- KOP RESMI PONDOK PESANTREN AL-FALAH PUTRA -->
    @if (!empty($kopGambar))
        <div style="text-align: center; margin-bottom: 16px;">
            <img src="{{ $kopGambar }}" style="width: 100%; max-height: 120px; height: auto; display: block;" alt="Kop Surat">
        </div>
    @else
        <div class="header-kop">
            <table>
                <tr>
                    <td style="width: 95px; text-align: center;">
                        @if(!empty($logo))
                            <img src="{{ $logo }}" style="width: 85px; height: auto;" alt="Logo">
                        @elseif(file_exists(public_path('images/logo-pesantren.png')))
                            <img src="{{ public_path('images/logo-pesantren.png') }}" style="width: 85px; height: auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if(!empty($kaligrafi))
                            <img src="{{ $kaligrafi }}" style="height: 28px; width: auto;" alt="Kaligrafi">
                        @elseif(file_exists(public_path('images/kaligrafi-kop.png')))
                            <img src="{{ public_path('images/kaligrafi-kop.png') }}" style="height: 28px; width: auto;" alt="Kaligrafi">
                        @endif
                        <p class="kop-yayasan">YAYASAN PONDOK PESANTREN SALAFIYAH AL-FALAH</p>
                        <p class="kop-pondok">PONDOK PUTRA</p>
                        <p class="kop-alamat">Dusun II Ds. Putak Kec. Gelumbang Kab. Muara Enim Sumatera Selatan Kode Pos 31171</p>
                        <p class="kop-kontak">Telepon: 6282118702717 &nbsp;&nbsp; E-mail: pengurusalfalahputra@gmail.com</p>
                    </td>
                    <td style="width: 95px;"></td>
                </tr>
            </table>
        </div>
    @endif

    <!-- JUDUL LAPORAN -->
    <div class="judul-laporan">
        <p class="judul">LAPORAN LABA RUGI (SURPLUS / DEFISIT KAS)</p>
        <p class="judul">PONDOK PUTRA</p>
        <p class="periode">PERIODE : {{ strtoupper($labelPeriode) }}</p>
    </div>

    <!-- KOTAK IKHTISAR -->
    <table class="ringkasan-box">
        <thead>
            <tr>
                <th style="width: 33.3%;">TOTAL PENDAPATAN (DEBIT)</th>
                <th style="width: 33.3%;">TOTAL BEBAN (KREDIT)</th>
                <th style="width: 33.4%;">{{ $isSurplus ? 'SURPLUS BERSIH' : 'DEFISIT BERSIH' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td>Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                <td class="saldo-akhir">{{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- TABEL UTAMA LAPORAN LABA RUGI -->
    <table class="tabel-data">
        <thead>
            <tr>
                <th style="width: 6%;">NO</th>
                <th style="width: 48%;">AKUN / KOMPONEN KEUANGAN</th>
                <th style="width: 15%;">TRANSAKSI</th>
                <th style="width: 19%;">JUMLAH (RP)</th>
                <th style="width: 12%;">RASIO</th>
            </tr>
        </thead>
        <tbody>
            <!-- BAGIAN I: PENDAPATAN + SALDO PERIODE SEBELUMNYA -->
            <tr class="section-header">
                <td colspan="5">I. PENDAPATAN / PENERIMAAN KAS OPERASIONAL</td>
            </tr>
            <tr>
                <td class="text-center">1</td>
                <td class="text-left" style="font-weight: bold;">Saldo Kas Periode Sebelumnya (Bulan Lalu)</td>
                <td class="text-center">Saldo Awal</td>
                <td class="text-right nowrap" style="font-weight: bold;">Rp {{ number_format($saldoAwal ?? 0, 0, ',', '.') }}</td>
                <td class="text-center">{{ $persentaseSaldoAwal ?? (($totalPendapatan > 0 && ($saldoAwal ?? 0) > 0) ? round((($saldoAwal ?? 0) / $totalPendapatan) * 100, 1) : 0) }}%</td>
            </tr>
            @forelse($pendapatans as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 2 }}</td>
                    <td class="text-left">{{ $item->kategori }}</td>
                    <td class="text-center">{{ $item->jumlah_transaksi }} transaksi</td>
                    <td class="text-right nowrap">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->persentase }}%</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td colspan="4" style="font-style: italic; padding: 6px 10px;">Tidak ada tambahan penerimaan kas baru pada periode ini (Rp 0).</td>
                </tr>
            @endforelse
            <tr class="subtotal-row">
                <td colspan="3" class="text-center">JUMLAH PENDAPATAN OPERASIONAL (A)</td>
                <td class="text-right nowrap">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td class="text-center">100.0%</td>
            </tr>

            <!-- BAGIAN II: BEBAN -->
            <tr class="section-header">
                <td colspan="5">II. BEBAN / PENGELUARAN KAS OPERASIONAL</td>
            </tr>
            @forelse($bebans as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-left">{{ $item->kategori }}</td>
                    <td class="text-center">{{ $item->jumlah_transaksi }} transaksi</td>
                    <td class="text-right nowrap">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $item->persentase }}%</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td colspan="4" style="font-style: italic; padding: 10px;">Tidak ada data pengeluaran pada periode ini.</td>
                </tr>
            @endforelse
            <tr class="subtotal-row">
                <td colspan="3" class="text-center">JUMLAH BEBAN OPERASIONAL (B)</td>
                <td class="text-right nowrap">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                <td class="text-center">100.0%</td>
            </tr>

            <!-- BAGIAN III: HASIL AKHIR (A - B) -->
            <tr class="total-akhir-row">
                <td colspan="3" class="text-center">
                    TOTAL {{ $isSurplus ? 'SURPLUS KAS OPERASIONAL (A - B)' : 'DEFISIT KAS OPERASIONAL (A - B)' }}
                </td>
                <td class="text-right nowrap highlight-cell">
                    {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </td>
                <td class="text-center highlight-cell">
                    {{ $marginSurplusDefisit }}%
                </td>
            </tr>
        </tbody>
    </table>

    <!-- TANDA TANGAN: 2 DI ATAS, 1 DI TENGAH BAWAH -->
    <div class="pengesahan">
        <!-- BARIS 1: 2 TTD DI ATAS -->
        <table>
            <tr>
                <td style="width: 50%;">
                    <p style="margin: 0; font-weight: bold;">Mengetahui,</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">Danil Ali Sabila</p>
                    <p class="ttd-jabatan">Ketua Umum AFPA</p>
                </td>
                <td style="width: 50%;">
                    <p style="margin: 0;">Putak, {{ $tanggalAkhirRiil }}</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">Azwil Yoga Pratama</p>
                    <p class="ttd-jabatan">Bendahara AFPA</p>
                </td>
            </tr>
        </table>
        <!-- BARIS 2: 1 TTD DI BAWAH TENGAH -->
        <table style="margin-top: 14px;">
            <tr>
                <td style="width: 30%;"></td>
                <td style="width: 40%;">
                    <p style="margin: 0; font-weight: bold;">Mengetahui,</p>
                    <div class="ttd-space"></div>
                    <p class="ttd-nama">Ust Imam Qusyairi</p>
                    <p class="ttd-jabatan">Bendahara Pusat PPAF</p>
                </td>
                <td style="width: 30%;"></td>
            </tr>
        </table>
    </div>

</body>
</html>
