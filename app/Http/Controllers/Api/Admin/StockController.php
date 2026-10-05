<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\StockIngredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * StockController — endpoint API untuk melihat daftar bahan baku/stok.
 *
 * Digunakan oleh halaman "Cek Stok" di mobile app untuk menampilkan
 * status stok semua bahan baku beserta indikator kritis.
 */
class StockController extends Controller
{
    /**
     * GET /api/v1/admin/stock
     *
     * Mengembalikan daftar seluruh bahan baku beserta stok dan status kritis.
     */
    public function index(Request $request): JsonResponse
    {
        $stocks = StockIngredient::query()
            ->when(
                $request->input('search'),
                fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
            )
            ->orderBy('name')
            ->get()
            ->map(fn ($item) => [
                'id'            => $item->id,
                'name'          => $item->name,
                'unit'          => $item->unit,
                'current_stock' => (float) $item->current_stock,
                'minimum_stock' => (float) $item->minimum_stock,
                'unit_cost'     => (float) $item->unit_cost,
                'is_critical'   => $item->current_stock <= $item->minimum_stock,
            ]);

        $criticalCount = $stocks->where('is_critical', true)->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'items'          => $stocks->values(),
                'total'          => $stocks->count(),
                'critical_count' => $criticalCount,
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'unit_cost'     => 'required|numeric|min:0',
        ]);

        $item = StockIngredient::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil ditambahkan',
            'data'    => $item
        ]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $item = StockIngredient::findOrFail($id);

        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'unit'          => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'minimum_stock' => 'required|numeric|min:0',
            'unit_cost'     => 'required|numeric|min:0',
        ]);

        $item->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil diperbarui',
            'data'    => $item
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $item = StockIngredient::findOrFail($id);
        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bahan baku berhasil dihapus'
        ]);
    }
}
