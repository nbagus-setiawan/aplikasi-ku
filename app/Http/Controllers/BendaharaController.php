<?php

namespace App\Http\Controllers;

use App\Models\KasTransaksi;
use App\Models\Pengaturan;
use App\Services\KasDocxService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BendaharaController extends Controller
{
    /**
     * Kategori Rekomendasi Akun Kas
     */
    protected array $kategoriDebit = [
        'Lain-lain (Penerimaan)',
    ];

    protected array $kategoriKredit = [
        'Lain-lain (Pengeluaran)',
    ];

    /**
     * Dapatkan daftar bulan yang memiliki riwayat transaksi di database
     */
    protected function getBulanTersedia()
    {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $ymExpr = $driver === 'sqlite' ? "strftime('%Y-%m', tanggal)" : "DATE_FORMAT(tanggal, '%Y-%m')";

        return KasTransaksi::selectRaw("{$ymExpr} as ym, MIN(tanggal) as min_tgl, MAX(tanggal) as max_tgl, COUNT(*) as total")
            ->groupBy('ym')
            ->orderByDesc('ym')
            ->get()
            ->map(function ($row) {
                $cMin = Carbon::parse($row->min_tgl)->locale('id');
                $cMax = Carbon::parse($row->max_tgl)->locale('id');
                $namaBulan = Carbon::createFromFormat('Y-m', $row->ym)->locale('id')->translatedFormat('F Y');

                if ($cMin->toDateString() === $cMax->toDateString()) {
                    $rentang = $cMin->translatedFormat('d F Y');
                } elseif ($cMin->month === $cMax->month && $cMin->year === $cMax->year) {
                    $rentang = $cMin->translatedFormat('d F') . ' - ' . $cMax->translatedFormat('d F Y');
                } else {
                    $rentang = $cMin->translatedFormat('d F Y') . ' - ' . $cMax->translatedFormat('d F Y');
                }

                return [
                    'ym' => $row->ym,
                    'label' => $namaBulan . ' (' . $rentang . ')',
                    'nama_bulan' => $namaBulan,
                    'rentang' => $rentang,
                    'min_tgl' => $row->min_tgl,
                    'max_tgl' => $row->max_tgl,
                    'total' => $row->total,
                ];
            });
    }

    /**
     * Dapatkan daftar tahun yang memiliki riwayat transaksi di database
     */
    protected function getTahunTersedia()
    {
        $driver = \Illuminate\Support\Facades\DB::connection()->getDriverName();
        $yExpr = $driver === 'sqlite' ? "strftime('%Y', tanggal)" : "YEAR(tanggal)";

        return KasTransaksi::selectRaw("{$yExpr} as y, MIN(tanggal) as min_tgl, MAX(tanggal) as max_tgl, COUNT(*) as total")
            ->groupBy('y')
            ->orderByDesc('y')
            ->get()
            ->map(function ($row) {
                $cMin = Carbon::parse($row->min_tgl)->locale('id');
                $cMax = Carbon::parse($row->max_tgl)->locale('id');

                if ($cMin->toDateString() === $cMax->toDateString()) {
                    $rentang = $cMin->translatedFormat('d F Y');
                } elseif ($cMin->year === $cMax->year) {
                    $rentang = $cMin->translatedFormat('d F') . ' - ' . $cMax->translatedFormat('d F Y');
                } else {
                    $rentang = $cMin->translatedFormat('d F Y') . ' - ' . $cMax->translatedFormat('d F Y');
                }

                return [
                    'tahun' => (string) $row->y,
                    'label' => 'Tahun ' . $row->y . ' (' . $rentang . ')',
                    'rentang' => $rentang,
                    'min_tgl' => $row->min_tgl,
                    'max_tgl' => $row->max_tgl,
                    'total' => $row->total,
                ];
            });
    }

    /**
     * Halaman Buku Kas Utama (1 Halaman Terpadu)
     */
    public function index(Request $request)
    {
        $bulanTersedia = $this->getBulanTersedia();
        $tahunTersedia = $this->getTahunTersedia();

        // 3 Jenis Filter Utama: 'semua', 'bulan', 'tahun'
        $filterTipe = $request->input('filter_tipe');
        $filterBulan = $request->input('filter_bulan');
        $filterTahun = $request->input('filter_tahun');

        // Kompatibilitas dengan parameter sebelumnya
        $legacyPeriode = $request->input('periode');
        $dari = $request->input('dari_tanggal');
        $sampai = $request->input('sampai_tanggal');
        $jenis = $request->input('jenis'); // 'debit', 'kredit', atau null
        $kategori = $request->input('kategori');
        $q = $request->input('q');

        // Tentukan mode filter aktif
        if (! $filterTipe) {
            if ($legacyPeriode === 'bulan-ini') {
                $filterTipe = 'bulan';
                $filterBulan = Carbon::now()->format('Y-m');
            } elseif ($legacyPeriode === 'tahun-ini') {
                $filterTipe = 'tahun';
                $filterTahun = Carbon::now()->format('Y');
            } elseif ($dari || $sampai) {
                $filterTipe = 'kustom';
            } else {
                $filterTipe = 'semua';
            }
        }

        // Terapkan rentang tanggal filter berdasarkan 3 tipe
        $isFiltered = false;

        if ($filterTipe === 'bulan') {
            if (! $filterBulan) {
                $filterBulan = $bulanTersedia->first()['ym'] ?? Carbon::now()->format('Y-m');
            }
            $dari = Carbon::createFromFormat('Y-m', $filterBulan)->startOfMonth()->toDateString();
            $sampai = Carbon::createFromFormat('Y-m', $filterBulan)->endOfMonth()->toDateString();
            $isFiltered = true;
        } elseif ($filterTipe === 'tahun') {
            if (! $filterTahun) {
                $filterTahun = $tahunTersedia->first()['tahun'] ?? Carbon::now()->format('Y');
            }
            $dari = Carbon::createFromFormat('Y', $filterTahun)->startOfYear()->toDateString();
            $sampai = Carbon::createFromFormat('Y', $filterTahun)->endOfYear()->toDateString();
            $isFiltered = true;
        } elseif ($filterTipe === 'kustom' && ($dari || $sampai)) {
            $isFiltered = true;
        } else {
            // Mode 'semua': Menampilkan seluruh riwayat transaksi
            $filterTipe = 'semua';
            $dari = null;
            $sampai = null;
        }

        // Query Utama untuk Tabel
        $query = KasTransaksi::with('user');

        // PENGURUTAN SESUAI PERMINTAAN USER:
        // Jika TIDAK difilter (semua): Urut dari tanggal terbaru (31, 30 ... 01)
        // Jika DI-FILTER: Urut sesuai urutan kronologis buku kas (01, 02 ... 31)
        if (! $isFiltered && empty($jenis) && empty($kategori) && empty($q)) {
            $query->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
        } else {
            $query->orderBy('tanggal', 'asc')->orderBy('id', 'asc');
        }

        if ($dari && $sampai) {
            $query->whereBetween('tanggal', [$dari, $sampai]);
        } elseif ($dari) {
            $query->whereDate('tanggal', '>=', $dari);
        } elseif ($sampai) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        if ($jenis && in_array($jenis, ['debit', 'kredit'])) {
            $query->where('jenis', $jenis);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('keterangan', 'like', "%{$q}%")
                    ->orWhere('kode_transaksi', 'like', "%{$q}%")
                    ->orWhere('nama_pihak', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        $transaksis = $query->paginate(25)->withQueryString();

        // Saldo Kas Riil Keseluruhan (Lifetime Balance)
        $totalDebitKeseluruhan = (float) KasTransaksi::debit()->sum('nominal');
        $totalKreditKeseluruhan = (float) KasTransaksi::kredit()->sum('nominal');
        $saldoKasKeseluruhan = $totalDebitKeseluruhan - $totalKreditKeseluruhan;

        // Total Transaksi Sesuai Filter Saat Ini
        $filterSumQuery = KasTransaksi::query();
        if ($dari && $sampai) {
            $filterSumQuery->whereBetween('tanggal', [$dari, $sampai]);
        } elseif ($dari) {
            $filterSumQuery->whereDate('tanggal', '>=', $dari);
        } elseif ($sampai) {
            $filterSumQuery->whereDate('tanggal', '<=', $sampai);
        }
        if ($kategori) {
            $filterSumQuery->where('kategori', $kategori);
        }
        if ($jenis && in_array($jenis, ['debit', 'kredit'])) {
            $filterSumQuery->where('jenis', $jenis);
        }
        if ($q) {
            $filterSumQuery->where(function ($w) use ($q) {
                $w->where('keterangan', 'like', "%{$q}%")
                    ->orWhere('kode_transaksi', 'like', "%{$q}%")
                    ->orWhere('nama_pihak', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        $totalDebitFiltered = (float) (clone $filterSumQuery)->debit()->sum('nominal');
        $totalKreditFiltered = (float) (clone $filterSumQuery)->kredit()->sum('nominal');
        $surplusDefisitFiltered = $totalDebitFiltered - $totalKreditFiltered;
        $totalCountFiltered = (clone $filterSumQuery)->count();

        // Tentukan Label Periode Transaksi Riil
        $transaksisList = collect($transaksis->items());
        if ($transaksisList->isNotEmpty()) {
            $minDate = $transaksisList->min('tanggal');
            $maxDate = $transaksisList->max('tanggal');
            $cMin = Carbon::parse($minDate)->locale('id');
            $cMax = Carbon::parse($maxDate)->locale('id');

            if ($cMin->toDateString() === $cMax->toDateString()) {
                $labelPeriode = $cMin->translatedFormat('d F Y');
            } elseif ($cMin->year === $cMax->year) {
                $labelPeriode = $cMin->translatedFormat('d F') . ' s.d. ' . $cMax->translatedFormat('d F Y');
            } else {
                $labelPeriode = $cMin->translatedFormat('d F Y') . ' s.d. ' . $cMax->translatedFormat('d F Y');
            }
        } elseif ($filterTipe === 'semua') {
            $labelPeriode = 'Semua Transaksi Pembukuan';
        } else {
            $labelPeriode = 'Tidak Ada Transaksi pada Periode Ini';
        }

        // Kategori terpakai untuk filter dropdown
        $kategoriList = KasTransaksi::distinct()->pluck('kategori')->filter()->values();

        // Respon AJAX (Seamless Tanpa Reload)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html_cards' => [
                    'saldo_kas' => 'Rp ' . number_format($saldoKasKeseluruhan, 0, ',', '.'),
                    'saldo_kas_raw' => $saldoKasKeseluruhan,
                    'total_debit' => 'Rp ' . number_format($totalDebitFiltered, 0, ',', '.'),
                    'total_kredit' => 'Rp ' . number_format($totalKreditFiltered, 0, ',', '.'),
                    'surplus_defisit' => ($surplusDefisitFiltered >= 0 ? '+' : '') . 'Rp ' . number_format($surplusDefisitFiltered, 0, ',', '.'),
                    'is_surplus' => $surplusDefisitFiltered >= 0,
                    'total_count' => $totalCountFiltered,
                    'periode_label' => $labelPeriode,
                ],
                'html_mobile' => view('bendahara.partials.card_feed', ['transaksis' => $transaksis])->render(),
                'html_desktop' => view('bendahara.partials.table_rows', ['transaksis' => $transaksis])->render(),
                'html_pagination' => $transaksis->hasPages() ? $transaksis->links()->toHtml() : '',
                'total_transaksi' => $transaksis->total(),
                'label_periode' => $labelPeriode,
                'filter_tipe' => $filterTipe,
                'filter_bulan' => $filterBulan,
                'filter_tahun' => $filterTahun,
                'url_word' => route('admin.bendahara.word', $request->query()),
                'url_cetak' => route('admin.bendahara.cetak', $request->query()),
            ]);
        }

        return view('bendahara.index', [
            'transaksis' => $transaksis,
            'saldoKasKeseluruhan' => $saldoKasKeseluruhan,
            'totalDebitKeseluruhan' => $totalDebitKeseluruhan,
            'totalKreditKeseluruhan' => $totalKreditKeseluruhan,
            'totalDebitFiltered' => $totalDebitFiltered,
            'totalKreditFiltered' => $totalKreditFiltered,
            'surplusDefisitFiltered' => $surplusDefisitFiltered,
            'totalCountFiltered' => $totalCountFiltered,
            'filterTipe' => $filterTipe,
            'filterBulan' => $filterBulan,
            'filterTahun' => $filterTahun,
            'bulanTersedia' => $bulanTersedia,
            'tahunTersedia' => $tahunTersedia,
            'labelPeriode' => $labelPeriode,
            'periode' => $legacyPeriode ?: ($filterTipe === 'semua' ? 'semua' : 'custom'),
            'dari' => $dari,
            'sampai' => $sampai,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'q' => $q,
            'kategoriDebit' => $this->kategoriDebit,
            'kategoriKredit' => $this->kategoriKredit,
            'kategoriList' => $kategoriList,
        ]);
    }

    /**
     * Catat Transaksi Kas Baru (Debit / Kredit)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:debit,kredit'],
            'kategori' => ['required', 'string', 'max:100'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'nama_pihak' => ['nullable', 'string', 'max:150'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'jenis.required' => 'Jenis transaksi (Debit/Kredit) wajib dipilih.',
            'kategori.required' => 'Pos Akun / Kategori wajib diisi.',
            'nominal.required' => 'Nominal transaksi wajib diisi.',
            'nominal.min' => 'Nominal minimal Rp 1.',
            'lampiran.max' => 'Ukuran file bukti transaksi maksimal 5 MB.',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('kas-bukti', 'public');
        }

        $kodeTransaksi = KasTransaksi::generateKode($validated['jenis'], $validated['tanggal']);

        $transaksi = KasTransaksi::create([
            'kode_transaksi' => $kodeTransaksi,
            'tanggal' => $validated['tanggal'],
            'jenis' => $validated['jenis'],
            'kategori' => $validated['kategori'],
            'nominal' => $validated['nominal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'nama_pihak' => $validated['nama_pihak'] ?? null,
            'lampiran' => $lampiranPath,
            'user_id' => Auth::id(),
        ]);

        $jenisLabel = $validated['jenis'] === 'debit' ? 'Debit (Pemasukan)' : 'Kredit (Pengeluaran)';
        $nominalFormatted = 'Rp ' . number_format($validated['nominal'], 0, ',', '.');
        $successMessage = "Transaksi {$jenisLabel} sebesar {$nominalFormatted} berhasil dibukukan dengan nomor bukti {$kodeTransaksi}.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'transaksi_id' => $transaksi->id,
            ]);
        }

        return redirect()->route('admin.bendahara.index')
            ->with('success', $successMessage);
    }

    /**
     * Perbarui Transaksi Kas
     */
    public function update(Request $request, KasTransaksi $bendahara)
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'jenis' => ['required', 'in:debit,kredit'],
            'kategori' => ['required', 'string', 'max:100'],
            'nominal' => ['required', 'numeric', 'min:1'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'nama_pihak' => ['nullable', 'string', 'max:150'],
            'lampiran' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'tanggal.required' => 'Tanggal transaksi wajib diisi.',
            'jenis.required' => 'Jenis transaksi wajib dipilih.',
            'kategori.required' => 'Pos Akun / Kategori wajib diisi.',
            'nominal.required' => 'Nominal transaksi wajib diisi.',
            'lampiran.max' => 'Ukuran file bukti transaksi maksimal 5 MB.',
        ]);

        $lampiranPath = $bendahara->lampiran;
        if ($request->hasFile('lampiran')) {
            if ($lampiranPath && Storage::disk('public')->exists($lampiranPath)) {
                Storage::disk('public')->delete($lampiranPath);
            }
            $lampiranPath = $request->file('lampiran')->store('kas-bukti', 'public');
        }

        $bendahara->update([
            'tanggal' => $validated['tanggal'],
            'jenis' => $validated['jenis'],
            'kategori' => $validated['kategori'],
            'nominal' => $validated['nominal'],
            'keterangan' => $validated['keterangan'] ?? null,
            'nama_pihak' => $validated['nama_pihak'] ?? null,
            'lampiran' => $lampiranPath,
        ]);

        $successMessage = "Data transaksi {$bendahara->kode_transaksi} berhasil diperbarui.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'transaksi_id' => $bendahara->id,
            ]);
        }

        return redirect()->route('admin.bendahara.index')
            ->with('success', $successMessage);
    }

    /**
     * Hapus Transaksi Kas (Hard Delete / Permanen)
     */
    public function destroy(Request $request, KasTransaksi $bendahara)
    {
        $kode = $bendahara->kode_transaksi;

        if ($bendahara->lampiran && Storage::disk('public')->exists($bendahara->lampiran)) {
            Storage::disk('public')->delete($bendahara->lampiran);
        }

        $bendahara->forceDelete();

        $successMessage = "Transaksi kas {$kode} berhasil dihapus dari pembukuan.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
            ]);
        }

        return redirect()->route('admin.bendahara.index')
            ->with('success', $successMessage);
    }

    /**
     * Siapkan Data Laporan Buku Kas (Dipakai Bersama oleh Cetak PDF & Ekspor Word)
     */
    protected function prepareReportData(Request $request): array
    {
        // 1. Tangani parameter dari pop-up modal unduh laporan
        $downloadMode = $request->input('mode', $request->input('filter_tipe', 'semua'));
        $bulanDari = $request->input('bulan_dari');
        $bulanSampai = $request->input('bulan_sampai');
        $bulanTunggal = $request->input('bulan', $request->input('filter_bulan'));
        $tahunPilihan = $request->input('tahun', $request->input('filter_tahun'));

        $dari = $request->input('dari_tanggal');
        $sampai = $request->input('sampai_tanggal');
        $jenis = $request->input('jenis');
        $kategori = $request->input('kategori');
        $q = $request->input('q');

        // Tentukan rentang $dari dan $sampai
        if ($downloadMode === 'rentang_bulan' && $bulanDari && $bulanSampai) {
            $dari = Carbon::createFromFormat('Y-m', min($bulanDari, $bulanSampai))->startOfMonth()->toDateString();
            $sampai = Carbon::createFromFormat('Y-m', max($bulanDari, $bulanSampai))->endOfMonth()->toDateString();
        } elseif (($downloadMode === 'bulan' || $request->has('filter_bulan')) && $bulanTunggal) {
            $dari = Carbon::createFromFormat('Y-m', $bulanTunggal)->startOfMonth()->toDateString();
            $sampai = Carbon::createFromFormat('Y-m', $bulanTunggal)->endOfMonth()->toDateString();
        } elseif (($downloadMode === 'tahun' || $request->has('filter_tahun')) && $tahunPilihan) {
            $dari = Carbon::createFromFormat('Y', $tahunPilihan)->startOfYear()->toDateString();
            $sampai = Carbon::createFromFormat('Y', $tahunPilihan)->endOfYear()->toDateString();
        } elseif ($request->input('periode') === 'bulan-ini') {
            $dari = Carbon::now()->startOfMonth()->toDateString();
            $sampai = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($request->input('periode') === 'tahun-ini') {
            $dari = Carbon::now()->startOfYear()->toDateString();
            $sampai = Carbon::now()->endOfYear()->toDateString();
        }

        $query = KasTransaksi::with('user')->orderBy('tanggal', 'asc')->orderBy('id', 'asc');

        if ($dari && $sampai) {
            $query->whereBetween('tanggal', [$dari, $sampai]);
        } elseif ($dari) {
            $query->whereDate('tanggal', '>=', $dari);
        } elseif ($sampai) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        if ($jenis && in_array($jenis, ['debit', 'kredit'])) {
            $query->where('jenis', $jenis);
        }

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($q) {
            $query->where(function ($w) use ($q) {
                $w->where('keterangan', 'like', "%{$q}%")
                    ->orWhere('kode_transaksi', 'like', "%{$q}%")
                    ->orWhere('nama_pihak', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%");
            });
        }

        $transaksis = $query->get();

        // Hitung Saldo Awal sebelum tanggal $dari (jika ada batasan tanggal awal)
        $saldoAwal = 0.0;
        if ($dari) {
            $debitSebelumnya = (float) KasTransaksi::debit()->whereDate('tanggal', '<', $dari)->sum('nominal');
            $kreditSebelumnya = (float) KasTransaksi::kredit()->whereDate('tanggal', '<', $dari)->sum('nominal');
            $saldoAwal = $debitSebelumnya - $kreditSebelumnya;
        }

        $totalDebit = (float) $transaksis->where('jenis', 'debit')->sum('nominal');
        $totalKredit = (float) $transaksis->where('jenis', 'kredit')->sum('nominal');
        $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;

        // FORMAT PERIODE DINAMIS BERDASARKAN TRANSAKSI RIIL (TIDAK BOLEH MUNCUL AWAL/SEKARANG)
        if ($transaksis->isNotEmpty()) {
            $minDate = $transaksis->min('tanggal');
            $maxDate = $transaksis->max('tanggal');
            $cMin = Carbon::parse($minDate)->locale('id');
            $cMax = Carbon::parse($maxDate)->locale('id');

            if ($cMin->toDateString() === $cMax->toDateString()) {
                $tglAwalTeks = $cMin->translatedFormat('d F Y');
                $tglAkhirTeks = $cMax->translatedFormat('d F Y');
                $labelPeriode = $cMin->translatedFormat('d F Y');
            } elseif ($cMin->year === $cMax->year) {
                $tglAwalTeks = $cMin->translatedFormat('d F');
                $tglAkhirTeks = $cMax->translatedFormat('d F Y');
                $labelPeriode = $cMin->translatedFormat('d F') . ' s.d ' . $cMax->translatedFormat('d F Y');
            } else {
                $tglAwalTeks = $cMin->translatedFormat('d F Y');
                $tglAkhirTeks = $cMax->translatedFormat('d F Y');
                $labelPeriode = $cMin->translatedFormat('d F Y') . ' s.d ' . $cMax->translatedFormat('d F Y');
            }
        } elseif ($dari && $sampai) {
            $cMin = Carbon::parse($dari)->locale('id');
            $cMax = Carbon::parse($sampai)->locale('id');
            $tglAwalTeks = $cMin->translatedFormat('d F');
            $tglAkhirTeks = $cMax->translatedFormat('d F Y');
            $labelPeriode = $cMin->translatedFormat('d F') . ' s.d ' . $cMax->translatedFormat('d F Y');
        } else {
            $cNow = Carbon::now()->locale('id');
            $tglAwalTeks = $cNow->translatedFormat('d F');
            $tglAkhirTeks = $cNow->translatedFormat('d F Y');
            $labelPeriode = 'Semua Transaksi Pembukuan';
        }

        $pengaturans = Pengaturan::getAllKeyValues();

        // Logo Satuan
        $logo = null;
        if (! empty($pengaturans['logo']) && file_exists(storage_path('app/public/' . $pengaturans['logo']))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['logo'])));
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
        }

        // Banner Kop Surat Resmi Khusus Laporan Buku Kas (Sesuai Template Word Resmi)
        $kopGambar = null;
        if (file_exists(public_path('images/kop-kas.png'))) {
            $kopGambar = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/kop-kas.png')));
        } elseif (! empty($pengaturans['kop_gambar']) && file_exists(storage_path('app/public/' . $pengaturans['kop_gambar']))) {
            $ext = pathinfo($pengaturans['kop_gambar'], PATHINFO_EXTENSION);
            $mime = strtolower($ext) === 'png' ? 'image/png' : 'image/jpeg';
            $kopGambar = "data:{$mime};base64," . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['kop_gambar'])));
        } elseif (file_exists(public_path('images/kop.png'))) {
            $kopGambar = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/kop.png')));
        } elseif (file_exists(public_path('images/kop.jpeg'))) {
            $kopGambar = 'data:image/jpeg;base64,' . base64_encode(file_get_contents(public_path('images/kop.jpeg')));
        }

        return [
            'transaksis' => $transaksis,
            'saldoAwal' => $saldoAwal,
            'totalDebit' => $totalDebit,
            'totalKredit' => $totalKredit,
            'saldoAkhir' => $saldoAkhir,
            'dari' => $dari,
            'sampai' => $sampai,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'periode' => $downloadMode,
            'q' => $q,
            'labelPeriode' => $labelPeriode,
            'tanggal_awal_teks' => $tglAwalTeks,
            'tanggal_akhir_teks' => $tglAkhirTeks,
            'pengaturan' => $pengaturans,
            'pengaturans' => $pengaturans,
            'logo' => $logo,
            'kopGambar' => $kopGambar,
            'isHtmlPrint' => false,
        ];
    }

    /**
     * Cetak Buku Kas Umum / Laporan Mutasi Kas (PDF)
     */
    public function cetak(Request $request)
    {
        try {
            $viewData = $this->prepareReportData($request);

            if (class_exists(\DOMImplementation::class)) {
                $pdf = Pdf::loadView('bendahara.cetak', $viewData)
                    ->setPaper('a4', 'landscape');

                $filename = 'buku-kas-umum-' . date('Ymd-His') . '.pdf';

                return $pdf->stream($filename);
            }

            $viewData['isHtmlPrint'] = true;
            return response()->view('bendahara.cetak', $viewData);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Fallback cetak web karena kendala PDF: ' . $e->getMessage());

            if (isset($viewData)) {
                $viewData['isHtmlPrint'] = true;
                return response()->view('bendahara.cetak', $viewData);
            }

            return redirect()->route('admin.bendahara.index')
                ->with('error', 'Gagal memproses cetak: ' . $e->getMessage());
        }
    }

    /**
     * Unduh Laporan Buku Kas Format Microsoft Word (.docx) Berdasarkan Template Resmi
     */
    public function exportWord(Request $request, KasDocxService $docxService)
    {
        try {
            $viewData = $this->prepareReportData($request);

            $docxBinary = $docxService->generateDocx($viewData);
            if ($docxBinary) {
                $filename = 'laporan-buku-kas-' . date('Ymd-His') . '.docx';

                return response($docxBinary, 200, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                    'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
                    'Pragma' => 'public',
                ]);
            }

            $html = view('bendahara.word', $viewData)->render();
            $filename = 'laporan-buku-kas-' . date('Ymd-His') . '.doc';

            return response($html, 200, [
                'Content-Type' => 'application/vnd.ms-word; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
                'Cache-Control' => 'max-age=0, no-cache, no-store, must-revalidate',
                'Pragma' => 'public',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Gagal ekspor word buku kas: ' . $e->getMessage());

            return redirect()->route('admin.bendahara.index')
                ->with('error', 'Gagal mengunduh dokumen Word: ' . $e->getMessage());
        }
    }
}
