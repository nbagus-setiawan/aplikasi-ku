<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuratUksRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna diizinkan membuat permintaan ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Dapatkan aturan validasi untuk permintaan ini.
     */
    public function rules(): array
    {
        return [
            'jenis' => ['required', 'in:pulang,sakit'],
            'santri_id' => ['required', 'exists:santris,id'],
            'tanggal_mulai' => ['required', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'keperluan' => ['required', 'string', 'max:255'],
            'diagnosa' => ['nullable', 'string', 'max:255'],
            'penjemput' => ['nullable', 'string', 'max:150'],
            'hubungan_penjemput' => ['nullable', 'string', 'max:100'],
            'petugas' => ['required', 'string', 'max:150'],
            'jabatan_petugas' => ['required', 'string', 'max:150'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'status' => ['nullable', 'in:aktif,kembali,dibatalkan'],
        ];
    }

    /**
     * Pesan kustom dalam bahasa Indonesia.
     */
    public function messages(): array
    {
        return [
            'jenis.required' => 'Jenis surat izin wajib dipilih.',
            'jenis.in' => 'Jenis surat izin harus berupa izin pulang atau izin sakit.',
            'santri_id.required' => 'Santri wajib dipilih.',
            'santri_id.exists' => 'Santri yang dipilih tidak ditemukan di database.',
            'tanggal_mulai.required' => 'Tanggal mulai izin wajib diisi.',
            'tanggal_mulai.date' => 'Format tanggal mulai izin tidak valid.',
            'tanggal_selesai.date' => 'Format tanggal kembali tidak valid.',
            'tanggal_selesai.after_or_equal' => 'Tanggal kembali harus sama atau setelah tanggal mulai izin.',
            'keperluan.required' => 'Keperluan atau keluhan izin wajib diisi.',
            'keperluan.max' => 'Keperluan maksimal 255 karakter.',
            'petugas.required' => 'Nama petugas wajib diisi.',
            'jabatan_petugas.required' => 'Jabatan petugas wajib diisi.',
        ];
    }
}
