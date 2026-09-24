(() => {
    const form = document.getElementById('form-buscar-historias');
    const wrapper = document.getElementById('tabla-historias-wrapper');
    if (!form || !wrapper) return;

    const cargar = async (url) => {
        wrapper.classList.add('historias-tabla--loading');
        try {
            const res = await fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });
            if (!res.ok) throw new Error('Error de servidor');
            const data = await res.json();
            const parser = new DOMParser();
            const doc = parser.parseFromString(data.html, 'text/html');
            while (wrapper.firstChild) wrapper.removeChild(wrapper.firstChild);
            const body = doc.body;
            while (body.firstChild) wrapper.appendChild(body.firstChild);
        } catch {
            window.location.href = url;
        } finally {
            wrapper.classList.remove('historias-tabla--loading');
        }
    };

    const buscar = () => {
        const params = new URLSearchParams(new FormData(form));
        const url = new URL(form.action, window.location.origin);
        cargar(url.toString() + '?' + params.toString());
    };

    wrapper.addEventListener('click', (e) => {
        const link = e.target.closest('.pagination-dash__link');
        if (!link || link.tagName !== 'A') return;
        e.preventDefault();
        cargar(link.href);
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        buscar();
    });

    const qInput = document.getElementById('filtro-q');
    if (qInput) {
        let timer;
        qInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(buscar, 400);
        });
    }

    const limpiarBtn = document.getElementById('btn-limpiar-historias');
    if (limpiarBtn) {
        limpiarBtn.addEventListener('click', () => {
            const qEl = document.getElementById('filtro-q');
            if (qEl) qEl.value = '';
            buscar();
        });
    }
})();
