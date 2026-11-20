<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    /**
     * Izinkan penyelenggara terautentikasi melakukan validasi data kategori.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi data kategori baru.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama_kategori' => ['required', 'string', 'max:255', Rule::unique('categories', 'nama_kategori')],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', Rule::unique('categories', 'slug')],
            'deskripsi' => ['nullable', 'string'],
            'ikon' => ['required', 'string', 'max:100'],
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
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string' => 'Nama kategori harus berupa teks.',
            'nama_kategori.max' => 'Nama kategori maksimal 255 karakter.',
            'nama_kategori.unique' => 'Nama kategori sudah digunakan.',
            'slug.required' => 'Slug wajib diisi.',
            'slug.string' => 'Slug harus berupa teks.',
            'slug.max' => 'Slug maksimal 255 karakter.',
            'slug.alpha_dash' => 'Slug hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'slug.unique' => 'Slug sudah digunakan oleh kategori lain.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            'ikon.required' => 'Ikon wajib diisi.',
            'ikon.string' => 'Ikon harus berupa teks.',
            'ikon.max' => 'Ikon maksimal 100 karakter.',
        ];
    }
}
