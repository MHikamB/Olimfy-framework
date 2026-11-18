<?php

namespace App\Models;

use Database\Factories\OrganizerProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrganizerProfile extends Model
{
    /** @use HasFactory<OrganizerProfileFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'nama_organisasi',
        'kategori_instansi',
        'nama_pic',
        'kontak_pic',
        'no_rekening',
        'nama_bank',
    ];

    /**
     * Get the user that owns the organizer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
