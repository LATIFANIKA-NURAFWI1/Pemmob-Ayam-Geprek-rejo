<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

/**
 * OrderSeeder — membuat data transaksi dummy hari ini agar dashboard
 * mobile menampilkan statistik, pesanan terkini, dan menu terlaris.
 *
 * Setiap pesanan berisi 1–4 item acak dari menu yang sudah ada di DB.
 * Status, tipe, dan metode bayar dibuat bervariasi untuk simulasi realistis.
 */
class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $menuItems = MenuItem::all();

        if ($menuItems->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada menu items di database. Jalankan MenuItemSeeder terlebih dahulu.');
            return;
        }

        $today    = Carbon::today();
        $statuses = ['pending', 'confirmed', 'preparing', 'completed'];
        $types    = ['dine_in', 'takeaway'];
        $payments = ['qris', 'cash'];

        // Buat 20 pesanan hari ini
        for ($i = 1; $i <= 20; $i++) {
            $status        = $statuses[array_rand($statuses)];
            $type          = $types[array_rand($types)];
            $paymentMethod = in_array($status, ['confirmed', 'preparing', 'completed'])
                ? $payments[array_rand($payments)]
                : null;

            // Waktu pembuatan di jam operasional (09:00–21:00)
            $createdAt = $today->copy()->addHours(rand(9, 20))->addMinutes(rand(0, 59));

            $order = Order::create([
                'order_number'  => sprintf('GR-%s-%04d', $today->format('Ymd'), $i),
                'queue_number'  => $i,
                'type'          => $type,
                'status'        => $status,
                'payment_method'=> $paymentMethod,
                'table_number'  => $type === 'dine_in' ? 'M' . rand(1, 12) : null,
                'subtotal'      => 0,
                'total_amount'  => 0,
                'total_hpp'     => 0,
                'notes'         => null,
                'confirmed_at'  => in_array($status, ['confirmed', 'preparing', 'completed']) ? $createdAt->copy()->addMinutes(rand(1, 5)) : null,
                'completed_at'  => $status === 'completed' ? $createdAt->copy()->addMinutes(rand(10, 30)) : null,
                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ]);

            // Tambahkan 1–4 item per order
            $orderItems = $menuItems->random(rand(1, min(4, $menuItems->count())));
            $subtotal   = 0;
            $totalHpp   = 0;

            foreach ($orderItems as $menu) {
                $qty       = rand(1, 3);
                $unitPrice = (float) $menu->price;
                $lineTotal = $unitPrice * $qty;
                $hppUnit   = $menu->calculateHppPerUnit();

                OrderDetail::create([
                    'order_id'       => $order->id,
                    'menu_item_id'   => $menu->id,
                    'menu_item_name' => $menu->name,
                    'quantity'       => $qty,
                    'unit_price'     => $unitPrice,
                    'subtotal'       => $lineTotal,
                    'hpp_snapshot'   => $hppUnit,
                    'created_at'     => $createdAt,
                    'updated_at'     => $createdAt,
                ]);

                $subtotal += $lineTotal;
                $totalHpp += $hppUnit * $qty;
            }

            $order->update([
                'subtotal'     => $subtotal,
                'total_amount' => $subtotal,
                'total_hpp'    => $totalHpp,
            ]);
        }

        $this->command->info("✅ 20 pesanan dummy berhasil dibuat untuk tanggal {$today->format('d/m/Y')}.");
    }
}
