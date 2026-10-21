<?php

namespace App\Http\Controllers;

use App\Models\TandaTangan;
use App\Services\TandaTanganService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TandaTanganPublikController extends Controller
{
    public function __construct(
        protected TandaTanganService $tandaTanganService
    ) {}

    /**
     * Tampilkan halaman penandatanganan jarak jauh lewat tautan sekali pakai.
     */
    public function show(string $token): View
    {
        $tandaTangan = TandaTangan::where('token_sekali_pakai', $token)->first();

        if (! $tandaTangan) {
            return view('public.tanda-tangan', [
                'status' => 'invalid',
                'pesan' => 'Tautan tanda tangan tidak ditemukan atau sudah pernah digunakan.',
            ]);
        }

        if ($tandaTangan->isExpired()) {
            return view('public.tanda-tangan', [
                'status' => 'expired',
                'pesan' => 'Tautan tanda tangan telah kedaluwarsa (berlaku 30 menit). Silakan hubungi sekretaris untuk meminta tautan baru.',
            ]);
        }

        $suratKeluar = $tandaTangan->suratKeluar;
        $suratKeluar->loadMissing(['kategori', 'tandaTangans']);
        $tandaTangans = $suratKeluar->tandaTangans->keyBy('urutan');

        $lembarData = $this->tandaTanganService->dataLembar($suratKeluar, true, $tandaTangans);

        return view('public.tanda-tangan', array_merge($lembarData, [
            'status' => 'active',
            'tandaTangan' => $tandaTangan,
            'token' => $token,
        ]));
    }

    /**
     * Proses pengiriman tanda tangan dari tautan sekali pakai.
     */
    public function sign(Request $request, string $token): View|RedirectResponse
    {
        $tandaTangan = TandaTangan::where('token_sekali_pakai', $token)->first();

        if (! $tandaTangan || $tandaTangan->isExpired()) {
            return view('public.tanda-tangan', [
                'status' => 'expired',
                'pesan' => 'Tautan tanda tangan telah kedaluwarsa atau tidak valid.',
            ]);
        }

        $request->validate([
            'data_ttd' => ['required', 'string'],
        ], [
            'data_ttd.required' => 'Goresan tanda tangan pada kanvas wajib dibuat.',
        ]);

        $suratKeluar = $tandaTangan->suratKeluar;

        $suratKeluar = $this->tandaTanganService->prosesTtdDigital(
            $suratKeluar,
            $request->data_ttd,
            $request->ip(),
            $tandaTangan
        );

        $pesanSukses = $suratKeluar->isFinal()
            ? 'Alhamdulillah, seluruh penandatangan telah lengkap dan dokumen resmi telah sah.'
            : 'Alhamdulillah, tanda tangan Anda telah berhasil direkam. Dokumen masih menunggu tanda tangan penandatangan lainnya.';

        return view('public.tanda-tangan', [
            'status' => 'success',
            'pesan' => $pesanSukses,
            'surat' => $suratKeluar,
        ]);
    }
}
