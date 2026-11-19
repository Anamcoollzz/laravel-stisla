@props(['variant' => 'dark'])

@php
    $isLight = $variant === 'light';
@endphp

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal rounded-lg']) }}>
    <img
        src="{{ asset('lp-assets/images/general/logo.svg') }}"
        alt="{{ config('company.legal_name') }} Logo"
        width="40"
        height="40"
        class="h-10 w-10 shrink-0 rounded-xl shadow-xs transition-transform duration-300 group-hover:scale-105"
    >
    <div class="leading-tight">
        <span class="block text-sm sm:text-base font-bold tracking-tight {{ $isLight ? 'text-white' : 'text-navy-dark' }}">
            {{ config('company.legal_name') }}
        </span>
        <span class="hidden sm:block text-[11px] font-semibold uppercase tracking-widest {{ $isLight ? 'text-gold' : 'text-ink-muted' }}">
            Manpower &amp; Care Solutions
        </span>
    </div>
</a>
