<?php

namespace Database\Seeders;

use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompetitionSeeder extends Seeder
{
    public function run(): void
    {
        $organizer = User::where('email', 'panitia@stei.itb.ac.id')->first();

        $competitions = [
            [
                'organizer_id' => $organizer->id,
                'judul' => 'ITB Hackathon 2026: AI & Smart City',
                'deskripsi' => 'Kompetisi pengembangan solusi perangkat lunak berbasis Kecerdasan Buatan untuk tata kelola kota cerdas.',
                'kategori' => 'Teknologi',
                'jenjang' => 'Mahasiswa',
                'mode_pelaksanaan' => 'Hybrid',
                'biaya_pendaftaran' => 150000,
                'kuota' => 100,
                'kuota_terisi' => 64,
                'deadline_pendaftaran' => now()->addDays(20)->toDateString(),
                'status' => 'Aktif',
            ],
            [
                'organizer_id' => $organizer->id,
                'judul' => 'National UI/UX Design Challenge 2026',
                'deskripsi' => 'Tantangan merancang antarmuka aplikasi digital yang inklusif dan ramah pengguna.',
                'kategori' => 'Desain',
                'jenjang' => 'Mahasiswa',
                'mode_pelaksanaan' => 'Online',
                'biaya_pendaftaran' => 50000,
                'kuota' => 80,
                'kuota_terisi' => 52,
                'deadline_pendaftaran' => now()->addDays(15)->toDateString(),
                'status' => 'Aktif',
            ],
            [
                'organizer_id' => $organizer->id,
                'judul' => 'Olimpiade Sains & Matematika Nasional',
                'deskripsi' => 'Ajang uji kemampuan logika, kalkulus dasar, dan pemecahan masalah sains tingkat nasional.',
                'kategori' => 'Sains',
                'jenjang' => 'SMA/SMK',
                'mode_pelaksanaan' => 'Online',
                'biaya_pendaftaran' => 0,
                'kuota' => 200,
                'kuota_terisi' => 145,
                'deadline_pendaftaran' => now()->addDays(25)->toDateString(),
                'status' => 'Aktif',
            ],
            [
                'organizer_id' => $organizer->id,
                'judul' => 'Business Case Competition Q4',
                'deskripsi' => 'Kompetisi studi kasus strategi bisnis nyata bersama perusahaan teknologi terkemuka.',
                'kategori' => 'Bisnis',
                'jenjang' => 'Mahasiswa',
                'mode_pelaksanaan' => 'Offline',
                'biaya_pendaftaran' => 100000,
                'kuota' => 50,
                'kuota_terisi' => 38,
                'deadline_pendaftaran' => now()->addDays(12)->toDateString(),
                'status' => 'Aktif',
            ],
            [
                'organizer_id' => $organizer->id,
                'judul' => 'Cyber Security CTF Championship',
                'deskripsi' => 'Kompetisi Capture The Flag untuk menguji ketangkasan keamanan jaringan dan kriptografi.',
                'kategori' => 'Teknologi',
                'jenjang' => 'Umum',
                'mode_pelaksanaan' => 'Online',
                'biaya_pendaftaran' => 75000,
                'kuota' => 60,
                'kuota_terisi' => 49,
                'deadline_pendaftaran' => now()->addDays(18)->toDateString(),
                'status' => 'Aktif',
            ],
            [
                'organizer_id' => $organizer->id,
                'judul' => 'Lomba Karya Tulis Ilmiah Nasional',
                'deskripsi' => 'Wadah gagasan riset inovatif mahasiswa untuk mendukung pembangunan berkelanjutan.',
                'kategori' => 'Sains',
                'jenjang' => 'Mahasiswa',
                'mode_pelaksanaan' => 'Online',
                'biaya_pendaftaran' => 35000,
                'kuota' => 120,
                'kuota_terisi' => 70,
                'deadline_pendaftaran' => now()->addDays(30)->toDateString(),
                'status' => 'Aktif',
            ],
        ];

        foreach ($competitions as $comp) {
            Competition::create($comp);
        }
    }
}
