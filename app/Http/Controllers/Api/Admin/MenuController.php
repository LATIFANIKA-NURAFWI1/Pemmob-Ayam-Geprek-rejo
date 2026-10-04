<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    /**
     * Display a listing of the menus along with categories.
     */
    public function index(Request $request)
    {
        // Ambil semua kategori
        $categories = Category::orderBy('sort_order')->get(['id', 'name']);

        // Ambil semua menu dengan relasi kategori
        $menus = MenuItem::with('category:id,name')
            ->orderBy('category_id')
            ->orderBy('sort_order')
            ->get();

        $baseUrl = rtrim($request->getSchemeAndHttpHost(), '/');

        // Transform data untuk response API
        $formattedMenus = $menus->map(function ($item) use ($baseUrl) {
            return [
                'id' => $item->id,
                'category_id' => $item->category_id,
                'name' => $item->name,
                'description' => $item->description,
                'price' => (float) $item->price,
                'is_available' => (bool) $item->is_available,
                'image' => $item->image ? $baseUrl . '/storage/' . $item->image : null,
                'category' => $item->category ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                ] : null,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'categories' => $categories,
                'items' => $formattedMenus
            ]
        ]);
    }
}
