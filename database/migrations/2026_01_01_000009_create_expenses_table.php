<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('description', 255);
            $table->enum('category', [
                'bahan_baku',
                'operasional',
                'gaji',
                'perawatan',
                'marketing',
                'lainnya',
            ])->default('operasional');
            $table->decimal('amount', 14, 2)->unsigned();
            $table->date('expense_date');
            $table->string('receipt_image', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('recorded_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();

            $table->index(['expense_date']);
            $table->index(['category', 'expense_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
