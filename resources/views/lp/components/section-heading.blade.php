@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'align' => 'center',
    'tone' => 'dark',
])

@php
    $alignClasses = $align === 'left' ? 'text-left' : 'mx-auto max-w-2xl text-center';
    $eyebrowClasses = $tone === 'light'
        ? 'bg-white/10 text-gold border border-white/15'
        : 'bg-teal-light text-teal';
    $titleClasses = $tone === 'light' ? 'text-white' : 'text-navy-dark';
    $descClasses = $tone === 'light' ? 'text-white/75' : 'text-ink-muted';
@endphp

<div {{ $attributes->merge(['class' => $alignClasses]) }}>
    @if ($eyebrow)
        <span class="inline-flex items-center gap-1.5 rounded-full px-3.5 py-1 text-xs font-bold uppercase tracking-wider {{ $eyebrowClasses }}">
            {{ $eyebrow }}
        </span>
    @endif

    <h2 class="mt-3 text-2xl font-bold tracking-tight sm:text-3xl lg:text-4xl {{ $titleClasses }}">
        {{ $title }}
    </h2>

    @if ($description)
        <p class="mt-3 text-base leading-relaxed {{ $descClasses }}">
            {{ $description }}
        </p>
    @endif
</div>
