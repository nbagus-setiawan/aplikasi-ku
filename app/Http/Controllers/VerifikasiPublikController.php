<?php

namespace App\Http\Controllers;

use App\Models\Pengaturan;
use App\Models\SuratKeluar;
use App\Services\DocxSuratParserService;
use App\Services\TandaTanganService;
use Illuminate\Http\Request;
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
}

