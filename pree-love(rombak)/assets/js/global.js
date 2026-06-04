document.addEventListener("DOMContentLoaded", function () {
    initPasswordToggle();
    initPesanScroll();
    initPesanAttachment();
    initPesanEnterSubmit();
    initProductImagePreview();
});

/* ===============================
   PASSWORD TOGGLE
   eye.png  = password tertutup
   view.png = password terlihat
================================ */
function initPasswordToggle() {
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    const passwordIcon = document.getElementById("passwordIcon");

    if (!togglePassword || !passwordInput || !passwordIcon) return;

    const eyeIcon = togglePassword.dataset.eye || "../assets/icons/eye.png";
    const viewIcon = togglePassword.dataset.view || "../assets/icons/view.png";

    // Paksa ikon awal langsung muncul saat halaman dibuka
    passwordInput.type = "password";
    passwordIcon.src = eyeIcon;

    togglePassword.addEventListener("click", function () {
        if (passwordInput.type === "password") {
            passwordInput.type = "text";
            passwordIcon.src = viewIcon;
            togglePassword.setAttribute("aria-label", "Sembunyikan password");
        } else {
            passwordInput.type = "password";
            passwordIcon.src = eyeIcon;
            togglePassword.setAttribute("aria-label", "Tampilkan password");
        }
    });
}

/* ===============================
   PESAN: AUTO SCROLL KE BAWAH
================================ */
function initPesanScroll() {
    const pesanBody = document.getElementById("pesanBody");

    if (pesanBody) {
        pesanBody.scrollTop = pesanBody.scrollHeight;
    }
}

/* ===============================
   PESAN: TOMBOL + UNTUK FOTO
================================ */
function initPesanAttachment() {
    const attachButton = document.getElementById("attachButton");
    const attachMenu = document.getElementById("attachMenu");

    const galleryInput = document.getElementById("galleryInput");
    const cameraInput = document.getElementById("cameraInput");

    const filePreview = document.getElementById("filePreview");
    const fileName = document.getElementById("fileName");
    const removeFile = document.getElementById("removeFile");

    if (!attachButton || !attachMenu) return;

    attachButton.addEventListener("click", function (event) {
        event.stopPropagation();
        attachMenu.classList.toggle("show");
    });

    document.addEventListener("click", function (event) {
        if (!attachMenu.contains(event.target) && !attachButton.contains(event.target)) {
            attachMenu.classList.remove("show");
        }
    });

    function showSelectedFile(file) {
        if (!file) return;

        const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp"];

        if (!allowedTypes.includes(file.type)) {
            alert("Format gambar harus JPG, JPEG, PNG, atau WEBP.");
            clearSelectedFile();
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert("Ukuran gambar maksimal 5MB.");
            clearSelectedFile();
            return;
        }

        if (fileName) {
            fileName.textContent = file.name;
        }

        if (filePreview) {
            filePreview.classList.add("show");
        }

        attachMenu.classList.remove("show");
    }

    function clearSelectedFile() {
        if (galleryInput) galleryInput.value = "";
        if (cameraInput) cameraInput.value = "";

        if (fileName) {
            fileName.textContent = "";
        }

        if (filePreview) {
            filePreview.classList.remove("show");
        }
    }

    if (galleryInput) {
        galleryInput.addEventListener("change", function () {
            if (cameraInput) cameraInput.value = "";
            showSelectedFile(this.files[0]);
        });
    }

    if (cameraInput) {
        cameraInput.addEventListener("change", function () {
            if (galleryInput) galleryInput.value = "";
            showSelectedFile(this.files[0]);
        });
    }

    if (removeFile) {
        removeFile.addEventListener("click", clearSelectedFile);
    }
}

/* ===============================
   PESAN: ENTER UNTUK KIRIM
   Shift + Enter untuk baris baru
================================ */
function initPesanEnterSubmit() {
    const replyTextarea = document.getElementById("replyTextarea");
    const pesanForm = document.getElementById("pesanForm");

    const galleryInput = document.getElementById("galleryInput");
    const cameraInput = document.getElementById("cameraInput");

    if (!replyTextarea || !pesanForm) return;

    replyTextarea.addEventListener("input", function () {
        autoResizeTextarea(replyTextarea);
    });

    replyTextarea.addEventListener("keydown", function (event) {
        if (event.key === "Enter" && !event.shiftKey) {
            event.preventDefault();

            const adaPesan = replyTextarea.value.trim() !== "";
            const adaGallery = galleryInput && galleryInput.files.length > 0;
            const adaCamera = cameraInput && cameraInput.files.length > 0;

            if (adaPesan || adaGallery || adaCamera) {
                pesanForm.submit();
            }
        }
    });
}

function autoResizeTextarea(textarea) {
    textarea.style.height = "auto";
    textarea.style.height = Math.min(textarea.scrollHeight, 120) + "px";
}

/* ===============================
   TAMBAH / EDIT PRODUK:
   PREVIEW FOTO PRODUK
================================ */
function initProductImagePreview() {
    const inputFoto = document.getElementById("foto_produk");
    const previewGrid = document.getElementById("previewGrid");
    const uploadBox = document.getElementById("uploadBox");

    if (!inputFoto || !previewGrid) return;

    inputFoto.addEventListener("change", function () {
        showProductPreview(inputFoto.files, previewGrid);
    });

    if (uploadBox) {
        uploadBox.addEventListener("dragover", function (event) {
            event.preventDefault();
            uploadBox.classList.add("drag-active");
        });

        uploadBox.addEventListener("dragleave", function () {
            uploadBox.classList.remove("drag-active");
        });

        uploadBox.addEventListener("drop", function (event) {
            event.preventDefault();
            uploadBox.classList.remove("drag-active");

            const droppedFiles = event.dataTransfer.files;

            if (droppedFiles.length > 0) {
                const dataTransfer = new DataTransfer();

                Array.from(droppedFiles).forEach(function (file) {
                    dataTransfer.items.add(file);
                });

                inputFoto.files = dataTransfer.files;
                showProductPreview(inputFoto.files, previewGrid);
            }
        });
    }
}

function showProductPreview(files, previewGrid) {
    previewGrid.innerHTML = "";

    const fileList = Array.from(files);
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/webp"];

    if (fileList.length === 0) {
        previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
        return;
    }

    if (fileList.length > 5) {
        alert("Maksimal 5 foto produk.");
        previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
        return;
    }

    for (const file of fileList) {
        if (!allowedTypes.includes(file.type)) {
            alert("Format foto harus JPG, JPEG, PNG, atau WEBP.");
            previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
            return;
        }

        if (file.size > 2 * 1024 * 1024) {
            alert("Ukuran setiap foto maksimal 2MB.");
            previewGrid.innerHTML = '<div class="preview-empty">Preview foto akan muncul di sini.</div>';
            return;
        }
    }

    fileList.forEach(function (file, index) {
        const reader = new FileReader();

        reader.onload = function (event) {
            const item = document.createElement("div");
            item.className = "preview-item";

            item.innerHTML = `
                <img src="${event.target.result}" alt="Preview Foto ${index + 1}">
                <span>Foto ${index + 1}</span>
            `;

            previewGrid.appendChild(item);
        };

        reader.readAsDataURL(file);
    });
}