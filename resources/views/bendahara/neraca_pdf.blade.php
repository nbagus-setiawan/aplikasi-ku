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
            margin-bottom: 12px;
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

        /* ===== KOTAK STATUS SEIMBANG ===== */
        table.status-box {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.status-box td {
            border: 1pt solid #000000;
            background-color: #f2f2f2;
            text-align: center;
            font-size: 8pt;
            font-weight: bold;
            padding: 5px 8px;
            letter-spacing: 0.3px;
        }

        /* ===== TABEL SKONTRO 4 KOLOM SEJAJAR (AKTIVA VS PASIVA) ===== */
        table.tabel-skontro {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
            font-size: 8.5pt;
        }

        table.tabel-skontro th,
        table.tabel-skontro td {
            border: 1pt solid #000000;
            padding: 5px 6px;
            vertical-align: middle;
        }

        table.tabel-skontro thead th {
            background-color: #a6a6a6;
            color: #000000;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 8.5pt;
            padding: 7px 6px;
            text-align: center;
        }

        table.tabel-skontro td.section-cell {
            background-color: #d9d9d9;
            font-weight: bold;
            font-size: 8pt;
            text-transform: uppercase;
            padding: 5px 7px;
        }

        table.tabel-skontro td.subtotal-cell {
            background-color: #f2f2f2;
            font-weight: bold;
            padding: 6px 7px;
        }

        table.tabel-skontro tr.total-row td {
            font-weight: bold;
            font-size: 8.5pt;
            padding: 7px 6px;
        }

        table.tabel-skontro tr.total-row td.highlight-cell {
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
        <button onclick="sesuaikanUrlCetak(); window.print()">Cetak Dokumen</button>
        <a href="{{ route('admin.bendahara.neraca') }}" style="color: #cbd5e1; text-decoration: none; margin-left: 10px; font-size: 9pt;">Kembali</a>
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
        <p class="judul">LAPORAN NERACA (POSISI KEUANGAN KAS &amp; SALDO DANA)</p>
        <p class="judul">PONDOK PUTRA</p>
        <p class="periode">PERIODE : {{ strtoupper($labelPeriode) }}</p>
    </div>

    <!-- KOTAK STATUS SEIMBANG -->
    <table class="status-box">
        <tr>
            <td>STATUS NERACA : SEIMBANG (BALANCE) &mdash; TOTAL AKTIVA = TOTAL PASIVA</td>
        </tr>
    </table>

    @php
        $alList = array_values($asetLancar ?? []);
        $atList = array_values($asetTetap ?? []);
        $kwList = array_values($kewajiban ?? []);
        $ekList = array_values($ekuitas ?? []);

        $onlyKasDanEkuitas = empty($atList) && empty($kwList);
    @endphp

    <!-- TABEL UTAMA NERACA SKONTRO SEJAJAR (4 KOLOM PRESISI) -->
    <table class="tabel-skontro">
        <thead>
            <tr>
                <th colspan="2" style="width: 50%;">AKTIVA (ASET KAS PESANTREN)</th>
                <th colspan="2" style="width: 50%;">PASIVA (EKUITAS SALDO DANA KAS)</th>
            </tr>
        </thead>
        <tbody>
            @if($onlyKasDanEkuitas)
                @php
                    $maxRows = max(count($alList), count($ekList));
                @endphp
                <tr>
                    <td colspan="2" class="section-cell">I. ASET LANCAR (KAS PESANTREN)</td>
                    <td colspan="2" class="section-cell">I. EKUITAS DANA (SALDO DANA KAS)</td>
                </tr>
                @for($i = 0; $i < $maxRows; $i++)
                    @php
                        $al = $alList[$i] ?? null;
                        $ek = $ekList[$i] ?? null;
                    @endphp
                    <tr>
                        <td style="width: 32%;">{{ $al['nama'] ?? '' }}</td>
                        <td class="text-right nowrap" style="width: 18%;">
                            @if($al)
                                {{ $al['nominal'] < 0 ? '-' : '' }}Rp {{ number_format(abs($al['nominal']), 0, ',', '.') }}
                            @endif
                        </td>
                        <td style="width: 32%;">{{ $ek['nama'] ?? '' }}</td>
                        <td class="text-right nowrap" style="width: 18%;">
                            @if($ek)
                                {{ $ek['nominal'] < 0 ? '-' : '' }}Rp {{ number_format(abs($ek['nominal']), 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                @endfor
                <tr>
                    <td class="subtotal-cell text-center">Jumlah Aset Kas Lancar</td>
                    <td class="subtotal-cell text-right nowrap">Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}</td>
                    <td class="subtotal-cell text-center">{{ $totalEkuitas >= 0 ? 'Surplus Kas Operasional (Saldo Dana)' : 'Defisit Kas Operasional (Saldo Dana)' }}</td>
                    <td class="subtotal-cell text-right nowrap">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</td>
                </tr>
            @else
                @php
                    $maxBagian1 = max(count($alList), count($kwList));
                    $maxBagian2 = max(count($atList), count($ekList));
                @endphp
                <!-- BAGIAN I: ASET LANCAR VS KEWAJIBAN -->
                <tr>
                    <td colspan="2" class="section-cell">I. ASET LANCAR</td>
                    <td colspan="2" class="section-cell">I. KEWAJIBAN (LIABILITAS)</td>
                </tr>
                @for($i = 0; $i < $maxBagian1; $i++)
                    @php
                        $al = $alList[$i] ?? null;
                        $kw = $kwList[$i] ?? null;
                    @endphp
                    <tr>
                        <td style="width: 32%;">{{ $al['nama'] ?? '' }}</td>
                        <td class="text-right nowrap" style="width: 18%;">
                            @if($al)
                                {{ $al['nominal'] > 0 ? 'Rp ' . number_format($al['nominal'], 0, ',', '.') : '-' }}
                            @endif
                        </td>
                        <td style="width: 32%;">{{ $kw['nama'] ?? '' }}</td>
                        <td class="text-right nowrap" style="width: 18%;">
                            @if($kw)
                                {{ $kw['nominal'] > 0 ? 'Rp ' . number_format($kw['nominal'], 0, ',', '.') : '-' }}
                            @endif
                        </td>
                    </tr>
                @endfor
                <tr>
                    <td class="subtotal-cell text-center">Jumlah Aset Lancar</td>
                    <td class="subtotal-cell text-right nowrap">Rp {{ number_format($totalAsetLancar, 0, ',', '.') }}</td>
                    <td class="subtotal-cell text-center">Jumlah Kewajiban</td>
                    <td class="subtotal-cell text-right nowrap">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</td>
                </tr>

                <!-- BAGIAN II: ASET TETAP VS EKUITAS DANA -->
                <tr>
                    <td colspan="2" class="section-cell">II. ASET TETAP (INVENTARIS)</td>
                    <td colspan="2" class="section-cell">II. EKUITAS DANA (SALDO DANA)</td>
                </tr>
                @for($i = 0; $i < $maxBagian2; $i++)
                    @php
                        $at = $atList[$i] ?? null;
                        $ek = $ekList[$i] ?? null;
                    @endphp
                    <tr>
                        <td>{{ $at['nama'] ?? '' }}</td>
                        <td class="text-right nowrap">
                            @if($at)
                                {{ $at['nominal'] > 0 ? 'Rp ' . number_format($at['nominal'], 0, ',', '.') : '-' }}
                            @endif
                        </td>
                        <td>{{ $ek['nama'] ?? '' }}</td>
                        <td class="text-right nowrap">
                            @if($ek)
                                {{ $ek['nominal'] < 0 ? '-' : '' }}Rp {{ number_format(abs($ek['nominal']), 0, ',', '.') }}
                            @endif
                        </td>
                    </tr>
                @endfor
                <tr>
                    <td class="subtotal-cell text-center">Jumlah Aset Tetap</td>
                    <td class="subtotal-cell text-right nowrap">Rp {{ number_format($totalAsetTetap, 0, ',', '.') }}</td>
                    <td class="subtotal-cell text-center">Jumlah Ekuitas Dana</td>
                    <td class="subtotal-cell text-right nowrap">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</td>
                </tr>
            @endif

            <!-- BARIS REKAPITULASI TOTAL SEIMBANG -->
            <tr class="total-row">
                <td class="text-center">TOTAL AKTIVA (ASET KAS)</td>
                <td class="text-right nowrap highlight-cell">Rp {{ number_format($totalAset, 0, ',', '.') }}</td>
                <td class="text-center">TOTAL PASIVA (EKUITAS DANA)</td>
                <td class="text-right nowrap highlight-cell">Rp {{ number_format($totalPasiva, 0, ',', '.') }}</td>
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
