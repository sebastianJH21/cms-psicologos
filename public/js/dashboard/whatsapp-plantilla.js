(() => {
    document.addEventListener('DOMContentLoaded', () => {
        const grupos = document.querySelectorAll('[data-whatsapp-grupo]');
        if (!grupos.length) {
            return;
        }

        grupos.forEach((grupo, indice) => {
            const textarea = grupo.querySelector('[data-whatsapp-plantilla]');
            const preview = grupo.querySelector('[data-whatsapp-preview]');
            const datos = indice === 0 ? window.WhatsappPreviewDatos : window.WhatsappPreviewDatosPago;

            if (!textarea || !preview || !datos) {
                return;
            }

            const renderizar = () => {
                let mensaje = textarea.value;
                Object.keys(datos).forEach((variable) => {
                    mensaje = mensaje.split(variable).join(datos[variable]);
                });
                preview.textContent = mensaje || '…';
            };

            const insertarEnCursor = (texto) => {
                const inicio = textarea.selectionStart ?? textarea.value.length;
                const fin = textarea.selectionEnd ?? textarea.value.length;
                const valorActual = textarea.value;

                textarea.value = valorActual.slice(0, inicio) + texto + valorActual.slice(fin);

                const nuevaPosicion = inicio + texto.length;
                textarea.focus();
                textarea.setSelectionRange(nuevaPosicion, nuevaPosicion);

                renderizar();
            };

            textarea.addEventListener('input', renderizar);
            renderizar();

            grupo.querySelectorAll('[data-emoji-btn]').forEach((boton) => {
                boton.addEventListener('click', (evento) => {
                    evento.preventDefault();
                    insertarEnCursor(boton.textContent.trim());
                });
            });
        });
    });
})();
