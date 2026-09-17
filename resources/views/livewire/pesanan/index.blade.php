<div class="flex h-full w-full flex-1 flex-col gap-section-margin p-6 bg-background text-on-background font-body-md antialiased">

    {{-- ══ Header & View Toggle ════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-6">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-background mb-1">Pesanan</h1>
            <p class="text-on-surface-variant">Riwayat transaksi penjualan</p>
        </div>

        {{-- Mode Toggle Pill --}}
        <div class="inline-flex bg-surface-container rounded-full p-1 self-start md:self-auto">
            <button wire:click="$set('viewMode', 'harian')"
                    class="{{ $viewMode === 'harian' ? 'bg-secondary-container text-on-secondary-container shadow-sm' : 'text-on-surface-variant hover:bg-surface-variant' }} px-6 py-2 rounded-full font-label-caps text-label-caps transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">calendar_view_day</span>
                Harian
            </button>
            <button wire:click="$set('viewMode', 'bulanan')"
                    class="{{ $viewMode === 'bulanan' ? 'bg-secondary-container text-on-secondary-container shadow-sm' : 'text-on-surface-variant hover:bg-surface-variant' }} px-6 py-2 rounded-full font-label-caps text-label-caps transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">calendar_month</span>
                Bulanan
            </button>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         MODE: HARIAN
    ══════════════════════════════════════════════════════════════════════════════ --}}
    @if($viewMode === 'harian')

        {{-- Filters --}}
        <div class="flex flex-col md:flex-row gap-4">
            <div class="relative w-full md:w-64">
                <input wire:model.live="tanggal" type="date"
                       class="w-full bg-surface-container-lowest border border-surface-variant rounded-xl px-4 py-3 text-on-background focus:ring-2 focus:ring-secondary focus:border-transparent outline-none transition-all cursor-pointer font-body-md text-body-md">
            </div>
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input wire:model.live.debounce.300ms="search" type="text"
                       placeholder="Cari no. pesanan..."
                       class="w-full bg-surface-container-lowest border border-surface-variant rounded-xl pl-12 pr-4 py-3 text-on-background focus:ring-2 focus:ring-secondary focus:border-transparent outline-none transition-all font-body-md text-body-md">
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $pendapatanHarian = ($orders ?? collect())->where('status', 'completed')->sum('total_amount');
            $totalPesananHarian = ($orders ?? collect())->count();
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-2 gap-card-gap">
            {{-- Pendapatan --}}
            <div class="bg-surface-container-lowest border border-surface-variant rounded-xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.1)] transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary">payments</span>
                    </div>
                    <span class="font-label-caps text-label-caps text-on-surface-variant">PENDAPATAN</span>
                </div>
                <div class="font-headline-lg text-headline-lg text-primary font-bold">
                    Rp {{ number_format($pendapatanHarian, 0, ',', '.') }}
                </div>
            </div>
            {{-- Total Pesanan --}}
            <div class="bg-surface-container-lowest border border-surface-variant rounded-xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:shadow-[0_8px_30px_-4px_rgba(0,0,0,0.1)] transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-background">receipt_long</span>
                    </div>
                    <span class="font-label-caps text-label-caps text-on-surface-variant">TOTAL PESANAN</span>
                </div>
                <div class="font-headline-lg text-headline-lg text-on-background font-bold">
                    {{ $totalPesananHarian }}
                </div>
            </div>
        </div>

        {{-- Transaction List --}}
        <div class="bg-surface-container-lowest border border-surface-variant rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] overflow-hidden">

            {{-- Desktop Table Header --}}
            <div class="hidden md:grid grid-cols-12 gap-4 p-4 border-b border-surface-variant bg-surface-container-low font-label-caps text-label-caps text-on-surface-variant">
                <div class="col-span-3">NO. PESANAN</div>
                <div class="col-span-3">PELANGGAN / MEJA</div>
                <div class="col-span-2">ITEM</div>
                <div class="col-span-2">TOTAL</div>
                <div class="col-span-1 text-center">STATUS</div>
                <div class="col-span-1 text-right">WAKTU</div>
            </div>

            {{-- List Items --}}
            <div class="divide-y divide-surface-variant">
                @forelse($orders ?? [] as $order)
                    @php
                        $customerName = $order->member?->name ?? 'Pelanggan Umum';
                        $initial      = strtoupper(substr($customerName, 0, 1));
                        $hasMember    = (bool) $order->member_id;
                        $avatarBg     = $hasMember ? 'bg-secondary-fixed text-on-secondary-fixed' : 'bg-surface-container text-on-surface-variant';
                        $itemCount    = $order->details?->count() ?? 0;
                        $orderType    = $order->order_type === 'dine_in'
                            ? 'Dine In (Meja ' . ($order->table_number ?? '-') . ')'
                            : 'Take Away (Antrian ' . ($order->queue_number ?? '-') . ')';
                        $orderTypeIcon = $order->order_type === 'dine_in' ? 'table_restaurant' : 'takeout_dining';

                        $statusConfig = match($order->status) {
                            'completed'  => ['label' => 'Selesai',     'class' => 'bg-secondary-fixed text-on-secondary-fixed',         'icon' => 'check_circle'],
                            'confirmed'  => ['label' => 'Dikonfirmasi','class' => 'bg-surface-container text-on-surface',               'icon' => 'thumb_up'],
                            'preparing'  => ['label' => 'Diproses',    'class' => 'bg-secondary-container text-on-secondary-container', 'icon' => 'skillet'],
                            'cancelled'  => ['label' => 'Dibatalkan',  'class' => 'bg-error-container text-on-error-container',         'icon' => 'cancel'],
                            default      => ['label' => 'Menunggu',    'class' => 'bg-surface-variant text-on-surface-variant',         'icon' => 'schedule'],
                        };
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 p-4 md:items-center hover:bg-surface-container-low transition-colors cursor-pointer group">

                        {{-- Mobile: No. Order + Status Badge --}}
                        <div class="flex justify-between items-start md:hidden mb-2">
                            <div class="font-body-lg text-body-lg font-bold text-on-background">#{{ $order->order_number }}</div>
                            <span class="{{ $statusConfig['class'] }} px-3 py-1 rounded-full font-label-caps text-label-caps flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">{{ $statusConfig['icon'] }}</span>
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>

                        {{-- Desktop: No. Order --}}
                        <div class="col-span-3 hidden md:block font-body-lg text-body-lg font-bold text-on-background group-hover:text-primary transition-colors">
                            #{{ $order->order_number }}
                        </div>

                        {{-- Customer --}}
                        <div class="col-span-3 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full {{ $avatarBg }} flex items-center justify-center font-bold shrink-0">{{ $initial }}</div>
                            <div>
                                <div class="font-body-md text-body-md font-semibold text-on-background">{{ $customerName }}</div>
                                <div class="text-on-surface-variant text-[12px] flex items-center gap-1">
                                    <span class="material-symbols-outlined text-[14px]">{{ $orderTypeIcon }}</span>
                                    {{ $orderType }}
                                </div>
                            </div>
                        </div>

                        {{-- Items Count --}}
                        <div class="col-span-2 text-on-surface-variant flex items-center gap-2 mt-2 md:mt-0">
                            <span class="material-symbols-outlined text-[16px] md:hidden">restaurant</span>
                            {{ $itemCount }} item
                        </div>

                        {{-- Total --}}
                        <div class="col-span-2 font-body-lg text-body-lg font-bold text-on-background flex justify-between md:block mt-2 md:mt-0">
                            <span class="md:hidden text-on-surface-variant font-normal text-body-md">Total:</span>
                            Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}
                        </div>

                        {{-- Status Badge (Desktop) --}}
                        <div class="col-span-1 text-center hidden md:block">
                            <span class="{{ $statusConfig['class'] }} px-3 py-1 rounded-full font-label-caps text-label-caps inline-flex items-center gap-1">
                                {{ $statusConfig['label'] }}
                            </span>
                        </div>

                        {{-- Time --}}
                        <div class="col-span-1 text-right text-on-surface-variant flex justify-between md:block mt-2 md:mt-0 border-t border-surface-variant pt-2 md:border-t-0 md:pt-0">
                            <span class="md:hidden">Waktu:</span>
                            {{ $order->created_at?->format('H:i') ?? '-' }}
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center">
                        <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">receipt_long</span>
                        <p class="text-on-surface-variant italic text-sm">Tidak ada pesanan untuk tanggal ini.</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination / Load More --}}
            @if(($orders ?? null) && method_exists($orders, 'links') && $orders->hasPages())
                <div class="p-4 bg-surface-container-low border-t border-surface-variant">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>

    {{-- ══════════════════════════════════════════════════════════════════════════
         MODE: BULANAN
    ══════════════════════════════════════════════════════════════════════════════ --}}
    @else

        {{-- Month Selector --}}
        <div>
            <label class="block font-label-caps text-label-caps text-tertiary mb-2 uppercase tracking-wider">Pilih Bulan</label>
            <div class="relative w-full md:w-64">
                <input wire:model.live="bulan" type="month"
                       class="w-full bg-surface-container-lowest border border-surface-variant rounded-xl px-4 py-3 font-body-lg text-body-lg text-on-background focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary cursor-pointer">
            </div>
        </div>

        {{-- Summary Cards --}}
        @php
            $bulanLabel = $bulan ? \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') : '-';
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-card-gap">
            {{-- Total Pendapatan --}}
            <div class="bg-surface-container-lowest border border-surface-variant rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] relative overflow-hidden group">
                <div class="absolute top-0 left-0 w-1 h-full bg-primary rounded-l-2xl"></div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary text-[18px]">payments</span>
                    </div>
                    <span class="font-body-md text-body-md text-on-surface-variant font-medium">Total Pendapatan</span>
                </div>
                <div class="font-headline-lg text-headline-lg text-primary mb-1">
                    Rp {{ number_format($this->totalRevenueBulan, 0, ',', '.') }}
                </div>
                <div class="font-body-md text-body-md text-tertiary">{{ $bulanLabel }}</div>
            </div>
            {{-- Total Transaksi --}}
            <div class="bg-surface-container-lowest border border-surface-variant rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] relative overflow-hidden">
                <div class="absolute top-0 left-0 w-1 h-full bg-secondary-container rounded-l-2xl"></div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-secondary-container/20 flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-secondary-container text-[18px]">receipt_long</span>
                    </div>
                    <span class="font-body-md text-body-md text-on-surface-variant font-medium">Total Transaksi</span>
                </div>
                <div class="font-headline-lg text-headline-lg text-on-background mb-1">
                    {{ $this->totalPesananBulan }}
                </div>
                <div class="font-body-md text-body-md text-tertiary">pesanan masuk</div>
            </div>
            {{-- Rata-rata Harian --}}
            <div class="bg-surface-container-lowest border border-surface-variant rounded-2xl p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] relative overflow-hidden">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-full bg-surface-variant flex items-center justify-center">
                        <span class="material-symbols-outlined text-on-surface-variant text-[18px]">monitoring</span>
                    </div>
                    <span class="font-body-md text-body-md text-on-surface-variant font-medium">Rata-rata Harian</span>
                </div>
                @php
                    $hariOperasional = count(array_filter($this->dailySummary, fn($d) => ($d['revenue'] ?? 0) > 0));
                    $rataRata = $hariOperasional > 0 ? $this->totalRevenueBulan / $hariOperasional : 0;
                @endphp
                <div class="font-headline-lg text-headline-lg text-on-background mb-1">
                    Rp {{ number_format($rataRata, 0, ',', '.') }}
                </div>
                <div class="font-body-md text-body-md text-tertiary">dari {{ $hariOperasional }} hari operasional</div>
            </div>
        </div>

        {{-- Daily Breakdown Table --}}
        <div class="bg-surface-container-lowest border border-surface-variant rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] overflow-hidden flex flex-col">
            <div class="bg-surface-container-low px-6 py-4 border-b border-surface-variant flex items-center gap-2">
                <span class="material-symbols-outlined text-on-surface-variant text-[20px]">list_alt</span>
                <h2 class="font-body-lg text-body-lg font-semibold text-on-background">Rincian Harian — {{ $bulanLabel }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse min-w-[600px]">
                    <thead>
                        <tr class="bg-surface-container-lowest border-b border-surface-variant">
                            <th class="px-6 py-4 font-label-caps text-label-caps text-tertiary font-bold tracking-wider uppercase">Tanggal</th>
                            <th class="px-6 py-4 font-label-caps text-label-caps text-tertiary font-bold tracking-wider uppercase text-right">Pesanan</th>
                            <th class="px-6 py-4 font-label-caps text-label-caps text-tertiary font-bold tracking-wider uppercase text-right">Terbayar</th>
                            <th class="px-6 py-4 font-label-caps text-label-caps text-tertiary font-bold tracking-wider uppercase text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="font-body-md text-body-md text-on-background divide-y divide-surface-variant/50">
                        @forelse($this->dailySummary as $row)
                            @php
                                $tgl = \Carbon\Carbon::parse($row['tanggal'])->translatedFormat('d F Y, l');
                                $revenue = (float) ($row['revenue'] ?? 0);
                            @endphp
                            <tr class="hover:bg-surface-container-lowest/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $tgl }}</td>
                                <td class="px-6 py-4 text-right">{{ $row['total_pesanan'] }}</td>
                                <td class="px-6 py-4 text-right">{{ $row['terbayar'] ?? '-' }}</td>
                                <td class="px-6 py-4 text-right {{ $revenue > 0 ? 'font-semibold text-primary' : 'text-tertiary' }}">
                                    Rp {{ number_format($revenue, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-on-surface-variant italic text-sm">
                                    Belum ada data untuk bulan ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-surface-variant border-t-2 border-surface-variant">
                            <td class="px-6 py-4 font-headline-md text-headline-md font-bold text-on-background whitespace-nowrap">Total Bulan Ini</td>
                            <td class="px-6 py-4 text-right font-headline-md text-headline-md font-bold text-on-background">{{ $this->totalPesananBulan }}</td>
                            <td class="px-6 py-4 text-right font-headline-md text-headline-md font-bold text-on-background">—</td>
                            <td class="px-6 py-4 text-right font-headline-md text-headline-md font-bold text-primary">
                                Rp {{ number_format($this->totalRevenueBulan, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    @endif

</div>
