<div class="flex h-full w-full flex-1 flex-col gap-6 p-6 bg-surface text-on-surface font-body-md antialiased">

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- HEADER                                                                --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-headline-lg font-bold text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary" style="font-variation-settings:'FILL' 1">badge</span>
                Manajemen Staf & Jadwal Shift
            </h1>
            <p class="text-body-md text-on-surface-variant mt-1">
                Kelola akun staf (REQ-FUNC-039) dan jadwal shift harian (REQ-FUNC-040)
            </p>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- FLASH MESSAGES                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if(session('status'))
        <div class="flex items-center gap-3 p-4 bg-secondary-container/20 border border-secondary-container rounded-xl text-on-surface"
             x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             x-transition:leave="transition ease-in duration-300" x-transition:leave-end="opacity-0">
            <span class="material-symbols-outlined text-secondary-container">check_circle</span>
            <span class="text-body-md font-medium">{{ session('status') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="flex items-center gap-3 p-4 bg-error-container/20 border border-primary-container rounded-xl text-on-surface">
            <span class="material-symbols-outlined text-primary-container">error</span>
            <span class="text-body-md font-medium">{{ session('error') }}</span>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB NAVIGATION                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    <div class="flex gap-1 p-1 bg-surface-container rounded-xl w-fit">
        <button wire:click="$set('activeTab', 'staff')"
                id="tab-staff"
                class="px-5 py-2 rounded-lg text-label-caps font-bold transition-all
                    {{ $activeTab === 'staff'
                        ? 'bg-primary text-on-primary shadow-sm'
                        : 'text-on-surface-variant hover:bg-surface-variant' }}">
            <span class="material-symbols-outlined text-sm align-middle mr-1">group</span>
            Staf
        </button>
        <button wire:click="$set('activeTab', 'shifts')"
                id="tab-shifts"
                class="px-5 py-2 rounded-lg text-label-caps font-bold transition-all
                    {{ $activeTab === 'shifts'
                        ? 'bg-primary text-on-primary shadow-sm'
                        : 'text-on-surface-variant hover:bg-surface-variant' }}">
            <span class="material-symbols-outlined text-sm align-middle mr-1">calendar_month</span>
            Jadwal Shift
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 1: MANAJEMEN STAF (REQ-FUNC-039)                                 --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'staff')
        <div class="flex flex-col gap-4">

            {{-- Toolbar --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
                {{-- Search --}}
                <div class="relative flex-1 max-w-sm">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
                    <input wire:model.live.debounce.300ms="staffSearch"
                           id="input-staff-search"
                           type="text"
                           placeholder="Cari nama atau email..."
                           class="w-full pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md focus:outline-none focus:ring-2 focus:ring-secondary-container">
                </div>
                {{-- Tombol Tambah Staf --}}
                <button wire:click="openCreateStaff"
                        id="btn-tambah-staf"
                        class="bg-primary-container text-on-primary-container hover:opacity-90 active:scale-95 transition-all px-5 py-2.5 rounded-lg flex items-center gap-2 shadow-sm font-label-caps font-bold whitespace-nowrap">
                    <span class="material-symbols-outlined text-sm">person_add</span>
                    Tambah Staf
                </button>
            </div>

            {{-- Tabel Staf --}}
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
                {{-- Header Tabel --}}
                <div class="hidden md:grid grid-cols-12 gap-4 p-4 border-b border-outline-variant bg-surface-container/50 text-label-caps font-label-caps text-on-surface-variant uppercase tracking-wider">
                    <div class="col-span-3">Nama</div>
                    <div class="col-span-3">Email</div>
                    <div class="col-span-2 text-center">Role</div>
                    <div class="col-span-2 text-center">Status</div>
                    <div class="col-span-2 text-right">Aksi</div>
                </div>

                <div class="flex flex-col divide-y divide-outline-variant">
                    @forelse($staffList as $staff)
                        <div class="p-4 flex flex-col md:grid md:grid-cols-12 md:items-center gap-3 hover:bg-surface-container-lowest/50 transition-colors
                                    {{ ! $staff->is_active ? 'opacity-60 bg-surface-variant/20' : '' }}">

                            {{-- Nama & Inisial --}}
                            <div class="flex items-center gap-3 md:col-span-3">
                                <div class="w-9 h-9 rounded-full bg-primary text-on-primary flex items-center justify-center text-label-caps font-bold shrink-0">
                                    {{ $staff->initials() }}
                                </div>
                                <div>
                                    <p class="text-body-md font-bold">{{ $staff->name }}</p>
                                    <p class="text-label-caps text-on-surface-variant md:hidden">{{ $staff->email }}</p>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="hidden md:block md:col-span-3 text-body-sm text-on-surface-variant truncate">
                                {{ $staff->email }}
                            </div>

                            {{-- Role Badge --}}
                            <div class="md:col-span-2 flex justify-start md:justify-center">
                                @php
                                    $roleColors = [
                                        'kasir'     => 'bg-blue-100 text-blue-700',
                                        'kds'       => 'bg-orange-100 text-orange-700',
                                        'inventory' => 'bg-green-100 text-green-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-label-caps font-bold {{ $roleColors[$staff->role] ?? 'bg-surface text-on-surface' }}">
                                    {{ ucfirst($staff->role) }}
                                </span>
                            </div>

                            {{-- Status Aktif/Nonaktif (N9.1 toggle) --}}
                            <div class="md:col-span-2 flex justify-start md:justify-center">
                                <button wire:click="toggleActive({{ $staff->id }})"
                                        id="btn-toggle-{{ $staff->id }}"
                                        title="{{ $staff->is_active ? 'Nonaktifkan akun' : 'Aktifkan akun' }}"
                                        class="flex items-center gap-1.5 px-3 py-1 rounded-full text-label-caps font-bold transition-colors
                                            {{ $staff->is_active
                                                ? 'bg-secondary-container/20 text-secondary-container hover:bg-secondary-container/30'
                                                : 'bg-primary-container/20 text-primary-container hover:bg-primary-container/30' }}">
                                    <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1">
                                        {{ $staff->is_active ? 'check_circle' : 'cancel' }}
                                    </span>
                                    {{ $staff->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </div>

                            {{-- Aksi --}}
                            <div class="md:col-span-2 flex items-center justify-end gap-2">
                                <button wire:click="openEditStaff({{ $staff->id }})"
                                        id="btn-edit-staf-{{ $staff->id }}"
                                        class="p-2 text-on-surface-variant hover:text-secondary-container hover:bg-surface-variant rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <button wire:click="confirmDelete({{ $staff->id }}, 'staff')"
                                        id="btn-hapus-staf-{{ $staff->id }}"
                                        class="p-2 text-on-surface-variant hover:text-primary-container hover:bg-error-container rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center">
                            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">group_off</span>
                            <p class="text-on-surface-variant italic text-sm">Belum ada staf terdaftar.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                @if($staffList->hasPages())
                    <div class="border-t border-outline-variant px-4 py-3">
                        {{ $staffList->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- TAB 2: JADWAL SHIFT (REQ-FUNC-040)                                   --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($activeTab === 'shifts')
        <div class="flex flex-col gap-4">

            {{-- Toolbar --}}
            <div class="flex flex-col sm:flex-row gap-3 justify-between items-start sm:items-center">
                {{-- Filter Tanggal --}}
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-on-surface-variant">event</span>
                    <input wire:model.live="shiftDateFilter"
                           id="input-filter-tanggal"
                           type="date"
                           class="pl-10 pr-4 py-2.5 bg-surface-container-lowest border border-outline-variant rounded-lg text-body-md focus:outline-none focus:ring-2 focus:ring-secondary-container">
                </div>
                {{-- Tombol Tambah Shift --}}
                <button wire:click="openCreateShift"
                        id="btn-tambah-shift"
                        class="bg-primary-container text-on-primary-container hover:opacity-90 active:scale-95 transition-all px-5 py-2.5 rounded-lg flex items-center gap-2 shadow-sm font-label-caps font-bold whitespace-nowrap">
                    <span class="material-symbols-outlined text-sm">add</span>
                    Tambah Shift
                </button>
            </div>

            {{-- Tabel Shift (P9.3: dapat dirender sebagai daftar) --}}
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl shadow-sm overflow-hidden">
                <div class="hidden md:grid grid-cols-12 gap-4 p-4 border-b border-outline-variant bg-surface-container/50 text-label-caps font-label-caps text-on-surface-variant uppercase tracking-wider">
                    <div class="col-span-3">Staf</div>
                    <div class="col-span-2 text-center">Tanggal</div>
                    <div class="col-span-2 text-center">Waktu</div>
                    <div class="col-span-2 text-center">Posisi</div>
                    <div class="col-span-2">Catatan</div>
                    <div class="col-span-1 text-right">Aksi</div>
                </div>

                <div class="flex flex-col divide-y divide-outline-variant">
                    @forelse($shifts as $shift)
                        <div class="p-4 flex flex-col md:grid md:grid-cols-12 md:items-center gap-3 hover:bg-surface-container-lowest/50 transition-colors">

                            {{-- Nama Staf --}}
                            <div class="md:col-span-3 flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-secondary text-on-secondary flex items-center justify-center text-label-caps font-bold shrink-0 text-xs">
                                    {{ $shift->user->initials() }}
                                </div>
                                <span class="text-body-md font-medium">{{ $shift->user->name }}</span>
                            </div>

                            {{-- Tanggal Shift --}}
                            <div class="md:col-span-2 text-center">
                                <p class="text-body-sm font-bold">{{ $shift->shift_date->format('d/m/Y') }}</p>
                                <p class="text-label-caps text-on-surface-variant">{{ $shift->shift_date->translatedFormat('l') }}</p>
                            </div>

                            {{-- Waktu (start–end + durasi) --}}
                            <div class="md:col-span-2 text-center">
                                <p class="text-body-sm font-bold">
                                    {{ substr($shift->start_time, 0, 5) }} – {{ substr($shift->end_time, 0, 5) }}
                                </p>
                                <p class="text-label-caps text-on-surface-variant">{{ $shift->durationHours() }} jam</p>
                            </div>

                            {{-- Posisi Badge --}}
                            <div class="md:col-span-2 flex md:justify-center">
                                @php
                                    $posColors = [
                                        'kasir'     => 'bg-blue-100 text-blue-700',
                                        'inventory' => 'bg-green-100 text-green-700',
                                        'dapur'     => 'bg-orange-100 text-orange-700',
                                    ];
                                @endphp
                                <span class="px-2.5 py-1 rounded-full text-label-caps font-bold {{ $posColors[$shift->position] ?? '' }}">
                                    {{ $shift->positionLabel() }}
                                </span>
                            </div>

                            {{-- Catatan --}}
                            <div class="md:col-span-2 text-body-sm text-on-surface-variant truncate">
                                {{ $shift->notes ?: '–' }}
                            </div>

                            {{-- Aksi --}}
                            <div class="md:col-span-1 flex items-center justify-end gap-1">
                                <button wire:click="openEditShift({{ $shift->id }})"
                                        id="btn-edit-shift-{{ $shift->id }}"
                                        class="p-2 text-on-surface-variant hover:text-secondary-container hover:bg-surface-variant rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                                <button wire:click="confirmDelete({{ $shift->id }}, 'shift')"
                                        id="btn-hapus-shift-{{ $shift->id }}"
                                        class="p-2 text-on-surface-variant hover:text-primary-container hover:bg-error-container rounded-lg transition-colors">
                                    <span class="material-symbols-outlined text-sm">delete</span>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center">
                            <span class="material-symbols-outlined text-5xl text-on-surface-variant/30 block mb-3">calendar_month</span>
                            <p class="text-on-surface-variant italic text-sm">
                                {{ $shiftDateFilter ? 'Tidak ada shift pada tanggal ini.' : 'Belum ada jadwal shift.' }}
                            </p>
                        </div>
                    @endforelse
                </div>

                @if($shifts->hasPages())
                    <div class="border-t border-outline-variant px-4 py-3">
                        {{ $shifts->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: FORM STAF                                                      --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showStaffForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-data x-on:keydown.escape.window="$wire.set('showStaffForm', false)">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-lg overflow-hidden"
                 @click.away="$wire.set('showStaffForm', false)">

                {{-- Header Modal --}}
                <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
                    <h2 class="font-bold text-headline-sm text-on-surface">
                        {{ $staffId ? 'Edit Akun Staf' : 'Tambah Staf Baru' }}
                    </h2>
                    <button wire:click="$set('showStaffForm', false)"
                            class="text-on-surface-variant hover:text-on-surface rounded-full p-1">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                {{-- Form Body --}}
                <form wire:submit.prevent="saveStaff" class="p-6 space-y-4">

                    {{-- Nama --}}
                    <div>
                        <label class="text-body-md font-medium text-on-surface mb-1 block">
                            Nama Lengkap <span class="text-error">*</span>
                        </label>
                        <input wire:model="name"
                               id="input-nama-staf"
                               type="text"
                               placeholder="Contoh: Budi Santoso"
                               class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                        @error('name') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="text-body-md font-medium text-on-surface mb-1 block">
                            Email <span class="text-error">*</span>
                        </label>
                        <input wire:model="email"
                               id="input-email-staf"
                               type="email"
                               placeholder="budi@geprekrejo.com"
                               class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                        @error('email') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Role & Status --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-body-md font-medium text-on-surface mb-1 block">
                                Role <span class="text-error">*</span>
                            </label>
                            <select wire:model="role"
                                    id="select-role-staf"
                                    class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                                <option value="kasir">Kasir</option>
                                <option value="kds">KDS Dapur</option>
                                <option value="inventory">Inventory</option>
                            </select>
                            @error('role') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-body-md font-medium text-on-surface mb-1 block">Status</label>
                            <label class="flex items-center gap-3 mt-2.5 cursor-pointer">
                                <input wire:model="is_active"
                                       id="checkbox-aktif-staf"
                                       type="checkbox"
                                       class="w-5 h-5 rounded accent-primary">
                                <span class="text-body-md">Akun Aktif</span>
                            </label>
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="text-body-md font-medium text-on-surface mb-1 block">
                            Password
                            @if($staffId)
                                <span class="text-on-surface-variant font-normal">(kosongkan jika tidak diubah)</span>
                            @else
                                <span class="text-error">*</span>
                            @endif
                        </label>
                        <input wire:model="password"
                               id="input-password-staf"
                               type="password"
                               placeholder="{{ $staffId ? '••••••••' : 'Min. 8 karakter' }}"
                               class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                        @error('password') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button"
                                wire:click="$set('showStaffForm', false)"
                                class="px-5 py-2 text-on-surface-variant hover:bg-surface-container rounded-lg font-medium">
                            Batal
                        </button>
                        <button type="submit"
                                id="btn-simpan-staf"
                                class="px-5 py-2 bg-primary text-on-primary hover:opacity-90 rounded-lg font-bold flex items-center gap-2">
                            <span wire:loading wire:target="saveStaff"
                                  class="material-symbols-outlined animate-spin text-base">sync</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: FORM SHIFT                                                     --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($showShiftForm)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
             x-data x-on:keydown.escape.window="$wire.set('showShiftForm', false)">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-lg overflow-hidden"
                 @click.away="$wire.set('showShiftForm', false)">

                <div class="px-6 py-4 border-b border-outline-variant flex justify-between items-center bg-surface">
                    <h2 class="font-bold text-headline-sm text-on-surface">
                        {{ $shiftId ? 'Edit Jadwal Shift' : 'Tambah Jadwal Shift' }}
                    </h2>
                    <button wire:click="$set('showShiftForm', false)"
                            class="text-on-surface-variant hover:text-on-surface rounded-full p-1">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <form wire:submit.prevent="saveShift" class="p-6 space-y-4">

                    {{-- Staf --}}
                    <div>
                        <label class="text-body-md font-medium text-on-surface mb-1 block">
                            Staf <span class="text-error">*</span>
                        </label>
                        <select wire:model="userId"
                                id="select-staf-shift"
                                class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                            <option value="">— Pilih Staf —</option>
                            @foreach($activeStaff as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} ({{ ucfirst($s->role) }})</option>
                            @endforeach
                        </select>
                        @error('userId') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal & Posisi --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-body-md font-medium text-on-surface mb-1 block">
                                Tanggal Shift <span class="text-error">*</span>
                            </label>
                            <input wire:model="shift_date"
                                   id="input-tanggal-shift"
                                   type="date"
                                   class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                            @error('shift_date') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-body-md font-medium text-on-surface mb-1 block">
                                Posisi <span class="text-error">*</span>
                            </label>
                            <select wire:model="position"
                                    id="select-posisi-shift"
                                    class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                                <option value="kasir">Kasir</option>
                                <option value="inventory">Inventory</option>
                                <option value="dapur">Dapur</option>
                            </select>
                            @error('position') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Waktu Mulai & Selesai --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-body-md font-medium text-on-surface mb-1 block">
                                Mulai <span class="text-error">*</span>
                            </label>
                            <input wire:model="start_time"
                                   id="input-mulai-shift"
                                   type="time"
                                   class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                            @error('start_time') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="text-body-md font-medium text-on-surface mb-1 block">
                                Selesai <span class="text-error">*</span>
                            </label>
                            <input wire:model="end_time"
                                   id="input-selesai-shift"
                                   type="time"
                                   class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface">
                            @error('end_time') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Catatan --}}
                    <div>
                        <label class="text-body-md font-medium text-on-surface mb-1 block">Catatan (opsional)</label>
                        <textarea wire:model="notes"
                                  id="textarea-catatan-shift"
                                  rows="2"
                                  placeholder="Misal: Shift pagi pengganti libur..."
                                  class="w-full px-4 py-2 bg-surface-container-low border border-outline-variant rounded-lg focus:ring-2 focus:ring-primary text-on-surface resize-none"></textarea>
                        @error('notes') <p class="text-error text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button"
                                wire:click="$set('showShiftForm', false)"
                                class="px-5 py-2 text-on-surface-variant hover:bg-surface-container rounded-lg font-medium">
                            Batal
                        </button>
                        <button type="submit"
                                id="btn-simpan-shift"
                                class="px-5 py-2 bg-primary text-on-primary hover:opacity-90 rounded-lg font-bold flex items-center gap-2">
                            <span wire:loading wire:target="saveShift"
                                  class="material-symbols-outlined animate-spin text-base">sync</span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: KONFIRMASI HAPUS                                               --}}
    {{-- ══════════════════════════════════════════════════════════════════════ --}}
    @if($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-surface-container-lowest rounded-2xl shadow-xl w-full max-w-sm p-6 text-center">
                <div class="w-14 h-14 rounded-full bg-error-container/30 flex items-center justify-center mx-auto mb-4">
                    <span class="material-symbols-outlined text-3xl text-primary-container" style="font-variation-settings:'FILL' 1">
                        warning
                    </span>
                </div>
                <h3 class="font-bold text-headline-sm text-on-surface mb-2">Konfirmasi Hapus</h3>
                <p class="text-body-md text-on-surface-variant mb-1">
                    Anda akan menghapus
                    <strong class="text-on-surface">{{ $deletingType === 'staff' ? 'staf' : 'shift' }}</strong>:
                </p>
                <p class="text-body-md font-bold text-primary-container mb-6">"{{ $deletingLabel }}"</p>
                <p class="text-label-caps text-on-surface-variant mb-6">Tindakan ini tidak dapat dibatalkan.</p>

                <div class="flex gap-3">
                    <button wire:click="cancelDelete"
                            id="btn-batal-hapus"
                            class="flex-1 py-2.5 rounded-xl border border-outline-variant text-on-surface hover:bg-surface-container font-medium">
                        Batal
                    </button>
                    <button wire:click="executeDelete"
                            id="btn-konfirmasi-hapus"
                            class="flex-1 py-2.5 rounded-xl bg-primary-container text-on-primary-container hover:opacity-90 font-bold">
                        <span wire:loading wire:target="executeDelete"
                              class="material-symbols-outlined animate-spin text-base align-middle mr-1">sync</span>
                        Hapus
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
