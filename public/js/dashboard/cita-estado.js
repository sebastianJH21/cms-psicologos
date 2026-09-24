(() => {
    const meta = document.querySelector('meta[name="csrf-token"]');
    const csrf = meta ? meta.getAttribute('content') : null;
    if (!csrf) return;

    const clasesEstado = ['success', 'info', 'danger', 'warning'];

    document.addEventListener('click', async (e) => {
        const btn = e.target.closest('.cita-estado-toggle');
        if (!btn) return;
        e.preventDefault();

        if (btn.dataset.loading === '1') return;

        const pareja = (btn.dataset.pair || '')
            .split(',')
            .map((s) => s.trim())
            .filter(Boolean);
        const actual = btn.dataset.estado;
        const destino = pareja.find((v) => v !== actual) || pareja[0];
        if (!destino || !btn.dataset.url) return;

        btn.dataset.loading = '1';
        btn.classList.add('cita-estado-toggle--loading');
        btn.classList.remove('cita-estado-toggle--error');

        try {
            const res = await fetch(btn.dataset.url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ estado: destino }),
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok || !data.ok) {
                btn.classList.add('cita-estado-toggle--error');
                return;
            }

            btn.dataset.estado = data.estado;

            const label = btn.querySelector('.cita-estado-toggle__label');
            if (label) label.textContent = data.estado_label;

            clasesEstado.forEach((c) => btn.classList.remove('badge--' + c));
            btn.classList.add('badge--' + (data.badge || 'info'));
        } catch {
            btn.classList.add('cita-estado-toggle--error');
        } finally {
            btn.dataset.loading = '0';
            btn.classList.remove('cita-estado-toggle--loading');
        }
    });
})();
