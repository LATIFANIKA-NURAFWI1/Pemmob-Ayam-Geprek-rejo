<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'image',
        'price',
        'is_available',
        'sort_order',
    ];

    // ─── Auto-generate slug ───────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $item) {
            if (empty($item->slug)) {
                $item->slug = self::generateUniqueSlug($item->name);
            }
        });

        static::updating(function (self $item) {
            if ($item->isDirty('name') && empty($item->slug)) {
                $item->slug = self::generateUniqueSlug($item->name);
            }
        });
    }

    private static function generateUniqueSlug(string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;
        while (static::withTrashed()->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'is_available' => 'boolean',
            'sort_order'   => 'integer',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Bahan baku yang digunakan dalam resep menu ini.
     * Pivot table: recipes (dengan kolom qty_used).
     */
    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(
            StockIngredient::class,
            'recipes',
            'menu_item_id',
            'stock_ingredient_id'
        )->withPivot('qty_used')->withTimestamps();
    }

    /**
     * Akses langsung ke model Recipe (untuk eager loading qty_used, dst.)
     */
    public function recipes(): HasMany
    {
        return $this->hasMany(Recipe::class);
    }

    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    /**
     * Hitung HPP per unit berdasarkan resep.
     * HPP = Σ(unit_cost × qty_used)
     */
    public function calculateHppPerUnit(): float
    {
        return (float) $this->recipes()
            ->join('stock_ingredients', 'recipes.stock_ingredient_id', '=', 'stock_ingredients.id')
            ->selectRaw('SUM(recipes.qty_used * stock_ingredients.unit_cost) as hpp')
            ->value('hpp') ?? 0.0;
    }
}
