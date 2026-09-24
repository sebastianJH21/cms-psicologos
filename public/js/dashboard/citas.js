(() => {
    const form = document.getElementById('form-filtros-citas');
    const wrapper = document.getElementById('tabla-citas-wrapper');
    if (!form || !wrapper) return;

    const cargar = async (url) => {
        wrapper.classList.add('tabla-citas--loading');

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

            while (wrapper.firstChild) {
                wrapper.removeChild(wrapper.firstChild);
            }

            const body = doc.body;
            while (body.firstChild) {
                wrapper.appendChild(body.firstChild);
            }
        } catch {
            window.location.href = url;
        } finally {
            wrapper.classList.remove('tabla-citas--loading');
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

    ['filtro-modalidad', 'filtro-estado', 'filtro-desde', 'filtro-hasta'].forEach((id) => {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', filtrar);
    });

    const periodoInput = document.getElementById('filtro-periodo');
    document.querySelectorAll('.citas-tab').forEach((tab) => {
        tab.addEventListener('click', () => {
            const valor = tab.dataset.periodo;
            if (!valor || !periodoInput) return;
            periodoInput.value = valor;
            document.querySelectorAll('.citas-tab').forEach((t) => t.classList.toggle('is-active', t === tab));
            filtrar();
        });
    });

    const limpiarBtn = document.getElementById('btn-limpiar-citas');
    if (limpiarBtn) {
        limpiarBtn.addEventListener('click', () => {
            const qEl = document.getElementById('filtro-q');
            const modalidadEl = document.getElementById('filtro-modalidad');
            const estadoEl = document.getElementById('filtro-estado');
            const desdeEl = document.getElementById('filtro-desde');
            const hastaEl = document.getElementById('filtro-hasta');
            if (qEl) qEl.value = '';
            if (modalidadEl) modalidadEl.value = '';
            if (estadoEl) estadoEl.value = '';
            if (desdeEl) desdeEl.value = '';
            if (hastaEl) hastaEl.value = '';
            filtrar();
        });
    }
})();
