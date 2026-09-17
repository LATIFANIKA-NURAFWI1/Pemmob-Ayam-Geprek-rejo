<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique()
                ->comment('Format: GR-YYYYMMDD-XXXX, contoh: GR-20260101-0001');
            $table->smallInteger('queue_number')->unsigned()
                ->comment('Nomor antrean harian, reset tiap hari (1–999)');
            $table->foreignId('member_id')
                ->nullable()
                ->constrained('members')
                ->nullOnDelete();
            $table->foreignId('voucher_id')
                ->nullable()
                ->constrained('vouchers')
                ->nullOnDelete();
            $table->string('table_number', 20)->nullable()
                ->comment('Nomor meja dari scan QR');
            $table->enum('type', ['dine_in', 'takeaway'])->default('dine_in');
            $table->enum('status', [
                'pending',
                'confirmed',
                'preparing',
                'completed',
                'cancelled',
            ])->default('pending');
            $table->enum('payment_method', ['qris', 'cash'])->nullable();
            $table->decimal('subtotal', 14, 2)->unsigned()->default(0);
            $table->decimal('discount_amount', 14, 2)->unsigned()->default(0);
            $table->decimal('points_redeemed_amount', 14, 2)->unsigned()->default(0)
                ->comment('Nilai rupiah dari poin yang digunakan');
            $table->integer('points_redeemed')->unsigned()->default(0)
                ->comment('Jumlah poin yang digunakan');
            $table->decimal('total_amount', 14, 2)->unsigned()->default(0)
                ->comment('Grand total yang harus dibayar');
            $table->decimal('total_hpp', 14, 2)->unsigned()->default(0)
                ->comment('Total HPP order (Σ hpp_snapshot × qty), diisi saat konfirmasi');
            $table->integer('points_earned')->unsigned()->default(0)
                ->comment('Poin yang didapat dari transaksi ini');
            $table->text('notes')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('confirmed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'created_at']);
            $table->index(['member_id', 'status']);
            $table->index(['created_at', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
