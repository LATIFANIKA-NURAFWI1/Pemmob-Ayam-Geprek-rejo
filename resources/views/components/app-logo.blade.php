@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand name="Geprek Rejo" class="text-white! [&_span]:text-white! [&_strong]:text-white!" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md">
            <img src="{{ asset('images/logo.png') }}" class="h-8 w-auto object-contain drop-shadow-sm" alt="Logo">
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand name="Geprek Rejo" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md">
            <img src="{{ asset('images/logo.png') }}" class="h-8 w-auto object-contain drop-shadow-sm" alt="Logo">
        </x-slot>
    </flux:brand>
@endif
