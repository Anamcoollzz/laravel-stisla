@props([
    'href' => null,
    'variant' => 'primary',
    'type' => 'button',
])

@php
    $styles = match ($variant) {
        'secondary' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-teal px-5 py-3 text-sm font-semibold text-white shadow-xs transition hover:bg-navy focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal',
        'gold' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-gold px-6 py-3.5 text-sm font-bold text-navy-dark shadow-sm transition hover:brightness-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold',
        'outline' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-border-subtle bg-white px-5 py-3 text-sm font-semibold text-navy-dark transition hover:border-navy hover:bg-surface focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-navy',
        'outline-light' => 'inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white',
        default => 'inline-flex items-center justify-center gap-2 rounded-xl bg-navy px-5 py-3 text-sm font-semibold text-white shadow-xs transition hover:bg-navy-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-navy',
    };
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $styles]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $styles]) }}>
        {{ $slot }}
    </button>
@endif
