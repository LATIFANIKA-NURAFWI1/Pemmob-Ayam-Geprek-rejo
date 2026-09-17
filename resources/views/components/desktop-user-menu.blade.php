{{-- ── Desktop User Menu (Alpine.js, fixed position overlay) ─── --}}
<div
    x-data="{ open: false }"
    @click.outside="open = false"
    class="relative hidden lg:block w-full px-2 pb-2"
>
    {{-- ── Trigger button ─────────────────────────────────────── --}}
    <button
        x-ref="trigger"
        @click="open = !open"
        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all cursor-pointer border"
        style="background: rgba(255,255,255,0.12); border-color: rgba(255,255,255,0.2);"
    >
        {{-- Avatar / Inisial --}}
        <div class="w-9 h-9 rounded-lg flex items-center justify-center text-sm font-bold shrink-0"
             style="background: #fabd00; color: #3b2400;">
            {{ auth()->user()->initials() }}
        </div>

        {{-- Nama --}}
        <div class="flex-1 text-left min-w-0">
            <p class="text-sm font-semibold truncate leading-tight" style="color:#ffffff !important;">
                {{ auth()->user()->name }}
            </p>
            <p class="text-[10px] uppercase tracking-wide" style="color:rgba(255,255,255,0.6) !important;">Owner</p>
        </div>

        {{-- Chevron --}}
        <svg class="w-4 h-4 shrink-0 transition-transform duration-200"
             :class="open ? 'rotate-180' : ''"
             style="color: rgba(255,255,255,0.6);"
             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
        </svg>
    </button>

    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0 scale-95"
        style="display:none; z-index:9999; min-width:220px; position:absolute; bottom:calc(100% + 8px); left:0; right:0;"
        class="sidebar-dropdown-panel"
    >
        {{-- Wrapper: bg putih SOLID, shadow kuat, tidak tembus merah sidebar --}}
        <div style="background:#ffffff !important; border-radius:1rem; box-shadow:0 8px 32px rgba(0,0,0,0.28), 0 2px 8px rgba(0,0,0,0.12); border:1px solid #e5e7eb; overflow:hidden;">
            {{-- Header info --}}
            <div style="padding:0.75rem 1rem; border-bottom:1px solid #f3f4f6; display:flex; align-items:center; gap:0.75rem; background:#f9fafb;">
                <div style="width:2.5rem; height:2.5rem; border-radius:0.625rem; display:flex; align-items:center; justify-content:center; font-size:0.875rem; font-weight:700; flex-shrink:0; background:#fabd00; color:#3b2400;">
                    {{ auth()->user()->initials() }}
                </div>
                <div style="min-width:0; flex:1;">
                    <p style="font-size:0.875rem; font-weight:600; color:#111827 !important; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ auth()->user()->name }}
                    </p>
                    <p style="font-size:0.75rem; color:#6b7280 !important; margin:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                        {{ auth()->user()->email }}
                    </p>
                </div>
            </div>

            {{-- Menu items --}}
            <div style="padding:0.375rem 0; background:#ffffff !important;">
                <a href="{{ route('profile.edit') }}"
                   wire:navigate
                   @click="open = false"
                   style="display:flex; align-items:center; gap:0.75rem; padding:0.625rem 1rem; color:#374151 !important; text-decoration:none; transition:background 0.15s;"
                   onmouseover="this.style.background='#f9fafb'"
                   onmouseout="this.style.background='transparent'">
                    <svg style="width:1rem; height:1rem; flex-shrink:0; color:#6b7280;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span style="font-size:0.875rem; font-weight:500; color:#374151 !important;">Pengaturan Akun</span>
                </a>

                <form method="POST" action="{{ route('logout') }}" style="width:100%; margin:0;">
                    @csrf
                    <button type="submit"
                            data-test="logout-button"
                            style="width:100%; display:flex; align-items:center; gap:0.75rem; padding:0.625rem 1rem; background:transparent; border:none; cursor:pointer; transition:background 0.15s; text-align:left;"
                            onmouseover="this.style.background='#fef2f2'"
                            onmouseout="this.style.background='transparent'">
                        <svg style="width:1rem; height:1rem; flex-shrink:0; color:#dc2626;" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        <span style="font-size:0.875rem; font-weight:500; color:#dc2626 !important;">Log out</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
