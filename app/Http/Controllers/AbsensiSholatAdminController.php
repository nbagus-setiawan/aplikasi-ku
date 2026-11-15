<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPetugas;
use App\Models\AbsensiSholat;
use App\Models\AbsensiSholatJadwal;
use App\Models\Pengaturan;
use App\Models\Pengurus;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiSholatAdminController extends Controller
{
    /**
     * Dashboard & Manajemen Absensi Korlap Sholat
     */
    public function index(Request $request)
    {
        AbsensiSholatJadwal::inisialisasiDefault();

        $activeTab = $request->query('tab', 'monitoring');

        // Parameter Tanggal & Bulan
        $now = Carbon::now();

        // Otomatis tandai Alfa bagi petugas yang belum absen pada sholat yang telah ditutup hari ini
        AbsensiSholat::autoAlfaPetugasYangBelumAbsen($now);

        $tanggalTerpilih = $request->query('tanggal', $now->toDateString());
        $bulanTerpilih = $request->query('bulan', $now->format('Y-m'));

        try {
            $dtBulan = Carbon::parse($bulanTerpilih . '-01');
        } catch (\Throwable) {
            $dtBulan = $now->copy()->startOfMonth();
            $bulanTerpilih = $dtBulan->format('Y-m');
        }

        $awalBulan = $dtBulan->copy()->startOfMonth()->toDateString();
        $akhirBulan = $dtBulan->copy()->endOfMonth()->toDateString();
        $jumlahHariDalamBulan = (int)$dtBulan->daysInMonth;

        // Data Jadwal 5 Sholat
        $jadwals = AbsensiSholatJadwal::all()->keyBy('nama_sholat');

        // Data Petugas
        $totalPetugas = AbsensiPetugas::count();
        $petugasAktif = AbsensiPetugas::aktif()->count();
        $semuaPetugas = AbsensiPetugas::orderBy('bagian')->orderBy('nama')->get();
        $pengurusList = Pengurus::orderBy('nama')->get();

        // -------------------------------------------------------------
        // 1. MONITORING HARI INI / TANGGAL TERPILIH
        // -------------------------------------------------------------
        $presensiHariIni = AbsensiSholat::with(['petugas', 'koordinator'])
            ->whereDate('tanggal', $tanggalTerpilih)
            ->get();

        $ringkasanPerSholat = [];
        foreach (AbsensiSholatJadwal::SHOLAT_LIST as $key => $label) {
            $records = $presensiHariIni->where('waktu_sholat', $key);
            $ringkasanPerSholat[$key] = [
                'nama' => $label,
                'total' => $records->count(),
                'hadir_tepat' => $records->where('status', 'hadir')->where('kategori_waktu', 'tepat_waktu')->count(),
                'hadir_telat' => $records->where('status', 'hadir')->where('kategori_waktu', 'terlambat')->count(),
                'sakit' => $records->where('status', 'sakit')->count(),
                'izin' => $records->where('status', 'izin')->count(),
                'alfa' => $records->where('status', 'alfa')->count(),
                'koordinator' => $records->first()?->koordinator?->nama ?? '-',
            ];
        }

        // -------------------------------------------------------------
        // 2. GRAFIK TERPADU 1 BULAN (SINGLE CHART MULTI-SHOLAT 5 WAKTU)
        // -------------------------------------------------------------
        $driver = DB::connection()->getDriverName();
        $tglExpr = ($driver === 'sqlite') ? "strftime('%d', tanggal) as tgl" : "DATE_FORMAT(tanggal, '%d') as tgl";

        $dataChartRaw = AbsensiSholat::whereBetween('tanggal', [$awalBulan, $akhirBulan])
            ->where('status', 'hadir')
            ->selectRaw("{$tglExpr}, waktu_sholat, count(*) as total")
            ->groupBy('tgl', 'waktu_sholat')
            ->get();

        $chartLabels = [];
        for ($h = 1; $h <= $jumlahHariDalamBulan; $h++) {
            $chartLabels[] = (string)$h;
        }

        $chartDatasets = [
            'subuh'   => array_fill(0, $jumlahHariDalamBulan, 0),
            'dzuhur'  => array_fill(0, $jumlahHariDalamBulan, 0),
            'ashar'   => array_fill(0, $jumlahHariDalamBulan, 0),
            'maghrib' => array_fill(0, $jumlahHariDalamBulan, 0),
            'isya'    => array_fill(0, $jumlahHariDalamBulan, 0),
        ];

        foreach ($dataChartRaw as $row) {
            $dayIndex = (int)$row->tgl - 1;
            if (isset($chartDatasets[$row->waktu_sholat][$dayIndex])) {
                $chartDatasets[$row->waktu_sholat][$dayIndex] = (int)$row->total;
            }
        }

        // -------------------------------------------------------------
        // 3. LOG DETAIL RIWAYAT (TAB LOG)
        // -------------------------------------------------------------
        $logQuery = AbsensiSholat::with(['petugas', 'koordinator'])->orderByDesc('tanggal')->orderByDesc('jam_absen');

        if ($request->filled('filter_tanggal')) {
            $logQuery->whereDate('tanggal', $request->filter_tanggal);
        } else {
            $logQuery->whereBetween('tanggal', [$awalBulan, $akhirBulan]);
        }

        if ($request->filled('filter_sholat')) {
            $logQuery->where('waktu_sholat', $request->filter_sholat);
        }

        if ($request->filled('filter_status')) {
            $logQuery->where('status', $request->filter_status);
        }

        if ($request->filled('filter_petugas')) {
            $logQuery->where('petugas_id', $request->filter_petugas);
        }

        $logs = $logQuery->paginate(25)->withQueryString();

        // -------------------------------------------------------------
        // 4. REKAPITULASI KEHADIRAN BULANAN PER PETUGAS
        // -------------------------------------------------------------
        $rekapBulanan = $this->hitungRekapBulanan($awalBulan, $akhirBulan);

        // Opsi daftar bulan untuk filter (6 bulan terakhir s.d 1 bulan ke depan)
        $bulanOptions = [];
        for ($i = -5; $i <= 1; $i++) {
            $m = $now->copy()->addMonths($i);
            $bulanOptions[] = [
                'val' => $m->format('Y-m'),
                'label' => $m->locale('id')->translatedFormat('F Y'),
            ];
        }

        return view('absensi_sholat.admin_index', compact(
            'activeTab',
            'now',
            'tanggalTerpilih',
            'bulanTerpilih',
            'dtBulan',
            'jadwals',
            'totalPetugas',
            'petugasAktif',
            'semuaPetugas',
            'pengurusList',
            'presensiHariIni',
            'ringkasanPerSholat',
            'chartLabels',
            'chartDatasets',
            'logs',
            'rekapBulanan',
            'bulanOptions'
        ));
    }

    /**
     * Update Pengaturan Jam 5 Waktu Sholat
     */
    public function updateJadwal(Request $request)
    {
        $validated = $request->validate([
            'jadwals' => ['required', 'array'],
            'jadwals.*.jam_buka' => ['required', 'date_format:H:i'],
            'jadwals.*.jam_tepat' => ['required', 'date_format:H:i'],
            'jadwals.*.jam_tutup' => ['required', 'date_format:H:i'],
            'jadwals.*.is_buka_manual' => ['nullable', 'in:otomatis,buka,tutup'],
        ]);

        foreach ($validated['jadwals'] as $sholat => $vals) {
            $manual = match ($vals['is_buka_manual'] ?? 'otomatis') {
                'buka' => true,
                'tutup' => false,
                default => null,
            };

            AbsensiSholatJadwal::where('nama_sholat', $sholat)->update([
                'jam_buka' => $vals['jam_buka'] . ':00',
                'jam_tepat' => $vals['jam_tepat'] . ':00',
                'jam_tutup' => $vals['jam_tutup'] . ':00',
                'is_buka_manual' => $manual,
            ]);
        }

        return redirect()->route('admin.absensi-sholat.index', ['tab' => 'jadwal'])
            ->with('success', 'Pengaturan jam 5 waktu sholat berhasil diperbarui.');
    }

    /**
     * Simpan Master Petugas Korlap Baru (Dari Pengurus atau Manual)
     */
    public function storePetugas(Request $request)
    {
        $sumber = $request->input('sumber_nama', 'manual');

        if ($sumber === 'pengurus') {
            $request->validate([
                'pengurus_id' => ['required', 'exists:penguruses,id'],
                'bagian' => ['required', 'in:koordinator,anggota'],
                'status' => ['required', 'in:aktif,nonaktif'],
                'jadwal_sholat' => ['nullable', 'array'],
                'jadwal_sholat.*' => ['in:subuh,dzuhur,ashar,maghrib,isya'],
                'keterangan' => ['nullable', 'string', 'max:255'],
            ], [
                'pengurus_id.required' => 'Pilih data pengurus terlebih dahulu.',
            ]);

            $pengurus = Pengurus::findOrFail($request->pengurus_id);
            $nama = $pengurus->nama;
            $pengurusId = $pengurus->id;
        } else {
            $request->validate([
                'nama' => ['required', 'string', 'max:150'],
                'bagian' => ['required', 'in:koordinator,anggota'],
                'status' => ['required', 'in:aktif,nonaktif'],
                'jadwal_sholat' => ['nullable', 'array'],
                'jadwal_sholat.*' => ['in:subuh,dzuhur,ashar,maghrib,isya'],
                'keterangan' => ['nullable', 'string', 'max:255'],
            ], [
                'nama.required' => 'Nama petugas wajib diisi.',
            ]);

            $nama = $request->nama;
            $pengurusId = null;
        }

        AbsensiPetugas::create([
            'nama' => $nama,
            'bagian' => $request->bagian,
            'status' => $request->status,
            'jadwal_sholat' => $request->input('jadwal_sholat'),
            'pengurus_id' => $pengurusId,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('admin.absensi-sholat.index', ['tab' => 'petugas'])
            ->with('success', "Petugas Korlap \"{$nama}\" berhasil ditambahkan.");
    }

    /**
     * Perbarui Data Petugas Korlap
     */
    public function updatePetugas(Request $request, AbsensiPetugas $petuga)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'bagian' => ['required', 'in:koordinator,anggota'],
            'status' => ['required', 'in:aktif,nonaktif'],
            'jadwal_sholat' => ['nullable', 'array'],
            'jadwal_sholat.*' => ['in:subuh,dzuhur,ashar,maghrib,isya'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $petuga->update([
            'nama' => $validated['nama'],
            'bagian' => $validated['bagian'],
            'status' => $validated['status'],
            'jadwal_sholat' => $request->input('jadwal_sholat'),
            'keterangan' => $validated['keterangan'] ?? null,
        ]);

        return redirect()->route('admin.absensi-sholat.index', ['tab' => 'petugas'])
            ->with('success', "Data petugas \"{$petuga->nama}\" berhasil diperbarui.");
    }

    /**
     * Hapus Petugas Korlap (Soft Delete)
     */
    public function destroyPetugas(AbsensiPetugas $petuga)
    {
        $nama = $petuga->nama;
        $petuga->delete();

        return redirect()->route('admin.absensi-sholat.index', ['tab' => 'petugas'])
            ->with('success', "Petugas \"{$nama}\" berhasil dihapus.");
    }

    /**
     * Koreksi / Edit Status Log Presensi Secara Manual oleh Sekretaris
     */
    public function updateStatusLog(Request $request, AbsensiSholat $absensi)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:hadir,sakit,izin,alfa'],
            'kategori_waktu' => ['nullable', 'in:tepat_waktu,terlambat'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $katWaktu = ($validated['status'] === 'hadir') ? ($validated['kategori_waktu'] ?? 'tepat_waktu') : null;

        $absensi->update([
            'status' => $validated['status'],
            'kategori_waktu' => $katWaktu,
            'keterangan' => $validated['keterangan'] ?? $absensi->keterangan,
        ]);

        return redirect()->back()->with('success', "Status presensi {$absensi->petugas->nama} berhasil diperbarui.");
    }

    /**
     * Cetak Rekapitulasi Presensi Sholat Bulanan (PDF)
     */
    public function cetakRekapPdf(Request $request)
    {
        $bulan = $request->query('bulan', Carbon::now()->format('Y-m'));
        $dtBulan = Carbon::parse($bulan . '-01');
        $awalBulan = $dtBulan->copy()->startOfMonth()->toDateString();
        $akhirBulan = $dtBulan->copy()->endOfMonth()->toDateString();

        $rekap = $this->hitungRekapBulanan($awalBulan, $akhirBulan);
        $labelPeriode = $dtBulan->locale('id')->translatedFormat('F Y');

        // Pengaturan Kop
        $pengaturans = Pengaturan::getAllKeyValues();
        $logo = null;
        if (file_exists(public_path('images/logo-pesantren.png'))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('images/logo-pesantren.png')));
        } elseif (!empty($pengaturans['logo']) && file_exists(storage_path('app/public/' . $pengaturans['logo']))) {
            $logo = 'data:image/png;base64,' . base64_encode(file_get_contents(storage_path('app/public/' . $pengaturans['logo'])));
        }

        $viewData = [
            'rekap' => $rekap,
            'labelPeriode' => $labelPeriode,
            'pengaturans' => $pengaturans,
            'logo' => $logo,
            'tanggalCetak' => Carbon::now()->locale('id')->translatedFormat('d F Y'),
        ];

        if (class_exists(\DOMImplementation::class)) {
            $pdf = Pdf::loadView('absensi_sholat.rekap_pdf', $viewData)
                ->setPaper('a4', 'portrait');

            return $pdf->stream('rekap-absensi-korlap-' . $bulan . '.pdf');
        }

        return view('absensi_sholat.rekap_pdf', $viewData);
    }

    /**
     * Helper Hitung Rekap Bulanan
     */
    protected function hitungRekapBulanan(string $awal, string $akhir)
    {
        $petugasList = AbsensiPetugas::orderBy('bagian')->orderBy('nama')->get();

        $presensiBulan = AbsensiSholat::whereBetween('tanggal', [$awal, $akhir])
            ->selectRaw('petugas_id, status, kategori_waktu, count(*) as total')
            ->groupBy('petugas_id', 'status', 'kategori_waktu')
            ->get();

        $hasil = [];

        foreach ($petugasList as $p) {
            $items = $presensiBulan->where('petugas_id', $p->id);

            $hadirTepat = $items->where('status', 'hadir')->where('kategori_waktu', 'tepat_waktu')->sum('total');
            $hadirTelat = $items->where('status', 'hadir')->where('kategori_waktu', 'terlambat')->sum('total');
            $hadirTotal = $hadirTepat + $hadirTelat;
            $sakit = $items->where('status', 'sakit')->sum('total');
            $izin = $items->where('status', 'izin')->sum('total');
            $alfa = $items->where('status', 'alfa')->sum('total');
            $totalSesi = $hadirTotal + $sakit + $izin + $alfa;

            $persentaseHadir = $totalSesi > 0 ? round(($hadirTotal / $totalSesi) * 100, 1) : 0;

            $hasil[] = (object)[
                'petugas' => $p,
                'hadir_tepat' => $hadirTepat,
                'hadir_telat' => $hadirTelat,
                'hadir_total' => $hadirTotal,
                'sakit' => $sakit,
                'izin' => $izin,
                'alfa' => $alfa,
                'total_sesi' => $totalSesi,
                'persentase_hadir' => $persentaseHadir,
            ];
        }

        return collect($hasil);
    }
}
