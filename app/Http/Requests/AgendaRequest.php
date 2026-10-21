<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AgendaRequest extends FormRequest
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
        return [
            'judul' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'mulai' => ['required', 'date'],
            'selesai' => ['required', 'date', 'after_or_equal:mulai'],
            'lokasi' => ['required', 'string', 'max:255'],
            'pj_id' => ['nullable', 'exists:penguruses,id'],
            'status' => ['required', 'in:rencana,berjalan,selesai,batal'],
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'judul.required' => 'Judul agenda kegiatan wajib diisi.',
            'mulai.required' => 'Waktu mulai kegiatan wajib diisi.',
            'mulai.date' => 'Format waktu mulai tidak valid.',
            'selesai.required' => 'Waktu selesai kegiatan wajib diisi.',
            'selesai.date' => 'Format waktu selesai tidak valid.',
            'selesai.after_or_equal' => 'Waktu selesai harus sama atau setelah waktu mulai.',
            'lokasi.required' => 'Lokasi atau tempat kegiatan wajib diisi.',
            'pj_id.exists' => 'Penanggung jawab yang dipilih tidak valid.',
            'status.required' => 'Status agenda wajib dipilih.',
            'status.in' => 'Status agenda tidak valid.',
        ];
    }
}
