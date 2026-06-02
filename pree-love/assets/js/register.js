function setupPasswordToggle(inputId, iconId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(iconId);

    if (!input || !icon) return;

    icon.src = "../assets/icons/eye.png";

    icon.addEventListener("click", function () {
        if (input.type === "password") {
            input.type = "text";
            icon.src = "../assets/icons/view.png";
        } else {
            input.type = "password";
            icon.src = "../assets/icons/eye.png";
        }
    });
}

setupPasswordToggle("password", "togglePassword");
setupPasswordToggle("confirmPassword", "toggleConfirmPassword");
