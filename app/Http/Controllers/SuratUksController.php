<?php

namespace App\Http\Controllers;

use App\Models\KasTransaksi;
use App\Models\Pengaturan;
use App\Models\Santri;
use App\Models\SuratUks;
use App\Services\NomorSuratService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
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

        // 2. Metrik Kas Masuk (Terjual Rp 5.000 x jumlah terjual, otomatis sinkron ke buku kas bendahara)
        $jumlahTerjual = SuratUks::whereIn('status', ['terjual', 'kembali'])->count();
        $totalKasMasuk = $jumlahTerjual * 5000;
        $kasMasukPulang = SuratUks::pulang()->whereIn('status', ['terjual', 'kembali'])->count() * 5000;
        $kasMasukSakit = SuratUks::sakit()->whereIn('status', ['terjual', 'kembali'])->count() * 5000;

        // 3. Metrik Sisa Stok Blangko (Tersedia)
        $totalSisaStok = SuratUks::tersedia()->count();
        $stokPulang = SuratUks::pulang()->tersedia()->count();
        $stokSakit = SuratUks::sakit()->tersedia()->count();

        $pengaturans = Pengaturan::getAllKeyValues();

        return view('surat-uks.index', compact(
            'suratUks',
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
    public function generateBlangko(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'jenis' => ['required', 'in:pulang,sakit'],
            'jumlah' => ['required', 'integer', 'min:1', 'max:100'],
            'tanggal_cetak' => ['nullable', 'date'],
        ], [
            'jenis.required' => 'Pilih jenis surat izin (pulang atau sakit).',
            'jumlah.required' => 'Masukkan jumlah lembar blangko yang ingin dibuat.',
            'jumlah.min' => 'Jumlah minimal 1 lembar.',
            'jumlah.max' => 'Jumlah maksimal 100 lembar per sekali buat.',
        ]);

        $jenis = $validated['jenis'];
        $jumlah = (int) $validated['jumlah'];
        $tanggalCetak = !empty($validated['tanggal_cetak']) ? Carbon::parse($validated['tanggal_cetak']) : Carbon::today();

        DB::transaction(function () use ($jenis, $jumlah, $tanggalCetak) {
            for ($i = 0; $i < $jumlah; $i++) {
                $nomorSurat = $this->nomorSuratService->generateNomorSuratUks($jenis, $tanggalCetak);
                $kodeVerifikasi = SuratUks::generateKodeVerifikasi();

                SuratUks::create([
                    'nomor_surat' => $nomorSurat,
                    'kode_verifikasi' => $kodeVerifikasi,
                    'jenis' => $jenis,
                    'tanggal_cetak' => $tanggalCetak,
                    'harga' => 5000,
                    'status' => 'tersedia',
                    'user_id' => Auth::id(),
                ]);
            }
        });

        $label = ($jenis === 'sakit') ? 'Surat Izin Sakit' : 'Surat Izin Pulang';

        return redirect()
            ->route('admin.surat-uks.index')
            ->with('success', "Alhamdulillah, berhasil menerbitkan {$jumlah} lembar blangko kosong {$label} siap cetak dan bernomor urut resmi.");
    }

    /**
     * Catat Surat Terjual / Terpakai berdasarkan ID surat.
     * Cukup input: nama_santri, kelas, asrama.
     * Otomatis membuat Kas Masuk (Debit Rp 5.000) di modul Bendahara.
     */
    public function catatTerjual(Request $request, SuratUks $suratUk): RedirectResponse
    {
        if ($suratUk->status !== 'tersedia') {
            return back()->with('error', "Surat nomor {$suratUk->nomor_surat} sudah berstatus {$suratUk->status}.");
        }

        $validated = $request->validate([
            'nama_santri' => ['required', 'string', 'max:255'],
            'kelas' => ['required', 'string', 'max:100'],
            'asrama' => ['required', 'string', 'max:100'],
            'tanggal_pembelian' => ['nullable', 'date'],
        ], [
            'nama_santri.required' => 'Nama santri wajib diisi.',
            'kelas.required' => 'Kelas wajib diisi.',
            'asrama.required' => 'Asrama / kamar wajib diisi.',
        ]);

        $tanggal = !empty($validated['tanggal_pembelian']) ? Carbon::parse($validated['tanggal_pembelian']) : Carbon::today();
        $akunKategori = ($suratUk->jenis === 'sakit') ? 'Pendapatan Surat Izin Sakit' : 'Pendapatan Surat Izin Pulang';

        DB::transaction(function () use ($suratUk, $validated, $tanggal, $akunKategori) {
            // 1. Buat transaksi kas masuk (Debit Rp 5.000) di kas bendahara
            $kodeKas = KasTransaksi::generateKode('debit', $tanggal->toDateString());
            $kas = KasTransaksi::create([
                'kode_transaksi' => $kodeKas,
                'tanggal' => $tanggal,
                'jenis' => 'debit',
                'kategori' => $akunKategori,
                'nominal' => 5000,
                'nama_pihak' => $validated['nama_santri'] . ' (' . $validated['kelas'] . ' / ' . $validated['asrama'] . ')',
                'keterangan' => "Penjualan 1 lbr {$suratUk->jenis_label} No. {$suratUk->nomor_surat} (Kode: {$suratUk->kode_verifikasi}) a.n {$validated['nama_santri']} (1 x Rp 5.000)",
                'user_id' => Auth::id(),
            ]);

            // 2. Update status surat UKS
            $suratUk->update([
                'nama_santri' => $validated['nama_santri'],
                'kelas' => $validated['kelas'],
                'asrama' => $validated['asrama'],
                'tanggal_pembelian' => $tanggal,
                'status' => 'terjual',
                'kas_transaksi_id' => $kas->id,
            ]);
        });

        return redirect()
            ->route('admin.surat-uks.index')
            ->with('success', "Surat No. {$suratUk->nomor_surat} (Kode: {$suratUk->kode_verifikasi}) berhasil dicatat terjual untuk {$validated['nama_santri']}. Kas masuk Rp 5.000 otomatis tercatat di Bendahara.");
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
            return back()->with('error', "Surat dengan kode verifikasi {$kode} tidak ditemukan dalam database.");
        }

        if ($suratUk->status !== 'tersedia') {
            return back()->with('error', "Surat nomor {$suratUk->nomor_surat} (Kode: {$kode}) sudah berstatus {$suratUk->status} a.n {$suratUk->nama_santri}.");
        }

        return $this->catatTerjual($request, $suratUk);
    }

    /**
     * Tandai santri telah kembali ke pondok.
     */
    public function tandaiKembali(Request $request, SuratUks $suratUk): RedirectResponse
    {
        $suratUk->update([
            'status' => 'kembali',
            'tanggal_kembali_riil' => Carbon::now(),
        ]);

        return back()->with('success', "Santri {$suratUk->nama_santri} ({$suratUk->nomor_surat}) telah ditandai kembali ke pondok.");
    }

    /**
     * Hapus surat UKS (soft delete) beserta hapus kas jika ada.
     */
    public function destroy(SuratUks $suratUk): RedirectResponse
    {
        $nomor = $suratUk->nomor_surat;

        DB::transaction(function () use ($suratUk) {
            if ($suratUk->kas_transaksi_id && $suratUk->kasTransaksi) {
                $suratUk->kasTransaksi->delete();
            }
            $suratUk->delete();
        });

        return redirect()
            ->route('admin.surat-uks.index')
            ->with('success', "Data surat UKS {$nomor} berhasil dihapus.");
    }

    /**
     * Cetak surat UKS tunggal ke PDF.
     */
    public function cetakPdf(Request $request, SuratUks $suratUk): Response
    {
        $pengaturans = Pengaturan::getAllKeyValues();
        $suratList = collect([$suratUk]);

        $pdf = Pdf::loadView('surat-uks.pdf', compact('suratList', 'pengaturans'))
            ->setPaper('a4', 'portrait');

        $slug = Str::slug($suratUk->nomor_surat);
        $filename = "surat-uks-{$suratUk->kode_verifikasi}-{$slug}.pdf";

        return $pdf->stream($filename);
    }

    /**
     * Cetak surat UKS massal (batch) ke satu berkas PDF.
     */
    public function cetakBatch(Request $request): Response
    {
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
            abort(404, 'Tidak ada surat blangko yang ditemukan untuk dicetak.');
        }

        $pdf = Pdf::loadView('surat-uks.pdf', compact('suratList', 'pengaturans'))
            ->setPaper('a4', 'portrait');

        $filename = "blangko-surat-uks-batch-" . date('Ymd-His') . ".pdf";

        return $pdf->stream($filename);
    }

    /**
     * Ekspor dokumen Word blangko surat.
     */
    public function exportWord(Request $request, SuratUks $suratUk): Response
    {
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
