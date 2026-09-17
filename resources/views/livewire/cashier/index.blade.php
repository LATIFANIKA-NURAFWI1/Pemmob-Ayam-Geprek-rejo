<div class="flex h-full w-full flex-1 flex-col bg-surface text-on-surface font-body-md antialiased">

    {{-- ── Top App Bar (Kasir / KDS Antrian) ────────────────────── --}}
    <header class="bg-surface dark:bg-inverse-surface border-b border-outline-variant shadow-sm flex justify-between items-center w-full px-container-padding py-4 sticky top-0 z-40">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/logo.png') }}" alt="Kasir Geprek Rejo" class="h-10 w-auto object-contain drop-shadow-sm">
            <div>
                <h1 class="text-headline-md font-headline-md font-bold text-on-surface">Kasir — Geprek Rejo</h1>
                <p class="text-label-caps font-label-caps text-on-surface-variant">Manajemen Pembayaran</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right hidden md:block">
                <div class="text-body-lg font-body-lg font-bold text-primary" id="cashier-time">--:--</div>
            </div>
        </div>
    </header>

    <main class="flex-grow p-container-padding">
        {{-- ── Status Filter Pills ─────────────────────────────── --}}
        <div class="flex gap-4 mb-section-margin overflow-x-auto pb-2 hide-scrollbar">
            <button wire:click="setFilter('menunggu_pembayaran')"
                    class="{{ $filter === 'menunggu_pembayaran' ? 'bg-secondary-container text-on-secondary-container border-secondary shadow-sm' : 'bg-surface text-on-surface border-outline-variant hover:bg-surface-variant' }} px-4 py-2 rounded-full font-label-caps text-label-caps flex items-center gap-2 transition-all border shrink-0">
                <span class="material-symbols-outlined text-[16px]">pending_actions</span>
                Menunggu Pembayaran
                @if(($counts['menunggu_pembayaran'] ?? 0) > 0)
                    <span class="{{ $filter === 'menunggu_pembayaran' ? 'bg-on-secondary-container text-secondary-container' : 'bg-surface-variant text-on-surface' }} px-2 rounded-full ml-1">{{ $counts['menunggu_pembayaran'] ?? 0 }}</span>
                @endif
            </button>
            <button wire:click="setFilter('diproses_dapur')"
                    class="{{ $filter === 'diproses_dapur' ? 'bg-secondary-container text-on-secondary-container border-secondary shadow-sm' : 'bg-surface text-on-surface border-outline-variant hover:bg-surface-variant' }} px-4 py-2 rounded-full font-label-caps text-label-caps flex items-center gap-2 border transition-all shrink-0">
                <span class="material-symbols-outlined text-[16px]">outdoor_grill</span>
                Diproses Dapur
                @if(($counts['diproses_dapur'] ?? 0) > 0)
                    <span class="bg-surface-variant text-on-surface px-2 rounded-full ml-1">{{ $counts['diproses_dapur'] ?? 0 }}</span>
                @endif
            </button>
            <button wire:click="setFilter('selesai')"
                    class="{{ $filter === 'selesai' ? 'bg-secondary-container text-on-secondary-container border-secondary shadow-sm' : 'bg-surface text-on-surface border-outline-variant hover:bg-surface-variant' }} px-4 py-2 rounded-full font-label-caps text-label-caps flex items-center gap-2 border transition-all shrink-0">
                <span class="material-symbols-outlined text-[16px]">check_circle</span>
                Selesai
            </button>
        </div>

        {{-- ── 2-Column Grid ─────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

            {{-- Column 1: Menunggu Pembayaran --}}
            <section class="flex flex-col gap-card-gap">
                <div class="flex items-center justify-between border-b-2 border-primary pb-2 mb-2">
                    <h2 class="font-headline-md text-headline-md text-primary flex items-center gap-2">
                        <span class="material-symbols-outlined icon-fill">payments</span>
                        Menunggu Pembayaran
                    </h2>
                    <span class="bg-primary text-on-primary font-label-caps text-label-caps px-2 py-1 rounded-full">{{ $counts['menunggu_pembayaran'] ?? 0 }}</span>
                </div>

                @forelse($menungguPembayaran ?? [] as $order)
                    <article class="bg-surface rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col gap-4 hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute top-0 right-0 w-16 h-16 bg-primary-container rounded-bl-full -z-0 opacity-20"></div>
                        <div class="flex justify-between items-start z-10">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="bg-secondary-container text-on-secondary-container font-label-caps text-label-caps px-2 py-0.5 rounded">{{ strtoupper($order->payment_method) }}</span>
                                    <span class="text-on-surface-variant font-body-md text-body-md flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span> {{ $order->time_formatted }}
                                    </span>
                                </div>
                                <h3 class="font-order-number text-order-number text-on-surface leading-none">#{{ $order->order_number }}</h3>
                            </div>
                            <div class="text-right">
                                <span class="block font-label-caps text-label-caps text-on-surface-variant mb-1">{{ $order->type === 'dine_in' ? 'MEJA' : 'BUNGKUS' }}</span>
                                <span class="font-headline-md text-headline-md text-primary bg-primary-container/20 px-3 py-1 rounded-lg">{{ $order->table_label }}</span>
                            </div>
                        </div>
                        <div class="border-t border-dashed border-outline-variant pt-3 z-10">
                            <ul class="flex flex-col gap-2 font-body-md text-body-md text-on-surface">
                                @foreach($order->items as $item)
                                    <li class="flex justify-between">
                                        <span>{{ $item->quantity }}x {{ $item->menu_name }}</span>
                                        <span class="font-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="border-t border-outline-variant pt-3 flex justify-between items-center z-10">
                            <div>
                                <span class="block font-label-caps text-label-caps text-on-surface-variant">TOTAL TAGIHAN</span>
                                <span class="font-headline-lg text-headline-lg text-primary">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 mt-2 z-10">
                            <button wire:click="batalkanPesanan({{ $order->id }})"
                                    class="flex-1 bg-surface border border-outline text-on-surface font-body-lg text-body-lg py-3 rounded-lg hover:bg-error-container hover:text-on-error-container hover:border-error transition-colors">
                                Batal
                            </button>
                            <button wire:click="konfirmasiLunas({{ $order->id }})"
                                    class="flex-[2] bg-gradient-to-r from-emerald-600 to-emerald-500 text-white font-body-lg text-body-lg py-3 rounded-lg shadow-sm hover:shadow-md transition-shadow font-bold flex items-center justify-center gap-2">
                                <span class="material-symbols-outlined">check_circle</span>
                                Konfirmasi Lunas
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-outline-variant rounded-xl bg-surface-container-low h-48 opacity-60">
                        <span class="material-symbols-outlined text-4xl text-outline mb-2">receipt_long</span>
                        <p class="font-body-md text-body-md text-on-surface-variant text-center">Tidak ada pesanan menunggu pembayaran.</p>
                    </div>
                @endforelse
            </section>

            {{-- Column 2: Sedang Diproses Dapur --}}
            <section class="flex flex-col gap-card-gap">
                <div class="flex items-center justify-between border-b-2 border-secondary-container pb-2 mb-2">
                    <h2 class="font-headline-md text-headline-md text-on-surface flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary-container icon-fill">soup_kitchen</span>
                        Diproses Dapur
                    </h2>
                    <span class="bg-secondary-container text-on-secondary-container font-label-caps text-label-caps px-2 py-1 rounded-full">{{ $counts['diproses_dapur'] ?? 0 }}</span>
                </div>

                @forelse($diproseDapur ?? [] as $order)
                    <article class="bg-surface rounded-xl border border-outline-variant shadow-sm p-5 flex flex-col gap-4 hover:shadow-md transition-shadow">
                        <div class="flex justify-between items-start">
                            <div>
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="bg-surface-variant text-on-surface font-label-caps text-label-caps px-2 py-0.5 rounded border border-outline-variant">LUNAS</span>
                                    <span class="text-on-surface-variant font-body-md text-body-md flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px]">schedule</span> {{ $order->time_formatted }}
                                    </span>
                                </div>
                                <h3 class="font-order-number text-order-number text-on-surface leading-none">#{{ $order->order_number }}</h3>
                            </div>
                            <div class="text-right">
                                <span class="block font-label-caps text-label-caps text-on-surface-variant mb-1">{{ $order->type === 'dine_in' ? 'MEJA' : 'BUNGKUS' }}</span>
                                <span class="font-headline-md text-headline-md text-primary bg-primary-container/20 px-3 py-1 rounded-lg">{{ $order->table_label }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 text-secondary font-body-md text-body-md bg-secondary-container/10 p-2 rounded-lg border border-secondary-container/20">
                            <span class="material-symbols-outlined text-[18px] animate-pulse">outdoor_grill</span>
                            <span>Sedang dimasak (est. {{ $order->est_time }})</span>
                        </div>
                        <div>
                            <button wire:click="lihatDetail({{ $order->id }})"
                                    class="w-full bg-surface border-2 border-secondary-container text-on-surface font-body-lg text-body-lg py-2 rounded-lg hover:bg-secondary-container hover:text-on-secondary-container transition-colors flex justify-center items-center gap-2">
                                <span class="material-symbols-outlined">receipt_long</span>
                                Detail Pesanan
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="flex flex-col items-center justify-center p-8 border-2 border-dashed border-outline-variant rounded-xl bg-surface-container-low h-48 opacity-60">
                        <span class="material-symbols-outlined text-4xl text-outline mb-2">outdoor_grill</span>
                        <p class="font-body-md text-body-md text-on-surface-variant text-center">Tidak ada pesanan lain yang sedang diproses.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </main>

    @push('scripts')
    <script>
        function updateCashierClock() {
            const el = document.getElementById('cashier-time');
            if (el) el.textContent = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
        }
        updateCashierClock();
        setInterval(updateCashierClock, 60000);
    </script>
    @endpush
</div>
