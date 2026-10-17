import '@fontsource-variable/source-sans-3';

document.querySelectorAll('[data-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = document.querySelector(button.dataset.toggle);
        if (!target) return;

        const isOpen = target.classList.toggle('hidden') === false;
        button.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });
});
document.querySelectorAll('[data-carousel]').forEach((carousel) => {
    const slides = [...carousel.querySelectorAll('[data-slide]')];
    const dots = [...carousel.querySelectorAll('[data-dot]')];
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    let current = 0;
    let timer = null;

    const show = (index) => {
        current = (index + slides.length) % slides.length;

        slides.forEach((slide, i) => {
            const active = i === current;
            slide.classList.toggle('opacity-100', active);
            slide.classList.toggle('opacity-0', !active);
            slide.classList.toggle('pointer-events-auto', active);
            slide.classList.toggle('pointer-events-none', !active);
            slide.setAttribute('aria-hidden', active ? 'false' : 'true');
        });

        dots.forEach((dot, i) => {
            const active = i === current;
            dot.classList.toggle('bg-brand-navy', active);
            dot.classList.toggle('bg-brand-navy/50', !active);
            dot.setAttribute('aria-current', active ? 'true' : 'false');
        });
    };

    const stop = () => clearInterval(timer);
    const play = () => {
        stop();
        if (!reduceMotion && slides.length > 1) {
            timer = setInterval(() => show(current + 1), 6000);
        }
    };

    dots.forEach((dot, i) => dot.addEventListener('click', () => { show(i); play(); }));

    carousel.addEventListener('mouseenter', stop);
    carousel.addEventListener('mouseleave', play);

    // Swipe on phones
    let startX = 0;
    carousel.addEventListener('touchstart', (e) => { startX = e.touches[0].clientX; }, { passive: true });
    carousel.addEventListener('touchend', (e) => {
        const diff = e.changedTouches[0].clientX - startX;
        if (Math.abs(diff) > 50) {
            show(current + (diff < 0 ? 1 : -1));
            play();
        }
    }, { passive: true });

    show(0);
    play();
});
document.querySelectorAll('[data-gallery]').forEach((gallery) => {
    const main = gallery.querySelector('[data-gallery-main]');
    const thumbs = [...gallery.querySelectorAll('[data-gallery-thumb]')];

    thumbs.forEach((thumb) => {
        thumb.addEventListener('click', () => {
            main.src = thumb.dataset.src;
            main.alt = thumb.dataset.alt;

            thumbs.forEach((t) => {
                const active = t === thumb;
                t.classList.toggle('ring-2', active);
                t.classList.toggle('ring-brand-blue', active);
            });
        });
    });
    document.querySelectorAll('[data-slug-source]').forEach((source) => {
    const target = document.querySelector(source.dataset.slugSource);
    if (!target) return;

    let manual = target.value !== '';

    target.addEventListener('input', () => { manual = true; });

    source.addEventListener('input', () => {
        if (manual) return;

        target.value = source.value
            .toLowerCase()
            .trim()
            .replace(/&/g, ' and ')
            .replace(/[^\w\s-]/g, '')
            .replace(/[\s_-]+/g, '-')
            .replace(/^-+|-+$/g, '');
    });
});
document.querySelectorAll('[data-icon-source]').forEach((input) => {
    const target = document.querySelector(input.dataset.iconSource);
    const icon = target?.querySelector('i');
    if (!icon) return;

    const update = () => {
        const name = input.value.trim().toLowerCase();
        icon.className = /^bi-[a-z0-9-]+$/.test(name) ? 'bi ' + name : 'bi bi-question-circle';
    };

    input.addEventListener('input', update);
    update();
});
});