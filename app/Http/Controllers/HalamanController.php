<?php

namespace App\Http\Controllers;

use App\Models\Halaman;
use App\Models\HalamanRevisi;
use App\Services\HalamanBlockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HalamanController extends Controller
{
    protected HalamanBlockService $blockService;

    public function __construct(HalamanBlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Tampilkan editor visual beranda.
     */
    public function editBeranda()
    {
        $halaman = Halaman::firstOrCreate(
            ['is_beranda' => true],
            [
                'slug' => 'beranda',
                'judul' => 'Beranda Utama',
                'konten' => $this->blockService->getDefaultBerandaBlocks(),
                'status' => 'terbit',
                'published_at' => Carbon::now(),
                'user_id' => Auth::id(),
            ]
        );

        // Jika konten masih kosong, isi dengan default
        if (empty($halaman->konten)) {
            $halaman->konten = $this->blockService->getDefaultBerandaBlocks();
            $halaman->save();
        }

        $revisis = $halaman->revisis()->with('user')->take(10)->get();

        return view('halaman.editor', compact('halaman', 'revisis'));
    }

    /**
     * Simpan perubahan blok beranda (Draft atau Terbitkan).
     */
    public function updateBeranda(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'status' => 'required|in:draft,terbit',
            'blocks' => 'required|array',
            'catatan_revisi' => 'nullable|string|max:255',
        ], [
            'judul.required' => 'Judul halaman wajib diisi.',
            'status.required' => 'Status halaman wajib dipilih.',
            'blocks.required' => 'Blok konten tidak boleh kosong.',
        ]);

        $halaman = Halaman::firstOrCreate(['is_beranda' => true], [
            'slug' => 'beranda',
            'judul' => 'Beranda Utama',
            'user_id' => Auth::id(),
        ]);

        // Sanitasi blok konten
        $sanitizedBlocks = $this->blockService->sanitizeBlocks($request->input('blocks', []));

        // Buat revisi baru sebelum menyimpan jika konten berubah
        if (!empty($halaman->konten)) {
            HalamanRevisi::create([
                'halaman_id' => $halaman->id,
                'konten' => $halaman->konten,
                'catatan' => $request->input('catatan_revisi') ?: ($request->status === 'terbit' ? 'Publikasi Beranda' : 'Simpan Draf'),
                'user_id' => Auth::id(),
            ]);
        }

        $halaman->judul = $this->blockService->sanitizeString($request->judul);
        $halaman->konten = $sanitizedBlocks;
        $halaman->status = $request->status;

        if ($request->status === 'terbit') {
            $halaman->published_at = Carbon::now();
        }

        $halaman->user_id = Auth::id();
        $halaman->save();

        $pesan = $request->status === 'terbit'
            ? 'Beranda berhasil diterbitkan dan langsung tampil di halaman depan.'
            : 'Draf beranda berhasil disimpan.';

        return redirect()->route('admin.halaman.beranda')->with('success', $pesan);
    }

    /**
     * Pulihkan konten beranda dari riwayat revisi.
     */
    public function restoreRevisi(Halaman $halaman, HalamanRevisi $revisi)
    {
        if ($revisi->halaman_id !== $halaman->id) {
            return redirect()->back()->with('error', 'Revisi tidak valid untuk halaman ini.');
        }

        // Simpan versi saat ini sebagai revisi cadangan
        HalamanRevisi::create([
            'halaman_id' => $halaman->id,
            'konten' => $halaman->konten,
            'catatan' => 'Cadangan otomatis sebelum dipulihkan ke revisi #' . $revisi->id,
            'user_id' => Auth::id(),
        ]);

        $halaman->konten = $revisi->konten;
        $halaman->user_id = Auth::id();
        $halaman->save();

        return redirect()->route('admin.halaman.beranda')
            ->with('success', 'Konten beranda berhasil dipulihkan dari revisi ' . $revisi->created_at->format('d-m-Y H:i') . '.');
    }
}
