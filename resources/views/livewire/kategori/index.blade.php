<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-surface text-on-surface font-body-md antialiased selection:bg-primary selection:text-on-primary">

    {{-- ── Page Header ─────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface mb-1">Kategori Menu</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Kelola kategori untuk mengorganisir menu</p>
        </div>
        <button wire:click="openCreate()"
                class="bg-primary hover:bg-surface-tint text-on-primary font-body-lg text-body-lg py-2.5 px-5 rounded-lg shadow-[0_2px_10px_-3px_rgba(188,0,10,0.4)] transition-all duration-300 active:scale-95 hover:-translate-y-0.5 hover:shadow-lg flex items-center gap-2 whitespace-nowrap w-full sm:w-auto justify-center">
            <span class="material-symbols-outlined text-xl">add</span>
            Tambah Kategori
        </button>
    </div>

    {{-- ── Filters & Search ────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-4">
        <div class="relative w-full sm:max-w-md">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <span class="material-symbols-outlined text-on-surface-variant">search</span>
            </div>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Cari kategori..."
                   class="block w-full pl-10 pr-3 py-2.5 bg-surface-container-lowest border border-surface-variant rounded-lg text-body-md font-body-md focus:ring-2 focus:ring-secondary focus:border-secondary transition-shadow">
        </div>
        <div class="relative w-full sm:w-48 shrink-0">
            <select wire:model.live="statusFilter"
                    class="block w-full pl-3 pr-10 py-2.5 bg-surface-container-lowest border border-surface-variant rounded-lg text-body-md font-body-md text-on-surface focus:ring-2 focus:ring-secondary focus:border-secondary appearance-none transition-shadow">
                <option value="">Semua Status</option>
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                <span class="material-symbols-outlined text-on-surface-variant">unfold_more</span>
            </div>
        </div>
    </div>

    {{-- ── Data Table Card ─────────────────────────────────────────── --}}
    <div class="bg-surface-container-lowest rounded-xl border border-surface-variant shadow-[0_4px_24px_-8px_rgba(0,0,0,0.05)] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-surface-variant bg-surface-container-lowest text-on-surface-variant">
                        <th class="py-4 px-6 font-label-caps text-label-caps uppercase tracking-wider whitespace-nowrap">
                            Nama
                            <button wire:click="sortBy('name')" class="align-middle ml-1">
                                <span class="material-symbols-outlined text-sm">{{ ($sortField ?? '') === 'name' ? (($sortDirection ?? 'asc') === 'asc' ? 'keyboard_arrow_up' : 'keyboard_arrow_down') : 'unfold_more' }}</span>
                            </button>
                        </th>
                        <th class="py-4 px-6 font-label-caps text-label-caps uppercase tracking-wider whitespace-nowrap text-center">Jml Menu</th>
                        <th class="py-4 px-6 font-label-caps text-label-caps uppercase tracking-wider whitespace-nowrap">Status</th>
                        <th class="py-4 px-6 font-label-caps text-label-caps uppercase tracking-wider whitespace-nowrap text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-surface-variant/50">
                    @forelse($categories ?? [] as $cat)
                        <tr class="hover:bg-surface-container-low transition-all duration-200 group">
                            <td class="py-4 px-6">
                                <span class="font-body-lg text-body-lg text-on-surface">{{ $cat->name }}</span>
                            </td>
                            <td class="py-4 px-6 text-center">
                                <span class="font-body-md text-body-md font-semibold text-on-surface">{{ $cat->menu_items_count ?? 0 }}</span>
                            </td>
                            <td class="py-4 px-6">
                                @if($cat->is_active)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-secondary-fixed text-on-secondary-fixed font-label-caps text-label-caps">Aktif</span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-surface-variant text-on-surface-variant font-label-caps text-label-caps">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex justify-end gap-2">
                                    <button wire:click="openEdit({{ $cat->id }})"
                                            class="p-2 text-on-surface-variant hover:text-secondary hover:bg-secondary-container/20 rounded-lg transition-all duration-200 hover:scale-110" title="Edit">
                                        <span class="material-symbols-outlined text-xl">edit</span>
                                    </button>
                                    <button wire:click="delete({{ $cat->id }})"
                                            wire:confirm="Hapus kategori '{{ $cat->name }}'? Semua menu di kategori ini akan kehilangan kategorinya."
                                            class="p-2 text-on-surface-variant hover:text-error hover:bg-error-container/50 rounded-lg transition-all duration-200 hover:scale-110" title="Hapus">
                                        <span class="material-symbols-outlined text-xl">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-16 text-center">
                                <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">category</span>
                                <p class="text-on-surface-variant italic text-sm">Belum ada kategori. Tambahkan kategori pertama Anda.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        <div class="bg-surface-container-lowest px-6 py-4 border-t border-surface-variant flex items-center justify-between">
            <span class="font-body-md text-body-md text-on-surface-variant">
                Menampilkan {{ ($categories ?? collect())->count() }} kategori
            </span>
            @if(($categories ?? null) && method_exists($categories, 'links'))
                <div>{{ $categories->links() }}</div>
            @endif
        </div>
    </div>

    {{-- ── Form Modal ────────────────────────────────────────────────── --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-data x-on:keydown.escape.window="$wire.set('showModal', false)">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-md overflow-hidden"
                 @click.away="$wire.set('showModal', false)">
                <div class="px-6 py-4 border-b border-surface-variant flex justify-between items-center bg-surface">
                    <h2 class="font-headline-md text-headline-md font-bold text-on-surface">{{ $editId ? 'Edit Kategori' : 'Tambah Kategori' }}</h2>
                    <button wire:click="$set('showModal', false)" class="text-on-surface-variant hover:text-on-surface rounded-full p-1 transition-colors">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>
                <form wire:submit.prevent="save" class="p-6">
                    <div class="flex flex-col gap-4 mb-6">
                        <div>
                            <label class="font-body-md text-body-md font-medium text-on-surface mb-2 block" for="name">Nama Kategori</label>
                            <input wire:model="name" id="name" type="text" class="w-full px-4 py-2 bg-surface-container-low border border-surface-variant rounded-lg focus:ring-2 focus:ring-primary focus:border-primary text-on-surface">
                            @error('name') <span class="text-error text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="font-body-md text-body-md font-medium text-on-surface">Status Aktif</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="is_active" type="checkbox" class="sr-only peer">
                                <div class="w-11 h-6 bg-surface-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                            </label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t border-surface-variant">
                        <button type="button" wire:click="$set('showModal', false)" class="px-5 py-2 font-body-md text-body-md font-medium text-on-surface-variant hover:bg-surface-container rounded-lg transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2 font-body-md text-body-md font-bold bg-primary text-on-primary hover:bg-surface-tint rounded-lg transition-colors flex items-center gap-2">
                            <span wire:loading wire:target="save" class="material-symbols-outlined animate-spin text-[18px]">sync</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
