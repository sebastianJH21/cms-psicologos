(() => {
    const togglePassword = (button) => {
        const wrapper = button.closest('.auth__password');
        if (!wrapper) return;
        const input = wrapper.querySelector('input');
        if (!input) return;

        const showLabel = button.querySelector('[data-icon-show]');
        const hideLabel = button.querySelector('[data-icon-hide]');
        const isText = input.type === 'text';

        input.type = isText ? 'password' : 'text';
        button.setAttribute('aria-pressed', String(!isText));
        if (showLabel && hideLabel) {
            showLabel.hidden = !isText;
            hideLabel.hidden = isText;
        }
        input.focus({ preventScroll: true });
    };

    const guardSubmit = (form) => {
        form.addEventListener('submit', (event) => {
            const submit = form.querySelector('button[type="submit"]');
            if (!submit) return;
            if (submit.dataset.busy === '1') {
                event.preventDefault();
                return;
            }
            submit.dataset.busy = '1';
            submit.disabled = true;
            submit.textContent = 'Comprobando...';
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-toggle-password]').forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                togglePassword(btn);
            });
        });

        document.querySelectorAll('form.auth__form').forEach(guardSubmit);
    });
})();
