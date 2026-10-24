<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuratKeluarRequest;
use App\Models\KategoriSurat;
use App\Models\Pengaturan;
use App\Models\Pengurus;
use App\Models\SuratKeluar;
use App\Models\TandaTangan;
use App\Models\TemplateSurat;
use App\Services\DocxSuratParserService;
use App\Services\NomorSuratService;
use App\Services\TandaTanganService;
use Carbon\Carbon;
use Database\Seeders\TemplateSuratSeeder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratKeluarController extends Controller
{
    public function __construct(
        protected NomorSuratService $nomorSuratService,
        protected TandaTanganService $tandaTanganService,
        protected DocxSuratParserService $docxParserService
    ) {}

    /**
     * Baca dan deteksi otomatis isi file Word (.docx) yang diunggah (AJAX).
     */
    public function parseDocx(Request $request): JsonResponse
    {
        $request->validate([
            'file_surat' => ['required', 'file', 'extensions:docx,doc,html,htm,txt', 'max:10240'],
        ], [
            'file_surat.required' => 'Silakan pilih file surat Word (.docx) terlebih dahulu.',
            'file_surat.extensions' => 'File surat harus berformat Word (.docx / .doc).',
        ]);

        try {
            $parsed = $this->docxParserService->parseUploadedDocument($request->file('file_surat'));

            // Buat saran nomor surat otomatis bila di dalam Word nomornya kosong atau belum lengkap
            $kodeJenis = $parsed['kode_jenis'] ?: 'SPm.01';
            $tglSurat = $parsed['tgl_surat'] ?: date('Y-m-d');
            $pengaturan = Pengaturan::getAllKeyValues();
            $lembaga = $pengaturan['kode_lembaga'] ?? 'AF-PA';
            $bulanRomawi = $this->nomorSuratService->toRomanMonth((int) date('n', strtotime($tglSurat)));
            $tahun = date('Y', strtotime($tglSurat));
            $urutBerikutnya = str_pad((string) (SuratKeluar::whereYear('tgl_surat', $tahun)->withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
            $saranNomor = "{$urutBerikutnya}/{$kodeJenis}/{$lembaga}/{$bulanRomawi}/{$tahun}";

            return response()->json([
                'success' => true,
                'data' => array_merge($parsed, [
                    'saran_no_surat' => $saranNomor,
                ]),
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Gagal parse otomatis file Word: ' . $e->getMessage(), ['exception' => $e]);

            // Fallback aman agar proses upload tidak gagal server error
            $fileName = pathinfo($request->file('file_surat')->getClientOriginalName(), PATHINFO_FILENAME);
            $tglSurat = date('Y-m-d');
            $pengaturan = Pengaturan::getAllKeyValues();
            $lembaga = $pengaturan['kode_lembaga'] ?? 'AF-PA';
            $bulanRomawi = $this->nomorSuratService->toRomanMonth((int) date('n'));
            $tahun = date('Y');
            $urutBerikutnya = str_pad((string) (SuratKeluar::whereYear('tgl_surat', $tahun)->withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);
            $saranNomor = "{$urutBerikutnya}/SPm.01/{$lembaga}/{$bulanRomawi}/{$tahun}";

            return response()->json([
                'success' => true,
                'data' => [
                    'no_surat' => '',
                    'is_nomor_lengkap' => false,
                    'kode_jenis' => 'SPm.01',
                    'lampiran_teks' => '-',
                    'ada_lampiran' => false,
                    'perihal' => $fileName,
                    'tujuan' => 'Penerima Surat',
                    'pengirim' => 'Instansi Pengirim',
                    'tgl_surat' => $tglSurat,
                    'tempat_surat' => 'Putak',
                    'isi_html' => '<p>' . e($fileName) . '</p>',
                    'saran_no_surat' => $saranNomor,
                ],
            ]);
        }
    }

    /**
     * Tampilkan daftar surat keluar dengan filter dan pencarian.
     */
    public function index(Request $request): View
    {
        $query = SuratKeluar::with(['kategori', 'user', 'tandaTangan', 'tandaTangans'])->latest('id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_surat', 'like', "%{$q}%")
                    ->orWhere('tujuan', 'like', "%{$q}%")
                    ->orWhere('perihal', 'like', "%{$q}%")
                    ->orWhere('lampiran_teks', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_mulai')) {
            $query->whereDate('tgl_surat', '>=', $request->tgl_mulai);
        }

        if ($request->filled('tgl_selesai')) {
            $query->whereDate('tgl_surat', '<=', $request->tgl_selesai);
        }

        $suratKeluars = $query->paginate(15)->withQueryString();
        $kategoriList = KategoriSurat::orderBy('nama')->get();

        $statistik = [
            'total' => SuratKeluar::count(),
            'draft' => SuratKeluar::where('status', 'draft')->count(),
            'final' => SuratKeluar::where('status', 'final')->count(),
            'terkirim' => SuratKeluar::where('status', 'terkirim')->count(),
        ];

        return view('surat-keluar.index', compact('suratKeluars', 'kategoriList', 'statistik'));
    }

    /**
     * Unduh buku induk administrasi surat keluar dalam format CSV.
     * Kolom: No, Tanggal Pembuatan Surat, Jenis Surat, Perihal, Nomor Surat, Tujuan Surat.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = SuratKeluar::with(['kategori'])->latest('tgl_surat')->latest('id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_surat', 'like', "%{$q}%")
                    ->orWhere('tujuan', 'like', "%{$q}%")
                    ->orWhere('perihal', 'like', "%{$q}%")
                    ->orWhere('lampiran_teks', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_mulai')) {
            $query->whereDate('tgl_surat', '>=', $request->tgl_mulai);
        }

        if ($request->filled('tgl_selesai')) {
            $query->whereDate('tgl_surat', '<=', $request->tgl_selesai);
        }

        $filename = 'buku-induk-surat-keluar-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($query) {
            $output = fopen('php://output', 'w');
            // Tambahkan UTF-8 BOM agar rapi saat dibuka di Microsoft Excel
            fputs($output, "\xEF\xBB\xBF");
            fputcsv($output, [
                'No',
                'Tanggal Pembuatan Surat',
                'Jenis Surat',
                'Perihal',
                'Nomor Surat',
                'Tujuan Surat',
            ]);

            $no = 1;
            $query->chunk(200, function ($surats) use ($output, &$no) {
                foreach ($surats as $surat) {
                    fputcsv($output, [
                        $no++,
                        $surat->tgl_surat ? Carbon::parse($surat->tgl_surat)->format('d-m-Y') : '-',
                        $surat->kategori?->nama ?? '-',
                        $surat->perihal ?? '-',
                        $surat->no_surat ?? '-',
                        $surat->tujuan ?? '-',
                    ]);
                }
            });

            fclose($output);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Form buat surat keluar baru.
     */
    public function create(): View
    {
        if (TemplateSurat::count() === 0) {
            (new TemplateSuratSeeder)->run();
        }

        $kategoriList = KategoriSurat::orderBy('nama')->get();
        $templateList = TemplateSurat::where('is_aktif', true)->orderBy('id')->get();
        $pengurusList = Pengurus::where('is_aktif', true)->orderBy('nama')->get();
        $pengaturan = Pengaturan::getAllKeyValues();

        do {
            $suggestedKode = strtoupper(Str::random(8));
        } while (SuratKeluar::where('kode_verifikasi', $suggestedKode)->exists());

        $qrPreviewDataUri = $this->tandaTanganService->generateQrCode(url('/verifikasi/' . $suggestedKode));

        return view('surat-keluar.create', compact('kategoriList', 'templateList', 'pengurusList', 'suggestedKode', 'qrPreviewDataUri', 'pengaturan'));
    }

    /**
     * Simpan surat keluar baru (mendukung Upload File Word otomatis maupun input manual).
     */
    public function store(SuratKeluarRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $fileSuratAsliPath = null;
        $parsedDocx = null;

        if ($request->hasFile('file_surat_asli')) {
            $uploadedWord = $request->file('file_surat_asli');
            try {
                $parsedDocx = $this->docxParserService->parseUploadedDocument($uploadedWord);
            } catch (\Throwable $e) {
                \Log::warning('Gagal parse dokumen Word saat store: ' . $e->getMessage());
            }
            Storage::disk('public')->makeDirectory('surat-keluar-word');
            $ext = strtolower($uploadedWord->getClientOriginalExtension()) ?: 'docx';
            $fileName = Str::random(40) . '.' . $ext;
            $fileSuratAsliPath = $uploadedWord->storeAs('surat-keluar-word', $fileName, 'public');
        }

        $tglSurat = $validated['tgl_surat'] ?? ($parsedDocx['tgl_surat'] ?? date('Y-m-d'));
        $tempatSurat = $validated['tempat_surat'] ?? ($parsedDocx['tempat_surat'] ?? 'Putak');
        $perihal = $validated['perihal'] ?? ($parsedDocx['perihal'] ?? 'Surat Keluar');
        $lampiranTeks = $validated['lampiran_teks'] ?? ($parsedDocx['lampiran_teks'] ?? '-');
        $tujuan = $validated['tujuan'] ?? ($parsedDocx['tujuan'] ?? '-');
        $isi = $validated['isi'] ?? ($parsedDocx['isi_html'] ?? '<p>' . e($perihal) . '</p>');
        $namaPenandatangan = $validated['nama_penandatangan'] ?? '-';
        $jabatanPenandatangan = $validated['jabatan_penandatangan'] ?? '-';

        // Tentukan kode jenis surat
        $kodeJenisInput = $validated['kode_jenis_surat'] ?? ($parsedDocx['kode_jenis'] ?? null);
        if (!empty($kodeJenisInput)) {
            $kodeJenis = strtoupper(trim($kodeJenisInput));
            $kategori = KategoriSurat::firstOrCreate(
                ['kode' => $kodeJenis],
                ['nama' => $kodeJenis, 'keterangan' => 'Jenis surat ' . $kodeJenis]
            );
        } elseif (!empty($validated['kategori_id'])) {
            $kategori = KategoriSurat::findOrFail($validated['kategori_id']);
        } else {
            $kategori = KategoriSurat::firstOrCreate(
                ['kode' => 'SPm.01'],
                ['nama' => 'Surat Permohonan']
            );
        }

        // Gunakan nomor dari Word/input manual bila lengkap dan belum dipakai; jika kosong/belum lengkap buat otomatis
        $noSuratCandidate = trim((string) ($request->input('no_surat_manual') ?: ($parsedDocx['no_surat'] ?? '')));
        $hasPlaceholder = preg_match('/(\.{2,}|_{2,}|\[\s*\]|^\/)/', $noSuratCandidate);

        if ($noSuratCandidate !== '' && ! $hasPlaceholder && ! SuratKeluar::withTrashed()->where('no_surat', $noSuratCandidate)->exists()) {
            $noSurat = $noSuratCandidate;
        } else {
            $noSurat = $this->nomorSuratService->generateNomorSuratKeluar($kategori->kode, $tglSurat);
        }

        $filePath = null;
        if ($request->hasFile('file')) {
            Storage::disk('public')->makeDirectory('surat-keluar');
            $ext = strtolower($request->file('file')->getClientOriginalExtension()) ?: 'pdf';
            $fileName = Str::random(40) . '.' . $ext;
            $filePath = $request->file('file')->storeAs('surat-keluar', $fileName, 'public');
        }

        $kodeVerifikasi = !empty($validated['kode_verifikasi'])
            ? strtoupper(trim($validated['kode_verifikasi']))
            : null;

        if (! $kodeVerifikasi) {
            do {
                $kodeVerifikasi = strtoupper(Str::random(8));
            } while (SuratKeluar::where('kode_verifikasi', $kodeVerifikasi)->exists());
        }

        $jmlPenandatangan = max(1, min(3, (int) ($validated['jumlah_penandatangan'] ?? 1)));
        $metodePilihan = $request->input('metode_ttd_pilihan');
        $ttd1Data = !empty($validated['data_ttd_langsung']) ? $validated['data_ttd_langsung'] : null;
        $ttd2Data = !empty($validated['data_ttd_2']) ? $validated['data_ttd_2'] : null;
        $ttd3Data = !empty($validated['data_ttd_3']) ? $validated['data_ttd_3'] : null;

        $anyDirectSign = ($ttd1Data !== null || $ttd2Data !== null || $ttd3Data !== null);

        $penandatangansData = [];
        $penandatangansData[] = [
            'urutan' => 1,
            'nama' => $namaPenandatangan,
            'jabatan' => $jabatanPenandatangan,
            'metode' => $ttd1Data ? 'digital' : ($metodePilihan === 'basah' ? 'basah' : ($request->input('penandatangan_1_metode', 'nanti'))),
            'status' => ($ttd1Data || $metodePilihan === 'basah' || $fileSuratAsliPath) ? 'sudah' : 'belum',
            'data_ttd' => $ttd1Data,
        ];

        if ($jmlPenandatangan >= 2) {
            $nama2 = $request->input('penandatangan_2_nama') ?: 'Penandatangan 2';
            $jabatan2 = $request->input('penandatangan_2_jabatan') ?: '-';
            $penandatangansData[] = [
                'urutan' => 2,
                'nama' => $nama2,
                'jabatan' => $jabatan2,
                'metode' => $ttd2Data ? 'digital' : ($metodePilihan === 'basah' ? 'basah' : ($request->input('penandatangan_2_metode', 'nanti'))),
                'status' => ($ttd2Data || $metodePilihan === 'basah') ? 'sudah' : 'belum',
                'data_ttd' => $ttd2Data,
            ];
        }

        if ($jmlPenandatangan >= 3) {
            $nama3 = $request->input('penandatangan_3_nama') ?: 'Penandatangan 3';
            $jabatan3 = $request->input('penandatangan_3_jabatan') ?: '-';
            $penandatangansData[] = [
                'urutan' => 3,
                'nama' => $nama3,
                'jabatan' => $jabatan3,
                'metode' => $ttd3Data ? 'digital' : ($metodePilihan === 'basah' ? 'basah' : ($request->input('penandatangan_3_metode', 'nanti'))),
                'status' => ($ttd3Data || $metodePilihan === 'basah') ? 'sudah' : 'belum',
                'data_ttd' => $ttd3Data,
            ];
        }

        $signedCount = 0;
        foreach ($penandatangansData as $pItem) {
            if (!empty($pItem['data_ttd'])) {
                $signedCount++;
            }
        }
        $allDigitalSigned = ($signedCount >= count($penandatangansData));

        $aksiSimpan = $request->input('aksi_simpan');
        $statusAwal = $validated['status'] ?? 'draft';

        if ($fileSuratAsliPath || $aksiSimpan === 'terbitkan_word') {
            $statusAwal = 'final';
        } elseif ($aksiSimpan === 'siap_kirim') {
            $statusAwal = 'terkirim';
        } elseif ($allDigitalSigned && $statusAwal === 'draft') {
            $statusAwal = 'final';
        }

        $metodeTtdFinal = null;
        $statusTtdFinal = 'belum';
        if ($fileSuratAsliPath) {
            $metodeTtdFinal = 'digital';
            $statusTtdFinal = 'sudah';
        } elseif ($metodePilihan === 'basah') {
            $metodeTtdFinal = 'basah';
            $statusTtdFinal = ($statusAwal !== 'draft') ? 'sudah' : 'belum';
        } elseif ($anyDirectSign) {
            $metodeTtdFinal = 'digital';
            $statusTtdFinal = $allDigitalSigned ? 'sudah' : 'belum';
        }

        $penandatangansClean = array_map(function ($item) {
            $raw = $item;
            unset($raw['data_ttd']);
            return $raw;
        }, $penandatangansData);

        $suratKeluar = SuratKeluar::create([
            'no_surat' => $noSurat,
            'tgl_surat' => $tglSurat,
            'tempat_surat' => trim($tempatSurat) ?: 'Putak',
            'tujuan' => $tujuan,
            'perihal' => $perihal,
            'lampiran_teks' => trim($lampiranTeks) ?: '-',
            'kategori_id' => $kategori->id,
            'ukuran_kertas' => $validated['ukuran_kertas'] ?? 'a4',
            'margin' => $validated['margin'] ?? 'standar',
            'jumlah_penandatangan' => $jmlPenandatangan,
            'penandatangans' => $penandatangansClean,
            'isi' => $isi,
            'nama_penandatangan' => $namaPenandatangan,
            'jabatan_penandatangan' => $jabatanPenandatangan,
            'file' => $filePath,
            'file_surat_asli' => $fileSuratAsliPath,
            'status' => $statusAwal,
            'metode_ttd' => $metodeTtdFinal,
            'status_ttd' => $statusTtdFinal,
            'kode_verifikasi' => $kodeVerifikasi,
            'user_id' => auth()->id(),
        ]);

        foreach ($penandatangansData as $pData) {
            $hasSig = !empty($pData['data_ttd']);
            TandaTangan::create([
                'surat_keluar_id' => $suratKeluar->id,
                'urutan' => $pData['urutan'],
                'nama_penandatangan' => $pData['nama'],
                'jabatan_penandatangan' => $pData['jabatan'],
                'data_ttd' => $hasSig ? $pData['data_ttd'] : null,
                'waktu_ttd' => ($hasSig || $fileSuratAsliPath) ? Carbon::now('Asia/Jakarta') : null,
                'ip_address' => ($hasSig || $fileSuratAsliPath) ? $request->ip() : null,
            ]);
        }

        // Jika diunggah dari Word (.docx), simpan asli Word + sisipkan QR & Kode Verifikasi tanpa mengubah format/tampilan Word
        if ($fileSuratAsliPath) {
            try {
                $absPath = storage_path('app/public/' . $fileSuratAsliPath);
                $hasilWordQr = $this->docxParserService->processOriginalWordWithQr($suratKeluar, $absPath);
                $suratKeluar->file_surat_asli = $hasilWordQr['file_word_qr'] ?? $fileSuratAsliPath;
                $suratKeluar->file_final = $hasilWordQr['file_tampilan'] ?? $suratKeluar->file_surat_asli;
                $suratKeluar->save();
            } catch (\Throwable $e) {
                \Log::warning('Gagal menyisipkan QR ke Word: ' . $e->getMessage(), ['exception' => $e]);
                $suratKeluar->file_surat_asli = $fileSuratAsliPath;
                $suratKeluar->file_final = $fileSuratAsliPath;
                $suratKeluar->save();
            }

            return redirect()->route('admin.surat-keluar.index')
                ->with('success', "Dokumen surat keluar ({$noSurat} - {$perihal}) berhasil disimpan.");
        }

        // Jika dibuat manual (tanpa upload Word) dan sudah final / terkirim
        if (in_array($suratKeluar->status, ['final', 'terkirim'], true) && $anyDirectSign) {
            $suratKeluar->load('tandaTangans');
            $pdf = $this->tandaTanganService->renderPdf($suratKeluar);
            $fileName = 'surat-keluar/final-' . $suratKeluar->id . '-' . time() . '.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());
            $suratKeluar->file_final = $fileName;
            $suratKeluar->save();
        }

        $redirect = redirect()->route('admin.surat-keluar.show', $suratKeluar)
            ->with('success', "Surat keluar berhasil dibuat dengan No. Surat {$noSurat}.");

        if ($aksiSimpan === 'cetak') {
            $redirect->with('auto_open_cetak', route('admin.surat-keluar.cetak', $suratKeluar));
        }

        return $redirect;
    }

    /**
     * Detail surat keluar beserta kontrol tanda tangan dan cetak.
     */
    public function show(SuratKeluar $suratKeluar): View
    {
        $this->docxParserService->ensureWordQrProcessed($suratKeluar);
        $suratKeluar->refresh();

        // Bersihkan jika sebelumnya file_final keliru menyimpan path .docx
        if (! empty($suratKeluar->file_final) && str_ends_with(strtolower($suratKeluar->file_final), '.docx')) {
            $suratKeluar->file_final = null;
            $suratKeluar->save();
        }

        $suratKeluar->loadMissing(['kategori', 'user', 'tandaTangan', 'tandaTangans']);
        $pengaturan = Pengaturan::getAllKeyValues();
        $qrCodeDataUri = $this->tandaTanganService->generateQrCode(url('/verifikasi/' . $suratKeluar->kode_verifikasi));
        // HANYA render template HTML bawaan jika surat dibuat murni dari form web tanpa berkas asli Word
        $pratinjauSuratHtml = empty($suratKeluar->file_surat_asli)
            ? $this->tandaTanganService->renderHtml($suratKeluar)
            : null;

        return view('surat-keluar.show', compact('suratKeluar', 'pengaturan', 'qrCodeDataUri', 'pratinjauSuratHtml'));
    }

    /**
     * Form edit surat keluar.
     */
    public function edit(SuratKeluar $suratKeluar): View|RedirectResponse
    {
        if ($suratKeluar->isFinal()) {
            return redirect()->route('admin.surat-keluar.show', $suratKeluar)
                ->with('error', 'Surat yang sudah berstatus final terkunci dan tidak dapat diubah isinya.');
        }

        if (TemplateSurat::count() === 0) {
            (new TemplateSuratSeeder)->run();
        }

        $suratKeluar->loadMissing(['kategori', 'tandaTangans']);
        $kategoriList = KategoriSurat::orderBy('nama')->get();
        $templateList = TemplateSurat::where('is_aktif', true)->orderBy('id')->get();
        $pengurusList = Pengurus::where('is_aktif', true)->orderBy('nama')->get();
        $pengaturan = Pengaturan::getAllKeyValues();

        return view('surat-keluar.edit', compact('suratKeluar', 'kategoriList', 'templateList', 'pengurusList', 'pengaturan'));
    }

    /**
     * Perbarui data surat keluar jika belum final.
     */
    public function update(SuratKeluarRequest $request, SuratKeluar $suratKeluar): RedirectResponse
    {
        if ($suratKeluar->isFinal()) {
            return redirect()->route('admin.surat-keluar.show', $suratKeluar)
                ->with('error', 'Surat yang sudah berstatus final terkunci dan tidak dapat diubah isinya.');
        }

        $validated = $request->validated();

        if ($request->hasFile('file')) {
            if ($suratKeluar->file && Storage::disk('public')->exists($suratKeluar->file)) {
                Storage::disk('public')->delete($suratKeluar->file);
            }
            $validated['file'] = $request->file('file')->store('surat-keluar', 'public');
        }

        if (!empty($validated['kode_verifikasi'])) {
            $validated['kode_verifikasi'] = strtoupper(trim($validated['kode_verifikasi']));
        } else {
            unset($validated['kode_verifikasi']);
        }

        if (!empty($validated['kode_jenis_surat'])) {
            $kodeJenis = strtoupper(trim($validated['kode_jenis_surat']));
            $kategori = KategoriSurat::firstOrCreate(
                ['kode' => $kodeJenis],
                ['nama' => $kodeJenis, 'keterangan' => 'Jenis surat ' . $kodeJenis]
            );
            $validated['kategori_id'] = $kategori->id;
        }

        $validated['tempat_surat'] = !empty($validated['tempat_surat']) ? trim($validated['tempat_surat']) : ($suratKeluar->tempat_surat ?: 'Putak');
        $validated['lampiran_teks'] = !empty($validated['lampiran_teks']) ? trim($validated['lampiran_teks']) : ($suratKeluar->lampiran_teks ?: '-');

        $jmlPenandatangan = max(1, min(3, (int) ($validated['jumlah_penandatangan'] ?? 1)));
        $validated['jumlah_penandatangan'] = $jmlPenandatangan;

        $existingTtds = $suratKeluar->tandaTangans()->get()->keyBy('urutan');
        $ttd1Data = !empty($validated['data_ttd_langsung']) ? $validated['data_ttd_langsung'] : ($existingTtds[1]->data_ttd ?? null);
        $ttd2Data = !empty($validated['data_ttd_2']) ? $validated['data_ttd_2'] : ($existingTtds[2]->data_ttd ?? null);
        $ttd3Data = !empty($validated['data_ttd_3']) ? $validated['data_ttd_3'] : ($existingTtds[3]->data_ttd ?? null);

        $penandatangansData = [];
        $penandatangansData[] = [
            'urutan' => 1,
            'nama' => $validated['nama_penandatangan'],
            'jabatan' => $validated['jabatan_penandatangan'],
            'metode' => $ttd1Data ? 'digital' : ($request->input('penandatangan_1_metode', 'nanti')),
            'status' => $ttd1Data ? 'sudah' : 'belum',
        ];

        if ($jmlPenandatangan >= 2) {
            $penandatangansData[] = [
                'urutan' => 2,
                'nama' => $request->input('penandatangan_2_nama') ?: 'Penandatangan 2',
                'jabatan' => $request->input('penandatangan_2_jabatan', '-'),
                'metode' => $ttd2Data ? 'digital' : ($request->input('penandatangan_2_metode', 'nanti')),
                'status' => $ttd2Data ? 'sudah' : 'belum',
            ];
        }

        if ($jmlPenandatangan >= 3) {
            $penandatangansData[] = [
                'urutan' => 3,
                'nama' => $request->input('penandatangan_3_nama') ?: 'Penandatangan 3',
                'jabatan' => $request->input('penandatangan_3_jabatan', '-'),
                'metode' => $ttd3Data ? 'digital' : ($request->input('penandatangan_3_metode', 'nanti')),
                'status' => $ttd3Data ? 'sudah' : 'belum',
            ];
        }
        $validated['penandatangans'] = $penandatangansData;

        // Tangani tanda tangan langsung untuk urutan 1, 2, 3
        $sigInputs = [
            1 => ['sig' => $validated['data_ttd_langsung'] ?? null, 'nama' => $validated['nama_penandatangan'], 'jabatan' => $validated['jabatan_penandatangan']],
            2 => ['sig' => $validated['data_ttd_2'] ?? null, 'nama' => $request->input('penandatangan_2_nama') ?: 'Penandatangan 2', 'jabatan' => $request->input('penandatangan_2_jabatan', '-')],
            3 => ['sig' => $validated['data_ttd_3'] ?? null, 'nama' => $request->input('penandatangan_3_nama') ?: 'Penandatangan 3', 'jabatan' => $request->input('penandatangan_3_jabatan', '-')],
        ];

        foreach ($sigInputs as $urutan => $sInfo) {
            if ($urutan > $jmlPenandatangan) {
                continue;
            }
            if (!empty($sInfo['sig'])) {
                TandaTangan::updateOrCreate(
                    ['surat_keluar_id' => $suratKeluar->id, 'urutan' => $urutan],
                    [
                        'nama_penandatangan' => $sInfo['nama'],
                        'jabatan_penandatangan' => $sInfo['jabatan'],
                        'data_ttd' => $sInfo['sig'],
                        'waktu_ttd' => Carbon::now('Asia/Jakarta'),
                        'ip_address' => $request->ip(),
                    ]
                );
                $validated['metode_ttd'] = 'digital';
            } else {
                TandaTangan::updateOrCreate(
                    ['surat_keluar_id' => $suratKeluar->id, 'urutan' => $urutan],
                    [
                        'nama_penandatangan' => $sInfo['nama'],
                        'jabatan_penandatangan' => $sInfo['jabatan'],
                    ]
                );
            }
        }

        $signedTotal = TandaTangan::where('surat_keluar_id', $suratKeluar->id)
            ->where('urutan', '<=', $jmlPenandatangan)
            ->whereNotNull('data_ttd')
            ->count();

        if ($signedTotal >= $jmlPenandatangan) {
            $validated['status_ttd'] = 'sudah';
        }

        $aksiSimpan = $request->input('aksi_simpan');
        if ($aksiSimpan === 'siap_kirim') {
            $validated['status'] = 'terkirim';
        }

        $suratKeluar->update($validated);

        $redirect = redirect()->route('admin.surat-keluar.show', $suratKeluar)
            ->with('success', 'Data surat keluar berhasil diperbarui.');

        if ($aksiSimpan === 'cetak') {
            $redirect->with('auto_open_cetak', route('admin.surat-keluar.cetak', $suratKeluar));
        }

        return $redirect;
    }

    /**
     * Buka / Unduh dokumen surat keluar dalam format PDF atau Word (.docx) ber-QR Code & Kode Verifikasi.
     */
    public function cetak(Request $request, SuratKeluar $suratKeluar): Response
    {
        $this->docxParserService->ensureWordQrProcessed($suratKeluar);
        $suratKeluar->refresh();
        $slugNo = Str::slug(str_replace('/', '-', $suratKeluar->no_surat)) ?: ('surat-' . $suratKeluar->id);
        $isDownload = $request->boolean('download') || $request->query('download') == '1';

        // 1. FORMAT WORD (.docx) BER-QR CODE DAN KODE VERIFIKASI
        if ($request->query('format') === 'docx') {
            $docxPath = $suratKeluar->file_surat_asli;
            if (empty($docxPath) || ! Storage::disk('public')->exists($docxPath)) {
                $docxPath = $this->docxParserService->generateDocxFromSuratKeluar($suratKeluar);
            }

            if (! empty($docxPath) && Storage::disk('public')->exists($docxPath)) {
                $content = Storage::disk('public')->get($docxPath);
                return response($content, 200, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'Content-Disposition' => 'attachment; filename="surat-' . $slugNo . '.docx"',
                ]);
            }
        }

        // 2. FORMAT PDF RESMI BER-QR CODE DAN KODE VERIFIKASI
        // Jika berkas asli diunggah dari Word, utamakan PDF hasil konversi langsung dari dokumen Word aslinya
        $pdfBinary = null;

        if (! empty($suratKeluar->file_final) && str_ends_with(strtolower($suratKeluar->file_final), '.pdf') && Storage::disk('public')->exists($suratKeluar->file_final)) {
            $pdfBinary = Storage::disk('public')->get($suratKeluar->file_final);
        } elseif (! empty($suratKeluar->file_surat_asli) && Storage::disk('public')->exists($suratKeluar->file_surat_asli)) {
            $matchingPdfs = glob(storage_path('app/public/surat-keluar/asli-pdf-' . $suratKeluar->id . '-*.pdf')) ?: [];
            if (! empty($matchingPdfs) && is_file($matchingPdfs[0])) {
                $pdfBinary = file_get_contents($matchingPdfs[0]);
            } else {
                $absDocx = storage_path('app/public/' . $suratKeluar->file_surat_asli);
                $pdfBinary = $this->docxParserService->convertDocxToPdf($absDocx);
                if ($pdfBinary) {
                    $savedPdfRel = 'surat-keluar/asli-pdf-' . $suratKeluar->id . '-' . Str::random(4) . '.pdf';
                    Storage::disk('public')->put($savedPdfRel, $pdfBinary);
                }
            }
        }

        if ($pdfBinary) {
            $disposition = $isDownload ? 'attachment' : 'inline';
            return response($pdfBinary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => $disposition . '; filename="surat-' . $slugNo . '.pdf"',
            ]);
        }

        // Jika surat berasal dari dokumen Word asli tetapi server cPanel belum memiliki LibreOffice,
        // buka tampilan cetak dokumen Word asli (BUKAN membuat ulang dari template HTML bawaan)
        if (! empty($suratKeluar->file_surat_asli) && Storage::disk('public')->exists($suratKeluar->file_surat_asli)) {
            return response()->view('surat-keluar.docx-print', compact('suratKeluar'));
        }

        // Fallback hanya untuk surat yang dibuat lewat formulir teks biasa (tanpa unggah Word):
        $pdf = $this->tandaTanganService->renderPdf($suratKeluar);

        if ($isDownload || $request->query('format') === 'pdf') {
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="surat-' . $slugNo . '.pdf"',
            ]);
        }

        // Pratinjau / cetak langsung di peramban
        return $pdf->stream('surat-' . $slugNo . '.pdf');
    }

    /**
     * Buat tautan sekali pakai tanda tangan jarak jauh (berlaku 30 menit).
     */
    public function buatTautanTtd(Request $request, SuratKeluar $suratKeluar): RedirectResponse
    {
        $urutan = max(1, min(3, (int) $request->input('urutan', 1)));

        $tandaTangan = $this->tandaTanganService->createTautanSekaliPakai($suratKeluar, $urutan);
        $tautanUrl = url('/tanda-tangan/' . $tandaTangan->token_sekali_pakai);

        return redirect()->route('admin.surat-keluar.show', $suratKeluar)
            ->with('tautan_ttd', $tautanUrl)
            ->with('tautan_urutan', $urutan)
            ->with('success', "Tautan tanda tangan sekali pakai untuk Penandatangan ke-{$urutan} berhasil dibuat (berlaku 30 menit).");
    }

    /**
     * Finalisasi tanda tangan digital langsung di web.
     */
    public function finalisasiDigital(Request $request, SuratKeluar $suratKeluar): RedirectResponse
    {
        $request->validate([
            'data_ttd' => ['required', 'string', 'regex:/^data:image\/(png|jpeg|jpg);base64,/'],
            'urutan' => ['nullable', 'integer', 'between:1,3'],
        ], [
            'data_ttd.required' => 'Goresan tanda tangan pada kanvas wajib dibuat.',
            'data_ttd.regex' => 'Format gambar tanda tangan kanvas tidak valid.',
        ]);

        $urutan = (int) ($request->urutan ?? 1);
        $tandaTangan = TandaTangan::firstOrNew([
            'surat_keluar_id' => $suratKeluar->id,
            'urutan' => $urutan,
        ]);

        $suratKeluar = $this->tandaTanganService->prosesTtdDigital(
            $suratKeluar,
            $request->data_ttd,
            $request->ip(),
            $tandaTangan
        );

        $pesan = $suratKeluar->isFinal()
            ? 'Seluruh penandatangan telah lengkap. Dokumen resmi telah berhasil difinalisasi.'
            : "Penandatangan ke-{$urutan} berhasil membubuhkan tanda tangan. Dokumen masih menunggu tanda tangan penandatangan lainnya.";

        return redirect()->route('admin.surat-keluar.show', $suratKeluar)
            ->with('success', $pesan);
    }

    /**
     * Finalisasi tanda tangan basah (manual / pindaian).
     */
    public function finalisasiBasah(Request $request, SuratKeluar $suratKeluar): RedirectResponse
    {
        $request->validate([
            'file_final' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ], [
            'file_final.mimes' => 'Berkas pindaian harus berformat PDF, JPG, atau PNG.',
            'file_final.max' => 'Ukuran berkas maksimal 5 MB.',
        ]);

        $filePath = null;
        if ($request->hasFile('file_final')) {
            $filePath = $request->file('file_final')->store('surat-keluar-final', 'public');
        }

        $this->tandaTanganService->prosesTtdBasah($suratKeluar, $filePath);

        return redirect()->route('admin.surat-keluar.show', $suratKeluar)
            ->with('success', 'Surat berhasil difinalisasi dengan metode tanda tangan basah.');
    }

    /**
     * Perbarui status surat keluar (misal: terkirim).
     */
    public function updateStatus(Request $request, SuratKeluar $suratKeluar): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:draft,final,terkirim'],
        ]);

        $suratKeluar->update(['status' => $request->status]);

        return redirect()->route('admin.surat-keluar.show', $suratKeluar)
            ->with('success', 'Status surat berhasil diperbarui.');
    }

    /**
     * Hapus surat keluar (soft delete).
     */
    public function destroy(SuratKeluar $suratKeluar): RedirectResponse
    {
        $suratKeluar->delete();

        return redirect()->route('admin.surat-keluar.index')
            ->with('success', 'Surat keluar berhasil dihapus.');
    }

    /**
     * Simpan template surat baru (dibuat dari web atau hasil unggah file .docx/.html/.txt).
     */
    public function storeTemplate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode_jenis_surat' => ['nullable', 'string', 'max:50'],
            'tujuan_default' => ['nullable', 'string', 'max:255'],
            'perihal_default' => ['nullable', 'string', 'max:255'],
            'isi_template' => ['nullable', 'string'],
            'file_template' => ['nullable', 'file', 'mimes:docx,html,htm,txt', 'max:5120'],
        ], [
            'nama.required' => 'Nama template surat wajib diisi.',
            'file_template.mimes' => 'Berkas template harus berformat DOCX, HTML, atau TXT.',
            'file_template.max' => 'Ukuran berkas template maksimal 5 MB.',
        ]);

        $filePath = null;
        $isiTemplate = $validated['isi_template'] ?? '';

        if ($request->hasFile('file_template')) {
            $uploaded = $request->file('file_template');
            $filePath = $uploaded->store('template-surat', 'public');
            $extracted = $this->extractContentFromFile($uploaded);
            if ($extracted !== '') {
                $isiTemplate = $extracted;
            }
        }

        if (trim(strip_tags($isiTemplate)) === '') {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Isi template atau berkas template tidak boleh kosong.');
        }

        $kodeJenis = !empty($validated['kode_jenis_surat'])
            ? strtoupper(trim($validated['kode_jenis_surat']))
            : 'SPm.01';

        $kategori = KategoriSurat::firstOrCreate(
            ['kode' => $kodeJenis],
            ['nama' => $validated['nama'], 'keterangan' => 'Jenis surat ' . $kodeJenis]
        );

        $template = TemplateSurat::create([
            'nama' => $validated['nama'],
            'kategori_id' => $kategori->id,
            'kode_jenis_surat' => $kodeJenis,
            'tujuan_default' => $validated['tujuan_default'] ?? null,
            'perihal_default' => $validated['perihal_default'] ?? null,
            'isi_template' => $this->sanitizeTemplateHtml($isiTemplate),
            'file_template' => $filePath,
            'is_aktif' => true,
        ]);

        return redirect()->back()
            ->with('selected_template_id', $template->id)
            ->with('success', "Template surat \"{$template->nama}\" berhasil disimpan dan siap digunakan.");
    }

    /**
     * Hapus template surat keluar.
     */
    public function destroyTemplate(TemplateSurat $templateSurat): RedirectResponse
    {
        if ($templateSurat->file_template && Storage::disk('public')->exists($templateSurat->file_template)) {
            Storage::disk('public')->delete($templateSurat->file_template);
        }

        $nama = $templateSurat->nama;
        $templateSurat->delete();

        return redirect()->back()
            ->with('success', "Template surat \"{$nama}\" berhasil dihapus.");
    }

    /**
     * Ekstrak isi naskah dari berkas template (.docx, .html, .txt).
     */
    protected function extractContentFromFile(\Illuminate\Http\UploadedFile $file): string
    {
        $ext = strtolower($file->getClientOriginalExtension());
        $realPath = $file->getRealPath();

        if ($ext === 'docx' && class_exists(\ZipArchive::class)) {
            $zip = new \ZipArchive;
            if ($zip->open($realPath) === true) {
                $xml = $zip->getFromName('word/document.xml');
                $zip->close();
                if ($xml !== false) {
                    $dom = new \DOMDocument;
                    @$dom->loadXML($xml);
                    $paragraphs = $dom->getElementsByTagName('p');
                    $htmlParts = [];
                    foreach ($paragraphs as $p) {
                        $text = trim($p->textContent);
                        if ($text !== '') {
                            $htmlParts[] = '<p style="text-indent: 28px;">' . e($text) . '</p>';
                        }
                    }

                    return implode("\n", $htmlParts);
                }
            }
        }

        $raw = @file_get_contents($realPath) ?: '';
        if ($ext === 'txt') {
            $lines = preg_split('/\r\n|\r|\n/', trim($raw));
            $htmlParts = [];
            foreach ($lines as $line) {
                if (trim($line) !== '') {
                    $htmlParts[] = '<p style="text-indent: 28px;">' . e(trim($line)) . '</p>';
                }
            }

            return implode("\n", $htmlParts);
        }

        // Untuk .html / .htm, ambil isi di dalam <body> bila ada
        if (preg_match('/<body[^>]*>(.*?)<\/body>/is', $raw, $m)) {
            $raw = $m[1];
        }

        return $this->sanitizeTemplateHtml($raw);
    }

    /**
     * Sanitasi HTML template surat agar aman dari XSS.
     */
    protected function sanitizeTemplateHtml(string $html): string
    {
        $clean = preg_replace('/<(script|iframe|object|embed|form|style|meta|link)\b[^>]*>.*?<\/\1>/is', '', $html);
        $clean = preg_replace('/<(script|iframe|object|embed|form|meta|link)\b[^>]*\/?>/is', '', $clean);
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*(["\']).*?\1/is', '', $clean);
        $clean = preg_replace('/\s+on[a-z]+\s*=\s*[^\s>]+/is', '', $clean);
        $clean = preg_replace('/javascript\s*:/i', '', $clean);

        return trim($clean);
    }
}
