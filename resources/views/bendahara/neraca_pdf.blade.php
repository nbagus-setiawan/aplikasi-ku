<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title></title>
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
            border-bottom: 2pt solid #000000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .header-kop table {
            width: 100%;
            border-collapse: collapse;
        }

        .judul-laporan {
            text-align: center;
            margin-top: 10px;
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

        /* BADGE STATUS SEIMBANG */
        .badge-balance {
            text-align: center;
            margin-bottom: 12px;
        }

        .badge-balance span {
            display: inline-block;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 0.4px;
            background-color: #f0fdf4;
            color: #166534;
            border: 1pt solid #86efac;
            padding: 4px 12px;
            border-radius: 4px;
        }

        /* TABEL SKONTRO DUA SISI (ASET VS PASIVA) */
        table.tabel-skontro {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 8.5pt;
            border: 1pt solid #94a3b8;
        }

        table.tabel-skontro th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            letter-spacing: 0.4px;
            border: 1pt solid #94a3b8;
            padding: 8px 10px;
            text-align: left;
            vertical-align: middle;
        }

        table.tabel-skontro td {
            border: 1pt solid #cbd5e1;
            padding: 8px 10px;
            vertical-align: top;
            background-color: #ffffff;
        }

        .section-title {
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            background-color: #f1f5f9;
            color: #0f172a;
            padding: 5px 8px;
            margin-bottom: 6px;
            border: 1pt solid #cbd5e1;
            border-left: 3pt solid #047857;
        }

        table.sub-tabel {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        table.sub-tabel td {
            border: none;
            border-bottom: 1pt solid #f1f5f9;
            padding: 4.5px 4px;
            font-size: 8.5pt;
            color: #0f172a;
        }

        table.sub-tabel tr.subtotal td {
            border-top: 1pt solid #94a3b8;
            border-bottom: 1pt solid #94a3b8;
            font-weight: bold;
            padding: 6px 4px;
            background-color: #f8fafc;
            color: #0f172a;
        }

        .total-box {
            background-color: #f1f5f9 !important;
            font-weight: bold;
            font-size: 9.5pt;
            border-top: 1pt solid #000000 !important;
            border-bottom: 1pt solid #000000 !important;
            padding: 8px 10px !important;
            color: #0f172a !important;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .nowrap { white-space: nowrap; }
        .font-mono { font-family: Consolas, 'Courier New', Courier, monospace; }
        .text-nominal { color: #047857; font-weight: bold; font-family: Consolas, 'Courier New', Courier, monospace; }

        /* LEMBAR TANDA TANGAN (2 DI ATAS, 1 DI BAWAH TENGAH) */
        .pengesahan-container {
            margin-top: 22px;
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
    <script>
        function sesuaikanUrlCetak() {
            document.title = '';
            if (window.history && window.history.replaceState) {
                try {
                    window.history.replaceState({}, '', 'https://ponpesalfalahputak.my.id');
                } catch (e) {}
            }
        }
        window.addEventListener('DOMContentLoaded', sesuaikanUrlCetak);
        window.addEventListener('beforeprint', sesuaikanUrlCetak);
    </script>
</head>
<body>

    @if(!empty($isHtmlPrint))
    <div class="no-print">
        <button onclick="sesuaikanUrlCetak(); window.print()">Cetak Dokumen</button>
        <a href="{{ route('admin.bendahara.neraca') }}" style="color: #cbd5e1; text-decoration: none; margin-left: 10px; font-size: 9pt;">Kembali</a>
    </div>
    @endif

    <!-- KOP RESMI PONDOK PESANTREN AL-FALAH PUTRA (SIMETRIS & RATA TENGAH) -->
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
                            <img src="{{ $logo }}" style="width: 70px; height: auto;" alt="Logo">
                        @elseif(file_exists(public_path('images/logo-pesantren.png')))
                            <img src="{{ public_path('images/logo-pesantren.png') }}" style="width: 70px; height: auto;" alt="Logo">
                        @else
                            <img src="{{ public_path('images/logo.png') }}" style="width: 70px; height: auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="text-align: center; vertical-align: middle; padding: 0 10px;">
                        @if(!empty($kaligrafi))
                            <div style="margin-bottom: 3px;">
                                <img src="{{ $kaligrafi }}" style="height: 22px; width: auto;" alt="Kaligrafi">
                            </div>
                        @elseif(file_exists(public_path('images/kaligrafi-kop.png')))
                            <div style="margin-bottom: 3px;">
                                <img src="{{ public_path('images/kaligrafi-kop.png') }}" style="height: 22px; width: auto;" alt="Kaligrafi">
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
        <h2>LAPORAN NERACA (POSISI KEUANGAN KAS &amp; SALDO DANA)</h2>
        <p>{{ $labelPeriode }}</p>
    </div>

    <!-- BADGE STATUS SEIMBANG -->
    <div class="badge-balance">
        <span>STATUS: SEIMBANG (BALANCE) &bull; ASET = KEWAJIBAN + EKUITAS DANA</span>
    </div>

    <!-- TABEL UTAMA NERACA SKONTRO (AKTIVA KIRI VS PASIVA KANAN) -->
    <table class="tabel-skontro">
        <thead>
            <tr>
                <th style="width: 50%;">AKTIVA (ASET / KEKAYAAN)</th>
                <th style="width: 50%;">PASIVA (KEWAJIBAN &amp; EKUITAS DANA)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <!-- SISI KIRI: ASET / AKTIVA -->
                <td style="width: 50%;">
                    <!-- I. ASET LANCAR -->
                    <div class="section-title">I. ASET LANCAR</div>
                    <table class="sub-tabel">
                        @foreach($asetLancar as $al)
                            <tr>
                                <td>{{ $al['nama'] }}</td>
                                <td class="text-right font-mono nowrap">
                                    {{ $al['nominal'] > 0 ? 'Rp ' . number_format($al['nominal'], 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="subtotal">
                            <td>Jumlah Aset Lancar</td>
                            <td class="text-right font-mono nowrap" style="color: #047857;">
                                Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>

                    <!-- II. ASET TETAP -->
                    <div class="section-title" style="margin-top: 10px;">II. ASET TETAP (INVENTARIS)</div>
                    <table class="sub-tabel">
                        @foreach($asetTetap as $at)
                            <tr>
                                <td>{{ $at['nama'] }}</td>
                                <td class="text-right font-mono nowrap">
                                    {{ $at['nominal'] > 0 ? 'Rp ' . number_format($at['nominal'], 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="subtotal">
                            <td>Jumlah Aset Tetap</td>
                            <td class="text-right font-mono nowrap">
                                Rp {{ number_format($totalAsetTetap, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </td>

                <!-- SISI KANAN: PASIVA (KEWAJIBAN & EKUITAS) -->
                <td style="width: 50%;">
                    <!-- I. KEWAJIBAN -->
                    <div class="section-title">I. KEWAJIBAN (LIABILITAS)</div>
                    <table class="sub-tabel">
                        @foreach($kewajiban as $kw)
                            <tr>
                                <td>{{ $kw['nama'] }}</td>
                                <td class="text-right font-mono nowrap">
                                    {{ $kw['nominal'] > 0 ? 'Rp ' . number_format($kw['nominal'], 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="subtotal">
                            <td>Jumlah Kewajiban</td>
                            <td class="text-right font-mono nowrap">
                                Rp {{ number_format($totalKewajiban, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>

                    <!-- II. EKUITAS DANA -->
                    <div class="section-title" style="margin-top: 10px;">II. EKUITAS DANA (SALDO DANA)</div>
                    <table class="sub-tabel">
                        @foreach($ekuitas as $ek)
                            <tr>
                                <td>{{ $ek['nama'] }}</td>
                                <td class="text-right font-mono nowrap">
                                    {{ $ek['nominal'] < 0 ? '-' : '' }}Rp {{ number_format(abs($ek['nominal']), 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                        <tr class="subtotal">
                            <td>Jumlah Ekuitas Dana</td>
                            <td class="text-right font-mono nowrap" style="color: #047857;">
                                Rp {{ number_format($totalEkuitas, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- BARIS REKAPITULASI TOTAL SEIMBANG -->
            <tr>
                <td class="total-box">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td>TOTAL ASET (AKTIVA):</td>
                            <td class="text-right font-mono nowrap" style="color: #047857; font-size: 10.5pt;">
                                Rp {{ number_format($totalAset, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="total-box">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td>TOTAL KEWAJIBAN &amp; EKUITAS:</td>
                            <td class="text-right font-mono nowrap" style="color: #047857; font-size: 10.5pt;">
                                Rp {{ number_format($totalPasiva, 0, ',', '.') }}
                            </td>
                        </tr>
                    </table>
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
