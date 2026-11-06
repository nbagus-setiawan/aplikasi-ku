<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Buku Kas Umum - {{ $pengaturans['nama_situs'] ?? 'Pondok Pesantren Al-Falah Putak' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 1cm 1.2cm 1cm 1.2cm;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9pt;
            color: #0f172a;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-kop {
            width: 100%;
            border-bottom: 3px double #0f172a;
            padding-bottom: 6px;
            margin-bottom: 12px;
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
            margin-bottom: 12px;
        }
        .judul-laporan h2 {
            margin: 0;
            font-size: 12.5pt;
            text-transform: uppercase;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.5px;
        }
        .judul-laporan p {
            margin: 3px 0 0;
            font-size: 9pt;
            color: #334155;
        }
        table.tabel-kas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
            font-size: 8.5pt;
        }
        table.tabel-kas th, table.tabel-kas td {
            border: 1px solid #334155;
            padding: 5px 6px;
            text-align: left;
            vertical-align: middle;
        }
        table.tabel-kas th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            font-size: 8pt;
            text-align: center;
            letter-spacing: 0.3px;
        }
        thead {
            display: table-header-group;
        }
        tr {
            page-break-inside: avoid;
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
            font-size: 9pt;
        }
        .pengesahan-container {
            width: 100%;
            page-break-inside: avoid;
            margin-top: 15px;
        }
        .tabel-ttd {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin: 0;
        }
        .tabel-ttd td {
            border: none;
            padding: 0;
            vertical-align: top;
            font-size: 9pt;
            line-height: 1.35;
        }
        .ttd-space {
            height: 55px;
        }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0 !important; }
        }
    </style>
