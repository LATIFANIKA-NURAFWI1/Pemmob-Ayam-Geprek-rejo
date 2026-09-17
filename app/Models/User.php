<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

// is_active ditambahkan untuk kebutuhan REQ-FUNC-039 / N9.1
#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'         => 'hashed',
            'is_active'        => 'boolean', // Cast agar mudah digunakan sebagai bool
        ];
    }

    // =========================================================================
    // ROLE HELPERS
    // =========================================================================

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Satu User memiliki banyak jadwal shift.
     * Digunakan untuk REQ-FUNC-040.
     */
    public function shifts(): HasMany
    {
        return $this->hasMany(StaffShift::class)->orderBy('shift_date');
    }

    // =========================================================================
    // ROLE & STATUS HELPERS
    // =========================================================================

    /** Apakah user ini adalah owner/admin? */
    public function isOwner(): bool
    {
        return $this->role === 'owner';
    }

    /** Apakah user ini adalah staf (bukan owner)? */
    public function isStaff(): bool
    {
        return in_array($this->role, ['kasir', 'kds', 'inventory']);
    }

    /** Apakah akun user ini aktif? Digunakan middleware & N9.1. */
    public function isActive(): bool
    {
        return (bool) $this->is_active;
    }

    /** Apakah user ini kasir? */
    public function isKasir(): bool
    {
        return $this->role === 'kasir';
    }

    /** Apakah user ini KDS dapur? */
    public function isKds(): bool
    {
        return $this->role === 'kds';
    }

    // =========================================================================
    // INITIALS
    // =========================================================================

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
}
