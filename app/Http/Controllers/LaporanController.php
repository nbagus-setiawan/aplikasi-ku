<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use App\Models\Pengaturan;
use App\Models\Santri;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Halaman index rekap laporan terpadu.
     */
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'surat-masuk');

        $dari = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->toDateString());

        $data = [];
        $total = 0;

        if ($tab === 'surat-masuk') {
            $query = SuratMasuk::whereDate('tgl_diterima', '>=', $dari)
                ->whereDate('tgl_diterima', '<=', $sampai);

            if ($request->filled('status_disposisi')) {
                $query->where('status_disposisi', $request->status_disposisi);
            }

            $data = $query->orderBy('tgl_diterima')->get();
            $total = $data->count();

        } elseif ($tab === 'surat-keluar') {
            $query = SuratKeluar::with('kategori')
                ->whereDate('tgl_surat', '>=', $dari)
                ->whereDate('tgl_surat', '<=', $sampai);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('metode_ttd')) {
                $query->where('metode_ttd', $request->metode_ttd);
            }

            $data = $query->orderBy('tgl_surat')->get();
            $total = $data->count();

        } elseif ($tab === 'santri') {
            $query = Santri::query();

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('kelas')) {
                $query->where('kelas', $request->kelas);
            }

            if ($request->filled('kamar')) {
                $query->where('kamar', $request->kamar);
            }

            if ($request->filled('tahun_masuk')) {
                $query->where('tahun_masuk', $request->tahun_masuk);
            }

            $data = $query->orderBy('nama')->get();
            $total = $data->count();

        } elseif ($tab === 'agenda') {
            $query = Agenda::with('pj')
                ->whereDate('mulai', '>=', $dari)
                ->whereDate('mulai', '<=', $sampai);

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $data = $query->orderBy('mulai')->get();
            $total = $data->count();
        }

        return view('laporan.index', compact('tab', 'dari', 'sampai', 'data', 'total'));
    }

    /**
     * Cetak PDF rekap Surat Masuk.
     */
    public function cetakSuratMasuk(Request $request)
    {
        $dari = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->toDateString());

        $query = SuratMasuk::whereDate('tgl_diterima', '>=', $dari)
            ->whereDate('tgl_diterima', '<=', $sampai);

        if ($request->filled('status_disposisi')) {
            $query->where('status_disposisi', $request->status_disposisi);
        }

        $suratMasuks = $query->orderBy('tgl_diterima')->get();
        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('laporan.pdf.surat-masuk', compact('suratMasuks', 'dari', 'sampai', 'pengaturan'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("rekap-surat-masuk-{$dari}-sd-{$sampai}.pdf");
    }

    /**
     * Cetak PDF rekap Surat Keluar.
     */
    public function cetakSuratKeluar(Request $request)
    {
        $dari = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->toDateString());

        $query = SuratKeluar::with('kategori')
            ->whereDate('tgl_surat', '>=', $dari)
            ->whereDate('tgl_surat', '<=', $sampai);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('metode_ttd')) {
            $query->where('metode_ttd', $request->metode_ttd);
        }

        $suratKeluars = $query->orderBy('tgl_surat')->get();
        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('laporan.pdf.surat-keluar', compact('suratKeluars', 'dari', 'sampai', 'pengaturan'))
            ->setPaper('a4', 'landscape');

        return $pdf->stream("rekap-surat-keluar-{$dari}-sd-{$sampai}.pdf");
    }

    /**
     * Cetak PDF rekap Santri.
     */
    public function cetakSantri(Request $request)
    {
        $query = Santri::query();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('kamar')) {
            $query->where('kamar', $request->kamar);
        }

        if ($request->filled('tahun_masuk')) {
            $query->where('tahun_masuk', $request->tahun_masuk);
        }

        $santris = $query->orderBy('nama')->get();
        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('laporan.pdf.santri', compact('santris', 'pengaturan', 'request'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('rekap-data-santri.pdf');
    }

    /**
     * Cetak PDF rekap Agenda Kegiatan.
     */
    public function cetakAgenda(Request $request)
    {
        $dari = $request->input('dari_tanggal', Carbon::now()->startOfMonth()->toDateString());
        $sampai = $request->input('sampai_tanggal', Carbon::now()->endOfMonth()->toDateString());

        $query = Agenda::with('pj')
            ->whereDate('mulai', '>=', $dari)
            ->whereDate('mulai', '<=', $sampai);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $agendas = $query->orderBy('mulai')->get();
        $pengaturan = Pengaturan::pluck('value', 'key')->toArray();

        $pdf = Pdf::loadView('laporan.pdf.agenda', compact('agendas', 'dari', 'sampai', 'pengaturan'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream("rekap-agenda-{$dari}-sd-{$sampai}.pdf");
    }
}
