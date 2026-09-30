<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * ExpenseController — Endpoint API pencatatan & pengelolaan pengeluaran operasional
 * untuk aplikasi mobile (Owner).
 */
class ExpenseController extends Controller
{
    public const CATEGORIES = [
        'bahan_baku'  => 'Bahan Baku',
        'operasional' => 'Operasional',
        'gaji'        => 'Gaji Karyawan',
        'perawatan'   => 'Perawatan & Servis',
        'lainnya'     => 'Lainnya',
    ];

    /**
     * GET /api/v1/admin/expenses
     *
     * Parameter:
     * - search: pencarian nama keterangan
     * - category: filter kategori ('bahan_baku', 'operasional', dst.)
     * - month: format Y-m (default bulan ini)
     * - page: halaman pagination
     */
    public function index(Request $request): JsonResponse
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));
        $search = $request->query('search');
        $category = $request->query('category');

        $query = Expense::with('recorder:id,name')
            ->where('expense_date', 'like', "{$month}%")
            ->when($search, fn ($q) => $q->where('description', 'like', "%{$search}%"))
            ->when($category, fn ($q) => $q->where('category', $category))
            ->orderByDesc('expense_date')
            ->orderByDesc('id');

        // Total pengeluaran bulan ini (sebelum paginate, tapi sesuai filter bulan)
        $monthTotal = (float) Expense::where('expense_date', 'like', "{$month}%")->sum('amount');

        $expenses = $query->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $expenses->items(),
            'meta'    => [
                'current_page' => $expenses->currentPage(),
                'last_page'    => $expenses->lastPage(),
                'per_page'     => $expenses->perPage(),
                'total'        => $expenses->total(),
                'month'        => $month,
                'month_total'  => $monthTotal,
                'categories'   => self::CATEGORIES,
            ],
        ]);
    }

    /**
     * POST /api/v1/admin/expenses
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'description'  => 'required|string|max:255',
            'category'     => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $validated['recorded_by'] = auth()->id();

        $expense = Expense::create($validated);
        $expense->load('recorder:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dicatat.',
            'data'    => $expense,
        ], 201);
    }

    /**
     * PUT /api/v1/admin/expenses/{id}
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $expense = Expense::findOrFail($id);

        $validated = $request->validate([
            'description'  => 'required|string|max:255',
            'category'     => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'amount'       => 'required|numeric|min:1',
            'expense_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $expense->update($validated);
        $expense->load('recorder:id,name');

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil diperbarui.',
            'data'    => $expense,
        ]);
    }

    /**
     * DELETE /api/v1/admin/expenses/{id}
     */
    public function destroy(int $id): JsonResponse
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dihapus.',
        ]);
    }

    /**
     * GET /api/v1/admin/expenses/summary
     *
     * Rekap per kategori untuk bulan tertentu
     */
    public function summary(Request $request): JsonResponse
    {
        $month = $request->query('month', Carbon::now()->format('Y-m'));

        $breakdown = Expense::where('expense_date', 'like', "{$month}%")
            ->select('category', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->get()
            ->map(fn ($row) => [
                'category'       => $row->category,
                'category_label' => self::CATEGORIES[$row->category] ?? ucfirst($row->category),
                'total'          => (float) $row->total,
                'count'          => (int) $row->count,
            ]);

        $grandTotal = (float) Expense::where('expense_date', 'like', "{$month}%")->sum('amount');

        return response()->json([
            'success' => true,
            'data'    => [
                'month'       => $month,
                'grand_total' => $grandTotal,
                'breakdown'   => $breakdown,
            ],
        ]);
    }
}
