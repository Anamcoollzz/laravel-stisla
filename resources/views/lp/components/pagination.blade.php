@props(['paginator'])

@if ($paginator->hasPages())
    <nav id="catalogServerPagination" aria-label="Worker Catalog Pagination" class="mt-10 flex items-center justify-center">
        <ul class="inline-flex flex-wrap items-center gap-1.5">
            {{-- Previous Page Link --}}
            <li>
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center gap-1 rounded-xl border border-border-subtle bg-surface px-3.5 py-2 text-xs font-semibold text-ink-muted/60 cursor-not-allowed">
                        <i data-lucide="chevron-left" class="h-4 w-4" aria-hidden="true"></i>
                        Previous
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center gap-1 rounded-xl border border-border-subtle bg-white px-3.5 py-2 text-xs font-semibold text-navy-dark transition hover:border-navy hover:bg-surface">
                        <i data-lucide="chevron-left" class="h-4 w-4" aria-hidden="true"></i>
                        Previous
                    </a>
                @endif
            </li>

            {{-- Page Number Links --}}
            @foreach (range(1, $paginator->lastPage()) as $page)
                <li>
                    @if ($page === $paginator->currentPage())
                        <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl bg-navy px-3 text-xs font-bold text-white shadow-xs">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl border border-border-subtle bg-white px-3 text-xs font-semibold text-navy-dark transition hover:border-navy hover:bg-surface">
                            {{ $page }}
                        </a>
                    @endif
                </li>
            @endforeach

            {{-- Next Page Link --}}
            <li>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center gap-1 rounded-xl border border-border-subtle bg-white px-3.5 py-2 text-xs font-semibold text-navy-dark transition hover:border-navy hover:bg-surface">
                        Next
                        <i data-lucide="chevron-right" class="h-4 w-4" aria-hidden="true"></i>
                    </a>
                @else
                    <span class="inline-flex items-center gap-1 rounded-xl border border-border-subtle bg-surface px-3.5 py-2 text-xs font-semibold text-ink-muted/60 cursor-not-allowed">
                        Next
                        <i data-lucide="chevron-right" class="h-4 w-4" aria-hidden="true"></i>
                    </span>
                @endif
            </li>
        </ul>
    </nav>
@endif
