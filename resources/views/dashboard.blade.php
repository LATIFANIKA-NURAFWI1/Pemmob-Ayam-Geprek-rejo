<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-background text-on-background font-body-md antialiased">

        {{-- ── Header ────────────────────────────────────────────── --}}
        <div class="animate-slide-down">
            <h1 class="text-headline-lg font-headline-lg text-on-background mb-1">Dashboard</h1>
            <p class="text-body-md text-on-surface-variant">Selamat datang di Sistem Self-Order Geprek Rejo 🍗</p>
        </div>

        {{-- ── Metrics Strip (Stats Cards Livewire Component) ──────── --}}
        @livewire('dashboard.stats-cards')

        {{-- ── Detail Sections ─────────────────────────────────────── --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-card-gap">

            {{-- Pesanan Hari Ini --}}
            <div class="bg-surface-container-lowest rounded-xl border border-[#E9ECEF] shadow-[0_4px_12px_rgba(0,0,0,0.05)] p-5">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-body-lg font-body-lg text-on-surface">Pesanan Hari Ini</h2>
                    <a href="{{ route('pesanan.index') }}" wire:navigate
                       class="text-xs text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                        Lihat Semua <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
                @livewire('dashboard.recent-orders')
            </div>

            {{-- Menu Terlaris --}}
            <div class="bg-surface-container-lowest rounded-xl border border-[#E9ECEF] shadow-[0_4px_12px_rgba(0,0,0,0.05)] p-5">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-body-lg font-body-lg text-on-surface">Menu Terlaris</h2>
                    <a href="{{ route('menu.index') }}" wire:navigate
                       class="text-xs text-on-surface-variant hover:text-primary transition-colors flex items-center gap-1">
                        Kelola Menu <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>
                @livewire('dashboard.top-menu')
            </div>

        </div>

    </div>

    @push('scripts')
    <script>
        // Stagger animation helper — applies to elements with .stagger-N and .list-stagger-N
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!prefersReducedMotion) {
            // Counter animation with Intersection Observer
            const counters = document.querySelectorAll('.counter');
            const speed = 1.5;

            counters.forEach(counter => {
                const animate = () => {
                    const target = +counter.getAttribute('data-target');
                    let startTime = null;
                    const step = (timestamp) => {
                        if (!startTime) startTime = timestamp;
                        const progress = timestamp - startTime;
                        const pct = Math.min(progress / (speed * 1000), 1);
                        const easeOut = 1 - Math.pow(1 - pct, 4);
                        counter.innerText = Math.floor(easeOut * target).toLocaleString('id-ID');
                        if (progress < speed * 1000) window.requestAnimationFrame(step);
                        else counter.innerText = target.toLocaleString('id-ID');
                    };
                    window.requestAnimationFrame(step);
                };
                const obs = new IntersectionObserver((entries) => {
                    if (entries[0].isIntersecting) { animate(); obs.disconnect(); }
                }, { threshold: 0.5 });
                obs.observe(counter);
            });
        } else {
            // Fallback for reduced motion
            document.querySelectorAll('.counter').forEach(counter => {
                counter.innerText = (+counter.getAttribute('data-target')).toLocaleString('id-ID');
            });
        }
    </script>
    @endpush
</x-layouts::app>
