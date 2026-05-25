const chatBody = document.getElementById("chatBody");

if (chatBody) {
    chatBody.scrollTop = chatBody.scrollHeight;
}

const quickButtons = document.querySelectorAll(".quick-replies button");
const textarea = document.querySelector(".chat-input-bar textarea");

quickButtons.forEach(function(button) {
    button.addEventListener("click", function() {
        if (textarea) {
            textarea.value = button.textContent;
            textarea.focus();
        }
    });
});