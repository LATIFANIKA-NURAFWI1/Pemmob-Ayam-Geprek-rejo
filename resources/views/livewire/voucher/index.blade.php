<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-background text-on-surface font-body-md antialiased">

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

    {{-- ── Header ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col gap-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="font-headline-lg text-headline-lg font-bold text-on-surface">Manajemen Voucher</h1>
                <p class="font-body-md text-body-md text-on-surface-variant mt-1">Kelola kode diskon dan promo pelanggan.</p>
            </div>
            <button wire:click="openCreate()"
                    class="w-full md:w-auto bg-primary text-on-primary py-3 px-6 rounded-xl flex items-center justify-center gap-2 hover:bg-surface-tint transition-colors active:scale-95 duration-150 shadow-[0_4px_14px_0_rgba(188,0,10,0.39)] hover:shadow-[0_6px_20px_rgba(188,0,10,0.23)] font-body-lg text-body-lg font-bold">
                <span class="material-symbols-outlined text-xl">add</span>
                Buat Voucher
            </button>
        </div>
    </div>

    {{-- ── Filters ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
            <input wire:model.live.debounce.300ms="search" type="text"
                   placeholder="Cari kode voucher..."
                   class="w-full bg-surface-container-lowest border border-surface-variant rounded-xl py-3 pl-10 pr-4 focus:ring-2 focus:ring-secondary-container focus:border-secondary-container outline-none transition-all font-body-md text-body-md shadow-sm">
        </div>
        <div class="relative sm:w-56">
            <select wire:model.live="filterStatus"
                    class="w-full bg-surface-container-lowest border border-surface-variant rounded-xl py-3 px-4 pr-10 appearance-none focus:ring-2 focus:ring-secondary-container focus:border-secondary-container outline-none transition-all font-body-md text-body-md shadow-sm">
                <option value="">Semua Status</option>
                <option value="active">Aktif</option>
                <option value="inactive">Tidak Aktif</option>
            </select>
            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
        </div>
    </div>

    {{-- ── Voucher Cards List ────────────────────────────────────────── --}}
    <div class="flex flex-col gap-4">
        @forelse($vouchers ?? [] as $voucher)
            @php
                $isAktif   = $voucher->is_active && (!$voucher->expires_at || $voucher->expires_at > now());
                $usedCount = $voucher->used_count ?? 0;
                $maxUsage  = $voucher->max_uses ?? '∞';
            @endphp
            <div class="bg-surface-container-lowest border border-[#E9ECEF] shadow-[0_4px_15px_rgba(0,0,0,0.03)] rounded-xl p-5 hover:shadow-[0_8px_24px_rgba(0,0,0,0.07)] transition-all duration-200">

                <div class="flex justify-between items-start mb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center border border-surface-variant shrink-0">
                            <span class="material-symbols-outlined text-secondary-fixed-dim">local_activity</span>
                        </div>
                        <div>
                            <h3 class="font-headline-md text-headline-md text-on-surface font-bold leading-none mb-1">{{ $voucher->code }}</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant text-[12px]">
                                Min. Rp {{ number_format($voucher->minimum_order ?? 0, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 shrink-0">
                        <span class="px-3 py-1 rounded-full border text-[11px] font-bold uppercase tracking-wider
                                     {{ $voucher->member_only ? 'border-blue-300 bg-blue-50/50 text-blue-600' : 'border-emerald-300 bg-emerald-50/50 text-emerald-600' }}">
                            {{ $voucher->member_only ? 'Member' : 'Umum' }}
                        </span>
                        <span class="px-3 py-1 rounded-full border text-[11px] font-bold uppercase tracking-wider
                                     {{ $isAktif ? 'border-secondary-fixed-dim bg-[#fff7e6] text-secondary-fixed-dim' : 'border-surface-variant bg-surface-container text-on-surface-variant' }}">
                            {{ $isAktif ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-surface-variant/50">
                    <div>
                        <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">POTONGAN</p>
                        <p class="font-headline-md text-[16px] text-primary font-bold">
                            @if($voucher->discount_type === 'percentage')
                                {{ $voucher->discount_value }}%
                            @else
                                Rp {{ number_format($voucher->discount_value, 0, ',', '.') }}
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">PENGGUNAAN</p>
                        <p class="font-headline-md text-[16px] text-on-surface font-bold">
                            {{ $usedCount }} / {{ $maxUsage }}
                        </p>
                    </div>
                </div>

                {{-- Periode --}}
                @if($voucher->starts_at || $voucher->expires_at)
                    <div class="mt-3 pt-3 border-t border-surface-variant/30">
                        <p class="font-label-caps text-label-caps text-on-surface-variant mb-1">PERIODE</p>
                        <p class="font-body-md text-body-md text-on-surface-variant text-[12px]">
                            {{ $voucher->starts_at?->format('d M Y') ?? '—' }}
                            → {{ $voucher->expires_at?->format('d M Y') ?? '∞' }}
                        </p>
                    </div>
                @endif

                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="openEdit({{ $voucher->id }})"
                            class="p-2 rounded-lg text-on-surface-variant hover:bg-surface hover:text-secondary-fixed-dim transition-colors border border-transparent hover:border-surface-variant"
                            title="Edit">
                        <span class="material-symbols-outlined text-[20px]">edit</span>
                    </button>
                    <button wire:click="confirmDelete({{ $voucher->id }})"
                            class="p-2 rounded-lg text-on-surface-variant hover:bg-surface hover:text-primary transition-colors border border-transparent hover:border-primary/30"
                            title="Hapus">
                        <span class="material-symbols-outlined text-[20px]">delete</span>
                    </button>
                </div>
            </div>
        @empty
            <div class="rounded-xl p-16 text-center border-2 border-dashed border-surface-variant">
                <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">confirmation_number</span>
                <p class="text-on-surface-variant text-sm mb-4">Belum ada voucher. Buat yang pertama!</p>
                <button wire:click="openCreate()"
                        class="inline-flex items-center gap-2 bg-primary text-on-primary px-5 py-2.5 rounded-lg font-body-md font-bold hover:bg-surface-tint transition-colors">
                    <span class="material-symbols-outlined text-sm">add</span> Buat Voucher
                </button>
            </div>
        @endforelse
    </div>

    {{-- ── Pagination ─────────────────────────────────────────────────── --}}
    @if(($vouchers ?? null) && method_exists($vouchers, 'links') && $vouchers->hasPages())
        <div>{{ $vouchers->links() }}</div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL / DRAWER: Form Buat & Edit Voucher (dari tambahvoucher.html)
    ══════════════════════════════════════════════════════════════════════ --}}
    @if($showForm)
        {{-- Backdrop --}}
        <div wire:click="closeForm()"
             class="fixed inset-0 bg-black/40 z-40 backdrop-blur-sm"
             x-data x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">
        </div>

        {{-- Drawer Sheet --}}
        <div class="fixed bottom-0 left-0 right-0 z-50 md:inset-0 md:flex md:items-center md:justify-center"
             x-data x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="translate-y-full md:translate-y-0 md:opacity-0 md:scale-95"
             x-transition:enter-end="translate-y-0 md:opacity-100 md:scale-100">

            <div class="bg-surface-container-lowest w-full md:max-w-lg md:rounded-2xl md:shadow-2xl max-h-[90dvh] flex flex-col rounded-t-2xl shadow-[0_-8px_30px_rgba(0,0,0,0.12)]">

                {{-- Drawer Header --}}
                <header class="sticky top-0 bg-surface-container-lowest border-b border-surface-variant flex justify-between items-center px-4 h-16 rounded-t-2xl shrink-0">
                    <button wire:click="closeForm()"
                            class="text-on-surface-variant hover:bg-surface-container-high transition-colors p-2 rounded-full active:scale-95">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                    <h2 class="font-headline-md text-headline-md text-on-surface">
                        {{ $editingId ? 'Edit Voucher' : 'Buat Voucher Baru' }}
                    </h2>
                    <div class="w-10"></div>
                </header>

                {{-- Form Body (scrollable) --}}
                <div class="flex-1 overflow-y-auto p-6 space-y-4">

                    {{-- Kode Voucher --}}
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant mb-2 flex items-center gap-1 uppercase block">
                            Kode Voucher <span class="text-primary">*</span>
                        </label>
                        <input wire:model="formCode" type="text"
                               placeholder="Contoh: GEPREKMERDEKA"
                               class="w-full bg-surface-container-low border border-transparent rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 uppercase transition-all focus:bg-surface-container-lowest focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none">
                        @error('formCode')
                            <p class="text-primary text-xs mt-1 flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Tipe & Nilai Diskon --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="font-label-caps text-label-caps text-on-surface-variant mb-2 uppercase block">Tipe Diskon</label>
                            <div class="relative">
                                <select wire:model.live="formDiscountType"
                                        class="w-full bg-surface-container-low border border-transparent rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface appearance-none transition-all focus:bg-surface-container-lowest focus:border-secondary-container focus:ring-1 focus:ring-secondary-container focus:outline-none pr-10">
                                    <option value="fixed">Nominal (Rp)</option>
                                    <option value="percentage">Persentase (%)</option>
                                </select>
                                <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                            </div>
                        </div>
                        <div>
                            <label class="font-label-caps text-label-caps text-on-surface-variant mb-2 uppercase block">Nilai Diskon</label>
                            <div class="relative flex items-center">
                                <input wire:model="formDiscountValue" type="number" min="0"
                                       placeholder="0"
                                       class="w-full bg-surface-container-low border border-transparent rounded-lg pl-4 pr-12 py-3 font-body-md text-body-md text-on-surface transition-all focus:bg-surface-container-lowest focus:border-secondary-container focus:ring-1 focus:ring-secondary-container focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                                <span class="absolute right-4 font-body-md text-body-md text-on-surface-variant font-medium pointer-events-none">
                                    {{ $formDiscountType === 'percentage' ? '%' : 'Rp' }}
                                </span>
                            </div>
                            @error('formDiscountValue')
                                <p class="text-primary text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Min. Pembelian --}}
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant mb-2 uppercase block">Min. Pembelian</label>
                        <div class="relative flex items-center">
                            <span class="absolute left-4 font-body-md text-body-md text-on-surface-variant font-medium pointer-events-none">Rp</span>
                            <input wire:model="formMinPurchase" type="number" min="0"
                                   placeholder="0"
                                   class="w-full bg-surface-container-low border border-transparent rounded-lg pl-12 pr-4 py-3 font-body-md text-body-md text-on-surface transition-all focus:bg-surface-container-lowest focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                        </div>
                    </div>

                    {{-- Maks. Penggunaan --}}
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant mb-2 uppercase block">Maks. Penggunaan (Kuota)</label>
                        <input wire:model="formMaxUses" type="number" min="1"
                               placeholder="Kosongkan untuk tidak terbatas"
                               class="w-full bg-surface-container-low border border-transparent rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface placeholder:text-on-surface-variant/50 transition-all focus:bg-surface-container-lowest focus:border-primary focus:ring-1 focus:ring-primary focus:outline-none [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
                    </div>

                    {{-- Periode Berlaku --}}
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant mb-2 uppercase block">Periode Berlaku</label>
                        <div class="flex items-center gap-2">
                            <input wire:model="formStartDate" type="date"
                                   class="flex-1 bg-surface-container-low border border-transparent rounded-lg px-3 py-3 font-body-md text-body-md text-on-surface transition-all focus:bg-surface-container-lowest focus:border-secondary-container focus:ring-1 focus:ring-secondary-container focus:outline-none">
                            <span class="text-on-surface-variant font-body-md">–</span>
                            <input wire:model="formEndDate" type="date"
                                   class="flex-1 bg-surface-container-low border border-transparent rounded-lg px-3 py-3 font-body-md text-body-md text-on-surface transition-all focus:bg-surface-container-lowest focus:border-secondary-container focus:ring-1 focus:ring-secondary-container focus:outline-none">
                        </div>
                        @error('formEndDate')
                            <p class="text-primary text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipe Akses Voucher (Umum vs Member) --}}
                    <div>
                        <label class="font-label-caps text-label-caps text-on-surface-variant mb-2 uppercase block">Tipe Akses Voucher</label>
                        <div class="relative">
                            <select wire:model="formMemberOnly"
                                    class="w-full bg-surface-container-low border border-transparent rounded-lg px-4 py-3 font-body-md text-body-md text-on-surface appearance-none transition-all focus:bg-surface-container-lowest focus:border-secondary-container focus:ring-1 focus:ring-secondary-container focus:outline-none pr-10">
                                <option value="0">Umum (Bisa digunakan oleh semua customer)</option>
                                <option value="1">Khusus Member (Hanya untuk customer yang login)</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant pointer-events-none">expand_more</span>
                        </div>
                        @error('formMemberOnly')
                            <p class="text-primary text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <hr class="border-surface-variant">

                    {{-- Toggle Aktifkan Voucher --}}
                    <div class="flex items-center justify-between bg-surface-container-low p-4 rounded-xl">
                        <div>
                            <h3 class="font-body-lg text-body-lg text-on-surface">Aktifkan Voucher</h3>
                            <p class="font-body-md text-body-md text-on-surface-variant text-[13px] mt-0.5">Voucher dapat digunakan pelanggan.</p>
                        </div>
                        {{-- Tailwind toggle switch --}}
                        <button type="button"
                                wire:click="$toggle('formIsActive')"
                                class="relative inline-flex h-6 w-12 shrink-0 items-center rounded-full transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2
                                       {{ $formIsActive ? 'bg-primary' : 'bg-surface-variant' }}"
                                role="switch" aria-checked="{{ $formIsActive ? 'true' : 'false' }}">
                            <span class="inline-block h-5 w-5 transform rounded-full bg-surface-container-lowest shadow-md transition-transform duration-300
                                         {{ $formIsActive ? 'translate-x-6' : 'translate-x-0.5' }}">
                            </span>
                        </button>
                    </div>

                </div>

                {{-- Drawer Footer / Action Bar --}}
                <div class="border-t border-surface-variant bg-surface-container-lowest px-6 py-4 flex gap-4 rounded-b-2xl shrink-0">
                    <button wire:click="closeForm()"
                            class="flex-1 font-headline-md text-headline-md text-on-surface-variant border border-surface-variant rounded-xl py-3 hover:bg-surface-container-high transition-colors active:scale-95">
                        Batal
                    </button>
                    <button wire:click="saveVoucher()"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-75 cursor-not-allowed"
                            class="flex-1 font-headline-md text-headline-md text-on-primary bg-primary rounded-xl py-3 hover:bg-surface-tint transition-colors shadow-sm active:scale-95 flex items-center justify-center gap-2">
                        <span wire:loading wire:target="saveVoucher" class="material-symbols-outlined text-xl animate-spin">sync</span>
                        <span wire:loading.remove wire:target="saveVoucher" class="material-symbols-outlined text-xl">save</span>
                        <span wire:loading.remove wire:target="saveVoucher">Simpan</span>
                        <span wire:loading wire:target="saveVoucher">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══ Delete Confirm Modal ══════════════════════════════════════════ --}}
    @if($deletingId)
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm"
             x-data x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-surface-container-lowest rounded-2xl shadow-2xl p-6 max-w-sm w-full">
                <div class="w-12 h-12 rounded-full bg-error-container flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-primary">delete_forever</span>
                </div>
                <h3 class="font-headline-md text-headline-md text-on-surface text-center mb-2">Hapus Voucher?</h3>
                <p class="font-body-md text-body-md text-on-surface-variant text-center mb-6">Voucher yang dihapus tidak dapat dikembalikan.</p>
                <div class="flex gap-3">
                    <button wire:click="cancelDelete()"
                            class="flex-1 py-3 rounded-xl border border-surface-variant font-body-lg text-body-lg text-on-surface-variant hover:bg-surface-container transition-colors">
                        Batal
                    </button>
                    <button wire:click="delete()"
                            class="flex-1 py-3 rounded-xl bg-primary text-on-primary font-body-lg text-body-lg hover:bg-surface-tint transition-colors shadow-sm">
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
