(() => {
    const modal = document.getElementById('modal-confirm');
    if (!modal) return;

    const titleEl = modal.querySelector('[data-modal-title]');
    const textEl = modal.querySelector('[data-modal-text]');
    const acceptBtn = modal.querySelector('[data-modal-accept]');
    const cancelBtn = modal.querySelector('[data-modal-cancel]');
    const closers = modal.querySelectorAll('[data-modal-close]');

    let activeForm = null;
    let lastFocus = null;

    const open = ({ title, text, confirmLabel, confirmStyle }) => {
        if (titleEl && title) titleEl.textContent = title;
        if (textEl && text) textEl.textContent = text;
        if (acceptBtn) {
            acceptBtn.textContent = confirmLabel || 'Confirmar';
            acceptBtn.classList.remove('btn--primary', 'btn--danger');
            acceptBtn.classList.add(confirmStyle === 'primary' ? 'btn--primary' : 'btn--danger');
        }
        modal.hidden = false;
        modal.setAttribute('aria-hidden', 'false');
        lastFocus = document.activeElement;
        if (acceptBtn) acceptBtn.focus();
    };

    const close = () => {
        modal.hidden = true;
        modal.setAttribute('aria-hidden', 'true');
        activeForm = null;
        if (lastFocus && typeof lastFocus.focus === 'function') {
            lastFocus.focus();
        }
    };

    closers.forEach((el) => el.addEventListener('click', (event) => {
        event.preventDefault();
        close();
    }));

    if (cancelBtn) {
        cancelBtn.addEventListener('click', (event) => {
            event.preventDefault();
            close();
        });
    }

    if (acceptBtn) {
        acceptBtn.addEventListener('click', async (event) => {
            event.preventDefault();
            if (activeForm) {
                if (activeForm.dataset.ajaxDelete === 'true') {
                    await handleAjaxDelete();
                } else {
                    activeForm.dataset.confirmed = '1';
                    activeForm.submit();
                }
            }
            close();
        });
    }

    const handleAjaxDelete = async () => {
        if (!activeForm) return;

        const url = activeForm.action;
        const csrfToken = activeForm.querySelector('[name="_token"]')?.value;
        const target = activeForm.dataset.deleteTarget;

        try {
            const response = await fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
            });

            if (response.ok) {
                const data = await response.json();
                if (data.success && target) {
                    const closest = activeForm.closest(target);
                    if (closest) {
                        closest.style.opacity = '0.5';
                        closest.style.transform = 'scale(0.95)';
                        setTimeout(() => closest.remove(), 200);
                    }
                }
            }
        } catch (err) {
            console.error('Delete failed:', err);
        }
    };

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !modal.hidden) {
            close();
        }
    });

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!(form instanceof HTMLFormElement)) return;
        if (!form.hasAttribute('data-confirm')) return;
        if (form.dataset.confirmed === '1') return;

        event.preventDefault();
        activeForm = form;
        open({
            title: form.dataset.confirmTitle || 'Confirmar acción',
            text: form.dataset.confirm || '¿Seguro que quieres continuar?',
            confirmLabel: form.dataset.confirmLabel || 'Confirmar',
            confirmStyle: form.dataset.confirmStyle || 'danger',
        });
    });
})();
