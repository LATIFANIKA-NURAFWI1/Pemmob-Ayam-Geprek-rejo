<div wire:poll.5s.keep-alive class="flex flex-col h-[100dvh] w-full bg-[#f4f6f9] dark:bg-gray-950 text-gray-800 dark:text-gray-100 font-sans antialiased overflow-hidden">
    
    {{-- ── TopBar ────────────────────────────────────────────────────────── --}}
    <header class="bg-[#bc000a] border-b border-[#a00008] flex justify-between items-center w-full px-6 py-4 shadow-[0_4px_20px_rgba(188,0,10,0.25)] z-10 shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Ayam Geprek Rejo" class="h-10 w-auto object-contain drop-shadow-md">
            <div>
                <h1 class="text-md md:text-lg font-extrabold text-white tracking-wider leading-tight">KASIR — GEPREK REJO</h1>
                <p class="text-[11px] text-red-100/80 font-semibold uppercase tracking-widest">Dashboard Kasir</p>
            </div>
        </div>

        {{-- Clock, Theme Toggle, & Logout --}}
        <div class="flex items-center gap-4 md:gap-6">
            <div class="text-right hidden sm:block" wire:ignore>
                <div class="text-xl md:text-2xl font-black text-white tracking-wider leading-none font-mono" id="kds-time">--:--:--</div>
                <div class="text-[10px] text-red-100/70 font-semibold mt-1 uppercase tracking-wide" id="kds-date">-</div>
            </div>

            {{-- Day / Dark Mode Toggle --}}
            <button
                id="kds-theme-btn"
                data-kds-theme-btn
                onclick="kdsToggleTheme()"
                class="flex items-center gap-1.5 px-3 py-2 rounded-xl border cursor-pointer text-xs font-bold border-white/30 bg-white/10 text-white hover:bg-white/20"
                title="Ubah Tema"
            >
                <span class="material-symbols-outlined text-[18px]" data-kds-theme-icon>dark_mode</span>
                <span class="hidden sm:inline uppercase tracking-wider" data-kds-theme-label>Night</span>
            </button>

            {{-- Logout Button --}}
            <button wire:click="logout" class="border-2 border-white/40 hover:bg-white/20 text-white px-4 py-2 rounded-xl transition-all font-bold text-xs flex items-center gap-2 cursor-pointer" title="Keluar">
                <span class="material-symbols-outlined text-sm">logout</span>
                <span class="hidden sm:inline">Keluar</span>
            </button>
        </div>
    </header>

    {{-- ── Main Scrollable Canvas ────────────────────────────────────────── --}}
    <main class="flex-1 overflow-y-auto p-4 md:p-6 pb-24 md:pb-6 relative">
        
        {{-- Flash Messages --}}
        @if(session('status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="absolute top-4 right-4 z-50 flex items-center gap-3 rounded-xl bg-emerald-500 px-5 py-3 text-white shadow-xl">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="text-sm font-bold">{{ session('status') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                 class="absolute top-4 right-4 z-50 flex items-center gap-3 rounded-xl bg-red-600 px-5 py-3 text-white shadow-xl">
                <span class="material-symbols-outlined">error</span>
                <span class="text-sm font-bold">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 🔔 Toast Notif Pesanan Baru --}}
        <div
            x-data="{
                show: false,
                queue_number: '',
                order_number: '',
                timer: null,
                init() {
                    $wire.on('new-order', (data) => {
                        this.queue_number = data.queue_number;
                        this.order_number = data.order_number;
                        this.show = true;
                        if (this.timer) clearTimeout(this.timer);
                        this.timer = setTimeout(() => this.show = false, 6000);
                    });
                }
            }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="fixed bottom-24 left-1/2 -translate-x-1/2 z-50 flex items-center gap-4 bg-[#bc000a] text-white px-6 py-4 rounded-2xl shadow-2xl min-w-[280px]"
            style="display:none"
        >
            <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-[22px] icon-fill">notifications_active</span>
            </div>
            <div>
                <p class="font-black text-sm leading-tight">🔔 Pesanan Baru Masuk!</p>
                <p class="text-white/80 text-xs mt-0.5" x-text="`Antrian #${queue_number} — ${order_number}`"></p>
            </div>
            <button @click="show = false" class="ml-auto text-white/70 hover:text-white">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        {{-- TAB 1: PENDING (Menunggu Pembayaran) --}}
        @if($activeTab === 'pending')
            <div class="flex flex-col gap-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border-t-4 border-[#bc000a] px-4 py-3 flex justify-between items-center border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#bc000a] text-xl icon-fill">payments</span>
                        <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Menunggu Pembayaran</h2>
                    </div>
                    <span class="bg-red-50 dark:bg-red-950/20 text-[#bc000a] px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border border-red-100 dark:border-red-900/30">
                        {{ $this->pendingOrders->count() }} pesanan
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($this->pendingOrders as $order)
                        <div wire:key="pending-{{ $order->id }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col p-4 gap-3 transition-all hover:shadow-lg relative">
                            {{-- Decorative corner --}}
                            <div class="absolute top-0 right-0 w-16 h-16 bg-[#bc000a] rounded-bl-full opacity-10 pointer-events-none"></div>

                            <div class="flex justify-between items-start gap-3 z-10">
                                <div class="flex-1 min-w-0">
                                    <div class="text-5xl font-black text-[#bc000a] tracking-tighter leading-none">#{{ $order->queue_number }}</div>
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1.5 uppercase tracking-wide">{{ $order->order_number }}</div>
                                    <div class="mt-2.5 inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-xl text-xs font-bold">
                                        <span class="material-symbols-outlined text-[15px]">{{ $order->type === 'takeaway' ? 'local_mall' : 'restaurant' }}</span>
                                        <span>{{ $order->type === 'takeaway' ? 'Take Away' : 'Dine In' }}{{ $order->table_number ? ' · M-'.$order->table_number : '' }}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($order->payment_method === 'qris')
                                        <span class="bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 font-bold px-2 py-1 rounded text-[10px] uppercase tracking-wider border border-yellow-200 dark:border-yellow-800">QRIS</span>
                                    @else
                                        <span class="bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 font-bold px-2 py-1 rounded text-[10px] uppercase tracking-wider border border-blue-200 dark:border-blue-800">TUNAI</span>
                                    @endif
                                </div>
                            </div>

                            @if($order->member)
                                <div class="flex items-center gap-1.5 bg-yellow-50 dark:bg-yellow-950/20 px-3 py-2 rounded-lg text-xs font-semibold text-yellow-800 dark:text-yellow-500 mt-1">
                                    <span class="material-symbols-outlined text-[14px]">stars</span>
                                    {{ $order->member->name }}
                                </div>
                            @endif

                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-800 px-3 py-2 rounded-lg mt-1">
                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider">Total</span>
                                <span class="text-lg font-black text-[#bc000a]">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                            </div>

                            {{-- Actions --}}
                            <div class="flex gap-2 mt-auto pt-2 border-t border-gray-100 dark:border-gray-800">
                                <button wire:click="openCancelModal({{ $order->id }})" class="flex-1 border border-red-200 dark:border-red-900/50 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 font-bold text-xs py-2.5 rounded-xl transition-colors">Batal</button>
                                <button wire:click="openConfirmModal({{ $order->id }})" class="flex-[2] bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs py-2.5 rounded-xl transition-colors shadow-sm flex items-center justify-center gap-1">
                                    <span class="material-symbols-outlined text-[16px]">check_circle</span> Konfirmasi
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 block mb-2">payments</span>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tidak ada pesanan menunggu pembayaran.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- TAB 2: PROSES (Diproses Dapur) --}}
        @if($activeTab === 'proses')
            <div class="flex flex-col gap-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border-t-4 border-[#fdc003] px-4 py-3 flex justify-between items-center border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#fdc003] text-xl icon-fill">outdoor_grill</span>
                        <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Diproses Dapur</h2>
                    </div>
                    <span class="bg-yellow-50 dark:bg-yellow-950/20 text-[#6c5000] dark:text-yellow-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider border border-yellow-100 dark:border-yellow-900/30">
                        {{ $this->confirmedOrders->count() }} pesanan
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($this->confirmedOrders as $order)
                        <div wire:key="proses-{{ $order->id }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col p-4 gap-3 transition-all hover:shadow-lg">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="text-5xl font-black text-gray-800 dark:text-gray-100 tracking-tighter leading-none">#{{ $order->queue_number }}</div>
                                    <div class="mt-2.5 inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-xl text-xs font-bold">
                                        <span class="material-symbols-outlined text-[15px]">{{ $order->type === 'takeaway' ? 'local_mall' : 'restaurant' }}</span>
                                        <span>{{ $order->type === 'takeaway' ? 'Take Away' : 'Dine In' }}{{ $order->table_number ? ' · M-'.$order->table_number : '' }}</span>
                                    </div>
                                </div>
                                <div class="bg-yellow-50 dark:bg-yellow-950/20 border border-yellow-100 dark:border-yellow-900/30 px-3.5 py-2.5 rounded-2xl text-right flex flex-col items-end shrink-0">
                                    <div class="flex items-center gap-1 text-yellow-700 dark:text-yellow-500 font-extrabold text-[10px] uppercase tracking-wider">
                                        <span class="material-symbols-outlined text-[14px] animate-pulse">outdoor_grill</span>
                                        <span>Dimasak</span>
                                    </div>
                                </div>
                            </div>

                            <button wire:click="openDetailModal({{ $order->id }})" class="mt-auto w-full border-2 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-bold text-xs py-2.5 rounded-xl transition-colors flex items-center justify-center gap-1">
                                <span class="material-symbols-outlined text-[16px]">receipt_long</span> Detail Pesanan
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 block mb-2">soup_kitchen</span>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tidak ada pesanan sedang diproses.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- TAB 3: RIWAYAT (Selesai/Batal) --}}
        @if($activeTab === 'riwayat')
            <div class="flex flex-col gap-4">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border-t-4 border-emerald-500 px-4 py-3 flex justify-between items-center border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-emerald-500 text-xl icon-fill">task_alt</span>
                        <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Riwayat Pesanan Hari Ini</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @forelse($this->historyOrders as $order)
                        <div wire:key="riwayat-{{ $order->id }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col p-4 gap-3 transition-all hover:shadow-lg opacity-80 hover:opacity-100">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="text-4xl font-black text-gray-800 dark:text-gray-100 tracking-tighter leading-none">#{{ $order->queue_number }}</div>
                                    <div class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">{{ $order->order_number }}</div>
                                </div>
                                <div class="text-right">
                                    @if($order->status === 'completed')
                                        <span class="bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold px-2 py-1 rounded text-[10px] uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">Selesai</span>
                                    @elseif($order->status === 'cancelled')
                                        <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 font-bold px-2 py-1 rounded text-[10px] uppercase tracking-wider border border-red-200 dark:border-red-800">Batal</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mt-1">
                                <span>Total: <strong>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</strong></span>
                                <span>{{ $order->created_at->format('H:i') }}</span>
                            </div>
                            <button wire:click="openDetailModal({{ $order->id }})" class="mt-auto w-full border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 font-bold text-xs py-2 rounded-xl transition-colors">
                                Lihat Detail
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full py-12 text-center bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 block mb-2">history</span>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum ada riwayat pesanan hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- Modals ───────────────────────────────────────────────────────── --}}
        
        {{-- Detail Modal --}}
        @if($showDetailModal && $this->selectedOrder)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 x-data x-on:keydown.escape.window="$wire.closeDetailModal()">
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden"
                     @click.away="$wire.closeDetailModal()">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                        <div class="flex items-center gap-4">
                            <span class="font-black text-4xl text-[#bc000a] dark:text-red-500 tracking-tighter leading-none">#{{ $this->selectedOrder->queue_number }}</span>
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800 dark:text-gray-100">Detail Pesanan</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[10px] bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-2 py-0.5 rounded font-bold uppercase tracking-wider">{{ $this->selectedOrder->type === 'dine_in' ? 'Dine In' : 'Take Away' }}</span>
                                    @if($this->selectedOrder->payment_method === 'qris')
                                        <span class="text-[10px] bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 px-2 py-0.5 rounded font-bold uppercase tracking-wider">QRIS</span>
                                    @else
                                        <span class="text-[10px] bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 px-2 py-0.5 rounded font-bold uppercase tracking-wider">TUNAI</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <button wire:click="closeDetailModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-full p-2 transition-colors bg-white dark:bg-gray-800 shadow-sm border border-gray-100 dark:border-gray-700">
                            <span class="material-symbols-outlined block">close</span>
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-white dark:bg-gray-900">
                        @foreach($this->selectedOrder->details as $detail)
                            <div class="flex justify-between items-start pb-4 border-b border-dashed border-gray-200 dark:border-gray-800 last:border-0 last:pb-0">
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-700 dark:text-gray-300 font-black shrink-0">
                                        {{ $detail->quantity }}
                                    </div>
                                    <div class="flex flex-col mt-1">
                                        <span class="font-bold text-sm text-gray-800 dark:text-gray-200">{{ $detail->menu_item_name }}</span>
                                        @if($detail->notes)
                                            <span class="text-[11px] font-medium text-amber-700 dark:text-amber-500 mt-1">Catatan: {{ $detail->notes }}</span>
                                        @endif
                                    </div>
                                </div>
                                <span class="font-bold text-sm text-gray-800 dark:text-gray-200 mt-1">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        
                        @if($this->selectedOrder->notes)
                            <div class="bg-amber-50 dark:bg-amber-950/20 border border-amber-100 dark:border-amber-900/30 p-3 rounded-xl mt-4">
                                <p class="text-xs font-bold text-amber-800 dark:text-amber-500">📝 Catatan: {{ $this->selectedOrder->notes }}</p>
                            </div>
                        @endif

                        <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4 border border-gray-100 dark:border-gray-800 mt-4 space-y-2">
                            <div class="flex justify-between items-center text-sm font-semibold text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($this->selectedOrder->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @if($this->selectedOrder->discount_amount > 0)
                                <div class="flex justify-between items-center text-sm font-semibold text-emerald-600">
                                    <span>Diskon Voucher</span>
                                    <span>- Rp {{ number_format($this->selectedOrder->discount_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            @if($this->selectedOrder->points_redeemed > 0)
                                <div class="flex justify-between items-center text-sm font-semibold text-emerald-600">
                                    <span>Diskon Poin ({{ $this->selectedOrder->points_redeemed }})</span>
                                    <span>- Rp {{ number_format($this->selectedOrder->points_redeemed_amount, 0, ',', '.') }}</span>
                                </div>
                            @endif
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-2 flex justify-between items-center mt-2">
                                <span class="text-lg font-black text-gray-800 dark:text-gray-100">Total Bayar</span>
                                <span class="text-2xl font-black text-[#bc000a] dark:text-red-500">Rp {{ number_format($this->selectedOrder->total_amount, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 flex gap-3 justify-end shrink-0">
                        <button wire:click="closeDetailModal" class="px-5 py-2.5 rounded-xl text-gray-600 dark:text-gray-300 font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Tutup</button>
                        @if($this->selectedOrder->isPending())
                            <button wire:click="openConfirmModal({{ $this->selectedOrder->id }})" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition-colors shadow-sm flex items-center gap-1">
                                <span class="material-symbols-outlined text-[18px]">check_circle</span> Konfirmasi
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        {{-- Confirm Modal --}}
        @if($confirmingOrderId)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 x-data x-on:keydown.escape.window="$wire.set('confirmingOrderId', null)">
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden text-center p-6"
                     @click.away="$wire.set('confirmingOrderId', null)">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4">
                        <span class="material-symbols-outlined text-3xl">check_circle</span>
                    </div>
                    <h3 class="text-lg font-black text-gray-800 dark:text-gray-100 mb-2">Konfirmasi Pembayaran</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Apakah Anda yakin pesanan ini sudah dibayar lunas?</p>
                    <div class="flex gap-3">
                        <button wire:click="$set('confirmingOrderId', null)" class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Batal</button>
                        <button wire:click="confirmPayment" class="flex-1 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold shadow-sm transition-colors flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="confirmPayment">Ya, Lunas</span>
                            <span wire:loading wire:target="confirmPayment" class="material-symbols-outlined animate-spin">progress_activity</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Cancel Modal --}}
        @if($showCancelModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                 x-data x-on:keydown.escape.window="$wire.closeCancelModal()">
                <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden p-6"
                     @click.away="$wire.closeCancelModal()">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 rounded-full flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-xl">cancel</span>
                        </div>
                        <h3 class="text-lg font-black text-gray-800 dark:text-gray-100">Batalkan Pesanan</h3>
                    </div>
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Alasan Pembatalan</label>
                        <input wire:model="cancelReason" type="text" placeholder="Masukkan alasan..." class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-red-500 outline-none">
                        @error('cancelReason') <span class="text-xs text-red-500 font-medium mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div class="flex gap-3">
                        <button wire:click="closeCancelModal" class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">Kembali</button>
                        <button wire:click="cancelOrder" class="flex-1 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold shadow-sm transition-colors flex items-center justify-center gap-2">
                            <span wire:loading.remove wire:target="cancelOrder">Batalkan</span>
                            <span wire:loading wire:target="cancelOrder" class="material-symbols-outlined animate-spin">progress_activity</span>
                        </button>
                    </div>
                </div>
            </div>
        @endif
        
    </main>

    {{-- ── Bottom Navigation Tab Bar ───────────────────────────────────────── --}}
    <nav class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 px-2 py-2 shadow-lg flex justify-around items-center z-20 shrink-0">
        
        {{-- Tab Pending --}}
        <button wire:click="switchTab('pending')" class="relative flex flex-col items-center gap-1 px-4 py-2 rounded-2xl transition-all cursor-pointer {{ $activeTab === 'pending' ? 'bg-[#bc000a] text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            @if($this->pendingOrders->count() > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-yellow-500 text-white text-[10px] font-black flex items-center justify-center shadow-sm">{{ $this->pendingOrders->count() > 9 ? '9+' : $this->pendingOrders->count() }}</span>
            @endif
            <span class="material-symbols-outlined text-[22px] {{ $activeTab === 'pending' ? 'icon-fill' : '' }}">payments</span>
            <span class="text-[10px] font-bold uppercase tracking-wider">Menunggu</span>
        </button>

        {{-- Tab Proses --}}
        <button wire:click="switchTab('proses')" class="relative flex flex-col items-center gap-1 px-4 py-2 rounded-2xl transition-all cursor-pointer {{ $activeTab === 'proses' ? 'bg-[#fdc003] text-[#5c4000] shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            @if($this->confirmedOrders->count() > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-orange-500 text-white text-[10px] font-black flex items-center justify-center shadow-sm">{{ $this->confirmedOrders->count() > 9 ? '9+' : $this->confirmedOrders->count() }}</span>
            @endif
            <span class="material-symbols-outlined text-[22px] {{ $activeTab === 'proses' ? 'icon-fill' : '' }}">outdoor_grill</span>
            <span class="text-[10px] font-bold uppercase tracking-wider">Diproses</span>
        </button>

        {{-- Tab Riwayat --}}
        <button wire:click="switchTab('riwayat')" class="relative flex flex-col items-center gap-1 px-4 py-2 rounded-2xl transition-all cursor-pointer {{ $activeTab === 'riwayat' ? 'bg-emerald-600 text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span class="material-symbols-outlined text-[22px] {{ $activeTab === 'riwayat' ? 'icon-fill' : '' }}">task_alt</span>
            <span class="text-[10px] font-bold uppercase tracking-wider">Riwayat</span>
        </button>

    </nav>

    @push('scripts')
    <script>
        // ── Live Clock (persists across Livewire polls) ──────────────
        let kdsClockInterval = null;

        function updateKdsClock() {
            const now = new Date();
            const timeEl = document.getElementById('kds-time');
            const dateEl = document.getElementById('kds-date');
            if (timeEl) {
                const hh = String(now.getHours()).padStart(2, '0');
                const mm = String(now.getMinutes()).padStart(2, '0');
                const ss = String(now.getSeconds()).padStart(2, '0');
                timeEl.textContent = `${hh}.${mm}.${ss}`;
            }
            if (dateEl) {
                dateEl.textContent = now.toLocaleDateString('id-ID', {
                    weekday: 'long', day: '2-digit', month: 'long', year: 'numeric'
                });
            }
        }

        function startKdsClock() {
            updateKdsClock();
            if (!kdsClockInterval) {
                kdsClockInterval = setInterval(updateKdsClock, 1000);
            }
        }

        startKdsClock();
        document.addEventListener('livewire:update', function () {
            if (!document.getElementById('kds-time')) return;
            updateKdsClock(); 
        });

        // ── Simple Theme Toggle ──────────────────────────────────────
        function applyKdsTheme(theme) {
            const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);
            const icon = document.querySelector('[data-kds-theme-icon]');
            const label = document.querySelector('[data-kds-theme-label]');
            if (icon && label) {
                icon.textContent = isDark ? 'light_mode' : 'dark_mode';
                label.textContent = isDark ? 'Light' : 'Night';
            }
        }

        function kdsToggleTheme() {
            const current = localStorage.getItem('kds-theme') || 'system';
            const next = current === 'dark' ? 'light' : 'dark';
            localStorage.setItem('kds-theme', next);
            applyKdsTheme(next);
        }

        const storedTheme = localStorage.getItem('kds-theme') || 'system';
        applyKdsTheme(storedTheme);
    </script>
    @endpush
</div>
