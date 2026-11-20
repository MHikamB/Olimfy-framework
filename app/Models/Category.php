<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kategori',
        'slug',
        'deskripsi',
        'ikon',
    ];

    /**
     * Kompetisi yang memakai kategori ini.
     *
     * Relasi memakai kolom `category_id` yang akan ditambahkan pada Step 3.
     */
    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'category_id');
    }
}
