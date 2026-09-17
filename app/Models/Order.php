<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'order_number',
        'queue_number',
        'member_id',
        'voucher_id',
        'table_number',
        'type',
        'status',
        'payment_method',
        'subtotal',
        'discount_amount',
        'points_redeemed_amount',
        'points_redeemed',
        'total_amount',
        'total_hpp',
        'points_earned',
        'notes',
        'confirmed_at',
        'completed_at',
        'confirmed_by',
    ];

    protected function casts(): array
    {
        return [
            'subtotal'               => 'decimal:2',
            'discount_amount'        => 'decimal:2',
            'points_redeemed_amount' => 'decimal:2',
            'total_amount'           => 'decimal:2',
            'total_hpp'              => 'decimal:2',
            'points_redeemed'        => 'integer',
            'points_earned'          => 'integer',
            'queue_number'           => 'integer',
            'confirmed_at'           => 'datetime',
            'completed_at'           => 'datetime',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function confirmedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'confirmed_by');
    }

    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function voucherUse(): HasMany
    {
        return $this->hasMany(VoucherUse::class);
    }

    public function pointLogs(): HasMany
    {
        return $this->hasMany(PointLog::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopePreparing($query)
    {
        return $query->where('status', 'preparing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePaid($query)
    {
        return $query->whereIn('status', ['confirmed', 'preparing', 'completed']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeForPeriod($query, string $from, string $to)
    {
        return $query->whereBetween('created_at', [
            \Carbon\Carbon::parse($from)->startOfDay(),
            \Carbon\Carbon::parse($to)->endOfDay(),
        ]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    public function isPaid(): bool
    {
        return in_array($this->status, ['confirmed', 'preparing', 'completed']);
    }

    public function getGrossProfitAttribute(): float
    {
        return max(0.0, (float) $this->total_amount - (float) $this->total_hpp);
    }

    public function getGrossMarginPercentAttribute(): float
    {
        if ((float) $this->total_amount <= 0) {
            return 0.0;
        }

        return round(($this->gross_profit / (float) $this->total_amount) * 100, 2);
    }
}
