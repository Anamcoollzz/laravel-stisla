@props([
    'message' => null,
    'label' => 'WhatsApp Us',
    'variant' => 'primary',
    'url' => null,
])

@php
    $number = preg_replace('/[^0-9]/', '', (string) config('company.whatsapp_number', '6282299109766'));
    $text = $message ?: (string) config('company.default_whatsapp_message');
    $href = $url ?: ('https://wa.me/' . $number . '?text=' . rawurlencode($text));

    $variantClasses = match ($variant) {
        'header' => 'inline-flex items-center justify-center gap-2 rounded-xl bg-teal px-4 py-2.5 text-sm font-semibold text-white shadow-xs transition hover:bg-navy focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal',
        'hero' => 'inline-flex items-center justify-center gap-2.5 rounded-xl bg-gold px-6 py-3.5 text-sm font-bold text-navy-dark shadow-sm transition hover:brightness-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold',
        'card' => 'inline-flex items-center justify-center gap-1.5 rounded-xl border border-teal/30 bg-teal-light px-3.5 py-2.5 text-xs font-semibold text-teal transition hover:bg-teal hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal',
        'detail' => 'inline-flex items-center justify-center gap-2.5 rounded-xl bg-gold px-6 py-3.5 text-sm font-bold text-navy-dark shadow-sm transition hover:brightness-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold',
        'footer' => 'inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gold px-4 py-2.5 text-sm font-bold text-navy-dark transition hover:brightness-95 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gold',
        'floating' => 'fixed bottom-5 right-5 z-40 inline-flex items-center gap-2.5 rounded-full bg-navy-dark px-4 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/15 transition hover:-translate-y-0.5 hover:bg-teal focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal',
        default => 'inline-flex items-center justify-center gap-2 rounded-xl bg-teal px-5 py-3 text-sm font-semibold text-white shadow-xs transition hover:bg-navy focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal',
    };
@endphp

<a
    href="{{ $href }}"
    target="_blank"
    rel="noopener noreferrer"
    {{ $attributes->merge(['class' => $variantClasses]) }}
>
    <svg class="h-4 w-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"/>
    </svg>
    <span class="{{ $variant === 'floating' ? 'hidden sm:inline' : '' }}">{{ $label }}</span>
</a>
