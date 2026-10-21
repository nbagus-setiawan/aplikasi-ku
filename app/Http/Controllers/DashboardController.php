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
            'disposisiStatusData'
        ));
    }
}
