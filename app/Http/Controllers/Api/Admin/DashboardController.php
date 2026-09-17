<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StockIngredient;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * DashboardController — menyediakan data ringkasan untuk halaman
 * utama Admin (sebelumnya dikerjakan oleh 3 komponen Livewire:
 * StatsCards, RecentOrders, dan TopMenu).
 *
 * Semua method mengembalikan JsonResponse murni tanpa Blade/Livewire.
 */
class DashboardController extends Controller
{
    /**
     * GET /api/admin/dashboard/stats
     *
     * Mengembalikan statistik ringkasan hari ini:
     * - Total pesanan & yang sudah terbayar
     * - Omset (revenue) & laba kotor
     * - Jumlah menu aktif & bahan stok kritis
     *
     * Dikonversi dari: App\Livewire\Dashboard\StatsCards
     */
    public function stats(): JsonResponse
    {
        // Ambil semua pesanan hari ini dalam satu query (efisien)
        $todayOrders = Order::today()->get();

        // Filter pesanan yang sudah terbayar (status: confirmed/preparing/completed)
        $paidOrders  = $todayOrders->filter(
            fn ($order) => in_array($order->status, ['confirmed', 'preparing', 'completed'])
        );

        $revenue     = (float) $paidOrders->sum('total_amount');
        $hpp         = (float) $paidOrders->sum('total_hpp');
        $grossProfit = $revenue - $hpp;

        return response()->json([
            'success' => true,
            'data'    => [
                'total_pesanan' => $todayOrders->count(),
                'paid_count'    => $paidOrders->count(),
                'pending_count' => $todayOrders->where('status', 'pending')->count(),
                'omset'         => $revenue,
                'gross_profit'  => $grossProfit,
                'menu_aktif'    => MenuItem::available()->count(),
                'stok_kritis'   => StockIngredient::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
            ],
        ]);
    }

    /**
     * GET /api/admin/dashboard/recent-orders
     *
     * Mengembalikan 5 pesanan terbaru hari ini beserta detail itemnya.
     *
     * Dikonversi dari: App\Livewire\Dashboard\RecentOrders
     */
    public function recentOrders(): JsonResponse
    {
        $orders = Order::today()
            ->with(['details.menuItem']) // Eager loading — hindari query N+1
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($order) => [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'queue_number' => $order->queue_number,
                'status'       => $order->status,
                'type'         => $order->type,
                'total_amount' => (float) $order->total_amount,
                'created_at'   => $order->created_at->toIso8601String(),
                'items'        => $order->details->map(fn ($d) => [
                    'name'     => $d->menu_item_name,
                    'quantity' => $d->quantity,
                    'subtotal' => (float) $d->subtotal,
                ]),
            ]);

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }

    /**
     * GET /api/admin/dashboard/top-menus
     *
     * Mengembalikan 5 menu terlaris hari ini berdasarkan jumlah terjual.
     *
     * Dikonversi dari: App\Livewire\Dashboard\TopMenu
     */
    public function topMenus(): JsonResponse
    {
        $topMenus = OrderDetail::select(
            'menu_item_id',
            'menu_item_name',
            DB::raw('SUM(quantity) as total_terjual')
        )
            ->whereHas('order', fn ($q) => $q->today())
            ->whereNotNull('menu_item_id')
            ->groupBy('menu_item_id', 'menu_item_name')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'menu_item_id'   => $item->menu_item_id,
                'name'           => $item->menu_item_name,
                'total_terjual'  => (int) $item->total_terjual,
            ]);

        return response()->json([
            'success' => true,
            'data'    => $topMenus,
        ]);
    }

    /**
     * GET /api/admin/dashboard
     *
     * Endpoint agregat — mengembalikan stats + recent orders + top menus
     * dalam satu request. Berguna agar mobile tidak perlu 3 kali request
     * saat pertama kali membuka halaman dashboard.
     */
    public function index(): JsonResponse
    {
        // ── Stats ────────────────────────────────────────────────────────────
        $todayOrders = Order::today()->get();
        $paidOrders  = $todayOrders->filter(
            fn ($order) => in_array($order->status, ['confirmed', 'preparing', 'completed'])
        );
        $revenue     = (float) $paidOrders->sum('total_amount');
        $hpp         = (float) $paidOrders->sum('total_hpp');

        // ── Recent Orders ────────────────────────────────────────────────────
        $recentOrders = Order::today()
            ->with(['details'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn ($order) => [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'queue_number' => $order->queue_number,
                'status'       => $order->status,
                'type'         => $order->type,
                'total_amount' => (float) $order->total_amount,
                'created_at'   => $order->created_at->toIso8601String(),
                'items'        => $order->details->map(fn ($d) => [
                    'name'     => $d->menu_item_name,
                    'quantity' => $d->quantity,
                    'subtotal' => (float) $d->subtotal,
                ]),
            ]);

        // ── Top Menus ────────────────────────────────────────────────────────
        $topMenus = OrderDetail::select(
            'menu_item_id',
            'menu_item_name',
            DB::raw('SUM(quantity) as total_terjual')
        )
            ->whereHas('order', fn ($q) => $q->today())
            ->whereNotNull('menu_item_id')
            ->groupBy('menu_item_id', 'menu_item_name')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get()
            ->map(fn ($item) => [
                'menu_item_id'  => $item->menu_item_id,
                'name'          => $item->menu_item_name,
                'total_terjual' => (int) $item->total_terjual,
            ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'stats' => [
                    'total_pesanan' => $todayOrders->count(),
                    'paid_count'    => $paidOrders->count(),
                    'pending_count' => $todayOrders->where('status', 'pending')->count(),
                    'omset'         => $revenue,
                    'gross_profit'  => $revenue - $hpp,
                    'menu_aktif'    => MenuItem::available()->count(),
                    'stok_kritis'   => StockIngredient::whereColumn('current_stock', '<=', 'minimum_stock')->count(),
                ],
                'recent_orders' => $recentOrders,
                'top_menus'     => $topMenus,
            ],
        ]);
    }
}
