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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:menu_items,name',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'image' => 'nullable|string'
        ]);
        
        $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);

        if (!empty($validated['image'])) {
            $imageData = base64_decode($validated['image']);
            $fileName = 'menu_images/' . uniqid() . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);
            $validated['image'] = $fileName;
        } else {
            unset($validated['image']);
        }

        $menu = MenuItem::create($validated);
        $menu->load('category:id,name');
        
        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil ditambahkan.',
            'data' => $this->formatSingle($menu, $request)
        ]);
    }

    public function update(Request $request, $id)
    {
        $menu = MenuItem::findOrFail($id);
        
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:menu_items,name,' . $id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'is_available' => 'boolean',
            'image' => 'nullable|string'
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name']);
        }
        
        if (!empty($validated['image'])) {
            $imageData = base64_decode($validated['image']);
            $fileName = 'menu_images/' . uniqid() . '.jpg';
            \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $imageData);
            $validated['image'] = $fileName;
            
            // Delete old image if exists
            if ($menu->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($menu->image);
            }
        } else {
            unset($validated['image']); // don't override existing image
        }
        
        $menu->update($validated);
        $menu->load('category:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil diperbarui.',
            'data' => $this->formatSingle($menu, $request)
        ]);
    }

    public function destroy($id)
    {
        $menu = MenuItem::findOrFail($id);
        $menu->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Menu berhasil dihapus.'
        ]);
    }

    public function toggle($id)
    {
        $menu = MenuItem::findOrFail($id);
        $menu->update(['is_available' => !$menu->is_available]);
        $menu->load('category:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Status menu berhasil diubah.',
            'data' => $this->formatSingle($menu, request())
        ]);
    }

    private function formatSingle($item, $request)
    {
        $baseUrl = rtrim($request->getSchemeAndHttpHost(), '/');
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
    }
}
