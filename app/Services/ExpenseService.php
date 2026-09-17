<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ExpenseService
{
    /**
     * Catat pengeluaran baru.
     *
     * @param  array{
     *     description: string,
     *     category: string,
     *     amount: float,
     *     expense_date: string,
     *     receipt_image: string|null,
     *     notes: string|null,
     *     recorded_by: int|null,
     * }  $data
     */
    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    /**
     * Update pengeluaran.
     */
    public function update(Expense $expense, array $data): Expense
    {
        $expense->update($data);

        return $expense->fresh();
    }

    /**
     * Hapus pengeluaran.
     */
    public function delete(Expense $expense): void
    {
        $expense->delete();
    }

    /**
     * Ambil pengeluaran dengan filter dan pagination.
     *
     * @param  array{
     *     from: string|null,
     *     to: string|null,
     *     category: string|null,
     *     search: string|null,
     *     per_page: int,
     * }  $filters
     */
    public function paginate(array $filters = []): LengthAwarePaginator
    {
        $query = Expense::query()->latest('expense_date');

        if (! empty($filters['from']) && ! empty($filters['to'])) {
            $query->whereBetween('expense_date', [$filters['from'], $filters['to']]);
        }

        if (! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (! empty($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Ringkasan pengeluaran per kategori untuk periode tertentu.
     *
     * @return Collection<string, float>  ['kategori' => total_amount]
     */
    public function summaryByCategory(string $from, string $to): Collection
    {
        return Expense::whereBetween('expense_date', [$from, $to])
            ->select('category', DB::raw('SUM(amount) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->map(fn ($v) => (float) $v);
    }

    /**
     * Total pengeluaran pada periode tertentu.
     */
    public function totalForPeriod(string $from, string $to): float
    {
        return (float) Expense::whereBetween('expense_date', [$from, $to])
            ->sum('amount');
    }
}
