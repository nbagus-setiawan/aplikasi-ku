<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Arsip;
use App\Models\KategoriSurat;
use App\Models\Pengaturan;
use App\Models\Santri;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Tampilkan halaman dashboard utama admin.
     */
    public function index(): View
    {
        $now = Carbon::now();

        $totalSantriAktif = Santri::where('status', 'aktif')->count();
        $suratMasukBulanIni = SuratMasuk::whereMonth('tgl_diterima', $now->month)
            ->whereYear('tgl_diterima', $now->year)
            ->count();
        $suratKeluarBulanIni = SuratKeluar::whereMonth('tgl_surat', $now->month)
            ->whereYear('tgl_surat', $now->year)
            ->count();
        $suratMasukBelumDisposisi = SuratMasuk::where('status_disposisi', 'baru')->count();

        // Agenda & Arsip (Fase 4)
        $agendaMendatang = Agenda::mendatang()->count();
        $agendasBesok = Agenda::hMinusSatu()->with('pj')->get();
        $agendasTerdekat = Agenda::mendatang()->with('pj')->take(5)->get();
        $totalArsip = Arsip::count();

        $suratMasukTerbaru = SuratMasuk::with('kategori')->latest('tgl_diterima')->take(5)->get();
        $suratKeluarTerbaru = SuratKeluar::with('kategori')->latest('tgl_surat')->take(5)->get();

        $pengaturans = Pengaturan::getAllKeyValues();
        $totalKategoriSurat = KategoriSurat::count();

        // Data Tren 6 Bulan Terakhir untuk Bagan (Chart) Persuratan
        $chartBulanLabels = [];
        $chartSuratMasukData = [];
        $chartSuratKeluarData = [];

        for ($i = 5; $i >= 0; $i--) {
            $tgl = Carbon::now()->subMonths($i);
            $chartBulanLabels[] = $tgl->translatedFormat('M y');

            $chartSuratMasukData[] = SuratMasuk::whereYear('tgl_diterima', $tgl->year)
                ->whereMonth('tgl_diterima', $tgl->month)
                ->count();

            $chartSuratKeluarData[] = SuratKeluar::whereYear('tgl_surat', $tgl->year)
                ->whereMonth('tgl_surat', $tgl->month)
                ->count();
        }

        // Data Statistik Santri berdasarkan Status
        $santriStatusData = [
            'aktif' => $totalSantriAktif,
            'lulus' => Santri::where('status', 'lulus')->count(),
            'mutasi' => Santri::where('status', 'mutasi')->count(),
            'keluar' => Santri::where('status', 'keluar')->count(),
        ];
        $totalSantriSemua = array_sum($santriStatusData);

        // Klasifikasi Jenjang Santri Aktif (MTs, MA, Mutakhorijin, Mahasantri) & Hunian Asrama
        $santriAktifList = Santri::where('status', 'aktif')->get(['id', 'kelas', 'kamar']);

        $countMts = 0;
        $countMtsVII = 0;
        $countMtsVIII = 0;
        $countMtsIX = 0;

        $countMa = 0;
        $countMaX = 0;
        $countMaXI = 0;
        $countMaXII = 0;

        $countMutakhorijin = 0;
        $countMahasantri = 0;
        $countLainnya = 0;

        $hunianKamar = [];

        foreach ($santriAktifList as $s) {
            $rawKelas = strtoupper(trim((string) $s->kelas));
            $rawKamar = trim((string) $s->kamar);

            // 1. Mutakhorijin (tamat kelas 12 & mengabdi 1 tahun)
            if (str_contains($rawKelas, 'MUTAKHORIJIN') || str_contains($rawKelas, 'MUTAKHORIJ') || str_contains($rawKelas, 'PENGABDIAN')) {
                $countMutakhorijin++;
            }
            // 2. Mahasantri (kuliah di luar tapi mukim di pondok)
            elseif (str_contains($rawKelas, 'MAHASANTRI') || str_contains($rawKelas, 'KULIAH')) {
                $countMahasantri++;
            }
            // 3. MTs (Kelas VII, VIII, IX) - Cek VIII terlebih dahulu agar awalan 'VIII' tidak tertangkap oleh 'VII'
            elseif (
                preg_match('/\bVIII\b/i', $rawKelas) || 
                str_starts_with($rawKelas, 'VIII') || 
                str_contains($rawKelas, 'MTS 2') || 
                str_contains($rawKelas, 'MTS 8') ||
                preg_match('/^8\b/', $rawKelas)
            ) {
                $countMts++;
                $countMtsVIII++;
            } elseif (
                preg_match('/\bIX\b/i', $rawKelas) || 
                str_starts_with($rawKelas, 'IX') || 
                str_contains($rawKelas, 'MTS 3') || 
                str_contains($rawKelas, 'MTS 9') ||
                preg_match('/^9\b/', $rawKelas)
            ) {
                $countMts++;
                $countMtsIX++;
            } elseif (
                preg_match('/\bVII\b/i', $rawKelas) || 
                str_starts_with($rawKelas, 'VII') || 
                str_contains($rawKelas, 'MTS 1') || 
                str_contains($rawKelas, 'MTS 7') ||
                preg_match('/^7\b/', $rawKelas)
            ) {
                $countMts++;
                $countMtsVII++;
            } elseif (str_contains($rawKelas, 'MTS') || str_contains($rawKelas, 'TSANAWIYAH')) {
                $countMts++;
            }
            // 4. MA (Kelas X, XI, XII)
            elseif (
                preg_match('/\bXII\b/i', $rawKelas) || 
                str_starts_with($rawKelas, 'XII') || 
                str_contains($rawKelas, 'MA 3') || 
                str_contains($rawKelas, 'MA 12') ||
                preg_match('/^12\b/', $rawKelas)
            ) {
                $countMa++;
                $countMaXII++;
            } elseif (
                preg_match('/\bXI\b/i', $rawKelas) || 
                str_starts_with($rawKelas, 'XI') || 
                str_contains($rawKelas, 'MA 2') || 
                str_contains($rawKelas, 'MA 11') ||
                preg_match('/^11\b/', $rawKelas)
            ) {
                $countMa++;
                $countMaXI++;
            } elseif (
                preg_match('/\bX\b/i', $rawKelas) || 
                str_starts_with($rawKelas, 'X') || 
                str_contains($rawKelas, 'MA 1') || 
                str_contains($rawKelas, 'MA 10') ||
                preg_match('/^10\b/', $rawKelas)
            ) {
                $countMa++;
                $countMaX++;
            } elseif (str_contains($rawKelas, 'MA') || str_contains($rawKelas, 'ALIYAH')) {
                $countMa++;
            } else {
                $countLainnya++;
            }

            // Agregasi Hunian Asrama / Kamar (Hanya yang berpenghuni)
            if (! empty($rawKamar) && $rawKamar !== '-' && strtolower($rawKamar) !== 'belum ada') {
                $cleanKamar = strtoupper($rawKamar);
                $hunianKamar[$cleanKamar] = ($hunianKamar[$cleanKamar] ?? 0) + 1;
            }
        }

        // Urutkan asrama berdasarkan jumlah penghuni terbanyak
        arsort($hunianKamar);

        $chartAsramaLabels = array_keys($hunianKamar);
        $chartAsramaData = array_values($hunianKamar);
        $totalKamarTerisi = count($hunianKamar);
        $totalSantriBerasrama = array_sum($chartAsramaData);
        $totalSantriTanpaKamar = max(0, $totalSantriAktif - $totalSantriBerasrama);

        // Data Grafik Jenjang Santri
        $chartJenjangLabels = ['Santri MTs', 'Santri MA', 'Mutakhorijin', 'Mahasantri'];
        $chartJenjangData = [$countMts, $countMa, $countMutakhorijin, $countMahasantri];
        if ($countLainnya > 0) {
            $chartJenjangLabels[] = 'Lainnya';
            $chartJenjangData[] = $countLainnya;
        }

        // Disposisi Surat Masuk
        $disposisiStatusData = [
            'baru' => SuratMasuk::where('status_disposisi', 'baru')->count(),
            'proses' => SuratMasuk::where('status_disposisi', 'proses')->count(),
            'selesai' => SuratMasuk::where('status_disposisi', 'selesai')->count(),
        ];

        return view('dashboard', compact(
            'totalSantriAktif',
            'suratMasukBulanIni',
            'suratKeluarBulanIni',
            'suratMasukBelumDisposisi',
            'agendaMendatang',
            'agendasBesok',
            'agendasTerdekat',
            'totalArsip',
            'suratMasukTerbaru',
            'suratKeluarTerbaru',
            'pengaturans',
            'totalKategoriSurat',
            'chartBulanLabels',
            'chartSuratMasukData',
            'chartSuratKeluarData',
            'santriStatusData',
            'totalSantriSemua',
            'disposisiStatusData',
            'countMts',
            'countMtsVII',
            'countMtsVIII',
            'countMtsIX',
            'countMa',
            'countMaX',
            'countMaXI',
            'countMaXII',
            'countMutakhorijin',
            'countMahasantri',
            'countLainnya',
            'chartJenjangLabels',
            'chartJenjangData',
            'chartAsramaLabels',
            'chartAsramaData',
            'totalKamarTerisi',
            'totalSantriBerasrama',
            'totalSantriTanpaKamar'
        ));
    }
}
