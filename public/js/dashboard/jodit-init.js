(() => {
    'use strict';

    const instances = new Map();

    const init = () => {
        if (typeof window.Jodit === 'undefined') {
            return;
        }

        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
        const uploadUrl = document.body.dataset.joditUploadUrl
            || (window.location.origin + '/panel-psicologa/blog/upload-imagen');

        document.querySelectorAll('textarea[data-jodit]').forEach((textarea) => {
            if (textarea.dataset.joditInit === '1') {
                return;
            }
            textarea.dataset.joditInit = '1';

            const noImage = 'joditNoImage' in textarea.dataset;

            const baseButtons = [
                'paragraph', 'bold', 'italic', 'underline', 'strikethrough', '|',
                'ul', 'ol', '|',
                'outdent', 'indent', '|',
                'link', '|',
                'align', '|',
                'hr', 'table', 'blockquote', '|',
                'undo', 'redo', '|',
                'eraser', 'fullsize',
            ];

            const fullButtons = [
                'paragraph', 'bold', 'italic', 'underline', 'strikethrough', '|',
                'ul', 'ol', '|',
                'outdent', 'indent', '|',
                'link', 'image', '|',
                'align', '|',
                'hr', 'table', 'blockquote', '|',
                'undo', 'redo', '|',
                'eraser', 'fullsize',
            ];

            const config = {
                language: 'es',
                height: 480,
                buttons: noImage ? baseButtons : fullButtons,
                buttonsMD: noImage ? baseButtons : fullButtons,
                buttonsSM: noImage ? baseButtons : fullButtons,
                buttonsXS: noImage ? baseButtons : fullButtons,
                toolbarAdaptive: false,
                statusbar: false,
                showCharsCounter: false,
                showWordsCounter: false,
                showXPathInStatusbar: false,
            };

            if (!noImage) {
                config.uploader = {
                    url: uploadUrl,
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        'Accept': 'application/json',
                    },
                    format: 'json',
                    method: 'POST',
                    filesVariableName: () => 'files',
                    isSuccess: (resp) => resp && resp.success === true,
                    getMessage: (resp) => resp && resp.message ? resp.message : 'Error al subir la imagen.',
                    process: (resp) => ({
                        files: (resp.files || []).map((f) => f.url),
                        path: '',
                        baseurl: '',
                    }),
                    defaultHandlerSuccess: function (data) {
                        const files = data.files || [];
                        files.forEach((url) => {
                            const img = this.j.createInside.element('img');
                            img.setAttribute('src', url);
                            this.j.s.insertImage(img);
                        });
                    },
                };
            }

            const editor = window.Jodit.make(textarea, config);
            instances.set(textarea, editor);

            const form = textarea.closest('form');
            if (form && !form.dataset.joditSyncBound) {
                form.dataset.joditSyncBound = '1';
                form.addEventListener('submit', () => {
                    form.querySelectorAll('textarea[data-jodit]').forEach((ta) => {
                        const inst = instances.get(ta)
                            || (ta.id && window.Jodit?.instances?.[ta.id]);
                        if (inst) {
                            ta.value = inst.value;
                        }
                    });
                });
            }
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
