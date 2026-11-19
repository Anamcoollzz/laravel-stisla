/**
 * PT Permata Kasih Anugerah - Worker Catalog Filtering, Sorting & Pagination
 */

document.addEventListener('DOMContentLoaded', () => {
    const headerSearchForm = document.getElementById('catalogHeaderSearchForm');
    const searchInput = document.getElementById('catalogSearchInput');
    const hiddenSearchInput = document.getElementById('catalogFilterSearchHidden');
    const filterForm = document.getElementById('catalogFilterForm');
    const sidebarPanel = document.getElementById('catalogSidebarPanel');
    const mobileFilterToggleBtn = document.getElementById('mobileFilterToggleBtn');
    const dynamicRegion = document.getElementById('catalogDynamicRegion');
    const loadingIndicator = document.getElementById('catalogLoadingIndicator');

    if (!filterForm || !dynamicRegion) {
        return;
    }

    let searchDebounce = null;

    if (mobileFilterToggleBtn && sidebarPanel) {
        mobileFilterToggleBtn.addEventListener('click', () => {
            const isHidden = sidebarPanel.classList.contains('hidden');
            sidebarPanel.classList.toggle('hidden', !isHidden);
            mobileFilterToggleBtn.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });
    }

    const setLoading = (isLoading) => {
        if (loadingIndicator) {
            loadingIndicator.classList.toggle('hidden', !isLoading);
            loadingIndicator.classList.toggle('flex', isLoading);
        }
        dynamicRegion.classList.toggle('opacity-50', isLoading);
    };

    const buildCatalogUrl = () => {
        if (hiddenSearchInput && searchInput) {
            hiddenSearchInput.value = searchInput.value;
        }

        const formData = new FormData(filterForm);
        const url = new URL(filterForm.action, window.location.origin);

        for (const [key, value] of formData.entries()) {
            const trimmed = String(value).trim();
            if (trimmed !== '' && trimmed !== 'all') {
                url.searchParams.set(key, trimmed);
            }
        }

        return url.toString();
    };

    const syncControlsFromUrl = () => {
        const params = new URLSearchParams(window.location.search);
        const searchValue = params.get('search') || '';

        if (searchInput) {
            searchInput.value = searchValue;
        }
        if (hiddenSearchInput) {
            hiddenSearchInput.value = searchValue;
        }

        ['category', 'age_range', 'experience', 'origin', 'status', 'sort'].forEach((name) => {
            const select = filterForm.querySelector(`select[name="${name}"]`);
            if (select) {
                select.value = params.get(name) || (name === 'sort' ? 'newest' : 'all');
            }
        });
    };

    const updateCatalogView = async (targetUrl, pushHistory = true, scrollToResults = false) => {
        setLoading(true);

        try {
            const response = await fetch(targetUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html',
                },
            });

            if (!response.ok) {
                throw new Error('Catalog request failed.');
            }

            const html = await response.text();
            const parsedDoc = new DOMParser().parseFromString(html, 'text/html');
            const nextRegion = parsedDoc.getElementById('catalogDynamicRegion');

            if (!nextRegion) {
                window.location.href = targetUrl;
                return;
            }

            dynamicRegion.innerHTML = nextRegion.innerHTML;

            if (pushHistory) {
                window.history.pushState({}, '', targetUrl);
            }

            syncControlsFromUrl();

            if (typeof window.pkaRefreshIcons === 'function') {
                window.pkaRefreshIcons();
            }

            if (scrollToResults) {
                dynamicRegion.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        } catch (error) {
            window.location.href = targetUrl;
        } finally {
            setLoading(false);
        }
    };

    if (headerSearchForm) {
        headerSearchForm.addEventListener('submit', (event) => {
            event.preventDefault();
            updateCatalogView(buildCatalogUrl());
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', () => {
            window.clearTimeout(searchDebounce);
            searchDebounce = window.setTimeout(() => {
                updateCatalogView(buildCatalogUrl());
            }, 260);
        });
    }

    filterForm.addEventListener('submit', (event) => {
        event.preventDefault();
        updateCatalogView(buildCatalogUrl());
    });

    filterForm.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => {
            updateCatalogView(buildCatalogUrl());
        });
    });

    const clearLink = document.getElementById('catalogClearFiltersLink');
    if (clearLink) {
        clearLink.addEventListener('click', (event) => {
            event.preventDefault();
            filterForm.reset();
            if (searchInput) {
                searchInput.value = '';
            }
            if (hiddenSearchInput) {
                hiddenSearchInput.value = '';
            }
            updateCatalogView(clearLink.href);
        });
    }

    dynamicRegion.addEventListener('click', (event) => {
        const paginationLink = event.target.closest('#catalogServerPagination a');
        if (paginationLink) {
            event.preventDefault();
            updateCatalogView(paginationLink.href, true, true);
            return;
        }

        const resetBtn = event.target.closest('[data-catalog-reset]');
        if (resetBtn) {
            event.preventDefault();
            filterForm.reset();
            if (searchInput) {
                searchInput.value = '';
            }
            if (hiddenSearchInput) {
                hiddenSearchInput.value = '';
            }
            updateCatalogView(resetBtn.href);
        }
    });

    window.addEventListener('popstate', () => {
        updateCatalogView(window.location.href, false);
    });
});
