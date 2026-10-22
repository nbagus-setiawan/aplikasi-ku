<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengumumanRequest;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengumumanController extends Controller
{
    /**
     * Tampilkan daftar pengumuman untuk admin.
     */
    public function index(Request $request)
    {
        $query = Pengumuman::query();

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('judul', 'like', "%{$q}%")
                    ->orWhere('isi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $pengumumans = $query->orderByDesc('is_pinned')
            ->orderByDesc('tgl_tayang')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('pengumuman.index', compact('pengumumans'));
    }

    /**
     * Tampilkan formulir pembuatan pengumuman baru.
     */
    public function create()
    {
        return view('pengumuman.create');
    }

    /**
     * Simpan pengumuman baru ke database.
     */
    public function store(PengumumanRequest $request)
    {
        $data = $request->validated();

        // Generate slug bila tidak diisi manual
        $baseSlug = $request->filled('slug') ? Str::slug($request->slug) : Str::slug($request->judul);
        $slug = $baseSlug;
        $counter = 1;
        while (Pengumuman::where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }
        $data['slug'] = $slug;

        // Atur boolean is_pinned
        $data['is_pinned'] = $request->boolean('is_pinned');

        try {
            // Handle upload gambar jika ada
            if ($request->hasFile('gambar')) {
                Storage::disk('public')->makeDirectory('pengumuman');
                $path = $request->file('gambar')->store('pengumuman', 'public');
                $data['gambar'] = $path;
            }

            $data['user_id'] = Auth::id();

            Pengumuman::create($data);

            return redirect()->route('admin.pengumuman.index')
                ->with('success', 'Pengumuman baru berhasil disimpan.');
        } catch (\Throwable $e) {
            \Log::error('Gagal menyimpan pengumuman: ' . $e->getMessage(), ['exception' => $e]);

            return back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan pengumuman ke server: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Tampilkan detail pengumuman.
     */
    public function show(Pengumuman $pengumuman)
    {
        return view('pengumuman.show', compact('pengumuman'));
    }

    /**
     * Tampilkan formulir edit pengumuman.
     */
    public function edit(Pengumuman $pengumuman)
    {
        return view('pengumuman.edit', compact('pengumuman'));
    }

    /**
     * Perbarui pengumuman di database.
     */
    public function update(PengumumanRequest $request, Pengumuman $pengumuman)
    {
        $data = $request->validated();

        if ($request->filled('slug') && $request->slug !== $pengumuman->slug) {
            $baseSlug = Str::slug($request->slug);
            $slug = $baseSlug;
            $counter = 1;
            while (Pengumuman::where('slug', $slug)->where('id', '!=', $pengumuman->id)->exists()) {
                $slug = "{$baseSlug}-{$counter}";
                $counter++;
            }
            $data['slug'] = $slug;
        }

        $data['is_pinned'] = $request->boolean('is_pinned');

        if ($request->hasFile('gambar')) {
            if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
                Storage::disk('public')->delete($pengumuman->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('pengumuman', 'public');
        }

        $pengumuman->update($data);

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil diperbarui.');
    }

    /**
     * Hapus pengumuman (soft delete).
     */
    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();

        return redirect()->route('admin.pengumuman.index')
            ->with('success', 'Pengumuman berhasil dihapus.');
    }
}
