(() => {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.querySelector('[data-sidebar-backdrop]');
    const openBtn = document.querySelector('[data-sidebar-open]');
    const closeBtn = document.querySelector('[data-sidebar-close]');

    const setOpen = (open) => {
        if (!sidebar || !backdrop) return;
        sidebar.dataset.open = open ? 'true' : 'false';
        backdrop.dataset.visible = open ? 'true' : 'false';
        backdrop.hidden = !open;
        document.body.style.overflow = open ? 'hidden' : '';
    };

    const toggleGroup = (button) => {
        const item = button.closest('.sidebar__item--group');
        if (!item) return;
        const open = item.classList.toggle('sidebar__item--open');
        button.setAttribute('aria-expanded', open ? 'true' : 'false');
    };

    document.addEventListener('DOMContentLoaded', () => {
        if (openBtn) {
            openBtn.addEventListener('click', (event) => {
                event.preventDefault();
                setOpen(true);
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', (event) => {
                event.preventDefault();
                setOpen(false);
            });
        }

        if (backdrop) {
            backdrop.addEventListener('click', () => setOpen(false));
        }

        document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
            button.addEventListener('click', (event) => {
                event.preventDefault();
                toggleGroup(button);
            });
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && sidebar && sidebar.dataset.open === 'true') {
                setOpen(false);
            }
        });
    });
})();
