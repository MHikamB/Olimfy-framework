<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PesertaProfile extends Model
{
    protected $fillable = [
        'user_id',
        'nim_nisn',
        'institusi',
        'jenjang',
        'jurusan',
        'no_hp',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
