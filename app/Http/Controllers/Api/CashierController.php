<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\InsufficientStockException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use RuntimeException;

class CashierController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $pending = Order::with(['details', 'member'])
            ->pending()
            ->today()
            ->orderBy('queue_number', 'asc')
            ->get();

        $confirmed = Order::with(['details', 'member'])
            ->whereIn('status', ['confirmed', 'preparing'])
            ->today()
            ->orderBy('queue_number', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'pending' => $pending,
                'proses' => $confirmed
            ]
        ]);
    }

    public function history(Request $request)
    {
        $history = Order::with(['details', 'member'])
            ->whereIn('status', ['completed', 'cancelled'])
            ->today()
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function show(Order $order)
    {
        return response()->json([
            'success' => true,
            'data' => $order->load(['details.menuItem', 'member'])
        ]);
    }

    public function confirmPayment(Request $request, Order $order)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:cash,qris'
        ]);

        try {
            $this->orderService->confirmPayment(
                $order->id,
                $validated['payment_method'],
                auth()->id()
            );

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran dikonfirmasi! Pesanan diteruskan ke dapur.'
            ]);
        } catch (InsufficientStockException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Stok bahan baku tidak mencukupi!'
            ], 400);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cancelOrder(Request $request, Order $order)
    {
        $validated = $request->validate([
            'reason' => 'required|string|min:3'
        ]);

        try {
            $this->orderService->cancelOrder($order->id, $validated['reason']);

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan.'
            ]);
        } catch (RuntimeException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
