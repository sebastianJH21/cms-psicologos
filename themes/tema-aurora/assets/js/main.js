(() => {
    // Mobile nav toggle
    const toggle = document.querySelector('.t-aurora__nav-toggle');
    const list = document.querySelector('.t-aurora__nav-list');
    if (toggle && list) {
        toggle.addEventListener('click', () => list.classList.toggle('is-open'));
        list.querySelectorAll('a').forEach((a) => a.addEventListener('click', () => list.classList.remove('is-open')));
    }

    // Scroll-top button
    const topBtn = document.getElementById('t-aurora-top');
    if (topBtn) {
        const onScroll = () => {
            if (window.scrollY > 480) topBtn.classList.add('is-show');
            else topBtn.classList.remove('is-show');
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        topBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
        onScroll();
    }

    // Reveal-on-scroll suave para secciones (opcional, sin romper si IO no existe)
    if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries) => {
            entries.forEach((e) => {
                if (e.isIntersecting) {
                    e.target.classList.add('is-revealed');
                    io.unobserve(e.target);
                }
            });
        }, { threshold: 0.12 });
        document.querySelectorAll('.t-aurora__service, .t-aurora__terapia, .t-aurora__plan, .t-aurora__post').forEach((el) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            io.observe(el);
        });
    }
})();

// Reveal class hook (añade estilos cuando el elemento se vuelve visible)
const style = document.createElement('style');
style.textContent = '.is-revealed{opacity:1 !important;transform:translateY(0) !important;}';
document.head.appendChild(style);

// Blog AJAX pagination
(() => {
    const container = document.getElementById('t-aurora__blog-ajax');
    if (!container) return;

    const cargarPagina = async (url) => {
        if (!url) return;
        container.classList.add('is-loading');
        try {
            const sep = url.includes('?') ? '&' : '?';
            const res = await fetch(url + sep + 'fragment=1', {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' }
            });
            if (!res.ok) throw new Error('Error de red');
            const html = await res.text();
            container.innerHTML = '';
            const tmp = document.createElement('div');
            tmp.innerHTML = html;
            while (tmp.firstChild) container.appendChild(tmp.firstChild);
            window.history.pushState({}, '', url);
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch (e) {
            // silencioso
        } finally {
            container.classList.remove('is-loading');
        }
    };

    container.addEventListener('click', (e) => {
        const link = e.target.closest('.t-aurora__pagination-link');
        if (!link || link.classList.contains('t-aurora__pagination-link--disabled') || link.classList.contains('t-aurora__pagination-link--active')) return;
        const href = link.getAttribute('href');
        if (!href) return;
        e.preventDefault();
        cargarPagina(href);
    });
})();
