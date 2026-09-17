<div wire:poll.5s.keep-alive class="flex flex-col h-screen w-full bg-[#f4f6f9] dark:bg-gray-950 text-gray-800 dark:text-gray-100 font-sans antialiased overflow-hidden">
    
    {{-- ── TopBar ────────────────────────────────────────────────── --}}
    <header class="bg-[#bc000a] border-b border-[#a00008] flex justify-between items-center w-full px-6 py-4 shadow-md z-10 shrink-0">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Ayam Geprek Rejo" class="h-12 w-auto object-contain drop-shadow-md">
            <div>
                <h1 class="text-md md:text-lg font-extrabold text-white tracking-wider leading-tight">DAPUR — GEPREK REJO</h1>
                <p class="text-[11px] text-white/70 font-semibold uppercase tracking-widest">Kitchen Display System</p>
            </div>
        </div>

        {{-- Clock, Theme Toggle, & Logout --}}
        <div class="flex items-center gap-4 md:gap-6">
            <div class="text-right hidden sm:block" wire:ignore>
                <div class="text-xl md:text-2xl font-black text-[#fabd00] dark:text-yellow-400 tracking-wider leading-none font-mono" id="kds-time">--.--.--</div>
                <div class="text-[10px] text-white/60 font-semibold mt-1 uppercase tracking-wide" id="kds-date">-</div>
            </div>

            {{-- Day / Dark Mode Toggle --}}
            <button
                id="kds-theme-btn"
                data-kds-theme-btn
                onclick="kdsToggleTheme()"
                class="flex items-center gap-1.5 px-3 py-2 rounded-xl border cursor-pointer text-xs font-bold border-white/40 bg-white/10 text-white hover:bg-white/20"
                title="Ubah Tema"
            >
                <span class="material-symbols-outlined text-[18px]" data-kds-theme-icon>dark_mode</span>
                <span class="hidden sm:inline uppercase tracking-wider" data-kds-theme-label>Night</span>
            </button>

            {{-- Logout Button --}}
            <button wire:click="logout" class="border-2 border-white/40 hover:bg-white/20 text-white px-4 py-2 rounded-xl transition-all font-bold text-xs flex items-center gap-2 shadow-xs cursor-pointer" title="Keluar">
                <span class="material-symbols-outlined text-sm">logout</span>
                <span class="hidden sm:inline">Keluar</span>
            </button>
        </div>
    </header>

    {{-- ── Main Scrollable Canvas ───────────────────────────────── --}}
    <main class="flex-1 overflow-y-auto p-4 md:p-6">

        {{-- Flash Stok Status --}}
        @if(session('stok_status'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="fixed top-4 right-4 z-50 flex items-center gap-3 rounded-xl bg-emerald-500 px-5 py-3 text-white shadow-xl">
                <span class="material-symbols-outlined">check_circle</span>
                <span class="text-sm font-bold">{{ session('stok_status') }}</span>
            </div>
        @endif


        {{-- 🔔 Toast Notif Antrian Baru --}}
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
                <p class="font-black text-sm leading-tight">🔔 Antrian Baru Masuk!</p>
                <p class="text-white/80 text-xs mt-0.5" x-text="`Antrian #${queue_number} — ${order_number}`"></p>
            </div>
            <button @click="show = false" class="ml-auto text-white/70 hover:text-white">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        
        @if($activeTab === 'antrian')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                
                {{-- Column 1: Antrian Masak --}}
                <section class="flex flex-col gap-4">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border-t-4 border-[#bc000a] px-4 py-3 flex justify-between items-center border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-yellow-500 animate-pulse"></span>
                            <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Antrian Masak</h2>
                        </div>
                        <span class="bg-[#fabd00] text-[#5b4300] px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-xs">
                            {{ $antrianCount }} pesanan
                        </span>
                    </div>

                    <div class="flex flex-col gap-4">
                        @forelse($antrianMasak as $order)
                            @php $isFirst = $loop->first; @endphp
                            <div wire:key="antrian-{{ $order->id }}" class="relative rounded-2xl overflow-hidden transition-all {{ $isFirst ? 'bg-white dark:bg-gray-900 shadow-md border border-gray-200 dark:border-gray-800 hover:shadow-lg' : 'bg-gray-50 dark:bg-gray-900/50 border border-dashed border-gray-300 dark:border-gray-700 opacity-70' }}">
                                {{-- LOCKOUT overlay untuk antrian ke-2 dst --}}
                                @unless($isFirst)
                                    <div class="absolute inset-0 z-10 bg-gray-100/80 dark:bg-gray-900/80 backdrop-blur-[1px] flex items-center justify-center rounded-2xl">
                                        <div class="text-center px-4">
                                            <span class="material-symbols-outlined text-4xl text-gray-400 dark:text-gray-600 block mb-2">lock</span>
                                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400">Selesaikan antrian</p>
                                            <p class="text-sm font-black text-[#bc000a]">#{{ $antrianMasak->first()->queue_number }} terlebih dahulu</p>
                                        </div>
                                    </div>
                                @endunless
                                <div class="flex flex-col p-4 gap-3">
                                    <div class="flex justify-between items-start gap-3">
                                        <div class="flex-1 min-w-0">
                                            <div class="text-5xl font-black {{ $isFirst ? 'text-[#bc000a] dark:text-red-500' : 'text-gray-400 dark:text-gray-600' }} tracking-tighter leading-none">#{{ $order->queue_number }}</div>
                                            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1.5 uppercase tracking-wide">{{ $order->order_number }}</div>
                                            <div class="mt-2.5 inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-xl text-xs font-bold">
                                                <span class="material-symbols-outlined text-[15px]">{{ $order->type === 'takeaway' ? 'local_mall' : 'restaurant' }}</span>
                                                <span>{{ $order->type === 'takeaway' ? 'Take Away' : 'Dine In' }}</span>
                                            </div>
                                        </div>
                                        
                                        {{-- Waiting Time Widget --}}
                                        <div class="bg-red-50 dark:bg-red-950/20 border border-red-100 dark:border-red-900/30 px-3.5 py-2.5 rounded-2xl text-right flex flex-col items-end min-w-[115px] shrink-0">
                                            <div class="flex items-center gap-1 text-[#bc000a] dark:text-red-500 font-extrabold text-[10px] uppercase tracking-wider">
                                                <span class="material-symbols-outlined text-[14px]">schedule</span>
                                                <span>Menunggu</span>
                                            </div>
                                            <div class="text-xl font-black text-[#bc000a] dark:text-red-500 mt-0.5 leading-none">{{ $this->getWaitingTime($order) }}</div>
                                            <div class="text-[9px] font-semibold text-gray-500 dark:text-gray-400 mt-1 uppercase">sejak {{ $order->confirmed_at ? $order->confirmed_at->format('H:i') : '-' }}</div>
                                        </div>
                                    </div>

                                    {{-- Items List --}}
                                    <div class="space-y-1.5 mt-1">
                                        @foreach($order->details as $detail)
                                            <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-950 border border-gray-100 dark:border-gray-800 px-3.5 py-2.5 rounded-xl">
                                                <span class="font-bold text-gray-800 dark:text-gray-200 text-sm leading-tight">{{ $detail->menu_item_name }}</span>
                                                <span class="bg-[#fabd00] text-[#5b4300] px-2.5 py-1 rounded-lg text-xs font-black shrink-0 ml-2">x{{ $detail->quantity }}</span>
                                            </div>
                                            @if(filled($detail->notes))
                                                <div class="text-[11px] font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/20 px-3 py-1.5 rounded-lg border border-amber-100 dark:border-amber-900/30 flex items-center gap-1">
                                                    <span class="material-symbols-outlined text-[13px]">edit_note</span>
                                                    <span>{{ $detail->notes }}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>

                                    {{-- Action Button --}}
                                    <button wire:click="mulaiMasak({{ $order->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="mulaiMasak({{ $order->id }})"
                                            {{ !$isFirst ? 'disabled' : '' }}
                                            class="w-full {{ $isFirst ? 'bg-[#bc000a] hover:bg-[#a00008] cursor-pointer' : 'bg-gray-300 dark:bg-gray-700 cursor-not-allowed' }} disabled:opacity-70 text-white py-3 px-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98] mt-1">
                                        <span wire:loading.remove wire:target="mulaiMasak({{ $order->id }})" class="material-symbols-outlined icon-fill text-[18px]">play_arrow</span>
                                        <span wire:loading wire:target="mulaiMasak({{ $order->id }})" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                        <span wire:loading.remove wire:target="mulaiMasak({{ $order->id }})">Mulai Masak</span>
                                        <span wire:loading wire:target="mulaiMasak({{ $order->id }})">Memproses...</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center p-10 text-center">
                                <div class="w-14 h-14 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600">restaurant</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tidak ada antrian masak saat ini.</p>
                                <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">Pesanan baru akan muncul otomatis</p>
                            </div>
                        @endforelse
                    </div>
                </section>

                {{-- Column 2: Sedang Dimasak --}}
                <section class="flex flex-col gap-4">
                    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border-t-4 border-blue-500 px-4 py-3 flex justify-between items-center border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                            <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Sedang Dimasak</h2>
                        </div>
                        <span class="bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                            {{ $masakCount }} pesanan
                        </span>
                    </div>

                    <div class="flex flex-col gap-4">
                        @forelse($sedangDimasak as $order)
                            <div wire:key="masak-{{ $order->id }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col p-4 gap-3 transition-all hover:shadow-lg">
                                <div class="flex justify-between items-start gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="text-5xl font-black text-[#bc000a] dark:text-red-500 tracking-tighter leading-none">#{{ $order->queue_number }}</div>
                                        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1.5 uppercase tracking-wide">{{ $order->order_number }}</div>
                                        <div class="mt-2.5 inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-xl text-xs font-bold">
                                            <span class="material-symbols-outlined text-[15px]">{{ $order->type === 'takeaway' ? 'local_mall' : 'restaurant' }}</span>
                                            <span>{{ $order->type === 'takeaway' ? 'Take Away' : 'Dine In' }}</span>
                                        </div>
                                    </div>
                                    
                                    {{-- Cooking Time Widget --}}
                                    <div class="bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 px-3.5 py-2.5 rounded-2xl text-right flex flex-col items-end min-w-[115px] shrink-0">
                                        <div class="flex items-center gap-1 text-blue-600 dark:text-blue-400 font-extrabold text-[10px] uppercase tracking-wider">
                                            <span class="material-symbols-outlined text-[14px] icon-fill">outdoor_grill</span>
                                            <span>Dimasak</span>
                                        </div>
                                        <div class="text-xl font-black text-blue-700 dark:text-blue-400 mt-0.5 leading-none">{{ $this->getCookingTime($order) }}</div>
                                        <div class="text-[9px] font-semibold text-gray-500 dark:text-gray-400 mt-1 uppercase">sejak {{ $order->updated_at ? $order->updated_at->format('H:i') : '-' }}</div>
                                    </div>
                                </div>

                                {{-- Items List --}}
                                <div class="space-y-1.5 mt-1">
                                    @foreach($order->details as $detail)
                                        <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-950 border border-gray-100 dark:border-gray-800 px-3.5 py-2.5 rounded-xl">
                                            <span class="font-bold text-gray-800 dark:text-gray-200 text-sm leading-tight">{{ $detail->menu_item_name }}</span>
                                            <span class="bg-[#fabd00] text-[#5b4300] px-2.5 py-1 rounded-lg text-xs font-black shrink-0 ml-2">x{{ $detail->quantity }}</span>
                                        </div>
                                        @if(filled($detail->notes))
                                            <div class="text-[11px] font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/20 px-3 py-1.5 rounded-lg border border-amber-100 dark:border-amber-900/30 flex items-center gap-1">
                                                <span class="material-symbols-outlined text-[13px]">edit_note</span>
                                                <span>{{ $detail->notes }}</span>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Action Button --}}
                                <button wire:click="selesaiMasak({{ $order->id }})"
                                        wire:loading.attr="disabled"
                                        wire:target="selesaiMasak({{ $order->id }})"
                                        class="w-full bg-[#10b981] hover:bg-[#059669] disabled:opacity-70 text-white py-3 px-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98] cursor-pointer mt-1">
                                    <span wire:loading.remove wire:target="selesaiMasak({{ $order->id }})" class="material-symbols-outlined icon-fill text-[18px]">check_circle</span>
                                    <span wire:loading wire:target="selesaiMasak({{ $order->id }})" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                    <span wire:loading.remove wire:target="selesaiMasak({{ $order->id }})">Selesai</span>
                                    <span wire:loading wire:target="selesaiMasak({{ $order->id }})">Menyimpan...</span>
                                </button>
                            </div>
                        @empty
                            <div class="bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center p-10 text-center">
                                <div class="w-14 h-14 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center mb-3">
                                    <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600">outdoor_grill</span>
                                </div>
                                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tidak ada pesanan yang sedang dimasak.</p>
                                <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">Tekan "Mulai Masak" dari antrian</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif

        {{-- TAB 2: PROSES --}}
        @if($activeTab === 'proses')
            <div class="flex flex-col gap-6">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border-t-4 border-blue-500 px-4 py-3 flex justify-between items-center border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></span>
                        <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Fokus Sedang Dimasak</h2>
                    </div>
                    <span class="bg-blue-100 dark:bg-blue-950/40 text-blue-700 dark:text-blue-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                        {{ $masakCount }} pesanan aktif
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($sedangDimasak as $order)
                        <div wire:key="proses-{{ $order->id }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-200 dark:border-gray-800 overflow-hidden flex flex-col p-4 gap-3">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="text-5xl font-black text-[#bc000a] dark:text-red-500 tracking-tighter leading-none">#{{ $order->queue_number }}</div>
                                    <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-1.5 uppercase tracking-wide">{{ $order->order_number }}</div>
                                    <div class="mt-2.5 inline-flex items-center gap-1.5 bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 px-3 py-1 rounded-xl text-xs font-bold">
                                        <span class="material-symbols-outlined text-[15px]">{{ $order->type === 'takeaway' ? 'local_mall' : 'restaurant' }}</span>
                                        <span>{{ $order->type === 'takeaway' ? 'Take Away' : 'Dine In' }}</span>
                                    </div>
                                </div>
                                
                                <div class="bg-blue-50 dark:bg-blue-950/20 border border-blue-100 dark:border-blue-900/30 px-3.5 py-2.5 rounded-2xl text-right flex flex-col items-end min-w-[110px] shrink-0">
                                    <div class="flex items-center gap-1 text-blue-600 dark:text-blue-400 font-extrabold text-[10px] uppercase tracking-wider">
                                        <span class="material-symbols-outlined text-[14px] icon-fill">outdoor_grill</span>
                                        <span>Dimasak</span>
                                    </div>
                                    <div class="text-xl font-black text-blue-700 dark:text-blue-400 mt-0.5 leading-none">{{ $this->getCookingTime($order) }}</div>
                                    <div class="text-[9px] font-semibold text-gray-500 dark:text-gray-400 mt-1 uppercase">sejak {{ $order->updated_at ? $order->updated_at->format('H:i') : '-' }}</div>
                                </div>
                            </div>

                            <div class="space-y-1.5 mt-1">
                                @foreach($order->details as $detail)
                                    <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-950 border border-gray-100 dark:border-gray-800 px-3.5 py-2.5 rounded-xl">
                                        <span class="font-bold text-gray-800 dark:text-gray-200 text-sm leading-tight">{{ $detail->menu_item_name }}</span>
                                        <span class="bg-[#fabd00] text-[#5b4300] px-2.5 py-1 rounded-lg text-xs font-black shrink-0 ml-2">x{{ $detail->quantity }}</span>
                                    </div>
                                    @if(filled($detail->notes))
                                        <div class="text-[11px] font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-950/20 px-3 py-1.5 rounded-lg border border-amber-100 dark:border-amber-900/30 flex items-center gap-1">
                                            <span class="material-symbols-outlined text-[13px]">edit_note</span>
                                            <span>{{ $detail->notes }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <button wire:click="selesaiMasak({{ $order->id }})"
                                    wire:loading.attr="disabled"
                                    wire:target="selesaiMasak({{ $order->id }})"
                                    class="w-full bg-[#10b981] hover:bg-[#059669] disabled:opacity-70 text-white py-3 px-4 rounded-xl font-bold text-sm flex items-center justify-center gap-2 shadow-sm transition-all active:scale-[0.98] cursor-pointer mt-1">
                                <span wire:loading.remove wire:target="selesaiMasak({{ $order->id }})" class="material-symbols-outlined icon-fill text-[18px]">check_circle</span>
                                <span wire:loading wire:target="selesaiMasak({{ $order->id }})" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                <span wire:loading.remove wire:target="selesaiMasak({{ $order->id }})">Selesai</span>
                                <span wire:loading wire:target="selesaiMasak({{ $order->id }})">Menyimpan...</span>
                            </button>
                        </div>
                    @empty
                        <div class="col-span-full bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center p-12 text-center">
                            <div class="w-14 h-14 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600">outdoor_grill</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tidak ada pesanan yang sedang dimasak saat ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- TAB 3: RIWAYAT --}}
        @if($activeTab === 'riwayat')
            <div class="flex flex-col gap-6">
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border-t-4 border-[#10b981] px-4 py-3 flex justify-between items-center border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#10b981] text-xl icon-fill">task_alt</span>
                        <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Riwayat Pesanan Hari Ini</h2>
                    </div>
                    <span class="bg-green-100 dark:bg-green-950/40 text-green-700 dark:text-green-400 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider">
                        {{ $riwayatPesanan->count() }} selesai
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($riwayatPesanan as $order)
                        <div wire:key="riwayat-{{ $order->id }}" class="bg-white dark:bg-gray-900 rounded-2xl shadow-md border border-gray-200 dark:border-gray-800/80 overflow-hidden flex flex-col p-4 gap-3 opacity-90 hover:opacity-100 transition-opacity">
                            <div class="flex justify-between items-start gap-3">
                                <div class="flex-1 min-w-0">
                                    <div class="text-4xl font-extrabold text-gray-400 dark:text-gray-500 tracking-tighter leading-none">#{{ $order->queue_number }}</div>
                                    <div class="text-xs font-semibold text-gray-400 dark:text-gray-500 mt-1 uppercase tracking-wide">{{ $order->order_number }}</div>
                                    <div class="mt-2 inline-flex items-center gap-1 bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-lg text-[10px] font-bold border border-gray-100 dark:border-gray-700">
                                        <span class="material-symbols-outlined text-[13px]">{{ $order->type === 'takeaway' ? 'local_mall' : 'restaurant' }}</span>
                                        <span>{{ $order->type === 'takeaway' ? 'Take Away' : 'Dine In' }}</span>
                                    </div>
                                </div>
                                
                                <div class="bg-green-50 dark:bg-green-950/20 border border-green-100 dark:border-green-900/30 px-3 py-2 rounded-2xl text-right flex flex-col items-end min-w-[110px] shrink-0">
                                    <div class="flex items-center gap-1 text-green-700 dark:text-green-400 font-extrabold text-[9px] uppercase tracking-wider">
                                        <span class="material-symbols-outlined text-[13px] icon-fill">done_all</span>
                                        <span>Selesai</span>
                                    </div>
                                    <div class="text-lg font-black text-green-700 dark:text-green-400 mt-0.5 leading-none">{{ $order->completed_at ? $order->completed_at->format('H:i') : '-' }}</div>
                                    <div class="text-[9px] text-gray-500 dark:text-gray-400 mt-1 uppercase">dari {{ $order->confirmed_at ? $order->confirmed_at->format('H:i') : '-' }}</div>
                                </div>
                            </div>

                            <div class="space-y-1.5 mt-1 flex-1">
                                @foreach($order->details as $detail)
                                    <div class="flex justify-between items-center bg-gray-50/70 dark:bg-gray-950/50 border border-gray-100 dark:border-gray-800/50 px-3 py-2 rounded-xl">
                                        <span class="font-semibold text-gray-600 dark:text-gray-400 text-xs">{{ $detail->menu_item_name }}</span>
                                        <span class="bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400 px-2 py-0.5 rounded text-[10px] font-extrabold ml-2">x{{ $detail->quantity }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="col-span-full bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 flex flex-col items-center justify-center p-12 text-center">
                            <div class="w-14 h-14 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center mb-3">
                                <span class="material-symbols-outlined text-3xl text-gray-300 dark:text-gray-600">history</span>
                            </div>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Belum ada pesanan yang selesai dimasak hari ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- TAB 4: MENU --}}
        @if($activeTab === 'menu')
            <div class="flex flex-col gap-6">
                
                {{-- Search & Title --}}
                <div class="bg-white dark:bg-gray-900 p-4 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#bc000a] text-xl icon-fill">restaurant_menu</span>
                        <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Ketersediaan Menu</h2>
                    </div>

                    {{-- Search Input --}}
                    <div class="relative flex items-center bg-gray-50 dark:bg-gray-950 px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-800 flex-1 sm:max-w-xs">
                        <span class="material-symbols-outlined text-gray-400 text-lg mr-2 shrink-0">search</span>
                        <input type="text" wire:model.live.debounce.250ms="searchQuery" placeholder="Cari nama menu..." class="w-full bg-transparent border-none outline-none text-xs text-gray-700 dark:text-gray-200 placeholder-gray-400 focus:ring-0">
                        @if(filled($searchQuery))
                            <button wire:click="$set('searchQuery', '')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 cursor-pointer ml-1 shrink-0">
                                <span class="material-symbols-outlined text-base">close</span>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Categories & Items --}}
                <div class="space-y-6">
                    @forelse($menuItemsGroup as $categoryName => $items)
                        <div class="bg-white dark:bg-gray-900/60 p-5 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-xs">
                            <h3 class="text-xs font-black text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 pb-2.5 flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-sm text-[#bc000a]">label</span>
                                <span>{{ $categoryName }}</span>
                                <span class="ml-auto text-[10px] normal-case font-semibold bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-2 py-0.5 rounded-full">{{ count($items) }} menu</span>
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach($items as $item)
                                    <div class="p-3.5 rounded-xl border {{ $item->is_available ? 'bg-white dark:bg-gray-950 border-gray-200 dark:border-gray-800' : 'bg-gray-50 dark:bg-gray-950/50 border-gray-200 dark:border-gray-800' }} flex justify-between items-center transition-all hover:shadow-sm gap-3">
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold text-sm {{ $item->is_available ? 'text-gray-800 dark:text-gray-200' : 'text-gray-400 dark:text-gray-600 line-through' }} truncate">{{ $item->name }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-500 mt-0.5 font-semibold">Rp {{ number_format($item->price, 0, ',', '.') }}</p>
                                            @if(!$item->is_available)
                                                <span class="text-[9px] font-black uppercase tracking-wider text-red-500 dark:text-red-400 bg-red-50 dark:bg-red-950/20 px-1.5 py-0.5 rounded mt-1 inline-block">Habis</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Toggle Switch --}}
                                        <button wire:click="toggleAvailability({{ $item->id }})" 
                                                wire:loading.attr="disabled"
                                                wire:target="toggleAvailability({{ $item->id }})"
                                                class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $item->is_available ? 'bg-[#bc000a]' : 'bg-gray-300 dark:bg-gray-700' }}"
                                                role="switch" aria-checked="{{ $item->is_available ? 'true' : 'false' }}">
                                            <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow-md ring-0 transition duration-200 ease-in-out {{ $item->is_available ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="bg-white dark:bg-gray-900 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-800 p-12 text-center">
                            <span class="material-symbols-outlined text-4xl text-gray-300 dark:text-gray-600 mb-3">search_off</span>
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Tidak ada menu yang sesuai pencarian.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- TAB 5: STOK BAHAN --}}
        @if($activeTab === 'stok')
            @php
                $totalBahan = $stokIngredients->count();
                $stokRendah = $stokIngredients->filter(fn($s) => $s->current_stock <= $s->minimum_stock)->count();
                $stokAman   = $totalBahan - $stokRendah;
            @endphp
            <div class="flex flex-col gap-4">

                {{-- Header --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border-t-4 border-[#bc000a] px-4 py-3 flex justify-between items-center border border-gray-100 dark:border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="material-symbols-outlined text-[#bc000a] text-xl icon-fill">inventory_2</span>
                        <h2 class="text-sm font-extrabold uppercase tracking-widest text-gray-700 dark:text-gray-300">Stok Bahan Baku</h2>
                    </div>
                    <span class="text-[10px] font-semibold text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800 px-3 py-1 rounded-full">View-only dapur</span>
                </div>

                {{-- Summary Cards --}}
                <div class="grid grid-cols-3 gap-3">
                    {{-- Total --}}
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 flex flex-col gap-1">
                        <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Bahan</p>
                        <p class="text-2xl font-black text-gray-800 dark:text-gray-100">{{ $totalBahan }}</p>
                    </div>
                    {{-- Stok Aman --}}
                    <div class="bg-white dark:bg-gray-900 border-t-4 border-t-emerald-500 border border-gray-200 dark:border-gray-800 rounded-xl p-4 flex flex-col gap-1">
                        <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Stok Aman</p>
                        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400">{{ $stokAman }}</p>
                    </div>
                    {{-- Stok Rendah --}}
                    <div class="bg-red-50 dark:bg-red-950/20 border-t-4 border-t-[#bc000a] border border-red-100 dark:border-red-900/30 rounded-xl p-4 flex flex-col gap-1 {{ $stokRendah > 0 ? 'animate-pulse' : '' }}">
                        <p class="text-[10px] font-bold text-[#bc000a] uppercase tracking-wider">Stok Rendah</p>
                        <p class="text-2xl font-black text-[#bc000a]">{{ $stokRendah }}</p>
                    </div>
                </div>

                {{-- Filters --}}
                <div class="flex gap-3">
                    <div class="relative flex-1">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                        <input wire:model.live.debounce.300ms="stokSearch" type="text" placeholder="Cari bahan baku..."
                               class="w-full pl-10 pr-4 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#bc000a] transition-all text-gray-800 dark:text-gray-200">
                    </div>
                    <div class="relative min-w-[140px]">
                        <select wire:model.live="stokFilter"
                                class="w-full pl-4 pr-10 py-2.5 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm appearance-none focus:outline-none focus:ring-2 focus:ring-[#bc000a] transition-all text-gray-800 dark:text-gray-200">
                            <option value="">Semua Status</option>
                            <option value="ok">Stok Aman</option>
                            <option value="low">Stok Rendah</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-[18px]">expand_more</span>
                    </div>
                </div>

                {{-- Data List --}}
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-sm overflow-hidden">
                    {{-- Desktop Header --}}
                    <div class="hidden md:grid grid-cols-12 gap-4 p-4 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-gray-800/50 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        <div class="col-span-4">Nama Bahan</div>
                        <div class="col-span-3 text-center">Stok Saat Ini</div>
                        <div class="col-span-2 text-center">Status</div>
                        <div class="col-span-3 text-right">Aksi</div>
                    </div>

                    <div class="flex flex-col divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse($stokIngredients as $item)
                            @php
                                $isRendah = $item->current_stock < $item->minimum_stock;
                                $pct = $item->minimum_stock > 0
                                    ? min(100, round(($item->current_stock / ($item->minimum_stock * 2)) * 100))
                                    : 100;
                            @endphp
                            <div class="p-4 flex flex-col md:grid md:grid-cols-12 md:items-center gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition-colors">
                                {{-- Nama --}}
                                <div class="flex items-center gap-3 md:col-span-4">
                                    <div class="w-10 h-10 rounded-xl {{ $isRendah ? 'bg-red-100 dark:bg-red-950/40' : 'bg-gray-100 dark:bg-gray-800' }} flex items-center justify-center shrink-0 text-lg">
                                        {{ $isRendah ? '⚠️' : '📦' }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm text-gray-800 dark:text-gray-200 flex items-center gap-1">
                                            {{ $item->name }}
                                            @if($isRendah)
                                                <span class="material-symbols-outlined text-[#bc000a] text-sm">error</span>
                                            @endif
                                        </p>
                                        <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5 md:hidden">Min: {{ number_format($item->minimum_stock, 2) }} {{ $item->unit }}</p>
                                    </div>
                                </div>

                                {{-- Stok --}}
                                <div class="flex flex-col md:col-span-3 md:items-center">
                                    <span class="text-sm font-bold {{ $isRendah ? 'text-[#bc000a]' : 'text-gray-800 dark:text-gray-200' }} mb-1">
                                        {{ number_format($item->current_stock, 2) }} {{ $item->unit }}
                                    </span>
                                    <div class="w-full md:w-3/4 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                        <div class="h-full {{ $isRendah ? 'bg-[#bc000a]' : 'bg-emerald-500' }}"
                                             style="width: {{ $pct }}%"></div>
                                    </div>
                                    <span class="text-[10px] text-gray-400 dark:text-gray-500 mt-0.5">
                                        Min: {{ number_format($item->minimum_stock, 2) }} {{ $item->unit }}
                                    </span>
                                </div>

                                {{-- Status Badge --}}
                                <div class="md:col-span-2 flex md:justify-center">
                                    @if($isRendah)
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-red-100 dark:bg-red-950/40 text-[#bc000a] text-[10px] font-black uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-[#bc000a]"></span> Rendah
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-black uppercase tracking-wider">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Aman
                                        </span>
                                    @endif
                                </div>

                                {{-- Action: Restock Only --}}
                                <div class="md:col-span-3 flex justify-end">
                                    <button wire:click="openRestock({{ $item->id }})"
                                            class="flex items-center justify-center gap-1.5 px-4 py-2 {{ $isRendah ? 'bg-[#bc000a] text-white hover:bg-[#a00008] shadow-sm' : 'border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-emerald-500 hover:text-emerald-600 dark:hover:text-emerald-400' }} rounded-xl text-xs font-bold transition-colors">
                                        <span class="material-symbols-outlined text-[15px]">add_circle</span>
                                        Restock
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="py-16 text-center">
                                <span class="material-symbols-outlined text-5xl text-gray-300 dark:text-gray-600 block mb-3">inventory_2</span>
                                <p class="text-gray-500 dark:text-gray-400 font-semibold text-sm">Belum ada bahan baku terdaftar.</p>
                                <p class="text-gray-400 dark:text-gray-600 text-xs mt-1">Tambahkan bahan melalui panel owner.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            {{-- Modal Restock --}}
            @if($showRestockModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                     x-data x-on:keydown.escape.window="$wire.closeRestockModal()">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-sm overflow-hidden"
                         @click.away="$wire.closeRestockModal()">
                        {{-- Header --}}
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between bg-gray-50 dark:bg-gray-800/50">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-[#bc000a]/10 dark:bg-red-950/40 flex items-center justify-center">
                                    <span class="material-symbols-outlined text-[#bc000a] text-[20px] icon-fill">add_circle</span>
                                </div>
                                <h3 class="font-bold text-gray-800 dark:text-gray-100">Restock Bahan</h3>
                            </div>
                            <button wire:click="closeRestockModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-full p-1 transition-colors">
                                <span class="material-symbols-outlined">close</span>
                            </button>
                        </div>

                        {{-- Body --}}
                        <form wire:submit.prevent="applyRestock" class="p-6">
                            <div class="mb-5">
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Jumlah Ditambahkan</label>
                                <div class="flex items-center gap-2">
                                    <button type="button" wire:click="$set('restockQty', [restockQty] > 1 ? [restockQty] - 1 : 0)"
                                            class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 font-black text-lg transition-colors">
                                        -
                                    </button>
                                    <input wire:model="restockQty" type="number" step="0.01" min="0.01"
                                           class="flex-1 px-4 py-3 text-center bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-lg font-black text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-[#bc000a] outline-none">
                                    <button type="button" wire:click="$set('restockQty', [restockQty] + 1)"
                                            class="w-11 h-11 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 flex items-center justify-center text-gray-700 dark:text-gray-300 font-black text-lg transition-colors">
                                        +
                                    </button>
                                </div>
                                @error('restockQty')
                                    <span class="text-xs text-red-500 font-medium mt-1.5 block text-center">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label class="block text-xs font-bold text-gray-600 dark:text-gray-400 uppercase tracking-wider mb-2">Catatan (opsional)</label>
                                <input wire:model="restockNote" type="text" placeholder="Misal: Belanja pagi hari ini..."
                                       class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-800 dark:text-gray-200 focus:ring-2 focus:ring-[#bc000a] outline-none">
                            </div>

                            <div class="flex gap-3">
                                <button type="button" wire:click="closeRestockModal"
                                        class="flex-1 py-3 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                                    Batal
                                </button>
                                <button type="submit"
                                        class="flex-[2] py-3 rounded-xl bg-[#bc000a] hover:bg-[#a00008] text-white font-bold shadow-sm transition-colors flex items-center justify-center gap-2">
                                    <span wire:loading.remove wire:target="applyRestock" class="material-symbols-outlined text-[18px]">add_circle</span>
                                    <span wire:loading wire:target="applyRestock" class="material-symbols-outlined animate-spin text-[18px]">progress_activity</span>
                                    <span wire:loading.remove wire:target="applyRestock">Tambah Stok</span>
                                    <span wire:loading wire:target="applyRestock">Menyimpan...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endif

    </main>

    {{-- ── Bottom Navigation Tab Bar ────────────────────────────── --}}
    <nav class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 px-2 py-2 shadow-lg flex justify-around items-center z-20 shrink-0">
        
        {{-- Tab Antrian --}}
        <button wire:click="switchTab('antrian')" class="relative flex flex-col items-center gap-1 px-5 py-2 rounded-2xl transition-all cursor-pointer {{ $activeTab === 'antrian' ? 'bg-[#bc000a] text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            @if($antrianCount > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-yellow-500 text-white text-[10px] font-black flex items-center justify-center shadow-sm">{{ $antrianCount > 9 ? '9+' : $antrianCount }}</span>
            @endif
            <span class="material-symbols-outlined text-[22px] {{ $activeTab === 'antrian' ? 'icon-fill' : '' }}">list_alt</span>
            <span class="text-[10px] font-bold uppercase tracking-wider">Antrian</span>
        </button>

        {{-- Tab Proses --}}
        <button wire:click="switchTab('proses')" class="relative flex flex-col items-center gap-1 px-5 py-2 rounded-2xl transition-all cursor-pointer {{ $activeTab === 'proses' ? 'bg-[#bc000a] text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            @if($masakCount > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-blue-500 text-white text-[10px] font-black flex items-center justify-center shadow-sm">{{ $masakCount > 9 ? '9+' : $masakCount }}</span>
            @endif
            <span class="material-symbols-outlined text-[22px] {{ $activeTab === 'proses' ? 'icon-fill' : '' }}">outdoor_grill</span>
            <span class="text-[10px] font-bold uppercase tracking-wider">Proses</span>
        </button>

        {{-- Tab Riwayat --}}
        <button wire:click="switchTab('riwayat')" class="relative flex flex-col items-center gap-1 px-5 py-2 rounded-2xl transition-all cursor-pointer {{ $activeTab === 'riwayat' ? 'bg-[#bc000a] text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span class="material-symbols-outlined text-[22px] {{ $activeTab === 'riwayat' ? 'icon-fill' : '' }}">history</span>
            <span class="text-[10px] font-bold uppercase tracking-wider">Riwayat</span>
        </button>

        {{-- Tab Menu --}}
        <button wire:click="switchTab('menu')" class="relative flex flex-col items-center gap-1 px-3 py-2 rounded-2xl transition-all cursor-pointer {{ $activeTab === 'menu' ? 'bg-[#bc000a] text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            <span class="material-symbols-outlined text-[22px] {{ $activeTab === 'menu' ? 'icon-fill' : '' }}">restaurant_menu</span>
            <span class="text-[10px] font-bold uppercase tracking-wider">Menu</span>
        </button>

        {{-- Tab Stok --}}
        <button wire:click="switchTab('stok')" class="relative flex flex-col items-center gap-1 px-3 py-2 rounded-2xl transition-all cursor-pointer {{ $activeTab === 'stok' ? 'bg-[#bc000a] text-white shadow-md' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800' }}">
            @php
                $stokRendahCount = \App\Models\StockIngredient::whereColumn('current_stock', '<=', 'minimum_stock')->count();
            @endphp
            @if($stokRendahCount > 0)
                <span class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-[#bc000a] text-white text-[10px] font-black flex items-center justify-center shadow-sm">{{ $stokRendahCount > 9 ? '9+' : $stokRendahCount }}</span>
            @endif
            <span class="material-symbols-outlined text-[22px] {{ $activeTab === 'stok' ? 'icon-fill' : '' }}">inventory_2</span>
            <span class="text-[10px] font-bold uppercase tracking-wider">Stok</span>
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

        // Boot clock immediately
        startKdsClock();

        // Re-attach after Livewire morphs the DOM (poll reinit)
        document.addEventListener('livewire:update', function () {
            if (!document.getElementById('kds-time')) return;
            updateKdsClock(); // snap current time immediately after poll
        });
    </script>
    @endpush
</div>
