<?php

namespace Database\Seeders;

use App\Models\StockIngredient;
use Illuminate\Database\Seeder;

class StockIngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            // Bahan utama ayam
            [
                'name'          => 'Ayam Potong',
                'unit'          => 'gram',
                'current_stock' => 50000,  // 50 kg
                'minimum_stock' => 5000,   // Alert di 5 kg
                'unit_cost'     => 0.038,  // Rp 38/gram = Rp 38.000/kg
            ],
            [
                'name'          => 'Tepung Terigu',
                'unit'          => 'gram',
                'current_stock' => 20000,
                'minimum_stock' => 2000,
                'unit_cost'     => 0.013,  // Rp 13/gram = Rp 13.000/kg
            ],
            [
                'name'          => 'Tepung Bumbu Crispy',
                'unit'          => 'gram',
                'current_stock' => 10000,
                'minimum_stock' => 1000,
                'unit_cost'     => 0.025,  // Rp 25/gram
            ],
            [
                'name'          => 'Minyak Goreng',
                'unit'          => 'ml',
                'current_stock' => 30000,  // 30 liter
                'minimum_stock' => 3000,
                'unit_cost'     => 0.022,  // Rp 22/ml = Rp 22.000/liter
            ],
            // Bumbu sambal geprek
            [
                'name'          => 'Cabai Rawit Merah',
                'unit'          => 'gram',
                'current_stock' => 5000,
                'minimum_stock' => 500,
                'unit_cost'     => 0.08,   // Rp 80/gram = Rp 80.000/kg
            ],
            [
                'name'          => 'Bawang Putih',
                'unit'          => 'gram',
                'current_stock' => 3000,
                'minimum_stock' => 300,
                'unit_cost'     => 0.04,   // Rp 40/gram
            ],
            [
                'name'          => 'Garam',
                'unit'          => 'gram',
                'current_stock' => 2000,
                'minimum_stock' => 200,
                'unit_cost'     => 0.003,  // Rp 3/gram
            ],
            [
                'name'          => 'Keju Mozzarella',
                'unit'          => 'gram',
                'current_stock' => 3000,
                'minimum_stock' => 300,
                'unit_cost'     => 0.12,   // Rp 120/gram = Rp 120.000/kg
            ],
            [
                'name'          => 'Saus Tiram',
                'unit'          => 'ml',
                'current_stock' => 2000,
                'minimum_stock' => 200,
                'unit_cost'     => 0.035,  // Rp 35/ml
            ],
            // Nasi
            [
                'name'          => 'Beras',
                'unit'          => 'gram',
                'current_stock' => 50000,  // 50 kg
                'minimum_stock' => 5000,
                'unit_cost'     => 0.015,  // Rp 15/gram = Rp 15.000/kg
            ],
            // Tahu & Tempe
            [
                'name'          => 'Tahu Putih',
                'unit'          => 'pcs',
                'current_stock' => 200,
                'minimum_stock' => 20,
                'unit_cost'     => 1500,   // Rp 1.500/pcs
            ],
            [
                'name'          => 'Tempe',
                'unit'          => 'gram',
                'current_stock' => 10000,
                'minimum_stock' => 1000,
                'unit_cost'     => 0.012,  // Rp 12/gram
            ],
            // Minuman
            [
                'name'          => 'Teh Celup',
                'unit'          => 'pcs',
                'current_stock' => 500,
                'minimum_stock' => 50,
                'unit_cost'     => 300,    // Rp 300/pcs
            ],
            [
                'name'          => 'Gula Pasir',
                'unit'          => 'gram',
                'current_stock' => 10000,
                'minimum_stock' => 1000,
                'unit_cost'     => 0.017,  // Rp 17/gram
            ],
            [
                'name'          => 'Jeruk Segar',
                'unit'          => 'pcs',
                'current_stock' => 300,
                'minimum_stock' => 30,
                'unit_cost'     => 1500,   // Rp 1.500/buah
            ],
            [
                'name'          => 'Air Mineral Botol',
                'unit'          => 'botol',
                'current_stock' => 100,
                'minimum_stock' => 24,
                'unit_cost'     => 2500,   // Rp 2.500/botol
            ],
            [
                'name'          => 'Susu Kental Manis',
                'unit'          => 'ml',
                'current_stock' => 5000,
                'minimum_stock' => 500,
                'unit_cost'     => 0.025,  // Rp 25/ml
            ],
            // Snack
            [
                'name'          => 'Kentang Beku',
                'unit'          => 'gram',
                'current_stock' => 10000,
                'minimum_stock' => 1000,
                'unit_cost'     => 0.035,  // Rp 35/gram
            ],
            [
                'name'          => 'Kerupuk Udang',
                'unit'          => 'pcs',
                'current_stock' => 500,
                'minimum_stock' => 50,
                'unit_cost'     => 500,    // Rp 500/pcs
            ],
        ];

        foreach ($ingredients as $ingredient) {
            StockIngredient::updateOrCreate(
                ['name' => $ingredient['name']],
                $ingredient
            );
        }

        $this->command->info('✅ Stock Ingredients seeded: ' . count($ingredients) . ' bahan baku');
    }
}
