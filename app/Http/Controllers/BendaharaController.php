<?php

namespace App\Http\Controllers;

use App\Models\KasTransaksi;
use App\Models\KasTutupBuku;
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
        'Pendapatan SIP',
        'Pendapatan SIS',
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

        // Hitung Saldo Awal sebelum tanggal $dari (untuk filter bulan / rentang tanggal)
        $saldoAwal = 0.0;
        if ($dari) {
            $debitSebelumnya = (float) KasTransaksi::debit()->whereDate('tanggal', '<', $dari)->sum('nominal');
            $kreditSebelumnya = (float) KasTransaksi::kredit()->whereDate('tanggal', '<', $dari)->sum('nominal');
            $saldoAwal = $debitSebelumnya - $kreditSebelumnya;
        }
        $saldoAkhirFiltered = $saldoAwal + $surplusDefisitFiltered;

        // Cek Status Tutup Buku Bulan yang Sedang Dipilih
        $tutupBukuData = null;
        $isTutupBuku = false;
        if ($filterBulan) {
            $tutupBukuData = KasTutupBuku::getBulan($filterBulan);
            $isTutupBuku = ($tutupBukuData && $tutupBukuData->status === 'tutup');
        }
        $tutupBulanList = KasTutupBuku::where('status', 'tutup')->pluck('tahun_bulan')->toArray();

        // Respon AJAX (Seamless Tanpa Reload)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html_cards' => [
                    'saldo_kas' => 'Rp ' . number_format($saldoKasKeseluruhan, 0, ',', '.'),
                    'saldo_kas_raw' => $saldoKasKeseluruhan,
                    'saldo_awal' => 'Rp ' . number_format($saldoAwal, 0, ',', '.'),
                    'saldo_awal_raw' => $saldoAwal,
                    'total_debit' => 'Rp ' . number_format($totalDebitFiltered, 0, ',', '.'),
                    'total_kredit' => 'Rp ' . number_format($totalKreditFiltered, 0, ',', '.'),
                    'saldo_akhir_filtered' => 'Rp ' . number_format($saldoAkhirFiltered, 0, ',', '.'),
                    'saldo_akhir_raw' => $saldoAkhirFiltered,
                    'surplus_defisit' => ($surplusDefisitFiltered >= 0 ? '+' : '') . 'Rp ' . number_format($surplusDefisitFiltered, 0, ',', '.'),
                    'is_surplus' => $surplusDefisitFiltered >= 0,
                    'total_count' => $totalCountFiltered,
                    'periode_label' => $labelPeriode,
                    'is_tutup_buku' => $isTutupBuku,
                    'tutup_buku_data' => $tutupBukuData,
                ],
                'html_mobile' => view('bendahara.partials.card_feed', ['transaksis' => $transaksis, 'tutupBulanList' => $tutupBulanList])->render(),
                'html_desktop' => view('bendahara.partials.table_rows', ['transaksis' => $transaksis, 'tutupBulanList' => $tutupBulanList])->render(),
                'html_pagination' => $transaksis->hasPages() ? $transaksis->links()->toHtml() : '',
                'total_transaksi' => $transaksis->total(),
                'label_periode' => $labelPeriode,
                'filter_tipe' => $filterTipe,
                'filter_bulan' => $filterBulan,
                'filter_tahun' => $filterTahun,
                'is_tutup_buku' => $isTutupBuku,
                'saldo_awal' => $saldoAwal,
                'saldo_akhir' => $saldoAkhirFiltered,
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
            'saldoAwal' => $saldoAwal,
            'saldoAkhirFiltered' => $saldoAkhirFiltered,
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
            'isTutupBuku' => $isTutupBuku,
            'tutupBukuData' => $tutupBukuData,
            'tutupBulanList' => $tutupBulanList,
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

        $tahunBulan = Carbon::parse($validated['tanggal'])->format('Y-m');
        if (KasTutupBuku::isTutup($tahunBulan)) {
            $namaBulan = Carbon::parse($validated['tanggal'])->locale('id')->translatedFormat('F Y');
            $pesan = "Pembukuan kas bulan {$namaBulan} telah DITUTUP BUKU (Terkunci). Transaksi baru tidak dapat ditambahkan kecuali kunci buku dibuka terlebih dahulu.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $pesan,
                    'errors' => ['tanggal' => [$pesan]],
                ], 422);
            }
            return back()->withInput()->with('error', $pesan);
        }

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

        $oldBulan = Carbon::parse($bendahara->tanggal)->format('Y-m');
        $newBulan = Carbon::parse($validated['tanggal'])->format('Y-m');
        if (KasTutupBuku::isTutup($oldBulan) || KasTutupBuku::isTutup($newBulan)) {
            $pesan = "Transaksi kas pada periode bulan yang telah DITUTUP BUKU tidak dapat diedit.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $pesan,
                    'errors' => ['tanggal' => [$pesan]],
                ], 422);
            }
            return back()->withInput()->with('error', $pesan);
        }

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
        $bulan = Carbon::parse($bendahara->tanggal)->format('Y-m');
        if (KasTutupBuku::isTutup($bulan)) {
            $pesan = "Transaksi kas pada periode bulan yang telah DITUTUP BUKU tidak dapat dihapus.";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $pesan], 422);
            }
            return back()->with('error', $pesan);
        }

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
     * Tutup Buku Kas Bulanan (Opsi A - Data Locking & Saldo Awal Resmi)
     */
    public function tutupBuku(Request $request)
    {
        $validated = $request->validate([
            'tahun_bulan' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
            'catatan' => ['nullable', 'string', 'max:500'],
        ]);

        $tahunBulan = $validated['tahun_bulan'];
        $dari = Carbon::createFromFormat('Y-m', $tahunBulan)->startOfMonth()->toDateString();
        $sampai = Carbon::createFromFormat('Y-m', $tahunBulan)->endOfMonth()->toDateString();

        // Hitung Saldo Awal (sebelum tanggal $dari)
        $debitSebelumnya = (float) KasTransaksi::debit()->whereDate('tanggal', '<', $dari)->sum('nominal');
        $kreditSebelumnya = (float) KasTransaksi::kredit()->whereDate('tanggal', '<', $dari)->sum('nominal');
        $saldoAwal = $debitSebelumnya - $kreditSebelumnya;

        // Hitung Total Mutasi Bulan Bersangkutan
        $totalDebit = (float) KasTransaksi::debit()->whereBetween('tanggal', [$dari, $sampai])->sum('nominal');
        $totalKredit = (float) KasTransaksi::kredit()->whereBetween('tanggal', [$dari, $sampai])->sum('nominal');
        $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;

        $tutupBuku = KasTutupBuku::updateOrCreate(
            ['tahun_bulan' => $tahunBulan],
            [
                'tanggal_tutup' => Carbon::now()->toDateString(),
                'saldo_awal' => $saldoAwal,
                'total_debit' => $totalDebit,
                'total_kredit' => $totalKredit,
                'saldo_akhir' => $saldoAkhir,
                'status' => 'tutup',
                'catatan' => $validated['catatan'] ?? null,
                'user_id' => Auth::id(),
            ]
        );

        $namaBulan = Carbon::createFromFormat('Y-m', $tahunBulan)->locale('id')->translatedFormat('F Y');
        $saldoAkhirFmt = 'Rp ' . number_format($saldoAkhir, 0, ',', '.');
        $pesan = "Alhamdulillah, pembukuan kas bulan {$namaBulan} resmi DITUTUP BUKU. Saldo akhir {$saldoAkhirFmt} resmi dibukukan sebagai saldo awal bulan berikutnya dan seluruh transaksi bulan ini telah dikunci.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $pesan,
                'data' => $tutupBuku,
            ]);
        }

        return redirect()->route('admin.bendahara.index', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => $tahunBulan,
        ])->with('success', $pesan);
    }

    /**
     * Buka Kunci Buku Kas Bulanan (Untuk Perbaikan Darurat)
     */
    public function bukaKunci(Request $request)
    {
        $validated = $request->validate([
            'tahun_bulan' => ['required', 'string', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        $tahunBulan = $validated['tahun_bulan'];
        $tutupBuku = KasTutupBuku::where('tahun_bulan', $tahunBulan)->first();

        if ($tutupBuku) {
            $tutupBuku->update(['status' => 'buka']);
        }

        $namaBulan = Carbon::createFromFormat('Y-m', $tahunBulan)->locale('id')->translatedFormat('F Y');
        $pesan = "Kunci pembukuan kas bulan {$namaBulan} berhasil DIBUKA KEMBALI. Transaksi dapat diedit atau diperbaiki jika diperlukan.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $pesan,
            ]);
        }

        return redirect()->route('admin.bendahara.index', [
            'filter_tipe' => 'bulan',
            'filter_bulan' => $tahunBulan,
        ])->with('success', $pesan);
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

        // Logo Satuan (Prioritaskan Logo Resmi Pesantren Hexagon dari Dokumen Asli)
        $logo = null;
        if (file_exists(public_path('images/logo-pesantren.png'))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo-pesantren.png')));
        } elseif (! empty($pengaturans['logo']) && file_exists(storage_path('app/public/' . $pengaturans['logo']))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['logo'])));
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
        }

        // Kaligrafi Arab Resmi
        $kaligrafi = null;
        if (file_exists(public_path('images/kaligrafi-kop.png'))) {
            $kaligrafi = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/kaligrafi-kop.png')));
        }

        // Banner Kop Surat Kustom (Hanya jika pengguna mengunggah gambar kop di halaman Pengaturan)
        $kopGambar = null;
        if (! empty($pengaturans['kop_gambar']) && file_exists(storage_path('app/public/' . $pengaturans['kop_gambar']))) {
            $ext = pathinfo($pengaturans['kop_gambar'], PATHINFO_EXTENSION);
            $mime = strtolower($ext) === 'png' ? 'image/png' : 'image/jpeg';
            $kopGambar = "data:{$mime};base64," . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['kop_gambar'])));
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
            'tanggalAkhirRiil' => $tglAkhirTeks,
            'pengaturan' => $pengaturans,
            'pengaturans' => $pengaturans,
            'logo' => $logo,
            'kaligrafi' => $kaligrafi,
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

    /**
     * Halaman Laporan Laba Rugi (Surplus / Defisit)
     */
    public function labaRugi(Request $request)
    {
        $data = $this->prepareLabaRugiData($request);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'url_cetak' => route('admin.bendahara.laba-rugi.cetak', $request->query()),
            ]);
        }

        return view('bendahara.laba_rugi', $data);
    }

    /**
     * Cetak Laporan Laba Rugi (PDF)
     */
    public function cetakLabaRugi(Request $request)
    {
        try {
            $viewData = $this->prepareLabaRugiData($request);

            if (class_exists(\DOMImplementation::class)) {
                $pdf = Pdf::loadView('bendahara.laba_rugi_pdf', $viewData)
                    ->setPaper('a4', 'portrait');

                $filename = 'laporan-laba-rugi-' . date('Ymd-His') . '.pdf';

                return $pdf->stream($filename);
            }

            $viewData['isHtmlPrint'] = true;
            return response()->view('bendahara.laba_rugi_pdf', $viewData);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Fallback cetak web laba rugi: ' . $e->getMessage());

            if (isset($viewData)) {
                $viewData['isHtmlPrint'] = true;
                return response()->view('bendahara.laba_rugi_pdf', $viewData);
            }

            return redirect()->route('admin.bendahara.laba-rugi')
                ->with('error', 'Gagal memproses cetak laba rugi: ' . $e->getMessage());
        }
    }

    /**
     * Siapkan Data Laporan Laba Rugi (Surplus / Defisit)
     */
    protected function prepareLabaRugiData(Request $request): array
    {
        $bulanTersedia = $this->getBulanTersedia();
        $tahunTersedia = $this->getTahunTersedia();

        $filterTipe = $request->input('filter_tipe', 'bulan');
        $filterBulan = $request->input('filter_bulan');
        $filterTahun = $request->input('filter_tahun');
        $dari = $request->input('dari_tanggal');
        $sampai = $request->input('sampai_tanggal');

        // Tentukan rentang $dari dan $sampai
        $labelPeriode = 'Semua Periode';
        $tglAkhirTeks = Carbon::now()->locale('id')->translatedFormat('d F Y');

        if ($filterTipe === 'bulan') {
            if (!$filterBulan) {
                $filterBulan = $bulanTersedia->first()['ym'] ?? Carbon::now()->format('Y-m');
            }
            try {
                $dtBulan = Carbon::parse($filterBulan . '-01');
            } catch (\Throwable) {
                $dtBulan = Carbon::now()->startOfMonth();
                $filterBulan = $dtBulan->format('Y-m');
            }
            $dari = $dtBulan->copy()->startOfMonth()->toDateString();
            $sampai = $dtBulan->copy()->endOfMonth()->toDateString();
            $labelPeriode = $dtBulan->locale('id')->translatedFormat('F Y');
            $tglAkhirTeks = $dtBulan->copy()->endOfMonth()->locale('id')->translatedFormat('d F Y');
        } elseif ($filterTipe === 'tahun') {
            if (!$filterTahun) {
                $filterTahun = $tahunTersedia->first()['tahun'] ?? Carbon::now()->format('Y');
            }
            try {
                $dtTahun = Carbon::createFromDate((int) $filterTahun, 1, 1);
            } catch (\Throwable) {
                $dtTahun = Carbon::now()->startOfYear();
                $filterTahun = $dtTahun->format('Y');
            }
            $dari = $dtTahun->copy()->startOfYear()->toDateString();
            $sampai = $dtTahun->copy()->endOfYear()->toDateString();
            $labelPeriode = 'Tahun ' . $filterTahun;
            $tglAkhirTeks = $dtTahun->copy()->endOfYear()->locale('id')->translatedFormat('d F Y');
        } elseif ($filterTipe === 'kustom' && ($dari || $sampai)) {
            $tglAwal = $dari ? Carbon::parse($dari)->locale('id')->translatedFormat('d F Y') : 'Awal';
            $tglAkhir = $sampai ? Carbon::parse($sampai)->locale('id')->translatedFormat('d F Y') : 'Sekarang';
            $labelPeriode = "{$tglAwal} s.d. {$tglAkhir}";
            $tglAkhirTeks = $sampai ? Carbon::parse($sampai)->locale('id')->translatedFormat('d F Y') : Carbon::now()->locale('id')->translatedFormat('d F Y');
        } else {
            $filterTipe = 'semua';
            $dari = null;
            $sampai = null;
        }

        // Query Pendapatan (Debit)
        $debitQuery = KasTransaksi::debit();
        $kreditQuery = KasTransaksi::kredit();

        if ($dari && $sampai) {
            $debitQuery->whereBetween('tanggal', [$dari, $sampai]);
            $kreditQuery->whereBetween('tanggal', [$dari, $sampai]);
        } elseif ($dari) {
            $debitQuery->whereDate('tanggal', '>=', $dari);
            $kreditQuery->whereDate('tanggal', '>=', $dari);
        } elseif ($sampai) {
            $debitQuery->whereDate('tanggal', '<=', $sampai);
            $kreditQuery->whereDate('tanggal', '<=', $sampai);
        }

        $totalPendapatanPeriode = (float) $debitQuery->sum('nominal');
        $totalBeban = (float) $kreditQuery->sum('nominal');

        // Hitung Saldo Awal Kas sebelum periode (jika ada batas tanggal awal; jika tidak ada maka 0)
        $saldoAwal = 0.0;
        if ($dari) {
            $debitSebelumnya = (float) KasTransaksi::debit()->whereDate('tanggal', '<', $dari)->sum('nominal');
            $kreditSebelumnya = (float) KasTransaksi::kredit()->whereDate('tanggal', '<', $dari)->sum('nominal');
            $saldoAwal = $debitSebelumnya - $kreditSebelumnya;
        }

        $labelSaldoAwal = match ($filterTipe) {
            'bulan' => 'Saldo Kas Periode Sebelumnya (Bulan Lalu)',
            'tahun' => 'Saldo Kas Periode Sebelumnya (Tahun Lalu)',
            'kustom' => 'Saldo Kas Sebelum Periode',
            default => 'Saldo Awal Kas',
        };

        // Total Pendapatan (A) = Pendapatan Kas Periode Ini + Saldo Periode Sebelumnya
        $totalPendapatan = $totalPendapatanPeriode + $saldoAwal;
        $persentaseSaldoAwal = $totalPendapatan > 0 ? round(($saldoAwal / $totalPendapatan) * 100, 1) : 0;

        // Rincian per Kategori Pendapatan
        $debitGroup = (clone $debitQuery)
            ->selectRaw('kategori, SUM(nominal) as total, COUNT(*) as jumlah_transaksi')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalPendapatan) {
                $item->persentase = $totalPendapatan > 0 ? round(($item->total / $totalPendapatan) * 100, 1) : 0;
                return $item;
            });

        // Rincian per Kategori Beban
        $kreditGroup = (clone $kreditQuery)
            ->selectRaw('kategori, SUM(nominal) as total, COUNT(*) as jumlah_transaksi')
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get()
            ->map(function ($item) use ($totalBeban) {
                $item->persentase = $totalBeban > 0 ? round(($item->total / $totalBeban) * 100, 1) : 0;
                return $item;
            });

        // Surplus / Defisit Kas Operasional = Total Pendapatan (termasuk Saldo Periode Lalu) - Total Beban
        $surplusDefisit = $totalPendapatan - $totalBeban;
        $isSurplus = $surplusDefisit >= 0;
        $marginSurplusDefisit = $totalPendapatan > 0 ? round(($surplusDefisit / $totalPendapatan) * 100, 1) : 0;
        $saldoAkhirKas = $surplusDefisit;

        // Ambil Data Pengaturan untuk Kop
        $pengaturans = Pengaturan::getAllKeyValues();

        // Logo Satuan (Prioritaskan Logo Resmi Hexagon Pesantren)
        $logo = null;
        if (file_exists(public_path('images/logo-pesantren.png'))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo-pesantren.png')));
        } elseif (! empty($pengaturans['logo']) && file_exists(storage_path('app/public/' . $pengaturans['logo']))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['logo'])));
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
        }

        // Kaligrafi Arab Resmi
        $kaligrafi = null;
        if (file_exists(public_path('images/kaligrafi-kop.png'))) {
            $kaligrafi = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/kaligrafi-kop.png')));
        }

        // Banner Kop Surat Kustom (Hanya jika pengguna mengunggah gambar kop di halaman Pengaturan)
        $kopGambar = null;
        if (! empty($pengaturans['kop_gambar']) && file_exists(storage_path('app/public/' . $pengaturans['kop_gambar']))) {
            $ext = pathinfo($pengaturans['kop_gambar'], PATHINFO_EXTENSION);
            $mime = strtolower($ext) === 'png' ? 'image/png' : 'image/jpeg';
            $kopGambar = "data:{$mime};base64," . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['kop_gambar'])));
        }

        return [
            'filterTipe' => $filterTipe,
            'filterBulan' => $filterBulan,
            'filterTahun' => $filterTahun,
            'dari' => $dari,
            'sampai' => $sampai,
            'labelPeriode' => $labelPeriode,
            'tanggalAkhirRiil' => $tglAkhirTeks,
            'bulanTersedia' => $bulanTersedia,
            'tahunTersedia' => $tahunTersedia,
            'pendapatans' => $debitGroup,
            'bebans' => $kreditGroup,
            'totalPendapatanPeriode' => $totalPendapatanPeriode,
            'totalPendapatan' => $totalPendapatan,
            'totalBeban' => $totalBeban,
            'surplusDefisit' => $surplusDefisit,
            'isSurplus' => $isSurplus,
            'marginSurplusDefisit' => $marginSurplusDefisit,
            'saldoAwal' => $saldoAwal,
            'labelSaldoAwal' => $labelSaldoAwal,
            'persentaseSaldoAwal' => $persentaseSaldoAwal,
            'saldoAkhirKas' => $saldoAkhirKas,
            'pengaturan' => $pengaturans,
            'pengaturans' => $pengaturans,
            'logo' => $logo,
            'kaligrafi' => $kaligrafi,
            'kopGambar' => $kopGambar,
            'isHtmlPrint' => false,
        ];
    }

    /**
     * Halaman Laporan Neraca (Posisi Keuangan Kas & Saldo Dana)
     */
    public function neraca(Request $request)
    {
        $data = $this->prepareNeracaData($request);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $data,
                'url_cetak' => route('admin.bendahara.neraca.cetak', $request->query()),
            ]);
        }

        return view('bendahara.neraca', $data);
    }

    /**
     * Cetak Laporan Neraca (PDF)
     */
    public function cetakNeraca(Request $request)
    {
        try {
            $viewData = $this->prepareNeracaData($request);

            if (class_exists(\DOMImplementation::class)) {
                $pdf = Pdf::loadView('bendahara.neraca_pdf', $viewData)
                    ->setPaper('a4', 'portrait');

                $filename = 'laporan-neraca-' . date('Ymd-His') . '.pdf';

                return $pdf->stream($filename);
            }

            $viewData['isHtmlPrint'] = true;
            return response()->view('bendahara.neraca_pdf', $viewData);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Fallback cetak web neraca: ' . $e->getMessage());

            if (isset($viewData)) {
                $viewData['isHtmlPrint'] = true;
                return response()->view('bendahara.neraca_pdf', $viewData);
            }

            return redirect()->route('admin.bendahara.neraca')
                ->with('error', 'Gagal memproses cetak neraca: ' . $e->getMessage());
        }
    }

    /**
     * Siapkan Data Laporan Neraca (Posisi Kas & Saldo Dana - Data Riil Buku Kas)
     */
    protected function prepareNeracaData(Request $request): array
    {
        $bulanTersedia = $this->getBulanTersedia();
        $tahunTersedia = $this->getTahunTersedia();

        $filterTipe = $request->input('filter_tipe', 'bulan');
        $filterBulan = $request->input('filter_bulan');
        $filterTahun = $request->input('filter_tahun');
        $dari = $request->input('dari_tanggal');
        $sampai = $request->input('sampai_tanggal');

        $labelPeriode = 'Semua Periode';
        $tglAkhirTeks = Carbon::now()->locale('id')->translatedFormat('d F Y');

        if ($filterTipe === 'bulan') {
            if (!$filterBulan) {
                $filterBulan = $bulanTersedia->first()['ym'] ?? Carbon::now()->format('Y-m');
            }
            try {
                $dtBulan = Carbon::parse($filterBulan . '-01');
            } catch (\Throwable) {
                $dtBulan = Carbon::now()->startOfMonth();
                $filterBulan = $dtBulan->format('Y-m');
            }
            $dari = $dtBulan->copy()->startOfMonth()->toDateString();
            $sampai = $dtBulan->copy()->endOfMonth()->toDateString();
            $labelPeriode = 'Per ' . $dtBulan->copy()->endOfMonth()->locale('id')->translatedFormat('d F Y');
            $tglAkhirTeks = $dtBulan->copy()->endOfMonth()->locale('id')->translatedFormat('d F Y');
        } elseif ($filterTipe === 'tahun') {
            if (!$filterTahun) {
                $filterTahun = $tahunTersedia->first()['tahun'] ?? Carbon::now()->format('Y');
            }
            try {
                $dtTahun = Carbon::createFromDate((int) $filterTahun, 1, 1);
            } catch (\Throwable) {
                $dtTahun = Carbon::now()->startOfYear();
                $filterTahun = $dtTahun->format('Y');
            }
            $dari = $dtTahun->copy()->startOfYear()->toDateString();
            $sampai = $dtTahun->copy()->endOfYear()->toDateString();
            $labelPeriode = 'Per 31 Desember ' . $filterTahun;
            $tglAkhirTeks = $dtTahun->copy()->endOfYear()->locale('id')->translatedFormat('d F Y');
        } elseif ($filterTipe === 'kustom' && ($dari || $sampai)) {
            $tglAkhir = $sampai ? Carbon::parse($sampai)->locale('id')->translatedFormat('d F Y') : Carbon::now()->locale('id')->translatedFormat('d F Y');
            $labelPeriode = "Per {$tglAkhir}";
            $tglAkhirTeks = $tglAkhir;
        } else {
            $filterTipe = 'semua';
            $dari = null;
            $sampai = null;
            $labelPeriode = 'Per ' . Carbon::now()->locale('id')->translatedFormat('d F Y');
        }

        // 1. Saldo Awal Kas (sebelum $dari jika ada batasan awal)
        $saldoAwal = 0.0;
        if ($dari) {
            $debitSebelumnya = (float) KasTransaksi::debit()->whereDate('tanggal', '<', $dari)->sum('nominal');
            $kreditSebelumnya = (float) KasTransaksi::kredit()->whereDate('tanggal', '<', $dari)->sum('nominal');
            $saldoAwal = $debitSebelumnya - $kreditSebelumnya;
        }

        $labelSaldoAwal = match ($filterTipe) {
            'bulan' => 'Saldo Kas Periode Sebelumnya (Bulan Lalu)',
            'tahun' => 'Saldo Kas Periode Sebelumnya (Tahun Lalu)',
            'kustom' => 'Saldo Kas Sebelum Periode',
            default => 'Saldo Awal Kas',
        };

        // 2. Mutasi Periode Berjalan
        $mutasiDebitQuery = KasTransaksi::debit();
        $mutasiKreditQuery = KasTransaksi::kredit();

        if ($dari && $sampai) {
            $mutasiDebitQuery->whereBetween('tanggal', [$dari, $sampai]);
            $mutasiKreditQuery->whereBetween('tanggal', [$dari, $sampai]);
        } elseif ($dari) {
            $mutasiDebitQuery->whereDate('tanggal', '>=', $dari);
            $mutasiKreditQuery->whereDate('tanggal', '>=', $dari);
        } elseif ($sampai) {
            $mutasiDebitQuery->whereDate('tanggal', '<=', $sampai);
            $mutasiKreditQuery->whereDate('tanggal', '<=', $sampai);
        }

        $totalDebitPeriode = (float) $mutasiDebitQuery->sum('nominal');
        $totalKreditPeriode = (float) $mutasiKreditQuery->sum('nominal');
        $surplusDefisitPeriode = $totalDebitPeriode - $totalKreditPeriode;

        // 3. Saldo Akhir Kas Kumulatif (Hingga Tanggal Cut-off $sampai)
        $totalDebitSampai = (float) KasTransaksi::debit()->when($sampai, fn($q) => $q->whereDate('tanggal', '<=', $sampai))->sum('nominal');
        $totalKreditSampai = (float) KasTransaksi::kredit()->when($sampai, fn($q) => $q->whereDate('tanggal', '<=', $sampai))->sum('nominal');
        $saldoAkhirKas = $totalDebitSampai - $totalKreditSampai;

        // 4. Struktur Aset (Aktiva) — Hanya Data Riil Buku Kas
        $asetLancar = [
            ['nama' => 'Kas Tunai Pesantren (Buku Kas Umum)', 'nominal' => $saldoAkhirKas],
        ];
        $totalAsetLancar = $saldoAkhirKas;

        $asetTetap = [];
        $totalAsetTetap = 0.0;

        $totalAset = $totalAsetLancar + $totalAsetTetap;

        // 5. Struktur Kewajiban (Liabilitas) — Kosong karena berbasis kas murni
        $kewajiban = [];
        $totalKewajiban = 0.0;

        // 6. Struktur Ekuitas Dana (Saldo Dana Pesantren — Sinkron Laba Rugi & Buku Kas)
        // Rumus: Saldo Periode Sebelumnya + Penerimaan Kas Periode Ini - Beban Kas Periode Ini = Surplus Kas Operasional
        $ekuitas = [
            ['nama' => $labelSaldoAwal, 'nominal' => $saldoAwal],
            ['nama' => 'Penerimaan Kas Periode Ini (Debit)', 'nominal' => $totalDebitPeriode],
            ['nama' => 'Dikurangi: Beban Pengeluaran Kas (Kredit)', 'nominal' => -$totalKreditPeriode],
        ];
        $totalEkuitas = $saldoAwal + $totalDebitPeriode - $totalKreditPeriode;

        // 7. Total Pasiva & Keseimbangan (Balance)
        $totalPasiva = $totalKewajiban + $totalEkuitas;
        $isBalance = abs($totalAset - $totalPasiva) < 0.01;

        // Data Kop Pengaturan
        $pengaturans = Pengaturan::getAllKeyValues();

        // Logo Satuan (Prioritaskan Logo Resmi Hexagon Pesantren)
        $logo = null;
        if (file_exists(public_path('images/logo-pesantren.png'))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo-pesantren.png')));
        } elseif (! empty($pengaturans['logo']) && file_exists(storage_path('app/public/' . $pengaturans['logo']))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['logo'])));
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
        }

        // Kaligrafi Arab Resmi
        $kaligrafi = null;
        if (file_exists(public_path('images/kaligrafi-kop.png'))) {
            $kaligrafi = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/kaligrafi-kop.png')));
        }

        // Banner Kop Surat Kustom (Hanya jika pengguna mengunggah gambar kop di halaman Pengaturan)
        $kopGambar = null;
        if (! empty($pengaturans['kop_gambar']) && file_exists(storage_path('app/public/' . $pengaturans['kop_gambar']))) {
            $ext = pathinfo($pengaturans['kop_gambar'], PATHINFO_EXTENSION);
            $mime = strtolower($ext) === 'png' ? 'image/png' : 'image/jpeg';
            $kopGambar = "data:{$mime};base64," . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['kop_gambar'])));
        }

        return [
            'filterTipe' => $filterTipe,
            'filterBulan' => $filterBulan,
            'filterTahun' => $filterTahun,
            'dari' => $dari,
            'sampai' => $sampai,
            'labelPeriode' => $labelPeriode,
            'tanggalAkhirRiil' => $tglAkhirTeks,
            'bulanTersedia' => $bulanTersedia,
            'tahunTersedia' => $tahunTersedia,
            'saldoAwal' => $saldoAwal,
            'totalDebitPeriode' => $totalDebitPeriode,
            'totalKreditPeriode' => $totalKreditPeriode,
            'surplusDefisitPeriode' => $surplusDefisitPeriode,
            'saldoAkhirKas' => $saldoAkhirKas,
            'asetLancar' => $asetLancar,
            'totalAsetLancar' => $totalAsetLancar,
            'asetTetap' => $asetTetap,
            'totalAsetTetap' => $totalAsetTetap,
            'totalAset' => $totalAset,
            'kewajiban' => $kewajiban,
            'totalKewajiban' => $totalKewajiban,
            'ekuitas' => $ekuitas,
            'totalEkuitas' => $totalEkuitas,
            'totalPasiva' => $totalPasiva,
            'isBalance' => $isBalance,
            'pengaturan' => $pengaturans,
            'pengaturans' => $pengaturans,
            'logo' => $logo,
            'kaligrafi' => $kaligrafi,
            'kopGambar' => $kopGambar,
            'isHtmlPrint' => false,
        ];
    }
}
