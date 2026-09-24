document.addEventListener('DOMContentLoaded', () => {
    const list = document.querySelector('.features-list');
    if (!list) return;

    const toggleUrl = list.dataset.toggleUrl;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    list.addEventListener('change', (e) => {
        const checkbox = e.target;
        if (!checkbox.matches('input[type="checkbox"][data-feature]')) return;

        const feature = checkbox.dataset.feature;
        const enabled = checkbox.checked ? 1 : 0;
        const item = checkbox.closest('.feature-item');
        const status = item?.querySelector('.feature-item__status');

        setStatus(status, 'loading', '');

        fetch(toggleUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ feature, value: enabled }),
        })
        .then(res => {
            if (!res.ok) throw new Error('Error del servidor');
            return res.json();
        })
        .then(() => {
            setStatus(status, 'ok', enabled ? 'Activado' : 'Desactivado');
            toggleItemStyle(item, enabled);
            clearAfter(status, 2000);
        })
        .catch(() => {
            checkbox.checked = !checkbox.checked;
            setStatus(status, 'error', 'No se pudo guardar');
            clearAfter(status, 3000);
        });
    });

    function setStatus(el, type, text) {
        if (!el) return;
        el.className = 'feature-item__status';
        el.classList.add('feature-item__status--' + type);
        el.textContent = text;
    }

    function clearAfter(el, ms) {
        if (!el) return;
        setTimeout(() => {
            el.className = 'feature-item__status';
            el.textContent = '';
        }, ms);
    }

    function toggleItemStyle(item, enabled) {
        if (!item) return;
        item.classList.toggle('feature-item--disabled', !enabled);
    }

    // Set initial disabled styling
    list.querySelectorAll('input[type="checkbox"][data-feature]').forEach(cb => {
        const item = cb.closest('.feature-item');
        if (item && !cb.checked) {
            item.classList.add('feature-item--disabled');
        }
    });
});
