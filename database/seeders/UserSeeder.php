<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\PesertaProfile;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Panitia / Penyelenggara
        $panitia = User::create([
            'name' => 'ITB STEI Panitia Informatika',
            'email' => 'panitia@stei.itb.ac.id',
            'password' => Hash::make('password123'),
            'role' => 'penyelenggara',
        ]);

        // Sesuaikan kolom organizer_profiles dengan migrasi milik temanmu
        DB::table('organizer_profiles')->insert([
            'user_id' => $panitia->id,
            'nama_instansi' => 'STEI Institut Teknologi Bandung',
            'deskripsi' => 'Sekolah Teknik Elektro dan Informatika ITB',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Akun Peserta
        $peserta = User::create([
            'name' => 'Muhammad Rayhan Pratama',
            'email' => 'rayhan.pratama@example.com',
            'password' => Hash::make('password123'),
            'role' => 'peserta',
        ]);

        PesertaProfile::create([
            'user_id' => $peserta->id,
            'nim_nisn' => '13522001',
            'institusi' => 'Institut Teknologi Bandung',
            'jenjang' => 'Mahasiswa',
            'jurusan' => 'Teknik Informatika',
            'no_hp' => '081234567890',
        ]);
    }
}
