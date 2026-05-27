<?php
function penjual_page_start($title, $active = 'dashboard', $search_value = '') {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($title); ?> - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/dashboard_penjual.css?v=2">
</head>
<body>

<header class="top-navbar">
    <button class="menu-toggle" id="menuToggle" type="button">☰</button>

    <a href="dashboard.php" class="nav-logo">CINTESA</a>

    <form action="produk.php" method="GET" class="search-box">
        <span>⌕</span>
        <input 
            type="text" 
            name="keyword" 
            placeholder="Cari produk saya..."
            value="<?php echo htmlspecialchars($search_value); ?>"
        >
    </form>

    <div class="nav-actions">
        <a href="tambah_produk.php" class="nav-btn primary">＋ Produk</a>
        <a href="pesan.php" class="nav-btn">▣</a>
    </div>
</header>

<aside class="sidebar" id="sideNavbar">
    <div class="sidebar-head">
        <button id="closeSidebar" class="close-btn" type="button">×</button>
        <h2>CINTESA</h2>
    </div>

    <nav>
        <a href="dashboard.php" class="<?php echo $active == 'dashboard' ? 'active' : ''; ?>">
            ⌂ Dashboard
        </a>

        <a href="profile.php" class="<?php echo $active == 'profile' ? 'active' : ''; ?>">
            ◉ Profil
        </a>

        <a href="produk.php" class="<?php echo $active == 'produk' ? 'active' : ''; ?>">
            ▦ Manajemen Produk
        </a>

        <a href="customer_service.php" class="<?php echo $active == 'cs' ? 'active' : ''; ?>">
            ? Customer Service
        </a>

        <a href="../auth/logout.php">
            ⎋ Logout
        </a>
    </nav>
</aside>

<main class="seller-main">
<?php
}

function penjual_page_end() {
?>
</main>

<script src="../assets/js/dashboard_penjual.js?v=2"></script>
</body>
</html>
<?php
}
?>