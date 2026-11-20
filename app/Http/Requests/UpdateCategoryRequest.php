<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends StoreCategoryRequest
{
    /**
     * Aturan validasi data kategori yang diubah.
     *
     * Aturan unik mengabaikan kategori yang sedang diedit agar nama/slug
     * milik kategori itu sendiri tidak dianggap duplikat.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $categoryId = $this->route('id');

        return [
            'nama_kategori' => [
                'required',
                'string',
                'max:255',
                Rule::unique('categories', 'nama_kategori')->ignore($categoryId),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'deskripsi' => ['nullable', 'string'],
            'ikon' => ['required', 'string', 'max:100'],
        ];
    }
}
