<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreCompetitionRequest extends FormRequest
{
    /**
     * Izinkan penyelenggara terautentikasi melakukan validasi data lomba.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi data lomba.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'judul' => ['required', 'string', 'max:255'],
            'kategori' => ['required', 'in:Teknologi,Desain,Bisnis,Sains'],
            'jenjang' => ['required', 'in:Mahasiswa,SMA,Umum'],
            'mode_pelaksanaan' => ['required', 'in:Online,Offline,Hybrid'],
            'biaya_pendaftaran' => ['required', 'numeric', 'min:0'],
            'kuota' => ['required', 'integer', 'min:1'],
            'deadline_pendaftaran' => ['required', 'date', 'after:today'],
            'deskripsi' => ['required', 'string'],
            'poster' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
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
            'judul.required' => 'Judul lomba wajib diisi.',
            'judul.string' => 'Judul lomba harus berupa teks.',
            'judul.max' => 'Judul lomba maksimal 255 karakter.',
            'kategori.required' => 'Kategori lomba wajib dipilih.',
            'kategori.in' => 'Kategori lomba harus salah satu dari: Teknologi, Desain, Bisnis, Sains.',
            'jenjang.required' => 'Jenjang peserta wajib dipilih.',
            'jenjang.in' => 'Jenjang peserta harus salah satu dari: Mahasiswa, SMA, Umum.',
            'mode_pelaksanaan.required' => 'Mode pelaksanaan wajib dipilih.',
            'mode_pelaksanaan.in' => 'Mode pelaksanaan harus Online, Offline, atau Hybrid.',
            'biaya_pendaftaran.required' => 'Biaya pendaftaran wajib diisi.',
            'biaya_pendaftaran.numeric' => 'Biaya pendaftaran harus berupa angka.',
            'biaya_pendaftaran.min' => 'Biaya pendaftaran tidak boleh kurang dari 0.',
            'kuota.required' => 'Kuota peserta wajib diisi.',
            'kuota.integer' => 'Kuota peserta harus berupa bilangan bulat.',
            'kuota.min' => 'Kuota peserta minimal 1 tim.',
            'deadline_pendaftaran.required' => 'Batas pendaftaran wajib diisi.',
            'deadline_pendaftaran.date' => 'Batas pendaftaran harus berupa tanggal yang valid.',
            'deadline_pendaftaran.after' => 'Batas pendaftaran harus setelah hari ini.',
            'deskripsi.required' => 'Deskripsi lomba wajib diisi.',
            'deskripsi.string' => 'Deskripsi lomba harus berupa teks.',
            'poster.image' => 'Poster harus berupa gambar.',
            'poster.mimes' => 'Poster harus berformat JPEG, JPG, atau PNG.',
            'poster.max' => 'Ukuran poster maksimal 2 MB.',
        ];
    }
}
