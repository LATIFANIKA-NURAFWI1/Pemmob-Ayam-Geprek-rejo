@if($topMenus->isEmpty())
    <div class="flex flex-col items-center justify-center py-8 text-on-surface-variant">
        <span class="material-symbols-outlined text-4xl opacity-30 mb-2">local_fire_department</span>
        <p class="text-sm">Belum ada data penjualan hari ini</p>
    </div>
@else
    <div class="flex flex-col gap-3">
        @foreach($topMenus as $index => $item)
            @php
                $rank       = $index + 1;
                $isTop3     = $rank <= 3;
                $rankBg     = $isTop3 ? 'bg-primary-container text-white' : 'bg-[#fef5e6] text-secondary-fixed-dim';
                $stagger    = 'list-stagger-' . min($rank, 5);
                $categoryName = $item->menuItem?->category?->name ?? '';
            @endphp
            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-surface-container-high transition-colors animate-fade-in {{ $stagger }}">
                <div class="flex items-center gap-4">
                    <div class="w-8 h-8 rounded-full {{ $rankBg }} flex items-center justify-center font-bold text-sm shrink-0">
                        {{ $rank }}
                    </div>
                    <div class="flex flex-col">
                        <span class="text-sm font-medium text-on-surface">
                            {{ $item->menuItem?->name ?? $item->menu_item_name }}
                        </span>
                        @if($categoryName)
                            <span class="text-xs text-on-surface-variant">{{ $categoryName }}</span>
                        @endif
                    </div>
                </div>
                <span class="text-sm font-bold text-on-surface shrink-0">{{ $item->total_terjual }}x</span>
            </div>
        @endforeach
    </div>
@endif
