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

    <?php
    $current_page = basename($_SERVER['PHP_SELF']);
    ?>
    
    <div class="nav-actions">
        <?php if ($current_page != 'produk.php') { ?>
            <a href="tambah_produk.php" class="nav-btn primary">
                <img src="../assets/icons/penjual/add-product.png" class="nav-icon" alt="">
            </a>
        <?php } ?>
            <a href="pesan.php" class="nav-btn">
                <img src="../assets/icons/chat.png" class="nav-icon" alt="">
            </a>
    </div>
</header>

<aside class="sidebar" id="sideNavbar">
    <div class="sidebar-head">
        <button id="closeSidebar" class="close-btn" type="button">×</button>
        <h2>CINTESA</h2>
    </div>

    <nav>
        <a href="dashboard.php" class="<?php echo $active == 'dashboard' ? 'active' : ''; ?>">
            <img src="../assets/icons/home.png" class="side-icon" alt="">
            <span>Dashboard</span>
        </a>
        
        <a href="profile.php" class="<?php echo $active == 'profile' ? 'active' : ''; ?>">
            <img src="../assets/icons/user.png" class="side-icon" alt="">
            <span>Profil</span>
        </a>
        
        <a href="produk.php" class="<?php echo $active == 'produk' ? 'active' : ''; ?>">
            <img src="../assets/icons/penjual/product-management.png" class="side-icon" alt="">
            <span>Manajemen Produk</span>
        </a>
        
        <a href="customer_service.php" class="<?php echo $active == 'cs' ? 'active' : ''; ?>">
            <img src="../assets/icons/call-center.png" class="side-icon" alt="">
            <span>Customer Service</span>
        </a>
        
        <a href="../auth/logout.php">
            <img src="../assets/icons/logout.png" class="side-icon" alt="">
            <span>Logout</span>
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
