<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // recipes: pivot antara menu_items dan stock_ingredients
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_item_id')
                ->constrained('menu_items')
                ->cascadeOnDelete();
            $table->foreignId('stock_ingredient_id')
                ->constrained('stock_ingredients')
                ->cascadeOnDelete();
            $table->decimal('qty_used', 14, 4)->unsigned()
                ->comment('Jumlah bahan yang dipakai per 1 porsi menu');
            $table->timestamps();

            $table->unique(['menu_item_id', 'stock_ingredient_id'], 'recipe_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};
