<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProfitLossService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * ReportController — Endpoint API laporan keuangan (Laba / Rugi)
 * untuk aplikasi mobile (Owner).
 */
class ReportController extends Controller
{
    public function __construct(
        protected ProfitLossService $profitLossService
    ) {}

    /**
     * GET /api/v1/admin/reports/profit-loss
     *
     * Parameter:
     * - preset: 'hari_ini' | 'minggu_ini' | 'bulan_ini' | 'tahun_ini' | 'custom' (default: 'bulan_ini')
     * - from: Y-m-d (wajib jika preset = custom)
     * - to:   Y-m-d (wajib jika preset = custom)
     */
    public function profitLoss(Request $request): JsonResponse
    {
        $preset = $request->query('preset', 'bulan_ini');

        switch ($preset) {
            case 'hari_ini':
                $from = Carbon::today()->toDateString();
                $to   = Carbon::today()->toDateString();
                break;

            case 'minggu_ini':
                $from = Carbon::now()->startOfWeek()->toDateString();
                $to   = Carbon::now()->endOfWeek()->toDateString();
                break;

            case 'tahun_ini':
                $from = Carbon::now()->startOfYear()->toDateString();
                $to   = Carbon::now()->endOfYear()->toDateString();
                break;

            case 'custom':
                $from = $request->query('from', Carbon::now()->startOfMonth()->toDateString());
                $to   = $request->query('to', Carbon::now()->toDateString());
                break;

            case 'bulan_ini':
            default:
                $preset = 'bulan_ini';
                $from = Carbon::now()->startOfMonth()->toDateString();
                $to   = Carbon::now()->endOfMonth()->toDateString();
                break;
        }

        $report = $this->profitLossService->calculate($from, $to);
        $dailyTrend = $this->profitLossService->dailyTrend($from, $to);
        $topMenus = $this->profitLossService->topMenuItems($from, $to, 5);

        return response()->json([
            'success' => true,
            'data'    => [
                'preset'      => $preset,
                'from'        => $from,
                'to'          => $to,
                'report'      => $report,
                'daily_trend' => $dailyTrend,
                'top_menus'   => $topMenus,
            ],
        ]);
    }
}
