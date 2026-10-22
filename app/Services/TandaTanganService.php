<?php

namespace App\Services;

use App\Models\Pengaturan;
use App\Models\SuratKeluar;
use App\Models\TandaTangan;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use chillerlan\QRCode\QRCode;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TandaTanganService
{
    /**
     * Hasilkan data URI PNG QR Code untuk tautan verifikasi (didukung penuh oleh Dompdf dan HTML).
     */
    public function generateQrCode(string $url): string
    {
        try {
            $options = new \chillerlan\QRCode\QROptions();
            $options->outputInterface = \chillerlan\QRCode\Output\QRGdImagePNG::class;
            $options->imageBase64 = true;
            $options->scale = 5;

            return (new QRCode($options))->render($url);
        } catch (\Throwable $e) {
            return (new QRCode)->render($url);
        }
    }

    /**
     * Buat atau perbarui tautan sekali pakai untuk penandatangan jarak jauh (berlaku 30 menit).
     */
    public function createTautanSekaliPakai(SuratKeluar $suratKeluar, int $urutan = 1): TandaTangan
    {
        $penandatanganItem = null;
        if (!empty($suratKeluar->penandatangans) && is_array($suratKeluar->penandatangans)) {
            foreach ($suratKeluar->penandatangans as $p) {
                if (($p['urutan'] ?? 1) == $urutan) {
                    $penandatanganItem = $p;
                    break;
                }
            }
        }

        $nama = $penandatanganItem['nama'] ?? $suratKeluar->nama_penandatangan;
        $jabatan = $penandatanganItem['jabatan'] ?? $suratKeluar->jabatan_penandatangan;

        return TandaTangan::updateOrCreate(
            [
                'surat_keluar_id' => $suratKeluar->id,
                'urutan' => $urutan,
            ],
            [
                'nama_penandatangan' => $nama,
                'jabatan_penandatangan' => $jabatan,
                'token_sekali_pakai' => Str::random(48),
                'expires_at' => Carbon::now('Asia/Jakarta')->addMinutes(30),
            ]
        );
    }

    /**
     * Proses finalisasi tanda tangan digital.
     */
    public function prosesTtdDigital(
        SuratKeluar $suratKeluar,
        string $dataTtd,
        string $ipAddress,
        ?TandaTangan $tandaTangan = null
    ): SuratKeluar {
        if (! $tandaTangan) {
            $tandaTangan = TandaTangan::firstOrNew([
                'surat_keluar_id' => $suratKeluar->id,
                'urutan' => 1,
            ]);
        }

        $now = Carbon::now('Asia/Jakarta');
        $tandaTangan->fill([
            'nama_penandatangan' => $tandaTangan->nama_penandatangan ?: $suratKeluar->nama_penandatangan,
            'jabatan_penandatangan' => $tandaTangan->jabatan_penandatangan ?: $suratKeluar->jabatan_penandatangan,
            'data_ttd' => $dataTtd,
            'waktu_ttd' => $now,
            'ip_address' => $ipAddress,
            'token_sekali_pakai' => null, // Hangus setelah dipakai
        ]);
        $tandaTangan->save();

        $suratKeluar->metode_ttd = 'digital';

        // Periksa apakah SEMUA penandatangan yang ditentukan sudah bertanda tangan
        $jumlahPenandatangan = (int) ($suratKeluar->jumlah_penandatangan ?? 1);
        $jumlahSudahTtd = TandaTangan::where('surat_keluar_id', $suratKeluar->id)
            ->whereNotNull('data_ttd')
            ->count();

        if ($jumlahSudahTtd >= $jumlahPenandatangan) {
            $suratKeluar->status_ttd = 'sudah';
            $suratKeluar->status = 'final';

            // Muat ulang relasi tandaTangans agar mengambil data_ttd yang baru saja disimpan
            $suratKeluar->load('tandaTangans');

            // Buat PDF final otomatis dan simpan ke storage hanya bila semua penandatangan sudah lengkap
            $pdf = $this->renderPdf($suratKeluar);
            $fileName = 'surat-keluar/final-' . $suratKeluar->id . '-' . time() . '.pdf';
            Storage::disk('public')->put($fileName, $pdf->output());

            $suratKeluar->file_final = $fileName;
        } else {
            $suratKeluar->status_ttd = 'belum';
            $suratKeluar->status = 'draft';
        }

        $suratKeluar->save();

        return $suratKeluar;
    }

    /**
     * Proses finalisasi tanda tangan basah.
     */
    public function prosesTtdBasah(SuratKeluar $suratKeluar, ?string $fileFinalPath = null): SuratKeluar
    {
        $suratKeluar->metode_ttd = 'basah';
        $suratKeluar->status_ttd = 'sudah';
        $suratKeluar->status = 'final';

        if ($fileFinalPath) {
            $suratKeluar->file_final = $fileFinalPath;
        }

        $suratKeluar->save();

        return $suratKeluar;
    }

    /**
     * Siapkan seluruh data lembar surat (dipakai bersama oleh PDF dan pratinjau HTML).
     *
     * @param  \Illuminate\Support\Collection|null  $tandaTangans  Override daftar tanda tangan (key = urutan) untuk pratinjau.
     */
    public function dataLembar(SuratKeluar $suratKeluar, bool $modeHtml = false, $tandaTangans = null): array
    {
        if ($suratKeluar->exists) {
            $suratKeluar->loadMissing(['kategori', 'tandaTangans']);
        }

        $verifikasiUrl = url('/verifikasi/' . $suratKeluar->kode_verifikasi);
        $qrCodeDataUri = $this->generateQrCode($verifikasiUrl);

        $pengaturan = Pengaturan::getAllKeyValues();
        $hasGd = extension_loaded('gd');
        // PNG di Dompdf butuh GD; di peramban (mode HTML) selalu bisa ditampilkan.
        $pngOk = $modeHtml || $hasGd;

        if ($tandaTangans === null) {
            $tandaTangans = $suratKeluar->exists
                ? $suratKeluar->tandaTangans->keyBy('urutan')
                : collect();
        }

        $kopGambar = null;
        if (!empty($pengaturan['kop_gambar'])) {
            $kopGambar = $this->fileToDataUri(storage_path('app/public/' . $pengaturan['kop_gambar']), $pngOk);
        } elseif (file_exists(public_path('images/kop.png'))) {
            $kopGambar = $this->fileToDataUri(public_path('images/kop.png'), $pngOk);
        }

        // Logo kop: logo dari Pengaturan, cadangan public/images/logo.png
        $logo = null;
        if (!empty($pengaturan['logo'])) {
            $logo = $this->fileToDataUri(storage_path('app/public/' . $pengaturan['logo']), $pngOk);
        }
        if (! $logo) {
            $logo = $this->fileToDataUri(public_path('images/logo.png'), $pngOk);
        }

        // Berkas Lampiran Gambar (jika file berupa gambar JPG/PNG)
        $lampiranGambar = null;
        $lampiranNama = null;
        if (!empty($suratKeluar->file)) {
            $lampiranPath = storage_path('app/public/' . $suratKeluar->file);
            if (file_exists($lampiranPath)) {
                $lampiranNama = basename($suratKeluar->file);
                $lampiranGambar = $this->fileToDataUri($lampiranPath, $pngOk);
            }
        }

        return [
            'surat' => $suratKeluar,
            'pengaturan' => $pengaturan,
            'qrCode' => $qrCodeDataUri,
            'verifikasiUrl' => $verifikasiUrl,
            'tandaTangans' => $tandaTangans,
            'kopGambar' => $kopGambar,
            'logo' => $logo,
            'lampiranGambar' => $lampiranGambar,
            'lampiranNama' => $lampiranNama,
            'modeHtml' => $modeHtml,
        ];
    }

    /**
     * Ubah berkas gambar menjadi data URI (null jika bukan gambar / tidak didukung).
     */
    protected function fileToDataUri(string $path, bool $pngOk): ?string
    {
        if (! is_file($path)) {
            return null;
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $mimes = ['png' => 'image/png', 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'webp' => 'image/webp'];

        if (! isset($mimes[$ext]) || ($ext === 'png' && ! $pngOk)) {
            return null;
        }

        return 'data:' . $mimes[$ext] . ';base64,' . base64_encode(file_get_contents($path));
    }

    /**
     * Render PDF surat keluar ber-kop pondok pesantren.
     */
    public function renderPdf(SuratKeluar $suratKeluar): \Barryvdh\DomPDF\PDF
    {
        $paperSize = ($suratKeluar->ukuran_kertas === 'f4') ? [0, 0, 609.45, 935.43] : 'a4';

        return Pdf::loadView('surat-keluar.pdf', $this->dataLembar($suratKeluar))
            ->setPaper($paperSize, 'portrait');
    }

    /**
     * Render lembar surat sebagai HTML, identik dengan PDF (untuk pratinjau sebelum jadi PDF).
     *
     * @param  \Illuminate\Support\Collection|null  $tandaTangans
     */
    public function renderHtml(SuratKeluar $suratKeluar, $tandaTangans = null): string
    {
        return view('surat-keluar.pdf', $this->dataLembar($suratKeluar, true, $tandaTangans))->render();
    }
}
