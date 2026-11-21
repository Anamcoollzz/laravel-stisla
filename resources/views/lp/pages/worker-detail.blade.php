@extends('lp.layouts.app')

@section('title', $worker['name'] . ' (' . $worker['code'] . ') — ' . $worker['category_label'] . ' | ' . config('company.legal_name'))
@section('meta_description', 'Public candidate profile for ' . $worker['name'] . ' (' . $worker['code'] . '), ' . $worker['category_label'] . ' from ' . $worker['origin'] . ' with ' . $worker['experience_label'] . ' experience.')
@section('og_title', $worker['name'] . ' — ' . $worker['category_label'])
@section('og_image', $worker['photo_url'] ?? asset($worker['photo']))

@section('content')
    @php
        $statusBadge = match ($worker['status']) {
            'Available' => 'bg-teal text-white',
            'In Training' => 'bg-gold text-navy-dark',
            default => 'bg-slate-700 text-white',
        };
    @endphp

    {{-- Top Breadcrumb Header --}}
    <section class="bg-navy-dark py-10 text-white">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                ['label' => 'Worker Catalog', 'url' => route('catalog')],
                ['label' => $worker['name']],
            ]" />

            <div class="mt-4 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <span class="inline-block rounded-lg bg-white/10 px-2.5 py-1 text-xs font-bold tracking-wide text-gold">
                        {{ $worker['code'] }}
                    </span>
                    <h1 class="mt-2 text-2xl font-extrabold text-white sm:text-3xl">
                        {{ $worker['name'] }} — {{ $worker['category_label'] }}
                    </h1>
                </div>
                <a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 self-start rounded-xl border border-white/20 bg-white/10 px-4 py-2 text-xs font-semibold text-white transition hover:bg-white/20 sm:self-auto">
                    <i data-lucide="arrow-left" class="h-4 w-4" aria-hidden="true"></i>
                    <span>Back to Catalog</span>
                </a>
            </div>
        </div>
    </section>

    <section class="bg-surface py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:items-start">
                {{-- Left Column: Profile Image, Additional Photos, Status & Code --}}
                <div class="lg:col-span-5 lg:sticky lg:top-24">
                    <div class="rounded-2xl border border-border-subtle bg-white p-5 shadow-xs">
                        <div class="relative aspect-4/5 overflow-hidden rounded-xl bg-surface">
                            <img
                                id="workerMainPhoto"
                                src="{{ $worker['photo_url'] ?? asset($worker['photo']) }}"
                                alt="{{ $worker['name'] }} — {{ $worker['category_label'] }}"
                                class="h-full w-full object-cover object-top"
                                onerror="this.onerror=null;this.src='{{ asset('lp-assets/images/general/placeholder-worker.svg') }}';"
                            >
                            <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold shadow-xs {{ $statusBadge }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                {{ $worker['status'] }}
                            </span>
                            <span class="absolute top-3 right-3 rounded-lg bg-navy-dark/85 px-2.5 py-1 text-xs font-bold text-white">
                                {{ $worker['code'] }}
                            </span>
                        </div>

                        @if (count($worker['gallery']) > 1)
                            <div class="mt-4 flex flex-wrap gap-2.5" role="group" aria-label="Additional candidate photos">
                                @foreach ($worker['gallery'] as $index => $galleryPhoto)
                                    @php
                                        $galleryUrl = $worker['gallery_urls'][$index] ?? asset($galleryPhoto);
                                    @endphp
                                    <button
                                        type="button"
                                        data-worker-thumb="{{ $galleryUrl }}"
                                        data-worker-thumb-alt="{{ $worker['name'] }} photo {{ $index + 1 }}"
                                        class="h-20 w-20 overflow-hidden rounded-xl border-2 transition {{ $index === 0 ? 'border-teal ring-2 ring-teal/20' : 'border-border-subtle hover:border-navy' }}"
                                        aria-label="View photo {{ $index + 1 }} of {{ $worker['name'] }}"
                                    >
                                        <img
                                            src="{{ $galleryUrl }}"
                                            alt="{{ $worker['name'] }} thumbnail {{ $index + 1 }}"
                                            loading="lazy"
                                            class="h-full w-full object-cover object-top"
                                        >
                                    </button>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-5 space-y-3 rounded-xl border border-border-subtle bg-surface p-4 text-xs">
                            <div class="flex items-start gap-2.5">
                                <i data-lucide="heart-pulse" class="h-4 w-4 text-teal shrink-0 mt-0.5" aria-hidden="true"></i>
                                <div>
                                    <strong class="block text-navy-dark">Medical Readiness</strong>
                                    <span class="text-ink-muted">{{ $worker['medical_check'] ?? 'General health & physical readiness screening completed' }}</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5">
                                <i data-lucide="lock" class="h-4 w-4 text-navy shrink-0 mt-0.5" aria-hidden="true"></i>
                                <div>
                                    <strong class="block text-navy-dark">Privacy Protection</strong>
                                    <span class="text-ink-muted">
                                        Public profiles display only recruitment-relevant details. National IDs, passport numbers, personal phone numbers, and private addresses are never exposed online.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Details, Additional Sections & WhatsApp CTA --}}
                <div class="space-y-6 lg:col-span-7">
                    <div class="rounded-2xl border border-border-subtle bg-white p-6 shadow-xs sm:p-8">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-teal-light px-3 py-1 text-xs font-bold text-teal">
                                {{ $worker['category_label'] }}
                            </span>
                            <span class="rounded-full border border-border-subtle bg-surface px-3 py-1 text-xs font-semibold text-navy-dark">
                                Code: {{ $worker['code'] }}
                            </span>
                            <span class="rounded-full bg-surface px-3 py-1 text-xs font-semibold text-ink-muted">
                                Status: {{ $worker['status'] }}
                            </span>
                        </div>

                        <h2 class="mt-4 text-2xl font-extrabold text-navy-dark sm:text-3xl">{{ $worker['name'] }}</h2>

                        {{-- Key Profile Attributes --}}
                        <div class="mt-6 grid grid-cols-2 gap-3 sm:grid-cols-3">
                            <div class="rounded-xl border border-border-subtle bg-surface p-3.5">
                                <span class="block text-[11px] font-medium text-ink-muted">Age</span>
                                <strong class="text-sm font-bold text-navy-dark">{{ $worker['age'] }} Years</strong>
                            </div>
                            <div class="rounded-xl border border-border-subtle bg-surface p-3.5">
                                <span class="block text-[11px] font-medium text-ink-muted">Origin</span>
                                <strong class="text-sm font-bold text-navy-dark">{{ $worker['origin'] }}</strong>
                            </div>
                            <div class="rounded-xl border border-border-subtle bg-surface p-3.5">
                                <span class="block text-[11px] font-medium text-ink-muted">Experience</span>
                                <strong class="text-sm font-bold text-navy-dark">{{ $worker['experience_label'] }}</strong>
                            </div>
                            <div class="rounded-xl border border-border-subtle bg-surface p-3.5 sm:col-span-2">
                                <span class="block text-[11px] font-medium text-ink-muted">Education</span>
                                <strong class="text-sm font-bold text-navy-dark">{{ $worker['education'] }}</strong>
                            </div>
                            <div class="rounded-xl border border-border-subtle bg-surface p-3.5">
                                <span class="block text-[11px] font-medium text-ink-muted">Languages</span>
                                <strong class="text-sm font-bold text-navy-dark">{{ implode(', ', $worker['languages']) }}</strong>
                            </div>
                        </div>

                        {{-- About This Candidate --}}
                        <div class="mt-8 border-t border-border-subtle pt-6">
                            <h3 class="text-base font-bold text-navy-dark">About This Candidate</h3>
                            <p class="mt-2 text-sm leading-relaxed text-ink-muted sm:text-base">{{ $worker['bio'] }}</p>
                        </div>

                        {{-- Work Experience --}}
                        <div class="mt-8 border-t border-border-subtle pt-6">
                            <h3 class="text-base font-bold text-navy-dark">Work Experience</h3>
                            <ul class="mt-3 space-y-2.5">
                                @foreach ($worker['work_history'] as $history)
                                    <li class="rounded-xl border-l-4 border-teal bg-surface px-4 py-3 text-sm font-medium text-navy-dark">
                                        {{ $history }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Skills and Capabilities --}}
                        <div class="mt-8 border-t border-border-subtle pt-6">
                            <h3 class="text-base font-bold text-navy-dark">Skills and Capabilities</h3>
                            <div class="mt-3 flex flex-wrap gap-2">
                                @foreach ($worker['skills'] as $skill)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-teal-light px-3.5 py-1.5 text-xs font-semibold text-teal">
                                        <i data-lucide="check" class="h-3.5 w-3.5" aria-hidden="true"></i>
                                        <span>{{ $skill }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        {{-- Work Preferences & Additional Relevant Information --}}
                        <div class="mt-8 border-t border-border-subtle pt-6">
                            <h3 class="text-base font-bold text-navy-dark">Work Preferences &amp; Certifications</h3>
                            <div class="mt-3 overflow-hidden rounded-xl border border-border-subtle">
                                <table class="w-full text-left text-sm">
                                    <tbody class="divide-y divide-border-subtle">
                                        @foreach ($worker['placement_preferences'] as $label => $value)
                                            <tr>
                                                <th scope="row" class="w-2/5 bg-surface px-4 py-3 font-semibold text-navy-dark">{{ $label }}</th>
                                                <td class="bg-white px-4 py-3 text-ink-muted">{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <th scope="row" class="bg-surface px-4 py-3 font-semibold text-navy-dark">Training &amp; Certifications</th>
                                            <td class="bg-white px-4 py-3 text-ink-muted">{{ implode(' • ', $worker['training_certifications']) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Prominent WhatsApp CTA Card --}}
                    <div class="rounded-2xl bg-navy-dark p-6 text-white shadow-lg sm:p-8">
                        <div class="flex flex-col justify-between gap-6 sm:flex-row sm:items-center">
                            <div>
                                <span class="inline-block rounded-full bg-white/10 px-3 py-1 text-xs font-bold uppercase tracking-wider text-gold">
                                    Direct Inquiry
                                </span>
                                <h3 class="mt-3 text-xl font-bold text-white">Interested in This Candidate?</h3>
                                <p class="mt-1.5 text-sm text-white/75">
                                    Contact our recruitment team to request more information about this candidate.
                                </p>
                            </div>
                            <x-whatsapp-button
                                :url="$whatsappUrl"
                                variant="detail"
                                label="Contact via WhatsApp"
                                class="shrink-0"
                            />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Related Candidates --}}
            @if ($relatedWorkers->isNotEmpty())
                <div class="mt-16 border-t border-border-subtle pt-12">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
                        <x-section-heading
                            eyebrow="Similar Profiles"
                            title="Related Candidates"
                            align="left"
                        />
                        <x-primary-button :href="route('catalog')" variant="outline" class="self-start sm:self-end">
                            View Full Catalog
                        </x-primary-button>
                    </div>

                    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach ($relatedWorkers as $related)
                            <x-worker-card :worker="$related" />
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection
