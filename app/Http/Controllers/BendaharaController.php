<?php

namespace App\Http\Controllers;

use App\Models\KasTransaksi;
use App\Models\Pengaturan;
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
        'Infaq & Shodaqoh',
        'Donasi & Bantuan',
        'Subsidi Pesantren',
        'Pendaftaran Santri',
        'Sumbangan Alumni',
        'Usaha Mandiri Pondok',
        'Lain-lain (Penerimaan)',
    ];

    protected array $kategoriKredit = [
        'ATK & Kesekretariatan',
        'Cetak Surat & Formulir',
        'Konsumsi & Rapat',
        'Listrik, Air & Internet',
        'Kebersihan & Kesehatan',
        'Transportasi & Operasional',
        'Pemeliharaan Sarana',
        'Bisyarah / Honorarium',
        'Lain-lain (Pengeluaran)',
    ];

    /**
     * Halaman Buku Kas Utama (1 Halaman Terpadu)
     */
    public function index(Request $request)
    {
        $periode = $request->input('periode', 'bulan-ini');
        $dari = $request->input('dari_tanggal');
        $sampai = $request->input('sampai_tanggal');
        $jenis = $request->input('jenis'); // 'debit', 'kredit', atau null (semua)
        $kategori = $request->input('kategori');
        $q = $request->input('q');

        // Tentukan rentang tanggal filter
        if ($periode === 'bulan-ini') {
            $dari = Carbon::now()->startOfMonth()->toDateString();
            $sampai = Carbon::now()->endOfMonth()->toDateString();
        } elseif ($periode === 'tahun-ini') {
            $dari = Carbon::now()->startOfYear()->toDateString();
            $sampai = Carbon::now()->endOfYear()->toDateString();
        } elseif ($periode === 'custom') {
            $dari = $dari ?: Carbon::now()->startOfMonth()->toDateString();
            $sampai = $sampai ?: Carbon::now()->endOfMonth()->toDateString();
        } else {
            // Semua Periode
            $dari = null;
            $sampai = null;
        }

        // Query Utama untuk Tabel
        $query = KasTransaksi::with('user')->orderBy('tanggal', 'desc')->orderBy('id', 'desc');

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

        $transaksis = $query->paginate(20)->withQueryString();

        // Saldo Kas Riil Keseluruhan (Lifetime Balance)
        $totalDebitKeseluruhan = (float) KasTransaksi::debit()->sum('nominal');
        $totalKreditKeseluruhan = (float) KasTransaksi::kredit()->sum('nominal');
        $saldoKasKeseluruhan = $totalDebitKeseluruhan - $totalKreditKeseluruhan;

        // Total Transaksi Sesuai Filter Saat Ini
        $filterSumQuery = KasTransaksi::query();
        if ($dari && $sampai) {
            $filterSumQuery->whereBetween('tanggal', [$dari, $sampai]);
        }
        if ($kategori) {
            $filterSumQuery->where('kategori', $kategori);
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

        // Kategori terpakai untuk filter dropdown
        $kategoriList = KasTransaksi::distinct()->pluck('kategori')->filter()->values();

        return view('bendahara.index', [
            'transaksis' => $transaksis,
            'saldoKasKeseluruhan' => $saldoKasKeseluruhan,
            'totalDebitKeseluruhan' => $totalDebitKeseluruhan,
            'totalKreditKeseluruhan' => $totalKreditKeseluruhan,
            'totalDebitFiltered' => $totalDebitFiltered,
            'totalKreditFiltered' => $totalKreditFiltered,
            'surplusDefisitFiltered' => $surplusDefisitFiltered,
            'totalCountFiltered' => $totalCountFiltered,
            'periode' => $periode,
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

        return redirect()->route('admin.bendahara.index')
            ->with('success', "Transaksi {$jenisLabel} sebesar {$nominalFormatted} berhasil dibukukan dengan nomor bukti {$kodeTransaksi}.");
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

        return redirect()->route('admin.bendahara.index')
            ->with('success', "Data transaksi {$bendahara->kode_transaksi} berhasil diperbarui.");
    }

    /**
     * Hapus Transaksi Kas (Soft Delete)
     */
    public function destroy(KasTransaksi $bendahara)
    {
        $kode = $bendahara->kode_transaksi;
        $bendahara->delete();

        return redirect()->route('admin.bendahara.index')
            ->with('success', "Transaksi kas {$kode} berhasil dihapus dari pembukuan.");
    }

    /**
     * Cetak Buku Kas Umum / Laporan Mutasi Kas (PDF)
     */
    public function cetak(Request $request)
    {
        try {
            $periode = $request->input('periode', 'bulan-ini');
            $dari = $request->filled('dari_tanggal') ? $request->input('dari_tanggal') : null;
            $sampai = $request->filled('sampai_tanggal') ? $request->input('sampai_tanggal') : null;
            $jenis = $request->filled('jenis') ? $request->input('jenis') : null;
            $kategori = $request->filled('kategori') ? $request->input('kategori') : null;
            $q = $request->filled('q') ? $request->input('q') : null;

            if ($periode === 'bulan-ini') {
                $dari = Carbon::now()->startOfMonth()->toDateString();
                $sampai = Carbon::now()->endOfMonth()->toDateString();
            } elseif ($periode === 'tahun-ini') {
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

            // Hitung Saldo Awal sebelum tanggal $dari (jika ada rentang tanggal)
            $saldoAwal = 0.0;
            if ($dari) {
                $debitSebelumnya = (float) KasTransaksi::debit()->whereDate('tanggal', '<', $dari)->sum('nominal');
                $kreditSebelumnya = (float) KasTransaksi::kredit()->whereDate('tanggal', '<', $dari)->sum('nominal');
                $saldoAwal = $debitSebelumnya - $kreditSebelumnya;
            }

            $totalDebit = (float) $transaksis->where('jenis', 'debit')->sum('nominal');
            $totalKredit = (float) $transaksis->where('jenis', 'kredit')->sum('nominal');
            $saldoAkhir = $saldoAwal + $totalDebit - $totalKredit;

            $pengaturans = Pengaturan::getAllKeyValues();

            $logo = null;
            if (! empty($pengaturans['logo']) && file_exists(storage_path('app/public/' . $pengaturans['logo']))) {
                $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['logo'])));
            } elseif (file_exists(public_path('images/logo.png'))) {
                $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo.png')));
            }

            $viewData = [
                'transaksis' => $transaksis,
                'saldoAwal' => $saldoAwal,
                'totalDebit' => $totalDebit,
                'totalKredit' => $totalKredit,
                'saldoAkhir' => $saldoAkhir,
                'dari' => $dari,
                'sampai' => $sampai,
                'jenis' => $jenis,
                'kategori' => $kategori,
                'periode' => $periode,
                'q' => $q,
                'pengaturan' => $pengaturans,
                'pengaturans' => $pengaturans,
                'logo' => $logo,
                'isHtmlPrint' => false,
            ];

            // Jika ekstensi PHP DOM tersedia, hasilkan berkas PDF murni (Landscape agar lapang & rapi)
            if (class_exists(\DOMImplementation::class)) {
                $pdf = Pdf::loadView('bendahara.cetak', $viewData)
                    ->setPaper('a4', 'landscape');

                $filename = 'buku-kas-umum-' . date('Ymd-His') . '.pdf';

                return $pdf->stream($filename);
            }

            // Fallback elegan: Jika PHP di server belum mengaktifkan ekstensi dom/xml,
            // langsung tampilkan halaman cetak web resmi (siap Simpan ke PDF via browser)
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
}
