<?php

namespace App\Http\Controllers;

use App\Models\AbsensiPetugas;
use App\Models\AbsensiSholat;
use App\Models\AbsensiSholatJadwal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AbsensiSholatPublikController extends Controller
{
    /**
     * Halaman Publik Presensi Korlap Sholat Jamaah (Tanpa Login)
     */
    public function index(Request $request)
    {
        AbsensiSholatJadwal::inisialisasiDefault();

        $now = Carbon::now();
        $today = $now->toDateString();

        // Otomatis tandai Alfa bagi petugas yang belum absen pada sholat yang telah ditutup hari ini
        AbsensiSholat::autoAlfaPetugasYangBelumAbsen($now);

        // 1. Ambil 5 jadwal sholat
        $jadwals = AbsensiSholatJadwal::all()->keyBy('nama_sholat');

        // 2. Dapatkan kondisi presensi saat ini (hanya waktu terdekat/aktif yang tampil)
        $kondisi = AbsensiSholatJadwal::getKondisiPresensi($now);
        $sholatTerpilih = $kondisi['sholat'];
        $isBisaAbsen = $kondisi['is_bisa_absen'];
        $fase = $kondisi['fase'];
        $targetWaktu = $kondisi['target_waktu'];
        $keteranganFase = $kondisi['keterangan_fase'];
        $pesanStatus = $kondisi['pesan_status'];

        // Jika ada permintaan eksplisit sholat dari URL
        if ($request->filled('sholat') && in_array($request->query('sholat'), array_keys(AbsensiSholatJadwal::SHOLAT_LIST))) {
            $jadwalReq = $jadwals->get($request->query('sholat'));
            if ($jadwalReq) {
                $sholatTerpilih = $jadwalReq;
                $isBisaAbsen = $jadwalReq->isBisaAbsen($now);
                if ($isBisaAbsen) {
                    $fase = ($now->format('H:i:s') <= $jadwalReq->jam_tepat) ? 'tepat' : 'telat';
                    $targetWaktu = ($fase === 'tepat')
                        ? Carbon::parse($today . ' ' . $jadwalReq->jam_tepat)
                        : Carbon::parse($today . ' ' . $jadwalReq->jam_tutup);
                    $keteranganFase = ($fase === 'tepat') ? 'Batas Presensi Tepat Waktu' : 'Form Ditutup Dalam';
                    $pesanStatus = 'Form presensi sholat ' . ucfirst($jadwalReq->nama_sholat) . ' sedang dibuka.';
                } else {
                    $fase = 'tutup';
                    $targetWaktu = Carbon::parse($today . ' ' . $jadwalReq->jam_buka);
                    if ($now->greaterThan($targetWaktu)) {
                        $targetWaktu = Carbon::parse($now->copy()->addDay()->toDateString() . ' ' . $jadwalReq->jam_buka);
                    }
                    $keteranganFase = 'Form Sholat ' . ucfirst($jadwalReq->nama_sholat) . ' Dibuka Dalam';
                    $pesanStatus = 'Presensi sholat ' . ucfirst($jadwalReq->nama_sholat) . ' saat ini sedang ditutup.';
                }
            }
        }

        $namaSholat = $sholatTerpilih ? $sholatTerpilih->nama_sholat : 'subuh';
        $kategoriWaktuSaatIni = $sholatTerpilih ? $sholatTerpilih->getKategoriWaktu($now) : 'tepat_waktu';
        $targetEpochMs = $targetWaktu ? ($targetWaktu->timestamp * 1000) : ($now->timestamp * 1000);

        // 3. Ambil daftar koordinator dan anggota aktif yang bertugas pada sholat ini
        $koordinators = AbsensiPetugas::aktif()->koordinator()->untukSholat($namaSholat)->orderBy('nama')->get();
        if ($koordinators->isEmpty()) {
            $koordinators = AbsensiPetugas::aktif()->untukSholat($namaSholat)->orderBy('nama')->get();
        }

        $anggotas = AbsensiPetugas::aktif()->untukSholat($namaSholat)->orderBy('nama')->get();

        // 4. Cek apakah sudah ada data absen yang tersimpan hari ini untuk sholat ini
        $riwayatHariIni = AbsensiSholat::whereDate('tanggal', $today)
            ->where('waktu_sholat', $namaSholat)
            ->get()
            ->keyBy('petugas_id');

        $sudahDiabsen = $riwayatHariIni->isNotEmpty();
        $koordinatorTerpilihId = $sudahDiabsen ? ($riwayatHariIni->first()->koordinator_id ?? null) : null;

        return view('absensi_sholat.publik', compact(
            'now',
            'today',
            'jadwals',
            'sholatTerpilih',
            'namaSholat',
            'isBisaAbsen',
            'fase',
            'targetWaktu',
            'targetEpochMs',
            'keteranganFase',
            'pesanStatus',
            'kategoriWaktuSaatIni',
            'koordinators',
            'anggotas',
            'riwayatHariIni',
            'sudahDiabsen',
            'koordinatorTerpilihId'
        ));
    }

    /**
     * Simpan Presensi Jamaah Oleh Koordinator
     */
    public function store(Request $request)
    {
        AbsensiSholatJadwal::inisialisasiDefault();

        $validated = $request->validate([
            'waktu_sholat' => ['required', 'in:subuh,dzuhur,ashar,maghrib,isya'],
            'koordinator_id' => ['required', 'exists:absensi_petugas,id'],
            'presensi' => ['required', 'array'],
            'presensi.*.status' => ['required', 'in:hadir,sakit,izin,alfa'],
            'presensi.*.keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'koordinator_id.required' => 'Pilih nama Koordinator yang mengisi presensi.',
            'waktu_sholat.required' => 'Waktu sholat harus dipilih.',
            'presensi.required' => 'Daftar presensi petugas tidak boleh kosong.',
        ]);

        $now = Carbon::now();
        $today = $now->toDateString();
        $jamAbsen = $now->format('H:i:s');
        $namaSholat = $validated['waktu_sholat'];

        $jadwal = AbsensiSholatJadwal::where('nama_sholat', $namaSholat)->first();

        // Validasi waktu tutup
        if ($jadwal && !$jadwal->isBisaAbsen($now)) {
            return redirect()->back()
                ->withInput()
                ->with('error', "Mohon maaf, presensi sholat " . ucfirst($namaSholat) . " saat ini telah ditutup atau belum dibuka.");
        }

        $kategoriWaktu = $jadwal ? $jadwal->getKategoriWaktu($now) : 'tepat_waktu';
        $ip = $request->ip();
        $ua = substr((string)$request->userAgent(), 0, 500);

        DB::transaction(function () use ($validated, $today, $namaSholat, $jamAbsen, $kategoriWaktu, $ip, $ua) {
            $koordinatorId = $validated['koordinator_id'];

            // 1. Simpan kehadiran Koordinator itu sendiri (default hadir jika belum tercatat)
            AbsensiSholat::updateOrCreate(
                [
                    'tanggal' => $today,
                    'waktu_sholat' => $namaSholat,
                    'petugas_id' => $koordinatorId,
                ],
                [
                    'koordinator_id' => $koordinatorId,
                    'jam_absen' => $jamAbsen,
                    'status' => 'hadir',
                    'kategori_waktu' => $kategoriWaktu,
                    'keterangan' => 'Koordinator bertugas',
                    'ip_address' => $ip,
                    'user_agent' => $ua,
                ]
            );

            // 2. Simpan kehadiran seluruh anggota yang diabsen
            foreach ($validated['presensi'] as $petugasId => $data) {
                // Jika koordinator sudah tersimpan di atas dan sama, lewati atau perbarui
                $status = $data['status'] ?? 'hadir';
                $keterangan = !empty($data['keterangan']) ? trim($data['keterangan']) : null;
                $katWaktu = ($status === 'hadir') ? $kategoriWaktu : null;

                AbsensiSholat::updateOrCreate(
                    [
                        'tanggal' => $today,
                        'waktu_sholat' => $namaSholat,
                        'petugas_id' => $petugasId,
                    ],
                    [
                        'koordinator_id' => $koordinatorId,
                        'jam_absen' => $jamAbsen,
                        'status' => $status,
                        'kategori_waktu' => $katWaktu,
                        'keterangan' => $keterangan,
                        'ip_address' => $ip,
                        'user_agent' => $ua,
                    ]
                );
            }
        });

        $namaSholatCap = ucfirst($namaSholat);
        return redirect()->route('absensi-sholat.index', ['sholat' => $namaSholat])
            ->with('success', "Alhamdulillah! Presensi sholat {$namaSholatCap} berhasil disimpan.");
    }
}
