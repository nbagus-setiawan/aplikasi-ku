<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengumumanRequest extends FormRequest
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
        $pengumumanId = $this->route('pengumuman') ? $this->route('pengumuman')->id : null;

        return [
            'judul' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('pengumumans', 'slug')->ignore($pengumumanId)],
            'kategori' => ['required', 'string', 'max:50'],
            'isi' => ['required', 'string'],
            'gambar' => ['nullable', 'file', 'extensions:jpeg,png,jpg,webp', 'max:10240'],
            'posisi_gambar' => ['nullable', 'string', 'max:50'],
            'tgl_tayang' => ['required', 'date'],
            'tgl_kedaluwarsa' => ['nullable', 'date', 'after_or_equal:tgl_tayang'],
            'is_pinned' => ['nullable', 'boolean'],
            'status' => ['required', 'in:draft,terbit'],
        ];
    }

    /**
     * Custom messages for validation errors in Indonesian.
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul pengumuman wajib diisi.',
            'judul.max' => 'Judul pengumuman maksimal 255 karakter.',
            'slug.unique' => 'Slug pengumuman sudah digunakan, silakan pilih slug lain.',
            'kategori.required' => 'Kategori pengumuman wajib dipilih.',
            'isi.required' => 'Isi pengumuman wajib diisi.',
            'gambar.uploaded' => 'Ukuran foto terlalu besar melebihi batas upload server. Silakan gunakan foto maksimal 5 MB atau perkecil terlebih dahulu.',
            'gambar.extensions' => 'Format gambar yang diperbolehkan: JPG, JPEG, PNG, atau WEBP.',
            'gambar.max' => 'Ukuran gambar maksimal 5 MB.',
            'tgl_tayang.required' => 'Tanggal tayang wajib diisi.',
            'tgl_tayang.date' => 'Format tanggal tayang tidak valid.',
            'tgl_kedaluwarsa.date' => 'Format tanggal kedaluwarsa tidak valid.',
            'tgl_kedaluwarsa.after_or_equal' => 'Tanggal kedaluwarsa tidak boleh sebelum tanggal tayang.',
            'status.required' => 'Status publikasi wajib dipilih.',
            'status.in' => 'Status harus berupa draft atau terbit.',
        ];
    }
}
