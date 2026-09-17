<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * REQ-FUNC-040
 * Membuat tabel staff_shifts sesuai Kamus Data SDD:
 * Menyimpan jadwal shift harian untuk setiap staf.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_shifts', function (Blueprint $table) {
            // Kolom identitas utama (PK, AUTO_INCREMENT)
            $table->id();

            // Relasi ke tabel users (FK — NOT NULL)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tanggal shift (DATE, NOT NULL)
            $table->date('shift_date');

            // Waktu mulai shift (TIME, NOT NULL)
            $table->time('start_time');

            // Waktu selesai shift (TIME, NOT NULL)
            $table->time('end_time');

            // Posisi/jabatan saat shift (ENUM, NOT NULL)
            $table->enum('position', ['kasir', 'inventory', 'dapur']);

            // Catatan tambahan opsional (TEXT, NULL)
            $table->text('notes')->nullable();

            // Timestamps: created_at & updated_at
            $table->timestamps();

            // Index untuk mempercepat query berdasarkan tanggal dan user
            $table->index(['shift_date', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_shifts');
    }
};
