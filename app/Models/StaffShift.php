<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model StaffShift — merepresentasikan jadwal shift harian staf.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $shift_date   Format: Y-m-d
 * @property string      $start_time   Format: H:i:s
 * @property string      $end_time     Format: H:i:s
 * @property string      $position     Nilai: kasir | inventory | dapur
 * @property string|null $notes
 * @property-read User   $user
 */
class StaffShift extends Model
{
    // =========================================================================
    // FILLABLE & CASTS
    // =========================================================================

    protected $fillable = [
        'user_id',
        'shift_date',
        'start_time',
        'end_time',
        'position',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'shift_date' => 'date',
        ];
    }

    // =========================================================================
    // RELASI
    // =========================================================================

    /**
     * Shift ini dimiliki oleh satu User (staf).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // =========================================================================
    // LABEL HELPER
    // =========================================================================

    /**
     * Label posisi yang ditampilkan di UI.
     */
    public function positionLabel(): string
    {
        return match ($this->position) {
            'kasir'     => '🖥️ Kasir',
            'inventory' => '📦 Inventory',
            'dapur'     => '🍳 Dapur',
            default     => ucfirst($this->position),
        };
    }

    /**
     * Menghitung durasi shift dalam jam (float).
     */
    public function durationHours(): float
    {
        $start = strtotime($this->start_time);
        $end   = strtotime($this->end_time);

        // Tangani shift yang melewati tengah malam
        if ($end < $start) {
            $end += 86400;
        }

        return round(($end - $start) / 3600, 1);
    }
}
