document.addEventListener("DOMContentLoaded", function () {
    const themeToggle = document.getElementById("themeToggle");
    const themeIcon = document.getElementById("themeIcon");

    const menuToggle = document.getElementById("menuToggle");
    const sideNavbar = document.getElementById("sideNavbar");
    const closeSidebar = document.getElementById("closeSidebar");
    const sidebarOverlay = document.getElementById("sidebarOverlay");

    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        document.body.classList.add("dark");

        if (themeIcon) {
            themeIcon.textContent = "☀";
        }
    }

    if (themeToggle) {
        themeToggle.addEventListener("click", function () {
            document.body.classList.toggle("dark");

            if (document.body.classList.contains("dark")) {
                localStorage.setItem("theme", "dark");

                if (themeIcon) {
                    themeIcon.textContent = "☀";
                }
            } else {
                localStorage.setItem("theme", "light");

                if (themeIcon) {
                    themeIcon.textContent = "☾";
                }
            }
        });
    }

    function openSidebar() {
        if (sideNavbar) {
            sideNavbar.classList.add("active");
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.add("active");
        }
    }

    function closeSidebarMenu() {
        if (sideNavbar) {
            sideNavbar.classList.remove("active");
        }

        if (sidebarOverlay) {
            sidebarOverlay.classList.remove("active");
        }
    }

    if (menuToggle) {
        menuToggle.addEventListener("click", openSidebar);
    }

    if (closeSidebar) {
        closeSidebar.addEventListener("click", closeSidebarMenu);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener("click", closeSidebarMenu);
    }

    document.querySelectorAll(".side-item").forEach(function (item) {
        item.addEventListener("click", function () {
            closeSidebarMenu();
        });
    });
});