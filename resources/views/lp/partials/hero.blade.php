<section class="relative overflow-hidden bg-navy-dark py-16 sm:py-20 lg:py-24">
    {{-- Subtle Corporate Radial Highlight --}}
    <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(21,154,145,0.22),transparent_48%),radial-gradient(circle_at_10%_85%,rgba(213,166,74,0.14),transparent_42%)]"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 items-center gap-12 lg:grid-cols-12">
            {{-- Left Column: Headline, Description, CTAs & Trust Indicators --}}
            <div class="lg:col-span-6">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3.5 py-1.5 text-xs font-bold uppercase tracking-wider text-gold">
                    <i data-lucide="shield-check" class="h-3.5 w-3.5" aria-hidden="true"></i>
                    Trusted Manpower Recruitment Agency
                </span>

                <h1 class="mt-5 text-3xl font-extrabold leading-[1.15] tracking-tight text-white sm:text-4xl lg:text-5xl">
                    Connecting You With Trusted and Qualified Professionals
                </h1>

                <p class="mt-5 max-w-xl text-base leading-relaxed text-white/80 sm:text-lg">
                    We help families and businesses find reliable, skilled, and carefully selected manpower through a professional recruitment and placement process.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3.5">
                    <x-primary-button :href="route('catalog')" variant="gold">
                        <i data-lucide="users" class="h-4 w-4" aria-hidden="true"></i>
                        <span>Explore Our Workers</span>
                    </x-primary-button>

                    <x-primary-button :href="route('contact')" variant="outline-light">
                        <span>Contact Our Team</span>
                        <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
                    </x-primary-button>
                </div>

                {{-- Trust Indicators --}}
                <div class="mt-10 grid grid-cols-1 gap-3 border-t border-white/15 pt-7 sm:grid-cols-3">
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-white/85">
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold">
                            <i data-lucide="award" class="h-4 w-4" aria-hidden="true"></i>
                        </span>
                        <span>Experienced Recruitment Team</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-white/85">
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold">
                            <i data-lucide="user-check" class="h-4 w-4" aria-hidden="true"></i>
                        </span>
                        <span>Carefully Selected Candidates</span>
                    </div>
                    <div class="flex items-center gap-2.5 text-xs font-semibold text-white/85">
                        <span class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white/10 text-gold">
                            <i data-lucide="headphones" class="h-4 w-4" aria-hidden="true"></i>
                        </span>
                        <span>Professional Assistance</span>
                    </div>
                </div>
            </div>

            {{-- Right Column: Framed Recruitment Photography --}}
            <div class="lg:col-span-6">
                <div class="relative">
                    <div class="overflow-hidden rounded-2xl border border-white/15 shadow-2xl">
                        <img
                            src="{{ asset('lp-assets/images/general/hero-main.jpg') }}"
                            alt="Trusted nanny and family together in a warm modern Indonesian home"
                            class="h-[340px] w-full object-cover sm:h-[420px] lg:h-[460px]"
                            fetchpriority="high"
                        >
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-4 rounded-xl border border-white/15 bg-white/95 p-4 shadow-lg sm:absolute sm:bottom-5 sm:left-5 sm:mt-0 sm:max-w-xs">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-teal-light text-teal">
                                <i data-lucide="check-circle-2" class="h-5 w-5" aria-hidden="true"></i>
                            </span>
                            <div>
                                <p class="text-xs font-bold text-navy-dark">Structured Screening</p>
                                <p class="text-[11px] text-ink-muted">Verified profiles ready for interview</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
