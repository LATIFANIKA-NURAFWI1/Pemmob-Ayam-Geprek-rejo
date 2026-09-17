<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_ingredients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('unit', 30)->comment('e.g. gram, ml, pcs, liter');
            $table->decimal('current_stock', 14, 3)->unsigned()->default(0);
            $table->decimal('minimum_stock', 14, 3)->unsigned()->default(0)->comment('Alert threshold');
            $table->decimal('unit_cost', 14, 4)->unsigned()->default(0)->comment('Harga beli per unit (untuk kalkulasi HPP)');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ingredients');
    }
};
