<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ProfitLossService
{
    /**
     * Hitung Laporan Laba-Rugi (Income Statement) untuk periode tertentu.
     *
     * Formula:
     *   Revenue         = Σ total_amount pada order [confirmed|preparing|completed]
     *   COGS (HPP)      = Σ total_hpp pada order yang sama
     *   Gross Profit    = Revenue - COGS
     *   Total Expenses  = Σ expenses.amount pada periode
     *   Net Profit      = Gross Profit - Total Expenses
     *   Gross Margin %  = (Gross Profit / Revenue) × 100
     *   Net Margin %    = (Net Profit / Revenue) × 100
     *
     * @param  string  $from  Format: 'Y-m-d'
     * @param  string  $to    Format: 'Y-m-d'
     * @return array{
     *     period_from: string,
     *     period_to: string,
     *     revenue: float,
     *     total_hpp: float,
     *     gross_profit: float,
     *     gross_margin_pct: float,
     *     total_expenses: float,
     *     expenses_by_category: array,
     *     net_profit: float,
     *     net_margin_pct: float,
     *     order_count: int,
     *     avg_order_value: float,
     *     summary: array,
     * }
     */
    public function calculate(string $from, string $to): array
    {
        $dateFrom = Carbon::parse($from)->startOfDay();
        $dateTo   = Carbon::parse($to)->endOfDay();

        // ── 1. Revenue & COGS dari order yang sudah dibayar ─────────────────
        $orderStats = Order::whereBetween('confirmed_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['confirmed', 'preparing', 'completed'])
            ->selectRaw(
                'COUNT(*) as order_count,
                 COALESCE(SUM(total_amount), 0) as revenue,
                 COALESCE(SUM(total_hpp), 0) as total_hpp'
            )
            ->first();

        $revenue    = (float) $orderStats->revenue;
        $totalHpp   = (float) $orderStats->total_hpp;
        $orderCount = (int) $orderStats->order_count;

        $grossProfit    = $revenue - $totalHpp;
        $grossMarginPct = $revenue > 0
            ? round(($grossProfit / $revenue) * 100, 2)
            : 0.0;

        // ── 2. Total Expenses per kategori ──────────────────────────────────
        $expensesByCategory = Expense::whereBetween('expense_date', [
            Carbon::parse($from)->startOfDay()->toDateString(),
            Carbon::parse($to)->endOfDay()->toDateString(),
        ])
            ->select('category', DB::raw('COALESCE(SUM(amount), 0) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->mapWithKeys(function ($v, $k) {
                $key = strtolower(str_replace(' ', '_', $k));
                return [$key => (float) $v];
            })
            ->toArray();

        $totalExpenses = (float) Expense::whereBetween('expense_date', [
            Carbon::parse($from)->startOfDay()->toDateString(),
            Carbon::parse($to)->endOfDay()->toDateString(),
        ])->sum('amount');

        // ── 3. Net Profit ───────────────────────────────────────────────────
        $netProfit    = $grossProfit - $totalExpenses;
        $netMarginPct = $revenue > 0
            ? round(($netProfit / $revenue) * 100, 2)
            : 0.0;

        $avgOrderValue = $orderCount > 0
            ? round($revenue / $orderCount, 2)
            : 0.0;

        return [
            'period_from'          => $from,
            'period_to'            => $to,
            'revenue'              => round($revenue, 2),
            'total_hpp'            => round($totalHpp, 2),
            'gross_profit'         => round($grossProfit, 2),
            'gross_margin_pct'     => $grossMarginPct,
            'total_expenses'       => round($totalExpenses, 2),
            'expenses_by_category' => $expensesByCategory,
            'net_profit'           => round($netProfit, 2),
            'net_margin_pct'       => $netMarginPct,
            'order_count'          => $orderCount,
            'avg_order_value'      => $avgOrderValue,
            'summary'              => $this->buildSummary(
                $revenue, $totalHpp, $grossProfit, $totalExpenses, $netProfit
            ),
        ];
    }

    /**
     * Hitung tren laba harian dalam satu periode (untuk chart).
     *
     * @param  string  $from
     * @param  string  $to
     * @return array<int, array{date: string, revenue: float, hpp: float, gross_profit: float}>
     */
    public function dailyTrend(string $from, string $to): array
    {
        $dateFrom = Carbon::parse($from)->startOfDay();
        $dateTo   = Carbon::parse($to)->endOfDay();

        $dailyOrders = Order::whereBetween('confirmed_at', [$dateFrom, $dateTo])
            ->whereIn('status', ['confirmed', 'preparing', 'completed'])
            ->selectRaw(
                'DATE(confirmed_at) as date,
                 COALESCE(SUM(total_amount), 0) as revenue,
                 COALESCE(SUM(total_hpp), 0) as hpp'
            )
            ->groupByRaw('DATE(confirmed_at)')
            ->orderBy('date')
            ->get();

        $dailyExpenses = Expense::whereBetween('expense_date', [
            Carbon::parse($from)->toDateString(),
            Carbon::parse($to)->toDateString(),
        ])
            ->selectRaw('expense_date as date, COALESCE(SUM(amount), 0) as total_expense')
            ->groupBy('expense_date')
            ->pluck('total_expense', 'date')
            ->map(fn ($v) => (float) $v)
            ->toArray();

        return $dailyOrders->map(function ($row) use ($dailyExpenses) {
            $revenue      = (float) $row->revenue;
            $hpp          = (float) $row->hpp;
            $grossProfit  = $revenue - $hpp;
            $expenses     = $dailyExpenses[$row->date] ?? 0.0;
            $netProfit    = $grossProfit - $expenses;

            return [
                'date'         => $row->date,
                'revenue'      => round($revenue, 2),
                'hpp'          => round($hpp, 2),
                'gross_profit' => round($grossProfit, 2),
                'expenses'     => round($expenses, 2),
                'net_profit'   => round($netProfit, 2),
            ];
        })->toArray();
    }

    /**
     * Perbandingan revenue & profit bulan ini vs bulan lalu (untuk dashboard widget).
     */
    public function monthComparison(): array
    {
        $thisMonth = [
            'from' => Carbon::now()->startOfMonth()->toDateString(),
            'to'   => Carbon::now()->toDateString(),
        ];

        $lastMonth = [
            'from' => Carbon::now()->subMonth()->startOfMonth()->toDateString(),
            'to'   => Carbon::now()->subMonth()->endOfMonth()->toDateString(),
        ];

        $current  = $this->calculate($thisMonth['from'], $thisMonth['to']);
        $previous = $this->calculate($lastMonth['from'], $lastMonth['to']);

        $revenueGrowth = $previous['revenue'] > 0
            ? round((($current['revenue'] - $previous['revenue']) / $previous['revenue']) * 100, 2)
            : ($current['revenue'] > 0 ? 100.0 : 0.0);

        $profitGrowth = $previous['net_profit'] > 0
            ? round((($current['net_profit'] - $previous['net_profit']) / $previous['net_profit']) * 100, 2)
            : 0.0;

        return [
            'current_month'  => $current,
            'previous_month' => $previous,
            'revenue_growth' => $revenueGrowth,
            'profit_growth'  => $profitGrowth,
        ];
    }

    /**
     * Top menu items berdasarkan revenue dalam periode.
     *
     * @param  string  $from
     * @param  string  $to
     * @param  int     $limit
     * @return array
     */
    public function topMenuItems(string $from, string $to, int $limit = 10): array
    {
        return DB::table('order_details')
            ->join('orders', 'order_details.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['confirmed', 'preparing', 'completed'])
            ->whereBetween('orders.confirmed_at', [
                Carbon::parse($from)->startOfDay(),
                Carbon::parse($to)->endOfDay(),
            ])
            ->selectRaw(
                'order_details.menu_item_name,
                 SUM(order_details.quantity) as total_qty,
                 SUM(order_details.subtotal) as total_revenue,
                 SUM(order_details.hpp_snapshot * order_details.quantity) as total_hpp,
                 SUM(order_details.subtotal - (order_details.hpp_snapshot * order_details.quantity)) as gross_profit'
            )
            ->groupBy('order_details.menu_item_name')
            ->orderByDesc('total_revenue')
            ->limit($limit)
            ->get()
            ->map(fn ($row) => [
                'menu_item_name' => $row->menu_item_name,
                'total_qty'      => (int) $row->total_qty,
                'total_revenue'  => round((float) $row->total_revenue, 2),
                'total_hpp'      => round((float) $row->total_hpp, 2),
                'gross_profit'   => round((float) $row->gross_profit, 2),
                'margin_pct'     => $row->total_revenue > 0
                    ? round(($row->gross_profit / $row->total_revenue) * 100, 2)
                    : 0.0,
            ])
            ->toArray();
    }

    // ─── Private ─────────────────────────────────────────────────────────────

    private function buildSummary(
        float $revenue,
        float $totalHpp,
        float $grossProfit,
        float $totalExpenses,
        float $netProfit
    ): array {
        return [
            ['label' => 'Total Pendapatan (Revenue)', 'amount' => $revenue,       'type' => 'income'],
            ['label' => 'HPP (Harga Pokok Penjualan)', 'amount' => -$totalHpp,    'type' => 'expense'],
            ['label' => 'Laba Kotor (Gross Profit)',   'amount' => $grossProfit,  'type' => 'subtotal'],
            ['label' => 'Total Pengeluaran',           'amount' => -$totalExpenses,'type' => 'expense'],
            ['label' => 'Laba Bersih (Net Profit)',    'amount' => $netProfit,    'type' => 'total'],
        ];
    }
}
