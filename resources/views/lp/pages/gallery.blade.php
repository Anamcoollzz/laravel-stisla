@extends('lp.layouts.app')

@section('title', 'Company Gallery — ' . config('company.legal_name'))
@section('meta_description', 'Browse training activities, recruitment activities, worker preparation, company activities, events, and documentation at PT Permata Kasih Anugerah.')

@section('content')
    {{-- Page Hero & Breadcrumb --}}
    <section class="bg-navy-dark py-16 text-white lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Gallery']]" />
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                Activity &amp; Training Gallery
            </h1>
            <p class="mt-4 max-w-2xl text-base leading-relaxed text-white/75 sm:text-lg">
                Visual documentation of training workshops, candidate preparation, recruitment consultations, and company activities.
            </p>
        </div>
    </section>

    <section class="bg-surface py-14 lg:py-20">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Category Filter Pills --}}
            <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div class="flex flex-wrap gap-2" role="group" aria-label="Filter gallery by category">
                    <button
                        type="button"
                        data-gallery-filter="all"
                        class="rounded-full border border-navy bg-navy px-4 py-2 text-xs font-semibold text-white transition"
                    >
                        All
                    </button>
                    @foreach ($galleryCategories as $category)
                        <button
                            type="button"
                            data-gallery-filter="{{ $category }}"
                            class="rounded-full border border-border-subtle bg-white px-4 py-2 text-xs font-semibold text-ink transition hover:border-navy hover:text-navy-dark"
                        >
                            {{ $category }}
                        </button>
                    @endforeach
                </div>

                <p class="text-xs font-semibold text-ink-muted">
                    Showing <span id="galleryVisibleCount" class="text-navy-dark">{{ count($galleryItems) }}</span> items
                </p>
            </div>

            {{-- Responsive Image Grid --}}
            <div id="galleryGrid" class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($galleryItems as $item)
                    <article
                        data-gallery-item
                        data-gallery-category="{{ $item['category'] }}"
                        class="group overflow-hidden rounded-2xl border border-border-subtle bg-white shadow-xs transition duration-300 hover:-translate-y-1 hover:shadow-md"
                    >
                        <button
                            type="button"
                            data-gallery-trigger
                            data-image="{{ asset($item['image']) }}"
                            data-title="{{ $item['title'] }}"
                            data-category="{{ $item['category'] }}"
                            data-date="{{ $item['date'] }}"
                            data-description="{{ $item['description'] }}"
                            class="flex h-full w-full flex-col text-left focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-teal"
                        >
                            <div class="relative aspect-16/11 w-full overflow-hidden bg-surface">
                                <img
                                    src="{{ asset($item['image']) }}"
                                    alt="{{ $item['title'] }}"
                                    loading="lazy"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
                                    onerror="this.onerror=null;this.src='{{ asset('lp-assets/images/general/placeholder-gallery.svg') }}';"
                                >
                                <span class="absolute right-3 bottom-3 inline-flex h-9 w-9 items-center justify-center rounded-full bg-navy-dark/80 text-white opacity-0 transition group-hover:opacity-100">
                                    <i data-lucide="maximize-2" class="h-4 w-4" aria-hidden="true"></i>
                                </span>
                            </div>

                            <div class="flex flex-1 flex-col p-5">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="rounded-full bg-teal-light px-2.5 py-0.5 text-[11px] font-semibold text-teal">
                                        {{ $item['category'] }}
                                    </span>
                                    <span class="text-xs text-ink-muted">{{ $item['date'] }}</span>
                                </div>
                                <h2 class="mt-2.5 text-base font-bold text-navy-dark">{{ $item['title'] }}</h2>
                                <p class="mt-1.5 text-xs leading-relaxed text-ink-muted">{{ $item['description'] }}</p>
                            </div>
                        </button>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Accessible Lightbox Modal --}}
    <div
        id="galleryLightboxModal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="lightboxTitle"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-navy-dark/85 p-4 backdrop-blur-xs"
    >
        <div class="relative w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-border-subtle bg-navy-dark px-5 py-3.5 text-white">
                <div class="flex items-center gap-2.5">
                    <span id="lightboxCategory" class="rounded-full bg-gold px-2.5 py-0.5 text-xs font-bold text-navy-dark"></span>
                    <span id="lightboxDate" class="text-xs text-white/75"></span>
                </div>
                <button
                    id="lightboxCloseBtn"
                    type="button"
                    aria-label="Close gallery preview"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-white/10 text-white transition hover:bg-white/20"
                >
                    <i data-lucide="x" class="h-4 w-4" aria-hidden="true"></i>
                </button>
            </div>

            <div class="bg-slate-950 text-center">
                <img
                    id="lightboxImage"
                    src="{{ asset('lp-assets/images/general/placeholder-gallery.svg') }}"
                    alt="Gallery Preview"
                    class="mx-auto max-h-[65vh] w-full object-contain"
                >
            </div>

            <div class="flex flex-col justify-between gap-4 p-5 sm:flex-row sm:items-center sm:px-6">
                <div>
                    <h3 id="lightboxTitle" class="text-base font-bold text-navy-dark"></h3>
                    <p id="lightboxDescription" class="mt-1 text-xs leading-relaxed text-ink-muted sm:text-sm"></p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <button
                        id="lightboxPrevBtn"
                        type="button"
                        aria-label="Previous image"
                        class="inline-flex items-center gap-1 rounded-xl border border-border-subtle bg-white px-3.5 py-2 text-xs font-semibold text-navy-dark transition hover:border-navy hover:bg-surface"
                    >
                        <i data-lucide="chevron-left" class="h-4 w-4" aria-hidden="true"></i>
                        <span>Prev</span>
                    </button>
                    <button
                        id="lightboxNextBtn"
                        type="button"
                        aria-label="Next image"
                        class="inline-flex items-center gap-1 rounded-xl border border-border-subtle bg-white px-3.5 py-2 text-xs font-semibold text-navy-dark transition hover:border-navy hover:bg-surface"
                    >
                        <span>Next</span>
                        <i data-lucide="chevron-right" class="h-4 w-4" aria-hidden="true"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('lp-assets/js/gallery.js') }}" defer></script>
@endpush
