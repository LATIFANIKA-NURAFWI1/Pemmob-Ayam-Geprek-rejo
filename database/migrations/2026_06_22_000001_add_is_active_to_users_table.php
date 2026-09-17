<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * REQ-FUNC-039 / N9.1
 * Menambahkan kolom is_active ke tabel users untuk memungkinkan
 * Owner menonaktifkan akses login staf tanpa menghapus akunnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Default TRUE agar user yang sudah ada tidak terpengaruh
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
