<?php

namespace App\Http\Controllers;

use App\Models\KasTransaksi;
use App\Models\Pengaturan;
use App\Models\Santri;
use App\Models\SuratUks;
use App\Services\NomorSuratService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class SuratUksController extends Controller
{
    public function __construct(
        protected NomorSuratService $nomorSuratService
    ) {}

    /**
     * Tampilkan daftar seluruh surat UKS (Izin Pulang & Izin Sakit), metrik laporan, dan filter.
     */
    public function index(Request $request): View
    {
        $query = SuratUks::with(['santri', 'user', 'kasTransaksi'])->latest('tanggal_cetak')->latest('id');

        if ($search = trim($request->input('search', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_surat', 'like', "%{$search}%")
                  ->orWhere('kode_verifikasi', 'like', "%{$search}%")
                  ->orWhere('nama_santri', 'like', "%{$search}%")
                  ->orWhere('kelas', 'like', "%{$search}%")
                  ->orWhere('asrama', 'like', "%{$search}%");
            });
        }

        if ($jenis = $request->input('jenis')) {
            $query->where('jenis', $jenis);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $suratUks = $query->paginate(20)->withQueryString();

        // 1. Metrik Surat Tercetak (Total, Izin Pulang, Izin Sakit)
        $totalTercetak = SuratUks::count();
        $totalCetakPulang = SuratUks::pulang()->count();
        $totalCetakSakit = SuratUks::sakit()->count();

        // 2. Metrik Kas Masuk (Terjual Rp 5.000 x jumlah terjual bulanan terakumulasi)
        $jumlahTerjual = SuratUks::whereIn('status', ['terjual', 'kembali'])->count();
        $totalKasMasuk = $jumlahTerjual * 5000;
        $kasMasukPulang = SuratUks::pulang()->whereIn('status', ['terjual', 'kembali'])->count() * 5000;
        $kasMasukSakit = SuratUks::sakit()->whereIn('status', ['terjual', 'kembali'])->count() * 5000;

        // 3. Metrik Sisa Stok Blangko (Tersedia)
        $totalSisaStok = SuratUks::tersedia()->count();
        $stokPulang = SuratUks::pulang()->tersedia()->count();
        $stokSakit = SuratUks::sakit()->tersedia()->count();

        $santris = Santri::where('status', 'aktif')
            ->orderBy('nama')
            ->get(['id', 'nama', 'kelas', 'kamar']);

        $santriJson = $santris->map(function ($s) {
            return [
                'nama' => $s->nama,
                'kelas' => $s->kelas ?? '',
                'asrama' => $s->kamar ?? '',
            ];
        })->values()->all();

        $pengaturans = Pengaturan::getAllKeyValues();

        return view('surat-uks.index', compact(
            'suratUks',
            'santris',
            'santriJson',
            'totalTercetak',
            'totalCetakPulang',
            'totalCetakSakit',
            'jumlahTerjual',
            'totalKasMasuk',
            'kasMasukPulang',
            'kasMasukSakit',
            'totalSisaStok',
            'stokPulang',
            'stokSakit',
            'pengaturans'
        ));
    }

    /**
     * Cetak / Buat Blangko Baru secara Masal (Batch)
     */
    /**
     * Cetak / Buat Blangko Baru secara Masal (Bisa Upload Berkas Banyak Sekaligus: PDF, Word, atau Gambar).
     * Setiap berkas otomatis diterbitkan Kode 4-Digit unik resmi.
     */
    public function generateBlangko(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:pulang,sakit'],
            'tanggal_cetak' => ['nullable', 'date'],
            'jumlah' => ['nullable', 'integer', 'min:0', 'max:100'],
            'berkas_surat' => ['nullable', 'array'],
            'berkas_surat.*' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:15360'],
        ], [
            'jenis.required' => 'Pilih jenis surat izin (pulang atau sakit).',
            'jenis.in' => 'Pilihan jenis surat tidak valid.',
            'jumlah.integer' => 'Jumlah lembar harus berupa angka bulat.',
            'jumlah.min' => 'Jumlah minimal 0 jika mengunggah berkas, atau minimal 1 jika tanpa berkas.',
            'jumlah.max' => 'Jumlah lembar maksimal 100.',
            'berkas_surat.*.file' => 'Berkas surat yang diunggah tidak valid.',
            'berkas_surat.*.mimes' => 'Format berkas harus PDF, Word (.doc/.docx), atau Gambar (.jpg, .jpeg, .png, .webp).',
            'berkas_surat.*.max' => 'Ukuran berkas maksimal 15 MB per file.',
        ]);

        $jenis = $validated['jenis'];
        $tanggalCetak = !empty($validated['tanggal_cetak']) ? Carbon::parse($validated['tanggal_cetak']) : Carbon::today();
        $files = $request->file('berkas_surat', []);
        $hasFiles = is_array($files) && count(array_filter($files)) > 0;
        $jumlah = (int) ($request->input('jumlah', 0));

        if (!$hasFiles && $jumlah <= 0) {
            return back()->withErrors([
                'jumlah' => 'Silakan pilih berkas surat untuk diunggah, atau masukkan jumlah minimal 1 lembar jika ingin membuat kode stok tanpa berkas.',
            ])->withInput();
        }

        $jumlahDibuat = 0;

        DB::transaction(function () use ($jenis, $tanggalCetak, $files, $hasFiles, $jumlah, &$jumlahDibuat) {
            if ($hasFiles) {
                foreach ($files as $file) {
                    if (!$file || !$file->isValid()) continue;

                    $kodeVerifikasi = SuratUks::generateKodeVerifikasi();
                    $ext = $file->getClientOriginalExtension();
                    $filename = "surat-{$kodeVerifikasi}-" . time() . "-{$jumlahDibuat}." . $ext;
                    $path = $file->storeAs('surat-uks', $filename, 'public');

                    SuratUks::create([
                        'nomor_surat' => $kodeVerifikasi,
                        'kode_verifikasi' => $kodeVerifikasi,
                        'jenis' => $jenis,
                        'tanggal_cetak' => $tanggalCetak,
                        'harga' => 5000,
                        'status' => 'tersedia',
                        'file_surat' => $path,
                        'user_id' => Auth::id(),
                    ]);

                    $jumlahDibuat++;
                }
            } else {
                for ($i = 0; $i < $jumlah; $i++) {
                    $kodeVerifikasi = SuratUks::generateKodeVerifikasi();

                    SuratUks::create([
                        'nomor_surat' => $kodeVerifikasi,
                        'kode_verifikasi' => $kodeVerifikasi,
                        'jenis' => $jenis,
                        'tanggal_cetak' => $tanggalCetak,
                        'harga' => 5000,
                        'status' => 'tersedia',
                        'user_id' => Auth::id(),
                    ]);

                    $jumlahDibuat++;
                }
            }
        });

        $label = ($jenis === 'sakit') ? 'Surat Izin Sakit' : 'Surat Izin Pulang';

        return redirect()
            ->route('admin.surat-uks.index')
            ->with('success', "Alhamdulillah, berhasil menerbitkan {$jumlahDibuat} lembar berkas {$label} dengan Kode 4-Digit resmi siap pakai.");
    }

    /**
     * Endpoint Cek Kode Verifikasi 4-Digit secara Realtime via AJAX.
     */
    public function cekKode(Request $request): JsonResponse
    {
        $kode = strtoupper(trim((string) $request->input('kode', '')));
        if (strlen($kode) < 4) {
            return response()->json(['found' => false]);
        }

        $surat = SuratUks::where('kode_verifikasi', $kode)->first();
        if (! $surat) {
            return response()->json([
                'found' => false,
                'status' => 'tidak_ada',
                'pesan' => '✕ Kode Tidak Terdaftar',
            ]);
        }

        $isTersedia = ($surat->status === 'tersedia');
        $label = ($surat->jenis === 'sakit') ? 'Surat Izin Sakit' : 'Surat Izin Pulang';

        return response()->json([
            'found' => true,
            'id' => $surat->id,
            'kode' => $surat->kode_verifikasi,
            'status' => $surat->status,
            'jenis' => $surat->jenis,
            'jenis_label' => $label,
            'nama_santri' => $surat->nama_santri,
            'kelas' => $surat->kelas,
            'asrama' => $surat->asrama,
            'file_url' => $surat->file_url,
            'is_image' => $surat->is_image,
            'is_pdf' => $surat->is_pdf,
            'is_word' => $surat->is_word,
            'pesan' => $isTersedia 
                ? "✓ Kode Terdaftar ({$label})" 
                : ($surat->status === 'terjual' ? "⚠️ Kode Sudah Terjual a.n {$surat->nama_santri}" : "⚠️ Kode Berstatus Telah Kembali"),
        ]);
    }

    /**
     * Sinkronkan Kas Bulanan ke Buku Kas Bendahara secara Terakumulasi:
     * Menghitung total lembar terjual pada bulan & tahun tersebut,
     * lalu mencatat 1 transaksi kas masuk (Debit) di Bendahara dengan keterangan:
     * "Penjualan [X] lbr [Jenis] Bulan [Bulan Tahun] ([X] lbr x Rp 5.000)".
     */
    public static function syncKasBulanan(string $jenis, int $year, int $month): ?KasTransaksi
    {
        $akunKategori = ($jenis === 'sakit') ? 'Pendapatan SIS' : 'Pendapatan SIP';
        $jenisLabel = ($jenis === 'sakit') ? 'Surat Izin Sakit' : 'Surat Izin Pulang';

        $startOfMonth = Carbon::createFromDate($year, $month, 1)->startOfMonth()->toDateString();
        $endOfMonth = Carbon::createFromDate($year, $month, 1)->endOfMonth()->toDateString();

        // Ambil semua surat yang terjual/kembali di bulan & tahun bersangkutan
        $suratList = SuratUks::where('jenis', $jenis)
            ->whereIn('status', ['terjual', 'kembali'])
            ->whereBetween('tanggal_pembelian', [$startOfMonth, $endOfMonth])
            ->get();

        $totalLembar = $suratList->count();
        $carbonBulan = Carbon::createFromDate($year, $month, 1)->locale('id');
        $namaBulan = $carbonBulan->translatedFormat('F Y');

        // Cari transaksi kas bulanan yang sudah ada di modul Bendahara
        $kas = KasTransaksi::where('kategori', $akunKategori)
            ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
            ->first();

        // Jika tidak ada lembar terjual di bulan ini, hapus transaksi kas jika pernah ada
        if ($totalLembar === 0) {
            if ($kas) {
                $kas->delete();
            }
            return null;
        }

        $nominal = $totalLembar * 5000;
        $keterangan = "Penjualan {$totalLembar} lbr {$jenisLabel} Bulan {$namaBulan} ({$totalLembar} lbr x Rp 5.000)";
        $tanggalTerakhir = $suratList->max('tanggal_pembelian') ? Carbon::parse($suratList->max('tanggal_pembelian'))->toDateString() : $endOfMonth;

        if (! $kas) {
            $kodeKas = KasTransaksi::generateKode('debit', (string) $tanggalTerakhir);
            $kas = KasTransaksi::create([
                'kode_transaksi' => $kodeKas,
                'tanggal' => $tanggalTerakhir,
                'jenis' => 'debit',
                'kategori' => $akunKategori,
                'nominal' => $nominal,
                'nama_pihak' => 'Unit Kesehatan Santri (UKS)',
                'keterangan' => $keterangan,
                'user_id' => Auth::id(),
            ]);
        } else {
            $kas->update([
                'tanggal' => $tanggalTerakhir,
                'nominal' => $nominal,
                'keterangan' => $keterangan,
            ]);
        }

        // Tautkan kas_transaksi_id di seluruh surat pada bulan tersebut
        SuratUks::whereIn('id', $suratList->pluck('id'))
            ->update(['kas_transaksi_id' => $kas->id]);

        return $kas;
    }

    /**
     * Sinkronkan seluruh rekap kas penjualan UKS per bulan ke Buku Kas Bendahara.
     */
    public function syncSemuaKas(): RedirectResponse
    {
        $suratList = SuratUks::whereIn('status', ['terjual', 'kembali'])
            ->whereNotNull('tanggal_pembelian')
            ->get(['jenis', 'tanggal_pembelian']);

        $periods = [];
        foreach ($suratList as $s) {
            $y = (int) $s->tanggal_pembelian->format('Y');
            $m = (int) $s->tanggal_pembelian->format('m');
            $key = "{$s->jenis}_{$y}_{$m}";
            $periods[$key] = [$s->jenis, $y, $m];
        }

        foreach ($periods as [$jenis, $y, $m]) {
            self::syncKasBulanan($jenis, $y, $m);
        }

        return back()->with('success', 'Alhamdulillah, seluruh data kas masuk bulanan berhasil disinkronkan ke Buku Kas Bendahara.');
    }

    /**
     * Helper untuk memastikan parameter selalu berupa instance SuratUks
     */
    protected function resolveSuratUks(mixed $suratUk): SuratUks
    {
        return $suratUk instanceof SuratUks ? $suratUk : SuratUks::findOrFail($suratUk);
    }

    /**
     * Catat Surat Terjual / Terpakai berdasarkan ID surat.
     * Cukup input: nama_santri, kelas, asrama.
     * Otomatis mengupdate kas masuk bulanan di modul Bendahara.
     */
    public function catatTerjual(Request $request, mixed $suratUk): RedirectResponse
    {
        $suratUk = $this->resolveSuratUks($suratUk);

        if ($suratUk->status !== 'tersedia') {
            return back()->with('error', "Surat Kode {$suratUk->kode_verifikasi} sudah berstatus {$suratUk->status}.");
        }

        $validated = $request->validate([
            'nama_santri' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:100'],
            'asrama' => ['required', 'string', 'max:100'],
            'tanggal_pembelian' => ['nullable', 'date'],
            'file_bukti' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:15360'],
        ], [
            'nama_santri.required' => 'Nama santri wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'asrama.required' => 'Asrama / kamar wajib diisi.',
        ]);

        $tanggal = !empty($validated['tanggal_pembelian']) ? Carbon::parse($validated['tanggal_pembelian']) : Carbon::today();

        $buktiPath = null;
        if ($request->hasFile('file_bukti') && $request->file('file_bukti')->isValid()) {
            $buktiPath = $request->file('file_bukti')->store('surat-uks/bukti', 'public');
        }

        DB::transaction(function () use ($suratUk, $validated, $tanggal, $buktiPath) {
            $dataUpdate = [
                'nama_santri' => $validated['nama_santri'],
                'kelas' => $validated['kelas'],
                'asrama' => $validated['asrama'],
                'tanggal_pembelian' => $tanggal,
                'status' => 'terjual',
            ];
            if ($buktiPath) {
                $dataUpdate['file_bukti_terjual'] = $buktiPath;
            }

            $suratUk->update($dataUpdate);

            self::syncKasBulanan($suratUk->jenis, (int) $tanggal->format('Y'), (int) $tanggal->format('m'));
        });

        return redirect()
            ->route('admin.surat-uks.index')
            ->with('success', "Surat Kode {$suratUk->kode_verifikasi} berhasil dicatat Terjual untuk {$validated['nama_santri']}. Kas penjualan bulan ini otomatis terakumulasi di Buku Kas Bendahara.");
    }

    /**
     * Catat Surat Terjual via 4-Digit Kode Verifikasi.
     */
    public function catatTerjualKode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_verifikasi' => ['required', 'string', 'size:4'],
            'nama_santri' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:100'],
            'asrama' => ['required', 'string', 'max:100'],
            'tanggal_pembelian' => ['nullable', 'date'],
            'file_bukti' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,jpeg,png,webp', 'max:15360'],
        ], [
            'kode_verifikasi.required' => 'Kode verifikasi 4-digit wajib diisi.',
            'kode_verifikasi.size' => 'Kode verifikasi harus tepat 4 karakter.',
            'nama_santri.required' => 'Nama santri wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'asrama.required' => 'Asrama / kamar wajib diisi.',
        ]);

        $kode = strtoupper(trim($validated['kode_verifikasi']));
        $suratUk = SuratUks::where('kode_verifikasi', $kode)->first();

        if (! $suratUk) {
            return back()->withInput()->with('error', "Kode verifikasi {$kode} Tidak Terdaftar.");
        }

        if ($suratUk->status !== 'tersedia') {
            return back()->withInput()->with('error', "Surat Kode {$kode} sudah berstatus {$suratUk->status} a.n {$suratUk->nama_santri}.");
        }

        return $this->catatTerjual($request, $suratUk);
    }

    /**
     * Tandai santri telah kembali ke pondok.
     */
    public function tandaiKembali(Request $request, mixed $suratUk): RedirectResponse
    {
        $suratUk = $this->resolveSuratUks($suratUk);

        $suratUk->update([
            'status' => 'kembali',
            'tanggal_kembali_riil' => Carbon::now(),
        ]);

        return back()->with('success', "Santri {$suratUk->nama_santri} ({$suratUk->nomor_surat}) telah ditandai kembali ke pondok.");
    }

    /**
     * Hapus surat UKS (soft delete) beserta update akumulasi kas bulanan jika ada.
     */
    public function destroy(Request $request, mixed $suratUk): JsonResponse|RedirectResponse
    {
        $suratUk = $this->resolveSuratUks($suratUk);
        $nomor = $suratUk->nomor_surat;

        DB::transaction(function () use ($suratUk, $nomor) {
            $tgl = $suratUk->tanggal_pembelian;
            $jenis = $suratUk->jenis;
            $kasId = $suratUk->kas_transaksi_id;

            // Lepaskan nomor surat dengan suffix -BATAL agar nomor aslinya langsung bebas dipakai ulang
            $suratUk->nomor_surat = $nomor . '-BATAL-' . $suratUk->id;
            $suratUk->saveQuietly();
            $suratUk->delete();

            if ($tgl) {
                self::syncKasBulanan($jenis, (int) $tgl->format('Y'), (int) $tgl->format('m'));
            } elseif ($kasId) {
                $kas = KasTransaksi::find($kasId);
                if ($kas) {
                    $kas->delete();
                }
            }
        });

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Data surat UKS {$nomor} berhasil dihapus.",
            ]);
        }

        return redirect()
            ->route('admin.surat-uks.index')
            ->with('success', "Data surat UKS {$nomor} berhasil dihapus.");
    }

    /**
     * Cetak surat UKS tunggal ke PDF ukuran A4 dibagi 3 (Lebar: 70mm, Tinggi: 297mm).
     */
    public function cetakPdf(Request $request, mixed $suratUk): Response|RedirectResponse|View
    {
        try {
            $suratUk = $this->resolveSuratUks($suratUk);
            $pengaturans = Pengaturan::getAllKeyValues();
            $suratList = collect([$suratUk]);

            if (class_exists(\DOMImplementation::class)) {
                // Ukuran A4 bagi 3: Lebar 70mm (198.43 pt), Tinggi 297mm (841.89 pt)
                $pdf = Pdf::loadView('surat-uks.pdf', compact('suratList', 'pengaturans'))
                    ->setPaper([0, 0, 198.43, 841.89], 'portrait');

                $slug = Str::slug($suratUk->nomor_surat);
                $filename = "surat-uks-{$suratUk->kode_verifikasi}-{$slug}.pdf";

                return $pdf->stream($filename);
            }

            // Fallback pratinjau cetak browser jika server hosting belum mengaktifkan ekstensi DOM
            $isHtmlPrint = true;
            return response()->view('surat-uks.pdf', compact('suratList', 'pengaturans', 'isHtmlPrint'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Fallback cetak web Surat UKS karena kendala PDF: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            if (isset($suratList) && isset($pengaturans)) {
                $isHtmlPrint = true;
                return response()->view('surat-uks.pdf', compact('suratList', 'pengaturans', 'isHtmlPrint'));
            }

            return back()->with('error', 'Gagal memproses cetak PDF: ' . $e->getMessage());
        }
    }

    /**
     * Cetak surat UKS massal (batch) ke satu berkas PDF ukuran 70mm x 297mm.
     */
    public function cetakBatch(Request $request): Response|RedirectResponse|View
    {
        try {
            $pengaturans = Pengaturan::getAllKeyValues();
            $query = SuratUks::latest('id');

            if ($ids = $request->input('ids')) {
                $idArray = is_array($ids) ? $ids : explode(',', $ids);
                $query->whereIn('id', $idArray);
            } else {
                if ($jenis = $request->input('jenis')) {
                    $query->where('jenis', $jenis);
                }
                if ($status = $request->input('status', 'tersedia')) {
                    $query->where('status', $status);
                }
                $limit = min(50, max(1, (int) $request->input('limit', 20)));
                $query->limit($limit);
            }

            $suratList = $query->get();

            if ($suratList->isEmpty()) {
                return back()->with('error', 'Tidak ada surat blangko yang ditemukan untuk dicetak.');
            }

            if (class_exists(\DOMImplementation::class)) {
                // Ukuran A4 bagi 3: Lebar 70mm (198.43 pt), Tinggi 297mm (841.89 pt)
                $pdf = Pdf::loadView('surat-uks.pdf', compact('suratList', 'pengaturans'))
                    ->setPaper([0, 0, 198.43, 841.89], 'portrait');

                $filename = "blangko-surat-uks-batch-" . date('Ymd-His') . ".pdf";

                return $pdf->stream($filename);
            }

            $isHtmlPrint = true;
            return response()->view('surat-uks.pdf', compact('suratList', 'pengaturans', 'isHtmlPrint'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('Fallback cetak batch web Surat UKS karena kendala PDF: ' . $e->getMessage());

            if (isset($suratList) && isset($pengaturans)) {
                $isHtmlPrint = true;
                return response()->view('surat-uks.pdf', compact('suratList', 'pengaturans', 'isHtmlPrint'));
            }

            return back()->with('error', 'Gagal memproses cetak batch PDF: ' . $e->getMessage());
        }
    }

    /**
     * Ekspor dokumen Word blangko surat.
     */
    public function exportWord(Request $request, mixed $suratUk): Response
    {
        $suratUk = $this->resolveSuratUks($suratUk);
        $pengaturans = Pengaturan::getAllKeyValues();
        $content = view('surat-uks.word', compact('suratUk', 'pengaturans'))->render();

        $slug = Str::slug($suratUk->nomor_surat);
        $filename = "surat-uks-{$suratUk->kode_verifikasi}-{$slug}.doc";

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-word; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
