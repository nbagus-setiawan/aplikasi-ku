<?php

namespace App\Http\Controllers;

use App\Http\Requests\PengurusRequest;
use App\Models\Pengurus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PengurusController extends Controller
{
    /**
     * Tampilkan daftar pengurus per periode dan struktur kepengurusan.
     */
    public function index(Request $request): View
    {
        $query = Pengurus::query();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('jabatan', 'like', "%{$search}%")
                  ->orWhere('divisi', 'like', "%{$search}%");
            });
        }

        if ($request->has('is_aktif') && $request->input('is_aktif') !== '') {
            $query->where('is_aktif', (bool) $request->input('is_aktif'));
        }

        if ($periode = $request->input('periode')) {
            $query->where('periode_mulai', $periode);
        }

        $penguruses = $query->orderBy('is_aktif', 'desc')
                            ->orderBy('id', 'asc')
                            ->paginate(15)
                            ->withQueryString();

        // Opsi daftar periode kepengurusan unik
        $listPeriode = Pengurus::whereNotNull('periode_mulai')
                               ->distinct()
                               ->pluck('periode_mulai')
                               ->sortDesc();

        $totalAktif = Pengurus::where('is_aktif', true)->count();
        $totalSemua = Pengurus::count();

        // Kelompokan pengurus aktif berdasarkan divisi untuk tampilan bagan/struktur
        $strukturPengurus = Pengurus::where('is_aktif', true)
                                   ->get()
                                   ->groupBy(function ($item) {
                                       return $item->divisi ?: 'Pimpinan & Sekretariat';
                                   });

        return view('pengurus.index', compact(
            'penguruses',
            'listPeriode',
            'totalAktif',
            'totalSemua',
            'strukturPengurus'
        ));
    }

    /**
     * Simpan data pengurus baru.
     */
    public function store(PengurusRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif', true);

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $ext = strtolower($foto->getClientOriginalExtension()) ?: 'jpg';
            $filename = \Illuminate\Support\Str::uuid() . '.' . $ext;
            $data['foto'] = $foto->storeAs('foto-pengurus', $filename, 'public');
        }

        Pengurus::create($data);

        return redirect()
            ->route('admin.pengurus.index')
            ->with('success', 'Data pengurus berhasil ditambahkan.');
    }

    /**
     * Perbarui data pengurus.
     */
    public function update(PengurusRequest $request, Pengurus $pengurus): RedirectResponse
    {
        $data = $request->validated();
        $data['is_aktif'] = $request->boolean('is_aktif', false);

        if ($request->hasFile('foto')) {
            if ($pengurus->foto && Storage::disk('public')->exists($pengurus->foto)) {
                Storage::disk('public')->delete($pengurus->foto);
            }
            $foto = $request->file('foto');
            $ext = strtolower($foto->getClientOriginalExtension()) ?: 'jpg';
            $filename = \Illuminate\Support\Str::uuid() . '.' . $ext;
            $data['foto'] = $foto->storeAs('foto-pengurus', $filename, 'public');
        }

        $pengurus->update($data);

        return redirect()
            ->route('admin.pengurus.index')
            ->with('success', 'Data pengurus berhasil diperbarui.');
    }

    /**
     * Hapus data pengurus.
     */
    public function destroy(Pengurus $pengurus): RedirectResponse
    {
        if ($pengurus->foto && Storage::disk('public')->exists($pengurus->foto)) {
            Storage::disk('public')->delete($pengurus->foto);
        }

        $pengurus->delete();

        return redirect()
            ->route('admin.pengurus.index')
            ->with('success', 'Data pengurus berhasil dihapus.');
    }
}
