<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-surface text-on-surface font-body-md antialiased">

    @php
        // Akumulasi dari SELURUH stok (bukan hanya halaman aktif)
        $totalBahan = \App\Models\StockIngredient::count();
        $stokRendah = \App\Models\StockIngredient::whereColumn('current_stock', '<', 'minimum_stock')->count();
        $stokAman   = $totalBahan - $stokRendah;
    @endphp

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg font-bold text-on-surface mb-1 flex items-center gap-2">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">inventory_2</span>
                Stok Bahan Baku
            </h1>
            <p class="text-body-md font-body-md text-on-surface-variant">Pantau dan kelola persediaan bahan baku dapur</p>
        </div>
        <button wire:click="openCreate()"
                class="bg-primary-container text-on-primary-container hover:opacity-90 active:scale-95 transition-all px-5 py-2.5 rounded-lg flex items-center gap-2 shadow-sm font-label-caps font-bold w-full md:w-auto justify-center">
            <span class="material-symbols-outlined text-sm">add</span>
            Tambah Bahan
        </button>
    </div>

    {{-- ── Summary Cards ────────────────────────────────────────────── --}}
    <div class="flex overflow-x-auto hide-scrollbar gap-4 pb-2 md:grid md:grid-cols-3 md:overflow-visible md:pb-0 snap-x">
        {{-- Total --}}
        <div class="min-w-[240px] md:min-w-0 bg-surface-container-lowest border border-outline-variant rounded-xl p-5 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform snap-center flex-shrink-0">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-label-caps font-label-caps text-on-surface-variant mb-1 uppercase tracking-wider">Total Bahan</p>
                    <p class="text-headline-lg font-headline-lg font-bold text-on-surface">{{ $totalBahan }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-secondary-container/20 flex items-center justify-center text-secondary-container">
                    <span class="material-symbols-outlined text-2xl">package</span>
                </div>
            </div>
        </div>
        {{-- Stok Aman --}}
        <div class="min-w-[240px] md:min-w-0 bg-surface-container-lowest border-t-4 border-t-secondary-container border border-outline-variant rounded-xl p-5 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] hover:-translate-y-1 transition-transform snap-center flex-shrink-0 relative overflow-hidden">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-secondary-container/5 rounded-full blur-xl"></div>
            <div class="flex items-start justify-between relative z-10">
                <div>
                    <p class="text-label-caps font-label-caps text-on-surface-variant mb-1 uppercase tracking-wider">Stok Aman</p>
                    <p class="text-headline-lg font-headline-lg font-bold text-on-surface">{{ $stokAman }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-secondary-container flex items-center justify-center text-on-secondary-container shadow-sm">
                    <span class="material-symbols-outlined text-2xl">check_circle</span>
                </div>
            </div>
        </div>
        {{-- Stok Rendah --}}
        <div class="min-w-[240px] md:min-w-0 bg-error-container/20 border border-primary-container rounded-xl p-5 shadow-[0_4px_20px_-4px_rgba(230,25,25,0.1)] hover:-translate-y-1 transition-transform snap-center flex-shrink-0 {{ $stokRendah > 0 ? 'animate-[pulse-border_2s_infinite]' : '' }}">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-label-caps font-label-caps text-primary-container mb-1 uppercase tracking-wider font-bold">Stok Rendah</p>
                    <p class="text-headline-lg font-headline-lg font-bold text-primary-container">{{ $stokRendah }}</p>
                </div>
                <div class="w-12 h-12 rounded-full bg-primary-container flex items-center justify-center text-on-primary-container shadow-sm">
                    <span class="material-symbols-outlined text-2xl">warning</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Filters ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari bahan baku..."
                   class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md focus:outline-none focus:ring-2 focus:ring-secondary-container transition-all">
        </div>
        <div class="relative min-w-[160px]">
            <select wire:model.live="filterStatus"
                    class="w-full pl-4 pr-10 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md appearance-none focus:outline-none focus:ring-2 focus:ring-secondary-container transition-all">
                <option value="">Semua Status</option>
                <option value="ok">Stok Aman</option>
                <option value="low">Stok Rendah</option>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
        </div>
    </div>

    {{-- ── Data List ───────────────────────────────────────────────── --}}
    <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] overflow-hidden">
        {{-- Desktop Header --}}
        <div class="hidden md:grid grid-cols-12 gap-4 p-4 border-b border-outline-variant bg-surface-container/50 text-label-caps font-label-caps text-on-surface-variant uppercase tracking-wider">
            <div class="col-span-3">Nama Bahan</div>
            <div class="col-span-3 text-center">Stok Saat Ini</div>
            <div class="col-span-2 text-center">Status</div>
            <div class="col-span-4 text-right">Aksi</div>
        </div>

        <div class="flex flex-col divide-y divide-outline-variant">
            @forelse($ingredients ?? [] as $item)
                @php
                    $isRendah = $item->current_stock < $item->minimum_stock;
                    $pct = $item->minimum_stock > 0
                        ? min(100, round(($item->current_stock / ($item->minimum_stock * 2)) * 100))
                        : 100;
                @endphp
                <div class="p-4 flex flex-col md:grid md:grid-cols-12 md:items-center gap-4 hover:bg-surface-container-lowest/50 transition-colors">
                    <div class="flex items-center space-x-3 md:col-span-3">
                        <div class="w-10 h-10 rounded-lg bg-surface-container flex items-center justify-center border border-outline-variant text-lg">
                            {{ $isRendah ? '⚠️' : '📦' }}
                        </div>
                        <div>
                            <h3 class="text-body-lg font-body-lg font-bold flex items-center gap-2">
                                {{ $item->name }}
                                @if($isRendah)
                                    <span class="material-symbols-outlined text-primary-container text-sm" title="Stok Rendah">error</span>
                                @endif
                            </h3>
                            <p class="text-label-caps text-on-surface-variant md:hidden">Min: {{ number_format($item->minimum_stock, 2) }} {{ $item->unit }}</p>
                        </div>
                    </div>

                    <div class="flex flex-col md:col-span-3 md:items-center">
                        <div class="flex justify-between md:justify-center items-end w-full mb-1">
                            <span class="text-label-caps text-on-surface-variant md:hidden">Stok:</span>
                            <span class="text-body-md font-bold {{ $isRendah ? 'text-primary-container' : 'text-on-surface' }}">
                                {{ number_format($item->current_stock, 2) }} {{ $item->unit }}
                            </span>
                        </div>
                        <div class="w-full md:w-3/4 h-1.5 bg-surface-variant rounded-full overflow-hidden">
                            <div class="h-full {{ $isRendah ? 'bg-primary-container' : 'bg-secondary-container' }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    <div class="md:col-span-2 flex justify-start md:justify-center">
                        @if($isRendah)
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-primary-container text-on-primary-container text-label-caps font-bold shadow-sm">Stok Rendah</span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-surface text-on-surface border border-secondary-container text-label-caps font-bold">
                                <span class="w-2 h-2 rounded-full bg-secondary-container mr-1.5"></span> Aman
                            </span>
                        @endif
                    </div>

                    <div class="flex items-center justify-end space-x-2 md:col-span-4 mt-2 md:mt-0 pt-3 md:pt-0 border-t border-outline-variant border-dashed md:border-none">
                        <button wire:click="openAdjust({{ $item->id }})"
                                class="flex-1 md:flex-none flex items-center justify-center gap-1 px-3 py-1.5 {{ $isRendah ? 'border border-primary-container text-primary-container hover:bg-primary-container/10' : 'border border-outline-variant text-on-surface hover:border-secondary-container hover:text-secondary-container' }} rounded-lg text-label-caps font-bold transition-colors">
                            <span class="material-symbols-outlined text-sm">add</span> Restock
                        </button>
                        <button wire:click="openEdit({{ $item->id }})"
                                class="p-2 text-on-surface-variant hover:text-secondary-container hover:bg-surface-variant rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-sm">edit</span>
                        </button>
                        <button wire:click="delete({{ $item->id }})"
                                wire:confirm="Hapus bahan '{{ $item->name }}'?"
                                class="p-2 text-on-surface-variant hover:text-primary-container hover:bg-error-container rounded-lg transition-colors">
                            <span class="material-symbols-outlined text-sm">delete</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">inventory_2</span>
                    <p class="text-on-surface-variant italic text-sm">Belum ada bahan baku terdaftar.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if(($ingredients ?? null) && method_exists($ingredients, 'links'))
            <div class="border-t border-outline-variant px-4 py-4">{{ $ingredients->links() }}</div>
        @endif
    </div>

    {{-- ── Modals ────────────────────────────────────────────────────── --}}

    {{-- Modal Create / Edit --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-data x-on:keydown.escape.window="$wire.set('showForm', false)">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col max-h-[90vh]"
                 @click.away="$wire.set('showForm', false)">
                <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center bg-surface shrink-0">
                    <h2 class="font-headline-md text-headline-md font-bold text-on-surface">{{ $editingId ? 'Edit Bahan' : 'Tambah Bahan Baru' }}</h2>
                    <button wire:click="$set('showForm', false)" class="text-on-surface-variant hover:text-on-surface rounded-full p-1 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form wire:submit.prevent="save" class="flex flex-col overflow-y-auto">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="font-body-md text-body-md font-medium text-on-surface mb-1 block">Nama Bahan <span class="text-error">*</span></label>
                            <input wire:model="name" type="text" placeholder="Contoh: Beras" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-on-surface">
                            @error('name') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="font-body-md text-body-md font-medium text-on-surface mb-1 block">Satuan <span class="text-error">*</span></label>
                                <input wire:model="unit" type="text" placeholder="Contoh: Kg, Liter, Pcs" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                                @error('unit') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="font-body-md text-body-md font-medium text-on-surface mb-1 block">Harga Satuan (Rp) <span class="text-error">*</span></label>
                                <input wire:model="unit_cost" type="number" min="0" step="100" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                                @error('unit_cost') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="font-body-md text-body-md font-medium text-on-surface mb-1 block">Stok Saat Ini <span class="text-error">*</span></label>
                                <input wire:model="current_stock" type="number" step="0.01" min="0" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                                @error('current_stock') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="font-body-md text-body-md font-medium text-on-surface mb-1 block">Stok Minimum <span class="text-error">*</span></label>
                                <input wire:model="minimum_stock" type="number" step="0.01" min="0" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                                @error('minimum_stock') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-surface-variant flex justify-end gap-3 shrink-0 bg-surface-container-lowest">
                        <button type="button" wire:click="$set('showForm', false)" class="px-5 py-2 font-body-md font-medium text-on-surface-variant hover:bg-surface-container rounded-lg">Batal</button>
                        <button type="submit" class="px-5 py-2 font-body-md font-bold bg-primary text-on-primary hover:bg-surface-tint rounded-lg flex items-center gap-2">
                            <span wire:loading wire:target="save" class="material-symbols-outlined animate-spin text-[18px]">sync</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Modal Adjust Stok --}}
    @if($showAdjustModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-data x-on:keydown.escape.window="$wire.set('showAdjustModal', false)">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-sm overflow-hidden"
                 @click.away="$wire.set('showAdjustModal', false)">
                <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center bg-surface">
                    <h2 class="font-headline-md font-bold text-on-surface">Restock / Penyesuaian</h2>
                    <button wire:click="$set('showAdjustModal', false)" class="text-on-surface-variant hover:text-on-surface rounded-full p-1">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form wire:submit.prevent="applyAdjustment" class="p-6">
                    <div class="mb-4">
                        <label class="font-body-md font-medium text-on-surface mb-2 block">Kuantitas Ditambahkan</label>
                        <div class="flex items-center gap-2">
                            <button type="button" wire:click="$set('adjustQty', $wire.adjustQty - 1)" class="w-10 h-10 rounded-lg bg-surface-container hover:bg-surface-variant flex items-center justify-center text-on-surface">-</button>
                            <input wire:model="adjustQty" type="number" step="0.01" class="flex-1 px-4 py-2 text-center bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface font-bold">
                            <button type="button" wire:click="$set('adjustQty', $wire.adjustQty + 1)" class="w-10 h-10 rounded-lg bg-surface-container hover:bg-surface-variant flex items-center justify-center text-on-surface">+</button>
                        </div>
                        <p class="text-xs text-on-surface-variant mt-2 text-center">Bisa menggunakan angka negatif untuk mengurangi stok.</p>
                        @error('adjustQty') <span class="text-error text-sm mt-1 block text-center">{{ $message }}</span> @enderror
                    </div>
                    <div class="mb-6">
                        <label class="font-body-md font-medium text-on-surface mb-1 block">Catatan (opsional)</label>
                        <input wire:model="adjustNote" type="text" placeholder="Misal: Pembelian baru" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                    </div>
                    <button type="submit" class="w-full py-2 font-body-md font-bold bg-primary text-on-primary hover:bg-surface-tint rounded-lg flex items-center justify-center gap-2">
                        <span wire:loading wire:target="applyAdjustment" class="material-symbols-outlined animate-spin text-[18px]">sync</span>
                        Terapkan Stok
                    </button>
                </form>
            </div>
        </div>
    @endif

</div>

<style>
@keyframes pulse-border {
    0%   { box-shadow: 0 0 0 0 rgba(230,25,25,0.4); }
    70%  { box-shadow: 0 0 0 6px rgba(230,25,25,0); }
    100% { box-shadow: 0 0 0 0 rgba(230,25,25,0); }
}
</style>
