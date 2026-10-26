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
     * Tampilkan Halaman Layanan Konsultasi Publik.
     */
    public function konsultasi()
    {
        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();
        $nomorWa = $pengaturan['kontak'] ?? '0821-1870-2717';
        $nomorWaClean = preg_replace('/[^0-9]/', '', $nomorWa);
        if (str_starts_with($nomorWaClean, '0')) {
            $nomorWaClean = '62' . substr($nomorWaClean, 1);
        }

        return view('public.konsultasi', compact('pengaturan', 'nomorWa', 'nomorWaClean'));
    }

    /**
     * Tampilkan Daftar Pengumuman Publik.
     */
    public function pengumumanIndex(Request $request)
    {
        // Alihkan string template literal {search_term_string} dari Google Schema ke URL kanonikal bersih
        $rawQ = (string) $request->input('q');
        if (str_contains($rawQ, '{search_term_string}') || str_contains($rawQ, '%7Bsearch_term_string%7D')) {
            return redirect()->route('publik.pengumuman.index', $request->except('q'), 301);
        }

        $query = Pengumuman::terbitPublik()
            ->withCount(['likes', 'komentars', 'shares']);

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
    public function pengumumanShow(Request $request, string $slug)
    {
        $pengumuman = Pengumuman::terbitPublik()
            ->with(['likes', 'komentars', 'shares'])
            ->where('slug', $slug)
            ->first();

        if (!$pengumuman) {
            // Jika dokumen ada tapi berstatus draft, tetap kembalikan 404
            if (Pengumuman::where('slug', $slug)->exists()) {
                abort(404);
            }

            // Cek apakah ada pengumuman terbit dengan slug mirip (potongan 10 huruf awal)
            $cleanPrefix = substr(preg_replace('/[^a-zA-Z0-9]/', '', $slug), 0, 10);
            $similar = !empty($cleanPrefix)
                ? Pengumuman::terbitPublik()->where('slug', 'LIKE', '%' . $cleanPrefix . '%')->first()
                : null;

            if ($similar) {
                return redirect()->route('publik.pengumuman.show', $similar->slug, 301);
            }

            // Fallback: alihkan pengunjung & bot pencari secara permanen (301) ke beranda warta
            return redirect()->route('publik.pengumuman.index', [], 301);
        }

        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        // Status like pengguna saat ini berdasarkan IP
        $isLiked = $pengumuman->isLikedByIp($request->ip());
        $likesCount = $pengumuman->likes()->count();
        $komentarsCount = $pengumuman->komentars()->count();
        $sharesCount = $pengumuman->shares()->count();

        // Ambil daftar komentar untuk ditampilkan di halaman
        $komentars = $pengumuman->komentars;

        // Pengumuman terkait lainnya
        $terkait = Pengumuman::terbitPublik()
            ->where('id', '!=', $pengumuman->id)
            ->take(4)
            ->get();

        return view('public.pengumuman-show', compact(
            'pengumuman',
            'terkait',
            'pengaturan',
            'isLiked',
            'likesCount',
            'komentarsCount',
            'sharesCount',
            'komentars'
        ));
    }

    /**
     * Tautan Singkat Pengumuman Publik (Short Link).
     * Contoh: /p/5 atau /p/jadwal-santri
     */
    public function pengumumanShortShow(string $identifier)
    {
        $pengumuman = is_numeric($identifier)
            ? Pengumuman::terbitPublik()->where('id', $identifier)->firstOrFail()
            : Pengumuman::terbitPublik()->where('slug', $identifier)->firstOrFail();

        return redirect()->route('publik.pengumuman.show', $pengumuman->slug, 301);
    }

    /**
     * Berikan Like atau Batalkan Like pada Pengumuman (AJAX).
     */
    public function likePengumuman(Request $request, string $slug)
    {
        $pengumuman = Pengumuman::terbitPublik()->where('slug', $slug)->firstOrFail();
        $ip = $request->ip();

        $existingLike = \App\Models\PengumumanLike::where('pengumuman_id', $pengumuman->id)
            ->where('ip_address', $ip)
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
            $pesan = 'Batal menyukai berita.';
        } else {
            \App\Models\PengumumanLike::create([
                'pengumuman_id' => $pengumuman->id,
                'ip_address' => $ip,
                'user_agent' => $request->userAgent(),
            ]);
            $liked = true;
            $pesan = 'Terima kasih telah menyukai berita ini!';

            // Buat Notifikasi untuk Panel Sekretaris (Lonceng Navbar Admin)
            \App\Models\Notifikasi::create([
                'judul' => 'Suka Baru',
                'pesan' => "User Publik menyukai berita: \"{$pengumuman->judul}\"",
                'tipe' => 'like',
                'pengumuman_id' => $pengumuman->id,
                'link' => route('publik.pengumuman.show', $pengumuman->slug),
                'data_tambahan' => [
                    'pengumuman_judul' => $pengumuman->judul,
                    'pengirim' => 'User Publik',
                ],
            ]);
        }

        $likesCount = $pengumuman->likes()->count();

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $likesCount,
            'message' => $pesan,
        ]);
    }

    /**
     * Kirim Komentar Pengumuman secara Pop-Up Modal tanpa Reload Halaman (AJAX).
     */
    public function komentarPengumuman(Request $request, string $slug)
    {
        $pengumuman = Pengumuman::terbitPublik()->where('slug', $slug)->firstOrFail();

        $request->validate([
            'nama' => 'nullable|string|max:100',
            'isi' => 'required|string|min:2|max:1000',
        ], [
            'isi.required' => 'Isi komentar wajib diisi.',
            'isi.min' => 'Komentar minimal terdiri dari 2 karakter.',
            'isi.max' => 'Komentar maksimal 1000 karakter.',
            'nama.max' => 'Nama maksimal 100 karakter.',
        ]);

        $komentar = \App\Models\PengumumanKomentar::create([
            'pengumuman_id' => $pengumuman->id,
            'nama' => $request->filled('nama') ? trim($request->nama) : null,
            'isi' => trim($request->isi),
            'ip_address' => $request->ip(),
            'is_tampil' => true,
        ]);

        $namaTampil = $komentar->nama_tampil;

        // Buat Notifikasi untuk Panel Sekretaris (Lonceng Navbar Admin)
        \App\Models\Notifikasi::create([
            'judul' => "Komentar Baru: {$namaTampil}",
            'pesan' => "Komentar dari {$namaTampil} pada berita \"{$pengumuman->judul}\":\n\n\"{$komentar->isi}\"",
            'tipe' => 'komentar',
            'pengumuman_id' => $pengumuman->id,
            'link' => route('publik.pengumuman.show', $pengumuman->slug),
            'data_tambahan' => [
                'pengumuman_judul' => $pengumuman->judul,
                'pengirim' => $namaTampil,
                'isi_komentar' => $komentar->isi,
                'komentar_id' => $komentar->id,
            ],
        ]);

        $komentarsCount = $pengumuman->komentars()->count();

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Komentar Anda berhasil dikirim dan ditayangkan.',
            'komentars_count' => $komentarsCount,
            'komentar' => [
                'id' => $komentar->id,
                'nama' => $komentar->nama_tampil,
                'inisial' => strtoupper(substr($komentar->nama_tampil, 0, 1)),
                'isi' => e($komentar->isi),
                'waktu' => $komentar->created_at->diffForHumans(),
            ],
        ]);
    }

    /**
     * Catat Aktivitas Pembagian (Share) Pengumuman (AJAX).
     */
    public function sharePengumuman(Request $request, string $slug)
    {
        $pengumuman = Pengumuman::terbitPublik()->where('slug', $slug)->firstOrFail();

        $platform = $request->input('platform', 'whatsapp');
        $validPlatforms = ['whatsapp', 'instagram', 'facebook', 'twitter', 'telegram', 'salin_tautan'];
        if (!in_array($platform, $validPlatforms)) {
            $platform = 'whatsapp';
        }

        \App\Models\PengumumanShare::create([
            'pengumuman_id' => $pengumuman->id,
            'platform' => $platform,
            'ip_address' => $request->ip(),
        ]);

        $labelPlatform = match ($platform) {
            'whatsapp' => 'WhatsApp',
            'instagram' => 'Instagram',
            'facebook' => 'Facebook',
            'twitter' => 'Twitter / X',
            'telegram' => 'Telegram',
            'salin_tautan' => 'Salin Tautan',
            default => 'Media Sosial',
        };

        // Buat Notifikasi untuk Panel Sekretaris (Lonceng Navbar Admin)
        \App\Models\Notifikasi::create([
            'judul' => 'Berita Dibagikan',
            'pesan' => "User Publik membagikan berita \"{$pengumuman->judul}\" melalui {$labelPlatform}",
            'tipe' => 'share',
            'pengumuman_id' => $pengumuman->id,
            'link' => route('publik.pengumuman.show', $pengumuman->slug),
            'data_tambahan' => [
                'pengumuman_judul' => $pengumuman->judul,
                'platform' => $labelPlatform,
                'pengirim' => 'User Publik',
            ],
        ]);

        $sharesCount = $pengumuman->shares()->count();

        return response()->json([
            'success' => true,
            'shares_count' => $sharesCount,
            'message' => 'Tautan berhasil dicatat.',
        ]);
    }

    /**
     * Hasilkan Sitemap XML untuk Pengindeksan Mesin Pencari Google.
     */
    public function sitemap()
    {
        try {
            $pengumumans = Pengumuman::terbitPublik()->orderByDesc('updated_at')->get();
            $content = view('public.sitemap', compact('pengumumans'))->render();

            // Tulis juga ke berkas fisik public/sitemap.xml agar web server (LiteSpeed/Apache/Nginx) bisa menyajikan langsung
            @file_put_contents(public_path('sitemap.xml'), $content);
        } catch (\Throwable $e) {
            // Fallback: gunakan berkas statis public/sitemap.xml jika ada kendala database
            if (is_file(public_path('sitemap.xml'))) {
                $content = file_get_contents(public_path('sitemap.xml'));
            } else {
                $content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\"><url><loc>https://ponpesalfalahputak.my.id/</loc><priority>1.0</priority></url></urlset>";
            }
        }

        return response($content, 200)
            ->header('Content-Type', 'application/xml; charset=utf-8')
            ->header('X-Robots-Tag', 'noindex');
    }
}
