<?php

namespace App\Http\Controllers;

use App\Services\PengurusImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PengurusImportController extends Controller
{
    protected PengurusImportService $importService;

    public function __construct(PengurusImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Unduh / ekspor seluruh data pengurus (atau sesuai filter) dalam format CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $csvContent = $this->importService->exportCsv($request->all());
        $filename = 'data-pengurus-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Unduh template file CSV untuk pengisian data pengurus.
     */
    public function template(): StreamedResponse
    {
        $csvContent = $this->importService->generateTemplateCsv();
        $filename = 'template-impor-pengurus.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Proses file impor pengurus yang diunggah.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // maks 5MB
        ], [
            'file.required' => 'Silakan pilih file impor terlebih dahulu.',
            'file.mimes' => 'Format file harus berupa CSV (.csv).',
            'file.max' => 'Ukuran file maksimal 5 MB.',
        ]);

        $result = $this->importService->import($request->file('file'));

        return redirect()
            ->route('admin.pengurus.index')
            ->with('import_result', $result)
            ->with('success', "Proses impor selesai: {$result['sukses']} data berhasil ditambahkan, {$result['gagal']} baris gagal.");
    }
}
