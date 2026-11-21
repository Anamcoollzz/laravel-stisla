/**
 * PT Permata Kasih Anugerah - Gallery Category Filtering & Lightbox Modal
 */

document.addEventListener('DOMContentLoaded', () => {
    const filterButtons = document.querySelectorAll('[data-gallery-filter]');
    const galleryItems = Array.from(document.querySelectorAll('[data-gallery-item]'));
    const visibleCountEl = document.getElementById('galleryVisibleCount');

    const modal = document.getElementById('galleryLightboxModal');
    const modalImg = document.getElementById('lightboxImage');
    const modalTitle = document.getElementById('lightboxTitle');
    const modalCategory = document.getElementById('lightboxCategory');
    const modalDate = document.getElementById('lightboxDate');
    const modalDesc = document.getElementById('lightboxDescription');
    const prevBtn = document.getElementById('lightboxPrevBtn');
    const nextBtn = document.getElementById('lightboxNextBtn');
    const closeBtn = document.getElementById('lightboxCloseBtn');

    if (filterButtons.length > 0 && galleryItems.length > 0) {
        filterButtons.forEach((button) => {
            button.addEventListener('click', () => {
                const selectedCategory = button.getAttribute('data-gallery-filter');
                let visibleCount = 0;

                filterButtons.forEach((btn) => {
                    btn.classList.remove('border-navy', 'bg-navy', 'text-white');
                    btn.classList.add('border-border-subtle', 'bg-white', 'text-ink');
                });

                button.classList.remove('border-border-subtle', 'bg-white', 'text-ink');
                button.classList.add('border-navy', 'bg-navy', 'text-white');

                galleryItems.forEach((item) => {
                    const itemCategory = item.getAttribute('data-gallery-category');
                    const matches = selectedCategory === 'all' || itemCategory === selectedCategory;
                    item.classList.toggle('hidden', !matches);
                    if (matches) {
                        visibleCount += 1;
                    }
                });

                if (visibleCountEl) {
                    visibleCountEl.textContent = String(visibleCount);
                }
            });
        });
    }

    if (!modal) {
        return;
    }

    let visibleTriggers = [];
    let activeIndex = 0;
    let lastFocusedTrigger = null;

    const getVisibleTriggers = () => {
        return Array.from(document.querySelectorAll('[data-gallery-item]:not(.hidden) [data-gallery-trigger]'));
    };

    const renderSlide = (index) => {
        const trigger = visibleTriggers[index];
        if (!trigger) {
            return;
        }

        if (modalImg) {
            modalImg.src = trigger.getAttribute('data-image') || '';
            modalImg.alt = trigger.getAttribute('data-title') || 'Gallery Preview';
        }
        if (modalTitle) {
            modalTitle.textContent = trigger.getAttribute('data-title') || '';
        }
        if (modalCategory) {
            modalCategory.textContent = trigger.getAttribute('data-category') || '';
        }
        if (modalDate) {
            modalDate.textContent = trigger.getAttribute('data-date') || '';
        }
        if (modalDesc) {
            modalDesc.textContent = trigger.getAttribute('data-description') || '';
        }
    };

    const openLightbox = (trigger) => {
        lastFocusedTrigger = trigger;
        visibleTriggers = getVisibleTriggers();
        activeIndex = Math.max(0, visibleTriggers.indexOf(trigger));
        renderSlide(activeIndex);

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');

        if (closeBtn) {
            closeBtn.focus();
        }
    };

    const closeLightbox = () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');

        if (lastFocusedTrigger) {
            lastFocusedTrigger.focus();
        }
    };

    document.querySelectorAll('[data-gallery-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', () => openLightbox(trigger));
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', closeLightbox);
    }

    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeLightbox();
        }
    });

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (visibleTriggers.length === 0) {
                return;
            }
            activeIndex = (activeIndex - 1 + visibleTriggers.length) % visibleTriggers.length;
            renderSlide(activeIndex);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (visibleTriggers.length === 0) {
                return;
            }
            activeIndex = (activeIndex + 1) % visibleTriggers.length;
            renderSlide(activeIndex);
        });
    }

    document.addEventListener('keydown', (event) => {
        if (modal.classList.contains('hidden')) {
            return;
        }

        if (event.key === 'Escape') {
            closeLightbox();
        } else if (event.key === 'ArrowLeft' && prevBtn) {
            prevBtn.click();
        } else if (event.key === 'ArrowRight' && nextBtn) {
            nextBtn.click();
        }
    });
});
