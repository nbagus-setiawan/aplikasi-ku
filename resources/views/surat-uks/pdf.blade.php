@php
    $namaLembaga = $pengaturans['nama_lembaga'] ?? 'Pondok Pesantren Al-Falah Putra';
    $alamatLembaga = $pengaturans['alamat_lembaga'] ?? 'Desa Putak, Kec. Gelumbang, Kab. Muara Enim, Sumatera Selatan 31171';
    $kontakLembaga = $pengaturans['kontak_lembaga'] ?? 'Telp/WA: 0812-3456-7890 | Website: alfalahputra.ponpes.id';

    $logoPath = public_path('images/logo.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Blangko Surat UKS - Pondok Pesantren Al-Falah Putra</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0.8cm 1.2cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            color: #111;
            line-height: 1.25;
            margin: 0;
            padding: 0;
            font-size: 9pt;
        }

        .page-container {
            page-break-after: always;
            box-sizing: border-box;
            height: 100%;
        }
        .page-container:last-child {
            page-break-after: avoid;
        }

        .slip-box {
            box-sizing: border-box;
            padding: 4px 6px;
        }

        .header-kop {
            text-align: center;
            border-bottom: 2.5px double #000;
            padding-bottom: 4px;
            margin-bottom: 6px;
        }
        .header-kop table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-kop td {
            vertical-align: middle;
        }
        .kop-logo {
            width: 48px;
            text-align: left;
        }
        .kop-logo img {
            width: 44px;
            height: auto;
        }
        .kop-text {
            text-align: center;
        }
        .kop-instansi {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #064e3b;
        }
        .kop-unit {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 1px 0 0 0;
            color: #111;
        }
        .kop-alamat {
            font-size: 7pt;
            color: #333;
            margin-top: 2px;
            line-height: 1.2;
        }

        .slip-badge {
            font-size: 7pt;
            font-family: Arial, sans-serif;
            font-weight: bold;
            border: 1px solid #333;
            padding: 2px 4px;
            display: inline-block;
            background: #f8fafc;
        }

        .judul-surat {
            text-align: center;
            margin: 4px 0 6px 0;
            position: relative;
        }
        .judul-surat h2 {
            font-size: 11pt;
            font-weight: bold;
            text-decoration: underline;
            text-transform: uppercase;
            margin: 0;
            letter-spacing: 0.5px;
        }
        .judul-surat p {
            font-size: 8.5pt;
            font-family: 'Times New Roman', Times, serif;
            margin: 1px 0 0 0;
            font-weight: bold;
        }

        .kode-box {
            position: absolute;
            right: 0;
            top: 0;
            text-align: right;
            font-family: Arial, sans-serif;
        }
        .kode-tag {
            display: inline-block;
            border: 1.5px solid #064e3b;
            background: #f0fdf4;
            color: #064e3b;
            padding: 2px 6px;
            font-size: 8.5pt;
            font-weight: bold;
            letter-spacing: 1px;
        }
        .kode-sub {
            font-size: 6.5pt;
            color: #555;
            margin-top: 1px;
        }

        .paragraf {
            text-align: justify;
            margin: 3px 0;
            font-size: 8.5pt;
            line-height: 1.3;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
        }
        .table-data td {
            vertical-align: middle;
            padding: 2.5px 0;
            font-size: 8.5pt;
        }
        .lbl { width: 24%; }
        .sep { width: 2%; text-align: center; }
        .val { width: 74%; font-weight: normal; }

        .titik-titik {
            display: inline-block;
            border-bottom: 1px dotted #333;
            min-height: 14px;
            width: 98%;
        }

        .kotak-catatan {
            border: 1px solid #94a3b8;
            background-color: #f8fafc;
            padding: 3px 6px;
            font-size: 7.5pt;
            margin: 4px 0 6px 0;
            line-height: 1.25;
        }

        .ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
        }
        .ttd-table td {
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
            width: 33.33%;
        }
        .ttd-ruang {
            height: 38px;
        }
        .ttd-nama {
            font-weight: bold;
            text-decoration: underline;
        }

        .separator-slip {
            margin: 8px 0;
            border-top: 1px dashed #64748b;
            position: relative;
            text-align: center;
        }
        .separator-slip span {
            position: absolute;
            top: -7px;
            left: 50%;
            transform: translateX(-50%);
            background: #fff;
            padding: 0 8px;
            font-size: 7pt;
            color: #64748b;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>

@foreach ($suratList as $suratUk)
    <div class="page-container">
        {{-- Setiap lembar A4 dibagi menjadi 2 slip: Lembar 1 (Santri/Satpam) dan Lembar 2 (Arsip Pesantren) --}}
        @for ($slip = 1; $slip <= 2; $slip++)
            <div class="slip-box">
                <!-- Header Kop Surat Al-Falah Putra -->
                <div class="header-kop">
                    <table>
                        <tr>
                            <td class="kop-logo">
                                @if ($logoBase64)
                                    <img src="{{ $logoBase64 }}" alt="Logo">
                                @endif
                            </td>
                            <td class="kop-text">
                                <div class="kop-instansi">{{ $namaLembaga }}</div>
                                <div class="kop-unit">UNIT KESEHATAN SANTRI & KESEKRETARIATAN</div>
                                <div class="kop-alamat">{{ $alamatLembaga }} &bull; {{ $kontakLembaga }}</div>
                            </td>
                            <td style="width: 70px; text-align: right; vertical-align: top;">
                                <span class="slip-badge">
                                    {{ $slip === 1 ? 'LEMBAR SANTRI / SATPAM' : 'LEMBAR ARSIP UKS' }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Judul & Nomor Surat + Kode Verifikasi 4 Digit (Tanpa QR) -->
                <div class="judul-surat">
                    <div class="kode-box">
                        <div class="kode-tag">KODE: {{ $suratUk->kode_verifikasi }}</div>
                        <div class="kode-sub">Verifikasi: alfalahputra.ponpes.id/verifikasi</div>
                    </div>
                    <h2>{{ $suratUk->jenis === 'sakit' ? 'SURAT KETERANGAN IZIN SAKIT' : 'SURAT KETERANGAN IZIN PULANG' }}</h2>
                    <p>Nomor: {{ $suratUk->nomor_surat }}</p>
                </div>

                <!-- Pembuka Surat -->
                <p class="paragraf">
                    Yang bertanda tangan di bawah ini menerangkan bahwa santri tersebut di bawah ini:
                </p>

                <!-- Tabel Data Isian / Dotted Handwriting -->
                <table class="table-data">
                    <tr>
                        <td class="lbl">Nama Santri</td>
                        <td class="sep">:</td>
                        <td class="val">
                            @if (!empty($suratUk->nama_santri))
                                <strong>{{ strtoupper($suratUk->nama_santri) }}</strong>
                            @else
                                <span class="titik-titik"></span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Kelas / Tingkat</td>
                        <td class="sep">:</td>
                        <td class="val">
                            @if (!empty($suratUk->kelas))
                                <strong>{{ $suratUk->kelas }}</strong>
                            @else
                                <span class="titik-titik"></span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Kamar / Asrama</td>
                        <td class="sep">:</td>
                        <td class="val">
                            @if (!empty($suratUk->asrama))
                                <strong>{{ $suratUk->asrama }}</strong>
                            @else
                                <span class="titik-titik"></span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">{{ $suratUk->jenis === 'sakit' ? 'Keluhan / Sakit' : 'Alasan / Keperluan' }}</td>
                        <td class="sep">:</td>
                        <td class="val">
                            <span class="titik-titik"></span>
                        </td>
                    </tr>
                    <tr>
                        <td class="lbl">Masa Izin</td>
                        <td class="sep">:</td>
                        <td class="val">
                            Tgl. ........................................ s/d Tgl. ........................................
                        </td>
                    </tr>
                </table>

                <!-- Catatan Ketentuan -->
                <div class="kotak-catatan">
                    <strong>Ketentuan:</strong>
                    Surat ini wajib diserahkan kepada Pos Keamanan saat keluar pondok dan dibawa kembali saat tiba di pondok. Keterlambatan tanpa keterangan akan dikenakan sanksi disiplin pondok.
                </div>

                <!-- Tanda Tangan Sesuai Ketentuan -->
                <table class="ttd-table">
                    @if ($suratUk->jenis === 'pulang')
                        {{-- Surat Izin Pulang: TTD Pengasuh Pondok KH. MURSYIDI (TANPA NAMA SEKRETARIS) --}}
                        <tr>
                            <td>
                                Mengetahui,<br>Orang Tua / Wali Santri
                                <div class="ttd-ruang"></div>
                                <div class="ttd-nama">( .................................... )</div>
                            </td>
                            <td>
                                Pos Keamanan / Satpam,
                                <div class="ttd-ruang"></div>
                                <div class="ttd-nama">( .................................... )</div>
                            </td>
                            <td>
                                Putak, ....................................<br>
                                Pengasuh Pondok Pesantren,
                                <div class="ttd-ruang"></div>
                                <div class="ttd-nama">KH. MURSYIDI</div>
                            </td>
                        </tr>
                    @else
                        {{-- Surat Izin Sakit: 2 TTD (Sekretaris Pondok Putra & Pengurus Kesehatan Putra) + Satpam --}}
                        <tr>
                            <td>
                                Pengurus Kesehatan Putra,
                                <div class="ttd-ruang"></div>
                                <div class="ttd-nama">( .................................... )</div>
                            </td>
                            <td>
                                Pos Keamanan / Satpam,
                                <div class="ttd-ruang"></div>
                                <div class="ttd-nama">( .................................... )</div>
                            </td>
                            <td>
                                Putak, ....................................<br>
                                Sekretaris Pondok Putra,
                                <div class="ttd-ruang"></div>
                                <div class="ttd-nama">( .................................... )</div>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>

            @if ($slip === 1)
                <div class="separator-slip">
                    <span>✂ Gunting Di Sini (Potongan Slip Rangkap)</span>
                </div>
            @endif
        @endfor
    </div>
@endforeach

</body>
</html>
