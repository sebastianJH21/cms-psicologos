document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.imagen-slot__file-input').forEach(input => {
        input.addEventListener('change', () => {
            const form = input.closest('form');
            if (input.files.length > 0) {
                form.submit();
            }
        });
    });
});
