<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratKeluarRequest extends FormRequest
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
        $hasWordFile = $this->hasFile('file_surat_asli');

        return [
            'file_surat_asli' => ['nullable', 'file', 'extensions:docx,doc,pdf,html,htm,txt', 'max:10240'],
            'no_surat_manual' => ['nullable', 'string', 'max:150'],
            'tgl_surat' => [$hasWordFile ? 'nullable' : 'required', 'date'],
            'tempat_surat' => ['nullable', 'string', 'max:100'],
            'tujuan' => [$hasWordFile ? 'nullable' : 'required', 'string', 'max:255'],
            'perihal' => [$hasWordFile ? 'nullable' : 'required', 'string', 'max:255'],
            'lampiran_teks' => ['nullable', 'string', 'max:100'],
            'kategori_id' => ['nullable', 'exists:kategori_surats,id'],
            'kode_jenis_surat' => ['nullable', 'string', 'max:50'],
            'ukuran_kertas' => ['nullable', 'in:a4,f4'],
            'margin' => ['nullable', 'string', 'max:50'],
            'jumlah_penandatangan' => ['nullable', 'integer', 'between:1,3'],
            'penandatangans' => ['nullable', 'array'],
            'metode_ttd_pilihan' => ['nullable', 'in:digital,basah,nanti'],
            'data_ttd_langsung' => ['nullable', 'string'],
            'data_ttd_2' => ['nullable', 'string'],
            'data_ttd_3' => ['nullable', 'string'],
            'isi' => [$hasWordFile ? 'nullable' : 'required', 'string'],
            'nama_penandatangan' => [$hasWordFile ? 'nullable' : 'required', 'string', 'max:255'],
            'jabatan_penandatangan' => [$hasWordFile ? 'nullable' : 'required', 'string', 'max:255'],
            'file' => ['nullable', 'file', 'extensions:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'status' => ['nullable', 'in:draft,final,terkirim'],
            'aksi_simpan' => ['nullable', 'in:draft,cetak,siap_kirim,terbitkan_word'],
            'kode_verifikasi' => [
                'nullable',
                'string',
                'max:8',
                'regex:/^[A-Za-z0-9]+$/',
                'unique:surat_keluars,kode_verifikasi,' . ($this->route('surat_keluar')?->id ?? ''),
            ],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tgl_surat.required' => 'Tanggal surat wajib diisi.',
            'tgl_surat.date' => 'Format tanggal surat tidak valid.',
            'tujuan.required' => 'Tujuan surat wajib diisi.',
            'perihal.required' => 'Perihal surat wajib diisi.',
            'kategori_id.required' => 'Jenis/kategori surat wajib dipilih.',
            'kategori_id.exists' => 'Jenis surat yang dipilih tidak terdaftar.',
            'isi.required' => 'Isi surat wajib diisi.',
            'nama_penandatangan.required' => 'Nama penandatangan wajib diisi.',
            'jabatan_penandatangan.required' => 'Jabatan penandatangan wajib diisi.',
            'file.file' => 'Berkas harus berupa dokumen atau gambar yang sah.',
            'file.mimes' => 'Berkas lampiran harus berformat PDF, JPG, PNG, atau Word (DOC/DOCX).',
            'file.max' => 'Ukuran berkas lampiran maksimal 5 MB.',
            'status.in' => 'Status surat tidak valid.',
            'kode_verifikasi.max' => 'Kode verifikasi maksimal 8 karakter.',
            'kode_verifikasi.regex' => 'Kode verifikasi hanya boleh berisi huruf dan angka.',
            'kode_verifikasi.unique' => 'Kode verifikasi sudah digunakan untuk surat lain.',
        ];
    }
}
