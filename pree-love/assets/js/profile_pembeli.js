const saveArea = document.getElementById('saveArea');
const watchedInputs = document.querySelectorAll('[data-watch]');
const fileInput = document.getElementById('foto_profile');
const previewPhoto = document.getElementById('previewPhoto');
const previewPlaceholder = document.getElementById('previewPlaceholder');

const initialValues = {};

watchedInputs.forEach(input => {
    initialValues[input.name] = input.value;
});

function checkChanges(){

    let changed = false;

    watchedInputs.forEach(input => {

        if(input.value !== initialValues[input.name]){
            changed = true;
        }

    });

    if(fileInput.files.length > 0){
        changed = true;
    }

    saveArea.classList.toggle('show', changed);
}

watchedInputs.forEach(input => {
    input.addEventListener('input', checkChanges);
});

fileInput.addEventListener('change', function(){

    const file = this.files[0];

    if(file){

        const reader = new FileReader();

        reader.onload = function(e){

            if(previewPlaceholder){
                previewPlaceholder.classList.add('hidden');
            }

            previewPhoto.src = e.target.result;
            previewPhoto.classList.remove('hidden');
        };

        reader.readAsDataURL(file);
    }

    checkChanges();
});