<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 12, 2)->unsigned()
                ->comment('Nilai diskon: persen (0-100) atau nominal rupiah');
            $table->decimal('minimum_order', 12, 2)->unsigned()->default(0)
                ->comment('Minimum total belanja untuk menggunakan voucher');
            $table->decimal('maximum_discount', 12, 2)->nullable()
                ->comment('Cap diskon (untuk tipe percentage)');
            $table->integer('max_uses')->unsigned()->default(1)
                ->comment('0 = unlimited');
            $table->integer('uses_count')->unsigned()->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('member_only')->default(false);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
