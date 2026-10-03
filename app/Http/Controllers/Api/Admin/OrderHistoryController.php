<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class OrderHistoryController extends Controller
{
    /**
     * GET /api/v1/admin/order-history
     * Get order history based on mode (harian/bulanan).
     */
    public function index(Request $request)
    {
        $mode = $request->query('mode', 'harian');
        $search = $request->query('search', '');
        
        if ($mode === 'harian') {
            $tanggal = $request->query('tanggal', today()->toDateString());
            
            $ordersQuery = Order::with(['details.menuItem', 'member'])
                ->when($search, fn ($q) => $q->where('order_number', 'like', "%{$search}%"))
                ->whereDate('created_at', $tanggal)
                ->latest();
                
            $orders = $ordersQuery->paginate(15);
                
            $totalRevenue = Order::whereDate('created_at', $tanggal)
                ->whereIn('status', ['confirmed', 'preparing', 'completed'])
                ->sum('total_amount');
                
            $totalOrders = Order::whereDate('created_at', $tanggal)->count();
                
            return response()->json([
                'success' => true,
                'data' => [
                    'mode' => 'harian',
                    'tanggal' => $tanggal,
                    'total_revenue' => (float)$totalRevenue,
                    'total_orders' => $totalOrders,
                    'orders' => $orders
                ]
            ]);
        } 
        else if ($mode === 'bulanan') {
            $bulan = $request->query('bulan', today()->format('Y-m'));
            [$year, $month] = explode('-', $bulan);
            
            $dailySummary = Order::query()
                ->selectRaw('DATE(created_at) as tanggal')
                ->selectRaw('COUNT(*) as total_pesanan')
                ->selectRaw('SUM(CASE WHEN status IN ("confirmed","preparing","completed") THEN total_amount ELSE 0 END) as revenue')
                ->selectRaw('SUM(CASE WHEN status IN ("confirmed","preparing","completed") THEN 1 ELSE 0 END) as terbayar')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->groupByRaw('DATE(created_at)')
                ->orderByRaw('DATE(created_at)')
                ->get()
                ->toArray();
                
            $totalRevenue = collect($dailySummary)->sum('revenue');
            $totalOrders = collect($dailySummary)->sum('total_pesanan');
            
            return response()->json([
                'success' => true,
                'data' => [
                    'mode' => 'bulanan',
                    'bulan' => $bulan,
                    'total_revenue' => (float)$totalRevenue,
                    'total_orders' => $totalOrders,
                    'daily_summary' => $dailySummary
                ]
            ]);
        }
        
        return response()->json(['success' => false, 'message' => 'Invalid mode'], 400);
    }
}
