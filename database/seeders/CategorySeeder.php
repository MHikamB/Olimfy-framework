<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Seed kategori lomba awal beserta ikon Bootstrap Icons.
     */
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Teknologi',
                'slug' => 'teknologi',
                'deskripsi' => 'Kompetisi pemrograman, kecerdasan buatan, keamanan siber, dan inovasi digital.',
                'ikon' => 'bi-cpu',
            ],
            [
                'nama_kategori' => 'Desain',
                'slug' => 'desain',
                'deskripsi' => 'Kompetisi desain antarmuka, grafis, produk, dan komunikasi visual.',
                'ikon' => 'bi-palette',
            ],
            [
                'nama_kategori' => 'Bisnis',
                'slug' => 'bisnis',
                'deskripsi' => 'Kompetisi studi kasus, kewirausahaan, dan strategi bisnis.',
                'ikon' => 'bi-briefcase',
            ],
            [
                'nama_kategori' => 'Sains',
                'slug' => 'sains',
                'deskripsi' => 'Kompetisi sains, matematika, riset, dan karya tulis ilmiah.',
                'ikon' => 'bi-lightbulb',
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category,
            );
        }
    }
}
