@php
    $navLinks = [
        ['label' => 'Home', 'route' => 'home', 'active' => request()->routeIs('home')],
        ['label' => 'About Us', 'route' => 'about', 'active' => request()->routeIs('about')],
        ['label' => 'Services', 'route' => 'services', 'active' => request()->routeIs('services')],
        ['label' => 'Worker Catalog', 'route' => 'catalog', 'active' => request()->routeIs('catalog') || request()->routeIs('worker.detail')],
        ['label' => 'Gallery', 'route' => 'gallery', 'active' => request()->routeIs('gallery')],
        ['label' => 'Contact', 'route' => 'contact', 'active' => request()->routeIs('contact')],
    ];
@endphp

{{-- Top Corporate Info Bar --}}
<div class="hidden border-b border-white/10 bg-navy-dark py-2 text-xs text-white/80 lg:block">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-5">
            <span class="inline-flex items-center gap-1.5">
                <i data-lucide="shield-check" class="h-3.5 w-3.5 text-gold" aria-hidden="true"></i>
                {{ config('company.license_number') }}
            </span>
            <span class="text-white/25">|</span>
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', config('company.phone_display')) }}" class="inline-flex items-center gap-1.5 transition hover:text-gold">
                <i data-lucide="phone" class="h-3.5 w-3.5 text-gold" aria-hidden="true"></i>
                {{ config('company.phone_display') }}
            </a>
            <a href="mailto:{{ config('company.email') }}" class="inline-flex items-center gap-1.5 transition hover:text-gold">
                <i data-lucide="mail" class="h-3.5 w-3.5 text-gold" aria-hidden="true"></i>
                {{ config('company.email') }}
            </a>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-white/70">Mon–Sat: 08:00 – 17:00 WIB</span>
            <div class="flex items-center gap-2.5">
                <a href="{{ config('company.social_links.instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="transition hover:text-gold">
                    <i data-lucide="instagram" class="h-3.5 w-3.5" aria-hidden="true"></i>
                </a>
                <a href="{{ config('company.social_links.facebook') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="transition hover:text-gold">
                    <i data-lucide="facebook" class="h-3.5 w-3.5" aria-hidden="true"></i>
                </a>
                <a href="{{ config('company.social_links.youtube') }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="transition hover:text-gold">
                    <i data-lucide="youtube" class="h-3.5 w-3.5" aria-hidden="true"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Sticky Primary Navbar --}}
<header id="siteHeader" class="sticky top-0 z-40 border-b border-border-subtle bg-white/95 backdrop-blur-md transition-shadow duration-300">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">
        <x-logo />

        {{-- Desktop Navigation --}}
        <nav class="hidden items-center gap-1 lg:flex" aria-label="Primary Navigation">
            @foreach ($navLinks as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="relative px-3.5 py-2 text-sm font-semibold transition {{ $link['active'] ? 'text-navy-dark' : 'text-ink-muted hover:text-navy-dark' }}"
                >
                    {{ $link['label'] }}
                    @if ($link['active'])
                        <span class="absolute inset-x-3.5 -bottom-0.5 h-0.5 rounded-full bg-teal"></span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Desktop WhatsApp CTA + Mobile Controls --}}
        <div class="flex items-center gap-2.5">
            <div class="hidden lg:block">
                <x-whatsapp-button variant="header" label="WhatsApp Consult" />
            </div>

            <div class="lg:hidden">
                <x-whatsapp-button variant="header" label="WhatsApp" class="px-3 py-2 text-xs" />
            </div>

            <button
                id="mobileMenuToggle"
                type="button"
                class="inline-flex items-center justify-center rounded-xl border border-border-subtle bg-white p-2.5 text-navy-dark transition hover:bg-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal lg:hidden"
                aria-controls="mobileNavigationMenu"
                aria-expanded="false"
                aria-label="Toggle navigation menu"
            >
                <i data-lucide="menu" class="h-5 w-5" aria-hidden="true"></i>
            </button>
        </div>
    </div>

    {{-- Mobile Navigation Drawer --}}
    <div id="mobileNavigationMenu" class="hidden border-t border-border-subtle bg-white lg:hidden">
        <nav class="mx-auto flex max-w-7xl flex-col gap-1 px-4 py-4 sm:px-6" aria-label="Mobile Navigation">
            @foreach ($navLinks as $link)
                <a
                    href="{{ route($link['route']) }}"
                    class="rounded-xl px-4 py-3 text-sm font-semibold transition {{ $link['active'] ? 'bg-teal-light text-teal' : 'text-ink hover:bg-surface hover:text-navy-dark' }}"
                >
                    {{ $link['label'] }}
                </a>
            @endforeach
            <div class="mt-2 pt-3 border-t border-border-subtle">
                <x-whatsapp-button variant="primary" label="Consult Our Team on WhatsApp" class="w-full" />
            </div>
        </nav>
    </div>
</header>
