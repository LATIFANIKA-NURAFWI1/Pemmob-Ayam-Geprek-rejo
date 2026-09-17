@if($orders->isEmpty())
    <div class="flex flex-col items-center justify-center py-8 text-on-surface-variant">
        <span class="material-symbols-outlined text-4xl opacity-30 mb-2">receipt_long</span>
        <p class="text-sm">Belum ada pesanan hari ini</p>
    </div>
@else
    <div class="flex flex-col gap-2">
        @foreach($orders as $index => $order)
            @php
                $statusConfig = match($order->status) {
                    'completed'  => ['label' => 'Selesai',      'class' => 'bg-[#fff7e6] text-secondary-fixed-dim border border-secondary-fixed-dim'],
                    'confirmed'  => ['label' => 'Dikonfirmasi', 'class' => 'bg-surface-container text-on-surface border border-surface-variant'],
                    'preparing'  => ['label' => 'Dimasak',      'class' => 'bg-secondary-container/30 text-on-secondary-container border border-secondary-container'],
                    'cancelled'  => ['label' => 'Batal',        'class' => 'bg-error-container text-on-error-container border border-primary/30'],
                    default      => ['label' => 'Pending',      'class' => 'bg-surface-variant text-on-surface-variant border border-outline-variant'],
                };
                $staggerClass = 'list-stagger-' . min($index + 1, 5);
            @endphp
            <div class="flex items-center justify-between p-3 rounded-lg hover:bg-surface-container-high transition-colors cursor-pointer group animate-fade-in {{ $staggerClass }}">
                <div class="flex flex-col">
                    <span class="font-headline-md text-sm font-semibold text-on-surface group-hover:text-primary transition-colors">
                        #{{ $order->order_number }}
                    </span>
                    <span class="text-xs text-on-surface-variant mt-1">
                        {{ $order->created_at->format('H:i') }} · {{ $order->details->count() }} item
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    <span class="font-medium text-sm text-on-surface">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $statusConfig['class'] }}">
                        {{ $statusConfig['label'] }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
@endif
