<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HPPService
{
    /**
     * Hitung HPP per unit untuk satu menu item.
     *
     * HPP per unit = Σ(unit_cost × qty_used) untuk semua bahan dalam resep.
     *
     * @param  int  $menuItemId
     * @return float  HPP per unit dalam Rupiah
     */
    public function calculateHppPerUnit(int $menuItemId): float
    {
        $hpp = DB::table('recipes')
            ->join('stock_ingredients', 'recipes.stock_ingredient_id', '=', 'stock_ingredients.id')
            ->where('recipes.menu_item_id', $menuItemId)
            ->selectRaw('COALESCE(SUM(recipes.qty_used * stock_ingredients.unit_cost), 0) as hpp')
            ->value('hpp');

        return round((float) $hpp, 4);
    }

    /**
     * "Freeze" HPP ke setiap order_detail dan update total_hpp pada order.
     *
     * Proses ini dilakukan SAAT KASIR KONFIRMASI PEMBAYARAN agar HPP
     * tidak berubah meskipun harga beli bahan baku berubah di kemudian hari.
     *
     * HARUS dipanggil dalam DB::transaction.
     *
     * @param  Order  $order  (harus sudah load relasi 'details')
     */
    public function freezeHppSnapshot(Order $order): void
    {
        if (! $order->relationLoaded('details')) {
            $order->load('details');
        }

        $totalHpp = 0.0;

        foreach ($order->details as $detail) {
            if (! $detail->menu_item_id) {
                // Menu sudah dihapus, HPP = 0 (tidak ada resep)
                $hppPerUnit = 0.0;
            } else {
                $hppPerUnit = $this->calculateHppPerUnit($detail->menu_item_id);
            }

            // Simpan snapshot ke order_detail
            $detail->update(['hpp_snapshot' => $hppPerUnit]);

            // Akumulasi total HPP order
            $totalHpp += $hppPerUnit * $detail->quantity;

            Log::info(
                "HPPService: Freeze HPP order_detail#{$detail->id} | "
                . "menu='{$detail->menu_item_name}' | "
                . "hpp_per_unit={$hppPerUnit} | "
                . "qty={$detail->quantity} | "
                . "hpp_line=" . ($hppPerUnit * $detail->quantity)
            );
        }

        // Update total_hpp pada order
        $order->update(['total_hpp' => round($totalHpp, 2)]);

        Log::info("HPPService: Order#{$order->id} total_hpp frozen = {$totalHpp}");
    }

    /**
     * Preview kalkulasi HPP untuk cart (sebelum order dibuat).
     * Digunakan di Admin dashboard untuk estimasi HPP.
     *
     * @param  array<int, int>  $cartItems  [menu_item_id => quantity]
     * @return array{total_hpp: float, breakdown: array}
     */
    public function previewHpp(array $cartItems): array
    {
        $breakdown = [];
        $totalHpp  = 0.0;

        foreach ($cartItems as $menuItemId => $quantity) {
            $hppPerUnit = $this->calculateHppPerUnit($menuItemId);
            $lineHpp    = $hppPerUnit * $quantity;

            $breakdown[] = [
                'menu_item_id' => $menuItemId,
                'hpp_per_unit' => $hppPerUnit,
                'quantity'     => $quantity,
                'line_hpp'     => $lineHpp,
            ];

            $totalHpp += $lineHpp;
        }

        return [
            'total_hpp' => round($totalHpp, 2),
            'breakdown' => $breakdown,
        ];
    }
}
