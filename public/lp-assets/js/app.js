/**
 * PT Permata Kasih Anugerah - Core Frontend Interactions (Vanilla JS)
 */

document.addEventListener('DOMContentLoaded', () => {
    refreshLucideIcons();
    initStickyHeader();
    initMobileNavigation();
    initFaqAccordion();
    initWorkerDetailThumbnails();
    initScrollReveal();
    initContactFormValidation();
});

function refreshLucideIcons() {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
}

window.pkaRefreshIcons = refreshLucideIcons;

/**
 * Add subtle elevation to sticky header on scroll.
 */
function initStickyHeader() {
    const header = document.getElementById('siteHeader');
    if (!header) {
        return;
    }

    const toggleShadow = () => {
        if (window.scrollY > 16) {
            header.classList.add('shadow-md');
        } else {
            header.classList.remove('shadow-md');
        }
    };

    toggleShadow();
    window.addEventListener('scroll', toggleShadow, { passive: true });
}

/**
 * Responsive mobile navigation drawer with keyboard support.
 */
function initMobileNavigation() {
    const toggleBtn = document.getElementById('mobileMenuToggle');
    const menu = document.getElementById('mobileNavigationMenu');
    if (!toggleBtn || !menu) {
        return;
    }

    const setOpen = (isOpen) => {
        menu.classList.toggle('hidden', !isOpen);
        toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    };

    toggleBtn.addEventListener('click', () => {
        const isCurrentlyHidden = menu.classList.contains('hidden');
        setOpen(isCurrentlyHidden);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !menu.classList.contains('hidden')) {
            setOpen(false);
            toggleBtn.focus();
        }
    });
}

/**
 * Accessible FAQ accordion interaction.
 */
function initFaqAccordion() {
    const groups = document.querySelectorAll('[data-faq-group]');
    groups.forEach((group) => {
        const triggers = group.querySelectorAll('[data-faq-trigger]');
        triggers.forEach((trigger) => {
            trigger.addEventListener('click', () => {
                const panelId = trigger.getAttribute('aria-controls');
                const targetPanel = panelId ? document.getElementById(panelId) : null;
                if (!targetPanel) {
                    return;
                }

                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';

                triggers.forEach((otherTrigger) => {
                    const otherId = otherTrigger.getAttribute('aria-controls');
                    const otherPanel = otherId ? document.getElementById(otherId) : null;
                    const otherIcon = otherTrigger.querySelector('[data-faq-icon]');

                    otherTrigger.setAttribute('aria-expanded', 'false');
                    if (otherPanel) {
                        otherPanel.classList.add('hidden');
                    }
                    if (otherIcon) {
                        otherIcon.classList.remove('rotate-180');
                    }
                });

                if (!isExpanded) {
                    trigger.setAttribute('aria-expanded', 'true');
                    targetPanel.classList.remove('hidden');
                    const icon = trigger.querySelector('[data-faq-icon]');
                    if (icon) {
                        icon.classList.add('rotate-180');
                    }
                }
            });
        });
    });
}

/**
 * Worker detail photo gallery thumbnail switcher.
 */
function initWorkerDetailThumbnails() {
    const mainPhoto = document.getElementById('workerMainPhoto');
    const thumbButtons = document.querySelectorAll('[data-worker-thumb]');
    if (!mainPhoto || thumbButtons.length === 0) {
        return;
    }

    thumbButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const nextSrc = button.getAttribute('data-worker-thumb');
            const nextAlt = button.getAttribute('data-worker-thumb-alt');
            if (!nextSrc) {
                return;
            }

            mainPhoto.src = nextSrc;
            if (nextAlt) {
                mainPhoto.alt = nextAlt;
            }

            thumbButtons.forEach((btn) => {
                btn.classList.remove('border-teal', 'ring-2', 'ring-teal/20');
                btn.classList.add('border-border-subtle');
            });

            button.classList.remove('border-border-subtle');
            button.classList.add('border-teal', 'ring-2', 'ring-teal/20');
        });
    });
}

/**
 * Subtle scroll reveal animations respecting reduced-motion preference.
 */
function initScrollReveal() {
    const sections = document.querySelectorAll('.reveal-section');
    if (sections.length === 0) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
        sections.forEach((section) => section.classList.add('is-visible'));
        return;
    }

    const observer = new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.08, rootMargin: '0px 0px -30px 0px' }
    );

    sections.forEach((section) => observer.observe(section));
}

/**
 * Contact page client-side form validation and demonstration submission state.
 */
function initContactFormValidation() {
    const form = document.getElementById('contactForm');
    if (!form) {
        return;
    }

    const successBox = document.getElementById('contactSuccessState');
    const waDirectLink = document.getElementById('contactWhatsAppDirectLink');

    const validateField = (input) => {
        const errorEl = form.querySelector(`[data-error-for="${input.id}"]`);
        const value = input.value.trim();
        let valid = value !== '';

        if (valid && input.type === 'email') {
            valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
        }

        if (errorEl) {
            errorEl.classList.toggle('hidden', valid);
        }
        input.classList.toggle('border-red-500', !valid);
        input.setAttribute('aria-invalid', valid ? 'false' : 'true');

        return valid;
    };

    const requiredFields = Array.from(form.querySelectorAll('input[required], textarea[required]'));

    requiredFields.forEach((field) => {
        field.addEventListener('blur', () => validateField(field));
        field.addEventListener('input', () => {
            if (field.getAttribute('aria-invalid') === 'true') {
                validateField(field);
            }
        });
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();

        let allValid = true;
        requiredFields.forEach((field) => {
            if (!validateField(field)) {
                allValid = false;
            }
        });

        if (!allValid) {
            const firstInvalid = requiredFields.find((field) => field.getAttribute('aria-invalid') === 'true');
            if (firstInvalid) {
                firstInvalid.focus();
            }
            return;
        }

        const fullName = document.getElementById('contactFullName')?.value.trim() || '';
        const email = document.getElementById('contactEmail')?.value.trim() || '';
        const phone = document.getElementById('contactPhone')?.value.trim() || '';
        const subject = document.getElementById('contactSubject')?.value.trim() || '';
        const message = document.getElementById('contactMessage')?.value.trim() || '';
        const waNumber = form.getAttribute('data-whatsapp-number') || '6282299109766';

        const waText = [
            `Hello PT Permata Kasih Anugerah, my name is ${fullName} (${phone}, ${email}).`,
            `Subject: ${subject}.`,
            `Message: ${message}`,
        ].join(' ');

        if (waDirectLink) {
            waDirectLink.href = `https://wa.me/${waNumber}?text=${encodeURIComponent(waText)}`;
        }

        if (successBox) {
            successBox.classList.remove('hidden');
            successBox.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        form.reset();
        requiredFields.forEach((field) => {
            field.classList.remove('border-red-500');
            field.setAttribute('aria-invalid', 'false');
        });
    });
}
