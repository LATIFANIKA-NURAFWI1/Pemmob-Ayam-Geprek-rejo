<?php

use App\Livewire\Cashier\Dashboard as CashierDashboard;
use App\Livewire\Customer\CheckoutPage;
use App\Livewire\Customer\MenuPage;
use App\Livewire\Customer\PaymentPage;
use App\Livewire\Customer\SuccessPage;
use App\Livewire\Kategori\Index as KategoriIndex;
use App\Livewire\Kds\Display as KdsDisplay;
use App\Livewire\Laporan\Index as LaporanIndex;
use App\Livewire\Member\Index as MemberIndex;
use App\Livewire\Menu\Index as MenuIndex;
use App\Livewire\Menu\Create as MenuCreate;
use App\Livewire\Menu\Edit as MenuEdit;
use App\Livewire\Pengeluaran\Index as PengeluaranIndex;
use App\Livewire\Pesanan\Index as PesananIndex;
use App\Livewire\Stok\Index as StokIndex;
use App\Livewire\Voucher\Index as VoucherIndex;
use Illuminate\Support\Facades\Route;

Route::get("/", function () {
    return redirect()->route('login');
})->name("home");

// ── Post-Login Redirect (berdasarkan role) ───────────────────────────────────
Route::get('/redirect-by-role', function () {
    if (! auth()->check()) {
        return redirect()->route('login');
    }
    return match (auth()->user()->role) {
        'owner' => redirect()->route('dashboard'),
        'kasir' => redirect()->route('kasir.dashboard'),
        'kds'   => redirect()->route('kds.display'),
        default => redirect()->route('dashboard'),
    };
})->middleware('auth')->name('redirect.by.role');

// ── Customer Self-Order (public, no auth) ─────────────────────────────────────
Route::prefix("order")
    ->name("order.")
    ->group(function () {
        Route::get("/", MenuPage::class)->name("menu");
        Route::get("/checkout", CheckoutPage::class)->name("checkout");
        Route::get("/payment/{order}", PaymentPage::class)->name("payment");
        Route::get("/success/{order}", SuccessPage::class)->name("success");
    });

// ── Redirect /customer/* → /order/* (backward compat) ────────────────────────
Route::redirect("/customer", "/order")->name("order.customer_alias");
Route::redirect("/customer/checkout", "/order/checkout");
Route::get("/customer/payment/{order}", fn($order) => redirect("/order/payment/{$order}"));
Route::get("/customer/success/{order}", fn($order) => redirect("/order/success/{$order}"));


// ── Owner / Admin Panel (hanya role: owner) ───────────────────────────────────
Route::middleware(["auth", "verified", "role:owner"])->group(function () {
    // Dashboard
    Route::view("dashboard", "dashboard")->name("dashboard");

    // Manajemen Menu
    Route::prefix("menu")
        ->name("menu.")
        ->group(function () {
            Route::get("/", MenuIndex::class)->name("index");
            Route::get("/tambah", MenuCreate::class)->name("create");
            Route::get("/edit/{id}", MenuEdit::class)->name("edit");
        });

    // Kategori
    Route::get("kategori", KategoriIndex::class)->name("kategori.index");

    // Transaksi
    Route::get("pesanan", PesananIndex::class)->name("pesanan.index");
    Route::get("member", MemberIndex::class)->name("member.index");

    // Operasional
    Route::get("stok", StokIndex::class)->name("stok.index");
    Route::get("pengeluaran", PengeluaranIndex::class)->name(
        "pengeluaran.index",
    );
    Route::get("voucher", VoucherIndex::class)->name("voucher.index");

    // Laporan
    Route::get("laporan", LaporanIndex::class)->name("laporan.index");

    // ── Modul 9: Manajemen Staf & Jadwal Shift (REQ-FUNC-039 & REQ-FUNC-040) ──
    // [DINONAKTIFKAN — DIMIGRASI KE REST API]
    // Route ini sudah dipindah ke routes/api.php:
    //   GET    /api/v1/admin/staff            → StaffController::index()
    //   POST   /api/v1/admin/staff            → StaffController::store()
    //   GET    /api/v1/admin/staff/{id}       → StaffController::show()
    //   PUT    /api/v1/admin/staff/{id}       → StaffController::update()
    //   DELETE /api/v1/admin/staff/{id}       → StaffController::destroy()
    //   PATCH  /api/v1/admin/staff/{id}/toggle-active
    //   GET    /api/v1/admin/shifts           → StaffController::shiftIndex()
    //   POST   /api/v1/admin/shifts           → StaffController::shiftStore()
    //
    // Route::get("staf", \App\Livewire\Admin\StaffManager::class)->name("staf.index");
});

// ── Kasir Dashboard (role: kasir) ─────────────────────────────────────────────
Route::middleware(["auth", "role:kasir,owner"])->group(function () {
    Route::get("/kasir", CashierDashboard::class)->name("kasir.dashboard");
});

// ── KDS Dapur (role: kds) ─────────────────────────────────────────────────────
Route::middleware(["auth", "role:kds,owner"])->group(function () {
    Route::get("/kds", KdsDisplay::class)->name("kds.display");
});

require __DIR__ . "/settings.php";

