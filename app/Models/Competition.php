<?php

namespace App\Models;

use Database\Factories\CompetitionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Competition extends Model
{
    /** @use HasFactory<CompetitionFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'organizer_id',
        'judul',
        'kategori',
        'jenjang',
        'mode_pelaksanaan',
        'biaya_pendaftaran',
        'kuota',
        'kuota_terisi',
        'deadline_pendaftaran',
        'deskripsi',
        'poster_path',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'biaya_pendaftaran' => 'integer',
            'kuota' => 'integer',
            'kuota_terisi' => 'integer',
            'deadline_pendaftaran' => 'date',
        ];
    }

    /**
     * Get the organizer that owns the competition.
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Pendaftaran tim yang masuk pada kompetisi ini.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function bookmarkedByUsers()
    {
        return $this->belongsToMany(User::class, 'bookmarks', 'competition_id', 'user_id')->withTimestamps();
    }
}
