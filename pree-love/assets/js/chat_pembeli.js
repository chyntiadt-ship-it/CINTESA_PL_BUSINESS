const chatBody = document.getElementById("chatBody");

if (chatBody) {
    chatBody.scrollTop = chatBody.scrollHeight;
}

const quickButtons = document.querySelectorAll(".quick-replies button");
const textarea = document.querySelector(".chat-input-bar textarea");

quickButtons.forEach(function (button) {
    button.addEventListener("click", function () {
        if (textarea) {
            textarea.value = button.textContent.trim();
            textarea.focus();
        }
    });
});

const attachToggle = document.getElementById("attachToggle");
const attachMenu = document.getElementById("attachMenu");

const moreToggle = document.getElementById("moreToggle");
const moreMenu = document.getElementById("moreMenu");

if (attachToggle && attachMenu) {
    attachToggle.addEventListener("click", function (event) {
        event.stopPropagation();
        attachMenu.classList.toggle("active");

        if (moreMenu) {
            moreMenu.classList.remove("active");
        }
    });
}

if (moreToggle && moreMenu) {
    moreToggle.addEventListener("click", function (event) {
        event.stopPropagation();
        moreMenu.classList.toggle("active");

        if (attachMenu) {
            attachMenu.classList.remove("active");
        }
    });
}

document.addEventListener("click", function () {
    if (attachMenu) {
        attachMenu.classList.remove("active");
    }

    if (moreMenu) {
        moreMenu.classList.remove("active");
    }
});
const chooseGallery = document.getElementById("chooseGallery");
const openCamera = document.getElementById("openCamera");
const galleryInput = document.getElementById("galleryInput");
const cameraInput = document.getElementById("cameraInput");

if (chooseGallery && galleryInput) {
    chooseGallery.addEventListener("click", function (event) {
        event.stopPropagation();
        galleryInput.click();
        attachMenu?.classList.remove("active");
    });
}

if (openCamera && cameraInput) {
    openCamera.addEventListener("click", function (event) {
        event.stopPropagation();
        cameraInput.click();
        attachMenu?.classList.remove("active");
    });
}
