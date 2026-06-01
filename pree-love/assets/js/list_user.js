document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('themeToggle');

    if (toggle) {
        toggle.addEventListener('click', function () {
            document.body.classList.toggle('dark');

            toggle.textContent = document.body.classList.contains('dark')
                ? '☀'
                : '☾';
        });
    }

    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const menuToggle = document.getElementById('menuToggle');
    const closeSidebar = document.getElementById('closeSidebar');

    if (menuToggle && sidebar && overlay) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.add('active');
            overlay.classList.add('active');
        });
    }

    if (closeSidebar && sidebar && overlay) {
        closeSidebar.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    if (overlay && sidebar) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }

    document.querySelectorAll('.dropdown-btn').forEach(function (button) {
        button.addEventListener('click', function () {
            button.classList.toggle('active');

            if (button.nextElementSibling) {
                button.nextElementSibling.classList.toggle('show');
            }
        });
    });

    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');

    let searchTimer;

    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function () {
            clearTimeout(searchTimer);

            searchTimer = setTimeout(function () {
                searchForm.submit();
            }, 500);
        });
    }
});