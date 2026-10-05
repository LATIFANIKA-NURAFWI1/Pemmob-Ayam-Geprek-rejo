<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Recipe;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecipeController extends Controller
{
    /**
     * Get recipe for a specific menu item
     */
    public function show(int $menuId): JsonResponse
    {
        $menu = MenuItem::findOrFail($menuId);
        
        $recipes = Recipe::with('ingredient:id,name,unit,unit_cost')
            ->where('menu_item_id', $menuId)
            ->get()
            ->map(function ($recipe) {
                return [
                    'id' => $recipe->id,
                    'stock_ingredient_id' => $recipe->stock_ingredient_id,
                    'ingredient_name' => $recipe->ingredient->name,
                    'unit' => $recipe->ingredient->unit,
                    'unit_cost' => (float) $recipe->ingredient->unit_cost,
                    'qty_used' => (float) $recipe->qty_used,
                    'hpp_contribution' => $recipe->hpp_contribution,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'menu' => [
                    'id' => $menu->id,
                    'name' => $menu->name,
                ],
                'recipes' => $recipes
            ]
        ]);
    }

    /**
     * Sync recipes for a specific menu item
     */
    public function sync(Request $request, int $menuId): JsonResponse
    {
        $request->validate([
            'ingredients' => 'array',
            'ingredients.*.stock_ingredient_id' => 'required|exists:stock_ingredients,id',
            'ingredients.*.qty_used' => 'required|numeric|min:0.0001',
        ]);

        $menu = MenuItem::findOrFail($menuId);

        // Delete all old recipes for this menu
        Recipe::where('menu_item_id', $menuId)->delete();

        // Insert new ones
        if ($request->has('ingredients') && !empty($request->ingredients)) {
            $recipesToInsert = [];
            foreach ($request->ingredients as $ing) {
                $recipesToInsert[] = [
                    'menu_item_id' => $menuId,
                    'stock_ingredient_id' => $ing['stock_ingredient_id'],
                    'qty_used' => $ing['qty_used'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            Recipe::insert($recipesToInsert);
        }

        return response()->json([
            'success' => true,
            'message' => 'Resep berhasil diperbarui.'
        ]);
    }
}
