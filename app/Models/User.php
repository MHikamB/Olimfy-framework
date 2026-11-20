<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Sinkronkan kolom lama `role` dengan relasi `role_id` saat model disimpan.
     *
     * Kolom `role` dipertahankan sementara agar kode lama tetap berjalan,
     * sehingga keduanya harus selalu bernilai sama.
     */
    protected static function booted(): void
    {
        static::saving(function (User $user): void {
            if ($user->isDirty('role_id') && $user->role_id !== null) {
                $namaRole = Role::query()
                    ->whereKey($user->role_id)
                    ->value('nama_role');

                if ($namaRole !== null) {
                    $user->role = $namaRole;
                }

                return;
            }

            if ($user->isDirty('role')) {
                $roleId = Role::query()
                    ->where('nama_role', $user->role)
                    ->value('id');

                if ($roleId !== null) {
                    $user->role_id = $roleId;
                }
            }
        });
    }

    /**
     * Role master yang dimiliki pengguna.
     */
    public function roleRecord(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Periksa apakah pengguna memiliki role tertentu.
     *
     * Fallback ke kolom lama `role` selama masa transisi.
     */
    public function hasRole(string $roleName): bool
    {
        return ($this->roleRecord?->nama_role ?? $this->role) === $roleName;
    }

    /**
     * Get the organizer profile owned by the user.
     */
    public function organizerProfile(): HasOne
    {
        return $this->hasOne(OrganizerProfile::class);
    }

    /**
     * Get the competitions organized by the user.
     */
    public function competitions(): HasMany
    {
        return $this->hasMany(Competition::class, 'organizer_id');
    }

    public function pesertaProfile()
    {
        return $this->hasOne(PesertaProfile::class);
    }

    public function bookmarkedCompetitions()
    {
        return $this->belongsToMany(Competition::class, 'bookmarks', 'user_id', 'competition_id')->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
