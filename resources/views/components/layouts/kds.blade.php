<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="kds-html">
    <head>
        @include('partials.head')
        <style>
            html, body { height: 100%; margin: 0; padding: 0; }

            /* Smooth transition saat switch theme */
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
    <body class="h-screen overflow-hidden antialiased">

        {{ $slot }}

        @fluxScripts
        @stack('scripts')

        <script>
            // ── Global KDS Theme Controller ─────────────────────────────
            // Menggunakan window global agar bisa dipanggil dari mana saja
            // tanpa bergantung pada Alpine.js scope hierarchy.

            window.kdsIsDark = function () {
                return document.documentElement.classList.contains('dark');
            };

            window.kdsToggleTheme = function () {
                const html = document.documentElement;

                // Tambah class transisi sementara
                html.classList.add('kds-transitioning');
                setTimeout(() => html.classList.remove('kds-transitioning'), 300);

                // Toggle dark class
                const nowDark = html.classList.toggle('dark');
                localStorage.setItem('kds-theme', nowDark ? 'dark' : 'light');

                // Perbarui semua tombol toggle yang ada di halaman
                document.querySelectorAll('[data-kds-theme-icon]').forEach(el => {
                    el.textContent = nowDark ? 'light_mode' : 'dark_mode';
                });
                document.querySelectorAll('[data-kds-theme-label]').forEach(el => {
                    el.textContent = nowDark ? 'Day' : 'Night';
                });
                document.querySelectorAll('[data-kds-theme-btn]').forEach(btn => {
                    if (nowDark) {
                        btn.classList.remove('border-gray-300', 'bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                        btn.classList.add('border-yellow-500/40', 'bg-yellow-500/10', 'text-yellow-400', 'hover:bg-yellow-500/20');
                    } else {
                        btn.classList.remove('border-yellow-500/40', 'bg-yellow-500/10', 'text-yellow-400', 'hover:bg-yellow-500/20');
                        btn.classList.add('border-gray-300', 'bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                    }
                });
            };

            // Inisialisasi state tombol setelah DOM siap
            document.addEventListener('DOMContentLoaded', function () {
                const isDark = window.kdsIsDark();
                document.querySelectorAll('[data-kds-theme-icon]').forEach(el => {
                    el.textContent = isDark ? 'light_mode' : 'dark_mode';
                });
                document.querySelectorAll('[data-kds-theme-label]').forEach(el => {
                    el.textContent = isDark ? 'Day' : 'Night';
                });
                document.querySelectorAll('[data-kds-theme-btn]').forEach(btn => {
                    if (isDark) {
                        btn.classList.add('border-yellow-500/40', 'bg-yellow-500/10', 'text-yellow-400', 'hover:bg-yellow-500/20');
                        btn.classList.remove('border-gray-300', 'bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                    } else {
                        btn.classList.add('border-gray-300', 'bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
                        btn.classList.remove('border-yellow-500/40', 'bg-yellow-500/10', 'text-yellow-400', 'hover:bg-yellow-500/20');
                    }
                });
            });

            // Re-inisialisasi setelah Livewire poll (DOM morphing)
            document.addEventListener('livewire:update', function () {
                const isDark = window.kdsIsDark();
                document.querySelectorAll('[data-kds-theme-icon]').forEach(el => {
                    el.textContent = isDark ? 'light_mode' : 'dark_mode';
                });
                document.querySelectorAll('[data-kds-theme-label]').forEach(el => {
                    el.textContent = isDark ? 'Day' : 'Night';
                });
            });
        </script>
    </body>
</html>
