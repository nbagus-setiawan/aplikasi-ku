<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 1cm 1.2cm 1.2cm 1.2cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 9.5pt;
            line-height: 1.35;
            color: #0f172a;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        .header-kop {
            width: 100%;
            border-bottom: 2pt solid #000000;
            padding-bottom: 8px;
            margin-bottom: 20px;
        }

        .header-kop table {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }

        .header-kop td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .judul-laporan {
            text-align: center;
            margin-top: 10px;
            margin-bottom: 16px;
        }

        .judul-laporan h2 {
            font-size: 12.5pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #0f172a;
        }

        .judul-laporan p {
            font-size: 9pt;
            margin: 3px 0 0;
            color: #334155;
        }

        .ringkasan-box {
            width: 100%;
            border: 1pt solid #cbd5e1;
            background-color: #f8fafc;
            border-radius: 4px;
            margin-bottom: 14px;
            border-collapse: collapse;
        }

        .ringkasan-box td {
            padding: 7px 10px;
            font-size: 9pt;
            text-align: center;
            border-right: 1pt solid #e2e8f0;
        }

        .ringkasan-box td:last-child {
            border-right: none;
        }

        .ringkasan-box .nilai {
            font-size: 10.5pt;
            font-weight: bold;
            font-family: Consolas, 'Courier New', Courier, monospace;
            display: block;
            margin-top: 2px;
        }

        .text-surplus {
            color: #047857;
        }

        .text-defisit {
            color: #b91c1c;
        }

        table.tabel-data {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }

        table.tabel-data th, table.tabel-data td {
            border: 1pt solid #cbd5e1;
            padding: 5px 7px;
            vertical-align: middle;
        }

        table.tabel-data thead th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8pt;
            letter-spacing: 0.3px;
            border: 1pt solid #94a3b8;
            padding: 6px 7px;
            text-align: left;
        }

        table.tabel-data tr.section-header td {
            background-color: #e2e8f0;
            font-weight: bold;
            font-size: 8.5pt;
            color: #0f172a;
            border: 1pt solid #94a3b8;
            padding: 6px 7px;
            letter-spacing: 0.3px;
        }

        table.tabel-data tr.subtotal-row td {
            background-color: #f8fafc;
            font-weight: bold;
            border-top: 1pt solid #94a3b8;
            border-bottom: 1pt solid #94a3b8;
            padding: 6px 7px;
        }

        table.tabel-data tr.total-akhir-row td {
            background-color: #f1f5f9;
            font-weight: bold;
            font-size: 9pt;
            border-top: 1pt solid #000000;
            border-bottom: 1pt solid #000000;
            padding: 7px 7px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .nowrap { white-space: nowrap; }
        .font-mono { font-family: Consolas, 'Courier New', Courier, monospace; }

        /* LEMBAR TANDA TANGAN (2 DI ATAS, 1 DI BAWAH TENGAH) */
        .pengesahan-container {
            margin-top: 18px;
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
            height: 50px;
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
        <button onclick="sesuaikanUrlCetak(); window.print();">Cetak Dokumen</button>
        <a href="{{ route('admin.bendahara.laba-rugi') }}" style="color: #cbd5e1; text-decoration: none; margin-left: 10px; font-size: 9pt;">Kembali</a>
    </div>
    @endif

    <script>
        function sesuaikanUrlCetak() {
            try {
                document.title = '';
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, '', 'https://ponpesalfalahputak.my.id');
                }
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
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="{{ $kopGambar }}" style="width: 100%; max-height: 115px; height: auto; display: block;" alt="Kop Surat">
        </div>
    @else
        <div class="header-kop">
            <table>
                <tr>
                    <td style="width: 80px; text-align: center; vertical-align: middle;">
                        @if(!empty($logo))
                            <img src="{{ $logo }}" style="width: 70px; height: auto; display: block; margin: 0 auto;" alt="Logo">
                        @elseif(file_exists(public_path('images/logo-pesantren.png')))
                            <img src="{{ asset('images/logo-pesantren.png') }}" style="width: 70px; height: auto; display: block; margin: 0 auto;" alt="Logo">
                        @else
                            <img src="{{ asset('images/logo.png') }}" style="width: 70px; height: auto; display: block; margin: 0 auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="text-align: center; vertical-align: middle; padding: 0 10px;">
                        @if(!empty($kaligrafi))
                            <div style="margin-bottom: 3px;">
                                <img src="{{ $kaligrafi }}" style="height: 22px; width: auto; display: inline-block;" alt="Kaligrafi">
                            </div>
                        @elseif(file_exists(public_path('images/kaligrafi-kop.png')))
                            <div style="margin-bottom: 3px;">
                                <img src="{{ asset('images/kaligrafi-kop.png') }}" style="height: 22px; width: auto; display: inline-block;" alt="Kaligrafi">
                            </div>
                        @endif
                        <div style="font-size: 11pt; font-weight: bold; letter-spacing: 0.5px; margin: 0; text-transform: uppercase; color: #000000;">
                            YAYASAN PONDOK PESANTREN SALAFIYAH AL-FALAH
                        </div>
                        <div style="font-size: 14pt; font-weight: bold; letter-spacing: 0.8px; margin: 2px 0; text-transform: uppercase; color: #064e3b;">
                            PONDOK PUTRA
                        </div>
                        <div style="font-size: 8pt; margin: 1px 0; color: #1e293b;">
                            Dusun II Ds. Putak Kec. Gelumbang Kab. Muara Enim Sumatera Selatan Kode Pos 31171
                        </div>
                        <div style="font-size: 8pt; margin: 1px 0; color: #334155;">
                            Telepon: 6282118702717 &nbsp;&bull;&nbsp; E-mail: pengurusalfalahputra@gmail.com
                        </div>
                    </td>
                    <td style="width: 80px;"></td>
                </tr>
            </table>
        </div>
    @endif

    <!-- JUDUL LAPORAN -->
    <div class="judul-laporan">
        <h2>LAPORAN LABA RUGI (SURPLUS / DEFISIT KAS)</h2>
        <p>Periode: <strong>{{ $labelPeriode }}</strong></p>
    </div>

    <!-- KOTAK IKHTISAR -->
    <table class="ringkasan-box">
        <tr>
            <td style="width: 33.3%;">
                <span style="color: #475569; font-size: 8pt; text-transform: uppercase;">Total Pendapatan (Debit)</span>
                <span class="nilai text-surplus">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </td>
            <td style="width: 33.3%;">
                <span style="color: #475569; font-size: 8pt; text-transform: uppercase;">Total Beban (Kredit)</span>
                <span class="nilai text-defisit">Rp {{ number_format($totalBeban, 0, ',', '.') }}</span>
            </td>
            <td style="width: 33.3%;">
                <span style="color: #475569; font-size: 8pt; text-transform: uppercase;">{{ $isSurplus ? 'Surplus Bersih' : 'Defisit Bersih' }}</span>
                <span class="nilai {{ $isSurplus ? 'text-surplus' : 'text-defisit' }}">
                    {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </span>
            </td>
        </tr>
    </table>

    <!-- TABEL UTAMA LAPORAN LABA RUGI (STANDAR LAPORAN KEUANGAN RESMI) -->
    <table class="tabel-data">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">NO</th>
                <th style="width: 52%;">KOMPONEN / POS AKUN KEUANGAN</th>
                <th style="width: 15%; text-align: center;">TRANSAKSI</th>
                <th style="width: 17%; text-align: right;">NOMINAL (RP)</th>
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
                    <td style="font-weight: 500;">{{ $item->kategori }}</td>
                    <td class="text-center">{{ $item->jumlah_transaksi }} transaksi</td>
                    <td class="text-right font-mono nowrap text-nominal">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ $item->persentase }}%</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td colspan="4" style="font-style: italic; color: #64748b; padding: 10px;">Tidak ada data penerimaan pada periode ini.</td>
                </tr>
            @endforelse
            <tr class="subtotal-row">
                <td colspan="3" class="text-right" style="letter-spacing: 0.3px;">JUMLAH PENDAPATAN OPERASIONAL (A)</td>
                <td class="text-right font-mono nowrap" style="color: #047857;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td class="text-right font-mono" style="color: #047857;">100.0%</td>
            </tr>

            <!-- BAGIAN II: BEBAN (PENGELUARAN KAS) -->
            <tr class="section-header">
                <td colspan="5">II. BEBAN / PENGELUARAN KAS OPERASIONAL</td>
            </tr>
            @forelse($bebans as $idx => $item)
                <tr>
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td style="font-weight: 500;">{{ $item->kategori }}</td>
                    <td class="text-center">{{ $item->jumlah_transaksi }} transaksi</td>
                    <td class="text-right font-mono nowrap">Rp {{ number_format($item->total, 0, ',', '.') }}</td>
                    <td class="text-right font-mono">{{ $item->persentase }}%</td>
                </tr>
            @empty
                <tr>
                    <td class="text-center">-</td>
                    <td colspan="4" style="font-style: italic; color: #64748b; padding: 10px;">Tidak ada data pengeluaran pada periode ini.</td>
                </tr>
            @endforelse
            <tr class="subtotal-row">
                <td colspan="3" class="text-right" style="letter-spacing: 0.3px;">JUMLAH BEBAN OPERASIONAL (B)</td>
                <td class="text-right font-mono nowrap" style="color: #b91c1c;">Rp {{ number_format($totalBeban, 0, ',', '.') }}</td>
                <td class="text-right font-mono" style="color: #b91c1c;">100.0%</td>
            </tr>

            <!-- BAGIAN III: HASIL AKHIR (SURPLUS / DEFISIT BERSIH) -->
            <tr class="total-akhir-row">
                <td colspan="3" class="text-right" style="letter-spacing: 0.3px;">
                    HASIL BERSIH OPERASIONAL: {{ $isSurplus ? 'SURPLUS KAS (A - B)' : 'DEFISIT KAS (A - B)' }}
                </td>
                <td class="text-right font-mono nowrap" style="font-size: 9.5pt; color: {{ $isSurplus ? '#047857' : '#b91c1c' }};">
                    {{ $isSurplus ? '+' : '' }}Rp {{ number_format($surplusDefisit, 0, ',', '.') }}
                </td>
                <td class="text-right font-mono" style="color: {{ $isSurplus ? '#047857' : '#b91c1c' }};">
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
