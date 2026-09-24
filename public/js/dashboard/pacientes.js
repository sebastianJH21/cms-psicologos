(() => {
    const form = document.getElementById('form-filtros-pacientes');
    const wrapper = document.getElementById('tabla-pacientes-wrapper');
    if (!form || !wrapper) return;

    const cargar = async (url) => {
        wrapper.classList.add('tabla-pacientes--loading');
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
            wrapper.classList.remove('tabla-pacientes--loading');
        }
    };

    const filtrar = () => {
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
        filtrar();
    });

    const qInput = document.getElementById('filtro-q');
    if (qInput) {
        let timer;
        qInput.addEventListener('input', () => {
            clearTimeout(timer);
            timer = setTimeout(filtrar, 400);
        });
    }

    const origenSelect = document.getElementById('filtro-origen');
    if (origenSelect) {
        origenSelect.addEventListener('change', filtrar);
    }

    const limpiarBtn = document.getElementById('btn-limpiar-filtros');
    if (limpiarBtn) {
        limpiarBtn.addEventListener('click', () => {
            const qEl = document.getElementById('filtro-q');
            const orEl = document.getElementById('filtro-origen');
            if (qEl) qEl.value = '';
            if (orEl) orEl.value = '';
            filtrar();
        });
    }
})();
