<?php

namespace App\Http\Controllers;

use App\Services\SantriImportService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SantriImportController extends Controller
{
    protected SantriImportService $importService;

    public function __construct(SantriImportService $importService)
    {
        $this->importService = $importService;
    }

    /**
     * Unduh / ekspor seluruh data santri (atau sesuai filter) dalam format CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $csvContent = $this->importService->exportCsv($request->all());
        $filename = 'data-santri-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Unduh template file CSV/Excel untuk data santri.
     */
    public function template(): StreamedResponse
    {
        $csvContent = $this->importService->generateTemplateCsv();

        return response()->streamDownload(function () use ($csvContent) {
            echo $csvContent;
        }, 'template-impor-santri.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="template-impor-santri.csv"',
        ]);
    }

    /**
     * Proses file impor santri yang diunggah.
     */
    public function import(Request $request): Response
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
            ->route('admin.santri.index')
            ->with('import_result', $result)
            ->with('success', "Proses impor selesai: {$result['sukses']} data berhasil ditambahkan, {$result['gagal']} baris gagal.");
    }
}
