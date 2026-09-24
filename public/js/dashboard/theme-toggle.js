document.addEventListener('DOMContentLoaded', () => {
    const html = document.documentElement;
    const btn = document.getElementById('btn-theme-toggle');
    const modal = document.getElementById('modal-tema-dashboard');
    const closeBtn = document.getElementById('modal-tema-close');
    const form = document.getElementById('form-tema-dashboard');

    if (!btn || !modal) return;

    const applyTheme = (mode, color) => {
        html.setAttribute('data-theme', mode);
        if (color) {
            html.style.setProperty('--color-primary', color);
            html.style.setProperty('--color-primary-dark', shadeColor(color, -20));
            html.style.setProperty('--color-primary-soft', shadeColor(color, 85));
            html.style.setProperty('--color-accent', shadeColor(color, 15));
        }
    };

    const shadeColor = (hex, percent) => {
        const num = parseInt(hex.replace('#', ''), 16);
        const r = Math.min(255, Math.max(0, (num >> 16) + Math.round(255 * percent / 100)));
        const g = Math.min(255, Math.max(0, ((num >> 8) & 0x00FF) + Math.round(255 * percent / 100)));
        const b = Math.min(255, Math.max(0, (num & 0x0000FF) + Math.round(255 * percent / 100)));
        return '#' + ((1 << 24) | (r << 16) | (g << 8) | b).toString(16).slice(1);
    };

    // Apply saved preferences on load
    const savedMode = html.getAttribute('data-theme') || 'light';
    const savedColor = html.dataset.primaryColor || null;
    if (savedColor) applyTheme(savedMode, savedColor);

    const colorInput = document.getElementById('input-primary-color');

    // Color swatches
    modal.querySelectorAll('.color-swatch').forEach(swatch => {
        swatch.addEventListener('click', () => {
            modal.querySelectorAll('.color-swatch').forEach(s => s.classList.remove('color-swatch--selected'));
            swatch.classList.add('color-swatch--selected');

            const color = swatch.dataset.color;
            if (colorInput) colorInput.value = color;
            const modeInput = modal.querySelector('input[name="theme_mode"]:checked');
            const mode = modeInput ? modeInput.value : 'light';
            applyTheme(mode, color);
        });
    });

    // Mode radios
    modal.querySelectorAll('input[name="theme_mode"]').forEach(radio => {
        radio.addEventListener('change', () => {
            const selectedSwatch = modal.querySelector('.color-swatch--selected');
            const color = selectedSwatch ? selectedSwatch.dataset.color : null;
            applyTheme(radio.value, color);
        });
    });

    btn.addEventListener('click', () => {
        modal.removeAttribute('hidden');
        document.body.style.overflow = 'hidden';

        // Mark current selections
        const currentMode = html.getAttribute('data-theme') || 'light';
        const modeRadio = modal.querySelector(`input[name="theme_mode"][value="${currentMode}"]`);
        if (modeRadio) modeRadio.checked = true;
    });

    const closeModal = () => {
        modal.setAttribute('hidden', '');
        document.body.style.overflow = '';
    };

    closeBtn.addEventListener('click', closeModal);
    const closeBtnFooter = document.getElementById('modal-tema-close-footer');
    if (closeBtnFooter) closeBtnFooter.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && !modal.hasAttribute('hidden')) closeModal();
    });

    // Save via AJAX
    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) closeModal();
        });
    });
});
