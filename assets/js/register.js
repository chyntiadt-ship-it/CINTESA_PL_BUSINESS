const passwordInput = document.getElementById("password");
const confirmPasswordInput = document.getElementById("confirmPassword");
const togglePassword = document.getElementById("togglePassword");
const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");

togglePassword.addEventListener("click", function () {
    const isPassword = passwordInput.type === "password";
    passwordInput.type = isPassword ? "text" : "password";
    togglePassword.textContent = isPassword ? "🙈" : "👁";
});

toggleConfirmPassword.addEventListener("click", function () {
    const isPassword = confirmPasswordInput.type === "password";
    confirmPasswordInput.type = isPassword ? "text" : "password";
    toggleConfirmPassword.textContent = isPassword ? "🙈" : "👁";
});

const registerForm = document.querySelector(".register-form");
const phoneInput = document.getElementById("phone");

phoneInput.addEventListener("input", function () {
    this.value = this.value.replace(/\D/g, "");

    if (this.value.length > 13) {
        this.value = this.value.slice(0, 13);
        alert("Nomor telepon maksimal 15 digit");
    }
});

registerForm.addEventListener("submit", function (e) {
    const phone = phoneInput.value.trim();

    if (phone.length > 13) {
        e.preventDefault();
        alert("Nomor telepon maksimal 15 digit");
        phoneInput.focus();
        return;
    }

    if (phone.length < 9) {
        e.preventDefault();
        alert("Nomor telepon tidak valid");
        phoneInput.focus();
        return;
    }

    if (passwordInput.value !== confirmPasswordInput.value) {
        e.preventDefault();
        alert("Konfirmasi password tidak sesuai");
        confirmPasswordInput.focus();
        return;
    }

    phoneInput.value = "+62" + phone;
});