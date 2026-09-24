(() => {
    const AUTO_DISMISS_MS = 6000;

    const dismiss = (flash) => {
        flash.style.transition = 'opacity 0.2s ease';
        flash.style.opacity = '0';
        window.setTimeout(() => flash.remove(), 220);
    };

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[data-flash]').forEach((flash) => {
            const closeBtn = flash.querySelector('[data-flash-close]');
            if (closeBtn) {
                closeBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    dismiss(flash);
                });
            }
            window.setTimeout(() => dismiss(flash), AUTO_DISMISS_MS);
        });
    });
})();
