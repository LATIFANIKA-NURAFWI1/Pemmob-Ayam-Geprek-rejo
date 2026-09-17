<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover" />
        <meta name="theme-color" content="#bc000a" />

        <title>{{ filled($title ?? null) ? $title.' — Geprek Rejo' : 'Geprek Rejo · Self Order' }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        {{-- Google Fonts: Plus Jakarta Sans (display) + Inter (body) --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        {{-- Vite: Tailwind CSS + app.js --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* Material Symbols — font-variation-settings tidak bisa via Tailwind */
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
                vertical-align: middle;
            }
            /* Scrollbar hide — ::-webkit-scrollbar tidak bisa via Tailwind v4 */
            .hide-scrollbar::-webkit-scrollbar { display: none; }
            .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
            /* Font utilities — didefinisikan langsung agar selalu terkompilasi */
            .font-jakarta { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
            .font-inter   { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        </style>
    </head>
    <body class="min-h-screen font-jakarta antialiased bg-[#f7f9ff] text-[#181c20]">

        {{ $slot }}

        {{-- @fluxScripts includes Livewire 4 + Alpine.js (needed for x-show/x-data/x-transition) --}}
        @fluxScripts
    </body>
</html>
