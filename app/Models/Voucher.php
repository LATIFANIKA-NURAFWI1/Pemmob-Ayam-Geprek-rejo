<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'member_id',
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_order',
        'maximum_discount',
        'max_uses',
        'uses_count',
        'is_active',
        'member_only',
        'starts_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'discount_value'   => 'decimal:2',
            'minimum_order'    => 'decimal:2',
            'maximum_discount' => 'decimal:2',
            'max_uses'         => 'integer',
            'uses_count'       => 'integer',
            'is_active'        => 'boolean',
            'member_only'      => 'boolean',
            'starts_at'        => 'datetime',
            'expires_at'       => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function uses(): HasMany
    {
        return $this->hasMany(VoucherUse::class);
    }

    public function member(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        $now = Carbon::now();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now))
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now));
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Cek apakah voucher masih bisa digunakan.
     */
    public function isUsable(?Member $member = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->starts_at && $this->starts_at->gt($now)) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->lt($now)) {
            return false;
        }

        if ($this->max_uses > 0 && $this->uses_count >= $this->max_uses) {
            return false;
        }

        if ($this->member_only && ! $member) {
            return false;
        }

        return true;
    }

    /**
     * Hitung nilai diskon yang diterapkan ke subtotal tertentu.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($subtotal < $this->minimum_order) {
            return 0.0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = $subtotal * ($this->discount_value / 100);

            if ($this->maximum_discount !== null) {
                $discount = min($discount, (float) $this->maximum_discount);
            }

            return $discount;
        }

        // fixed
        return min((float) $this->discount_value, $subtotal);
    }
}
