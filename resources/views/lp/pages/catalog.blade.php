@extends('lp.layouts.app')

@section('title', 'Worker Catalog — ' . config('company.legal_name'))
@section('meta_description', 'Search and filter verified Domestic Helpers (ART), Babysitters, Nannies, Caregivers, Elderly Care companions, and Other Manpower candidates.')

@section('content')
    @php
        $hasActiveFilters = $filters['search'] !== ''
            || ($filters['category'] !== '' && $filters['category'] !== 'all')
            || ($filters['experience'] !== '' && $filters['experience'] !== 'all')
            || ($filters['age_range'] !== '' && $filters['age_range'] !== 'all')
            || ($filters['origin'] !== '' && $filters['origin'] !== 'all')
            || ($filters['status'] !== '' && $filters['status'] !== 'all');
    @endphp

    {{-- Catalog Header --}}
    <section class="bg-navy-dark py-14 text-white lg:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[['label' => 'Worker Catalog']]" />

            <div class="mt-4 flex flex-col justify-between gap-6 lg:flex-row lg:items-end">
                <div>
                    <h1 class="text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                        Find the Right Worker for Your Needs
                    </h1>
                    <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/75 sm:text-base">
                        Browse pre-screened candidate profiles by category, age range, work experience, regional origin, and availability status.
                    </p>
                </div>
                <div class="inline-flex items-center gap-2 self-start rounded-xl border border-white/15 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white lg:self-end">
                    <i data-lucide="users" class="h-4 w-4 text-gold" aria-hidden="true"></i>
                    <span>{{ $totalCatalogCount }} Total Profiles in Catalog</span>
                </div>
            </div>

            {{-- Top Search Bar --}}
            <form id="catalogHeaderSearchForm" method="GET" action="{{ route('catalog') }}" class="mt-8">
                <div class="flex flex-col gap-3 rounded-2xl border border-white/15 bg-white p-2.5 shadow-lg sm:flex-row">
                    <div class="relative flex-1">
                        <i data-lucide="search" class="pointer-events-none absolute top-1/2 left-3.5 h-4 w-4 -translate-y-1/2 text-ink-muted" aria-hidden="true"></i>
                        <input
                            id="catalogSearchInput"
                            type="search"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="Search by candidate name, code (e.g. PKA-2026-001), or skill keyword..."
                            class="w-full rounded-xl border border-transparent bg-surface py-3 pr-4 pl-10 text-sm text-ink placeholder:text-ink-muted focus:border-teal focus:bg-white focus:outline-none"
                        >
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-navy px-6 py-3 text-sm font-semibold text-white transition hover:bg-navy-dark">
                        <span>Search Candidates</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    {{-- Main Directory Section --}}
    <section class="bg-surface py-12 lg:py-16">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                {{-- Left Filter Sidebar (Desktop) & Mobile Filter Drawer --}}
                <aside class="lg:col-span-3">
                    <div class="mb-4 flex items-center justify-between gap-3 lg:hidden">
                        <button
                            id="mobileFilterToggleBtn"
                            type="button"
                            aria-controls="catalogSidebarPanel"
                            aria-expanded="false"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl border border-border-subtle bg-white px-4 py-3 text-sm font-semibold text-navy-dark shadow-xs transition hover:border-navy"
                        >
                            <i data-lucide="sliders-horizontal" class="h-4 w-4 text-teal" aria-hidden="true"></i>
                            <span>Filters &amp; Sort</span>
                        </button>
                        @if ($hasActiveFilters)
                            <a href="{{ route('catalog') }}" class="rounded-xl border border-border-subtle bg-white px-4 py-3 text-xs font-semibold text-ink-muted hover:text-navy-dark">
                                Clear
                            </a>
                        @endif
                    </div>

                    <div id="catalogSidebarPanel" class="hidden rounded-2xl border border-border-subtle bg-white p-6 shadow-xs lg:sticky lg:top-24 lg:block">
                        <div class="flex items-center justify-between border-b border-border-subtle pb-4">
                            <h2 class="inline-flex items-center gap-2 text-sm font-bold text-navy-dark">
                                <i data-lucide="filter" class="h-4 w-4 text-teal" aria-hidden="true"></i>
                                <span>Filter Candidates</span>
                            </h2>
                            <a href="{{ route('catalog') }}" id="catalogClearFiltersLink" class="text-xs font-semibold text-teal hover:underline">
                                Clear Filters
                            </a>
                        </div>

                        <form id="catalogFilterForm" method="GET" action="{{ route('catalog') }}" class="mt-5 space-y-4">
                            <input type="hidden" id="catalogFilterSearchHidden" name="search" value="{{ $filters['search'] }}">

                            <div>
                                <label for="filterCategory" class="block text-xs font-bold text-navy-dark mb-1.5">Worker Category</label>
                                <select id="filterCategory" name="category" class="w-full rounded-xl border border-border-subtle bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none">
                                    <option value="all">All Categories</option>
                                    @foreach ($categories as $key => $label)
                                        <option value="{{ $key }}" @selected($filters['category'] === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="filterAgeRange" class="block text-xs font-bold text-navy-dark mb-1.5">Age Range</label>
                                <select id="filterAgeRange" name="age_range" class="w-full rounded-xl border border-border-subtle bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none">
                                    <option value="all">All Ages</option>
                                    @foreach ($ageRangeOptions as $key => $label)
                                        <option value="{{ $key }}" @selected($filters['age_range'] === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="filterExperience" class="block text-xs font-bold text-navy-dark mb-1.5">Work Experience</label>
                                <select id="filterExperience" name="experience" class="w-full rounded-xl border border-border-subtle bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none">
                                    <option value="all">Any Experience</option>
                                    @foreach ($experienceOptions as $key => $label)
                                        <option value="{{ $key }}" @selected($filters['experience'] === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="filterOrigin" class="block text-xs font-bold text-navy-dark mb-1.5">Origin</label>
                                <select id="filterOrigin" name="origin" class="w-full rounded-xl border border-border-subtle bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none">
                                    <option value="all">All Regions</option>
                                    @foreach ($originOptions as $key => $label)
                                        <option value="{{ $key }}" @selected($filters['origin'] === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="filterStatus" class="block text-xs font-bold text-navy-dark mb-1.5">Availability</label>
                                <select id="filterStatus" name="status" class="w-full rounded-xl border border-border-subtle bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none">
                                    <option value="all">All Statuses</option>
                                    @foreach ($statusOptions as $key => $label)
                                        <option value="{{ $key }}" @selected($filters['status'] === $key)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="filterSort" class="block text-xs font-bold text-navy-dark mb-1.5">Sort By</label>
                                <select id="filterSort" name="sort" class="w-full rounded-xl border border-border-subtle bg-surface px-3 py-2.5 text-sm text-ink focus:border-teal focus:bg-white focus:outline-none">
                                    <option value="newest" @selected($filters['sort'] === 'newest')>Newest</option>
                                    <option value="experience_desc" @selected($filters['sort'] === 'experience_desc')>Most experienced</option>
                                    <option value="name_asc" @selected($filters['sort'] === 'name_asc')>Name A-Z</option>
                                </select>
                            </div>

                            <div class="pt-2 space-y-2">
                                <button type="submit" class="w-full rounded-xl bg-navy px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-navy-dark">
                                    Apply Filters
                                </button>
                                <a href="{{ route('catalog') }}" class="block w-full rounded-xl border border-border-subtle bg-white px-4 py-2.5 text-center text-xs font-semibold text-ink-muted transition hover:bg-surface hover:text-navy-dark">
                                    Clear Filters
                                </a>
                            </div>
                        </form>
                    </div>
                </aside>

                {{-- Right Candidate Listing --}}
                <div class="lg:col-span-9">
                    <div id="catalogLoadingIndicator" class="mb-4 hidden items-center justify-center gap-2 rounded-2xl border border-border-subtle bg-white py-3 text-sm font-semibold text-navy-dark">
                        <span class="h-4 w-4 animate-spin rounded-full border-2 border-teal border-t-transparent"></span>
                        <span>Updating candidate results...</span>
                    </div>

                    <div id="catalogDynamicRegion">
                        {{-- Result Count & Active Filter Indicators --}}
                        <div class="mb-6 flex flex-col gap-3 rounded-2xl border border-border-subtle bg-white p-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p id="catalogResultCount" class="text-sm font-bold text-navy-dark">
                                    @if ($workers->total() > 0)
                                        Showing {{ $workers->firstItem() }}–{{ $workers->lastItem() }} of {{ $workers->total() }} candidates
                                    @else
                                        0 candidates matched your filters
                                    @endif
                                </p>
                                @if ($hasActiveFilters)
                                    <div id="catalogActiveFilters" class="mt-2 flex flex-wrap items-center gap-1.5">
                                        @if ($filters['search'] !== '')
                                            <span class="rounded-full bg-teal-light px-2.5 py-0.5 text-xs font-semibold text-teal">Search: "{{ $filters['search'] }}"</span>
                                        @endif
                                        @if ($filters['category'] !== '' && $filters['category'] !== 'all')
                                            <span class="rounded-full bg-teal-light px-2.5 py-0.5 text-xs font-semibold text-teal">Category: {{ $categories[$filters['category']] ?? $filters['category'] }}</span>
                                        @endif
                                        @if ($filters['age_range'] !== '' && $filters['age_range'] !== 'all')
                                            <span class="rounded-full bg-teal-light px-2.5 py-0.5 text-xs font-semibold text-teal">Age: {{ $filters['age_range'] }}</span>
                                        @endif
                                        @if ($filters['experience'] !== '' && $filters['experience'] !== 'all')
                                            <span class="rounded-full bg-teal-light px-2.5 py-0.5 text-xs font-semibold text-teal">Experience: {{ $experienceOptions[$filters['experience']] ?? $filters['experience'] }}</span>
                                        @endif
                                        @if ($filters['origin'] !== '' && $filters['origin'] !== 'all')
                                            <span class="rounded-full bg-teal-light px-2.5 py-0.5 text-xs font-semibold text-teal">Origin: {{ $filters['origin'] }}</span>
                                        @endif
                                        @if ($filters['status'] !== '' && $filters['status'] !== 'all')
                                            <span class="rounded-full bg-teal-light px-2.5 py-0.5 text-xs font-semibold text-teal">Status: {{ $filters['status'] }}</span>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            <div class="inline-flex items-center gap-1.5 text-xs font-medium text-ink-muted shrink-0">
                                <i data-lucide="shield-check" class="h-4 w-4 text-teal" aria-hidden="true"></i>
                                <span>Privacy-safe public profiles</span>
                            </div>
                        </div>

                        @if ($workers->isEmpty())
                            {{-- Empty State --}}
                            <div id="catalogEmptyState" class="rounded-2xl border border-border-subtle bg-white p-10 text-center shadow-xs sm:p-14">
                                <span class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-surface text-navy">
                                    <i data-lucide="search-x" class="h-7 w-7" aria-hidden="true"></i>
                                </span>
                                <h2 class="mt-4 text-xl font-bold text-navy-dark">No Candidates Match Your Filters</h2>
                                <p class="mx-auto mt-2 max-w-md text-sm leading-relaxed text-ink-muted">
                                    Try clearing a filter or broadening your search keyword. You can also message our recruitment desk directly on WhatsApp for personalized candidate recommendations.
                                </p>
                                <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                                    <a href="{{ route('catalog') }}" data-catalog-reset class="inline-flex items-center justify-center rounded-xl bg-navy px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-navy-dark">
                                        Clear Filters
                                    </a>
                                    <x-whatsapp-button variant="primary" label="Consult via WhatsApp" />
                                </div>
                            </div>
                        @else
                            {{-- Responsive Candidate Grid (3 columns desktop, 2 tablet, 1 mobile) --}}
                            <div id="catalogGrid" class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3">
                                @foreach ($workers as $worker)
                                    <x-worker-card :worker="$worker" />
                                @endforeach
                            </div>

                            <x-pagination :paginator="$workers" />
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('lp-assets/js/catalog.js') }}" defer></script>
@endpush
