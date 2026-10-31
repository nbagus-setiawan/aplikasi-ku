<?php

namespace App\Http\Controllers;

use App\Http\Requests\SantriRequest;
use App\Models\Pengaturan;
use App\Models\Santri;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SantriController extends Controller
{
    /**
     * Tampilkan daftar santri dengan pencarian, filter, dan pagination.
     */
    public function index(Request $request): View
    {
        $query = Santri::query();

        // Pencarian nama atau NIS
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        // Filter kelas
        if ($kelas = $request->input('kelas')) {
            $query->where('kelas', $kelas);
        }

        // Filter kamar
        if ($kamar = $request->input('kamar')) {
            $query->where('kamar', $kamar);
        }

        $santris = $query->latest()->paginate(15)->withQueryString();

        // Data untuk opsi filter
        $listKelas = Santri::whereNotNull('kelas')->where('kelas', '!=', '')->distinct()->pluck('kelas')->sort();
        $listKamar = Santri::whereNotNull('kamar')->where('kamar', '!=', '')->distinct()->pluck('kamar')->sort();
        $totalAktif = Santri::where('status', 'aktif')->count();
        $totalSemua = Santri::count();

        // Simpan URL filter aktif ke session agar setelah edit/hapus tetap berada di filter & halaman yang sama
        session(['santri_filter_url' => $request->fullUrl()]);

        return view('santri.index', compact(
            'santris',
            'listKelas',
            'listKamar',
            'totalAktif',
            'totalSemua'
        ));
    }

    /**
     * Tampilkan formulir tambah santri.
     */
    public function create(): View
    {
        return view('santri.create');
    }

    /**
     * Simpan data santri baru.
     */
    public function store(SantriRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $foto = $request->file('foto');
            $ext = strtolower($foto->getClientOriginalExtension()) ?: 'jpg';
            $filename = \Illuminate\Support\Str::uuid() . '.' . $ext;
            $data['foto'] = $foto->storeAs('foto-santri', $filename, 'public');
        }

        Santri::create($data);

        return redirect()
            ->route('admin.santri.index')
            ->with('success', 'Data santri berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail biodata santri.
     */
    public function show(Request $request, Santri $santri): View
    {
        $returnUrl = $request->query('return_url', session('santri_filter_url', route('admin.santri.index')));
        return view('santri.show', compact('santri', 'returnUrl'));
    }

    /**
     * Tampilkan formulir edit santri.
     */
    public function edit(Request $request, Santri $santri): View
    {
        $returnUrl = $request->query('return_url', session('santri_filter_url', route('admin.santri.index')));
        return view('santri.edit', compact('santri', 'returnUrl'));
    }

    /**
     * Perbarui data santri.
     */
    public function update(SantriRequest $request, Santri $santri): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
                Storage::disk('public')->delete($santri->foto);
            }
            $foto = $request->file('foto');
            $ext = strtolower($foto->getClientOriginalExtension()) ?: 'jpg';
            $filename = \Illuminate\Support\Str::uuid() . '.' . $ext;
            $data['foto'] = $foto->storeAs('foto-santri', $filename, 'public');
        }

        $santri->update($data);

        // Arahkan kembali ke data yang sedang difilter (bukan reset ke semua data)
        $returnUrl = $request->input('return_url')
            ?: session('santri_filter_url')
            ?: route('admin.santri.index');

        return redirect($returnUrl)
            ->with('success', 'Data santri berhasil diperbarui.');
    }

    /**
     * Hapus data santri (Hard Delete / Permanen).
     */
    public function destroy(Santri $santri): RedirectResponse
    {
        if ($santri->foto && Storage::disk('public')->exists($santri->foto)) {
            Storage::disk('public')->delete($santri->foto);
        }

        $santri->forceDelete();

        // Tetap berada pada filter aktif setelah menghapus
        $returnUrl = session('santri_filter_url') ?: route('admin.santri.index');

        return redirect($returnUrl)
            ->with('success', 'Data santri berhasil dihapus.');
    }

    /**
     * Cetak daftar rekap santri ke file PDF ber-kop pondok.
     */
    public function cetakDaftar(Request $request): Response
    {
        $query = Santri::query();

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($kelas = $request->input('kelas')) {
            $query->where('kelas', $kelas);
        }

        if ($kamar = $request->input('kamar')) {
            $query->where('kamar', $kamar);
        }

        $santris = $query->orderBy('nama', 'asc')->get();
        $pengaturans = Pengaturan::getAllKeyValues();

        $pdf = Pdf::loadView('santri.pdf-daftar', compact('santris', 'pengaturans'))
                  ->setPaper('a4', 'landscape');

        return $pdf->stream('daftar-santri-' . date('Ymd-His') . '.pdf');
    }

    /**
     * Cetak kartu biodata perorangan santri ke PDF.
     */
    public function cetakBiodata(Santri $santri): Response
    {
        $pengaturans = Pengaturan::getAllKeyValues();

        $pdf = Pdf::loadView('santri.pdf-biodata', compact('santri', 'pengaturans'))
                  ->setPaper('a4', 'portrait');

        return $pdf->stream("biodata-santri-{$santri->nis}.pdf");
    }
}
