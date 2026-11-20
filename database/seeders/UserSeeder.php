<?php

namespace Database\Seeders;

use App\Models\OrganizerProfile;
use App\Models\PesertaProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed akun contoh penyelenggara dan peserta beserta profilnya.
     */
    public function run(): void
    {
        $rolePenyelenggara = Role::where('nama_role', 'penyelenggara')->firstOrFail();
        $rolePeserta = Role::where('nama_role', 'peserta')->firstOrFail();

        // 1. Akun Panitia / Penyelenggara
        $panitia = User::updateOrCreate(
            ['email' => 'panitia@stei.itb.ac.id'],
            [
                'name' => 'ITB STEI Panitia Informatika',
                'password' => Hash::make('password123'),
                'role' => $rolePenyelenggara->nama_role,
                'role_id' => $rolePenyelenggara->id,
            ],
        );

        OrganizerProfile::updateOrCreate(
            ['user_id' => $panitia->id],
            [
                'nama_organisasi' => 'STEI Institut Teknologi Bandung',
                'kategori_instansi' => 'Perguruan Tinggi',
                'nama_pic' => 'Panitia Informatika STEI ITB',
                'kontak_pic' => '081234567890',
                'no_rekening' => '1234567890',
                'nama_bank' => 'Bank Mandiri',
            ],
        );

        // 2. Akun Peserta
        $peserta = User::updateOrCreate(
            ['email' => 'rayhan.pratama@example.com'],
            [
                'name' => 'Muhammad Rayhan Pratama',
                'password' => Hash::make('password123'),
                'role' => $rolePeserta->nama_role,
                'role_id' => $rolePeserta->id,
            ],
        );

        PesertaProfile::updateOrCreate(
            ['user_id' => $peserta->id],
            [
                'nim_nisn' => '13522001',
                'institusi' => 'Institut Teknologi Bandung',
                'jenjang' => 'Mahasiswa',
                'jurusan' => 'Teknik Informatika',
                'no_hp' => '081234567890',
            ],
        );
    }
}
