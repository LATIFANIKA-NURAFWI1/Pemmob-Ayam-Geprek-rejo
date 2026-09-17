<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use App\Models\StockIngredient;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Resep = berapa banyak bahan baku yang dipakai per 1 porsi menu.
 * Kolom qty_used = jumlah satuan unit bahan yang digunakan.
 *
 * Contoh kalkulasi HPP Geprek Original:
 *   Ayam Potong  : 200 gram × Rp 0.038 = Rp  7.600
 *   Tepung Bumbu : 30  gram × Rp 0.025 = Rp    750
 *   Minyak Goreng: 50  ml   × Rp 0.022 = Rp  1.100
 *   Cabai Rawit  : 20  gram × Rp 0.080 = Rp  1.600
 *   Bawang Putih : 10  gram × Rp 0.040 = Rp    400
 *   Garam        : 5   gram × Rp 0.003 = Rp     15
 *   Beras        : 200 gram × Rp 0.015 = Rp  3.000
 *   ─────────────────────────────────────────────────
 *   TOTAL HPP    :                       Rp 14.465
 *   Harga Jual   :                       Rp 15.000
 *   Margin       :                       3.6%
 */
class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // Helper: cari ID bahan baku
        $ing = fn (string $name) => StockIngredient::where('name', $name)->value('id');
        $menu = fn (string $slug) => MenuItem::where('slug', $slug)->value('id');

        $recipes = [
            // ── Geprek Original (Rp 15.000) ──────────────────────────────
            ['menu' => 'geprek-original', 'ingredient' => 'Ayam Potong',      'qty' => 200],
            ['menu' => 'geprek-original', 'ingredient' => 'Tepung Bumbu Crispy', 'qty' => 30],
            ['menu' => 'geprek-original', 'ingredient' => 'Minyak Goreng',    'qty' => 50],
            ['menu' => 'geprek-original', 'ingredient' => 'Cabai Rawit Merah','qty' => 20],
            ['menu' => 'geprek-original', 'ingredient' => 'Bawang Putih',     'qty' => 10],
            ['menu' => 'geprek-original', 'ingredient' => 'Garam',            'qty' => 5],
            ['menu' => 'geprek-original', 'ingredient' => 'Beras',            'qty' => 200],

            // ── Geprek Mozzarella (Rp 22.000) ────────────────────────────
            ['menu' => 'geprek-mozzarella', 'ingredient' => 'Ayam Potong',       'qty' => 200],
            ['menu' => 'geprek-mozzarella', 'ingredient' => 'Tepung Bumbu Crispy','qty' => 30],
            ['menu' => 'geprek-mozzarella', 'ingredient' => 'Minyak Goreng',     'qty' => 50],
            ['menu' => 'geprek-mozzarella', 'ingredient' => 'Cabai Rawit Merah', 'qty' => 20],
            ['menu' => 'geprek-mozzarella', 'ingredient' => 'Bawang Putih',      'qty' => 10],
            ['menu' => 'geprek-mozzarella', 'ingredient' => 'Garam',             'qty' => 5],
            ['menu' => 'geprek-mozzarella', 'ingredient' => 'Keju Mozzarella',   'qty' => 50],
            ['menu' => 'geprek-mozzarella', 'ingredient' => 'Beras',             'qty' => 200],

            // ── Geprek Super Pedas (Rp 17.000) ───────────────────────────
            ['menu' => 'geprek-super-pedas', 'ingredient' => 'Ayam Potong',       'qty' => 200],
            ['menu' => 'geprek-super-pedas', 'ingredient' => 'Tepung Bumbu Crispy','qty' => 30],
            ['menu' => 'geprek-super-pedas', 'ingredient' => 'Minyak Goreng',     'qty' => 50],
            ['menu' => 'geprek-super-pedas', 'ingredient' => 'Cabai Rawit Merah', 'qty' => 40], // 2x lebih pedas
            ['menu' => 'geprek-super-pedas', 'ingredient' => 'Bawang Putih',      'qty' => 10],
            ['menu' => 'geprek-super-pedas', 'ingredient' => 'Garam',             'qty' => 5],
            ['menu' => 'geprek-super-pedas', 'ingredient' => 'Beras',             'qty' => 200],

            // ── Geprek Saus Tiram (Rp 18.000) ────────────────────────────
            ['menu' => 'geprek-saus-tiram', 'ingredient' => 'Ayam Potong',       'qty' => 200],
            ['menu' => 'geprek-saus-tiram', 'ingredient' => 'Tepung Bumbu Crispy','qty' => 30],
            ['menu' => 'geprek-saus-tiram', 'ingredient' => 'Minyak Goreng',     'qty' => 50],
            ['menu' => 'geprek-saus-tiram', 'ingredient' => 'Bawang Putih',      'qty' => 10],
            ['menu' => 'geprek-saus-tiram', 'ingredient' => 'Garam',             'qty' => 5],
            ['menu' => 'geprek-saus-tiram', 'ingredient' => 'Saus Tiram',        'qty' => 30],
            ['menu' => 'geprek-saus-tiram', 'ingredient' => 'Beras',             'qty' => 200],

            // ── Ayam Goreng Biasa (Rp 13.000) ────────────────────────────
            ['menu' => 'ayam-goreng-biasa', 'ingredient' => 'Ayam Potong',  'qty' => 200],
            ['menu' => 'ayam-goreng-biasa', 'ingredient' => 'Tepung Terigu','qty' => 20],
            ['menu' => 'ayam-goreng-biasa', 'ingredient' => 'Minyak Goreng','qty' => 50],
            ['menu' => 'ayam-goreng-biasa', 'ingredient' => 'Bawang Putih', 'qty' => 8],
            ['menu' => 'ayam-goreng-biasa', 'ingredient' => 'Garam',        'qty' => 5],
            ['menu' => 'ayam-goreng-biasa', 'ingredient' => 'Beras',        'qty' => 200],

            // ── Ayam Goreng Crispy (Rp 16.000) ───────────────────────────
            ['menu' => 'ayam-goreng-crispy', 'ingredient' => 'Ayam Potong',       'qty' => 200],
            ['menu' => 'ayam-goreng-crispy', 'ingredient' => 'Tepung Bumbu Crispy','qty' => 40],
            ['menu' => 'ayam-goreng-crispy', 'ingredient' => 'Tepung Terigu',     'qty' => 20],
            ['menu' => 'ayam-goreng-crispy', 'ingredient' => 'Minyak Goreng',     'qty' => 60],
            ['menu' => 'ayam-goreng-crispy', 'ingredient' => 'Garam',             'qty' => 5],
            ['menu' => 'ayam-goreng-crispy', 'ingredient' => 'Beras',             'qty' => 200],

            // ── Nasi Putih (Rp 4.000) ─────────────────────────────────────
            ['menu' => 'nasi-putih', 'ingredient' => 'Beras', 'qty' => 150],

            // ── Nasi Jumbo (Rp 6.000) ─────────────────────────────────────
            ['menu' => 'nasi-jumbo', 'ingredient' => 'Beras', 'qty' => 250],

            // ── Tahu Goreng (Rp 3.000) ────────────────────────────────────
            ['menu' => 'tahu-goreng', 'ingredient' => 'Tahu Putih',   'qty' => 2],
            ['menu' => 'tahu-goreng', 'ingredient' => 'Minyak Goreng','qty' => 20],
            ['menu' => 'tahu-goreng', 'ingredient' => 'Garam',        'qty' => 2],

            // ── Tempe Goreng (Rp 3.000) ───────────────────────────────────
            ['menu' => 'tempe-goreng', 'ingredient' => 'Tempe',       'qty' => 80],
            ['menu' => 'tempe-goreng', 'ingredient' => 'Tepung Terigu','qty' => 15],
            ['menu' => 'tempe-goreng', 'ingredient' => 'Minyak Goreng','qty' => 20],
            ['menu' => 'tempe-goreng', 'ingredient' => 'Garam',        'qty' => 2],

            // ── Es Teh Manis (Rp 5.000) ───────────────────────────────────
            ['menu' => 'es-teh-manis', 'ingredient' => 'Teh Celup',  'qty' => 1],
            ['menu' => 'es-teh-manis', 'ingredient' => 'Gula Pasir', 'qty' => 20],

            // ── Es Jeruk (Rp 7.000) ───────────────────────────────────────
            ['menu' => 'es-jeruk', 'ingredient' => 'Jeruk Segar', 'qty' => 3],
            ['menu' => 'es-jeruk', 'ingredient' => 'Gula Pasir',  'qty' => 15],

            // ── Air Mineral (Rp 4.000) ────────────────────────────────────
            ['menu' => 'air-mineral', 'ingredient' => 'Air Mineral Botol', 'qty' => 1],

            // ── Es Teh Tarik (Rp 10.000) ──────────────────────────────────
            ['menu' => 'es-teh-tarik', 'ingredient' => 'Teh Celup',        'qty' => 1],
            ['menu' => 'es-teh-tarik', 'ingredient' => 'Susu Kental Manis','qty' => 40],
            ['menu' => 'es-teh-tarik', 'ingredient' => 'Gula Pasir',       'qty' => 10],

            // ── Kentang Goreng (Rp 10.000) ────────────────────────────────
            ['menu' => 'kentang-goreng', 'ingredient' => 'Kentang Beku',   'qty' => 150],
            ['menu' => 'kentang-goreng', 'ingredient' => 'Minyak Goreng',  'qty' => 40],
            ['menu' => 'kentang-goreng', 'ingredient' => 'Garam',          'qty' => 3],

            // ── Kerupuk (Rp 2.000) ────────────────────────────────────────
            ['menu' => 'kerupuk', 'ingredient' => 'Kerupuk Udang', 'qty' => 3],
        ];

        // Insert menggunakan upsert untuk idempotent
        foreach ($recipes as $recipe) {
            $menuId = $menu($recipe['menu']);
            $ingId  = $ing($recipe['ingredient']);

            if (! $menuId || ! $ingId) {
                $this->command->warn("  ⚠ Skip: menu '{$recipe['menu']}' atau bahan '{$recipe['ingredient']}' tidak ditemukan");
                continue;
            }

            DB::table('recipes')->updateOrInsert(
                ['menu_item_id' => $menuId, 'stock_ingredient_id' => $ingId],
                ['qty_used' => $recipe['qty'], 'updated_at' => now(), 'created_at' => now()]
            );
        }

        $this->command->info('✅ Recipes seeded: ' . count($recipes) . ' entri resep');
        $this->command->newLine();
        $this->command->info('📊 Estimasi HPP (contoh):');
        $this->command->table(
            ['Menu', 'Harga Jual', 'Est. HPP', 'Margin'],
            [
                ['Geprek Original', 'Rp 15.000', '~Rp 14.465', '~3.6%'],
                ['Geprek Mozzarella', 'Rp 22.000', '~Rp 20.465', '~7%'],
                ['Es Teh Manis', 'Rp 5.000', '~Rp 640', '~87%'],
                ['Nasi Putih', 'Rp 4.000', '~Rp 2.250', '~44%'],
            ]
        );
    }
}
