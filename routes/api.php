<?php

use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\ExpenseController;
use App\Http\Controllers\Api\Admin\ReportController;
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

// ── Fallback Auth routes (jika client memanggil /api/login langsung) ────────
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->middleware('auth:sanctum');

// ── Auth endpoint (v1) ───────────────────────────────────────────────────────
Route::prefix('v1')->group(function () {

    // Public export PDF route
    Route::get('/admin/reports/export-pdf', [\App\Http\Controllers\Api\Admin\ReportController::class, 'exportPdf'])
        ->name('api.admin.reports.export-pdf');

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
            Route::prefix('stock')->name('stock.')->group(function () {
                Route::get('/', [StockController::class, 'index'])->name('index');
                Route::post('/', [StockController::class, 'store'])->name('store');
                Route::put('/{id}', [StockController::class, 'update'])->name('update');
                Route::delete('/{id}', [StockController::class, 'destroy'])->name('destroy');
            });

            // ── Laporan Finansial (Laba / Rugi) ──────────────────────────────
            Route::prefix('reports')->name('reports.')->group(function () {
                Route::get('/profit-loss', [ReportController::class, 'profitLoss'])
                    ->name('profit-loss');   // GET /api/v1/admin/reports/profit-loss
            });

            // ── Manajemen Pengeluaran Operasional ─────────────────────────────
            Route::prefix('expenses')->name('expenses.')->group(function () {
                Route::get('/', [ExpenseController::class, 'index'])
                    ->name('index');          // GET /api/v1/admin/expenses
                Route::get('/summary', [ExpenseController::class, 'summary'])
                    ->name('summary');        // GET /api/v1/admin/expenses/summary
                Route::post('/', [ExpenseController::class, 'store'])
                    ->name('store');          // POST /api/v1/admin/expenses
                Route::put('/{id}', [ExpenseController::class, 'update'])
                    ->name('update');         // PUT /api/v1/admin/expenses/{id}
                Route::delete('/{id}', [ExpenseController::class, 'destroy'])
                    ->name('destroy');        // DELETE /api/v1/admin/expenses/{id}
            });

            // ── Menu Makanan & Resep ────────────────────────────────────────────────
            Route::prefix('menu')->name('menu.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Api\Admin\MenuController::class, 'index'])
                    ->name('index');         // GET /api/v1/admin/menu
                Route::post('/', [\App\Http\Controllers\Api\Admin\MenuController::class, 'store'])
                    ->name('store');
                Route::put('/{menu_id}', [\App\Http\Controllers\Api\Admin\MenuController::class, 'update'])
                    ->name('update');
                Route::delete('/{menu_id}', [\App\Http\Controllers\Api\Admin\MenuController::class, 'destroy'])
                    ->name('destroy');
                Route::patch('/{menu_id}/toggle', [\App\Http\Controllers\Api\Admin\MenuController::class, 'toggle'])
                    ->name('toggle');
                    
                Route::get('/{menu_id}/recipes', [\App\Http\Controllers\Api\Admin\RecipeController::class, 'show'])
                    ->name('recipes.show');  // GET /api/v1/admin/menu/{id}/recipes
                    
                Route::post('/{menu_id}/recipes', [\App\Http\Controllers\Api\Admin\RecipeController::class, 'sync'])
                    ->name('recipes.sync');  // POST /api/v1/admin/menu/{id}/recipes
            });

            // ── Riwayat Pesanan ──────────────────────────────────────────────────
            Route::get('/order-history', [\App\Http\Controllers\Api\Admin\OrderHistoryController::class, 'index'])
                ->name('order-history');     // GET /api/v1/admin/order-history
        });
    });
});
