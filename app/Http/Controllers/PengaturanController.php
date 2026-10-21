<?php

namespace App\Http\Controllers;

use App\Models\KategoriSurat;
use App\Models\Pengaturan;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PengaturanController extends Controller
{
    /**
     * Tampilkan halaman formulir pengaturan sistem & status backup harian.
     */
    public function index(BackupService $backupService): View
    {
        $pengaturans = Pengaturan::getAllKeyValues();
        $kategoriSurats = KategoriSurat::latest()->get();
        $backupInfo = $backupService->ensureDailyBackup();

        return view('pengaturan.index', compact('pengaturans', 'kategoriSurats', 'backupInfo'));
    }

    /**
     * Simpan perubahan pengaturan sistem.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_situs' => ['required', 'string', 'max:255'],
            'nama_lembaga' => ['required', 'string', 'max:255'],
            'kode_lembaga' => ['required', 'string', 'max:50'],
            'pola_nomor_surat' => ['required', 'string', 'max:255'],
            'kop_tipe' => ['nullable', 'in:gambar,teks'],
            'kop_yayasan' => ['nullable', 'string', 'max:255'],
            'kop_nama' => ['nullable', 'string', 'max:255'],
            'kop_alamat' => ['nullable', 'string', 'max:500'],
            'kop_kontak' => ['nullable', 'string', 'max:255'],
            'kop_gambar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'footer_teks' => ['nullable', 'string', 'max:500'],
            'warna_tema' => ['nullable', 'string', 'max:20'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:100'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'ukuran_kertas' => ['nullable', 'in:a4,f4'],
            'orientasi_kertas' => ['nullable', 'in:portrait'],
            'margin_kertas' => ['nullable', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
        ], [
            'nama_situs.required' => 'Nama situs wajib diisi.',
            'nama_lembaga.required' => 'Nama lembaga wajib diisi.',
            'kode_lembaga.required' => 'Kode lembaga wajib diisi.',
            'pola_nomor_surat.required' => 'Pola nomor surat wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'logo.image' => 'File logo harus berupa gambar.',
            'logo.max' => 'Ukuran file logo maksimal 5 MB.',
            'kop_gambar.image' => 'File gambar kop harus berupa format gambar (PNG/JPG).',
            'kop_gambar.max' => 'Ukuran file gambar kop maksimal 5 MB.',
        ]);

        foreach ($validated as $key => $value) {
            if (in_array($key, ['logo', 'kop_gambar'])) {
                continue;
            }
            Pengaturan::set($key, $value);
        }

        if ($request->hasFile('logo')) {
            $oldLogo = Pengaturan::get('logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $logoPath = $request->file('logo')->store('pengaturan', 'public');
            Pengaturan::set('logo', $logoPath);
        }

        if ($request->hasFile('kop_gambar')) {
            $oldKop = Pengaturan::get('kop_gambar');
            if ($oldKop && Storage::disk('public')->exists($oldKop)) {
                Storage::disk('public')->delete($oldKop);
            }
            $kopPath = $request->file('kop_gambar')->store('pengaturan', 'public');
            Pengaturan::set('kop_gambar', $kopPath);
            Pengaturan::set('kop_tipe', 'gambar');
        } elseif ($request->input('hapus_kop_gambar') === '1') {
            $oldKop = Pengaturan::get('kop_gambar');
            if ($oldKop && Storage::disk('public')->exists($oldKop)) {
                Storage::disk('public')->delete($oldKop);
            }
            Pengaturan::set('kop_gambar', null);
            Pengaturan::set('kop_tipe', 'teks');
        }

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }

    /**
     * Buat / perbarui 1 file backup terbaru (Backupddmmyy.zip) dan hapus semua backup lama.
     */
    public function backupNow(BackupService $backupService): RedirectResponse
    {
        $info = $backupService->createSingleLatestBackup();

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Backup terbaru (' . $info['filename'] . ') berhasil dibuat dan backup lama telah dihapus otomatis.');
    }

    /**
     * Unduh 1 file backup terbaru (Backupddmmyy.zip).
     */
    public function downloadBackup(BackupService $backupService): BinaryFileResponse
    {
        $info = $backupService->ensureDailyBackup();

        return response()->download($info['path'], $info['filename']);
    }

    /**
     * Pulihkan database dari file backup yang diunggah atau dari backup server terbaru.
     */
    public function restoreBackup(Request $request, BackupService $backupService): RedirectResponse
    {
        if ($request->input('sumber') === 'server') {
            try {
                $backupService->restoreFromLatestBackup();

                return redirect()
                    ->route('admin.pengaturan.index')
                    ->with('success', 'Database berhasil dipulihkan dari file backup server terbaru.');
            } catch (\Throwable $e) {
                return redirect()
                    ->route('admin.pengaturan.index')
                    ->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
            }
        }

        $request->validate([
            'file_backup' => ['required', 'file', 'max:20480'],
        ], [
            'file_backup.required' => 'Pilih file backup yang ingin dipulihkan.',
            'file_backup.file' => 'File backup harus berupa file yang valid.',
            'file_backup.max' => 'Ukuran file backup maksimal 20 MB.',
        ]);

        $file = $request->file('file_backup');
        $ext = strtolower($file->getClientOriginalExtension());

        if (! in_array($ext, ['zip', 'sql', 'txt'])) {
            return redirect()
                ->route('admin.pengaturan.index')
                ->with('error', 'Format file tidak didukung. Harap unggah file berformat .zip atau .sql.');
        }

        try {
            $backupService->restoreFromFile($file->getRealPath(), $ext);

            return redirect()
                ->route('admin.pengaturan.index')
                ->with('success', 'Database sistem berhasil dipulihkan dari file ' . $file->getClientOriginalName() . '.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.pengaturan.index')
                ->with('error', 'Gagal memulihkan database: ' . $e->getMessage());
        }
    }
}
