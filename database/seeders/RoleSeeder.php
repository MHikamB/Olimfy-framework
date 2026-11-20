<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed role dasar aplikasi dan selaraskan role_id pengguna lama.
     */
    public function run(): void
    {
        $roles = [
            'penyelenggara' => 'Membuat dan mengelola kompetisi serta memverifikasi pendaftaran tim.',
            'peserta' => 'Mendaftar dan mengikuti kompetisi yang tersedia.',
        ];

        foreach ($roles as $namaRole => $deskripsi) {
            Role::updateOrCreate(
                ['nama_role' => $namaRole],
                ['deskripsi' => $deskripsi],
            );
        }

        // Sinkronkan akun yang sudah ada sebelum kolom role_id ditambahkan.
        Role::query()->each(function (Role $role): void {
            User::query()
                ->where('role', $role->nama_role)
                ->whereNull('role_id')
                ->update(['role_id' => $role->id]);
        });
    }
}
