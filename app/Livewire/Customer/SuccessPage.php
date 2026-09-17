<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.customer')]
#[Title('Pesanan Berhasil!')]
class SuccessPage extends Component
{
    public Order $order;

    public string $customerName = '';

    // =========================================================================
    // LIFECYCLE
    // =========================================================================

    public function mount(Order $order): void
    {
        $this->order        = $order;
        $this->customerName = session('order_customer_name', $order->member?->name ?? 'Pelanggan');

        // Guard: success page is only for non-pending, non-cancelled orders
        if (in_array($order->status, ['pending', 'cancelled'])) {
            $this->redirect(route('order.menu'), navigate: true);
        }
    }

    // =========================================================================
    // COMPUTED
    // =========================================================================

    /** @return Collection */
    #[Computed]
    public function orderDetails(): Collection
    {
        return $this->order->details()->with('menuItem')->get();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.customer.success-page');
    }
}
