<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
        <script>
            (function() {
                const t = localStorage.getItem('flux-theme') ?? 'system';
                if (t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            })();
        </script>
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-[#a00008] bg-[#bc000a] dark:border-[#a00008] dark:bg-[#bc000a]">
            <flux:sidebar.header>
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav>

                {{-- ── Utama ──────────────────────────────────── --}}
                <div class="sidebar-group-separator">
                    <flux:sidebar.group :heading="__('Utama')" class="grid">
                        <flux:sidebar.item icon="home"
                            :href="route('dashboard')"
                            :current="request()->routeIs('dashboard')"
                            wire:navigate>
                            {{ __('Dashboard') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </div>

                {{-- ── Manajemen Menu ─────────────────────────── --}}
                <div class="sidebar-group-separator">
                    <flux:sidebar.group :heading="__('Manajemen Menu')" class="grid">
                        <flux:sidebar.item icon="fire"
                            :href="route('menu.index')"
                            :current="request()->routeIs('menu.*')"
                            wire:navigate>
                            {{ __('Menu Makanan') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="tag"
                            :href="route('kategori.index')"
                            :current="request()->routeIs('kategori.*')"
                            wire:navigate>
                            {{ __('Kategori') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </div>

                {{-- ── Transaksi ───────────────────────────────── --}}
                <div class="sidebar-group-separator">
                    <flux:sidebar.group :heading="__('Transaksi')" class="grid">
                        <flux:sidebar.item icon="shopping-cart"
                            :href="route('pesanan.index')"
                            :current="request()->routeIs('pesanan.*')"
                            wire:navigate>
                            {{ __('Pesanan') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="users"
                            :href="route('member.index')"
                            :current="request()->routeIs('member.*')"
                            wire:navigate>
                            {{ __('Member') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </div>

                {{-- ── Operasional ─────────────────────────────── --}}
                <div class="sidebar-group-separator">
                    <flux:sidebar.group :heading="__('Operasional')" class="grid">
                        <flux:sidebar.item icon="archive-box"
                            :href="route('stok.index')"
                            :current="request()->routeIs('stok.*')"
                            wire:navigate>
                            {{ __('Stok Bahan') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="receipt-percent"
                            :href="route('pengeluaran.index')"
                            :current="request()->routeIs('pengeluaran.*')"
                            wire:navigate>
                            {{ __('Pengeluaran') }}
                        </flux:sidebar.item>

                        <flux:sidebar.item icon="ticket"
                            :href="route('voucher.index')"
                            :current="request()->routeIs('voucher.*')"
                            wire:navigate>
                            {{ __('Voucher') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </div>

                {{-- ── Laporan ─────────────────────────────────── --}}
                <div class="sidebar-group-separator">
                    <flux:sidebar.group :heading="__('Laporan')" class="grid">
                        <flux:sidebar.item icon="chart-bar"
                            :href="route('laporan.index')"
                            :current="request()->routeIs('laporan.*')"
                            wire:navigate>
                            {{ __('Laporan Penjualan') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </div>

                {{-- ── Staf & Shift ────────────────────────────── --}}
                <div class="sidebar-group-separator">
                    <flux:sidebar.group :heading="__('Staf')" class="grid">
                        <flux:sidebar.item icon="user-group"
                            :href="route('staf.index')"
                            :current="request()->routeIs('staf.*')"
                            wire:navigate>
                            {{ __('Staf & Shift') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                </div>

            </flux:sidebar.nav>

            <flux:spacer />

            {{-- ── Theme Toggle ────────────────────────────────────────────── --}}
            <div x-data="{
                theme: localStorage.getItem('flux-theme') ?? 'system',
                apply(t) {
                    this.theme = t;
                    localStorage.setItem('flux-theme', t);
                    document.documentElement.classList.toggle('dark',
                        t === 'dark' || (t === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches)
                    );
                },
                init() { this.apply(this.theme); }
            }" class="px-3 pb-2">
                <p class="mb-1.5 px-1 text-[10px] font-semibold uppercase tracking-wide text-zinc-400">Tema</p>
                <div class="flex rounded-xl border border-zinc-200 bg-zinc-100 p-1 dark:border-zinc-700 dark:bg-zinc-800">
                    <button @click="apply('light')"
                        :class="theme==='light' ? 'bg-white shadow text-orange-500 dark:bg-zinc-700' : 'text-zinc-400 hover:text-zinc-600'"
                        class="flex flex-1 items-center justify-center gap-1 rounded-lg py-1.5 text-xs font-semibold transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M17.657 17.657l-.707-.707M6.343 6.343l-.707-.707M12 7a5 5 0 110 10A5 5 0 0112 7z"/></svg>
                        Terang
                    </button>
                    <button @click="apply('dark')"
                        :class="theme==='dark' ? 'bg-white shadow text-orange-500 dark:bg-zinc-700' : 'text-zinc-400 hover:text-zinc-600'"
                        class="flex flex-1 items-center justify-center gap-1 rounded-lg py-1.5 text-xs font-semibold transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                        Gelap
                    </button>
                    <button @click="apply('system')"
                        :class="theme==='system' ? 'bg-white shadow text-orange-500 dark:bg-zinc-700' : 'text-zinc-400 hover:text-zinc-600'"
                        class="flex flex-1 items-center justify-center gap-1 rounded-lg py-1.5 text-xs font-semibold transition">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Auto
                    </button>
                </div>
            </div>

            {{-- Profile / User menu — display:block agar tidak ada kotak blank --}}
            <div class="sidebar-user-area">
                <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
            </div>
        </flux:sidebar>

        <style>
            /* ════════════════════════════════════════════════════════════
               SIDEBAR — Merah background, semua teks PUTIH SOLID
               Hanya active/hover yang berubah ke kuning + teks gelap
               ════════════════════════════════════════════════════════════ */

            /* 1. Background sidebar: merah */
            [data-flux-sidebar] {
                --flux-sidebar-bg: #bc000a !important;
                background-color: #bc000a !important;
            }

            /* 2. SEMUA elemen di dalam sidebar → PUTIH SOLID */
            [data-flux-sidebar],
            [data-flux-sidebar] * {
                color: #ffffff !important;
            }

            /* 2b. Group headings: putih, lebih besar, bold */
            [data-flux-sidebar] [data-flux-sidebar-heading],
            [data-flux-sidebar] .text-xs.font-medium,
            [data-flux-sidebar] .text-xs.text-zinc-500,
            [data-flux-sidebar] .text-xs.font-medium.text-zinc-500 {
                color: #ffffff !important;
                font-size: 0.8125rem !important;   /* 13px — lebih besar dari 10px default */
                font-weight: 700 !important;        /* bold */
                letter-spacing: 0.08em !important;
                text-transform: uppercase !important;
            }

            /* 3. Item nav: transparan, teks putih, font lebih besar & tebal */
            [data-flux-sidebar] [data-flux-sidebar-item],
            [data-flux-sidebar] [data-sidebar-item] {
                background-color: transparent !important;
                color: #ffffff !important;
                border-radius: 0.625rem;
                font-size: 0.9375rem !important;   /* 15px — lebih besar */
                font-weight: 600 !important;        /* semi-bold — lebih tebal */
                letter-spacing: 0.01em;
                transition: background-color 0.15s ease, color 0.15s ease;
            }

            /* 4. Icon di dalam item: ikut warna teks */
            [data-flux-sidebar] [data-flux-sidebar-item] svg,
            [data-flux-sidebar] [data-flux-sidebar-item] [data-flux-icon] {
                color: currentColor !important;
            }

            /* 5. Hover: kuning + teks GELAP (semua anak juga gelap) */
            [data-flux-sidebar] [data-flux-sidebar-item]:hover,
            [data-flux-sidebar] [data-flux-sidebar-item]:hover * {
                background-color: #fabd00 !important;
                color: #3b2400 !important;
            }

            /* 6. Active/current: kuning solid + teks GELAP + bold */
            [data-flux-sidebar] [data-flux-sidebar-item][data-current],
            [data-flux-sidebar] [data-flux-sidebar-item][data-current] *,
            [data-flux-sidebar] [data-flux-sidebar-item][aria-current="page"],
            [data-flux-sidebar] [data-flux-sidebar-item][aria-current="page"] * {
                background-color: #fabd00 !important;
                color: #3b2400 !important;
                font-weight: 700 !important;
            }

            /* 7. Border & separator */
            [data-flux-sidebar] [data-flux-sidebar-header] {
                border-bottom: 1px solid rgba(255,255,255,0.2) !important;
            }
            [data-flux-sidebar] [data-flux-separator],
            [data-flux-sidebar] hr {
                border-color: rgba(255,255,255,0.2) !important;
            }

            /* 8. ── TEMA TOGGLE: gelap di light mode, putih di dark mode ── */

            /* Container tema toggle: bg abu-abu (ikut bawaan Tailwind) */
            [data-flux-sidebar] .px-3.pb-2 .flex.rounded-xl {
                background-color: #f3f4f6 !important;   /* zinc-100 light */
                border-color: #e5e7eb !important;
            }
            .dark [data-flux-sidebar] .px-3.pb-2 .flex.rounded-xl {
                background-color: #3f3f46 !important;   /* zinc-700 dark */
                border-color: #52525b !important;
            }

            /* Tombol Terang / Gelap / Auto: teks GELAP di light mode */
            [data-flux-sidebar] .px-3.pb-2 button,
            [data-flux-sidebar] .px-3.pb-2 button * {
                color: #374151 !important;   /* gray-700 */
            }

            /* Tombol Terang / Gelap / Auto: teks PUTIH di dark mode */
            .dark [data-flux-sidebar] .px-3.pb-2 button,
            .dark [data-flux-sidebar] .px-3.pb-2 button * {
                color: #f3f4f6 !important;   /* gray-100 */
            }

            /* Label "Tema": putih bold */
            [data-flux-sidebar] .px-3.pb-2 > p {
                color: #ffffff !important;
                font-size: 0.8125rem !important;
                font-weight: 700 !important;
                letter-spacing: 0.08em !important;
            }
            .dark [data-flux-sidebar] .px-3.pb-2 > p {
                color: rgba(255,255,255,0.8) !important;
            }

            /* Hover tombol tema: kuning di light mode */
            [data-flux-sidebar] .px-3.pb-2 button:hover,
            [data-flux-sidebar] .px-3.pb-2 button:hover * {
                background-color: #fabd00 !important;
                color: #3b2400 !important;
            }
            /* Hover tombol tema: abu gelap di dark mode */
            .dark [data-flux-sidebar] .px-3.pb-2 button:hover,
            .dark [data-flux-sidebar] .px-3.pb-2 button:hover * {
                background-color: #52525b !important;
                color: #ffffff !important;
            }

            /* 9. ── PROFIL AKUN OWNER: gelap di light mode, putih di dark ── */

            /* 9. ── PROFIL AKUN OWNER: custom Alpine dropdown ────────────── */
            .sidebar-user-area {
                /* isolasi agar CSS global sidebar tidak bocor ke dalam */
            }

            /* ── DROPDOWN POPUP: bg putih SOLID + teks gelap ───────────────── */
            /* Pastikan SELURUH popup tidak mewarisi background merah sidebar */
            [data-flux-sidebar] .sidebar-dropdown-panel {
                background: transparent !important;  /* outer: transparan agar shadow terlihat */
            }
            /* Inner wrapper: background putih solid */
            [data-flux-sidebar] .sidebar-dropdown-panel > div {
                background: #ffffff !important;
            }
            /* Semua teks di dalam popup → hitam gelap */
            [data-flux-sidebar] .sidebar-dropdown-panel *:not(svg):not(path) {
                color: #374151 !important;
                background-color: transparent;
            }
            /* Override khusus: teks & icon logout → merah */
            [data-flux-sidebar] .sidebar-dropdown-panel button span,
            [data-flux-sidebar] .sidebar-dropdown-panel button svg {
                color: #dc2626 !important;
            }
            /* Override khusus: header area abu terang */
            [data-flux-sidebar] .sidebar-dropdown-panel > div > div:first-child {
                background: #f9fafb !important;
            }

            /* 10. ── SEPARATOR KUNING antar group menu ───────────────────── */
            .sidebar-group-separator {
                border-bottom: 1px solid rgba(253, 192, 3, 0.35);
                padding-bottom: 0.5rem;
                margin-bottom: 0.25rem;
            }
            .sidebar-group-separator:last-child {
                border-bottom: none;
            }

            /* 11. ── sidebar-user-area: transparan, tidak ada bg putih ────── */
            .sidebar-user-area,
            .sidebar-user-area > div {
                background: transparent !important;
                box-shadow: none !important;
            }
        </style>

        {{-- ── Mobile Header ─────────────────────────────────── --}}
        <flux:header class="lg:hidden sticky top-0 z-50 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700 shadow-sm">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />
                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        {{-- Flash notification global (sukses/error/warning/info) --}}
        <x-flash-notification />

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
