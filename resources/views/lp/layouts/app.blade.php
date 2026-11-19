<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('company.legal_name') . ' — ' . config('company.tagline'))</title>
    <meta name="description" content="@yield('meta_description', 'PT Permata Kasih Anugerah is a trusted manpower recruitment and worker placement agency connecting families with qualified domestic helpers (ART), babysitters, nannies, and caregivers.')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph Metadata --}}
    <meta property="og:site_name" content="{{ config('company.legal_name') }}">
    <meta property="og:title" content="@yield('og_title', config('company.legal_name') . ' — Trusted Manpower Recruitment')">
    <meta property="og:description" content="@yield('meta_description', 'Trusted manpower recruitment agency for trained domestic helpers, babysitters, nannies, caregivers, and elderly care.')">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('lp-assets/images/general/hero-main.jpg'))">

    <link rel="icon" type="image/svg+xml" href="{{ asset('lp-assets/images/general/logo.svg') }}">
    <link rel="alternate icon" href="{{ asset('favicon.ico') }}">

    {{-- Google Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'], 'lp-build')
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col bg-white text-ink antialiased overflow-x-hidden">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-50 focus:rounded-lg focus:bg-navy-dark focus:px-4 focus:py-2 focus:text-sm focus:font-semibold focus:text-white">
        Skip to main content
    </a>

    <x-header />

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    <x-footer />

    <x-whatsapp-button variant="floating" label="WhatsApp Consult" />

    <script src="https://unpkg.com/lucide@0.468.0/dist/umd/lucide.min.js" defer></script>
    <script src="{{ asset('lp-assets/js/app.js') }}" defer></script>
    @stack('scripts')
</body>
</html>
