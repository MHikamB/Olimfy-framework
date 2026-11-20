<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'competition_id',
        'peserta_id',
        'nama_tim',
        'status_verifikasi',
        'catatan_kurasi',
        'verified_by',
        'verified_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }

    /**
     * Kompetisi yang didaftari oleh tim ini.
     */
    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Akun peserta pemilik pendaftaran.
     */
    public function peserta(): BelongsTo
    {
        return $this->belongsTo(User::class, 'peserta_id');
    }

    /**
     * Penyelenggara yang memverifikasi pendaftaran ini.
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Anggota tim (ketua dan anggota) pada pendaftaran ini.
     */
    public function members(): HasMany
    {
        return $this->hasMany(RegistrationMember::class);
    }

    /**
     * Dokumen persyaratan yang diunggah pada pendaftaran ini.
     */
    public function documents(): HasMany
    {
        return $this->hasMany(RegistrationDocument::class);
    }

    /**
     * Data tagihan pembayaran untuk pendaftaran ini.
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Data pengumpulan karya lomba untuk pendaftaran ini.
     */
    public function submission(): HasOne
    {
        return $this->hasOne(Submission::class);
    }
}
