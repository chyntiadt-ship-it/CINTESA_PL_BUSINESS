document.addEventListener("DOMContentLoaded", function () {
    const shareBtn = document.getElementById("shareProductBtn");
    const shareModal = document.getElementById("shareModal");
    const closeShareModal = document.getElementById("closeShareModal");
    const productLinkInput = document.getElementById("productLinkInput");
    const copyProductLink = document.getElementById("copyProductLink");

    if (!shareBtn || !shareModal) return;

    if (productLinkInput) {
        productLinkInput.value = window.location.href;
    }

    shareBtn.addEventListener("click", function () {
        shareModal.classList.add("active");
    });

    closeShareModal?.addEventListener("click", function () {
        shareModal.classList.remove("active");
    });

    shareModal.addEventListener("click", function (e) {
        if (e.target === shareModal) {
            shareModal.classList.remove("active");
        }
    });

    copyProductLink?.addEventListener("click", function () {
        productLinkInput.select();
        document.execCommand("copy");
        copyProductLink.textContent = "✓";

        setTimeout(() => {
            copyProductLink.textContent = "⧉";
        }, 1200);
    });
});
