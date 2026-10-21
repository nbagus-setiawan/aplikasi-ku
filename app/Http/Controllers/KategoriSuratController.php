<?php

namespace App\Http\Controllers;

use App\Models\KategoriSurat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KategoriSuratController extends Controller
{
    /**
     * Simpan jenis/kategori surat baru.
     */
    public function store(Request $request): \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', 'unique:kategori_surats,kode'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'nama.required' => 'Nama jenis surat wajib diisi.',
            'kode.required' => 'Kode surat wajib diisi.',
            'kode.unique' => 'Kode surat sudah digunakan.',
        ]);

        $kategori = KategoriSurat::create($validated);

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Jenis surat baru berhasil ditambahkan.',
                'data' => $kategori,
            ], 201);
        }

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Jenis surat baru berhasil ditambahkan.');
    }

    /**
     * Perbarui jenis/kategori surat.
     */
    public function update(Request $request, KategoriSurat $kategoriSurat): RedirectResponse
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'kode' => ['required', 'string', 'max:50', 'unique:kategori_surats,kode,' . $kategoriSurat->id],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ], [
            'nama.required' => 'Nama jenis surat wajib diisi.',
            'kode.required' => 'Kode surat wajib diisi.',
            'kode.unique' => 'Kode surat sudah digunakan.',
        ]);

        $kategoriSurat->update($validated);

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Jenis surat berhasil diperbarui.');
    }

    /**
     * Hapus jenis/kategori surat.
     */
    public function destroy(KategoriSurat $kategoriSurat): RedirectResponse
    {
        $kategoriSurat->delete();

        return redirect()
            ->route('admin.pengaturan.index')
            ->with('success', 'Jenis surat berhasil dihapus.');
    }
}
