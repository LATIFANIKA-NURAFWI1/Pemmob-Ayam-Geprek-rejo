<?php

namespace App\Livewire\Kds;

use App\Models\Order;
use App\Models\StockIngredient;
use App\Services\OrderService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.kds')]
#[Title('KDS - Dapur Geprek Rejo')]
class Display extends Component
{
    public string $activeTab = 'antrian';
    public string $searchQuery = '';
    public ?int $completingOrderId = null;
    public int $lastAntrianCount = -1;

    // ─── Stok Inventory ──────────────────────────────────────────────────────

    public string $stokSearch = '';
    public string $stokFilter = ''; // '' | 'low' | 'ok'
    public bool $showRestockModal = false;
    public ?int $restockingId = null;
    public float $restockQty = 0;
    public string $restockNote = '';

    // ─── Computed Properties ─────────────────────────────────────────────────

    #[Computed]
    public function kitchenOrders()
    {
        return Order::with(['details'])
            ->whereIn('status', ['confirmed', 'preparing'])
            ->today()
            ->orderBy('queue_number', 'asc')
            ->get();
    }

    // ─── Actions ─────────────────────────────────────────────────────────────

    public function switchTab(string $tab): void
    {
        if (in_array($tab, ['antrian', 'proses', 'riwayat', 'menu', 'stok'])) {
            $activeTabBefore = $this->activeTab;
            $this->activeTab = $tab;
            if ($activeTabBefore !== $tab) {
                $this->reset('searchQuery');
            }
        }
    }

    public function startPreparing(int $orderId): void
    {
        app(OrderService::class)->startPreparing($orderId);
        unset($this->kitchenOrders);
    }

    public function completeOrder(int $orderId): void
    {
        app(OrderService::class)->completeOrder($orderId);
        unset($this->kitchenOrders);
    }

    // Aliases to support existing display.blade.php wire:clicks
    public function mulaiMasak(int $orderId): void
    {
        $this->startPreparing($orderId);
    }

    public function selesaiMasak(int $orderId): void
    {
        $this->completeOrder($orderId);
    }

    public function toggleAvailability(int $menuItemId): void
    {
        $menuItem = \App\Models\MenuItem::findOrFail($menuItemId);
        $menuItem->update([
            'is_available' => !$menuItem->is_available,
        ]);
    }

    public function getWaitingTime(Order $order): string
    {
        if (!$order->confirmed_at) {
            return '-';
        }
        $diffInSeconds = (int) $order->confirmed_at->diffInSeconds(now());
        if ($diffInSeconds < 3600) {
            $mins = str_pad((int) floor($diffInSeconds / 60), 2, '0', STR_PAD_LEFT);
            $secs = str_pad($diffInSeconds % 60, 2, '0', STR_PAD_LEFT);
            return "{$mins}:{$secs}";
        }
        $hours = (int) floor($diffInSeconds / 3600);
        $mins = str_pad((int) floor(($diffInSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
        return "{$hours}j {$mins}m";
    }

    public function getCookingTime(Order $order): string
    {
        if ($order->status !== 'preparing' || !$order->updated_at) {
            return '-';
        }
        $diffInSeconds = (int) $order->updated_at->diffInSeconds(now());
        if ($diffInSeconds < 3600) {
            $mins = str_pad((int) floor($diffInSeconds / 60), 2, '0', STR_PAD_LEFT);
            $secs = str_pad($diffInSeconds % 60, 2, '0', STR_PAD_LEFT);
            return "{$mins}:{$secs}";
        }
        $hours = (int) floor($diffInSeconds / 3600);
        $mins = str_pad((int) floor(($diffInSeconds % 3600) / 60), 2, '0', STR_PAD_LEFT);
        return "{$hours}j {$mins}m";
    }

    // ─── Stok Actions (Dapur) ─────────────────────────────────────────────────

    public function openRestock(int $id): void
    {
        $this->restockingId     = $id;
        $this->restockQty       = 0;
        $this->restockNote      = '';
        $this->showRestockModal = true;
    }

    public function applyRestock(): void
    {
        $this->validate([
            'restockQty' => 'required|numeric|min:0.01',
        ], [
            'restockQty.required' => 'Jumlah restock wajib diisi.',
            'restockQty.min'      => 'Jumlah restock harus lebih dari 0.',
        ]);

        $ingredient = StockIngredient::findOrFail($this->restockingId);
        $ingredient->increment('current_stock', $this->restockQty);

        session()->flash('stok_status', "Restock {$ingredient->name}: +{$this->restockQty} {$ingredient->unit} berhasil.");

        $this->showRestockModal = false;
        $this->reset(['restockingId', 'restockQty', 'restockNote']);
    }

    public function closeRestockModal(): void
    {
        $this->showRestockModal = false;
        $this->reset(['restockingId', 'restockQty', 'restockNote']);
        $this->resetValidation();
    }

    public function logout(\App\Livewire\Actions\Logout $logout): void
    {
        $logout();
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $antrianMasak  = $this->kitchenOrders->where('status', 'confirmed');
        $sedangDimasak = $this->kitchenOrders->where('status', 'preparing');

        // Notif antrian baru masuk (poll-based)
        $currentCount = $antrianMasak->count();
        if ($this->lastAntrianCount >= 0 && $currentCount > $this->lastAntrianCount) {
            $newest = $antrianMasak->first();
            $this->dispatch('new-order',
                queue_number: $newest ? $newest->queue_number : '?',
                order_number: $newest ? $newest->order_number : '',
            );
        }
        $this->lastAntrianCount = $currentCount;

        $riwayatPesanan = Order::with(['details'])
            ->where('status', 'completed')
            ->today()
            ->orderBy('completed_at', 'desc')
            ->get();

        $menuItems = \App\Models\MenuItem::with('category')
            ->when(filled($this->searchQuery), function ($query) {
                $query->where('name', 'like', '%' . $this->searchQuery . '%');
            })
            ->ordered()
            ->get()
            ->groupBy(fn($item) => $item->category ? $item->category->name : 'Lainnya');

        // Stok (sinkron dengan owner: paginate 20 per halaman, tapi KDS pakai all)
        $stokIngredients = StockIngredient::query()
            ->when($this->stokSearch, fn($q) => $q->where('name', 'like', "%{$this->stokSearch}%"))
            ->when($this->stokFilter === 'low', fn($q) => $q->whereColumn('current_stock', '<', 'minimum_stock'))
            ->when($this->stokFilter === 'ok',  fn($q) => $q->whereColumn('current_stock', '>=', 'minimum_stock'))
            ->orderBy('name')
            ->get();

        return view('livewire.kds.display', [
            'antrianMasak'    => $antrianMasak,
            'sedangDimasak'   => $sedangDimasak,
            'antrianCount'    => $antrianMasak->count(),
            'masakCount'      => $sedangDimasak->count(),
            'riwayatPesanan'  => $riwayatPesanan,
            'menuItemsGroup'  => $menuItems,
            'stokIngredients' => $stokIngredients,
        ]);
    }
}
