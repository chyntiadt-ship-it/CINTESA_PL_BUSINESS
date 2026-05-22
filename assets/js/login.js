const togglePassword = document.getElementById("togglePassword");
const passwordInput = document.getElementById("password");

togglePassword.addEventListener("click", function () {
    const isPassword = passwordInput.type === "password";

    passwordInput.type = isPassword ? "text" : "password";
    togglePassword.textContent = isPassword ? "🙈" : "👁";
});

const popup = document.querySelector(".popup-error");

if (popup) {
    setTimeout(() => {
        popup.style.display = "none";
    }, 3000);
}