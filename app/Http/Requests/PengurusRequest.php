<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PengurusRequest extends FormRequest
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
        return [
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'divisi' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'file', 'extensions:jpeg,png,jpg,webp', 'max:5120'],
            'no_hp' => ['nullable', 'string', 'max:50'],
            'periode_mulai' => ['nullable', 'string', 'max:20'],
            'periode_selesai' => ['nullable', 'string', 'max:20'],
            'is_aktif' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Pesan validasi dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pengurus wajib diisi.',
            'jabatan.required' => 'Jabatan pengurus wajib diisi.',
            'foto.extensions' => 'Format foto harus berupa JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 5 MB.',
        ];
    }
}
