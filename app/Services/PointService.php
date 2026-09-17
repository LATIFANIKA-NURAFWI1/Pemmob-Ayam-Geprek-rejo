<?php

namespace App\Services;

use App\Models\Member;
use App\Models\Order;
use App\Models\PointLog;
use Illuminate\Support\Facades\Log;

class PointService
{
    /**
     * Formula: poin = floor(total_amount / 1000)
     * Artinya: setiap Rp 1.000 = 1 poin.
     */
    public function calculateEarnedPoints(float $totalAmount): int
    {
        return (int) floor($totalAmount / 1000);
    }

    /**
     * Tambah poin ke member dan catat di point_logs.
     * HARUS dipanggil dalam DB::transaction.
     *
     * @param  Member  $member
     * @param  Order   $order
     * @return int  jumlah poin yang diperoleh
     */
    public function earnPoints(Member $member, Order $order): int
    {
        $pointsEarned = $this->calculateEarnedPoints((float) $order->total_amount);

        if ($pointsEarned <= 0) {
            return 0;
        }

        $newBalance = $member->points + $pointsEarned;

        // Update saldo poin member
        $member->increment('points', $pointsEarned);

        // Catat di point_logs
        PointLog::create([
            'member_id'   => $member->id,
            'order_id'    => $order->id,
            'type'        => 'earn',
            'points'      => $pointsEarned,
            'balance_after' => $newBalance,
            'description' => "Poin dari order #{$order->order_number}",
        ]);

        // Update statistik order
        $order->update(['points_earned' => $pointsEarned]);

        Log::info("PointService: Member#{$member->id} earn {$pointsEarned} poin dari order#{$order->id}. Saldo: {$newBalance}");

        return $pointsEarned;
    }

    // ── Konstanta Program Reward ─────────────────────────────────────────────

    public const REDEEM_POINTS_REQUIRED = 150;
    public const REDEEM_DISCOUNT_VALUE  = 15000.0; // Rp 15.000 = 1 Paket Geprek Gratis

    /**
     * Cek apakah member bisa menukarkan poin (saldo >= 150).
     */
    public function canRedeem(Member $member): bool
    {
        return $member->points >= self::REDEEM_POINTS_REQUIRED;
    }

    /**
     * Validasi apakah cart mengandung item "Paket Geprek" / "Paket Nasi Geprek".
     * Aturan bisnis: poin hanya bisa ditukar jika ada item Paket Geprek di keranjang.
     *
     * @param  array<string, array{name: string, ...}>  $cart  Session cart
     */
    public function cartHasGeprekItem(array $cart): bool
    {
        foreach ($cart as $item) {
            $name = strtolower($item['name'] ?? '');
            if (str_contains($name, 'paket') && (
                str_contains($name, 'geprek') || str_contains($name, 'nasi')
            )) {
                return true;
            }
        }
        return false;
    }

    /**
     * Hitung diskon dari penukaran poin.
     * Aturan: 150 poin = Rp 15.000 (gratis 1 Paket Geprek).
     *
     * @param  Member  $member
     * @param  bool    $wantsRedeem     Apakah pelanggan mengaktifkan redeem
     * @param  array   $cart            Session cart untuk validasi item Geprek
     * @param  float   $orderTotal      Total sebelum redeem
     * @return array{discount: float, points_used: int}
     *
     * @throws \InvalidArgumentException
     */
    public function calculateRedemption(Member $member, int $pointsToRedeem, float $orderTotal): array
    {
        if ($pointsToRedeem <= 0) {
            return ['discount' => 0.0, 'points_used' => 0];
        }

        if ($pointsToRedeem !== self::REDEEM_POINTS_REQUIRED) {
            throw new \InvalidArgumentException(
                'Penukaran hanya bisa dilakukan sebesar ' . self::REDEEM_POINTS_REQUIRED . ' poin sekaligus.'
            );
        }

        if ($member->points < self::REDEEM_POINTS_REQUIRED) {
            throw new \InvalidArgumentException(
                "Poin tidak mencukupi. Saldo: {$member->points}, dibutuhkan: " . self::REDEEM_POINTS_REQUIRED . '.');
        }

        // 150 poin = Rp 15.000 (harga 1 Paket Geprek gratis)
        $actualDiscount = min(self::REDEEM_DISCOUNT_VALUE, $orderTotal);

        return [
            'discount'    => $actualDiscount,
            'points_used' => self::REDEEM_POINTS_REQUIRED,
        ];
    }

