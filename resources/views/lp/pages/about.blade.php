@extends('lp.layouts.app')

@section('title', 'About Us — ' . config('company.legal_name'))
@section('meta_description', 'Learn about PT Permata Kasih Anugerah, our background, vision, mission, corporate values, recruitment expertise, and training standards.')

@section('content')
    {{-- 1. Page Hero & Breadcrumb --}}
    <section class="bg-navy-dark py-16 text-white lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'About Us']]" />
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                About {{ config('company.legal_name') }}
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">
                Dedicated to raising standards in domestic and care manpower recruitment through structured candidate screening, orientation, and transparent placement guidance.
            </p>
        </div>
    </section>

    {{-- 2. Company Introduction --}}
    <section class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <x-section-heading
                        eyebrow="Company Introduction"
                        title="Connecting Households With Reliable Support"
                        align="left"
                    />
                    <p class="mt-5 text-base leading-relaxed text-ink-muted">
                        Based in Jatiasih, Bekasi, {{ config('company.legal_name') }} operates as a manpower recruitment and placement agency serving families and residential clients across Greater Jakarta and major Indonesian cities.
                    </p>
                    <p class="mt-4 text-base leading-relaxed text-ink-muted">
                        We focus on matching each family with the right Domestic Helper (ART), Babysitter, Nanny, Caregiver, Elderly Care companion, or Household Support worker through transparent candidate representation and attentive consultation.
                    </p>

                    <div class="mt-8 grid grid-cols-2 gap-4">
                        @foreach ($stats as $stat)
                            <div class="rounded-xl border border-border-subtle bg-surface p-4">
                                <span class="block text-2xl font-extrabold text-navy-dark">{{ $stat['value'] }}</span>
                                <span class="text-xs font-medium text-ink-muted">{{ $stat['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <div class="overflow-hidden rounded-2xl border border-border-subtle shadow-md">
                        <img
                            src="{{ asset('lp-assets/images/general/about-intro.jpg') }}"
                            alt="PT Permata Kasih Anugerah recruitment and family care support"
                            class="h-[360px] w-full object-cover sm:h-[420px]"
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Company Background --}}
    <section class="border-y border-border-subtle bg-surface py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-section-heading
                eyebrow="Company Background"
                title="Milestones & Continuous Growth"
                description="Illustrative company background highlighting our steady commitment to structured candidate readiness and ethical placement."
            />

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($milestones as $milestone)
                    <div class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs">
                        <span class="inline-block rounded-lg bg-navy px-3 py-1 text-xs font-bold text-gold">{{ $milestone['year'] }}</span>
                        <h3 class="mt-4 text-base font-bold text-navy-dark">{{ $milestone['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ $milestone['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 4. Vision & Mission --}}
    @php
        $vm = $visionMission ?? null;
        $visionEyebrow = $vm?->vision_eyebrow ?: 'Our Vision';
        $visionTitle = $vm?->vision_title ?: 'Setting a Dependable Standard in Manpower Placement';
        $visionDesc = $vm?->vision_description ?: 'To be recognized as a trusted, people-centered manpower recruitment partner—delivering qualified workers and fostering respectful, harmonious working relationships in every home.';
        $missionEyebrow = $vm?->mission_eyebrow ?: 'Our Mission';
        $missionTitle = $vm?->mission_title ?: 'How We Serve Families & Candidates';
        $missionPoints = $vm?->mission_points ?: [
            'Apply disciplined multi-step candidate screening, reference verification, and readiness checks before placement.',
            'Provide practical orientation in residential hygiene, infant care, senior assistance, and communication etiquette.',
            'Communicate candidate qualifications transparently while protecting sensitive personal privacy.',
            'Maintain responsive consultation and structured follow-up assistance after placement day.',
        ];
    @endphp
    <section class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                <div class="rounded-2xl bg-navy-dark p-8 text-white sm:p-10 lg:col-span-5">
                    <span class="inline-flex rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gold">
                        {{ $visionEyebrow }}
                    </span>
                    <h2 class="mt-4 text-2xl font-bold text-white sm:text-3xl">
                        {{ $visionTitle }}
                    </h2>
                    <p class="mt-4 text-sm leading-relaxed text-white/80 sm:text-base">
                        {{ $visionDesc }}
                    </p>
                </div>

                <div class="rounded-2xl border border-border-subtle bg-surface p-8 sm:p-10 lg:col-span-7">
                    <span class="inline-flex rounded-full bg-teal-light px-3 py-1 text-xs font-bold uppercase tracking-wider text-teal">
                        {{ $missionEyebrow }}
                    </span>
                    <h2 class="mt-4 text-2xl font-bold text-navy-dark sm:text-3xl">
                        {{ $missionTitle }}
                    </h2>
                    <ul class="mt-6 space-y-4">
                        @foreach ($missionPoints as $point)
                            <li class="flex items-start gap-3 text-sm leading-relaxed text-ink-muted sm:text-base">
                                <i data-lucide="check-circle-2" class="h-5 w-5 text-teal shrink-0 mt-0.5" aria-hidden="true"></i>
                                <span>{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. Company Values --}}
    <section class="border-t border-border-subtle bg-surface py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-section-heading
                eyebrow="Company Values"
                title="Principles That Guide Every Placement"
            />

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($teamValues as $value)
                    <div class="rounded-2xl border border-border-subtle bg-white p-6 text-center shadow-xs">
                        <span class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-xl bg-teal-light text-teal">
                            <i data-lucide="{{ $value['icon'] }}" class="h-6 w-6" aria-hidden="true"></i>
                        </span>
                        <h3 class="mt-4 text-base font-bold text-navy-dark">{{ $value['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ $value['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 6. Why Choose Us --}}
    <section class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-section-heading
                eyebrow="Why Choose Us"
                title="Our Core Recruitment Advantages"
            />

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($whyChooseUs as $advantage)
                    <div class="rounded-2xl border border-border-subtle bg-surface p-6">
                        <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white text-navy shadow-xs border border-border-subtle">
                            <i data-lucide="{{ $advantage['icon'] }}" class="h-5 w-5" aria-hidden="true"></i>
                        </span>
                        <h3 class="mt-4 text-base font-bold text-navy-dark">{{ $advantage['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ $advantage['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 7. Recruitment Expertise (Alternating Section) --}}
    <section class="border-y border-border-subtle bg-surface py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <div class="overflow-hidden rounded-2xl border border-border-subtle shadow-md">
                        <img
                            src="{{ asset('lp-assets/images/general/about-training.jpg') }}"
                            alt="Practical training classroom and candidate preparation"
                            loading="lazy"
                            class="h-[340px] w-full object-cover sm:h-[400px]"
                        >
                    </div>
                </div>
                <div class="lg:col-span-6">
                    <x-section-heading
                        eyebrow="Recruitment Expertise"
                        title="Structured Screening & Practical Orientation"
                        align="left"
                    />
                    <p class="mt-4 text-base leading-relaxed text-ink-muted">
                        Our recruitment team evaluates candidates not only on previous work history, but also on temperament, communication clarity, cleanliness habits, and practical readiness for real family environments.
                    </p>
                    <p class="mt-3 text-base leading-relaxed text-ink-muted">
                        Before candidates appear in our public catalog, they complete interviews and orientation modules tailored to domestic upkeep, newborn handling, toddler engagement, or senior care assistance.
                    </p>
                    <div class="mt-7 flex flex-wrap gap-3">
                        <x-primary-button :href="route('catalog')" variant="primary">Browse Candidate Catalog</x-primary-button>
                        <x-primary-button :href="route('services')" variant="outline">Explore Services</x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 8. Company Gallery Preview --}}
    <section class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                <x-section-heading
                    eyebrow="Gallery Preview"
                    title="Inside Our Training & Placement Activities"
                    align="left"
                />
                <x-primary-button :href="route('gallery')" variant="outline" class="shrink-0 self-start sm:self-end">
                    <span>View Full Gallery</span>
                    <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
                </x-primary-button>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($galleryPreview as $item)
                    <a href="{{ route('gallery') }}" class="group overflow-hidden rounded-2xl border border-border-subtle bg-white shadow-xs transition hover:-translate-y-1 hover:shadow-md">
                        <div class="aspect-16/10 overflow-hidden bg-surface">
                            <img
                                src="{{ $item['image_url'] ?? asset($item['image']) }}"
                                alt="{{ $item['title'] }}"
                                loading="lazy"
                                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                            >
                        </div>
                        <div class="p-5">
                            <span class="text-xs font-semibold text-teal">{{ $item['category'] }}</span>
                            <h3 class="mt-1 text-sm font-bold text-navy-dark">{{ $item['title'] }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 9. Final CTA --}}
    @include('lp.partials.cta')
@endsection
