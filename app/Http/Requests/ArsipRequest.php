<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArsipRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $isCreate = $this->isMethod('post');

        return [
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'string', 'max:255'],
            'tahun' => ['required', 'integer', 'min:1950', 'max:2100'],
            'file' => [$isCreate ? 'required' : 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'keterangan' => ['nullable', 'string'],
            'surat_masuk_id' => ['nullable', 'exists:surat_masuks,id'],
            'surat_keluar_id' => ['nullable', 'exists:surat_keluars,id'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul dokumen arsip wajib diisi.',
            'kategori.required' => 'Kategori arsip wajib diisi.',
            'tahun.required' => 'Tahun dokumen wajib diisi.',
            'tahun.integer' => 'Tahun dokumen harus berupa angka tahun yang sah.',
            'file.required' => 'Berkas dokumen wajib diunggah.',
            'file.file' => 'Berkas harus berupa dokumen atau gambar yang sah.',
            'file.mimes' => 'Berkas harus berformat PDF, JPG, PNG, atau Word (DOC/DOCX).',
            'file.max' => 'Ukuran berkas maksimal 5 MB.',
            'surat_masuk_id.exists' => 'Surat masuk yang ditautkan tidak ditemukan.',
            'surat_keluar_id.exists' => 'Surat keluar yang ditautkan tidak ditemukan.',
        ];
    }
}
