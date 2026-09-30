(() => {
    const container = document.getElementById('t-organico__blog-ajax');
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
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            container.replaceChildren();
            Array.from(doc.body.childNodes).forEach((node) => {
                container.appendChild(document.importNode(node, true));
            });
            window.history.pushState({}, '', url);
            container.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } catch {
            // silencioso
        } finally {
            container.classList.remove('is-loading');
        }
    };

    container.addEventListener('click', (e) => {
        const link = e.target.closest('.t-organico__pagination-link');
        if (!link || link.classList.contains('t-organico__pagination-link--disabled') || link.classList.contains('t-organico__pagination-link--active')) return;
        const href = link.getAttribute('href');
        if (!href) return;
        e.preventDefault();
        cargarPagina(href);
    });

    document.querySelectorAll('.t-organico__filter').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const href = btn.getAttribute('href');
            if (!href) return;
            e.preventDefault();
            document.querySelectorAll('.t-organico__filter').forEach((b) => b.classList.remove('is-active'));
            btn.classList.add('is-active');
            cargarPagina(href);
        });
    });
})();
