<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\SuratKeluar;
use App\Services\DocxSuratParserService;
use App\Services\TandaTanganService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VerifikasiPublikController extends Controller
{
    public function __construct(
        protected DocxSuratParserService $docxParserService,
        protected TandaTanganService $tandaTanganService
    ) {}

    /**
     * Tampilkan formulir pencarian verifikasi atau hasil verifikasi surat.
     */
    public function index(Request $request): View
    {
        $kode = trim($request->input('kode', ''));
        $surat = null;
        $qrCodeDataUri = null;

        if (!empty($kode)) {
            $surat = SuratKeluar::with(['kategori', 'tandaTangan', 'tandaTangans'])
                ->where('kode_verifikasi', $kode)
                ->orWhere('no_surat', $kode)
                ->first();

            if ($surat) {
                $this->docxParserService->ensureWordQrProcessed($surat);
                $qrCodeDataUri = $this->tandaTanganService->generateQrCode(url('/verifikasi/' . $surat->kode_verifikasi));
            }
        }

        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        return view('public.verifikasi', [
            'surat' => $surat,
            'kode' => $kode,
            'qrCodeDataUri' => $qrCodeDataUri,
            'isSearch' => true,
            'pengaturan' => $pengaturan,
        ]);
    }

    /**
     * Tampilkan hasil verifikasi keabsahan surat berdasarkan kode verifikasi QR langsung.
     */
    public function show(string $kode): View
    {
        $surat = SuratKeluar::with(['kategori', 'tandaTangan', 'tandaTangans'])
            ->where('kode_verifikasi', $kode)
            ->first();

        $qrCodeDataUri = null;
        if ($surat) {
            $this->docxParserService->ensureWordQrProcessed($surat);
            $qrCodeDataUri = $this->tandaTanganService->generateQrCode(url('/verifikasi/' . $surat->kode_verifikasi));
        }

        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        return view('public.verifikasi', [
            'surat' => $surat,
            'kode' => $kode,
            'qrCodeDataUri' => $qrCodeDataUri,
            'isSearch' => false,
            'pengaturan' => $pengaturan,
        ]);
    }

    /**
     * Unduh berkas dokumen surat resmi yang telah diverifikasi (format Word .docx ber-QR atau PDF).
     */
    public function unduh(Request $request, string $kode): Response
    {
        $surat = SuratKeluar::where('kode_verifikasi', $kode)
            ->orWhere('no_surat', $kode)
            ->firstOrFail();

        $this->docxParserService->ensureWordQrProcessed($surat);
        $surat->refresh();

        $format = $request->query('format', 'docx');
        $slugNo = Str::slug(str_replace('/', '-', $surat->no_surat)) ?: ('surat-' . $surat->id);

        if ($format === 'docx') {
            $docxPath = $surat->file_surat_asli;
            if (empty($docxPath) || ! Storage::disk('public')->exists($docxPath)) {
                $docxPath = $this->docxParserService->generateDocxFromSuratKeluar($surat);
            }
            if (! empty($docxPath) && Storage::disk('public')->exists($docxPath)) {
                $content = Storage::disk('public')->get($docxPath);
                return response($content, 200, [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'Content-Disposition' => 'attachment; filename="surat-' . $slugNo . '.docx"',
                ]);
            }
        }

        // Format PDF
        if (! empty($surat->file_final) && str_ends_with(strtolower($surat->file_final), '.pdf') && Storage::disk('public')->exists($surat->file_final)) {
            $pdfBinary = Storage::disk('public')->get($surat->file_final);
            return response($pdfBinary, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="surat-' . $slugNo . '.pdf"',
            ]);
        }

        if (! empty($surat->file_surat_asli) && Storage::disk('public')->exists($surat->file_surat_asli)) {
            $matchingPdfs = glob(storage_path('app/public/surat-keluar/asli-pdf-' . $surat->id . '-*.pdf')) ?: [];
            if (! empty($matchingPdfs) && is_file($matchingPdfs[0])) {
                $pdfBinary = file_get_contents($matchingPdfs[0]);
            } else {
                $absDocx = storage_path('app/public/' . $surat->file_surat_asli);
                $pdfBinary = $this->docxParserService->convertDocxToPdf($absDocx);
            }
            if ($pdfBinary) {
                return response($pdfBinary, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="surat-' . $slugNo . '.pdf"',
                ]);
            }
        }

        $pdf = $this->tandaTanganService->renderPdf($surat);
        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="surat-' . $slugNo . '.pdf"',
        ]);
    }
}

