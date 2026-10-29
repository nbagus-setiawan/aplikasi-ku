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
        @page Section1 {
            size: 841.9pt 595.3pt; /* A4 Landscape: 297mm x 210mm */
            mso-page-orientation: landscape;
            margin: 0.7in 0.8in 0.7in 0.8in;
            mso-header-margin: 36.0pt;
            mso-footer-margin: 36.0pt;
            mso-paper-source: 0;
        }
        div.Section1 {
            page: Section1;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10pt;
            color: #0f172a;
            line-height: 1.35;
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
            font-size: 9.5pt;
            color: #475569;
        }
        table.tabel-kas {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 9.5pt;
        }
        table.tabel-kas th, table.tabel-kas td {
            border: 1px solid #64748b;
            padding: 6px 8px;
            text-align: left;
            vertical-align: middle;
        }
        table.tabel-kas th {
            background-color: #f1f5f9;
            color: #0f172a;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9pt;
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
            font-size: 10pt;
        }
        table.tabel-ttd {
            width: 100%;
            margin-top: 25px;
            border-collapse: collapse;
            border: none;
        }
        table.tabel-ttd td {
            border: none;
            padding: 0;
            vertical-align: top;
            font-size: 10pt;
            line-height: 1.4;
        }
        .ttd-space {
            height: 60px;
        }
    </style>
</head>
<body>
<div class="Section1">

    <!-- KOP RESMI LEMBAGA (GAMBAR KOP PNG RESMI) -->
    <div class="header-kop">
        @if (!empty($kopGambar))
            <img src="{{ $kopGambar }}" style="width: 100%; max-height: 125px; height: auto; display: block; margin: 0 auto;" alt="Kop Surat">
        @else
            <!-- Fallback Teks Resmi jika gambar tidak ditemukan -->
            <table style="width: 100%; border-collapse: collapse; border-bottom: 3px double #0f172a; padding-bottom: 8px; margin: 0 0 12px 0;">
                <tr>
                    <td style="text-align: center; border: none; padding: 0;">
                        <div style="font-size: 13pt; font-weight: bold; text-transform: uppercase; color: #0f172a;">
                            {{ $pengaturans['kop_yayasan'] ?? 'YAYASAN PONDOK PESANTREN AL-FALAH PUTAK' }}
                        </div>
                        <div style="font-size: 16pt; font-weight: bold; text-transform: uppercase; color: #064e3b; margin: 2px 0;">
                            {{ $pengaturans['kop_nama'] ?? 'PONDOK PESANTREN AL-FALAH PUTRA' }}
                        </div>
                        <div style="font-size: 9pt; color: #334155;">
                            {{ $pengaturans['kop_alamat'] ?? 'Desa Putak, Kecamatan Gelumbang, Kabupaten Muara Enim, Sumatera Selatan 31171' }}
                        </div>
                        <div style="font-size: 9pt; color: #475569;">
                            {{ $pengaturans['kop_kontak'] ?? 'Telp/WA: 0812-7890-3117 | Email: sekretariat@alfalahputra.sch.id' }}
                        </div>
                    </td>
                </tr>
            </table>
        @endif
    </div>

    <!-- JUDUL LAPORAN BUKU KAS -->
    <div class="judul-laporan">
        <h2>BUKU KAS UMUM (MUTASI DEBIT & KREDIT)</h2>
        <p>
            Periode: <strong>
            @if($dari && $sampai)
                {{ date('d/m/Y', strtotime($dari)) }} s.d. {{ date('d/m/Y', strtotime($sampai)) }}
            @elseif($dari)
                Mulai {{ date('d/m/Y', strtotime($dari)) }}
            @elseif($sampai)
                Sampai {{ date('d/m/Y', strtotime($sampai)) }}
            @else
                Seluruh Transaksi Pembukuan
            @endif
            </strong>
            @if(!empty($kategori)) &bull; Pos Akun: <strong>{{ $kategori }}</strong> @endif
            @if(!empty($jenis)) &bull; Jenis: <strong>{{ strtoupper($jenis) }}</strong> @endif
        </p>
    </div>

    <!-- TABEL BUKU KAS UMUM -->
    <table class="tabel-kas">
        <thead>
            <tr>
                <th style="width: 4%;">NO</th>
                <th style="width: 10%;">TANGGAL</th>
                <th style="width: 14%;">NO. BUKTI</th>
                <th style="width: 15%;">POS AKUN / KATEGORI</th>
                <th style="width: 27%;">URAIAN TRANSAKSI & PIHAK TERKAIT</th>
                <th style="width: 10%;" class="text-right">DEBIT (MASUK)</th>
                <th style="width: 10%;" class="text-right">KREDIT (KELUAR)</th>
                <th style="width: 10%;" class="text-right">SALDO KAS (RP)</th>
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
                        @if($t->nama_pihak)
                            <div style="font-size: 8pt; color: #64748b; margin-top: 2px;">
                                <span style="color: #94a3b8;">{{ $t->jenis === 'debit' ? 'Dari:' : 'Kepada:' }}</span>
                                <strong style="color: #334155;">{{ $t->nama_pihak }}</strong>
                            </div>
                        @endif
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
                    <td colspan="8" class="text-center" style="padding: 18px; color: #64748b;">
                        Tidak ada transaksi kas yang tercatat pada periode ini.
                    </td>
                </tr>
            @endforelse

            <!-- REKAPITULASI TOTAL MUTASI & SALDO AKHIR -->
            <tr class="total-row">
                <td colspan="5" class="text-center" style="letter-spacing: 0.5px;">TOTAL MUTASI & SALDO AKHIR KAS</td>
                <td class="text-right text-debit nowrap">Rp {{ number_format((float) $totalDebit, 0, ',', '.') }}</td>
                <td class="text-right text-kredit nowrap">Rp {{ number_format((float) $totalKredit, 0, ',', '.') }}</td>
                <td class="text-right text-saldo nowrap" style="font-size: 10pt;">Rp {{ number_format((float) $currentSaldo, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>

    <!-- LEMBAR PENGESAHAN DUA TANDA TANGAN SEJAJAR -->
    <table class="tabel-ttd">
        <tr>
            <td style="width: 50%; text-align: center;">
                <p style="margin: 0;">Mengetahui,</p>
                <p style="margin: 2px 0 0; font-weight: bold;">Pengasuh Pondok Pesantren</p>
                <div class="ttd-space"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">K.H. Mursyidi</p>
                <p style="margin: 2px 0 0; font-size: 8.5pt; color: #64748b;">Pengasuh PP Al-Falah Putak</p>
            </td>
            <td style="width: 50%; text-align: center;">
                @php
                    $namaBulanIndo = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    $tglCetak = date('j') . ' ' . ($namaBulanIndo[(int) date('n')] ?? date('F')) . ' ' . date('Y');
                @endphp
                <p style="margin: 0;">Desa Putak, {{ $tglCetak }}</p>
                <p style="margin: 2px 0 0; font-weight: bold;">Bendahara Pesantren</p>
                <div class="ttd-space"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">( __________________________ )</p>
                <p style="margin: 2px 0 0; font-size: 8.5pt; color: #64748b;">Bagian Kebendaharaan</p>
            </td>
        </tr>
    </table>

</div>
</body>
</html>
