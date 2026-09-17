<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recipe extends Model
{
    protected $fillable = [
        'menu_item_id',
        'stock_ingredient_id',
        'qty_used',
    ];

    protected function casts(): array
    {
        return [
            'qty_used' => 'decimal:4',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(StockIngredient::class, 'stock_ingredient_id');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Hitung kontribusi HPP bahan ini untuk 1 porsi.
     */
    public function getHppContributionAttribute(): float
    {
        if (! $this->relationLoaded('ingredient')) {
            $this->load('ingredient');
        }

        return (float) ($this->qty_used * $this->ingredient->unit_cost);
    }
}
