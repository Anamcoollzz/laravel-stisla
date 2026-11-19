<footer class="bg-navy-dark text-slate-300 mt-auto">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 gap-10 md:grid-cols-2 lg:grid-cols-12">
            {{-- Brand & Summary --}}
            <div class="lg:col-span-4 space-y-4">
                <x-logo variant="light" />
                <p class="text-sm leading-relaxed text-slate-300/85">
                    A professional manpower recruitment and placement company connecting households and businesses with carefully selected Domestic Helpers (ART), Babysitters, Nannies, Caregivers, and Elderly Care specialists.
                </p>
                <div class="flex items-center gap-2.5 pt-1">
                    <a href="{{ config('company.social_links.instagram') }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white transition hover:border-gold hover:text-gold">
                        <i data-lucide="instagram" class="h-4 w-4" aria-hidden="true"></i>
                    </a>
                    <a href="{{ config('company.social_links.facebook') }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white transition hover:border-gold hover:text-gold">
                        <i data-lucide="facebook" class="h-4 w-4" aria-hidden="true"></i>
                    </a>
                    <a href="{{ config('company.social_links.youtube') }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="inline-flex h-9 w-9 items-center justify-center rounded-full border border-white/15 bg-white/5 text-white transition hover:border-gold hover:text-gold">
                        <i data-lucide="youtube" class="h-4 w-4" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div class="lg:col-span-2">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Quick Links</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="transition hover:text-gold">Home</a></li>
                    <li><a href="{{ route('about') }}" class="transition hover:text-gold">About Us</a></li>
                    <li><a href="{{ route('services') }}" class="transition hover:text-gold">Services</a></li>
                    <li><a href="{{ route('catalog') }}" class="transition hover:text-gold">Worker Catalog</a></li>
                    <li><a href="{{ route('gallery') }}" class="transition hover:text-gold">Gallery</a></li>
                    <li><a href="{{ route('contact') }}" class="transition hover:text-gold">Contact Us</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div class="lg:col-span-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Services</h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('catalog', ['category' => 'ART']) }}" class="transition hover:text-gold">Domestic Helper (ART)</a></li>
                    <li><a href="{{ route('catalog', ['category' => 'Babysitter']) }}" class="transition hover:text-gold">Babysitter</a></li>
                    <li><a href="{{ route('catalog', ['category' => 'Nanny']) }}" class="transition hover:text-gold">Nanny</a></li>
                    <li><a href="{{ route('catalog', ['category' => 'Caregiver']) }}" class="transition hover:text-gold">Caregiver</a></li>
                    <li><a href="{{ route('catalog', ['category' => 'Elderly Care']) }}" class="transition hover:text-gold">Elderly Care</a></li>
                    <li><a href="{{ route('catalog', ['category' => 'Other']) }}" class="transition hover:text-gold">Other Manpower Services</a></li>
                </ul>
            </div>

            {{-- Contact Information --}}
            <div class="lg:col-span-3">
                <h3 class="text-xs font-bold uppercase tracking-wider text-white mb-4">Contact Information</h3>
                <ul class="space-y-3 text-sm mb-5">
                    <li class="flex items-start gap-2.5">
                        <i data-lucide="map-pin" class="h-4 w-4 text-gold shrink-0 mt-0.5" aria-hidden="true"></i>
                        <span class="text-slate-300/90 leading-relaxed">{{ config('company.address') }}</span>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i data-lucide="phone" class="h-4 w-4 text-gold shrink-0" aria-hidden="true"></i>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', config('company.phone_display')) }}" class="transition hover:text-gold">
                            {{ config('company.phone_display') }}
                        </a>
                    </li>
                    <li class="flex items-center gap-2.5">
                        <i data-lucide="mail" class="h-4 w-4 text-gold shrink-0" aria-hidden="true"></i>
                        <a href="mailto:{{ config('company.email') }}" class="break-all transition hover:text-gold">
                            {{ config('company.email') }}
                        </a>
                    </li>
                </ul>
                <x-whatsapp-button variant="footer" label="WhatsApp Consultation" />
            </div>
        </div>
    </div>

    <div class="border-t border-white/10 bg-black/20 py-4">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-2 px-4 text-xs text-slate-400 sm:flex-row sm:px-6 lg:px-8">
            <p>&copy; {{ date('Y') }} {{ config('company.legal_name') }}. All Rights Reserved.</p>
            <p>Professional Manpower Recruitment &amp; Placement Agency</p>
        </div>
    </div>
</footer>
