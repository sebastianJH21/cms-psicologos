(() => {
    const modal = document.getElementById('modal-icon-picker');
    if (!modal) return;

    const grid = document.getElementById('icon-picker-grid');
    const search = document.getElementById('icon-picker-search');
    const closers = modal.querySelectorAll('[data-icon-picker-close]');

    let activeInput = null;
    let renderedAll = false;

    const ICONS = [
        'fa-brain', 'fa-heart', 'fa-heart-pulse', 'fa-hand-holding-heart',
        'fa-people-arrows', 'fa-people-group', 'fa-person', 'fa-person-walking',
        'fa-user', 'fa-user-doctor', 'fa-user-nurse', 'fa-users',
        'fa-comments', 'fa-comment', 'fa-comment-medical', 'fa-comment-dots',
        'fa-house', 'fa-house-medical', 'fa-bed', 'fa-couch',
        'fa-leaf', 'fa-tree', 'fa-seedling', 'fa-spa', 'fa-yin-yang',
        'fa-sun', 'fa-moon', 'fa-cloud', 'fa-cloud-sun',
        'fa-lightbulb', 'fa-fire', 'fa-bolt', 'fa-star',
        'fa-medal', 'fa-trophy', 'fa-award', 'fa-ribbon',
        'fa-book', 'fa-book-open', 'fa-book-medical', 'fa-graduation-cap',
        'fa-clipboard', 'fa-clipboard-list', 'fa-clipboard-check', 'fa-file-medical',
        'fa-notes-medical', 'fa-stethoscope', 'fa-pills', 'fa-prescription-bottle',
        'fa-children', 'fa-baby', 'fa-child', 'fa-person-cane',
        'fa-female', 'fa-male', 'fa-venus', 'fa-mars', 'fa-venus-mars',
        'fa-handshake', 'fa-hands', 'fa-hands-praying', 'fa-hands-clapping',
        'fa-eye', 'fa-ear-listen', 'fa-face-smile', 'fa-face-meh', 'fa-face-frown',
        'fa-face-grin-hearts', 'fa-face-tired', 'fa-face-sad-tear', 'fa-face-laugh-beam',
        'fa-shield', 'fa-shield-halved', 'fa-lock', 'fa-key',
        'fa-globe', 'fa-globe-europe', 'fa-earth-europe', 'fa-location-dot',
        'fa-phone', 'fa-mobile-screen', 'fa-envelope', 'fa-paper-plane',
        'fa-calendar', 'fa-calendar-check', 'fa-calendar-day', 'fa-clock',
        'fa-magnifying-glass', 'fa-puzzle-piece', 'fa-chart-line', 'fa-bullseye',
        'fa-compass', 'fa-route', 'fa-map', 'fa-flag',
        'fa-microphone', 'fa-headphones', 'fa-music', 'fa-feather',
        'fa-wand-magic-sparkles', 'fa-infinity', 'fa-quote-left', 'fa-circle-question',
        'fa-circle-check', 'fa-circle-info', 'fa-circle-exclamation', 'fa-droplet',
        'fa-mug-hot', 'fa-mug-saucer', 'fa-cookie-bite', 'fa-apple-whole',
        'fa-baby-carriage', 'fa-school', 'fa-briefcase', 'fa-suitcase',
        'fa-running', 'fa-walking', 'fa-bicycle', 'fa-dumbbell',
        'fa-handshake-angle', 'fa-hand-fist', 'fa-hand-peace', 'fa-thumbs-up',
        'fa-headset', 'fa-video', 'fa-laptop', 'fa-desktop',
        'fa-anchor', 'fa-mountain', 'fa-water', 'fa-umbrella',
        'fa-bell', 'fa-bell-concierge', 'fa-gift', 'fa-cake-candles',
        // Salud, cuerpo y naturaleza
        'fa-lungs', 'fa-bone', 'fa-tooth', 'fa-dna', 'fa-microscope',
        'fa-syringe', 'fa-hospital', 'fa-hospital-user', 'fa-x-ray', 'fa-weight-scale',
        'fa-wind', 'fa-fire-flame-curved', 'fa-earth-americas', 'fa-paw', 'fa-dove',
        'fa-fish', 'fa-temperature-half', 'fa-vial', 'fa-flask', 'fa-capsules',
        'fa-tablets', 'fa-wheelchair', 'fa-person-running', 'fa-person-swimming',
        'fa-person-biking', 'fa-heart-circle-check', 'fa-hand-sparkles', 'fa-cloud-rain',
        'fa-snowflake', 'fa-circle-nodes',
    ];

    const render = (filtro = '') => {
        while (grid.firstChild) grid.removeChild(grid.firstChild);

        const f = filtro.trim().toLowerCase();
        const lista = f ? ICONS.filter((i) => i.toLowerCase().includes(f)) : ICONS;

        if (lista.length === 0) {
            const msg = document.createElement('p');
            msg.className = 'icon-picker__empty';
            msg.textContent = 'No se encontraron iconos.';
            grid.appendChild(msg);
            return;
        }

        lista.forEach((iconName) => {
            const fullName = `fa-solid ${iconName}`;
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'icon-picker__item';
            btn.title = iconName;
            btn.dataset.icon = fullName;

            const i = document.createElement('i');
            i.className = fullName;
            i.setAttribute('aria-hidden', 'true');
            btn.appendChild(i);

            const lbl = document.createElement('span');
            lbl.className = 'icon-picker__label';
            lbl.textContent = iconName.replace(/^fa-/, '');
            btn.appendChild(lbl);

            btn.addEventListener('click', () => {
                if (activeInput) {
                    activeInput.value = fullName;
                    activeInput.dispatchEvent(new Event('input', { bubbles: true }));
                    activeInput.dispatchEvent(new Event('change', { bubbles: true }));
                    actualizarPreview(activeInput);
                }
                close();
            });

            grid.appendChild(btn);
        });
    };

    const actualizarPreview = (input) => {
        const previewId = input.dataset.iconPreview;
        if (!previewId) return;
        const preview = document.getElementById(previewId);
        if (!preview) return;
        preview.className = input.value || 'fa-solid fa-icons';
    };

    const open = (input) => {
        activeInput = input;
        modal.hidden = false;
        if (!renderedAll) {
            render('');
            renderedAll = true;
        }
        search.value = '';
        setTimeout(() => search.focus(), 50);
    };

    const close = () => {
        modal.hidden = true;
        activeInput = null;
    };

    document.querySelectorAll('[data-icon-picker-trigger]').forEach((trigger) => {
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            const targetId = trigger.dataset.iconPickerTrigger;
            const input = document.getElementById(targetId);
            if (input) open(input);
        });
    });

    // inicializar previews
    document.querySelectorAll('[data-icon-preview]').forEach((input) => {
        actualizarPreview(input);
        input.addEventListener('input', () => actualizarPreview(input));
    });

    closers.forEach((el) => el.addEventListener('click', close));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.hidden) close();
    });

    let timer;
    search.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => render(search.value), 150);
    });
})();
