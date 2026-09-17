<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Voucher;
use App\Models\VoucherUse;
use Illuminate\Support\Facades\Log;

class VoucherService
{
    /**
     * Validasi dan kalkulasi diskon voucher sebelum checkout.
     *
     * @param  string  $code
     * @param  float   $subtotal
     * @param  int|null  $memberId
     * @return array{voucher: Voucher, discount: float}
     *
     * @throws \InvalidArgumentException
     */
    public function validateAndCalculate(string $code, float $subtotal, ?int $memberId = null, array $cart = []): array
    {
        $voucher = Voucher::active()->where('code', strtoupper(trim($code)))->first();

        if (! $voucher) {
            throw new \InvalidArgumentException('Kode voucher tidak ditemukan atau sudah tidak aktif.');
        }

        // Load member jika ada
        $member = $memberId
            ? \App\Models\Member::find($memberId)
            : null;

        if (! $voucher->isUsable($member)) {
            throw new \InvalidArgumentException('Voucher tidak dapat digunakan. Periksa syarat & ketentuan.');
        }

        // Hitung diskon khusus untuk reward FREE-GEPREK-
        if (str_starts_with($voucher->code, 'FREE-GEPREK-')) {
            // Dapatkan list menu_item_id dari cart
            $menuItemIds = [];
            foreach ($cart as $key => $item) {
                if (is_array($item)) {
                    if (isset($item['menu_item_id'])) {
                        $menuItemIds[] = (int) $item['menu_item_id'];
                    } elseif (isset($item['id'])) {
                        $menuItemIds[] = (int) $item['id'];
                    }
                } else {
                    if (is_numeric($key)) {
                        $menuItemIds[] = (int) $key;
                    }
                }
            }

            // Cari item dengan nama "Paket Nasi Ayam Geprek" di DB berdasarkan ID yang ada di keranjang
            $menuItems = \App\Models\MenuItem::whereIn('id', $menuItemIds)->get();
            $geprekItem = $menuItems->first(fn ($item) => strtolower(trim($item->name)) === 'paket nasi ayam geprek');

            if (! $geprekItem) {
                throw new \InvalidArgumentException('Voucher ini hanya dapat digunakan jika terdapat menu Paket Nasi Ayam Geprek di dalam keranjang.');
            }

            // Nilai diskon adalah harga dari 1 porsi menu Geprek tersebut
            $discount = (float) $geprekItem->price;
        } else {
            if ($subtotal < (float) $voucher->minimum_order) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Minimum pembelian untuk voucher ini adalah Rp %s.',
                        number_format($voucher->minimum_order, 0, ',', '.')
                    )
                );
            }

            $discount = $voucher->calculateDiscount($subtotal);
        }

        if ($discount <= 0) {
            throw new \InvalidArgumentException('Voucher tidak memberikan nilai diskon untuk order ini.');
        }

        return [
            'voucher'  => $voucher,
            'discount' => $discount,
        ];
    }

    /**
     * Catat penggunaan voucher ke voucher_uses dan increment uses_count.
     * HARUS dipanggil dalam DB::transaction.
     *
     * @param  Voucher  $voucher
     * @param  Order    $order
     * @param  float    $discountApplied
     */
    public function recordUse(Voucher $voucher, Order $order, float $discountApplied): VoucherUse
    {
        // Increment counter penggunaan voucher secara atomik
        $voucher->increment('uses_count');

        $use = VoucherUse::create([
            'voucher_id'       => $voucher->id,
            'order_id'         => $order->id,
            'member_id'        => $order->member_id,
            'discount_applied' => $discountApplied,
        ]);

        Log::info(
            "VoucherService: Voucher '{$voucher->code}' digunakan pada Order#{$order->id}. "
            . "Diskon: {$discountApplied}. Total uses: {$voucher->uses_count}"
        );

        return $use;
    }

    /**
     * Kembalikan uses_count saat order dibatalkan.
     * HARUS dipanggil dalam DB::transaction.
     */
    public function rollbackUse(int $orderId): void
    {
        $use = VoucherUse::where('order_id', $orderId)->first();

        if (! $use) {
            return;
        }

        $voucher = Voucher::find($use->voucher_id);
        if ($voucher && $voucher->uses_count > 0) {
            $voucher->decrement('uses_count');
        }

        $use->delete();

        Log::info("VoucherService: Rollback penggunaan voucher pada Order#{$orderId}");
    }
}
