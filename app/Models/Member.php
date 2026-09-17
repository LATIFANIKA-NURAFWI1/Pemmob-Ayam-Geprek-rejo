<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'pin',
        'points',
        'total_orders',
        'total_spent',
        'tier',
        'is_active',
        'last_order_at',
    ];

    protected function casts(): array
    {
        return [
            'points'        => 'integer',
            'total_orders'  => 'integer',
            'total_spent'   => 'decimal:2',
            'is_active'     => 'boolean',
            'last_order_at' => 'datetime',
            'pin'           => 'hashed',   // Laravel 10+ native hashing cast
        ];
    }

    /**
     * Kolom yang TIDAK boleh masuk response JSON/array.
     */
    protected $hidden = ['pin'];

    // ─── Relations ───────────────────────────────────────────────────────────

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(PointLog::class);
    }

    public function voucherUses(): HasMany
    {
        return $this->hasMany(VoucherUse::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByPhone($query, string $phone)
    {
        return $query->where('phone', $phone);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Hitung tier berdasarkan total_spent.
     * Tier otomatis berubah saat OrderService mengupdate total_spent.
     */
    public static function resolveTier(float $totalSpent): string
    {
        return match (true) {
            $totalSpent >= 5_000_000 => 'platinum',
            $totalSpent >= 2_000_000 => 'gold',
            $totalSpent >= 500_000   => 'silver',
            default                  => 'bronze',
        };
    }

    /**
     * Konversi poin ke nilai rupiah.
     * Asumsi: 1 poin = Rp 1
     */
    public function pointsToRupiah(int $points): float
    {
        return (float) $points;
    }
}
