<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreSubmissionRequest extends FormRequest
{
    /**
     * Izinkan peserta terautentikasi melakukan pengumpulan karya.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi formulir pengumpulan karya lomba.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul_karya' => ['required', 'string', 'min:5', 'max:200'],
            'deskripsi_karya' => ['required', 'string', 'min:20'],
            'link_karya' => ['nullable', 'url', 'max:500'],
            'file_karya' => ['nullable', 'file', 'mimes:pdf,zip', 'max:10240'],
        ];
    }

    /**
     * Pesan error kustom dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'judul_karya.required' => 'Judul karya wajib diisi.',
            'judul_karya.min' => 'Judul karya minimal 5 karakter.',
            'judul_karya.max' => 'Judul karya maksimal 200 karakter.',
            'deskripsi_karya.required' => 'Deskripsi karya wajib diisi.',
            'deskripsi_karya.min' => 'Deskripsi karya minimal 20 karakter.',
            'link_karya.url' => 'Format tautan karya harus berupa URL yang valid (contoh: https://...).',
            'link_karya.max' => 'Tautan karya maksimal 500 karakter.',
            'file_karya.file' => 'Berkas karya harus berupa file yang valid.',
            'file_karya.mimes' => 'Format berkas karya harus berupa PDF atau ZIP.',
            'file_karya.max' => 'Ukuran berkas karya maksimal 10 MB.',
        ];
    }
}
