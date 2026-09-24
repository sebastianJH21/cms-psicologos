(() => {
    const modos = document.querySelectorAll('input[name="modo_logo"]');
    const grupos = document.querySelectorAll('.logo-page__group[data-show-for]');

    const actualizar = () => {
        const seleccionado = document.querySelector('input[name="modo_logo"]:checked');
        const valor = seleccionado ? seleccionado.value : 'ninguno';
        grupos.forEach((g) => {
            g.hidden = g.dataset.showFor !== valor;
        });
    };

    modos.forEach((m) => m.addEventListener('change', actualizar));
    actualizar();
})();
