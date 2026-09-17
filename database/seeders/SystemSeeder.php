<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Member;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\StockIngredient;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding 25 Dummy Data for Testing...');

        // 0. Create Admin User
        $admin = \App\Models\User::firstOrCreate(
            ['email' => 'admin@geprek.com'],
            [
                'name' => 'Admin Kasir',
                'password' => bcrypt('password'),
            ]
        );
        $this->command->info('✅ Admin User seeded.');

        // 1. Categories (5)
        $categories = collect([
            'Ayam Geprek', 'Minuman', 'Paket Nasi', 'Camilan', 'Ekstra'
        ])->map(function ($name) {
            return Category::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'is_active' => true,
                ]
            );
        });
        $this->command->info('✅ Categories seeded.');

        // 2. Menu Items (25)
        $menusData = [
            ['Ayam Geprek Original', 'Ayam Geprek'],
            ['Ayam Geprek Keju', 'Ayam Geprek'],
            ['Ayam Geprek Mozzarella', 'Ayam Geprek'],
            ['Ayam Geprek Sambal Matah', 'Ayam Geprek'],
            ['Ayam Geprek Level Dewa', 'Ayam Geprek'],
            
            ['Es Teh Manis', 'Minuman'],
            ['Es Jeruk', 'Minuman'],
            ['Es Lemon Tea', 'Minuman'],
            ['Kopi Hitam Panas', 'Minuman'],
            ['Es Kopi Susu', 'Minuman'],

            ['Paket Nasi Geprek Ori', 'Paket Nasi'],
            ['Paket Nasi Geprek Keju', 'Paket Nasi'],
            ['Paket Nasi Geprek Mozza', 'Paket Nasi'],
            ['Paket Nasi Kulit Krispi', 'Paket Nasi'],
            ['Paket Komplit Rejo', 'Paket Nasi'],

            ['Jamur Krispi', 'Camilan'],
            ['Tahu Crispy', 'Camilan'],
            ['Tempe Mendoan', 'Camilan'],
            ['Kulit Ayam Krispi', 'Camilan'],
            ['Sosis Bakar', 'Camilan'],

            ['Nasi Putih', 'Ekstra'],
            ['Telur Dadar', 'Ekstra'],
            ['Telur Mata Sapi', 'Ekstra'],
            ['Sambal Tambahan', 'Ekstra'],
            ['Kerupuk', 'Ekstra'],
        ];

        $menuItems = collect();
        foreach ($menusData as $index => [$name, $catName]) {
            $category = $categories->firstWhere('name', $catName);
            $price = rand(5, 25) * 1000;
            $menuItems->push(MenuItem::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'category_id' => $category->id,
                    'name' => $name,
                    'price' => $price,
                    'description' => "Deskripsi enak untuk $name.",
                    'is_available' => true,
                ]
            ));
        }
        $this->command->info('✅ Menu Items seeded (25 items).');

        // 3. Stock Ingredients (25)
        $ingredients = [
            'Daging Ayam', 'Beras', 'Minyak Goreng', 'Cabai Rawit', 'Bawang Putih',
            'Bawang Merah', 'Garam', 'Gula Pasir', 'Tepung Terigu', 'Tepung Bumbu',
            'Keju Cheddar', 'Keju Mozzarella', 'Teh Celup', 'Jeruk Peras', 'Kopi Bubuk',
            'Susu Kental Manis', 'Jamur Tiram', 'Tahu Putih', 'Tempe', 'Kulit Ayam',
            'Sosis Sapi', 'Telur Ayam', 'Tomat', 'Jeruk Nipis', 'Es Batu'
        ];

        foreach ($ingredients as $name) {
            StockIngredient::create([
                'name' => $name,
                'unit' => collect(['kg', 'liter', 'pcs', 'gram', 'ikat'])->random(),
                'current_stock' => rand(10, 100),
                'minimum_stock' => 10,
                'unit_cost' => rand(1, 5) * 1000,
                'notes' => "Stok dummy $name",
            ]);
        }
        $this->command->info('✅ Stock Ingredients seeded (25 items).');

        // 4. Members (25)
        $members = collect();
        for ($i = 1; $i <= 25; $i++) {
            $totalSpent = rand(50, 1000) * 1000;
            $members->push(Member::create([
                'name' => "Member Dummy $i",
                'phone' => '0812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'pin' => bcrypt('123456'),
                'points' => rand(0, 5000),
                'tier' => Member::resolveTier($totalSpent),
                'total_orders' => rand(1, 50),
                'total_spent' => $totalSpent,
                'is_active' => true,
            ]));
        }
        $this->command->info('✅ Members seeded (25 members).');

        // 5. Vouchers (15)
        for ($i = 1; $i <= 15; $i++) {
            $type = collect(['fixed', 'percentage'])->random();
            $value = $type === 'fixed' ? rand(5, 20) * 1000 : rand(5, 50);
            Voucher::create([
                'code' => "PROMO2026_$i",
                'name' => "Promo Dummy $i",
                'discount_type' => $type,
                'discount_value' => $value,
                'minimum_order' => rand(20, 100) * 1000,
                'maximum_discount' => $type === 'percentage' ? rand(10, 50) * 1000 : null,
                'starts_at' => Carbon::now()->subDays(rand(1, 30)),
                'expires_at' => Carbon::now()->addDays(rand(10, 30)),
                'is_active' => true,
                'max_uses' => rand(10, 100),
                'uses_count' => rand(0, 10),
            ]);
        }
        $this->command->info('✅ Vouchers seeded (15 items).');

        // 6. Expenses (25)
        $expenseCategories = ['bahan_baku', 'operasional', 'gaji', 'perawatan', 'marketing', 'lainnya'];
        for ($i = 1; $i <= 25; $i++) {
            Expense::create([
                'expense_date' => Carbon::now()->subDays(rand(0, 30)),
                'category' => collect($expenseCategories)->random(),
                'amount' => rand(5, 50) * 10000,
                'description' => "Pengeluaran dummy $i",
                'recorded_by' => $admin->id,
            ]);
        }
        $this->command->info('✅ Expenses seeded (25 items).');

        // 7. Orders (25)
        for ($i = 1; $i <= 25; $i++) {
            $member = $members->random();
            $items = $menuItems->random(rand(2, 5));
            
            $subtotal = 0;
            $totalHpp = 0;
            $cart = [];

            foreach ($items as $item) {
                $qty = rand(1, 3);
                $subtotal += $item->price * $qty;
                $hpp = $item->price * 0.6; // Mock HPP as 60% of price
                $totalHpp += $hpp * $qty;
                $cart[] = [
                    'menu_item_id' => $item->id,
                    'menu_item_name' => $item->name,
                    'quantity' => $qty,
                    'unit_price' => $item->price,
                    'subtotal' => $item->price * $qty,
                    'hpp_snapshot' => $hpp * $qty,
                ];
            }

            $order = Order::create([
                'order_number' => 'GR-' . Carbon::now()->subDays(rand(0, 30))->format('Ymd') . '-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'queue_number' => $i,
                'member_id' => rand(0, 1) ? $member->id : null,
                'type' => collect(['dine_in', 'takeaway'])->random(),
                'status' => collect(['pending', 'confirmed', 'preparing', 'completed', 'cancelled'])->random(),
                'payment_method' => collect(['cash', 'qris'])->random(),
                'subtotal' => $subtotal,
                'total_hpp' => $totalHpp,
                'total_amount' => $subtotal,
                'created_at' => Carbon::now()->subDays(rand(0, 30)),
            ]);

            foreach ($cart as $c) {
                OrderDetail::create(array_merge($c, ['order_id' => $order->id]));
            }
        }
        $this->command->info('✅ Orders seeded (25 items).');

        $this->command->info('All dummy data seeded successfully!');
    }
}
