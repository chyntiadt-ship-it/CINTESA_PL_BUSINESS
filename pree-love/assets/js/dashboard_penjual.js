const menuToggle = document.getElementById("menuToggle");
const sideNavbar = document.getElementById("sideNavbar");
const closeSidebar = document.getElementById("closeSidebar");

if (menuToggle && sideNavbar) {
    menuToggle.addEventListener("click", () => {
        sideNavbar.classList.add("active");
    });
}

if (closeSidebar && sideNavbar) {
    closeSidebar.addEventListener("click", () => {
        sideNavbar.classList.remove("active");
    });
}

document.addEventListener("click", function (e) {
    if (!sideNavbar || !menuToggle) return;

    if (!sideNavbar.contains(e.target) && !menuToggle.contains(e.target)) {
        sideNavbar.classList.remove("active");
    }
});

const chatBody = document.getElementById("chatBody");
if (chatBody) {
    chatBody.scrollTop = chatBody.scrollHeight;
}

document.querySelectorAll("[data-quick-reply]").forEach(button => {
    button.addEventListener("click", () => {
        const textarea = document.getElementById("replyTextarea");
        if (textarea) {
            textarea.value = button.dataset.quickReply;
            textarea.focus();
        }
    });
});