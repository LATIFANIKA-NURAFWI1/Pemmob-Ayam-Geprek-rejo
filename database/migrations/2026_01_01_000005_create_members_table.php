<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 20)->unique()->comment('No. HP sebagai identifier login');
            $table->string('pin', 255)->nullable()->comment('Hashed PIN untuk login');
            $table->integer('points')->unsigned()->default(0)->comment('Saldo poin loyalty');
            $table->integer('total_orders')->unsigned()->default(0);
            $table->decimal('total_spent', 14, 2)->unsigned()->default(0);
            $table->string('tier', 30)->default('bronze')->comment('bronze, silver, gold, platinum');
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_order_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
