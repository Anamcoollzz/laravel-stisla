@props([
    'items' => [],
    'tone' => 'light',
])

@php
    $linkClass = $tone === 'dark' ? 'text-white/75 hover:text-gold' : 'text-ink-muted hover:text-navy';
    $sepClass = $tone === 'dark' ? 'text-white/40' : 'text-ink-muted/50';
    $currentClass = $tone === 'dark' ? 'text-gold font-semibold' : 'text-navy-dark font-semibold';
@endphp

<nav aria-label="Breadcrumb" {{ $attributes }}>
    <ol class="flex flex-wrap items-center gap-2 text-xs sm:text-sm">
        <li>
            <a href="{{ route('home') }}" class="transition {{ $linkClass }}">Home</a>
        </li>
        @foreach ($items as $item)
            <li aria-hidden="true" class="{{ $sepClass }}">/</li>
            <li>
                @if (!empty($item['url']))
                    <a href="{{ $item['url'] }}" class="transition {{ $linkClass }}">{{ $item['label'] }}</a>
                @else
                    <span class="{{ $currentClass }}" aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
