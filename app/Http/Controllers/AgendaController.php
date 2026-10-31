<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgendaRequest;
use App\Models\Agenda;
use App\Models\Pengurus;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AgendaController extends Controller
{
    /**
     * Tampilkan agenda kegiatan dalam mode kalender bulanan atau daftar tabel.
     */
    public function index(Request $request): View
    {
        $viewMode = $request->get('view', 'daftar');

        // Parameter bulan & tahun untuk kalender
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $currentDate = Carbon::createFromDate($tahun, $bulan, 1);

        // Query agenda untuk kalender bulanan
        $agendasBulan = Agenda::with('pj')
            ->where(function ($q) use ($bulan, $tahun) {
                $q->where(function ($sub) use ($bulan, $tahun) {
                    $sub->whereMonth('mulai', $bulan)->whereYear('mulai', $tahun);
                })->orWhere(function ($sub) use ($bulan, $tahun) {
                    $sub->whereMonth('selesai', $bulan)->whereYear('selesai', $tahun);
                });
            })
            ->orderBy('mulai', 'asc')
            ->get();

        // Query tabel daftar agenda dengan filter dan pencarian
        $query = Agenda::with(['pj', 'user'])->orderBy('mulai', 'desc');

        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('judul', 'like', "%{$q}%")
                    ->orWhere('lokasi', 'like', "%{$q}%")
                    ->orWhere('deskripsi', 'like', "%{$q}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tgl_mulai')) {
            $query->whereDate('mulai', '>=', $request->tgl_mulai);
        }

        if ($request->filled('tgl_selesai')) {
            $query->whereDate('mulai', '<=', $request->tgl_selesai);
        }

        $agendas = $query->paginate(15)->withQueryString();

        // Peringatan H-1
        $agendasBesok = Agenda::hMinusSatu()->with('pj')->get();

        // Statistik Ringkasan Kegiatan
        $statistik = [
            'total' => Agenda::count(),
            'rencana' => Agenda::where('status', 'rencana')->count(),
            'berjalan' => Agenda::where('status', 'berjalan')->count(),
            'selesai' => Agenda::where('status', 'selesai')->count(),
        ];

        return view('agenda.index', compact(
            'viewMode',
            'bulan',
            'tahun',
            'currentDate',
            'agendasBulan',
            'agendas',
            'agendasBesok',
            'statistik'
        ));
    }

    /**
     * Form tambah agenda baru.
     */
    public function create(): View
    {
        $pengurusList = Pengurus::where('is_aktif', true)->orderBy('nama')->get();

        return view('agenda.create', compact('pengurusList'));
    }

    /**
     * Simpan agenda baru.
     */
    public function store(AgendaRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['user_id'] = auth()->id();

        $agenda = Agenda::create($validated);

        return redirect()->route('admin.agenda.show', $agenda)
            ->with('success', 'Agenda kegiatan berhasil ditambahkan.');
    }

    /**
     * Tampilkan detail agenda kegiatan.
     */
    public function show(Agenda $agenda): View
    {
        $agenda->loadMissing(['pj', 'user']);

        return view('agenda.show', compact('agenda'));
    }

    /**
     * Form ubah agenda.
     */
    public function edit(Agenda $agenda): View
    {
        $pengurusList = Pengurus::orderBy('nama')->get();

        return view('agenda.edit', compact('agenda', 'pengurusList'));
    }

    /**
     * Perbarui data agenda kegiatan.
     */
    public function update(AgendaRequest $request, Agenda $agenda): RedirectResponse
    {
        $agenda->update($request->validated());

        return redirect()->route('admin.agenda.show', $agenda)
            ->with('success', 'Agenda kegiatan berhasil diperbarui.');
    }

    /**
     * Hapus agenda kegiatan (Hard Delete / Permanen).
     */
    public function destroy(Agenda $agenda): RedirectResponse
    {
        $agenda->forceDelete();

        return redirect()->route('admin.agenda.index')
            ->with('success', 'Agenda kegiatan berhasil dihapus.');
    }
}
