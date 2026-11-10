<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>&#8203;</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm 1.3cm 1.1cm 1.3cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #000000;
            line-height: 1.3;
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
            font-size: 13pt;
            letter-spacing: 0.3px;
            margin: 0;
        }
        .kop-pondok {
            font-family: 'Times New Roman', Times, serif;
            font-size: 22pt;
            font-weight: bold;
            line-height: 1.05;
            margin: 0;
        }
        .kop-alamat {
            font-size: 8.5pt;
            font-weight: bold;
            margin: 2px 0 0;
        }
        .kop-kontak {
            font-size: 8pt;
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

        /* ===== TABEL ===== */
        table.tabel-kas {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
        }
        table.tabel-kas th,
        table.tabel-kas td {
            border: 1pt solid #000000;
            padding: 5px 6px;
            vertical-align: middle;
        }
        table.tabel-kas th {
            background-color: #a6a6a6;
            font-weight: bold;
            font-size: 8.5pt;
            text-align: center;
            padding: 7px 6px;
        }
        table.tabel-kas td {
            text-align: center;
        }
        thead { display: table-header-group; }
        tr { page-break-inside: avoid; }

        .text-left { text-align: left !important; }
        .text-right { text-align: right !important; }
        .nowrap { white-space: nowrap; }
        .bold { font-weight: bold; }

        .saldo-awal-row td {
            background-color: #f2f2f2;
            font-style: italic;
        }
        .total-row td {
            font-weight: bold;
            padding: 7px 6px;
        }
        .total-row td.saldo-akhir {
            background-color: #a6a6a6;
        }

        /* ===== TANDA TANGAN ===== */
        .pengesahan {
            width: 100%;
            margin-top: 22px;
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
        .ttd-space { height: 58px; }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
            margin: 0;
        }
        .ttd-jabatan {
            font-style: italic;
            margin: 1px 0 0;
        }

        @media print {
            @page {
                size: A4 landscape;
                margin-top: 0cm !important;
                margin-bottom: 1.1cm !important;
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
    <!-- Toolbar Pratinjau Cetak Browser (Otomatis Disembunyikan Saat Cetak/PDF) -->
    <div class="no-print" style="background: #ffffff; border-bottom: 2px solid #047857; padding: 10px 18px; margin: -10px -10px 14px -10px; display: flex; align-items: center; justify-content: space-between; font-family: sans-serif; box-shadow: 0 1px 3px rgba(0,0,0,0.1); gap: 12px;">
        <div>
            <strong style="color: #064e3b; font-size: 13px; display: block;">Pratinjau Cetak Buku Kas (Format Landscape)</strong>
            <p style="margin: 2px 0 0; font-size: 11px; color: #475569;">Tata letak mengikuti template resmi. Klik tombol di kanan untuk Simpan PDF atau Cetak.</p>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <button onclick="sesuaikanUrlCetak(); window.print();" type="button" style="background: #047857; color: white; border: none; padding: 7px 15px; border-radius: 7px; font-weight: bold; font-size: 11.5px; cursor: pointer;">
                Cetak / Simpan PDF
            </button>
            <a href="{{ route('admin.bendahara.index') }}" style="background: #e2e8f0; color: #334155; text-decoration: none; padding: 7px 13px; border-radius: 7px; font-weight: bold; font-size: 11.5px; display: inline-block;">
                &larr; Kembali
            </a>
        </div>
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

    <!-- KOP RESMI LEMBAGA -->
    @if (!empty($kopGambar))
        <div style="text-align: center; margin-bottom: 16px;">
            <img src="{{ $kopGambar }}" style="width: 100%; max-height: 130px; height: auto; display: block;" alt="Kop Surat">
        </div>
    @else
        <div class="header-kop">
            <table>
                <tr>
                    <td style="width: 120px; text-align: center;">
                        @if (!empty($logo))
                            <img src="{{ $logo }}" style="width: 105px; height: auto;" alt="Logo">
                        @elseif (file_exists(public_path('images/logo-pesantren.png')))
                            <img src="{{ public_path('images/logo-pesantren.png') }}" style="width: 105px; height: auto;" alt="Logo">
                        @endif
                    </td>
                    <td style="text-align: center;">
                        @if(!empty($kaligrafi))
                            <img src="{{ $kaligrafi }}" style="height: 34px; width: auto;" alt="Kaligrafi">
                        @elseif(file_exists(public_path('images/kaligrafi-kop.png')))
                            <img src="{{ public_path('images/kaligrafi-kop.png') }}" style="height: 34px; width: auto;" alt="Kaligrafi">
                        @endif
                        <p class="kop-yayasan">YAYASAN PONDOK PESANTREN SALAFIYAH AL-FALAH</p>
                        <p class="kop-pondok">PONDOK PUTRA</p>
                        <p class="kop-alamat">Dusun II Ds. Putak Kec. Gelumbang Kab. Muara Enim Sumatera Selatan Kode Pos 31171</p>
                        <p class="kop-kontak">Telepon: 6282118702717 &nbsp;&nbsp; E-mail: pengurusalfalahputra@gmail.com</p>
                    </td>
                    <td style="width: 120px;"></td>
                </tr>
            </table>
        </div>
    @endif

    <!-- JUDUL LAPORAN -->
    <div class="judul-laporan">
        <p class="judul">LAPORAN BUKU KAS BULANAN</p>
        <p class="judul">PONDOK PUTRA</p>
        <p class="periode">
            PERIODE : {{ strtoupper($labelPeriode ?? 'Semua Transaksi Pembukuan') }}
            @if(!empty($kategori)) &nbsp;|&nbsp; AKUN : {{ strtoupper($kategori) }} @endif
            @if(!empty($jenis)) &nbsp;|&nbsp; JENIS : {{ strtoupper($jenis) }} @endif
        </p>
    </div>

    <!-- TABEL BUKU KAS (SESUAI TEMPLATE RESMI) -->
    <table class="tabel-kas">
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width: 12%;" class="nowrap">TANGGAL TRANSAKSI</th>
                <th style="width: 10%;">No. BUKTI</th>
                <th style="width: 13%;">AKUN</th>
                <th style="width: 25%;">KETERANGAN</th>
                <th style="width: 12%;">DEBIT</th>
                <th style="width: 12%;">KREDIT</th>
                <th style="width: 12%;">SALDO</th>
            </tr>
        </thead>
        <tbody>
            @php $currentSaldo = (float) $saldoAwal; @endphp

            @if($dari && $saldoAwal != 0)
            <tr class="saldo-awal-row">
                <td></td>
                <td class="nowrap">{{ date('d-m-Y', strtotime($dari)) }}</td>
                <td>-</td>
                <td colspan="2" class="text-left">Saldo awal sebelum periode</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
                <td class="text-right nowrap">Rp {{ number_format($currentSaldo, 0, ',', '.') }}</td>
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
                    <td>{{ $idx + 1 }}</td>
                    <td class="nowrap">{{ $t->tanggal ? date('d-m-Y', strtotime($t->tanggal)) : '-' }}</td>
                    <td class="nowrap">{{ $t->kode_transaksi }}</td>
                    <td>{{ $t->kategori }}</td>
                    <td class="text-left">{{ $t->keterangan ?: '-' }}</td>
                    <td class="text-right nowrap">{{ $t->jenis === 'debit' ? 'Rp ' . number_format((float) $t->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="text-right nowrap">{{ $t->jenis === 'kredit' ? 'Rp ' . number_format((float) $t->nominal, 0, ',', '.') : '-' }}</td>
                    <td class="text-right nowrap">Rp {{ number_format((float) $currentSaldo, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="padding: 14px;">Tidak ada transaksi kas yang tercatat pada periode ini.</td>
                </tr>
            @endforelse

            <!-- TOTAL MUTASI & SALDO AKHIR -->
            <tr class="total-row">
                <td></td>
                <td colspan="4">TOTAL MUTASI &amp; SALDO AKHIR</td>
                <td class="text-right nowrap">Rp {{ number_format((float) $totalDebit, 0, ',', '.') }}</td>
                <td class="text-right nowrap">Rp {{ number_format((float) $totalKredit, 0, ',', '.') }}</td>
                <td class="text-right nowrap saldo-akhir">Rp {{ number_format((float) $currentSaldo, 0, ',', '.') }}</td>
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
                    <p style="margin: 0;">Putak, {{ $tanggalAkhirRiil ?? date('d-m-Y') }}</p>
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
