<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDetail extends Model
{
    protected $fillable = [
        'order_id',
        'menu_item_id',
        'menu_item_name',
        'quantity',
        'unit_price',
        'subtotal',
        'hpp_snapshot',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity'     => 'integer',
            'unit_price'   => 'decimal:2',
            'subtotal'     => 'decimal:2',
            'hpp_snapshot' => 'decimal:4',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relasi ke MenuItem bisa null jika menu di-soft-delete
     * (ON DELETE SET NULL pada menu_item_id).
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class)->withTrashed();
    }

    // ─── Accessors ───────────────────────────────────────────────────────────

    /**
     * Total HPP untuk line item ini: hpp_snapshot (per unit) × quantity.
     */
    public function getTotalHppAttribute(): float
    {
        return (float) ($this->hpp_snapshot * $this->quantity);
    }

    /**
     * Gross profit untuk line item ini.
     */
    public function getLineGrossProfitAttribute(): float
    {
        return max(0.0, (float) $this->subtotal - $this->total_hpp);
    }
}
