<?php

namespace App\Http\Controllers;

use App\Models\Halaman;
use App\Models\Pengaturan;
use App\Models\Pengumuman;
use App\Services\HalamanBlockService;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    protected HalamanBlockService $blockService;

    public function __construct(HalamanBlockService $blockService)
    {
        $this->blockService = $blockService;
    }

    /**
     * Tampilkan Beranda Publik (dinamis sesuai blok yang disusun sekretaris).
     */
    public function beranda()
    {
        $halaman = Halaman::beranda()->terbit()->first();

        $blocks = $halaman && !empty($halaman->konten)
            ? $halaman->konten
            : $this->blockService->getDefaultBerandaBlocks();

        // Siapkan data pengumuman terbaru jika ada blok pengumuman
        $pengumumanTerbaru = Pengumuman::terbitPublik()->take(3)->get();

        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        // Hitung data riil statistik pesantren langsung dari database
        $santriAktifCount = \App\Models\Santri::where('status', 'aktif')->count();
        $pengurusCount = \App\Models\Pengurus::where('is_aktif', true)->count();
        $kamarCount = \App\Models\Santri::whereNotNull('kamar')->where('kamar', '!=', '')->distinct('kamar')->count('kamar');
        $alumniCount = \App\Models\Santri::where('status', 'lulus')->distinct('tahun_masuk')->count('tahun_masuk');

        $statistikOtomatis = [
            'santri_aktif' => number_format($santriAktifCount, 0, ',', '.'),
            'pengurus_asatidz' => number_format($pengurusCount, 0, ',', '.'),
            'asrama_kamar' => number_format($kamarCount, 0, ',', '.'),
            'angkatan_alumni' => number_format($alumniCount, 0, ',', '.'),
        ];

        return view('public.beranda', compact('halaman', 'blocks', 'pengumumanTerbaru', 'pengaturan', 'statistikOtomatis'));
    }

    /**
     * Tampilkan Daftar Pengumuman Publik.
     */
    public function pengumumanIndex(Request $request)
    {
        $query = Pengumuman::terbitPublik();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('judul', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $pengumumans = $query->paginate(9)->withQueryString();
        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        return view('public.pengumuman-index', compact('pengumumans', 'pengaturan'));
    }

    /**
     * Tampilkan Detail Pengumuman Publik.
     */
    public function pengumumanShow(string $slug)
    {
        $pengumuman = Pengumuman::terbitPublik()->where('slug', $slug)->firstOrFail();
        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        // Pengumuman terkait lainnya
        $terkait = Pengumuman::terbitPublik()
            ->where('id', '!=', $pengumuman->id)
            ->take(4)
            ->get();

        return view('public.pengumuman-show', compact('pengumuman', 'terkait', 'pengaturan'));
    }
}
