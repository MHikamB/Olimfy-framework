<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreRegistrationRequest extends FormRequest
{
    /**
     * Izinkan peserta terautentikasi melakukan validasi pendaftaran.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi input pendaftaran tim dan berkas persyaratan.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'competition_id' => ['required', 'exists:competitions,id'],
            'nama_tim' => ['required', 'string', 'min:3', 'max:100'],
            'nama_ketua' => ['required', 'string', 'max:150'],
            'email_ketua' => ['required', 'email', 'max:150'],
            'no_hp_ketua' => ['required', 'string', 'min:10', 'max:15'],
            'nama_anggota_1' => ['nullable', 'string', 'max:150'],
            'email_anggota_1' => ['nullable', 'required_with:nama_anggota_1', 'email', 'max:150'],
            'no_hp_anggota_1' => ['nullable', 'required_with:nama_anggota_1', 'string', 'min:10', 'max:15'],
            'nama_anggota_2' => ['nullable', 'string', 'max:150'],
            'email_anggota_2' => ['nullable', 'required_with:nama_anggota_2', 'email', 'max:150'],
            'no_hp_anggota_2' => ['nullable', 'required_with:nama_anggota_2', 'string', 'min:10', 'max:15'],
            'ktm_ketua' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'ktm_anggota' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            'proposal' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
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
            'competition_id.required' => 'Kompetisi wajib dipilih.',
            'competition_id.exists' => 'Kompetisi yang dipilih tidak ditemukan.',
            'nama_tim.required' => 'Nama tim wajib diisi.',
            'nama_tim.min' => 'Nama tim minimal 3 karakter.',
            'nama_tim.max' => 'Nama tim maksimal 100 karakter.',
            'nama_ketua.required' => 'Nama ketua tim wajib diisi.',
            'email_ketua.required' => 'Email ketua tim wajib diisi.',
            'email_ketua.email' => 'Format email ketua tim tidak valid.',
            'no_hp_ketua.required' => 'Nomor WhatsApp/HP ketua tim wajib diisi.',
            'no_hp_ketua.min' => 'Nomor WhatsApp/HP ketua tim minimal 10 digit.',
            'no_hp_ketua.max' => 'Nomor WhatsApp/HP ketua tim maksimal 15 digit.',
            'email_anggota_1.required_with' => 'Email anggota 1 wajib diisi jika nama anggota 1 dicantumkan.',
            'email_anggota_1.email' => 'Format email anggota 1 tidak valid.',
            'no_hp_anggota_1.required_with' => 'Nomor HP anggota 1 wajib diisi jika nama anggota 1 dicantumkan.',
            'no_hp_anggota_1.min' => 'Nomor HP anggota 1 minimal 10 digit.',
            'email_anggota_2.required_with' => 'Email anggota 2 wajib diisi jika nama anggota 2 dicantumkan.',
            'email_anggota_2.email' => 'Format email anggota 2 tidak valid.',
            'no_hp_anggota_2.required_with' => 'Nomor HP anggota 2 wajib diisi jika nama anggota 2 dicantumkan.',
            'no_hp_anggota_2.min' => 'Nomor HP anggota 2 minimal 10 digit.',
            'ktm_ketua.required' => 'Berkas KTM ketua tim wajib diunggah.',
            'ktm_ketua.file' => 'KTM ketua tim harus berupa file yang valid.',
            'ktm_ketua.mimes' => 'Format KTM ketua tim harus berupa PDF, JPG, JPEG, atau PNG.',
            'ktm_ketua.max' => 'Ukuran file KTM ketua tim maksimal 2 MB.',
            'ktm_anggota.file' => 'KTM anggota harus berupa file yang valid.',
            'ktm_anggota.mimes' => 'Format KTM anggota harus berupa PDF, JPG, JPEG, atau PNG.',
            'ktm_anggota.max' => 'Ukuran file KTM anggota maksimal 2 MB.',
            'proposal.file' => 'Proposal karya harus berupa file yang valid.',
            'proposal.mimes' => 'Format berkas proposal harus berupa PDF.',
            'proposal.max' => 'Ukuran file proposal maksimal 5 MB.',
        ];
    }
}
