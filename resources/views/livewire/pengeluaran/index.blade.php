<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-surface text-on-surface font-body-md antialiased">

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-headline-lg font-headline-lg text-on-surface">Manajemen Pengeluaran</h1>
            <p class="text-body-md font-body-md text-on-surface-variant mt-1">Catat dan pantau semua biaya operasional warung</p>
        </div>
        <button wire:click="openCreate()"
                class="bg-primary hover:bg-on-error-container text-on-primary px-6 py-3 rounded-lg flex items-center gap-2 transition-all duration-300 shadow-[0_2px_8px_rgba(188,0,10,0.2)] active:scale-95 hover:-translate-y-0.5 hover:shadow-lg w-full md:w-auto justify-center">
            <span class="material-symbols-outlined">add</span>
            <span class="font-body-lg font-semibold">Catat Pengeluaran</span>
        </button>
    </div>

    {{-- ── Summary Cards Bento Grid ────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-card-gap">
        {{-- Total Card (Hero) --}}
        <div class="col-span-1 md:col-span-2 lg:col-span-1 bg-surface-container-lowest rounded-xl p-5 border border-outline-variant shadow-[0_8px_24px_rgba(0,0,0,0.04)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-1 bg-primary"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-body-md font-body-md text-on-surface-variant font-medium">Total Bulan Ini</p>
                    <h2 class="text-[28px] font-bold font-headline-lg text-primary mt-2">
                        Rp {{ number_format($totalBulanIni ?? 0, 0, ',', '.') }}
                    </h2>
                </div>
                <div class="w-12 h-12 rounded-full bg-error-container flex items-center justify-center text-primary group-hover:scale-110 transition-transform duration-300">
                    <span class="material-symbols-outlined" style="font-variation-settings:'FILL' 1">account_balance_wallet</span>
                </div>
            </div>
        </div>

        {{-- Category Breakdown Cards --}}
        @php
            $categories = [
                ['icon' => 'inventory_2',   'label' => 'Bahan Baku',  'key' => 'bahan_baku'],
                ['icon' => 'storefront',    'label' => 'Operasional', 'key' => 'operasional'],
                ['icon' => 'group',         'label' => 'Gaji',        'key' => 'gaji'],
                ['icon' => 'build',         'label' => 'Perawatan',   'key' => 'perawatan'],
                ['icon' => 'more_horiz',    'label' => 'Lainnya',     'key' => 'lainnya'],
            ];
        @endphp
        @foreach($categories as $cat)
            <div class="bg-surface-container-lowest rounded-xl p-5 border border-surface-variant shadow-[0_2px_8px_rgba(0,0,0,0.02)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center gap-3 mb-3">
                    <span class="material-symbols-outlined text-secondary-fixed-dim">{{ $cat['icon'] }}</span>
                    <p class="text-body-md font-body-md text-on-surface-variant font-medium">{{ $cat['label'] }}</p>
                </div>
                <p class="text-headline-md font-headline-md text-on-surface">
                    Rp {{ number_format($this->categoryTotals[$cat['key']] ?? 0, 0, ',', '.') }}
                </p>
            </div>
        @endforeach
    </div>

    {{-- ── Filters ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row gap-4">
        <div class="relative flex-1 max-w-xs">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">calendar_today</span>
            <input wire:model.live="bulan" type="month"
                   class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-surface-variant rounded-lg text-body-md focus:border-secondary-fixed-dim focus:ring-1 focus:ring-secondary-fixed-dim outline-none transition-all">
        </div>
        <div class="relative flex-1 max-w-xs">
            <select wire:model.live="filterKategori"
                    class="w-full pl-4 pr-10 py-2.5 bg-surface-container-lowest border border-surface-variant rounded-lg text-body-md appearance-none focus:border-secondary-fixed-dim focus:ring-1 focus:ring-secondary-fixed-dim outline-none transition-all">
                <option value="">Semua Kategori</option>
                <option value="bahan_baku">Bahan Baku</option>
                <option value="operasional">Operasional</option>
                <option value="gaji">Gaji</option>
                <option value="perawatan">Perawatan</option>
                <option value="lainnya">Lainnya</option>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
        </div>
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari deskripsi..."
                   class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-surface-variant rounded-lg text-body-md focus:border-secondary-fixed-dim focus:ring-1 focus:ring-secondary-fixed-dim outline-none transition-all">
        </div>
    </div>

    {{-- ── Data Table ──────────────────────────────────────────────── --}}
    <div class="bg-surface-container-lowest border border-surface-variant rounded-xl overflow-hidden shadow-[0_2px_8px_rgba(0,0,0,0.02)]">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-surface-variant bg-surface-container-low text-label-caps font-label-caps text-on-surface-variant">
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider">Deskripsi</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-right">Jumlah</th>
                        <th class="px-6 py-4 font-semibold uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-body-md font-body-md">
                    @forelse($expenses ?? [] as $expense)
                        <tr class="border-b border-surface-variant hover:bg-surface transition-colors group">
                            <td class="px-6 py-4 text-on-surface">{{ \Carbon\Carbon::parse($expense->expense_date)->format('d M Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full border border-secondary-fixed-dim text-secondary-fixed-dim text-label-caps font-label-caps bg-surface-container-lowest">
                                    {{ ucwords(str_replace('_', ' ', $expense->category ?? 'Lainnya')) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-on-surface max-w-[200px] truncate">{{ $expense->description }}</td>
                            <td class="px-6 py-4 text-right text-primary font-bold">Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <button wire:click="openEdit({{ $expense->id }})"
                                            class="w-8 h-8 rounded-full flex items-center justify-center text-on-surface-variant hover:text-secondary-fixed-dim hover:bg-secondary-fixed/20 transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </button>
                                    <button x-on:click="if(confirm('Hapus pengeluaran ini?')) { $wire.confirmDelete({{ $expense->id }}); $wire.delete(); }"
                                            class="w-8 h-8 rounded-full flex items-center justify-center text-on-surface-variant hover:text-primary hover:bg-error-container transition-colors" title="Hapus">
                                        <span class="material-symbols-outlined text-[20px]">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">payments</span>
                                <p class="text-on-surface-variant italic text-sm">Belum ada data pengeluaran bulan ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        <div class="border-t border-surface-variant bg-surface-container-lowest px-6 py-4 flex items-center justify-between">
            <p class="text-body-md text-on-surface-variant">
                Menampilkan {{ ($expenses ?? collect())->count() }} entri
            </p>
            @if(($expenses ?? null) && method_exists($expenses, 'links'))
                <div>{{ $expenses->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ── Form Modal ────────────────────────────────────────────────── --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-data x-on:keydown.escape.window="$wire.closeForm()">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col"
                 @click.away="$wire.closeForm()">
                <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center bg-surface shrink-0">
                    <h2 class="font-headline-md text-headline-md font-bold text-on-surface">{{ $editingId ? 'Edit Pengeluaran' : 'Catat Pengeluaran Baru' }}</h2>
                    <button wire:click="closeForm()" class="text-on-surface-variant hover:text-on-surface rounded-full p-1 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form wire:submit.prevent="saveExpense" class="flex flex-col">
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="font-body-md font-medium text-on-surface mb-1 block">Tanggal</label>
                            <input wire:model="formDate" type="date" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                            @error('formDate') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-body-md font-medium text-on-surface mb-1 block">Kategori</label>
                            <select wire:model="formCategory" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                                <option value="bahan_baku">Bahan Baku</option>
                                <option value="operasional">Operasional</option>
                                <option value="gaji">Gaji</option>
                                <option value="perawatan">Perawatan</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                            @error('formCategory') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-body-md font-medium text-on-surface mb-1 block">Deskripsi</label>
                            <input wire:model="formDescription" type="text" placeholder="Misal: Beli gas elpiji" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                            @error('formDescription') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="font-body-md font-medium text-on-surface mb-1 block">Jumlah (Rp)</label>
                            <input wire:model="formAmount" type="number" min="0" step="500" placeholder="0" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                            @error('formAmount') <span class="text-error text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="px-6 py-4 border-t border-surface-variant flex justify-end gap-3 shrink-0 bg-surface-container-lowest">
                        <button type="button" wire:click="closeForm()" class="px-5 py-2 font-body-md font-medium text-on-surface-variant hover:bg-surface-container rounded-lg">Batal</button>
                        <button type="submit" class="px-5 py-2 font-body-md font-bold bg-primary text-on-primary hover:bg-surface-tint rounded-lg flex items-center gap-2">
                            <span wire:loading wire:target="saveExpense" class="material-symbols-outlined animate-spin text-[18px]">sync</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
