(() => {
    'use strict';

    const initTestDb = () => {
        const btn = document.getElementById('test-db-btn');
        const result = document.getElementById('test-db-result');
        if (!btn || !result || !window.PSICOCMS_INSTALL) {
            return;
        }

        btn.addEventListener('click', async (event) => {
            event.preventDefault();
            result.textContent = 'Probando…';
            result.className = 'test-result';

            const payload = new FormData();
            payload.append('db_host', document.getElementById('db_host').value);
            payload.append('db_port', document.getElementById('db_port').value);
            payload.append('db_username', document.getElementById('db_username').value);
            payload.append('db_password', document.getElementById('db_password').value);

            try {
                const response = await fetch(window.PSICOCMS_INSTALL.testDbUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': window.PSICOCMS_INSTALL.csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: payload
                });
                const data = await response.json();
                if (data.ok) {
                    result.textContent = '✓ Conexión correcta';
                    result.classList.add('test-result--ok');
                } else {
                    result.textContent = '✗ ' + (data.error || 'No se pudo conectar');
                    result.classList.add('test-result--ko');
                }
            } catch (err) {
                result.textContent = '✗ Error de red';
                result.classList.add('test-result--ko');
            }
        });
    };

    const initRepeaters = () => {
        const repeaters = document.querySelectorAll('[data-repeater]');
        repeaters.forEach((repeater) => {
            const name = repeater.dataset.name;
            const list = repeater.querySelector('[data-repeater-list]');
            const template = repeater.querySelector('[data-repeater-template]');
            const addBtn = repeater.querySelector('[data-repeater-add]');
            let counter = 0;

            const reindex = () => {
                const rows = list.querySelectorAll('.repeater__row');
                rows.forEach((row, idx) => {
                    row.querySelectorAll('[data-name]').forEach((field) => {
                        const fieldName = field.dataset.name;
                        field.name = `${name}[${idx}][${fieldName}]`;
                    });
                });
            };

            const createRow = () => {
                const fragment = template.content.cloneNode(true);
                const row = fragment.querySelector('.repeater__row');
                const removeBtn = row.querySelector('[data-repeater-remove]');
                if (removeBtn) {
                    removeBtn.addEventListener('click', (event) => {
                        event.preventDefault();
                        row.remove();
                        reindex();
                    });
                }
                list.appendChild(row);
                counter += 1;
                reindex();
            };

            addBtn.addEventListener('click', (event) => {
                event.preventDefault();
                createRow();
            });
        });
    };

    const initUploadPreview = () => {
        const input = document.querySelector('[data-upload-input]');
        if (!input) {
            return;
        }
        const filename = document.getElementById('upload-filename');
        const preview = document.getElementById('upload-preview');

        input.addEventListener('change', () => {
            const file = input.files && input.files[0];
            if (!file) {
                return;
            }
            if (filename) {
                filename.textContent = file.name;
            }
            if (preview) {
                const reader = new FileReader();
                reader.addEventListener('load', () => {
                    preview.src = reader.result;
                    preview.hidden = false;
                });
                reader.readAsDataURL(file);
            }
        });
    };

    document.addEventListener('DOMContentLoaded', () => {
        initTestDb();
        initRepeaters();
        initUploadPreview();
    });
})();
