<section class="bg-surface py-16 sm:py-20">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-2xl border border-navy/10 bg-navy-dark px-6 py-12 text-white shadow-xl sm:px-12 lg:py-16">
            <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_90%_15%,rgba(213,166,74,0.18),transparent_40%)]"></div>

            <div class="relative flex flex-col items-start justify-between gap-8 lg:flex-row lg:items-center">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gold">
                        Consultation &amp; Placement Support
                    </span>
                    <h2 class="mt-4 text-2xl font-bold tracking-tight text-white sm:text-3xl lg:text-4xl">
                        Looking for the Right Person to Support Your Needs?
                    </h2>
                    <p class="mt-3 text-base leading-relaxed text-white/75">
                        Let our team help you find suitable manpower through a professional recruitment process.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3.5 shrink-0">
                    <x-whatsapp-button variant="hero" label="Contact Us via WhatsApp" />
                    <x-primary-button :href="route('catalog')" variant="outline-light">
                        <span>Explore Worker Catalog</span>
                    </x-primary-button>
                </div>
            </div>
        </div>
    </div>
</section>
