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
}
