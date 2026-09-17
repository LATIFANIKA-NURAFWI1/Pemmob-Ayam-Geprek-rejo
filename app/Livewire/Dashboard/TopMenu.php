<?php

/*
|=============================================================================
| ⚠️  [LEGACY — NOT CONNECTED]
|=============================================================================
|
| File ini sudah DIMIGRASI ke:
|   App\Http\Controllers\Api\Admin\DashboardController::topMenus()
|   Endpoint: GET /api/v1/admin/dashboard/top-menus
|=============================================================================
*/

namespace App\Livewire\Dashboard;

use App\Models\OrderDetail;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class TopMenu extends Component
{
    /** [LEGACY — DISABLED] Gunakan GET /api/v1/admin/dashboard/top-menus */
    public function render()
    {
        throw new \RuntimeException(
            '[LEGACY] TopMenu sudah dinonaktifkan. Gunakan GET /api/v1/admin/dashboard/top-menus'
        );
    }
}
