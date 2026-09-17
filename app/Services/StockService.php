<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\MenuItem;
use App\Models\StockIngredient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StockService
{
    /**
     * Validasi ketersediaan stok untuk seluruh item di cart.
     *
     * @param  array<int, int>  $cartItems  [menu_item_id => quantity]
     * @throws InsufficientStockException
     */
    public function validateStockAvailability(array $cartItems): void
    {
        $insufficients = [];

        // Hitung kebutuhan total per bahan baku dari semua item di cart
        $requirements = $this->aggregateRequirements($cartItems);

        foreach ($requirements as $ingredientId => $totalNeeded) {
            $ingredient = StockIngredient::find($ingredientId);

            if (! $ingredient) {
                continue; // Bahan baku dihapus, skip (edge case)
            }

            if ((float) $ingredient->current_stock < $totalNeeded) {
                $insufficients[] = [
                    'ingredient'   => $ingredient->name,
                    'unit'         => $ingredient->unit,
                    'needed'       => $totalNeeded,
                    'available'    => (float) $ingredient->current_stock,
                ];
            }
        }

        if (! empty($insufficients)) {
            throw new InsufficientStockException(
                'Stok bahan baku tidak mencukupi.',
                $insufficients
            );
        }
    }

    /**
     * Potong stok bahan baku berdasarkan resep secara atomik.
     * HARUS dipanggil dalam DB::transaction.
     *
     * @param  array<int, int>  $cartItems  [menu_item_id => quantity]
     *
     * @throws InsufficientStockException
     */
    public function deductStock(array $cartItems): void
    {
        $requirements = $this->aggregateRequirements($cartItems);

        foreach ($requirements as $ingredientId => $totalNeeded) {
            // Gunakan pessimistic locking untuk cegah race condition
            $ingredient = StockIngredient::lockForUpdate()->find($ingredientId);

            if (! $ingredient) {
                continue;
            }

            if ((float) $ingredient->current_stock < $totalNeeded) {
                throw new InsufficientStockException(
                    "Stok '{$ingredient->name}' tidak mencukupi saat proses pemotongan.",
                    [[
                        'ingredient' => $ingredient->name,
                        'unit'       => $ingredient->unit,
                        'needed'     => $totalNeeded,
                        'available'  => (float) $ingredient->current_stock,
                    ]]
                );
            }

            $newStock = (float) $ingredient->current_stock - $totalNeeded;
            $ingredient->update(['current_stock' => $newStock]);

            Log::info("StockService: Deducted {$totalNeeded} {$ingredient->unit} dari '{$ingredient->name}'. Sisa: {$newStock}");
        }
    }

    /**
     * Tambah stok bahan baku (untuk fitur pembelian/restock).
     */
    public function addStock(int $ingredientId, float $qty, ?float $newUnitCost = null): StockIngredient
    {
        /** @var StockIngredient $ingredient */
        $ingredient = StockIngredient::lockForUpdate()->findOrFail($ingredientId);

        $ingredient->current_stock = (float) $ingredient->current_stock + $qty;

        if ($newUnitCost !== null) {
            // Weighted Average Cost: (old_stock × old_cost + new_qty × new_cost) / total
            $totalStock = (float) $ingredient->current_stock;
            if ($totalStock > 0) {
                $oldStock    = $totalStock - $qty;
                $oldCost     = (float) $ingredient->unit_cost;
                $newCost     = ($oldStock * $oldCost + $qty * $newUnitCost) / $totalStock;
                $ingredient->unit_cost = round($newCost, 4);
            } else {
                $ingredient->unit_cost = $newUnitCost;
            }
        }

        $ingredient->save();

        return $ingredient;
    }

    /**
     * Kembalikan stok saat order dibatalkan.
     *
     * @param  array<int, int>  $cartItems  [menu_item_id => quantity]
     */
    public function restoreStock(array $cartItems): void
    {
        $requirements = $this->aggregateRequirements($cartItems);

        foreach ($requirements as $ingredientId => $totalNeeded) {
            StockIngredient::where('id', $ingredientId)
                ->lockForUpdate()
                ->increment('current_stock', $totalNeeded);
        }
    }

    /**
     * Ambil semua bahan baku dengan stok di bawah ambang minimum.
     *
     * @return Collection<int, StockIngredient>
     */
    public function getLowStockIngredients(): Collection
    {
        return StockIngredient::lowStock()->get();
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    /**
     * Agregasi kebutuhan bahan baku dari seluruh item di cart.
     *
     * @param  array<int, int>  $cartItems  [menu_item_id => quantity]
     * @return array<int, float>  [ingredient_id => total_qty_needed]
     */
    private function aggregateRequirements(array $cartItems): array
    {
        $requirements = [];

        foreach ($cartItems as $menuItemId => $quantity) {
            $recipes = DB::table('recipes')
                ->join('stock_ingredients', 'recipes.stock_ingredient_id', '=', 'stock_ingredients.id')
                ->where('recipes.menu_item_id', $menuItemId)
                ->select('recipes.stock_ingredient_id', 'recipes.qty_used')
                ->get();

            foreach ($recipes as $recipe) {
                $needed = $recipe->qty_used * $quantity;
                $requirements[$recipe->stock_ingredient_id] =
                    ($requirements[$recipe->stock_ingredient_id] ?? 0) + $needed;
            }
        }

        return $requirements;
    }
}
