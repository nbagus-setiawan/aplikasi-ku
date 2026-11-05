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

            // Agregasi Hunian Asrama / Kamar (Hanya yang berpenghuni, sesuai teks asli yang diinputkan)
            if (! empty($rawKamar) && $rawKamar !== '-' && $rawKamar !== '0' && strtolower($rawKamar) !== 'belum ada') {
                $hunianKamar[$rawKamar] = ($hunianKamar[$rawKamar] ?? 0) + 1;
            }
        }

        // Urutkan asrama berdasarkan jumlah penghuni terbanyak (persis sesuai inputan data santri)
        arsort($hunianKamar);

        $chartAsramaLabels = array_keys($hunianKamar);
        $chartAsramaData = array_values($hunianKamar);
        $totalKamarTerisi = count($hunianKamar);
        $totalSantriBerasrama = array_sum($chartAsramaData);
        $totalSantriTanpaKamar = max(0, $totalSantriAktif - $totalSantriBerasrama);

        // Agregasi Kelas Santri (Murni persis sesuai nilai yang diinputkan di data santri)
        $kelasCounts = [];
        foreach ($santriAktifList as $s) {
            $kls = trim((string) $s->kelas);
            if (! empty($kls) && $kls !== '-' && $kls !== '0') {
                $kelasCounts[$kls] = ($kelasCounts[$kls] ?? 0) + 1;
            }
        }

        // Urutkan kelas berdasarkan urutan jenjang pendidikan pondok:
        // VII -> VIII -> IX -> X -> XI -> XII -> Mutakhorijin -> Mahasantri -> Lainnya
        uksort($kelasCounts, function ($a, $b) {
            $order = function ($val) {
                $u = strtoupper(trim($val));
                if (preg_match('/\bVII\b/i', $u) || str_starts_with($u, 'VII') || str_contains($u, 'MTS 1') || str_contains($u, 'MTS 7')) return 10;
                if (preg_match('/\bVIII\b/i', $u) || str_starts_with($u, 'VIII') || str_contains($u, 'MTS 2') || str_contains($u, 'MTS 8')) return 20;
                if (preg_match('/\bIX\b/i', $u) || str_starts_with($u, 'IX') || str_contains($u, 'MTS 3') || str_contains($u, 'MTS 9')) return 30;
                if (preg_match('/\bX\b/i', $u) || str_starts_with($u, 'X') || str_contains($u, 'MA 1') || str_contains($u, 'MA 10')) {
                    if (! str_starts_with($u, 'XI') && ! str_starts_with($u, 'XII')) return 40;
                }
                if (preg_match('/\bXI\b/i', $u) || str_starts_with($u, 'XI') || str_contains($u, 'MA 2') || str_contains($u, 'MA 11')) {
                    if (! str_starts_with($u, 'XII')) return 50;
                }
                if (preg_match('/\bXII\b/i', $u) || str_starts_with($u, 'XII') || str_contains($u, 'MA 3') || str_contains($u, 'MA 12')) return 60;
                if (str_contains($u, 'MUTAKHORIJ')) return 70;
                if (str_contains($u, 'MAHASANTRI') || str_contains($u, 'KULIAH')) return 80;
                return 90;
            };

            $weightA = $order($a);
            $weightB = $order($b);
            if ($weightA === $weightB) {
                return strnatcasecmp($a, $b);
            }
            return $weightA <=> $weightB;
        });

        $chartKelasLabels = array_keys($kelasCounts);
        $chartKelasData = array_values($kelasCounts);

        // Skema warna per batang kelas sesuai jenjang
        $chartKelasColors = [];
        foreach ($chartKelasLabels as $label) {
            $u = strtoupper(trim($label));
            if (str_contains($u, 'MUTAKHORIJ')) {
                $chartKelasColors[] = '#d97706'; // Amber
            } elseif (str_contains($u, 'MAHASANTRI') || str_contains($u, 'KULIAH')) {
                $chartKelasColors[] = '#7c3aed'; // Purple
            } elseif (preg_match('/\b(VII|VIII|IX)\b/i', $u) || str_contains($u, 'MTS')) {
                $chartKelasColors[] = '#059669'; // Emerald (MTs)
            } elseif (preg_match('/\b(X|XI|XII)\b/i', $u) || str_contains($u, 'MA')) {
                $chartKelasColors[] = '#2563eb'; // Blue (MA)
            } else {
                $chartKelasColors[] = '#64748b'; // Slate (Lainnya)
            }
        }

        // Data Ringkasan Grafik Jenjang Santri
        $chartJenjangLabels = ['Santri MTs', 'Santri MA', 'Mutakhorijin', 'Mahasantri'];
        $chartJenjangData = [$countMts, $countMa, $countMutakhorijin, $countMahasantri];
        if ($countLainnya > 0) {
            $chartJenjangLabels[] = 'Lainnya';
            $chartJenjangData[] = $countLainnya;
        }

        // Persentase jenjang santri terhadap total santri aktif
        $pctMts = $totalSantriAktif > 0 ? round(($countMts / $totalSantriAktif) * 100, 1) : 0;
        $pctMa = $totalSantriAktif > 0 ? round(($countMa / $totalSantriAktif) * 100, 1) : 0;
        $pctMutakhorijin = $totalSantriAktif > 0 ? round(($countMutakhorijin / $totalSantriAktif) * 100, 1) : 0;
        $pctMahasantri = $totalSantriAktif > 0 ? round(($countMahasantri / $totalSantriAktif) * 100, 1) : 0;

        // Daftar hunian asrama terstruktur untuk visual ranking bar
        $hunianKamarList = [];
        foreach ($hunianKamar as $kamarNama => $kamarJml) {
            $hunianKamarList[] = [
                'nama' => $kamarNama,
                'total' => $kamarJml,
            ];
        }
        $maxHunian = ! empty($chartAsramaData) ? max($chartAsramaData) : 1;

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
            'pctMts',
            'pctMa',
            'pctMutakhorijin',
            'pctMahasantri',
            'hunianKamarList',
            'maxHunian',
            'chartKelasLabels',
            'chartKelasData',
            'chartKelasColors',
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
