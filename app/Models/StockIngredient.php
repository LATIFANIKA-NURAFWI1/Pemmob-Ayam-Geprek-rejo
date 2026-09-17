<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockIngredient extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'current_stock',
        'minimum_stock',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:3',
            'minimum_stock' => 'decimal:3',
            'unit_cost'     => 'decimal:4',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function menuItems(): BelongsToMany
    {
        return $this->belongsToMany(
            MenuItem::class,
            'recipes',
            'stock_ingredient_id',
            'menu_item_id'
        )->withPivot('qty_used')->withTimestamps();
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    /**
     * Stok yang berada di bawah ambang minimum.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->current_stock <= 0;
    }
}
