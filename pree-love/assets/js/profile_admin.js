const profileForm = document.getElementById('profileForm');
const saveBtn = document.getElementById('saveBtn');

if (profileForm && saveBtn) {
    const inputs = profileForm.querySelectorAll('input');
    const initialValues = {};

    inputs.forEach(input => {
        if (input.type !== 'file') {
            const key = input.name || input.id;
            initialValues[key] = input.value;
        }
    });

    function checkChanges() {
        let changed = false;

        inputs.forEach(input => {
            if (input.type === 'file') {
                if (input.files.length > 0) {
                    changed = true;
                }
            } else {
                const key = input.name || input.id;

                if (initialValues[key] !== input.value) {
                    changed = true;
                }
            }
        });

        if (changed) {
            saveBtn.classList.add('show');
        } else {
            saveBtn.classList.remove('show');
        }
    }

    inputs.forEach(input => {
        input.addEventListener('input', checkChanges);
        input.addEventListener('change', checkChanges);
    });
}