    /**
     * Eksekusi pemotongan poin setelah order confirmed.
     * HARUS dipanggil dalam DB::transaction.
     *
     * @param  Member  $member
     * @param  Order   $order
     * @param  int     $pointsToRedeem
     */
    public function redeemPoints(Member $member, Order $order, int $pointsToRedeem): void
    {
        if ($pointsToRedeem <= 0) {
            return;
        }

        if ($member->points < $pointsToRedeem) {
            throw new \InvalidArgumentException(
                "Poin tidak mencukupi saat eksekusi redeem. Saldo: {$member->points}."
            );
        }

        $newBalance = $member->points - $pointsToRedeem;
        $member->decrement('points', $pointsToRedeem);

        PointLog::create([
            'member_id'     => $member->id,
            'order_id'      => $order->id,
            'type'          => 'redeem',
            'points'        => -$pointsToRedeem,
            'balance_after' => $newBalance,
            'description'   => "Redeem poin untuk order #{$order->order_number}",
        ]);

        Log::info("PointService: Member#{$member->id} redeem {$pointsToRedeem} poin. Saldo: {$newBalance}");
    }

    /**
     * Adjustment poin manual oleh admin.
     */
    public function adjustPoints(Member $member, int $delta, string $reason): void
    {
        $newBalance = $member->points + $delta;

        if ($newBalance < 0) {
            throw new \InvalidArgumentException('Saldo poin tidak boleh negatif.');
        }

        $member->update(['points' => $newBalance]);

        PointLog::create([
            'member_id'     => $member->id,
            'order_id'      => null,
            'type'          => 'adjustment',
            'points'        => $delta,
            'balance_after' => $newBalance,
            'description'   => $reason,
        ]);
    }

    /**
     * Cek dan auto-redeem poin member jika mencapai target 150 poin.
     * Mengurangi 150 poin per voucher, membuat voucher baru, dan menyimpan ke session.
     */
    public function checkAndAutoRedeemReward(Member $member): ?string
    {
        if ($member->points < 150) {
            return null;
        }

        $lastCode = null;
        while ($member->points >= 150) {
            if (\Illuminate\Support\Facades\DB::getDriverName() !== 'sqlite') {
                \Illuminate\Support\Facades\DB::statement('SET TRANSACTION ISOLATION LEVEL REPEATABLE READ');
            }
            $lastCode = \Illuminate\Support\Facades\DB::transaction(function () use ($member) {
                // Re-fetch member under lock to avoid race conditions
                $memberLocked = Member::lockForUpdate()->find($member->id);
                if (!$memberLocked || $memberLocked->points < 150) {
                    return null;
                }

                // Deduct 150 points
                $pointsToDeduct = 150;
                $newBalance = $memberLocked->points - $pointsToDeduct;
                $memberLocked->decrement('points', $pointsToDeduct);

                // Record in point_logs
                PointLog::create([
                    'member_id'     => $memberLocked->id,
                    'order_id'      => null,
                    'type'          => 'redeem',
                    'points'        => -$pointsToDeduct,
                    'balance_after' => $newBalance,
                    'description'   => "Auto-redemption 150 poin untuk Voucher Paket Nasi Ayam Geprek Gratis",
                ]);

                // Generate a unique 5-char alphanumeric code
                do {
                    $suffix = strtoupper(\Illuminate\Support\Str::random(5));
                    $code = 'FREE-GEPREK-' . $suffix;
                } while (\App\Models\Voucher::where('code', $code)->exists());

                // Create the Voucher
                \App\Models\Voucher::create([
                    'member_id'        => $memberLocked->id,
                    'code'             => $code,
                    'name'             => 'Gratis 1 Paket Nasi Ayam Geprek',
                    'description'      => 'Reward member penukaran 150 poin. Berlaku untuk 1 Paket Nasi Ayam Geprek gratis.',
                    'discount_type'    => 'fixed',
                    'discount_value'   => 15000.00,
                    'minimum_order'    => 0.00,
                    'maximum_discount' => 22000.00,
                    'max_uses'         => 1,
                    'uses_count'       => 0,
                    'is_active'        => true,
                    'member_only'      => true,
                    'starts_at'        => now(),
                    'expires_at'       => now()->addDays(7),
                ]);

                Log::info("PointService: Auto-redeemed 150 points for Member#{$memberLocked->id}. Generated voucher: {$code}");

                return $code;
            });

            if ($lastCode) {
                // Store in session
                $redeemed = session('reward_vouchers_redeemed', []);
                $redeemed[] = $lastCode;
                session(['reward_vouchers_redeemed' => $redeemed]);
            }

            // Refresh member state
            $member->refresh();
        }

        return $lastCode;
    }
}
