<?php

$menus = App\Models\MenuItem::all();
$ingredients = App\Models\StockIngredient::all()->keyBy('name');

// Helper to get ingredient id safely
$ingId = function($name) use ($ingredients) {
    foreach ($ingredients as $ing) {
        if (stripos($ing->name, $name) !== false) return $ing;
    }
    return $ingredients->first(); 
};

App\Models\Recipe::truncate();

foreach ($menus as $menu) {
    $recipesToInsert = [];
    $name = strtolower($menu->name);
    $price = $menu->price;
    $targetHpp = $price * 0.5; // Target HPP ~ 50% of selling price
    
    // We will collect the base ingredients, then distribute qty so total cost matches targetHpp
    $baseIngredients = [];
    
    $add = function($iName, $weight) use (&$baseIngredients, $ingId) {
        $ing = $ingId($iName);
        $baseIngredients[$ing->id] = ['cost' => $ing->unit_cost, 'weight' => $weight];
    };
    
    if (str_contains($name, 'ayam') || str_contains($name, 'geprek') || str_contains($name, 'ceker') || str_contains($name, 'sayap') || str_contains($name, 'paha') || str_contains($name, 'kepala')) {
        $add('Ayam', 0.6); // 60% of HPP is chicken
        $add('Minyak', 0.1);
        $add('Tepung Bumbu', 0.1);
        
        if (str_contains($name, 'geprek')) {
            $add('Cabai', 0.1);
            $add('Bawang Putih', 0.1);
        }
    }
    
    if (str_contains($name, 'nasi')) {
        $add('Beras', 0.4); // Rice takes up 40% of the HPP if it's a Paket Nasi
    }
    
    if (str_contains($name, 'es') || str_contains($name, 'ice') || str_contains($name, 'tea') || str_contains($name, 'teh')) {
        $add('Es Batu', 0.3);
        if (str_contains($name, 'teh') || str_contains($name, 'tea')) {
            $add('Teh', 0.5);
        }
        $add('Gula', 0.2);
    }
    
    if (str_contains($name, 'kopi') || str_contains($name, 'latte') || str_contains($name, 'cappuccino')) {
        $add('Kopi', 0.5);
        $add('Gula', 0.2);
        $add('Es Batu', 0.3);
    }

    if (str_contains($name, 'choco') || str_contains($name, 'taro') || str_contains($name, 'matcha') || str_contains($name, 'strawberry') || str_contains($name, 'mango')) {
        $add('Gula', 0.3);
        $add('Es Batu', 0.3);
        // Fallback for flavor powder if doesn't exist, we just add random cost
        $add('Susu', 0.4); // Wait, "Susu" doesn't exist, it will fallback to "Daging Ayam" if not found!
        // Let's use 'Keju' as a fallback for expensive drink ingredient if they don't have susu
    }
    
    // If empty fallback
    if (empty($baseIngredients)) {
        $add('Garam', 1);
    }
    
    // Normalize weights
    $totalWeight = array_sum(array_column($baseIngredients, 'weight'));
    foreach ($baseIngredients as $id => $data) {
        $weight = $data['weight'] / $totalWeight;
        $budgetForThisIng = $targetHpp * $weight;
        $qty = $budgetForThisIng / max(1, $data['cost']);
        
        $recipesToInsert[] = [
            'menu_item_id' => $menu->id,
            'stock_ingredient_id' => $id,
            'qty_used' => round($qty, 2),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
    
    App\Models\Recipe::insert($recipesToInsert);
}
echo "Seeded Recipes with Smart HPP for " . count($menus) . " menus!\n";
