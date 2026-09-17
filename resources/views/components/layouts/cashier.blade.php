<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="kds-html">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta name="theme-color" content="#bc000a" />
        <title>{{ filled($title ?? null) ? $title.' — Geprek Rejo' : 'Kasir — Geprek Rejo' }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">

        {{-- Google Fonts: Plus Jakarta Sans + Inter --}}
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

        {{-- Material Symbols --}}
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            .font-jakarta { font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, sans-serif; }
            .font-inter   { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
            .scrollbar-hide::-webkit-scrollbar { display: none; }
            .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
            html.kds-transitioning,
            html.kds-transitioning *,
            html.kds-transitioning *::before,
            html.kds-transitioning *::after {
                transition: background-color 0.25s ease, border-color 0.2s ease, color 0.2s ease !important;
            }
        </style>

        {{-- Terapkan tema SEBELUM render untuk hindari flash of wrong theme --}}
        <script>
            (function () {
                const isDark = localStorage.getItem('kds-theme') === 'dark';
                if (isDark) document.documentElement.classList.add('dark');
            })();
        </script>
    </head>
    <body class="font-inter h-screen overflow-hidden antialiased">

        {{ $slot }}

        @fluxScripts
        @stack('scripts')

        <script>
            // ── Global Theme Controller (sama seperti KDS layout) ────────────────
            window.kdsIsDark = function () {
                return document.documentElement.classList.contains('dark');
            };

            window.kdsToggleTheme = function () {
                const html = document.documentElement;
                html.classList.add('kds-transitioning');
                setTimeout(() => html.classList.remove('kds-transitioning'), 300);

                const nowDark = html.classList.toggle('dark');
                localStorage.setItem('kds-theme', nowDark ? 'dark' : 'light');

                document.querySelectorAll('[data-kds-theme-icon]').forEach(el => {
                    el.textContent = nowDark ? 'light_mode' : 'dark_mode';
                });
                document.querySelectorAll('[data-kds-theme-label]').forEach(el => {
                    el.textContent = nowDark ? 'Light' : 'Night';
                });
            };

            // Inisialisasi state tombol setelah DOM siap
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = window.kdsIsDark();
                document.querySelectorAll('[data-kds-theme-icon]').forEach(el => {
                    el.textContent = isDark ? 'light_mode' : 'dark_mode';
                });
                document.querySelectorAll('[data-kds-theme-label]').forEach(el => {
                    el.textContent = isDark ? 'Light' : 'Night';
                });
            });

            // Re-inisialisasi setelah Livewire poll (DOM morphing)
            document.addEventListener('livewire:update', function () {
                const isDark = window.kdsIsDark();
                document.querySelectorAll('[data-kds-theme-icon]').forEach(el => {
                    el.textContent = isDark ? 'light_mode' : 'dark_mode';
                });
                document.querySelectorAll('[data-kds-theme-label]').forEach(el => {
                    el.textContent = isDark ? 'Light' : 'Night';
                });
            });
        </script>
    </body>
</html>
