<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    protected $fillable = [
        'description',
        'category',
        'amount',
        'expense_date',
        'receipt_image',
        'notes',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    // ─── Relations ───────────────────────────────────────────────────────────

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'recorded_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeForPeriod($query, string $from, string $to)
    {
        return $query->whereBetween('expense_date', [$from, $to]);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }
}
