<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voucher_uses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('voucher_id')
                ->constrained('vouchers')
                ->cascadeOnDelete();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete();
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('members')
                ->nullOnDelete();
            $table->decimal('discount_applied', 12, 2)->unsigned()
                ->comment('Nilai diskon aktual yang diterapkan ke order ini');
            $table->timestamps();

            $table->unique(['voucher_id', 'order_id']);
            $table->index(['voucher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voucher_uses');
    }
};
