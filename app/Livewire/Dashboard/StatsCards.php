<?php

/*
|=============================================================================
| ⚠️  [LEGACY — NOT CONNECTED]
|=============================================================================
|
| File ini sudah DIMIGRASI ke:
|   App\Http\Controllers\Api\Admin\DashboardController::stats()
|   Endpoint: GET /api/v1/admin/dashboard/stats
|=============================================================================
*/

namespace App\Livewire\Dashboard;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\StockIngredient;
use Livewire\Attributes\Computed;
use Livewire\Component;

class StatsCards extends Component
{
    #[Computed]
    public function stats(): array
    {
        $todayOrders   = Order::today()->get();
        $paidToday     = $todayOrders->filter(fn ($o) => in_array($o->status, ['confirmed', 'preparing', 'completed']));

        $revenue       = $paidToday->sum('total_amount');
        $hpp           = $paidToday->sum('total_hpp');
        $grossProfit   = $revenue - $hpp;
        $pending       = $todayOrders->where('status', 'pending')->count();

        $menuAktif     = MenuItem::available()->count();
        $stokKritis    = StockIngredient::whereColumn('current_stock', '<=', 'minimum_stock')->count();

        return [
            'total_pesanan' => $todayOrders->count(),
            'paid_count'    => $paidToday->count(),
            'omset'         => $revenue,
            'gross_profit'  => $grossProfit,
            'pending'       => $pending,
            'menu_aktif'    => $menuAktif,
            'stok_kritis'   => $stokKritis,
        ];
    }

    /** [LEGACY — DISABLED] Gunakan GET /api/v1/admin/dashboard/stats */
    public function render()
    {
        throw new \RuntimeException(
            '[LEGACY] StatsCards sudah dinonaktifkan. Gunakan GET /api/v1/admin/dashboard/stats'
        );
    }
}
