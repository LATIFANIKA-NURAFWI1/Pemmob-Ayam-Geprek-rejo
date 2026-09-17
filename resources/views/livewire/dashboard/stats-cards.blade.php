@php $s = $this->stats; @endphp

<div class="flex overflow-x-auto hide-scrollbar gap-card-gap pb-4 -mx-6 px-6 md:mx-0 md:px-0 md:grid md:grid-cols-3 lg:grid-cols-5 mb-section-margin snap-x">

    {{-- Card: Total Pesanan --}}
    <div class="min-w-[160px] md:min-w-0 bg-surface-container-lowest rounded-xl p-5 border border-[#E9ECEF] shadow-[0_4px_12px_rgba(0,0,0,0.05)] hover:-translate-y-1 hover:shadow-[0_8px_24px_rgba(0,0,0,0.1)] transition-all duration-200 snap-center flex flex-col justify-between animate-fade-in-up stagger-1">
        <div class="flex flex-col gap-2 mb-4">
            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary mb-2">
                <span class="material-symbols-outlined">shopping_cart</span>
            </div>
            <span class="text-xs text-on-surface-variant font-medium">Total Pesanan</span>
        </div>
        <div>
            <div class="text-headline-lg font-headline-lg text-on-surface">{{ $s['total_pesanan'] }}</div>
            <div class="text-xs text-on-surface-variant mt-1">{{ $s['paid_count'] }} terbayar</div>
        </div>
    </div>

    {{-- Card: Omset Hari Ini --}}
    <div class="min-w-[160px] md:min-w-0 bg-surface-container-lowest rounded-xl p-5 border border-[#E9ECEF] shadow-[0_4px_12px_rgba(0,0,0,0.05)] hover:-translate-y-1 hover:shadow-[0_8px_24px_rgba(0,0,0,0.1)] transition-all duration-200 snap-center flex flex-col justify-between animate-fade-in-up stagger-2">
        <div class="flex flex-col gap-2 mb-4">
            <div class="w-10 h-10 rounded-full bg-[#fef5e6] text-secondary-fixed-dim flex items-center justify-center mb-2">
                <span class="material-symbols-outlined">payments</span>
            </div>
            <span class="text-xs text-on-surface-variant font-medium">Omset Hari Ini</span>
        </div>
        <div>
            <div class="text-headline-md font-headline-md text-secondary-fixed-dim">
                Rp {{ number_format($s['omset'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Card: Laba Kotor --}}
    <div class="min-w-[160px] md:min-w-0 bg-surface-container-lowest rounded-xl p-5 border border-[#E9ECEF] shadow-[0_4px_12px_rgba(0,0,0,0.05)] hover:-translate-y-1 hover:shadow-[0_8px_24px_rgba(0,0,0,0.1)] transition-all duration-200 snap-center flex flex-col justify-between animate-fade-in-up stagger-3">
        <div class="flex flex-col gap-2 mb-4">
            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-primary mb-2">
                <span class="material-symbols-outlined">trending_up</span>
            </div>
            <span class="text-xs text-on-surface-variant font-medium">Laba Kotor</span>
        </div>
        <div>
            <div class="text-headline-md font-headline-md text-on-surface">
                Rp {{ number_format($s['gross_profit'], 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Card: Menu Aktif --}}
    <div class="min-w-[160px] md:min-w-0 bg-surface-container-lowest rounded-xl p-5 border border-[#E9ECEF] shadow-[0_4px_12px_rgba(0,0,0,0.05)] hover:-translate-y-1 hover:shadow-[0_8px_24px_rgba(0,0,0,0.1)] transition-all duration-200 snap-center flex flex-col justify-between animate-fade-in-up stagger-4">
        <div class="flex flex-col gap-2 mb-4">
            <div class="w-10 h-10 rounded-full bg-surface-container flex items-center justify-center text-on-surface-variant mb-2">
                <span class="material-symbols-outlined">restaurant</span>
            </div>
            <span class="text-xs text-on-surface-variant font-medium">Menu Aktif</span>
        </div>
        <div>
            <div class="text-headline-lg font-headline-lg text-on-surface">{{ $s['menu_aktif'] }}</div>
        </div>
    </div>

    {{-- Card: Bahan Stok Rendah (Critical) --}}
    <div class="min-w-[200px] md:min-w-0 bg-surface-container-lowest rounded-xl p-5 border-2 border-primary shadow-[0_8px_16px_rgba(188,0,10,0.1)] hover:-translate-y-1 hover:shadow-[0_12px_24px_rgba(188,0,10,0.2)] transition-all duration-200 snap-center flex flex-col justify-between relative overflow-hidden animate-fade-in-up stagger-5">
        <div class="absolute top-0 right-0 w-16 h-16 bg-error-container rounded-bl-full -z-10 opacity-50"></div>
        <div class="flex flex-col gap-2 mb-4 relative z-10">
            <div class="flex items-center justify-between w-full mb-2">
                <div class="w-10 h-10 rounded-full bg-error-container flex items-center justify-center text-primary">
                    <span class="material-symbols-outlined">warning</span>
                </div>
            </div>
            <span class="text-sm text-primary font-bold">Bahan Stok Rendah</span>
        </div>
        <div class="relative z-10 flex flex-col gap-3">
            <div class="text-headline-lg font-headline-lg text-primary">{{ $s['stok_kritis'] }}</div>
            <a href="{{ route('stok.index') }}" wire:navigate
               class="bg-primary hover:bg-surface-tint text-on-primary text-xs font-bold py-2 px-3 rounded-lg transition-colors flex items-center justify-center gap-1 w-fit">
                Cek Stok <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>
    </div>

</div>
