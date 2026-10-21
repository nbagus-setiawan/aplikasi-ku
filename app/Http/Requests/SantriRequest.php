<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SantriRequest extends FormRequest
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
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $santriId = $this->route('santri') ? $this->route('santri')->id : null;

        return [
            'nis' => [
                'required',
                'string',
                'max:50',
                Rule::unique('santris', 'nis')->ignore($santriId)->whereNull('deleted_at'),
            ],
            'nama' => ['required', 'string', 'max:255'],
            'tempat_lahir' => ['nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['nullable', 'date'],
            'alamat' => ['nullable', 'string', 'max:1000'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
            'hp_wali' => ['nullable', 'string', 'max:50'],
            'kamar' => ['nullable', 'string', 'max:100'],
            'kelas' => ['nullable', 'string', 'max:100'],
            'tahun_masuk' => ['nullable', 'string', 'max:10'],
            'status' => ['required', 'string', Rule::in(['aktif', 'lulus', 'keluar', 'pindah'])],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'], // maks 5MB
        ];
    }

    /**
     * Pesan validasi dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'nis.required' => 'Nomor Induk Santri (NIS) wajib diisi.',
            'nis.unique' => 'NIS sudah terdaftar dalam sistem.',
            'nama.required' => 'Nama lengkap santri wajib diisi.',
            'status.required' => 'Status santri wajib dipilih.',
            'status.in' => 'Status santri tidak valid.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ];
    }
}
