@extends('lp.layouts.app')

@section('title', 'Recruitment Services — ' . config('company.legal_name'))
@section('meta_description', 'Explore Domestic Helper (ART), Babysitter, Nanny, Caregiver, Elderly Care, and Other Manpower Services offered by PT Permata Kasih Anugerah.')

@section('content')
    {{-- Page Hero & Breadcrumb --}}
    <section class="bg-navy-dark py-16 text-white lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Services']]" />
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                Our Recruitment Services
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">
                Six specialized manpower placement categories structured around practical preparation, transparent candidate profiles, and attentive client coordination.
            </p>
        </div>
    </section>

    {{-- Detailed 6 Service Categories --}}
    <section class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-7xl space-y-12 px-4 sm:px-6 lg:px-8">
            @foreach ($services as $index => $service)
                <article id="{{ $service['slug'] }}" class="scroll-mt-24 overflow-hidden rounded-2xl border border-border-subtle bg-surface shadow-xs">
                    <div class="grid grid-cols-1 lg:grid-cols-12">
                        <div class="lg:col-span-5 {{ $index % 2 === 1 ? 'lg:order-2' : '' }}">
                            <img
                                src="{{ $service['image_url'] ?? asset($service['image']) }}"
                                alt="{{ $service['title'] }}"
                                loading="lazy"
                                class="h-72 w-full object-cover object-top lg:h-full"
                                onerror="this.onerror=null;this.src='{{ asset('lp-assets/images/general/placeholder-gallery.svg') }}';"
                            >
                        </div>

                        <div class="p-7 sm:p-10 lg:col-span-7 {{ $index % 2 === 1 ? 'lg:order-1' : '' }}">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-teal-light text-teal">
                                    <i data-lucide="{{ $service['icon'] }}" class="h-5 w-5" aria-hidden="true"></i>
                                </span>
                                <span class="text-xs font-bold uppercase tracking-wider text-teal">{{ $service['subtitle'] }}</span>
                            </div>

                            <h2 class="mt-3 text-2xl font-bold text-navy-dark sm:text-3xl">{{ $service['title'] }}</h2>
                            <p class="mt-3 text-sm leading-relaxed text-ink-muted sm:text-base">{{ $service['description'] }}</p>

                            <div class="mt-6 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-navy-dark">Main Responsibilities</h3>
                                    <ul class="mt-3 space-y-2 text-sm text-ink-muted">
                                        @foreach ($service['responsibilities'] as $responsibility)
                                            <li class="flex items-start gap-2">
                                                <i data-lucide="check" class="h-4 w-4 text-teal shrink-0 mt-0.5" aria-hidden="true"></i>
                                                <span>{{ $responsibility }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>

                                <div>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-navy-dark">Key Benefits</h3>
                                    <ul class="mt-3 space-y-2 text-sm text-ink-muted">
                                        @foreach ($service['benefits'] as $benefit)
                                            <li class="flex items-start gap-2">
                                                <i data-lucide="shield-check" class="h-4 w-4 text-gold shrink-0 mt-0.5" aria-hidden="true"></i>
                                                <span>{{ $benefit }}</span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <h3 class="mt-5 text-xs font-bold uppercase tracking-wider text-navy-dark">Relevant Skills</h3>
                                    <div class="mt-2.5 flex flex-wrap gap-1.5">
                                        @foreach ($service['skills'] as $skill)
                                            <span class="rounded-full border border-border-subtle bg-white px-2.5 py-1 text-xs font-medium text-ink">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex flex-wrap items-center gap-3 pt-6 border-t border-border-subtle">
                                <x-whatsapp-button
                                    variant="primary"
                                    :message="'Hello PT Permata Kasih Anugerah, I would like to consult regarding ' . $service['title'] . ' placement.'"
                                    label="Consult About This Service"
                                />
                                <x-primary-button :href="route('catalog', ['category' => $service['category_key']])" variant="outline">
                                    <span>View Available Candidates</span>
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    {{-- Recruitment Process --}}
    <section class="border-t border-border-subtle bg-surface py-20 lg:py-24">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-section-heading
                eyebrow="Recruitment Process"
                title="How Placement Coordination Works"
            />

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($processSteps as $step)
                    <div class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs">
                        <div class="flex items-center justify-between">
                            <span class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-navy text-sm font-extrabold text-white">
                                {{ $step['step'] }}
                            </span>
                            <i data-lucide="{{ $step['icon'] }}" class="h-5 w-5 text-teal" aria-hidden="true"></i>
                        </div>
                        <h3 class="mt-4 text-base font-bold text-navy-dark">{{ $step['title'] }}</h3>
                        <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- FAQ Accordion --}}
    <section class="bg-white py-20 lg:py-24">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <x-section-heading
                eyebrow="Questions & Answers"
                title="Service & Placement FAQs"
            />

            <div class="mt-10 space-y-3" data-faq-group>
                @foreach ($faqs as $index => $faq)
                    <div class="overflow-hidden rounded-2xl border border-border-subtle bg-white">
                        <h3>
                            <button
                                type="button"
                                data-faq-trigger
                                aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                aria-controls="svc-faq-panel-{{ $index }}"
                                class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left text-sm font-bold text-navy-dark transition hover:bg-surface sm:text-base"
                            >
                                <span>{{ $faq['question'] }}</span>
                                <i data-lucide="chevron-down" data-faq-icon class="h-5 w-5 shrink-0 text-teal transition-transform duration-200 {{ $index === 0 ? 'rotate-180' : '' }}" aria-hidden="true"></i>
                            </button>
                        </h3>
                        <div
                            id="svc-faq-panel-{{ $index }}"
                            data-faq-panel
                            class="{{ $index === 0 ? '' : 'hidden' }} border-t border-border-subtle bg-surface/60 px-6 py-4 text-sm leading-relaxed text-ink-muted"
                        >
                            {{ $faq['answer'] }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('lp.partials.cta')
@endsection
