<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratMasukRequest extends FormRequest
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
        $isWordUpload = $this->hasFile('file_surat_asli');

        return [
            'no_surat' => [$isWordUpload ? 'nullable' : 'required', 'string', 'max:255'],
            'tgl_surat' => [$isWordUpload ? 'nullable' : 'required', 'date'],
            'tgl_diterima' => [$isWordUpload ? 'nullable' : 'required', 'date'],
            'tempat_surat' => ['nullable', 'string', 'max:100'],
            'pengirim' => [$isWordUpload ? 'nullable' : 'required', 'string', 'max:255'],
            'perihal' => [$isWordUpload ? 'nullable' : 'required', 'string', 'max:255'],
            'lampiran_teks' => ['nullable', 'string', 'max:100'],
            'kategori_id' => ['nullable', 'exists:kategori_surats,id'],
            'file_surat_asli' => ['nullable', 'file', 'extensions:docx,doc,pdf,jpg,jpeg,png,txt,html,htm', 'max:5120'],
            'file' => ['nullable', 'file', 'extensions:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'file_lampiran' => ['nullable', 'file', 'extensions:pdf,jpg,jpeg,png,doc,docx', 'max:5120'],
            'disposisi' => ['nullable', 'string'],
            'status_disposisi' => [$isWordUpload ? 'nullable' : 'required', 'in:baru,diproses,selesai'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'no_surat.required' => 'Nomor surat wajib diisi.',
            'tgl_surat.required' => 'Tanggal surat wajib diisi.',
            'tgl_surat.date' => 'Format tanggal surat tidak valid.',
            'tgl_diterima.required' => 'Tanggal diterima wajib diisi.',
            'tgl_diterima.date' => 'Format tanggal diterima tidak valid.',
            'pengirim.required' => 'Nama atau lembaga pengirim wajib diisi.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'kategori_id.exists' => 'Kategori surat yang dipilih tidak valid.',
            'file_surat_asli.extensions' => 'File surat harus berformat Word (.docx/.doc), PDF, atau Gambar (JPG/PNG).',
            'file_surat_asli.max' => 'Ukuran file surat maksimal 5 MB.',
            'file.file' => 'Berkas harus berupa dokumen atau gambar yang sah.',
            'file.extensions' => 'Berkas lampiran harus berformat PDF, JPG, PNG, atau Word (DOC/DOCX).',
            'file.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
            'file_lampiran.extensions' => 'Berkas lampiran harus berformat PDF, JPG, PNG, atau Word (DOC/DOCX).',
            'file_lampiran.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
            'status_disposisi.required' => 'Status disposisi wajib ditentukan.',
            'status_disposisi.in' => 'Status disposisi harus salah satu dari: Baru, Diproses, atau Selesai.',
        ];
    }
}

