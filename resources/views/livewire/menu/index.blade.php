<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-background text-on-background font-body-md antialiased">

    {{-- ── Flash Status ─────────────────────────────────────────────────── --}}
    @if(session('status'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="bg-secondary-container/20 border border-secondary-container text-on-secondary-container px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">check_circle</span>
            {{ session('status') }}
        </div>
    @endif

    {{-- ── Header ───────────────────────────────────────────────────── --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface mb-1">Menu Makanan</h1>
            <p class="font-body-md text-body-md text-on-surface-variant">Kelola daftar menu, harga, dan ketersediaan stok.</p>
        </div>
        <a href="{{ route('menu.create') }}" wire:navigate
           class="bg-primary hover:bg-surface-tint text-on-primary font-body-lg text-body-lg font-bold py-3 px-6 rounded-lg shadow-[0_4px_14px_0_rgba(188,0,10,0.39)] hover:shadow-[0_6px_20px_rgba(188,0,10,0.23)] transition-all duration-200 flex items-center gap-2 active:scale-95 w-full md:w-auto justify-center">
            <span class="material-symbols-outlined">add</span>
            Tambah Menu
        </a>
    </div>

    {{-- ── Filters Section ─────────────────────────────────────────── --}}
    <div class="bg-surface-container-lowest border border-[#E9ECEF] rounded-xl p-4 flex flex-col sm:flex-row sm:items-center gap-4 justify-between shadow-sm">

        {{-- Category Filter Pills --}}
        <div class="flex overflow-x-auto gap-2 pb-2 sm:pb-0 hide-scrollbar">
            <button wire:click="$set('kategori', '')"
                    class="whitespace-nowrap px-4 py-2 font-label-caps text-label-caps rounded-full transition-colors
                           {{ ($kategori ?? '') === '' ? 'bg-primary-container text-on-primary-container font-bold' : 'border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                Semua
            </button>
            @foreach($categories ?? [] as $cat)
                <button wire:click="$set('kategori', '{{ $cat->id }}')"
                        class="whitespace-nowrap px-4 py-2 font-label-caps text-label-caps rounded-full transition-colors
                               {{ ($kategori ?? '') == $cat->id ? 'bg-primary-container text-on-primary-container font-bold' : 'border border-outline-variant text-on-surface-variant hover:bg-surface-container hover:text-on-surface' }}">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        <div class="flex gap-2 w-full sm:w-auto">
            {{-- Status Filter --}}
            <div class="relative">
                <select wire:model.live="status"
                        class="appearance-none w-full bg-white dark:bg-gray-800 dark:text-gray-100 border border-gray-200 dark:border-gray-600 text-gray-900 font-body-md rounded-lg px-4 py-2 pr-9 shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-400 transition-all duration-200">
                    <option value="">Semua Status</option>
                    <option value="1">Tersedia</option>
                    <option value="0">Habis</option>
                </select>
                <span class="material-symbols-outlined absolute right-2 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none text-[20px]">expand_more</span>
            </div>

            {{-- Search Input --}}
            <div class="relative w-full sm:w-72">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                <input wire:model.live.debounce.300ms="search"
                       class="w-full pl-10 pr-4 py-2 bg-white dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400 border border-gray-200 dark:border-gray-600 rounded-lg shadow-sm font-body-md text-body-md text-gray-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-400 transition-all duration-200"
                       placeholder="Cari nama menu..." type="text">
            </div>
        </div>
    </div>

    {{-- ── Menu Grid ────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-card-gap">
        @forelse($menuItems ?? [] as $item)
            @php
                $isHabis = !$item->is_available;
                $isSisa  = $item->is_available && isset($item->stock) && $item->stock <= 5;
            @endphp

            <div class="bg-surface-container-lowest border border-[#E9ECEF] rounded-xl overflow-hidden shadow-[0_4px_12px_rgba(0,0,0,0.03)] hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col {{ $isHabis ? 'opacity-70 grayscale-[20%]' : '' }}">

                {{-- Thumbnail --}}
                <div class="relative h-48 w-full overflow-hidden">
                    @if($item->image)
                        <img src="{{ Storage::url($item->image) }}" alt="{{ $item->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full bg-surface-container-high flex items-center justify-center">
                            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30">restaurant</span>
                        </div>
                    @endif

                    {{-- Availability Badge --}}
                    @if($isHabis)
                        <div class="absolute top-3 right-3 bg-tertiary-fixed-dim/90 backdrop-blur-sm px-3 py-1 rounded-full border border-tertiary">
                            <span class="font-label-caps text-label-caps text-on-tertiary-fixed-variant">Habis</span>
                        </div>
                    @elseif($isSisa)
                        <div class="absolute top-3 right-3 bg-secondary-fixed-dim/90 backdrop-blur-sm px-3 py-1 rounded-full border border-secondary-container">
                            <span class="font-label-caps text-label-caps text-on-secondary-fixed">Sisa {{ $item->stock }} Porsi</span>
                        </div>
                    @else
                        <div class="absolute top-3 right-3 bg-surface-container-lowest/90 backdrop-blur-sm px-3 py-1 rounded-full border border-primary/20">
                            <span class="font-label-caps text-label-caps text-primary">Tersedia</span>
                        </div>
                    @endif
                </div>

                {{-- Card Body --}}
                <div class="p-5 flex-1 flex flex-col">
                    <span class="font-label-caps text-label-caps text-on-surface-variant mb-1">
                        {{ $item->category->name ?? 'Tanpa Kategori' }}
                    </span>
                    <h3 class="font-headline-md text-headline-md text-on-surface mb-2 line-clamp-2">{{ $item->name }}</h3>

                    <div class="mt-auto pt-4 flex items-center justify-between border-t border-outline-variant/30">
                        <span class="font-headline-md text-headline-md font-bold {{ $isHabis ? 'text-on-surface-variant' : 'text-primary' }}">
                            Rp {{ number_format($item->price, 0, ',', '.') }}
                        </span>
                        <div class="flex gap-2">
                            <button wire:click="toggleAvailable({{ $item->id }})"
                                    class="p-2 text-on-surface-variant hover:text-primary bg-surface-container-lowest border border-outline-variant rounded-lg hover:border-primary transition-colors"
                                    title="Toggle Tersedia/Habis">
                                <span class="material-symbols-outlined">{{ $item->is_available ? 'block' : 'check_circle' }}</span>
                            </button>
                            <a href="{{ route('menu.edit', $item->id) }}" wire:navigate
                               class="p-2 text-on-surface-variant hover:text-secondary-fixed-dim bg-surface-container-lowest border border-outline-variant rounded-lg hover:border-secondary-fixed-dim transition-colors"
                               title="Edit">
                                <span class="material-symbols-outlined">edit</span>
                            </a>
                            <button wire:click="delete({{ $item->id }})"
                                    wire:confirm="Hapus menu '{{ $item->name }}'?"
                                    class="p-2 text-on-surface-variant hover:text-error bg-surface-container-lowest border border-outline-variant rounded-lg hover:border-error transition-colors"
                                    title="Hapus">
                                <span class="material-symbols-outlined">delete</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        @empty
            <div class="col-span-full flex flex-col items-center justify-center py-20 border-2 border-dashed border-surface-variant rounded-xl">
                <span class="material-symbols-outlined text-6xl text-on-surface-variant/30 mb-4">restaurant_menu</span>
                <p class="font-body-lg text-body-lg text-on-surface-variant mb-4">Belum ada menu. Mulai tambahkan sekarang.</p>
                <a href="{{ route('menu.create') }}" wire:navigate
                   class="bg-primary text-on-primary px-5 py-2.5 rounded-lg font-body-md font-bold flex items-center gap-2 hover:bg-surface-tint transition-colors">
                    <span class="material-symbols-outlined text-sm">add</span> Tambah Menu Pertama
                </a>
            </div>
        @endforelse
    </div>

    {{-- ── Pagination ─────────────────────────────────────────────────── --}}
    @if(($menuItems ?? null) && method_exists($menuItems, 'links') && $menuItems->hasPages())
        <div class="mt-2">{{ $menuItems->links() }}</div>
    @endif

</div>
