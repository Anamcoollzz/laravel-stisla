@props(['worker'])

@php
    $statusClasses = match ($worker['status']) {
        'Available' => 'bg-teal text-white',
        'In Training' => 'bg-gold text-navy-dark',
        default => 'bg-slate-700 text-white',
    };

    $detailUrl = route('worker.detail', $worker['slug']);

    $waMessage = sprintf(
        'Hello, I am interested in candidate %s (%s) - %s. Profile URL: %s. I would like to know more about this candidate.',
        $worker['name'],
        $worker['code'],
        $worker['category_label'],
        $detailUrl
    );

    $searchBlob = strtolower(implode(' ', [
        $worker['name'],
        $worker['code'],
        $worker['origin'],
        $worker['origin_region'],
        $worker['category'],
        $worker['category_label'],
        implode(' ', $worker['skills']),
    ]));
@endphp

<article {{ $attributes->merge([
    'class' => 'group flex flex-col overflow-hidden rounded-2xl border border-border-subtle bg-white shadow-xs transition duration-300 hover:-translate-y-1 hover:border-teal/40 hover:shadow-md',
    'data-worker-card' => 'true',
    'data-name' => $worker['name'],
    'data-code' => $worker['code'],
    'data-category' => $worker['category'],
    'data-experience' => $worker['experience_bracket'],
    'data-age-range' => $worker['age_bracket'],
    'data-origin' => $worker['origin_region'],
    'data-status' => $worker['status'],
    'data-created' => $worker['created_at'],
    'data-experience-years' => $worker['experience_years'],
    'data-search' => $searchBlob,
]) }}>
    {{-- Consistent Aspect-Ratio Photo Header --}}
    <div class="relative aspect-4/5 overflow-hidden bg-surface">
        <img
            src="{{ $worker['photo_url'] ?? asset($worker['photo']) }}"
            alt="{{ $worker['name'] }} — {{ $worker['category_label'] }}"
            loading="lazy"
            class="h-full w-full object-cover object-top transition duration-500 group-hover:scale-105"
            onerror="this.onerror=null;this.src='{{ asset('lp-assets/images/general/placeholder-worker.svg') }}';"
        >

        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold shadow-xs {{ $statusClasses }}">
            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
            {{ $worker['status'] }}
        </span>

        <span class="absolute top-3 right-3 rounded-lg bg-navy-dark/85 px-2.5 py-1 text-[11px] font-bold tracking-wide text-white backdrop-blur-xs">
            {{ $worker['code'] }}
        </span>
    </div>

    {{-- Candidate Body --}}
    <div class="flex flex-1 flex-col p-5">
        <div class="mb-2">
            <span class="inline-block rounded-lg bg-teal-light px-2.5 py-1 text-xs font-semibold text-teal">
                {{ $worker['category_label'] }}
            </span>
        </div>

        <h3 class="text-lg font-bold text-navy-dark transition group-hover:text-teal">
            <a href="{{ $detailUrl }}" class="focus-visible:outline-none focus-visible:underline">
                {{ $worker['name'] }}
            </a>
        </h3>

        <p class="mt-1 inline-flex items-center gap-1.5 text-xs font-medium text-ink-muted">
            <i data-lucide="map-pin" class="h-3.5 w-3.5 text-teal shrink-0" aria-hidden="true"></i>
            {{ $worker['origin'] }}
        </p>

        {{-- Age & Experience Grid --}}
        <div class="mt-4 grid grid-cols-2 divide-x divide-border-subtle rounded-xl border border-border-subtle bg-surface py-2.5 text-center">
            <div class="px-2">
                <span class="block text-[11px] font-medium text-ink-muted">Age</span>
                <span class="text-sm font-bold text-navy-dark">{{ $worker['age'] }} Years</span>
            </div>
            <div class="px-2">
                <span class="block text-[11px] font-medium text-ink-muted">Experience</span>
                <span class="text-sm font-bold text-navy-dark">{{ $worker['experience_label'] }}</span>
            </div>
        </div>

        {{-- Skills Summary --}}
        <div class="mt-3.5 flex flex-wrap gap-1.5">
            @foreach (array_slice($worker['skills'], 0, 2) as $skill)
                <span class="rounded-full border border-border-subtle bg-surface px-2.5 py-1 text-[11px] font-medium text-ink">
                    {{ $skill }}
                </span>
            @endforeach
        </div>

        {{-- Action Footer --}}
        <div class="mt-auto pt-5 flex items-center gap-2">
            <a
                href="{{ $detailUrl }}"
                class="inline-flex flex-1 items-center justify-center rounded-xl bg-navy px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-navy-dark focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-navy"
            >
                View Details
            </a>
            <x-whatsapp-button
                variant="card"
                :message="$waMessage"
                label="WhatsApp"
                aria-label="Contact via WhatsApp about {{ $worker['name'] }}"
            />
        </div>
    </div>
</article>
