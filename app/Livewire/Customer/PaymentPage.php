<?php

namespace App\Livewire\Customer;

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.customer')]
#[Title('Menunggu Konfirmasi')]
class PaymentPage extends Component
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

        // If already past pending, redirect to the appropriate page
        if (in_array($order->status, ['confirmed', 'preparing', 'completed'])) {
            $this->redirect(route('order.success', $order->id), navigate: true);
            return;
        }

        if ($order->status === 'cancelled') {
            session()->flash('error', 'Maaf, pesanan Anda telah dibatalkan.');
            $this->redirect(route('order.menu'), navigate: true);
        }
    }

    // =========================================================================
    // POLLING
    // =========================================================================

    /**
     * Called every 5 seconds via wire:poll.5s="checkStatus".
     * Refreshes the order and redirects when the cashier confirms or cancels.
     */
    public function checkStatus(): void
    {
        $this->order->refresh();

        if ($this->order->status === 'pending') {
            return; // Still waiting — do nothing
        }

        if (in_array($this->order->status, ['confirmed', 'preparing', 'completed'])) {
            $this->redirect(route('order.success', $this->order->id), navigate: true);
            return;
        }

        // cancelled
        session()->flash('error', 'Maaf, pesanan Anda telah dibatalkan oleh kasir.');
        $this->redirect(route('order.menu'), navigate: true);
    }

    // =========================================================================
    // COMPUTED
    // =========================================================================

    #[Computed]
    public function qrisImageUrl(): ?string
    {
        // Guard: Setting model may not exist in all environments
        if (! class_exists(\App\Models\Setting::class)) {
            return null;
        }

        $path = \App\Models\Setting::get('qris_image_path');

        return $path ? Storage::url($path) . '?v=' . time() : null;
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    public function render()
    {
        return view('livewire.customer.payment-page');
    }
}
