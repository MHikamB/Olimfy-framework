<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Submission extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'registration_id',
        'competition_id',
        'submitted_by',
        'judul_karya',
        'deskripsi_karya',
        'link_karya',
        'file_karya',
        'status',
        'nilai',
        'catatan_juri',
        'graded_by',
        'submitted_at',
        'graded_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai' => 'integer',
            'submitted_at' => 'datetime',
            'graded_at' => 'datetime',
        ];
    }

    /**
     * Data pendaftaran tim pemilik karya ini.
     */
    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    /**
     * Kompetisi tujuan karya ini.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Akun peserta yang mengirimkan karya.
     */
    public function submitter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Akun penyelenggara yang menilai karya.
     */
    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}
