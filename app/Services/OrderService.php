<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Models\Member;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Voucher;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class OrderService
{
    public function __construct(
        private readonly StockService   $stockService,
        private readonly HPPService     $hppService,
        private readonly PointService   $pointService,
        private readonly VoucherService $voucherService,
    ) {}

    // =========================================================================
    // BAGIAN 1: PEMBUATAN ORDER
    // =========================================================================

    /**
     * Buat order baru dari cart.
     * Order dibuat dengan status 'pending' — belum dikonfirmasi kasir.
     *
     * @param  array{
     *     cart: array<int, array{menu_item_id: int, quantity: int, notes: string|null}>,
     *     member_id: int|null,
     *     voucher_code: string|null,
     *     points_to_redeem: int,
     *     table_number: string|null,
     *     type: string,
     *     payment_method: string,
     *     order_notes: string|null,
     * }  $data
     *
     * @throws InsufficientStockException
     * @throws \InvalidArgumentException
     * @throws Throwable
     */
    public function createOrder(array $data): Order
    {
        // --- Pre-validasi (di luar transaction untuk hemat lock) ---

        // Bangun array cartItems [menu_item_id => quantity]
        $cartItems = [];
        foreach ($data['cart'] as $item) {
            $cartItems[$item['menu_item_id']] = ($cartItems[$item['menu_item_id']] ?? 0) + $item['quantity'];
        }

        // Validasi stok sebelum masuk DB transaction
        $this->stockService->validateStockAvailability($cartItems);

        // Validasi & hitung diskon voucher (jika ada)
        $voucherData = null;
        if (! empty($data['voucher_code'])) {
            $voucherData = $this->voucherService->validateAndCalculate(
                $data['voucher_code'],
                $this->calculateSubtotal($data['cart']),
                $data['member_id'] ?? null,
                $data['cart']
            );
        }

        // Validasi & hitung diskon poin
        $redemptionData = ['discount' => 0.0, 'points_used' => 0];
        $member         = null;
        if (! empty($data['member_id'])) {
            $member = Member::findOrFail($data['member_id']);

            if (($data['points_to_redeem'] ?? 0) > 0) {
                $redemptionData = $this->pointService->calculateRedemption(
                    $member,
                    $data['points_to_redeem'],
                    $this->calculateSubtotal($data['cart'])
                );
            }
        }

        $this->setRepeatableRead();
        return DB::transaction(function () use ($data, $cartItems, $voucherData, $redemptionData, $member) {
            // 1. Hitung finansial
            $subtotal          = $this->calculateSubtotal($data['cart']);
            $discountAmount    = $voucherData ? $voucherData['discount'] : 0.0;
            $pointsRedAmount   = $redemptionData['discount'];
            $pointsRedeemed    = $redemptionData['points_used'];
            $totalAmount       = max(0.0, $subtotal - $discountAmount - $pointsRedAmount);

            // 2. Generate nomor order & queue number secara atomik
            ['order_number' => $orderNumber, 'queue_number' => $queueNumber]
                = $this->generateOrderNumberAndQueue();

            // 3. Buat record order
            $order = Order::create([
                'order_number'           => $orderNumber,
                'queue_number'           => $queueNumber,
                'member_id'              => $data['member_id'] ?? null,
                'voucher_id'             => $voucherData ? $voucherData['voucher']->id : null,
                'table_number'           => $data['table_number'] ?? null,
                'type'                   => $data['type'] ?? 'dine_in',
                'status'                 => 'pending',
                'payment_method'         => $data['payment_method'],
                'subtotal'               => $subtotal,
                'discount_amount'        => $discountAmount,
                'points_redeemed_amount' => $pointsRedAmount,
                'points_redeemed'        => $pointsRedeemed,
                'total_amount'           => $totalAmount,
                'total_hpp'              => 0, // diisi saat konfirmasi
                'notes'                  => $data['order_notes'] ?? null,
            ]);

            // 4. Buat order_details
            foreach ($data['cart'] as $item) {
                $menuItem = MenuItem::findOrFail($item['menu_item_id']);

                OrderDetail::create([
                    'order_id'       => $order->id,
                    'menu_item_id'   => $menuItem->id,
                    'menu_item_name' => $menuItem->name,
                    'quantity'       => $item['quantity'],
                    'unit_price'     => $menuItem->price,
                    'subtotal'       => $menuItem->price * $item['quantity'],
                    'hpp_snapshot'   => 0, // diisi saat konfirmasi
                    'notes'          => $item['notes'] ?? null,
                ]);
            }

            // 5. JANGAN potong poin di sini — poin hanya dipotong saat kasir konfirmasi (confirmPayment)
            //    Order sudah mencatat points_redeemed & points_redeemed_amount untuk referensi kasir.

            Log::info("OrderService: Order #{$orderNumber} (queue: {$queueNumber}) dibuat.");

            return $order;
        });
    }

    // =========================================================================
    // BAGIAN 2: KONFIRMASI PEMBAYARAN (INTI ATOMIK)
    // =========================================================================

    /**
     * Konfirmasi pembayaran oleh kasir.
     *
     * Urutan eksekusi atomik dalam satu DB::transaction:
     * 1. Update status order → 'confirmed'
     * 2. StockService::deductStock   — Potong stok bahan baku
     * 3. HPPService::freezeHppSnapshot — Freeze HPP ke order_details
     * 4. PointService::earnPoints    — Tambah poin member
     * 5. VoucherService::recordUse   — Catat log voucher
     * 6. Update statistik member     — total_orders, total_spent, tier, last_order_at
     *
     * @param  int     $orderId
     * @param  string  $paymentMethod  'qris' | 'cash'
     * @param  int|null  $confirmedBy  User ID kasir
     *
     * @throws \RuntimeException
     * @throws InsufficientStockException
     * @throws Throwable
     */
    public function confirmPayment(int $orderId, string $paymentMethod, ?int $confirmedBy = null): Order
    {
        $this->setRepeatableRead();
        return DB::transaction(function () use ($orderId, $paymentMethod, $confirmedBy) {

            // Pessimistic lock pada order untuk cegah double confirm
            /** @var Order $order */
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if (! $order->isPending()) {
                throw new \RuntimeException(
                    "Order #{$order->order_number} tidak bisa dikonfirmasi (status: {$order->status})."
                );
            }

            // Load relasi yang diperlukan
            $order->load('details', 'member', 'voucher');

            // ── STEP 1: Update status & payment method ──────────────────────
            $order->update([
                'status'         => 'confirmed',
                'payment_method' => $paymentMethod,
                'confirmed_at'   => Carbon::now(),
                'confirmed_by'   => $confirmedBy,
            ]);

            // ── STEP 2: Potong stok bahan baku ─────────────────────────────
            $cartItems = $order->details->pluck('quantity', 'menu_item_id')
                ->filter(fn ($qty, $id) => $id !== null)  // skip jika menu_item_id null
                ->toArray();

            $this->stockService->deductStock($cartItems);

            // ── STEP 3: Freeze HPP snapshot ─────────────────────────────────
            $this->hppService->freezeHppSnapshot($order);

            // Refresh total_hpp setelah freeze
            $order->refresh();

            // ── STEP 4: Proses poin earn & REDEEM (ATOMIC) ─────────────────
            if ($order->member) {
                // REDEEM: Potong poin jika pesanan menggunakan redeem
                // Poin baru dipotong DI SINI (bukan saat createOrder)
                if ((int) $order->points_redeemed > 0) {
                    // Re-fetch member dengan lock untuk cegah race condition
                    $memberLocked = \App\Models\Member::lockForUpdate()->findOrFail($order->member_id);
                    $this->pointService->redeemPoints($memberLocked, $order, (int) $order->points_redeemed);
                    $order->member = $memberLocked->refresh(); // sinkronisasi state
                }

                // EARN: Hitung poin dari total_amount (setelah diskon)
                // Catatan: jika total_amount = 0 (full redeem), poin yang didapat = 0
                $this->pointService->earnPoints($order->member, $order);
            }

            if ($order->voucher) {
                $this->voucherService->recordUse(
                    $order->voucher,
                    $order,
                    (float) $order->discount_amount
                );
            }

            // ── STEP 6: Update statistik member ─────────────────────────────
            if ($order->member) {
                $this->updateMemberStats($order->member, $order);
            }

            Log::info(
                "OrderService: Order #{$order->order_number} CONFIRMED via {$paymentMethod}. "
                . "Total: {$order->total_amount} | HPP: {$order->total_hpp} | "
                . "Gross Profit: {$order->gross_profit}"
            );

            return $order;
        });
    }

    // =========================================================================
    // BAGIAN 3: TRANSISI STATUS (KDS)
    // =========================================================================

    /**
     * Kasir / KDS mengubah status order ke 'preparing'.
     */
    public function startPreparing(int $orderId): Order
    {
        $this->setRepeatableRead();
        return DB::transaction(function () use ($orderId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if ($order->status !== 'confirmed') {
                throw new \RuntimeException(
                    "Order #{$order->order_number} belum dikonfirmasi pembayarannya."
                );
            }

            $order->update(['status' => 'preparing']);

            return $order;
        });
    }

    /**
     * KDS menyelesaikan pesanan → status 'completed'.
     */
    public function completeOrder(int $orderId): Order
    {
        $this->setRepeatableRead();
        return DB::transaction(function () use ($orderId) {
            $order = Order::lockForUpdate()->findOrFail($orderId);

            if ($order->status !== 'preparing') {
                throw new \RuntimeException(
                    "Order #{$order->order_number} belum dalam status 'preparing'."
                );
            }

            $order->update([
                'status'       => 'completed',
                'completed_at' => Carbon::now(),
            ]);

            return $order;
        });
    }

    /**
     * Batalkan order (hanya bisa saat status 'pending').
     * Rollback stok dan voucher jika sudah terlanjur dipotong.
     */
    public function cancelOrder(int $orderId, string $reason = ''): Order
    {
        $this->setRepeatableRead();
        return DB::transaction(function () use ($orderId, $reason) {
            $order = Order::lockForUpdate()->with('details')->findOrFail($orderId);

            if (! in_array($order->status, ['pending', 'confirmed'])) {
                throw new \RuntimeException(
                    "Order #{$order->order_number} tidak bisa dibatalkan (status: {$order->status})."
                );
            }

            // Rollback stok jika sudah confirmed (stok sudah dipotong)
            if ($order->status === 'confirmed') {
                $cartItems = $order->details->pluck('quantity', 'menu_item_id')
                    ->filter(fn ($qty, $id) => $id !== null)
                    ->toArray();
                $this->stockService->restoreStock($cartItems);
            }

            // Rollback voucher
            $this->voucherService->rollbackUse($orderId);

            $order->update([
                'status' => 'cancelled',
                'notes'  => $order->notes . ($reason ? "\n[Dibatalkan: {$reason}]" : ''),
            ]);

            Log::info("OrderService: Order #{$order->order_number} DIBATALKAN. Alasan: {$reason}");

            return $order;
        });
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Hitung subtotal dari cart.
     *
     * @param  array<int, array{menu_item_id: int, quantity: int}>  $cart
     */
    private function calculateSubtotal(array $cart): float
    {
        $subtotal = 0.0;

        foreach ($cart as $item) {
            $menuItem = MenuItem::find($item['menu_item_id']);

            if ($menuItem) {
                $subtotal += (float) $menuItem->price * $item['quantity'];
            }
        }

        return $subtotal;
    }

    /**
     * Generate order_number dan queue_number secara atomik (lock tabel).
     *
     * Format order_number : GR-YYYYMMDD-XXXX (4 digit, reset tiap hari)
     * Format queue_number : 1–999 (reset tiap hari)
     *
     * @return array{order_number: string, queue_number: int}
     */
    private function generateOrderNumberAndQueue(): array
    {
        $today = Carbon::today()->toDateString();

        // Ambil nomor terakhir hari ini dengan SELECT FOR UPDATE (row lock via aggregate)
        // Kita gunakan pendekatan: MAX(queue_number) pada hari ini dalam satu transaction.
        $lastQueue = Order::whereDate('created_at', $today)
            ->lockForUpdate()
            ->max('queue_number') ?? 0;

        $queueNumber = $lastQueue + 1;

        if ($queueNumber > 999) {
            $queueNumber = 1; // Wrap around (edge case, operasi < 999 order/hari)
        }

        $orderNumber = 'GR-' . Carbon::today()->format('Ymd') . '-' . str_pad($queueNumber, 4, '0', STR_PAD_LEFT);

        return [
            'order_number' => $orderNumber,
            'queue_number' => $queueNumber,
        ];
    }

    /**
     * Set isolation level ke REPEATABLE READ jika driver mendukung.
     * SQLite tidak mendukung perintah ini (sudah serializable by default).
     */
    private function setRepeatableRead(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
        }
    }

    /**
     * Update total_orders, total_spent, tier, dan last_order_at member.
     */
    private function updateMemberStats(Member $member, Order $order): void
    {
        $newTotalSpent = (float) $member->total_spent + (float) $order->total_amount;
        $newTier       = Member::resolveTier($newTotalSpent);

        $member->update([
            'total_orders'  => $member->total_orders + 1,
            'total_spent'   => $newTotalSpent,
            'tier'          => $newTier,
            'last_order_at' => Carbon::now(),
        ]);
    }
}
