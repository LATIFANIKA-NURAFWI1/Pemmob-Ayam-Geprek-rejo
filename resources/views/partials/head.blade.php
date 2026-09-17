<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' — Ayam Geprek Rejo' : 'Ayam Geprek Rejo' }}
</title>
<meta name="description" content="Sistem manajemen Ayam Geprek Rejo — Lezat, Gurih, Halal">

{{-- Favicon: Logo Geprek Rejo --}}
<link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
<link rel="apple-touch-icon" href="{{ asset('images/logo.png') }}">
<meta name="theme-color" content="#bc000a">

{{-- Google Fonts: Plus Jakarta Sans (display) + Inter (body) + Material Symbols --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
