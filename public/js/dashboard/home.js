(() => {
    const tabs = document.querySelectorAll('[data-availability-tab]');
    const lists = document.querySelectorAll('.availability-list');

    const activarTab = (modalidad) => {
        tabs.forEach((tab) => {
            const activa = tab.dataset.availabilityTab === modalidad;
            tab.classList.toggle('availability-tabs__btn--active', activa);
            tab.setAttribute('aria-selected', activa ? 'true' : 'false');
        });
        lists.forEach((list) => {
            list.classList.toggle('availability-list--active', list.dataset.availabilityModalidad === modalidad);
        });
    };

    tabs.forEach((tab) => {
        tab.addEventListener('click', (event) => {
            event.preventDefault();
            activarTab(tab.dataset.availabilityTab);
        });
    });
})();
