<div class="min-h-screen bg-gray-50 flex items-start justify-center px-4 py-6 sm:py-10">

    {{-- =====================================================================
         SINGLE RECEIPT CARD
         ===================================================================== --}}
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden">

        {{-- ── Success Header ─────────────────────────────────────── --}}
        <div class="bg-gradient-to-br from-emerald-50 to-green-50 px-6 pt-8 pb-6 flex flex-col items-center text-center border-b border-emerald-100">
            {{-- Green checkmark circle --}}
            <div class="relative mb-4">
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center animate-[bounce_0.6s_ease-out_1]">
                    <svg class="w-10 h-10 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                {{-- Pulse ring --}}
                <span class="absolute inset-0 rounded-full bg-emerald-300 opacity-20 animate-ping"></span>
            </div>

            <h1 class="font-jakarta text-xl font-black text-gray-900">Pesanan Berhasil!</h1>
            <p class="font-inter text-gray-500 mt-1 text-sm">Pesanan Anda sedang diproses oleh dapur</p>
        </div>

        {{-- ── Queue Number ──────────────────────────────────────── --}}
        <div class="bg-gradient-to-br from-[#e61919] to-[#bc000a] px-6 py-6 text-center">
            <p class="font-inter text-[#e8bcb6] text-[10px] font-semibold uppercase tracking-[0.2em] mb-1">Nomor Antrean Anda</p>
            <p class="font-jakarta text-7xl sm:text-8xl font-black text-white leading-none drop-shadow-md">
                {{ $order->queue_number }}
            </p>
        </div>

        {{-- ── Card Body ─────────────────────────────────────────── --}}
        <div class="px-5 py-5 space-y-4">

            {{-- Order type & Payment method row --}}
            <div class="flex gap-3">
                <div class="flex-1 flex items-center gap-2.5 bg-gray-50 rounded-xl px-3 py-2.5 border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-gray-200 flex items-center justify-center flex-shrink-0">
                        <span class="text-base">{{ $order->type === 'dine_in' ? '🍽️' : '🥡' }}</span>
                    </div>
                    <div>
                        <p class="font-inter text-[9px] text-gray-400 uppercase tracking-wider font-semibold">Jenis</p>
                        <p class="font-jakarta text-xs font-bold text-gray-900">
                            {{ $order->type === 'dine_in' ? 'Dine In' : 'Bawa Pulang' }}
                        </p>
                    </div>
                </div>
                <div class="flex-1 flex items-center gap-2.5 {{ $order->payment_method === 'qris' ? 'bg-blue-50 border-blue-100' : 'bg-green-50 border-green-100' }} rounded-xl px-3 py-2.5 border">
                    <div class="w-8 h-8 rounded-lg {{ $order->payment_method === 'qris' ? 'bg-blue-100' : 'bg-green-100' }} flex items-center justify-center flex-shrink-0">
                        <span class="text-base">{{ $order->payment_method === 'qris' ? '📱' : '💵' }}</span>
                    </div>
                    <div>
                        <p class="font-inter text-[9px] text-gray-400 uppercase tracking-wider font-semibold">Bayar</p>
                        <p class="font-jakarta text-xs font-bold {{ $order->payment_method === 'qris' ? 'text-blue-700' : 'text-green-700' }} uppercase">
                            {{ $order->payment_method === 'qris' ? 'QRIS' : 'Tunai' }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Customer identity --}}
            <div class="flex items-center gap-3 bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-[#e61919] to-[#bc000a] flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($customerName, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate text-sm">{{ $customerName }}</p>
                    @if($order->table_number)
                        <p class="text-xs text-gray-500">🪑 Meja {{ $order->table_number }}</p>
                    @endif
                </div>
                @if($order->member)
                    <span class="text-[10px] bg-[#fdc003] text-[#6c5000] px-2.5 py-1 rounded-full font-bold flex-shrink-0 flex items-center gap-1 shadow-sm">
                        ⭐ Member
                    </span>
                @endif
            </div>

            {{-- Dashed separator --}}
            <div class="border-t-2 border-dashed border-gray-200 relative">
                <div class="absolute -left-8 -top-3.5 w-7 h-7 bg-gray-50 rounded-full"></div>
                <div class="absolute -right-8 -top-3.5 w-7 h-7 bg-gray-50 rounded-full"></div>
            </div>

            {{-- Order Items --}}
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-[#bc000a]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    <h2 class="font-jakarta font-semibold text-gray-900 text-sm">Detail Pesanan</h2>
                </div>
                <div class="space-y-2">
                    @foreach($this->orderDetails as $detail)
                        <div class="flex items-center justify-between py-1.5">
                            <div class="flex items-center gap-2.5 min-w-0 flex-1">
                                <span class="bg-[#fff8f7] text-[#bc000a] font-bold text-[10px] w-6 h-6 rounded-md flex items-center justify-center flex-shrink-0 border border-[#ffdad5]">
                                    {{ $detail->quantity }}x
                                </span>
                                <span class="text-sm text-gray-800 truncate">{{ $detail->menu_item_name }}</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-700 flex-shrink-0 ml-3">
                                Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Dashed separator --}}
            <div class="border-t border-dashed border-gray-200"></div>

            {{-- Payment Summary --}}
            <div class="space-y-2">
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-medium text-gray-900">
                        Rp {{ number_format($order->subtotal, 0, ',', '.') }}
                    </span>
                </div>

                @if((float)$order->discount_amount > 0)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-emerald-600">Diskon Voucher</span>
                        <span class="font-semibold text-emerald-600">
                            −Rp {{ number_format($order->discount_amount, 0, ',', '.') }}
                        </span>
                    </div>
                @endif

                @if((float)$order->points_redeemed_amount > 0)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-amber-700">Diskon Poin ({{ $order->points_redeemed }} poin)</span>
                        <span class="font-semibold text-amber-700">
                            −Rp {{ number_format($order->points_redeemed_amount, 0, ',', '.') }}
                        </span>
                    </div>
                @endif

                <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                    <span class="font-jakarta font-bold text-gray-900">Total Dibayar</span>
                    <span class="font-jakarta text-xl font-black text-[#bc000a]">
                        Rp {{ number_format($order->total_amount, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            {{-- Points earned notification (if member) --}}
            @if($order->member && $order->points_earned > 0)
                <div class="bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 flex items-center gap-3">
                    <span class="text-xl flex-shrink-0">⭐</span>
                    <div>
                        <p class="text-sm font-semibold text-amber-800">
                            +{{ number_format($order->points_earned, 0, ',', '.') }} poin diperoleh!
                        </p>
                        <p class="text-xs text-amber-600 mt-0.5">
                            Poin akan ditambahkan setelah pembayaran dikonfirmasi
                        </p>
                    </div>
                </div>
            @endif

            {{-- Notes --}}
            @if($order->notes)
                <div class="bg-gray-50 rounded-xl px-4 py-3 border border-gray-100">
                    <p class="text-xs font-medium text-gray-500 mb-1">📝 Catatan Pesanan</p>
                    <p class="text-sm text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif

            {{-- Selesai button --}}
            <div class="pt-2 pb-1">
                <a href="{{ route('order.menu') }}" wire:navigate
                   class="w-full flex items-center justify-center gap-2 py-4 bg-[#fdc003] text-[#6c5000] font-bold text-base rounded-2xl shadow-md hover:bg-[#fabd00] active:scale-[0.98] transition-all">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                    Selesai
                </a>
            </div>

        </div>
    </div>

</div>
