@extends('lp.layouts.app')

@section('title', config('company.legal_name') . ' — Trusted Manpower Recruitment & Placement Agency')
@section('meta_description', 'PT Permata Kasih Anugerah connects families and businesses with trusted Domestic Helpers (ART), Babysitters, Nannies, Caregivers, and Elderly Care specialists.')

@section('content')
    {{-- 1. Hero Section --}}
    @include('lp.partials.hero')

    {{-- 2. Company Introduction --}}
    @php
        $intro = $companyIntro ?? null;
        $introImageUrl = $intro?->image_url ?? asset('lp-assets/images/general/about-training.jpg');
        $introImageAlt = $intro?->image_alt ?: ('Candidates attending practical orientation at ' . config('company.legal_name'));
        $introMissionLabel = $intro?->mission_label ?: 'Our Mission';
        $introMissionText = $intro?->mission_text ?: 'Delivering dependable, carefully screened household and care professionals with transparent communication.';
        $introEyebrow = $intro?->eyebrow ?: 'Company Introduction';
        $introTitle = $intro?->title ?: 'Your Trusted Partner in Manpower Solutions';
        $introParagraph1 = $intro?->paragraph_1 ?: ('Welcoming someone into your home or workplace requires confidence and clarity. At ' . config('company.legal_name') . ', we manage structured candidate screening, practical readiness orientation, and responsive placement coordination so you can hire with peace of mind.');
        $introParagraph2 = $intro?->paragraph_2;
        $introF1Icon = $intro?->feature_1_icon ?: 'shield-check';
        $introF1Title = $intro?->feature_1_title ?: 'Verified Profiles';
        $introF1Desc = $intro?->feature_1_description ?: 'Identity, reference, and readiness screening prior to listing.';
        $introF2Icon = $intro?->feature_2_icon ?: 'handshake';
        $introF2Title = $intro?->feature_2_title ?: 'Guided Placement';
        $introF2Desc = $intro?->feature_2_description ?: 'Personal consultant support from interview to onboarding.';
        $introPrimaryLabel = $intro?->primary_button_label ?: 'Learn More';
        $introPrimaryUrl = $intro?->primary_button_url ?: route('about');
        $introWaLabel = $intro?->whatsapp_button_label ?: 'Consult Our Team';
        $introWaMessage = $intro?->whatsapp_button_message;
    @endphp
    <section class="reveal-section bg-white py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
                <div class="lg:col-span-6">
                    <div class="relative">
                        <div class="overflow-hidden rounded-2xl border border-border-subtle shadow-md">
                            <img
                                src="{{ $introImageUrl }}"
                                alt="{{ $introImageAlt }}"
                                loading="lazy"
                                class="h-[340px] w-full object-cover sm:h-[420px]"
                            >
                        </div>
                        <div class="mt-4 rounded-2xl border border-border-subtle bg-navy-dark p-5 text-white shadow-lg sm:absolute sm:-bottom-6 sm:right-6 sm:mt-0 sm:max-w-xs">
                            <p class="text-xs font-bold uppercase tracking-wider text-gold">{{ $introMissionLabel }}</p>
                            <p class="mt-1.5 text-sm leading-relaxed text-white/85">
                                {{ $introMissionText }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6">
                    <x-section-heading
                        :eyebrow="$introEyebrow"
                        :title="$introTitle"
                        align="left"
                    />

                    <p class="mt-5 text-base leading-relaxed text-ink-muted">
                        {{ $introParagraph1 }}
                    </p>

                    @if ($introParagraph2)
                        <p class="mt-4 text-base leading-relaxed text-ink-muted">
                            {{ $introParagraph2 }}
                        </p>
                    @endif

                    <div class="mt-7 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="flex items-start gap-3 rounded-xl border border-border-subtle bg-surface p-4">
                            <i data-lucide="{{ $introF1Icon }}" class="h-5 w-5 text-teal shrink-0 mt-0.5" aria-hidden="true"></i>
                            <div>
                                <h3 class="text-sm font-bold text-navy-dark">{{ $introF1Title }}</h3>
                                <p class="mt-1 text-xs text-ink-muted">{{ $introF1Desc }}</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl border border-border-subtle bg-surface p-4">
                            <i data-lucide="{{ $introF2Icon }}" class="h-5 w-5 text-teal shrink-0 mt-0.5" aria-hidden="true"></i>
                            <div>
                                <h3 class="text-sm font-bold text-navy-dark">{{ $introF2Title }}</h3>
                                <p class="mt-1 text-xs text-ink-muted">{{ $introF2Desc }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <x-primary-button :href="$introPrimaryUrl" variant="primary">
                            <span>{{ $introPrimaryLabel }}</span>
                            <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
                        </x-primary-button>
                        <x-whatsapp-button variant="card" :label="$introWaLabel" :message="$introWaMessage" class="px-5 py-3 text-sm" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 3. Trust and Statistics --}}
    <section class="reveal-section border-y border-border-subtle bg-surface py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 overflow-hidden rounded-2xl border border-border-subtle bg-white shadow-xs sm:grid-cols-2 lg:grid-cols-4 lg:divide-x divide-y lg:divide-y-0 divide-border-subtle">
                @foreach ($stats as $stat)
                    <div class="p-7 sm:p-8">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-3xl font-extrabold tracking-tight text-navy-dark sm:text-4xl">
                                {{ $stat['value'] }}
                            </span>
                            <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-teal-light text-teal">
                                <i data-lucide="{{ $stat['icon'] }}" class="h-5 w-5" aria-hidden="true"></i>
                            </span>
                        </div>
                        <h3 class="mt-3 text-sm font-bold text-navy-dark">{{ $stat['label'] }}</h3>
                        <p class="mt-1 text-xs leading-relaxed text-ink-muted">{{ $stat['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- 4. Our Services (6 Categories) --}}
    <section class="reveal-section bg-white py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-section-heading
                eyebrow="Our Services"
                title="Specialized Manpower Categories"
                description="Explore six dedicated staffing categories tailored to household management, infant and child care, senior companionship, and private support."
            />

            <div class="mt-12 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($services as $service)
                    <x-service-card :service="$service" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- 5. Featured Worker Catalog --}}
    <section class="reveal-section border-t border-border-subtle bg-surface py-20 lg:py-28" id="featured-workers">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-end">
                <x-section-heading
                    eyebrow="Featured Candidates"
                    title="Meet Qualified Workers Ready for Placement"
                    description="Review candidate profiles, skills, and availability status, or open the full directory to search and filter."
                    align="left"
                />
                <x-primary-button :href="route('catalog')" variant="primary" class="shrink-0 self-start sm:self-end">
                    <span>Explore All Workers</span>
                    <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
                </x-primary-button>
            </div>

            <div class="mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($featuredWorkers as $worker)
                    <x-worker-card :worker="$worker" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- 6. Why Choose Us --}}
    <section class="reveal-section bg-navy-dark py-20 text-white lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-4">
                    <x-section-heading
                        eyebrow="Why Choose Us"
                        title="Built on Trust, Structure, and Personal Care"
                        description="Sample agency strengths demonstrating how we prioritize transparency, candidate readiness, and responsive client communication."
                        align="left"
                        tone="light"
                    />
                    <div class="mt-8">
                        <x-whatsapp-button variant="hero" label="Speak With a Consultant" />
                    </div>
                </div>

                <div class="lg:col-span-8">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        @foreach ($whyChooseUs as $item)
                            <div class="rounded-2xl border border-white/10 bg-white/5 p-6 transition hover:border-gold/40 hover:bg-white/10">
                                <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-white/10 text-gold">
                                    <i data-lucide="{{ $item['icon'] }}" class="h-5 w-5" aria-hidden="true"></i>
                                </span>
                                <h3 class="mt-4 text-base font-bold text-white">{{ $item['title'] }}</h3>
                                <p class="mt-2 text-sm leading-relaxed text-white/75">{{ $item['description'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 7. Recruitment Process (4-Step Timeline) --}}
    <section class="reveal-section bg-white py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-section-heading
                eyebrow="How It Works"
                title="A Clear Four-Step Recruitment Process"
                description="Structured coordination from initial consultation through post-placement assistance."
            />

            <div class="relative mt-14">
                <div class="hidden lg:block absolute top-7 left-[12.5%] right-[12.5%] h-0.5 bg-border-subtle"></div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($processSteps as $step)
                        <div class="relative rounded-2xl border border-border-subtle bg-surface p-6 transition hover:border-teal/40 hover:bg-white hover:shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-navy text-base font-extrabold text-white shadow-xs">
                                    {{ $step['step'] }}
                                </span>
                                <i data-lucide="{{ $step['icon'] }}" class="h-6 w-6 text-teal" aria-hidden="true"></i>
                            </div>
                            <h3 class="mt-5 text-lg font-bold text-navy-dark">{{ $step['title'] }}</h3>
                            <p class="mt-2 text-sm leading-relaxed text-ink-muted">{{ $step['description'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- 8. Testimonials --}}
    <section class="reveal-section border-t border-border-subtle bg-surface py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-section-heading
                eyebrow="Client Stories"
                title="Trusted by Families Across Greater Jakarta"
                description="Illustrative client testimonials showcasing typical placement experiences."
            />

            <div class="mt-12 grid grid-cols-1 gap-6 md:grid-cols-3">
                @foreach ($testimonials as $testimonial)
                    <x-testimonial-card :testimonial="$testimonial" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- 9. FAQ Accordion --}}
    <section class="reveal-section bg-white py-20 lg:py-28">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-12 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-5">
                    <x-section-heading
                        eyebrow="FAQ"
                        title="Frequently Asked Questions"
                        description="Clear answers about finding candidates, understanding profiles, and coordinating placement with our recruitment team."
                        align="left"
                    />
                    <div class="mt-8 rounded-2xl border border-border-subtle bg-surface p-6">
                        <h3 class="text-sm font-bold text-navy-dark">Still have questions?</h3>
                        <p class="mt-1.5 text-sm text-ink-muted">
                            Our consultants are available Monday through Saturday to help match candidates to your needs.
                        </p>
                        <div class="mt-4">
                            <x-whatsapp-button variant="primary" label="Ask on WhatsApp" class="w-full" />
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7">
                    <div class="space-y-3" data-faq-group>
                        @foreach ($faqs as $index => $faq)
                            <div class="overflow-hidden rounded-2xl border border-border-subtle bg-white transition">
                                <h3>
                                    <button
                                        type="button"
                                        data-faq-trigger
                                        aria-expanded="{{ $index === 0 ? 'true' : 'false' }}"
                                        aria-controls="faq-panel-{{ $index }}"
                                        class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left text-sm font-bold text-navy-dark transition hover:bg-surface sm:text-base"
                                    >
                                        <span>{{ $faq['question'] }}</span>
                                        <i data-lucide="chevron-down" data-faq-icon class="h-5 w-5 shrink-0 text-teal transition-transform duration-200 {{ $index === 0 ? 'rotate-180' : '' }}" aria-hidden="true"></i>
                                    </button>
                                </h3>
                                <div
                                    id="faq-panel-{{ $index }}"
                                    data-faq-panel
                                    class="{{ $index === 0 ? '' : 'hidden' }} border-t border-border-subtle bg-surface/60 px-6 py-4 text-sm leading-relaxed text-ink-muted"
                                >
                                    {{ $faq['answer'] }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 10. Final CTA --}}
    @include('lp.partials.cta')
@endsection
