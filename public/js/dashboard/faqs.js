(() => {
    const list = document.getElementById('faq-list');
    if (!list) return;

    const url = list.dataset.reorderUrl;
    const csrf = list.dataset.csrf;

    let dragged = null;

    list.querySelectorAll('.faq-item').forEach((item) => {
        item.addEventListener('dragstart', (e) => {
            dragged = item;
            item.classList.add('faq-item--dragging');
            e.dataTransfer.effectAllowed = 'move';
            try { e.dataTransfer.setData('text/plain', item.dataset.id); } catch (_) {}
        });

        item.addEventListener('dragend', () => {
            if (dragged) dragged.classList.remove('faq-item--dragging');
            dragged = null;
            list.querySelectorAll('.faq-item--over').forEach((el) => el.classList.remove('faq-item--over'));
            persist();
        });

        item.addEventListener('dragover', (e) => {
            e.preventDefault();
            if (!dragged || dragged === item) return;
            const rect = item.getBoundingClientRect();
            const middle = rect.top + rect.height / 2;
            if (e.clientY < middle) {
                list.insertBefore(dragged, item);
            } else {
                list.insertBefore(dragged, item.nextSibling);
            }
        });

        item.addEventListener('dragenter', () => {
            if (dragged && dragged !== item) item.classList.add('faq-item--over');
        });

        item.addEventListener('dragleave', () => {
            item.classList.remove('faq-item--over');
        });
    });

    const persist = async () => {
        const ids = Array.from(list.querySelectorAll('.faq-item')).map((el) => el.dataset.id);
        try {
            await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ ids }),
            });
        } catch (_) {}
    };
})();
