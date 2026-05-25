<button class="admin-menu-toggle" id="adminMenuToggle" type="button">
    ☰
</button>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<aside class="sidebar" id="adminSidebar">

    <div class="sidebar-logo">

        <button class="sidebar-close" id="sidebarClose" type="button">
            ×
        </button>

        <h2>CINTESA</h2>
    </div>

    <nav class="sidebar-menu">

        <a href="dashboard.php"
           class="<?php echo $activePage == 'dashboard' ? 'active' : ''; ?>">
            Dashboard
        </a>

        <a href="manajemen_user.php"
           class="<?php echo $activePage == 'manajemen_user' ? 'active' : ''; ?>">
            Manajemen User
        </a>

        <a href="manajemen_postingan.php"
           class="<?php echo $activePage == 'manajemen_postingan' ? 'active' : ''; ?>">
            Manajemen Postingan
        </a>

        <a href="customer_service.php"
           class="<?php echo $activePage == 'customer_service' ? 'active' : ''; ?>">
            Customer Service
        </a>

        <a href="profile.php"
           class="<?php echo $activePage == 'profile' ? 'active' : ''; ?>">
            Profile Admin
        </a>

    </nav>

    <div class="sidebar-footer">
        <a href="../auth/logout.php" class="logout-btn">
            Logout
        </a>
    </div>

</aside>