<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $paketNasi = Category::where('slug', 'paket-nasi')->first();
        $ayam     = Category::where('slug', 'ayam')->first();
        $minuman  = Category::where('slug', 'minuman')->first();
        $snack    = Category::where('slug', 'snack')->first();

        $menus = [
            // ── Ayam Geprek ──────────────────────────────────────────────────
            [
                'category_id'  => $ayam?->id,
                'name'         => 'Geprek Original',
                'slug'         => 'geprek-original',
                'description'  => 'Ayam goreng crispy diulek dengan sambal bawang segar',
                'price'        => 15000,
                'sort_order'   => 1,
            ],
            [
                'category_id'  => $ayam?->id,
                'name'         => 'Geprek Mozzarella',
                'slug'         => 'geprek-mozzarella',
                'description'  => 'Geprek original + keju mozzarella leleh yang memanjakan',
                'price'        => 22000,
                'sort_order'   => 2,
            ],
            [
                'category_id'  => $ayam?->id,
                'name'         => 'Geprek Super Pedas',
                'slug'         => 'geprek-super-pedas',
                'description'  => 'Level kepedasan maksimal, cocok buat pecinta pedas sejati',
                'price'        => 17000,
                'sort_order'   => 3,
            ],
            [
                'category_id'  => $ayam?->id,
                'name'         => 'Geprek Saus Tiram',
                'slug'         => 'geprek-saus-tiram',
                'description'  => 'Geprek dengan siraman saus tiram gurih spesial',
                'price'        => 18000,
                'sort_order'   => 4,
            ],
            // ── Ayam Goreng ──────────────────────────────────────────────────
            [
                'category_id'  => $ayam?->id,
                'name'         => 'Ayam Goreng Biasa',
                'slug'         => 'ayam-goreng-biasa',
                'description'  => 'Ayam goreng bumbu kuning renyah khas rumahan',
                'price'        => 13000,
                'sort_order'   => 1,
            ],
            [
                'category_id'  => $ayam?->id,
                'name'         => 'Ayam Goreng Crispy',
                'slug'         => 'ayam-goreng-crispy',
                'description'  => 'Ayam goreng dibalut tepung crispy berlapis',
                'price'        => 16000,
                'sort_order'   => 2,
            ],
            // ── Nasi & Lauk ──────────────────────────────────────────────────
            [
                'category_id'  => $paketNasi?->id,
                'name'         => 'Nasi Putih',
                'slug'         => 'nasi-putih',
                'description'  => 'Nasi putih pulen porsi standar',
                'price'        => 4000,
                'sort_order'   => 1,
            ],
            [
                'category_id'  => $paketNasi?->id,
                'name'         => 'Nasi Jumbo',
                'slug'         => 'nasi-jumbo',
                'description'  => 'Nasi putih porsi jumbo, cocok buat yang lapar berat',
                'price'        => 6000,
                'sort_order'   => 2,
            ],
            [
                'category_id'  => $paketNasi?->id,
                'name'         => 'Tahu Goreng',
                'slug'         => 'tahu-goreng',
                'description'  => 'Tahu goreng crispy gurih',
                'price'        => 3000,
                'sort_order'   => 3,
            ],
            [
                'category_id'  => $paketNasi?->id,
                'name'         => 'Tempe Goreng',
                'slug'         => 'tempe-goreng',
                'description'  => 'Tempe goreng tepung renyah',
                'price'        => 3000,
                'sort_order'   => 4,
            ],
            // ── Minuman ──────────────────────────────────────────────────────
            [
                'category_id'  => $minuman?->id,
                'name'         => 'Es Teh Manis',
                'slug'         => 'es-teh-manis',
                'description'  => 'Teh manis segar dengan es batu',
                'price'        => 5000,
                'sort_order'   => 1,
            ],
            [
                'category_id'  => $minuman?->id,
                'name'         => 'Es Jeruk',
                'slug'         => 'es-jeruk',
                'description'  => 'Jeruk peras segar dengan es batu',
                'price'        => 7000,
                'sort_order'   => 2,
            ],
            [
                'category_id'  => $minuman?->id,
                'name'         => 'Air Mineral',
                'slug'         => 'air-mineral',
                'description'  => 'Air mineral botol 600ml',
                'price'        => 4000,
                'sort_order'   => 3,
            ],
            [
                'category_id'  => $minuman?->id,
                'name'         => 'Es Teh Tarik',
                'slug'         => 'es-teh-tarik',
                'description'  => 'Teh tarik susu creamy dengan es batu',
                'price'        => 10000,
                'sort_order'   => 4,
            ],
            // ── Snack & Extra ─────────────────────────────────────────────────
            [
                'category_id'  => $snack?->id,
                'name'         => 'Kentang Goreng',
                'slug'         => 'kentang-goreng',
                'description'  => 'Kentang goreng crispy dengan saus sambal',
                'price'        => 10000,
                'sort_order'   => 1,
            ],
            [
                'category_id'  => $snack?->id,
                'name'         => 'Kerupuk',
                'slug'         => 'kerupuk',
                'description'  => 'Kerupuk udang renyah',
                'price'        => 2000,
                'sort_order'   => 2,
            ],
        ];

        foreach ($menus as $menu) {
            MenuItem::updateOrCreate(
                ['slug' => $menu['slug']],
                array_merge($menu, ['is_available' => true])
            );
        }

        $this->command->info('✅ Menu Items seeded: ' . count($menus) . ' menu');
    }
}
