<?php

namespace App\Http\Controllers;

use App\Http\Requests\SuratMasukRequest;
use App\Models\KategoriSurat;
use App\Models\SuratMasuk;
use App\Services\DocxSuratParserService;
use App\Services\NomorSuratService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SuratMasukController extends Controller
{
    public function __construct(
        protected NomorSuratService $nomorSuratService,
        protected DocxSuratParserService $docxParserService
    ) {}

    /**
     * Baca dan deteksi otomatis isi file surat masuk (.docx/.doc/.pdf/.jpg/.png) yang diunggah (AJAX).
     */
    public function parseDocx(Request $request): JsonResponse
    {
        $request->validate([
            'file_surat' => ['required', 'file', 'mimes:docx,doc,pdf,jpg,jpeg,png,txt,html,htm', 'max:5120'],
        ], [
            'file_surat.required' => 'Silakan pilih file surat masuk terlebih dahulu.',
            'file_surat.mimes' => 'File surat harus berformat Word (.docx/.doc), PDF, atau Gambar (JPG/PNG).',
        ]);

        $parsed = $this->docxParserService->parseUploadedDocument($request->file('file_surat'));
        $tahun = date('Y');
        $noAgendaBerikutnya = str_pad((string) (SuratMasuk::whereYear('tgl_diterima', $tahun)->withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);

        return response()->json([
            'success' => true,
            'data' => array_merge($parsed, [
                'tgl_diterima' => date('Y-m-d'),
                'no_agenda_berikutnya' => $noAgendaBerikutnya,
            ]),
        ]);
    }

    /**
     * Tampilkan daftar surat masuk dengan pencarian dan filter.
     */
    public function index(Request $request): View
    {
        $query = SuratMasuk::with(['kategori', 'user'])->latest('id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_agenda', 'like', "%{$q}%")
                    ->orWhere('no_surat', 'like', "%{$q}%")
                    ->orWhere('pengirim', 'like', "%{$q}%")
                    ->orWhere('perihal', 'like', "%{$q}%")
                    ->orWhere('lampiran_teks', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status_disposisi')) {
            $query->where('status_disposisi', $request->status_disposisi);
        }

        if ($request->filled('tgl_mulai')) {
            $query->whereDate('tgl_diterima', '>=', $request->tgl_mulai);
        }

        if ($request->filled('tgl_selesai')) {
            $query->whereDate('tgl_diterima', '<=', $request->tgl_selesai);
        }

        $suratMasuks = $query->paginate(15)->withQueryString();
        $kategoriList = KategoriSurat::orderBy('nama')->get();

        $statistik = [
            'total' => SuratMasuk::count(),
            'baru' => SuratMasuk::where('status_disposisi', 'baru')->count(),
            'diproses' => SuratMasuk::where('status_disposisi', 'diproses')->count(),
            'selesai' => SuratMasuk::where('status_disposisi', 'selesai')->count(),
        ];

        return view('surat-masuk.index', compact('suratMasuks', 'kategoriList', 'statistik'));
    }

    /**
     * Unduh buku induk agenda surat masuk dalam format CSV.
     * Kolom: No, Tanggal Pembuatan Surat, Jenis Surat, Perihal, Nomor Surat, Asal Surat.
     */
    public function exportCsv(Request $request): StreamedResponse
    {
        $query = SuratMasuk::with(['kategori'])->latest('tgl_diterima')->latest('id');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('no_agenda', 'like', "%{$q}%")
                    ->orWhere('no_surat', 'like', "%{$q}%")
                    ->orWhere('pengirim', 'like', "%{$q}%")
                    ->orWhere('perihal', 'like', "%{$q}%")
                    ->orWhere('lampiran_teks', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        if ($request->filled('status_disposisi')) {
            $query->where('status_disposisi', $request->status_disposisi);
        }

        if ($request->filled('tgl_mulai')) {
            $query->whereDate('tgl_diterima', '>=', $request->tgl_mulai);
        }

        if ($request->filled('tgl_selesai')) {
            $query->whereDate('tgl_diterima', '<=', $request->tgl_selesai);
        }

        $filename = 'buku-induk-surat-masuk-' . date('Ymd-His') . '.csv';

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
                'Asal Surat',
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
                        $surat->pengirim ?? '-',
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
     * Form tambah surat masuk baru.
     */
    public function create(): View
    {
        $kategoriList = KategoriSurat::orderBy('nama')->get();
        $noAgendaBerikutnya = str_pad((string) (SuratMasuk::whereYear('tgl_diterima', date('Y'))->withTrashed()->count() + 1), 3, '0', STR_PAD_LEFT);

        return view('surat-masuk.create', compact('kategoriList', 'noAgendaBerikutnya'));
    }

    /**
     * Simpan data surat masuk baru (mendukung Upload File Word/Dokumen otomatis maupun input manual).
     */
    public function store(SuratMasukRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $fileSuratAsliPath = null;
        $fileTampilanPath = null;
        $fileLampiranPath = null;
        $parsedDocx = null;

        if ($request->hasFile('file_surat_asli')) {
            $uploadedMain = $request->file('file_surat_asli');
            $parsedDocx = $this->docxParserService->parseUploadedDocument($uploadedMain);
            $ext = strtolower($uploadedMain->getClientOriginalExtension()) ?: 'docx';
            $fileSuratAsliPath = $uploadedMain->storeAs('surat-masuk', \Illuminate\Support\Str::uuid() . '.' . $ext, 'public');

            $absPath = storage_path('app/public/' . $fileSuratAsliPath);
            $renderedPng = $this->docxParserService->renderWordToOriginalPng($absPath);
            $fileTampilanPath = $renderedPng ?: $fileSuratAsliPath;
        }

        if ($request->hasFile('file_lampiran')) {
            $fLampiran = $request->file('file_lampiran');
            $extLampiran = strtolower($fLampiran->getClientOriginalExtension()) ?: 'pdf';
            $fileLampiranPath = $fLampiran->storeAs('surat-masuk-lampiran', \Illuminate\Support\Str::uuid() . '.' . $extLampiran, 'public');
        } elseif ($request->hasFile('file')) {
            $fFile = $request->file('file');
            $extFile = strtolower($fFile->getClientOriginalExtension()) ?: 'pdf';
            $uploadedFile = $fFile->storeAs('surat-masuk', \Illuminate\Support\Str::uuid() . '.' . $extFile, 'public');
            if ($fileSuratAsliPath) {
                $fileLampiranPath = $uploadedFile;
            } else {
                $fileTampilanPath = $uploadedFile;
            }
        }

        $tglDiterima = $validated['tgl_diterima'] ?? date('Y-m-d');
        $tglSurat = $validated['tgl_surat'] ?? ($parsedDocx['tgl_surat'] ?? $tglDiterima);
        $tempatSurat = $validated['tempat_surat'] ?? ($parsedDocx['tempat_surat'] ?? 'Putak');
        $noSurat = trim((string) ($validated['no_surat'] ?? ($parsedDocx['no_surat'] ?? '')));
        if ($noSurat === '') {
            $noSurat = '-';
        }
        $pengirim = trim((string) ($validated['pengirim'] ?? ($parsedDocx['pengirim'] ?? 'Instansi Pengirim')));
        $perihal = trim((string) ($validated['perihal'] ?? ($parsedDocx['perihal'] ?? 'Surat Masuk')));
        $lampiranTeks = trim((string) ($validated['lampiran_teks'] ?? ($parsedDocx['lampiran_teks'] ?? '-')));

        $noAgenda = $this->nomorSuratService->generateNomorAgenda($tglDiterima);

        $suratMasuk = SuratMasuk::create([
            'no_agenda' => $noAgenda,
            'no_surat' => $noSurat,
            'tgl_surat' => $tglSurat,
            'tgl_diterima' => $tglDiterima,
            'tempat_surat' => $tempatSurat ?: 'Putak',
            'pengirim' => $pengirim ?: '-',
            'perihal' => $perihal ?: 'Surat Masuk',
            'lampiran_teks' => $lampiranTeks ?: '-',
            'kategori_id' => $validated['kategori_id'] ?? null,
            'file' => $fileTampilanPath,
            'file_surat_asli' => $fileSuratAsliPath ?: $fileTampilanPath,
            'file_lampiran' => $fileLampiranPath,
            'disposisi' => $validated['disposisi'] ?? null,
            'status_disposisi' => $validated['status_disposisi'] ?? 'baru',
            'user_id' => auth()->id(),
        ]);

        if ($request->input('aksi_simpan') === 'terbitkan_word') {
            return redirect()->route('admin.surat-masuk.index')
                ->with('success', "Dokumen surat masuk (#{$noAgenda} - {$perihal}) berhasil disimpan asli.");
        }

        return redirect()->route('admin.surat-masuk.show', $suratMasuk)
            ->with('success', "Surat masuk berhasil dicatat dengan No. Agenda {$noAgenda}.");
    }

    /**
     * Tampilkan detail surat masuk dan riwayat disposisi.
     */
    public function show(SuratMasuk $suratMasuk): View
    {
        $suratMasuk->loadMissing(['kategori', 'user']);

        return view('surat-masuk.show', compact('suratMasuk'));
    }

    /**
     * Form ubah data surat masuk.
     */
    public function edit(SuratMasuk $suratMasuk): View
    {
        $kategoriList = KategoriSurat::orderBy('nama')->get();

        return view('surat-masuk.edit', compact('suratMasuk', 'kategoriList'));
    }

    /**
     * Perbarui data surat masuk.
     */
    public function update(SuratMasukRequest $request, SuratMasuk $suratMasuk): RedirectResponse
    {
        $validated = $request->validated();

        $validated['tempat_surat'] = !empty($validated['tempat_surat']) ? trim($validated['tempat_surat']) : ($suratMasuk->tempat_surat ?: 'Putak');
        $validated['lampiran_teks'] = !empty($validated['lampiran_teks']) ? trim($validated['lampiran_teks']) : ($suratMasuk->lampiran_teks ?: '-');

        if ($request->hasFile('file_surat_asli')) {
            if ($suratMasuk->file_surat_asli && Storage::disk('public')->exists($suratMasuk->file_surat_asli)) {
                Storage::disk('public')->delete($suratMasuk->file_surat_asli);
            }
            $uploadedMain = $request->file('file_surat_asli');
            $ext = strtolower($uploadedMain->getClientOriginalExtension()) ?: 'docx';
            $fileSuratAsliPath = $uploadedMain->storeAs('surat-masuk', \Illuminate\Support\Str::uuid() . '.' . $ext, 'public');
            $validated['file_surat_asli'] = $fileSuratAsliPath;

            $absPath = storage_path('app/public/' . $fileSuratAsliPath);
            $renderedPng = $this->docxParserService->renderWordToOriginalPng($absPath);
            $validated['file'] = $renderedPng ?: $fileSuratAsliPath;
        } elseif ($request->hasFile('file')) {
            if ($suratMasuk->file && Storage::disk('public')->exists($suratMasuk->file)) {
                Storage::disk('public')->delete($suratMasuk->file);
            }
            $fFile = $request->file('file');
            $extFile = strtolower($fFile->getClientOriginalExtension()) ?: 'pdf';
            $validated['file'] = $fFile->storeAs('surat-masuk', \Illuminate\Support\Str::uuid() . '.' . $extFile, 'public');
        }

        if ($request->hasFile('file_lampiran')) {
            if ($suratMasuk->file_lampiran && Storage::disk('public')->exists($suratMasuk->file_lampiran)) {
                Storage::disk('public')->delete($suratMasuk->file_lampiran);
            }
            $fLampiran = $request->file('file_lampiran');
            $extLampiran = strtolower($fLampiran->getClientOriginalExtension()) ?: 'pdf';
            $validated['file_lampiran'] = $fLampiran->storeAs('surat-masuk-lampiran', \Illuminate\Support\Str::uuid() . '.' . $extLampiran, 'public');
        }

        $suratMasuk->update($validated);

        return redirect()->route('admin.surat-masuk.show', $suratMasuk)
            ->with('success', 'Data surat masuk berhasil diperbarui.');
    }

    /**
     * Perbarui disposisi surat masuk.
     */
    public function updateDisposisi(Request $request, SuratMasuk $suratMasuk): RedirectResponse
    {
        $validated = $request->validate([
            'disposisi' => ['nullable', 'string'],
            'status_disposisi' => ['required', 'in:baru,diproses,selesai'],
        ], [
            'status_disposisi.required' => 'Status disposisi wajib ditentukan.',
            'status_disposisi.in' => 'Status disposisi tidak valid.',
        ]);

        $suratMasuk->update($validated);

        return redirect()->route('admin.surat-masuk.show', $suratMasuk)
            ->with('success', 'Disposisi surat berhasil diperbarui.');
    }

    /**
     * Hapus data surat masuk (soft delete).
     */
    public function destroy(SuratMasuk $suratMasuk): RedirectResponse
    {
        $suratMasuk->delete();

        return redirect()->route('admin.surat-masuk.index')
            ->with('success', 'Surat masuk berhasil dihapus.');
    }
}
