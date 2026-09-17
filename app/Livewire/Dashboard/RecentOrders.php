<?php

/*
|=============================================================================
| ⚠️  [LEGACY — NOT CONNECTED]
|=============================================================================
|
| File ini sudah DIMIGRASI ke:
|   App\Http\Controllers\Api\Admin\DashboardController::recentOrders()
|   Endpoint: GET /api/v1/admin/dashboard/recent-orders
|=============================================================================
*/

namespace App\Livewire\Dashboard;

use App\Models\Order;
use Livewire\Component;

class RecentOrders extends Component
{
    /** [LEGACY — DISABLED] Gunakan GET /api/v1/admin/dashboard/recent-orders */
    public function render()
    {
        throw new \RuntimeException(
            '[LEGACY] RecentOrders sudah dinonaktifkan. Gunakan GET /api/v1/admin/dashboard/recent-orders'
        );
    }
}
