<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\StaffController;
use App\Http\Controllers\Api\Admin\StockController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Mobile Ayam Geprek Rejo
|--------------------------------------------------------------------------
|
| Semua route di sini menggunakan prefix /api/ secara otomatis oleh
| Laravel. Middleware 'auth:sanctum' menjaga endpoint yang memerlukan
| autentikasi.
|
| Versi API : v1
| Prefix    : /api/v1
|
*/

// ── Auth endpoint (tidak butuh token) ────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Endpoint login menggunakan Sanctum token — dihandle Fortify/Sanctum
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login'])
        ->name('api.login');

    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])
        ->middleware('auth:sanctum')
        ->name('api.logout');

    // ── Protected routes — butuh Bearer token Sanctum ─────────────────────
    Route::middleware(['auth:sanctum'])->group(function () {

        // Informasi user yang sedang login
        Route::get('/me', fn (Request $request) => response()->json([
            'success' => true,
            'data'    => [
                'id'        => $request->user()->id,
                'name'      => $request->user()->name,
                'email'     => $request->user()->email,
                'role'      => $request->user()->role,
                'is_active' => $request->user()->is_active,
            ],
        ]))->name('api.me');

        // ── Admin-only routes (role: owner) ───────────────────────────────
        Route::middleware(['role:owner'])->prefix('admin')->name('api.admin.')->group(function () {

            // ── Dashboard (dikonversi dari StatsCards + RecentOrders + TopMenu) ──
            Route::prefix('dashboard')->name('dashboard.')->group(function () {
                Route::get('/', [DashboardController::class, 'index'])
                    ->name('index');          // GET /api/v1/admin/dashboard

                Route::get('/stats', [DashboardController::class, 'stats'])
                    ->name('stats');          // GET /api/v1/admin/dashboard/stats

                Route::get('/recent-orders', [DashboardController::class, 'recentOrders'])
                    ->name('recent-orders');  // GET /api/v1/admin/dashboard/recent-orders

                Route::get('/top-menus', [DashboardController::class, 'topMenus'])
                    ->name('top-menus');      // GET /api/v1/admin/dashboard/top-menus
            });

            // ── Manajemen Staf (REQ-FUNC-039) ────────────────────────────────
            Route::prefix('staff')->name('staff.')->group(function () {
                Route::get('/', [StaffController::class, 'index'])
                    ->name('index');          // GET /api/v1/admin/staff

                // active-list HARUS sebelum {staff} agar tidak dianggap parameter
                Route::get('/active-list', [StaffController::class, 'activeList'])
                    ->name('active-list');    // GET /api/v1/admin/staff/active-list

                Route::post('/', [StaffController::class, 'store'])
                    ->name('store');          // POST /api/v1/admin/staff

                Route::get('/{staff}', [StaffController::class, 'show'])
                    ->name('show');           // GET /api/v1/admin/staff/{id}

                Route::put('/{staff}', [StaffController::class, 'update'])
                    ->name('update');         // PUT /api/v1/admin/staff/{id}

                Route::delete('/{staff}', [StaffController::class, 'destroy'])
                    ->name('destroy');        // DELETE /api/v1/admin/staff/{id}

                Route::patch('/{staff}/toggle-active', [StaffController::class, 'toggleActive'])
                    ->name('toggle-active');  // PATCH /api/v1/admin/staff/{id}/toggle-active
            });

            // ── Manajemen Jadwal Shift (REQ-FUNC-040) ────────────────────────
            Route::prefix('shifts')->name('shifts.')->group(function () {
                Route::get('/', [StaffController::class, 'shiftIndex'])
                    ->name('index');          // GET /api/v1/admin/shifts

                Route::post('/', [StaffController::class, 'shiftStore'])
                    ->name('store');          // POST /api/v1/admin/shifts

                Route::put('/{shift}', [StaffController::class, 'shiftUpdate'])
                    ->name('update');         // PUT /api/v1/admin/shifts/{id}

                Route::delete('/{shift}', [StaffController::class, 'shiftDestroy'])
                    ->name('destroy');        // DELETE /api/v1/admin/shifts/{id}
            });

            // ── Stok Bahan Baku ───────────────────────────────────────────────
            Route::get('/stock', [StockController::class, 'index'])
                ->name('stock.index');       // GET /api/v1/admin/stock
        });
    });
});
