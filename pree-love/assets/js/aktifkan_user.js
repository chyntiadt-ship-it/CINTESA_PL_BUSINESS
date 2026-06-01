
document.addEventListener("DOMContentLoaded", function () {
    const savedTheme = localStorage.getItem("cintesa_theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark");
    }

    if (savedTheme === "light") {
        document.body.classList.remove("dark");
    }
});