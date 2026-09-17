<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('point_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->cascadeOnDelete();
            $table->foreignId('order_id')
                ->nullable()
                ->constrained('orders')
                ->nullOnDelete();
            $table->enum('type', ['earn', 'redeem', 'adjustment', 'expire'])
                ->comment('Tipe mutasi poin');
            $table->integer('points')
                ->comment('Positif untuk earn/adjustment+, negatif untuk redeem/expire');
            $table->integer('balance_after')->unsigned()
                ->comment('Saldo poin setelah mutasi ini');
            $table->string('description', 255)->nullable();
            $table->timestamps();

            $table->index(['member_id', 'created_at']);
            $table->index(['order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_logs');
    }
};
