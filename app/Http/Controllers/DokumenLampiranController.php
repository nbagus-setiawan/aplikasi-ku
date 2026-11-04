<?php

namespace App\Http\Controllers;

use App\Models\Arsip;
use App\Models\DokumenLampiran;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use App\Services\DocxSuratParserService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DokumenLampiranController extends Controller
{
    public function __construct(
        protected DocxSuratParserService $docxParserService
    ) {}

    /**
     * Cari model induk berdasarkan tipe dan ID.
     */
    protected function resolveAttachable(string $type, int|string $id): ?Model
    {
        return match ($type) {
            'surat-masuk' => SuratMasuk::find($id),
            'surat-keluar' => SuratKeluar::find($id),
            'arsip' => Arsip::find($id),
            default => null,
        };
    }

    /**
     * Dapatkan nama folder penyimpanan berdasarkan tipe.
     */
    protected function getStorageFolder(string $type): string
    {
        return match ($type) {
            'surat-masuk' => 'surat-masuk-lampiran',
            'surat-keluar' => 'surat-keluar-lampiran',
            'arsip' => 'arsip-lampiran',
            default => 'lampiran-dokumen',
        };
    }

    /**
     * Simpan satu atau banyak berkas lampiran baru untuk dokumen terkait.
     */
    public function store(Request $request, string $type, int|string $id)
    {
        $attachable = $this->resolveAttachable($type, $id);
        if (! $attachable) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Dokumen induk tidak ditemukan.'], 404);
            }
            return back()->with('error', 'Dokumen induk tidak ditemukan.');
        }

        $request->validate([
            'lampiran_berkas' => ['required', 'array', 'min:1'],
            'lampiran_berkas.*' => ['file', 'mimes:pdf,docx,doc,jpg,jpeg,png,webp', 'max:10240'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ], [
            'lampiran_berkas.required' => 'Silakan pilih berkas lampiran yang akan diunggah.',
            'lampiran_berkas.*.file' => 'Berkas lampiran tidak valid.',
            'lampiran_berkas.*.mimes' => 'Format berkas harus berupa PDF, Word (.docx/.doc), atau Gambar (JPG/PNG/WEBP).',
            'lampiran_berkas.*.max' => 'Ukuran masing-masing berkas maksimal 10 MB.',
        ]);

        $folder = $this->getStorageFolder($type);
        Storage::disk('public')->makeDirectory($folder);

        $savedList = [];
        $files = $request->file('lampiran_berkas');
        $keteranganInput = $request->input('keterangan');

        foreach ($files as $file) {
            if (! $file || ! $file->isValid()) {
                continue;
            }

            $namaAsli = $file->getClientOriginalName();
            $ext = strtolower($file->getClientOriginalExtension()) ?: 'pdf';
            $fileName = Str::uuid() . '.' . $ext;
            $path = $file->storeAs($folder, $fileName, 'public');

            $lampiran = $attachable->lampiranBerkas()->create([
                'nama_berkas' => $namaAsli,
                'path_berkas' => $path,
                'mime_type' => $file->getClientMimeType() ?: $file->getMimeType(),
                'ukuran_byte' => $file->getSize() ?: 0,
                'keterangan' => $keteranganInput,
            ]);

            $savedList[] = [
                'id' => $lampiran->id,
                'nama_berkas' => $lampiran->nama_berkas,
                'ukuran' => $lampiran->ukuran_formatted,
                'ekstensi' => $lampiran->ekstensi,
                'badge' => $lampiran->tipe_badge,
                'url' => $lampiran->url,
                'unduh_url' => route('admin.dokumen-lampiran.unduh', $lampiran),
                'hapus_url' => route('admin.dokumen-lampiran.destroy', $lampiran),
            ];
        }

        $totalUploaded = count($savedList);
        $pesan = "{$totalUploaded} berkas lampiran berhasil diunggah.";

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $pesan,
                'total' => $totalUploaded,
                'lampirans' => $savedList,
            ]);
        }

        return back()->with('success', $pesan);
    }

    /**
     * Unduh satu berkas lampiran fisik dengan nama berkas asli.
     */
    public function unduh(DokumenLampiran $lampiran)
    {
        if (! $lampiran->path_berkas || ! Storage::disk('public')->exists($lampiran->path_berkas)) {
            return back()->with('error', 'Berkas lampiran fisik tidak ditemukan di penyimpanan server.');
        }

        $filename = $lampiran->nama_berkas ?: basename($lampiran->path_berkas);
        return Storage::disk('public')->download($lampiran->path_berkas, $filename);
    }

    /**
     * Unduh semua berkas lampiran sekaligus dalam format ZIP.
     */
    public function unduhSemua(string $type, int|string $id)
    {
        $attachable = $this->resolveAttachable($type, $id);
        if (! $attachable) {
            return back()->with('error', 'Dokumen tidak ditemukan.');
        }

        $lampiranList = $attachable->lampiranBerkas;
        $zipEntries = [];
        $usedNames = [];

        // Masukkan berkas dari tabel dokumen_lampirans
        foreach ($lampiranList as $lampiran) {
            if ($lampiran->path_berkas && Storage::disk('public')->exists($lampiran->path_berkas)) {
                $baseName = $lampiran->nama_berkas ?: basename($lampiran->path_berkas);
                $uniqueName = $this->getUniqueZipFilename($baseName, $usedNames);
                $usedNames[] = $uniqueName;
                $zipEntries[$uniqueName] = Storage::disk('public')->get($lampiran->path_berkas);
            }
        }

        // Masukkan berkas utama lama jika ada dan belum masuk
        $legacyFiles = match ($type) {
            'surat-masuk' => [
                'surat_masuk_asli' => $attachable->file_surat_asli,
                'surat_masuk' => $attachable->file,
                'lampiran_lama' => $attachable->file_lampiran,
            ],
            'surat-keluar' => [
                'surat_keluar_final' => $attachable->file_final,
                'surat_keluar_asli' => $attachable->file_surat_asli,
                'lampiran_lama' => $attachable->file,
            ],
            'arsip' => [
                'dokumen_arsip' => $attachable->file,
            ],
            default => [],
        };

        foreach ($legacyFiles as $label => $lPath) {
            if ($lPath && Storage::disk('public')->exists($lPath)) {
                $ext = pathinfo($lPath, PATHINFO_EXTENSION);
                $baseName = Str::slug($label) . '.' . $ext;
                $uniqueName = $this->getUniqueZipFilename($baseName, $usedNames);
                $usedNames[] = $uniqueName;
                $zipEntries[$uniqueName] = Storage::disk('public')->get($lPath);
            }
        }

        if (empty($zipEntries)) {
            return back()->with('error', 'Tidak ada berkas berkas lampiran yang tersedia untuk diunduh.');
        }

        $zipFileName = 'lampiran-' . Str::slug($type . '-' . $id) . '-' . date('Ymd-His') . '.zip';
        $tempZipPath = rtrim(sys_get_temp_dir(), '/') . '/' . $zipFileName;

        $created = $this->docxParserService->writeZipEntries($tempZipPath, $zipEntries);
        if (! $created || ! is_file($tempZipPath)) {
            return back()->with('error', 'Gagal memaketkan berkas lampiran ke format ZIP.');
        }

        return response()->download($tempZipPath, $zipFileName)->deleteFileAfterSend(true);
    }

    /**
     * Hapus berkas lampiran satuan (AJAX & konfirmasi warna merah).
     */
    public function destroy(Request $request, DokumenLampiran $lampiran)
    {
        $nama = $lampiran->nama_berkas;

        // Hapus berkas fisik
        $lampiran->hapusBerkasFisik();

        // Hapus dari basis data
        $lampiran->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Berkas lampiran '{$nama}' berhasil dihapus.",
            ]);
        }

        return back()->with('success', "Berkas lampiran '{$nama}' berhasil dihapus.");
    }

    /**
     * Helper nama file unik dalam arsip zip.
     */
    protected function getUniqueZipFilename(string $name, array $existing): string
    {
        if (! in_array($name, $existing, true)) {
            return $name;
        }

        $info = pathinfo($name);
        $fn = $info['filename'];
        $ext = ! empty($info['extension']) ? '.' . $info['extension'] : '';
        $i = 1;

        while (in_array("{$fn}_{$i}{$ext}", $existing, true)) {
            $i++;
        }

        return "{$fn}_{$i}{$ext}";
    }
}
