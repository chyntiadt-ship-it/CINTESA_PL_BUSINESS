const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("password");

if (togglePassword && password) {
    // kondisi awal: password tersembunyi, maka ikon mata tertutup
    togglePassword.src = "../assets/icons/eye.png";

    togglePassword.addEventListener("click", function () {
        if (password.type === "password") {
            password.type = "text";
            togglePassword.src = "../assets/icons/view.png";
        } else {
            password.type = "password";
            togglePassword.src = "../assets/icons/eye.png";
        }
    });
}
