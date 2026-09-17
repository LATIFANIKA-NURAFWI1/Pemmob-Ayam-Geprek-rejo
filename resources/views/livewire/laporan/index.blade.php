<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-surface text-on-surface font-body-md antialiased">
    <style>
        /* Skeleton Animation */
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .skeleton {
            background: #f6f7f8;
            background-image: linear-gradient(to right, #f6f7f8 0%, #edeef1 20%, #f6f7f8 40%, #f6f7f8 100%);
            background-repeat: no-repeat;
            background-size: 1000px 100%; 
            animation-duration: 1s;
            animation-fill-mode: forwards; 
            animation-iteration-count: infinite;
            animation-name: shimmer;
            animation-timing-function: linear;
        }

        /* Number Counting Animation - Initial State */
        .count-up {
            opacity: 0;
            transform: translateY(10px);
            transition: opacity 0.5s ease-out, transform 0.5s ease-out;
        }
        .count-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>

    @php
        $rep   = $this->report ?? [];
        $rev   = (float)($rep['revenue']        ?? 0);
        $cogs  = (float)($rep['total_hpp']      ?? 0);
        $gross = (float)($rep['gross_profit']   ?? ($rev - $cogs));
        $exp   = (float)($rep['total_expenses'] ?? 0);
        $net   = (float)($rep['net_profit']     ?? ($gross - $exp));
        $top   = $this->topItems ?? [];

        // expenses_by_category keyed by DB snake_case (e.g. 'gaji', 'bahan_baku', 'operasional', 'perawatan', 'lainnya')
        $expCat = $rep['expenses_by_category'] ?? [];

        $gaji   = (float)($expCat['gaji']        ?? 0);
        $ops    = (float)($expCat['operasional']  ?? 0);
        $bahan  = (float)($expCat['bahan_baku']   ?? 0);
        $rawat  = (float)($expCat['perawatan']    ?? 0);
        $lainnya= (float)($expCat['lainnya']      ?? 0);

        // "Lain-lain" di chart = semua selain Gaji & Operasional
        $lain = $bahan + $rawat + $lainnya;

        $gajiPct        = $exp > 0 ? round(($gaji / $exp) * 100) : 0;
        $operasionalPct = $exp > 0 ? round(($ops  / $exp) * 100) : 0;
        $lainPct        = $exp > 0 ? (100 - $gajiPct - $operasionalPct) : 0;

        $gajiNominal        = 'Rp ' . number_format($gaji, 0, ',', '.');
        $operasionalNominal = 'Rp ' . number_format($ops,  0, ',', '.');
        $lainNominal        = 'Rp ' . number_format($lain, 0, ',', '.');

        // SVG Donut: circumference = 2π × r, r = 54 (viewBox 120×120, cx=cy=60)
        $r            = 54;
        $circumference = round(2 * M_PI * $r, 2); // ≈ 339.29

        // Donut segments (stroke-dasharray = filled gap)
        $gajiDash        = $exp > 0 ? round(($gaji / $exp) * $circumference, 2)   : 0;
        $opsDash         = $exp > 0 ? round(($ops  / $exp) * $circumference, 2)   : 0;
        $lainDash        = $exp > 0 ? round(($lain / $exp) * $circumference, 2)   : 0;

        // Offsets (rotate segments: gaji starts at top = -circumference/4)
        $gajiOffset = round($circumference * 0.25, 2);
        $opsOffset  = round($gajiOffset - $gajiDash, 2);
        $lainOffset = round($opsOffset - $opsDash, 2);
    @endphp

    {{-- ── Header & Filters ──────────────────────────────────────── --}}
    <section class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
        <div>
            <h1 class="font-headline-lg text-headline-lg text-on-surface mb-4">Laporan Penjualan</h1>
            <div class="flex flex-wrap gap-4 items-center">
                {{-- Segmented Control --}}
                <div class="flex p-1 bg-surface-container-low rounded-lg border border-surface-variant">
                    <button wire:click="applyPreset('hari_ini')" class="px-4 py-2 font-label-caps text-label-caps transition-colors {{ $preset === 'hari_ini' ? 'bg-surface text-primary font-bold rounded-md shadow-sm border border-surface-variant' : 'text-on-surface-variant hover:text-on-surface rounded-md' }}">Hari Ini</button>
                    <button wire:click="applyPreset('minggu_ini')" class="px-4 py-2 font-label-caps text-label-caps transition-colors {{ $preset === 'minggu_ini' ? 'bg-surface text-primary font-bold rounded-md shadow-sm border border-surface-variant' : 'text-on-surface-variant hover:text-on-surface rounded-md' }}">Minggu Ini</button>
                    <button wire:click="applyPreset('bulanan')" class="px-4 py-2 font-label-caps text-label-caps transition-colors {{ $preset === 'bulanan' ? 'bg-surface text-primary font-bold rounded-md shadow-sm border border-surface-variant' : 'text-on-surface-variant hover:text-on-surface rounded-md' }}">Bulan Ini</button>
                    <button wire:click="applyPreset('tahun')" class="px-4 py-2 font-label-caps text-label-caps transition-colors {{ $preset === 'tahun' ? 'bg-surface text-primary font-bold rounded-md shadow-sm border border-surface-variant' : 'text-on-surface-variant hover:text-on-surface rounded-md' }}">Tahun Ini</button>
                </div>
                {{-- Dropdowns --}}
                <div class="flex gap-2">
                    <select wire:model.live="selectedMonth" class="form-select border border-surface-variant rounded-lg bg-surface text-on-surface font-body-md py-2 pl-3 pr-8 focus:ring-primary focus:border-primary">
                        <option value="1">Januari</option><option value="2">Februari</option>
                        <option value="3">Maret</option><option value="4">April</option>
                        <option value="5">Mei</option><option value="6">Juni</option>
                        <option value="7">Juli</option><option value="8">Agustus</option>
                        <option value="9">September</option><option value="10">Oktober</option>
                        <option value="11">November</option><option value="12">Desember</option>
                    </select>
                    <select wire:model.live="selectedYear" class="form-select border border-surface-variant rounded-lg bg-surface text-on-surface font-body-md py-2 pl-3 pr-8 focus:ring-primary focus:border-primary">
                        @for($y = date('Y'); $y >= date('Y') - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                        @endfor
                    </select>
                </div>
            </div>
        </div>
        {{-- Export Action --}}
        <button wire:click="exportPdf" class="flex items-center justify-center gap-2 px-6 py-3 border border-primary text-primary rounded-lg font-body-lg text-body-lg hover:bg-primary hover:text-white transition-colors duration-200 active:scale-95 whitespace-nowrap">
            <span class="material-symbols-outlined">picture_as_pdf</span>
            Export PDF
        </button>
    </section>

    {{-- Flash Status/Error --}}
    @if(session('error'))
        <div class="bg-error-container/20 border border-error text-error px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <span class="material-symbols-outlined text-[18px]">error</span>
            {{ session('error') }}
        </div>
    @endif

    {{-- ── Financial Summary Cards ─────────────────────────────────── --}}
    <section class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-card-gap">
        {{-- Total Revenue --}}
        <div class="bg-surface border border-surface-variant rounded-xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <p class="font-body-md text-body-md text-on-surface-variant mb-2">Total Revenue</p>
            <p class="font-headline-lg text-headline-lg text-secondary">Rp {{ number_format($rev, 0, ',', '.') }}</p>
        </div>
        {{-- Laba Kotor --}}
        <div class="bg-surface border border-surface-variant rounded-xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <p class="font-body-md text-body-md text-on-surface-variant mb-2">Laba Kotor</p>
            <p class="font-headline-lg text-headline-lg text-secondary">Rp {{ number_format($gross, 0, ',', '.') }}</p>
        </div>
        {{-- Total Expense --}}
        <div class="bg-surface border border-surface-variant rounded-xl p-5 shadow-[0_4px_20px_rgba(0,0,0,0.03)] hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
            <p class="font-body-md text-body-md text-on-surface-variant mb-2">Total Expense</p>
            <p class="font-headline-lg text-headline-lg text-error">Rp {{ number_format($exp, 0, ',', '.') }}</p>
        </div>
        {{-- Laba Bersih (Hero) --}}
        <div class="bg-surface border border-surface-variant border-t-4 border-t-secondary-container rounded-xl p-5 shadow-[0_8px_30px_rgba(253,192,3,0.15)] hover:shadow-[0_12px_35px_rgba(253,192,3,0.3)] hover:-translate-y-1 transition-all duration-300 md:col-span-2 lg:col-span-1">
            <p class="font-label-caps text-label-caps text-on-surface-variant mb-2 uppercase">Laba Bersih</p>
            <p class="font-headline-lg text-headline-lg font-bold text-secondary">Rp {{ number_format($net, 0, ',', '.') }}</p>
        </div>
    </section>

    {{-- ── Main Data Section ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-card-gap">

        {{-- SVG Donut Chart: Pengeluaran Operasional --}}
        <section class="lg:col-span-1 bg-surface border border-surface-variant rounded-xl p-container-padding shadow-[0_4px_20px_rgba(0,0,0,0.03)] flex flex-col"
            wire:key="donut-chart-{{ $dari }}-{{ $sampai }}"
            x-data="{
                circumference: {{ $circumference }},
                gajiDash: 0,
                opsDash: 0,
                lainDash: 0,
                gajiOffset: {{ $gajiOffset }},
                opsOffset: 0,
                lainOffset: 0,
                init() {
                    // Animate: start all at 0, then transition to real values
                    this.$nextTick(() => {
                        setTimeout(() => {
                            this.gajiDash  = {{ $gajiDash }};
                            this.opsDash   = {{ $opsDash }};
                            this.lainDash  = {{ $lainDash }};
                            this.opsOffset  = {{ $opsOffset }};
                            this.lainOffset = {{ $lainOffset }};
                        }, 150);
                    });
                }
            }">
            <h3 class="font-headline-md text-headline-md text-on-surface mb-6">Pengeluaran Operasional</h3>

            {{-- SVG Donut --}}
            <div class="flex-1 flex flex-col items-center justify-center relative min-h-[250px]">
                <svg viewBox="0 0 120 120" class="w-48 h-48 -rotate-90" aria-hidden="true">
                    {{-- Track (background circle) --}}
                    <circle cx="60" cy="60" r="{{ $r }}"
                            fill="none"
                            stroke="currentColor"
                            class="text-surface-container-high"
                            stroke-width="14"/>

                    {{-- Segment: Lain-lain (bahan baku + perawatan + lainnya) = abu --}}
                    <circle cx="60" cy="60" r="{{ $r }}"
                            fill="none"
                            stroke="#5a5c5e"
                            stroke-width="14"
                            stroke-linecap="round"
                            :stroke-dasharray="lainDash + ' ' + (circumference - lainDash)"
                            :stroke-dashoffset="lainOffset"
                            style="transition: stroke-dasharray 0.9s ease-in-out, stroke-dashoffset 0.9s ease-in-out;"/>

                    {{-- Segment: Operasional = kuning --}}
                    <circle cx="60" cy="60" r="{{ $r }}"
                            fill="none"
                            stroke="#fdc003"
                            stroke-width="14"
                            stroke-linecap="round"
                            :stroke-dasharray="opsDash + ' ' + (circumference - opsDash)"
                            :stroke-dashoffset="opsOffset"
                            style="transition: stroke-dasharray 0.9s ease-in-out 0.1s, stroke-dashoffset 0.9s ease-in-out 0.1s;"/>

                    {{-- Segment: Gaji = merah --}}
                    <circle cx="60" cy="60" r="{{ $r }}"
                            fill="none"
                            stroke="#bc000a"
                            stroke-width="14"
                            stroke-linecap="round"
                            :stroke-dasharray="gajiDash + ' ' + (circumference - gajiDash)"
                            :stroke-dashoffset="gajiOffset"
                            style="transition: stroke-dasharray 0.9s ease-in-out 0.2s;"/>
                </svg>

                {{-- Center Label --}}
                <div class="absolute inset-0 flex items-center justify-center flex-col pointer-events-none">
                    <span class="font-body-md text-body-md text-on-surface-variant">Total</span>
                    <span class="font-headline-md text-headline-md font-bold text-on-surface">
                        <span wire:loading.class="opacity-30 animate-pulse" wire:target="selectedMonth, selectedYear, applyPreset">
                            {{ number_format($exp, 0, ',', '.') }}
                        </span>
                    </span>
                </div>
            </div>

            {{-- Legend --}}
            <div class="mt-6 flex flex-col gap-3">
                {{-- Gaji --}}
                <div class="flex items-center justify-between group/leg">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-primary shrink-0"></div>
                        <span class="font-body-md text-body-md text-on-surface">Gaji Karyawan</span>
                    </div>
                    <div class="text-right">
                        <span class="font-body-md text-body-md font-bold block" wire:loading.class="animate-pulse opacity-40" wire:target="selectedMonth, selectedYear, applyPreset">
                            {{ $gajiPct }}%
                        </span>
                        <span class="text-xs text-on-surface-variant" wire:loading.class="animate-pulse opacity-40" wire:target="selectedMonth, selectedYear, applyPreset">
                            {{ $gajiNominal }}
                        </span>
                    </div>
                </div>

                {{-- Operasional --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-secondary-container shrink-0"></div>
                        <span class="font-body-md text-body-md text-on-surface">Operasional &amp; Listrik</span>
                    </div>
                    <div class="text-right">
                        <span class="font-body-md text-body-md font-bold block" wire:loading.class="animate-pulse opacity-40" wire:target="selectedMonth, selectedYear, applyPreset">
                            {{ $operasionalPct }}%
                        </span>
                        <span class="text-xs text-on-surface-variant" wire:loading.class="animate-pulse opacity-40" wire:target="selectedMonth, selectedYear, applyPreset">
                            {{ $operasionalNominal }}
                        </span>
                    </div>
                </div>

                {{-- Lain-lain --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-3 h-3 rounded-full bg-[#5a5c5e] shrink-0"></div>
                        <span class="font-body-md text-body-md text-on-surface">Lain-lain</span>
                    </div>
                    <div class="text-right">
                        <span class="font-body-md text-body-md font-bold block" wire:loading.class="animate-pulse opacity-40" wire:target="selectedMonth, selectedYear, applyPreset">
                            {{ $lainPct }}%
                        </span>
                        <span class="text-xs text-on-surface-variant" wire:loading.class="animate-pulse opacity-40" wire:target="selectedMonth, selectedYear, applyPreset">
                            {{ $lainNominal }}
                        </span>
                    </div>
                </div>

                {{-- Separator + Breakdown extra --}}
                @if($bahan > 0 || $rawat > 0)
                <div class="border-t border-surface-variant pt-3 mt-1 space-y-1.5">
                    @if($bahan > 0)
                    <div class="flex justify-between text-xs text-on-surface-variant">
                        <span class="pl-5">↳ Bahan Baku</span>
                        <span>Rp {{ number_format($bahan, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($rawat > 0)
                    <div class="flex justify-between text-xs text-on-surface-variant">
                        <span class="pl-5">↳ Perawatan</span>
                        <span>Rp {{ number_format($rawat, 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($lainnya > 0)
                    <div class="flex justify-between text-xs text-on-surface-variant">
                        <span class="pl-5">↳ Lainnya</span>
                        <span>Rp {{ number_format($lainnya, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </section>

        {{-- Top Menu Terlaris Table --}}
        <section class="lg:col-span-2 bg-surface border border-surface-variant rounded-xl p-container-padding shadow-[0_4px_20px_rgba(0,0,0,0.03)] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="font-headline-md text-headline-md text-on-surface">Top Menu Terlaris</h3>
                <a href="{{ route('menu.index') }}" wire:navigate class="text-primary text-sm font-bold hover:underline">Lihat Semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-surface-variant">
                            <th class="py-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase w-16 text-center">Rank</th>
                            <th class="py-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase">Menu</th>
                            <th class="py-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase text-right">Terjual</th>
                            <th class="py-3 px-4 font-label-caps text-label-caps text-on-surface-variant uppercase text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($top as $index => $item)
                            <tr class="border-b border-surface-variant hover:bg-surface-container-low transition-colors cursor-default">
                                <td class="py-3 px-4 text-center">
                                    <div class="w-8 h-8 rounded-full {{ $index < 3 ? 'bg-secondary-container text-on-secondary-container' : 'bg-surface-container text-on-surface-variant' }} flex items-center justify-center font-bold mx-auto text-sm">{{ $index + 1 }}</div>
                                </td>
                                <td class="py-3 px-4 font-body-lg text-body-lg text-on-surface">{{ $item['menu_item_name'] ?? ($item->menu_item_name ?? '-') }}</td>
                                <td class="py-3 px-4 font-body-md text-body-md text-on-surface-variant text-right">{{ number_format($item['total_qty'] ?? ($item->total_qty ?? 0), 0, ',', '.') }} porsi</td>
                                <td class="py-3 px-4 font-body-lg text-body-lg font-bold text-on-surface text-right">Rp {{ number_format($item['total_revenue'] ?? ($item->total_revenue ?? 0), 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-on-surface-variant font-body-md text-body-md">Belum ada data penjualan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>

</div>
