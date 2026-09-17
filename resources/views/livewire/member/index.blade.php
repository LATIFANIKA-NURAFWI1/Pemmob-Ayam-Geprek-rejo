<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-surface text-on-surface font-body-md antialiased">

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div>
        <h1 class="font-headline-md text-headline-md text-on-surface mb-2">Manajemen Member (CRM)</h1>
        <p class="text-on-surface-variant">Pantau loyalitas, poin, dan riwayat pembelian pelanggan</p>
    </div>

    {{-- ── Search Bar ───────────────────────────────────────────────── --}}
    <div class="relative w-full md:w-1/2">
        <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
        <input wire:model.live.debounce.300ms="search" type="text"
               placeholder="Cari nama / no. HP..."
               class="w-full bg-surface-container-low border-none rounded-full py-3 pl-12 pr-4 focus:ring-2 focus:ring-primary text-on-surface placeholder:text-on-surface-variant transition-all">
    </div>

    {{-- ── Member List ─────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-card-gap">
        @forelse($members ?? [] as $member)
            @php
                $initial = strtoupper(substr($member->name, 0, 1));
                $colors  = ['bg-secondary-container text-on-secondary-container', 'bg-primary-container text-on-primary-container'];
                $color   = $colors[$loop->index % 2];
            @endphp
            <div wire:click="viewMember({{ $member->id }})"
                 class="bg-surface-container-lowest rounded-xl p-4 shadow-sm border border-surface-variant flex flex-col md:flex-row md:items-center justify-between gap-4 cursor-pointer hover:bg-surface-container-low transition-colors">

                {{-- Member Info --}}
                <div class="flex items-center gap-4 md:w-1/3">
                    <div class="w-12 h-12 rounded-full {{ $color }} font-headline-md text-headline-md flex items-center justify-center shrink-0 font-bold">
                        {{ $initial }}
                    </div>
                    <div>
                        <h3 class="font-body-lg text-body-lg text-on-surface font-bold">{{ $member->name }}</h3>
                        <div class="flex items-center text-on-surface-variant text-sm mt-1">
                            <span class="material-symbols-outlined text-[16px] mr-1">smartphone</span>
                            {{ $member->phone }}
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-2 md:flex md:w-2/3 justify-between items-center gap-4 text-center md:text-right">
                    <div class="flex flex-col md:items-end">
                        <span class="text-xs text-on-surface-variant uppercase tracking-wider mb-1 md:hidden">Poin</span>
                        <span class="font-bold text-secondary-fixed-dim text-lg">{{ number_format($member->points ?? 0, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex flex-col md:items-end">
                        <span class="text-xs text-on-surface-variant uppercase tracking-wider mb-1 md:hidden">Total Belanja</span>
                        <span class="font-bold text-primary text-lg">
                            Rp {{ number_format($member->total_spent ?? 0, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex flex-col md:items-center">
                        <span class="text-xs text-on-surface-variant uppercase tracking-wider mb-1 md:hidden">Status</span>
                        <span class="inline-block px-3 py-1 rounded-full border border-secondary-fixed-dim text-secondary-fixed-dim font-label-caps text-label-caps bg-surface-container-lowest">
                            Aktif
                        </span>
                    </div>
                    <div class="flex justify-end items-center md:justify-center">
                        <button class="text-primary hover:bg-primary-container hover:text-on-primary-container p-2 rounded-full transition-colors flex items-center justify-center">
                            <span class="material-symbols-outlined">visibility</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="py-16 text-center border-2 border-dashed border-surface-variant rounded-xl">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">group</span>
                <p class="text-on-surface-variant italic text-sm">Belum ada member terdaftar.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if(($members ?? null) && method_exists($members, 'links'))
        <div>{{ $members->links() }}</div>
    @endif

    {{-- ── Member Detail Drawer (Slide-in) ──────────────────────────── --}}
    @php $m = $this->viewingMember ?? null; @endphp
    @if($m)
        <div class="fixed inset-0 z-[100]" x-data @click.self="$wire.closeDetail()">
            {{-- Scrim --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="closeDetail()"></div>

            {{-- Panel --}}
            <div class="absolute right-0 top-0 h-full w-full max-w-md bg-surface-container-lowest shadow-2xl overflow-y-auto flex flex-col">
                {{-- Header --}}
                <div class="relative pt-12 pb-6 px-6 bg-surface">
                    <div class="absolute top-0 left-0 w-full h-2 bg-primary"></div>
                    <button wire:click="closeDetail()"
                            class="absolute top-4 right-4 p-2 rounded-full bg-surface-container hover:bg-surface-variant text-on-surface transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-16 h-16 rounded-full bg-secondary-container text-on-secondary-container font-headline-lg text-headline-lg font-bold flex items-center justify-center shrink-0">
                            {{ strtoupper(substr($m->name, 0, 1)) }}
                        </div>
                        <div>
                            <h2 class="font-headline-md text-headline-md font-bold text-on-surface">{{ $m->name }}</h2>
                            <div class="flex items-center text-on-surface-variant text-sm mt-1">
                                <span class="material-symbols-outlined text-[16px] mr-1">smartphone</span>
                                {{ $m->phone }}
                            </div>
                            <div class="text-xs text-on-surface-variant mt-1">
                                Member sejak {{ $m->created_at?->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-surface-container-lowest border border-surface-variant rounded-xl p-3 text-center">
                            <span class="text-xl font-bold text-secondary-fixed-dim block">{{ number_format($m->points ?? 0) }}</span>
                            <span class="text-xs text-on-surface-variant uppercase mt-1 block">Poin</span>
                        </div>
                        <div class="bg-surface-container-lowest border border-surface-variant rounded-xl p-3 text-center">
                            <span class="text-xl font-bold text-on-surface block">{{ $m->total_orders ?? 0 }}</span>
                            <span class="text-xs text-on-surface-variant uppercase mt-1 block">Pesanan</span>
                        </div>
                        <div class="bg-surface-container-lowest border border-surface-variant rounded-xl p-3 text-center">
                            <span class="text-xl font-bold text-primary block">
                                {{ number_format(($m->total_spent ?? 0) / 1000) }}K
                            </span>
                            <span class="text-xs text-on-surface-variant uppercase mt-1 block text-center">Total</span>
                        </div>
                    </div>
                </div>

                {{-- History --}}
                <div class="p-6 flex-1">
                    <div class="mb-8">
                        <h3 class="font-label-caps text-label-caps text-on-surface-variant mb-4 border-b border-surface-variant pb-2">10 Pesanan Terakhir</h3>
                        @forelse($m->orders ?? [] as $order)
                            <div class="flex justify-between py-2 text-sm">
                                <span class="text-on-surface">#{{ $order->order_number ?? $order->id }}</span>
                                <span class="text-primary font-semibold">Rp {{ number_format($order->total_amount ?? 0, 0, ',', '.') }}</span>
                            </div>
                        @empty
                            <div class="text-center py-8 text-on-surface-variant italic text-sm">Belum ada pesanan</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
