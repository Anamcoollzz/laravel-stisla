@extends('lp.layouts.app')

@section('title', 'Contact Us — ' . config('company.legal_name'))
@section('meta_description', 'Contact PT Permata Kasih Anugerah via WhatsApp, phone, email, or our online consultation form. Visit our Jatiasih, Bekasi office during business hours.')

@section('content')
    {{-- Page Hero & Breadcrumb --}}
    <section class="bg-navy-dark py-16 text-white lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Contact']]" />
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                Contact Our Recruitment Team
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">
                Reach out for candidate inquiries, interview scheduling, or a direct WhatsApp consultation with our placement specialists.
            </p>
        </div>
    </section>

    <section class="bg-surface py-16 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Contact Information Cards --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-teal-light text-teal">
                        <i data-lucide="map-pin" class="h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <h2 class="mt-4 text-sm font-bold text-navy-dark">Company Address</h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-ink-muted">{{ $company['address'] }}</p>
                </div>

                <div class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-teal-light text-teal">
                        <i data-lucide="phone" class="h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <h2 class="mt-4 text-sm font-bold text-navy-dark">WhatsApp &amp; Phone</h2>
                    <p class="mt-1.5 text-sm font-bold text-navy-dark">
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $company['phone_display']) }}" class="hover:text-teal">
                            {{ $company['phone_display'] }}
                        </a>
                    </p>
                    <p class="mt-0.5 text-xs text-ink-muted">Office: {{ $company['secondary_phone'] }}</p>
                    <div class="mt-3">
                        <x-whatsapp-button variant="card" label="Chat on WhatsApp" />
                    </div>
                </div>

                <div class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-teal-light text-teal">
                        <i data-lucide="mail" class="h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <h2 class="mt-4 text-sm font-bold text-navy-dark">Email Address</h2>
                    <p class="mt-1.5 text-xs font-semibold text-navy-dark break-all">
                        <a href="mailto:{{ $company['email'] }}" class="hover:text-teal">{{ $company['email'] }}</a>
                    </p>
                    <p class="mt-1.5 text-xs text-ink-muted">For formal inquiries, partnership requests, and placement documentation.</p>
                </div>

                <div class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs">
                    <span class="inline-flex h-11 w-11 items-center justify-center rounded-xl bg-surface text-navy border border-border-subtle">
                        <i data-lucide="clock" class="h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <h2 class="mt-4 text-sm font-bold text-navy-dark">Business Hours</h2>
                    <ul class="mt-1.5 space-y-1 text-xs text-ink-muted">
                        <li>{{ $company['operating_hours']['weekday'] }}</li>
                        <li>{{ $company['operating_hours']['saturday'] }}</li>
                        <li>{{ $company['operating_hours']['sunday'] }}</li>
                    </ul>
                </div>
            </div>

            {{-- Contact Form & Google Maps Placeholder --}}
            <div class="mt-10 grid grid-cols-1 gap-8 lg:grid-cols-12">
                <div class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs sm:p-8 lg:col-span-6">
                    <span class="inline-block rounded-full bg-teal-light px-3 py-1 text-xs font-bold uppercase tracking-wider text-teal">
                        Send an Inquiry
                    </span>
                    <h2 class="mt-3 text-2xl font-bold text-navy-dark">Consultation Request Form</h2>
                    <p class="mt-2 text-sm text-ink-muted">
                        Fill out your contact details below for a quick inquiry preview, or connect immediately via WhatsApp.
                    </p>

                    <div id="contactSuccessState" class="mt-6 hidden rounded-xl border border-teal/30 bg-teal-light p-5 text-sm text-navy-dark" role="status">
                        <div class="flex items-start gap-3">
                            <i data-lucide="check-circle-2" class="h-5 w-5 text-teal shrink-0 mt-0.5" aria-hidden="true"></i>
                            <div>
                                <strong class="block font-bold">Inquiry Preview Prepared</strong>
                                <p class="mt-1 text-xs text-ink-muted">
                                    This demonstration form validated your message locally. For immediate consultant assistance, send your inquiry directly on WhatsApp below.
                                </p>
                                <a id="contactWhatsAppDirectLink" href="#" target="_blank" rel="noopener noreferrer" class="mt-3 inline-flex items-center gap-2 rounded-lg bg-teal px-4 py-2 text-xs font-semibold text-white hover:bg-navy">
                                    <span>Send This Message on WhatsApp</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <form
                        id="contactForm"
                        data-whatsapp-number="{{ preg_replace('/[^0-9]/', '', $company['whatsapp_number']) }}"
                        class="mt-6 space-y-4"
                        novalidate
                    >
                        <div>
                            <label for="contactFullName" class="block text-xs font-bold text-navy-dark mb-1.5">Full Name</label>
                            <input
                                id="contactFullName"
                                type="text"
                                name="full_name"
                                required
                                placeholder="e.g., Amanda Putri"
                                class="w-full rounded-xl border border-border-subtle bg-surface px-4 py-3 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none"
                            >
                            <p data-error-for="contactFullName" class="mt-1 hidden text-xs font-medium text-red-600">Please enter your full name.</p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label for="contactEmail" class="block text-xs font-bold text-navy-dark mb-1.5">Email Address</label>
                                <input
                                    id="contactEmail"
                                    type="email"
                                    name="email"
                                    required
                                    placeholder="name@example.com"
                                    class="w-full rounded-xl border border-border-subtle bg-surface px-4 py-3 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none"
                                >
                                <p data-error-for="contactEmail" class="mt-1 hidden text-xs font-medium text-red-600">Please enter a valid email address.</p>
                            </div>
                            <div>
                                <label for="contactPhone" class="block text-xs font-bold text-navy-dark mb-1.5">Phone Number</label>
                                <input
                                    id="contactPhone"
                                    type="tel"
                                    name="phone"
                                    required
                                    placeholder="0812-xxxx-xxxx"
                                    class="w-full rounded-xl border border-border-subtle bg-surface px-4 py-3 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none"
                                >
                                <p data-error-for="contactPhone" class="mt-1 hidden text-xs font-medium text-red-600">Please enter a valid phone number.</p>
                            </div>
                        </div>

                        <div>
                            <label for="contactSubject" class="block text-xs font-bold text-navy-dark mb-1.5">Subject</label>
                            <input
                                id="contactSubject"
                                type="text"
                                name="subject"
                                required
                                placeholder="e.g., Babysitter / Caregiver Inquiry"
                                class="w-full rounded-xl border border-border-subtle bg-surface px-4 py-3 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none"
                            >
                            <p data-error-for="contactSubject" class="mt-1 hidden text-xs font-medium text-red-600">Please provide a subject.</p>
                        </div>

                        <div>
                            <label for="contactMessage" class="block text-xs font-bold text-navy-dark mb-1.5">Message</label>
                            <textarea
                                id="contactMessage"
                                name="message"
                                rows="4"
                                required
                                placeholder="Describe the worker role, household location, and preferred schedule..."
                                class="w-full rounded-xl border border-border-subtle bg-surface px-4 py-3 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none"
                            ></textarea>
                            <p data-error-for="contactMessage" class="mt-1 hidden text-xs font-medium text-red-600">Please enter your message.</p>
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-navy px-6 py-3.5 text-sm font-semibold text-white transition hover:bg-navy-dark">
                            Submit Inquiry
                        </button>
                    </form>
                </div>

                {{-- Google Maps Embed Placeholder + Direct WhatsApp CTA --}}
                <div class="flex flex-col gap-6 lg:col-span-6">
                    <div class="flex flex-1 flex-col rounded-2xl border border-border-subtle bg-white p-5 shadow-xs">
                        <div class="flex items-center justify-between gap-3 pb-4">
                            <div>
                                <h2 class="text-lg font-bold text-navy-dark">Office Location Map</h2>
                                <p class="text-xs text-ink-muted">{{ $company['short_address'] }}</p>
                            </div>
                            <a
                                href="https://maps.google.com/?q=Kemang+IFI+Graha,+Jatirasa,+Jatiasih,+Bekasi"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex items-center gap-1.5 rounded-xl border border-border-subtle bg-surface px-3 py-2 text-xs font-semibold text-navy-dark transition hover:border-navy"
                            >
                                <span>Open Maps</span>
                                <i data-lucide="external-link" class="h-3.5 w-3.5" aria-hidden="true"></i>
                            </a>
                        </div>

                        <div class="relative min-h-[300px] flex-1 overflow-hidden rounded-xl border border-border-subtle bg-surface">
                            <iframe
                                src="{{ $company['map_embed_url'] }}"
                                title="PT Permata Kasih Anugerah Head Office Map"
                                loading="lazy"
                                referrerpolicy="no-referrer-when-downgrade"
                                class="h-full w-full min-h-[300px] border-0"
                                allowfullscreen
                            ></iframe>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-navy-dark p-6 text-white shadow-md sm:p-8">
                        <h3 class="text-xl font-bold text-white">Need Fast Candidate Shortlists?</h3>
                        <p class="mt-2 text-sm text-white/75">
                            Start a direct WhatsApp consultation with our Bekasi placement desk during operating hours.
                        </p>
                        <div class="mt-5">
                            <x-whatsapp-button variant="hero" label="Chat Directly via WhatsApp" class="w-full sm:w-auto" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
