<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Pake Nasi', 'slug' => 'pake-nasi', 'icon' => '🍚', 'sort_order' => 1],
            ['name' => 'Ayam',      'slug' => 'ayam',      'icon' => '🍗', 'sort_order' => 2],
            ['name' => 'Minuman',   'slug' => 'minuman',   'icon' => '🥤', 'sort_order' => 3],
            ['name' => 'Snack',     'slug' => 'snack',     'icon' => '🍟', 'sort_order' => 4],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['slug' => $cat['slug']], array_merge($cat, ['is_active' => true]));
        }

        $this->command->info('✅ Categories seeded: ' . count($categories) . ' kategori');
    }
}