</head>
<body>

    @if(!empty($isHtmlPrint))
    <!-- Toolbar Pratinjau Cetak Browser (Otomatis Disembunyikan Saat Cetak/PDF) -->
    <div class="no-print" style="background: #ffffff; border-bottom: 2px solid #047857; padding: 10px 18px; margin: -10px -10px 14px -10px; display: flex; align-items: center; justify-content: space-between; font-family: sans-serif; box-shadow: 0 1px 3px rgba(0,0,0,0.1); gap: 12px;">
        <div>
            <strong style="color: #064e3b; font-size: 13px; display: block;">Pratinjau Cetak Buku Kas Umum (Format Landscape)</strong>
            <p style="margin: 2px 0 0; font-size: 11px; color: #475569;">Tata letak telah dipresisikan sesuai dokumen resmi. Klik tombol di kanan untuk Simpan PDF atau Cetak.</p>
        </div>
        <div style="display: flex; gap: 8px; flex-shrink: 0;">
            <button onclick="sesuaikanUrlCetak(); window.print();" type="button" style="background: #047857; color: white; border: none; padding: 7px 15px; border-radius: 7px; font-weight: bold; font-size: 11.5px; cursor: pointer; display: inline-flex; align-items: center; gap: 6px;">
                <svg style="width: 14px; height: 14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                <span>Cetak / Simpan PDF</span>
            </button>
            <a href="{{ route('admin.bendahara.index') }}" style="background: #e2e8f0; color: #334155; text-decoration: none; padding: 7px 13px; border-radius: 7px; font-weight: bold; font-size: 11.5px; display: inline-block;">
                &larr; Kembali
            </a>
        </div>
    </div>

    <script>
        function sesuaikanUrlCetak() {
            try {
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, document.title, '{{ url('/') }}/');
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
    @endif

    <!-- KOP RESMI LEMBAGA (SESUAI DOKUMEN RESMI PONDOK PESANTREN) -->
    <div class="header-kop">
        <table>
            <tr>
                @if (!empty($logo))
                    <td style="width: 80px; text-align: center;">
                        <img src="{{ $logo }}" style="width: 68px; height: auto;" alt="Logo Lembaga">
                    </td>
                @else
                    <td style="width: 80px; text-align: center;">
                        <img src="{{ public_path('images/logo.png') }}" style="width: 68px; height: auto;" alt="Logo">
                    </td>
                @endif
                <td style="text-align: center; padding: 0 10px;">
                    <div style="font-size: 12pt; font-weight: bold; letter-spacing: 0.5px; margin: 0; text-transform: uppercase; color: #0f172a;">
                        YAYASAN PONDOK PESANTREN SALAFIYAH AL-FALAH
                    </div>
                    <div style="font-size: 15pt; font-weight: bold; letter-spacing: 1px; margin: 2px 0; text-transform: uppercase; color: #064e3b;">
                        PONDOK PUTRA
                    </div>
                    <div style="font-size: 8.5pt; margin: 1px 0; color: #334155;">
                        Dusun II Ds. Putak Kec. Gelumbang Kab. Muara Enim Sumatera Selatan Kode Pos 31171
                    </div>
                    <div style="font-size: 8.5pt; margin: 1px 0; color: #475569;">
                        Telepon: 6282118702717 &nbsp;&nbsp;&nbsp; E-mail: pengurusalfalahputra@gmail.com
                    </div>
                </td>
                <td style="width: 80px;"></td>
            </tr>
        </table>
    </div>

    <!-- JUDUL LAPORAN BUKU KAS -->
    <div class="judul-laporan">
        <h2>BUKU KAS UMUM (MUTASI DEBIT &amp; KREDIT)</h2>
        <p>
            Periode: <strong>{{ $labelPeriode ?? 'Semua Transaksi Pembukuan' }}</strong>
            @if(!empty($kategori)) &bull; Pos Akun: <strong>{{ $kategori }}</strong> @endif
            @if(!empty($jenis)) &bull; Jenis: <strong>{{ strtoupper($jenis) }}</strong> @endif
        </p>
    </div>

    <!-- TABEL BUKU KAS UMUM 8 KOLOM (PERSIS SESUAI DOKUMEN RESMI) -->
    <table class="tabel-kas">
        <thead>
            <tr>
                <th style="width: 3.5%;">NO</th>
                <th style="width: 9%;">TANGGAL</th>
                <th style="width: 13%;">NO. BUKTI</th>
                <th style="width: 14%;">POS AKUN / KATEGORI</th>
                <th style="width: 25%;">URAIAN TRANSAKSI</th>
                <th style="width: 11.5%;" class="text-right">DEBIT (MASUK)</th>
                <th style="width: 11.5%;" class="text-right">KREDIT (KELUAR)</th>
                <th style="width: 12.5%;" class="text-right">SALDO KAS(RP)</th>
            </tr>
        </thead>
        <tbody>
            @php $currentSaldo = (float) $saldoAwal; @endphp

            @if($dari && $saldoAwal != 0)
            <tr class="saldo-awal-row">
                <td class="text-center">-</td>
                <td class="text-center nowrap">{{ date('d/m/Y', strtotime($dari)) }}</td>
                <td class="text-center nowrap font-mono">-</td>
                <td colspan="2"><em>SALDO AWAL KAS SEBELUM PERIODE</em></td>
                <td class="text-right text-debit">-</td>
                <td class="text-right text-kredit">-</td>
                <td class="text-right text-saldo nowrap">Rp {{ number_format($currentSaldo, 0, ',', '.') }}</td>
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
                <tr class="{{ $idx % 2 === 1 ? 'bg-strip' : '' }}">
                    <td class="text-center">{{ $idx + 1 }}</td>
                    <td class="text-center nowrap">{{ $tglDisplay }}</td>
                    <td class="text-center nowrap" style="font-family: monospace; font-weight: bold; color: #0f172a;">
                        {{ $t->kode_transaksi }}
                    </td>
                    <td style="font-weight: 600;">{{ $t->kategori }}</td>
                    <td>
                        <div>{{ $t->keterangan ?: '-' }}</div>
                    </td>
                    <td class="text-right text-debit nowrap">
                        {{ $t->jenis === 'debit' ? 'Rp ' . number_format((float) $t->nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right text-kredit nowrap">
                        {{ $t->jenis === 'kredit' ? 'Rp ' . number_format((float) $t->nominal, 0, ',', '.') : '-' }}
                    </td>
                    <td class="text-right text-saldo nowrap">
                        Rp {{ number_format((float) $currentSaldo, 0, ',', '.') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 16px; color: #64748b;">
                        Tidak ada transaksi kas yang tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse

            <!-- REKAPITULASI TOTAL MUTASI & SALDO AKHIR -->
            <tr class="total-row">
                <td colspan="5" class="text-center" style="letter-spacing: 0.5px;">TOTAL MUTASI &amp; SALDO AKHIR KAS</td>
                <td class="text-right text-debit nowrap">Rp {{ number_format((float) $totalDebit, 0, ',', '.') }}</td>
                <td class="text-right text-kredit nowrap">Rp {{ number_format((float) $totalKredit, 0, ',', '.') }}</td>
                <td class="text-right text-saldo nowrap" style="font-size: 9.5pt;">Rp {{ number_format((float) $currentSaldo, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN 3 TANDA TANGAN (2 DI ATAS, 1 DI BAWAH TENGAH) -->
    <div class="pengesahan-container">
        <!-- BARIS 1: 2 TTD DI ATAS (KETUA UMUM AFPA & BENDAHARA AFPA) -->
        <table class="tabel-ttd">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="margin: 0; font-size: 9pt;">Mengetahui,</p>
                    <p style="margin: 2px 0 0; font-size: 9pt; font-weight: bold;">Ketua Umum AFPA</p>
                    <div class="ttd-space"></div>
                    <p style="margin: 0; font-size: 9pt; font-weight: bold; text-decoration: underline;">Danil Ali Sabila</p>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="margin: 0; font-size: 9pt;">Putak, {{ $tanggalAkhirRiil ?? date('d F Y') }}</p>
                    <p style="margin: 2px 0 0; font-size: 9pt; font-weight: bold;">Bendahara AFPA</p>
                    <div class="ttd-space"></div>
                    <p style="margin: 0; font-size: 9pt; font-weight: bold; text-decoration: underline;">Azwil Yoga Pratama</p>
                </td>
            </tr>
        </table>

        <!-- BARIS 2: 1 TTD DI BAWAH TENGAH (BENDAHARA PUSAT PPAF) -->
        <table class="tabel-ttd" style="margin-top: 14px;">
            <tr>
                <td style="width: 25%;"></td>
                <td style="width: 50%; text-align: center; vertical-align: top;">
                    <p style="margin: 0; font-size: 9pt;">Mengetahui,</p>
                    <p style="margin: 2px 0 0; font-size: 9pt; font-weight: bold;">Bendahara Pusat PPAF</p>
                    <div class="ttd-space"></div>
                    <p style="margin: 0; font-size: 9pt; font-weight: bold; text-decoration: underline;">Ust Imam Qusyairi</p>
                </td>
                <td style="width: 25%;"></td>
            </tr>
        </table>
    </div>

</body>
</html>
