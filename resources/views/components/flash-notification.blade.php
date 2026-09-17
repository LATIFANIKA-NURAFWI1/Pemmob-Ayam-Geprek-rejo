{{--
    Komponen flash notification terpusat.
    Mendukung:
      - session('status')  → sukses (hijau)
      - session('error')   → error (merah)
      - session('warning') → peringatan (kuning)
      - session('info')    → info (biru)

    Notifikasi muncul di TENGAH ATAS layar, lebar penuh di mobile,
    maks 480px di desktop. Auto hilang 3.5 detik.
--}}
@if(session()->hasAny(['status', 'success', 'error', 'warning', 'info']))
    @php
        $type    = session('error') ? 'error'
                 : (session('warning') ? 'warning'
                 : (session('info') ? 'info' : 'success'));
        $message = session('status') ?? session('success') ?? session('error') ?? session('warning') ?? session('info');
        $cfg = match($type) {
            'error'   => ['bg-red-500',    'border-red-600',    'text-red-100',    '✖'],
            'warning' => ['bg-amber-500',  'border-amber-600',  'text-amber-100',  '⚠'],
            'info'    => ['bg-blue-500',   'border-blue-600',   'text-blue-100',   'ℹ'],
            default   => ['bg-emerald-500','border-emerald-600','text-emerald-100', '✔'],
        };
    @endphp
    <div
        x-data="{ show: true }"
        x-show="show"
        x-init="setTimeout(() => show = false, 3500)"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-4"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-4"
        class="pointer-events-none fixed inset-x-0 top-4 z-[9999] flex justify-center px-4"
    >
        <div class="pointer-events-auto flex w-full max-w-lg items-center gap-3 rounded-2xl border {{ $cfg[1] }} {{ $cfg[0] }} px-5 py-4 text-white shadow-2xl">
            {{-- Icon --}}
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/20 text-base font-bold">
                {{ $cfg[3] }}
            </div>
            {{-- Message --}}
            <p class="flex-1 text-sm font-semibold">{{ $message }}</p>
            {{-- Close button --}}
            <button @click="show = false" class="ml-2 flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/20 text-white/80 transition hover:bg-white/30">
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>
@endif
