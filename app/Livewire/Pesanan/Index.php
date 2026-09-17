<?php

namespace App\Livewire\Pesanan;

use App\Models\Order;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pesanan')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $tanggal = '';

    /** Bulan aktif untuk view history bulanan (format: Y-m) */
    #[Url]
    public string $bulan = '';

    /** Mode tampilan: 'harian' (list per tanggal) | 'bulanan' (summary per hari dalam 1 bulan) */
    #[Url]
    public string $viewMode = 'harian';

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedStatus(): void   { $this->resetPage(); }
    public function updatedTanggal(): void  { $this->resetPage(); }
    public function updatedBulan(): void    { $this->resetPage(); }
    public function updatedViewMode(): void { $this->resetPage(); }

    public function mount(): void
    {
        $this->tanggal = $this->tanggal ?: today()->toDateString();
        $this->bulan   = $this->bulan   ?: today()->format('Y-m');
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    public function konfirmasi(int $id): void
    {
        $order = Order::findOrFail($id);
        $order->update([
            'status'       => 'confirmed',
            'confirmed_at' => now(),
            'confirmed_by' => auth()->id(),
        ]);
        $this->dispatch('notify', message: "Pesanan #{$order->order_number} dikonfirmasi.");
    }

    public function selesai(int $id): void
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'completed', 'completed_at' => now()]);
        $this->dispatch('notify', message: "Pesanan #{$order->order_number} selesai.");
    }

    public function batal(int $id): void
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => 'cancelled']);
        $this->dispatch('notify', message: "Pesanan #{$order->order_number} dibatalkan.");
    }

    // =========================================================================
    // COMPUTED: Ringkasan harian dalam 1 bulan
    // =========================================================================

    /**
     * Summary per hari untuk bulan yang dipilih.
     * Digunakan di mode 'bulanan'.
     *
     * @return array<int, array{tanggal: string, pesanan: int, revenue: float}>
     */
    #[Computed]
    public function dailySummary(): array
    {
        if (! $this->bulan) return [];

        [$year, $month] = explode('-', $this->bulan);

        return Order::query()
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
    }

    /**
     * Total revenue bulan yang dipilih.
     */
    #[Computed]
    public function totalRevenueBulan(): float
    {
        return (float) collect($this->dailySummary)->sum('revenue');
    }

    /**
     * Total pesanan bulan yang dipilih.
     */
    #[Computed]
    public function totalPesananBulan(): int
    {
        return (int) collect($this->dailySummary)->sum('total_pesanan');
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        $orders = Order::with(['details.menuItem', 'member'])
            ->when($this->search, fn ($q) => $q->where('order_number', 'like', "%{$this->search}%"))
            ->when($this->status, fn ($q) => $q->where('status', $this->status))
            ->when($this->viewMode === 'harian' && $this->tanggal,
                fn ($q) => $q->whereDate('created_at', $this->tanggal))
            ->when($this->viewMode === 'bulanan' && $this->bulan, function ($q) {
                [$year, $month] = explode('-', $this->bulan);
                return $q->whereYear('created_at', $year)->whereMonth('created_at', $month);
            })
            ->latest()
            ->paginate(15);

        return view('livewire.pesanan.index', compact('orders'));
    }
}
