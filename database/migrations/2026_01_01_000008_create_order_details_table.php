<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            // ON DELETE SET NULL: jika menu dihapus (soft/hard), order_detail tetap ada
            $table->foreignId('menu_item_id')
                ->nullable()
                ->constrained('menu_items')
                ->nullOnDelete();
            // Snapshot nama menu saat order dibuat
            $table->string('menu_item_name', 150)
                ->comment('Snapshot nama menu saat order dibuat');
            $table->smallInteger('quantity')->unsigned()->default(1);
            $table->decimal('unit_price', 12, 2)->unsigned()
                ->comment('Harga jual per item saat order dibuat (snapshot)');
            $table->decimal('subtotal', 12, 2)->unsigned()
                ->comment('unit_price × quantity');
            $table->decimal('hpp_snapshot', 12, 4)->unsigned()->default(0)
                ->comment('HPP per unit saat konfirmasi pembayaran: Σ(unit_cost × qty_used)');
            $table->text('notes')->nullable()->comment('Catatan khusus per item (misal: tidak pedas)');
            $table->timestamps();

            $table->index(['order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
