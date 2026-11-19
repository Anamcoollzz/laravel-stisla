@props(['service'])

<article {{ $attributes->merge(['class' => 'group flex flex-col overflow-hidden rounded-2xl border border-border-subtle bg-white shadow-xs transition duration-300 hover:-translate-y-1 hover:border-teal/40 hover:shadow-md']) }}>
    <div class="relative aspect-16/10 overflow-hidden bg-surface">
        <img
            src="{{ asset($service['image']) }}"
            alt="{{ $service['title'] }}"
            loading="lazy"
            class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-105"
            onerror="this.onerror=null;this.src='{{ asset('lp-assets/images/general/placeholder-gallery.svg') }}';"
        >
        <div class="absolute inset-0 bg-linear-to-t from-navy-dark/55 via-transparent to-transparent"></div>
        <div class="absolute bottom-3 left-4 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-white text-teal shadow-sm">
            <i data-lucide="{{ $service['icon'] }}" class="h-5 w-5" aria-hidden="true"></i>
        </div>
    </div>

    <div class="flex flex-1 flex-col p-6">
        <span class="text-xs font-bold uppercase tracking-wider text-teal">{{ $service['subtitle'] }}</span>
        <h3 class="mt-1 text-lg font-bold text-navy-dark">{{ $service['title'] }}</h3>
        <p class="mt-2 text-sm leading-relaxed text-ink-muted flex-1">
            {{ $service['short_description'] }}
        </p>

        <div class="mt-5 pt-4 border-t border-border-subtle flex items-center justify-between gap-3">
            <a
                href="{{ route('services') }}#{{ $service['slug'] }}"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-navy transition hover:text-teal"
            >
                <span>Learn More</span>
                <i data-lucide="arrow-right" class="h-4 w-4 transition-transform group-hover:translate-x-1" aria-hidden="true"></i>
            </a>

            <a
                href="{{ route('catalog', ['category' => $service['category_key']]) }}"
                class="text-xs font-semibold text-ink-muted transition hover:text-navy"
            >
                Browse Workers
            </a>
        </div>
    </div>
</article>
