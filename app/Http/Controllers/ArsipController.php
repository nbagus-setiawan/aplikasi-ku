<?php

namespace App\Http\Controllers;

use App\Http\Requests\ArsipRequest;
use App\Models\Arsip;
use App\Models\SuratKeluar;
use App\Models\SuratMasuk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ArsipController extends Controller
{
    /**
     * Tampilkan daftar arsip dokumen dengan filter dan pencarian.
     */
    public function index(Request $request): View
    {
        $query = Arsip::with(['user', 'suratMasuk', 'suratKeluar'])->latest();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('kategori', 'like', "%{$q}%")
                    ->orWhere('keterangan', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('tahun')) {
            $query->where('tahun', $request->tahun);
        }

        $arsips = $query->paginate(15)->withQueryString();

        // Kategori & Tahun unik untuk filter
        $kategoriList = Arsip::distinct()->orderBy('kategori')->pluck('kategori');
        $tahunList = Arsip::distinct()->orderByDesc('tahun')->pluck('tahun');

        // Statistik Ringkasan Arsip
        $statistik = [
            'total' => Arsip::count(),
            'kategori' => Arsip::distinct('kategori')->count('kategori'),
            'tahun_ini' => Arsip::where('tahun', date('Y'))->count(),
            'tertaut' => Arsip::where(function ($q) {
                $q->whereNotNull('surat_masuk_id')->orWhereNotNull('surat_keluar_id');
            })->count(),
        ];

        return view('arsip.index', compact('arsips', 'kategoriList', 'tahunList', 'statistik'));
    }

    /**
     * Form unggah arsip dokumen baru.
     */
    public function create(): View
    {
        $suratMasukList = SuratMasuk::latest('tgl_diterima')->take(50)->get();
        $suratKeluarList = SuratKeluar::latest('tgl_surat')->take(50)->get();

        return view('arsip.create', compact('suratMasukList', 'suratKeluarList'));
    }

    /**
     * Simpan arsip baru beserta file dokumen.
     */
    public function store(ArsipRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension()) ?: 'pdf';
        $filePath = $file->storeAs('arsip', \Illuminate\Support\Str::uuid() . '.' . $ext, 'public');
        $validated['file'] = $filePath;
        $validated['user_id'] = auth()->id();

        $arsip = Arsip::create($validated);

        return redirect()->route('admin.arsip.show', $arsip)
            ->with('success', 'Dokumen arsip berhasil diunggah dan disimpan.');
    }

    /**
     * Tampilkan detail dokumen arsip.
     */
    public function show(Arsip $arsip): View
    {
        $arsip->loadMissing(['user', 'suratMasuk', 'suratKeluar']);

        return view('arsip.show', compact('arsip'));
    }

    /**
     * Form ubah arsip dokumen.
     */
    public function edit(Arsip $arsip): View
    {
        $suratMasukList = SuratMasuk::latest('tgl_diterima')->take(50)->get();
        $suratKeluarList = SuratKeluar::latest('tgl_surat')->take(50)->get();

        return view('arsip.edit', compact('arsip', 'suratMasukList', 'suratKeluarList'));
    }

    /**
     * Perbarui data arsip.
     */
    public function update(ArsipRequest $request, Arsip $arsip): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('file')) {
            if ($arsip->file && Storage::disk('public')->exists($arsip->file)) {
                Storage::disk('public')->delete($arsip->file);
            }
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension()) ?: 'pdf';
            $validated['file'] = $file->storeAs('arsip', \Illuminate\Support\Str::uuid() . '.' . $ext, 'public');
        }

        $arsip->update($validated);

        return redirect()->route('admin.arsip.show', $arsip)
            ->with('success', 'Data dokumen arsip berhasil diperbarui.');
    }

    /**
     * Unduh berkas fisik arsip.
     */
    public function unduh(Arsip $arsip): \Symfony\Component\HttpFoundation\Response|RedirectResponse
    {
        if (! Storage::disk('public')->exists($arsip->file)) {
            return back()->with('error', 'Berkas fisik dokumen tidak ditemukan di penyimpanan.');
        }

        $extension = pathinfo($arsip->file, PATHINFO_EXTENSION);
        $downloadName = \Illuminate\Support\Str::slug($arsip->judul) . '.' . $extension;

        return Storage::disk('public')->download($arsip->file, $downloadName);
    }

    /**
     * Hapus arsip dokumen (soft delete).
     */
    public function destroy(Arsip $arsip): RedirectResponse
    {
        $arsip->delete();

        return redirect()->route('admin.arsip.index')
            ->with('success', 'Dokumen arsip berhasil dihapus.');
    }
}
