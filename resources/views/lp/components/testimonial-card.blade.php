@props(['testimonial'])

<article {{ $attributes->merge(['class' => 'flex flex-col rounded-2xl border border-border-subtle bg-white p-6 sm:p-7 shadow-xs transition duration-300 hover:-translate-y-1 hover:shadow-md']) }}>
    <div class="flex items-center justify-between gap-2 mb-4">
        <div class="flex items-center gap-1 text-gold" aria-label="{{ $testimonial['rating'] }} out of 5 stars">
            @for ($i = 0; $i < $testimonial['rating']; $i++)
                <svg class="h-4 w-4 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            @endfor
        </div>
        <span class="rounded-full bg-teal-light px-2.5 py-1 text-[11px] font-semibold text-teal">
            {{ $testimonial['service'] }}
        </span>
    </div>

    <blockquote class="flex-1 text-sm leading-relaxed text-ink">
        "{{ $testimonial['quote'] }}"
    </blockquote>

    <div class="mt-6 flex items-center gap-3 pt-4 border-t border-border-subtle">
        <img
            src="{{ $testimonial['avatar_url'] ?? asset($testimonial['avatar']) }}"
            alt="{{ $testimonial['name'] }}"
            width="44"
            height="44"
            loading="lazy"
            class="h-11 w-11 rounded-full object-cover object-top border border-border-subtle"
            onerror="this.onerror=null;this.src='{{ asset('lp-assets/images/general/placeholder-worker.svg') }}';"
        >
        <div>
            <strong class="block text-sm font-bold text-navy-dark">{{ $testimonial['name'] }}</strong>
            <span class="text-xs text-ink-muted">{{ $testimonial['location'] }}</span>
        </div>
    </div>
</article>
