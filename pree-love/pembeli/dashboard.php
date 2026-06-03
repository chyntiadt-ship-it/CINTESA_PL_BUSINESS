<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'pembeli') {
    header("Location: ../auth/login.php");
    exit;
}

$username = $_SESSION['username'] ?? 'Pembeli';

$produk = mysqli_query($koneksi, "
    SELECT produk.*, kategori.nama_kategori, user.username
    FROM produk
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
    JOIN user ON produk.id_user = user.id_user
    WHERE produk.status_produk = 'Tersedia'
    ORDER BY produk.tanggal_upload DESC
    LIMIT 8
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pembeli - CINTESA</title>
    <link rel="stylesheet" href="../assets/css/dashboard_pembeli.css?v=20">
</head>
<body>

<header class="top-navbar">

    <button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu">
        ☰
    </button>

    <a href="dashboard.php" class="nav-logo">
        CINTESA
    </a>

    <div class="dashboard-search-wrap">
    <form action="cari_produk.php" method="GET" class="desktop-search" id="dashboardSearchForm" autocomplete="off">
        <span class="search-icon">⌕</span>

        <input
            type="text"
            name="keyword"
            id="dashboardSearchInput"
            placeholder="Cari produk, kategori, atau toko..."
            autocomplete="off"
        >

        <div class="search-suggestion-box" id="searchSuggestionBox"></div>
    </form>
</div>
</header>

<aside class="side-navbar" id="sideNavbar">

    <div class="sidebar-header">
        <button class="close-sidebar" id="closeSidebar" type="button" aria-label="Tutup menu">
            ×
        </button>

        <h2>CINTESA</h2>
    </div>

    <nav class="side-menu">
        <a href="dashboard.php" class="side-item active">
            <img src="../assets/icons/home.png" class="side-icon" alt="Beranda">
            Beranda
        </a>

        <a href="profile.php" class="side-item">
             <img src="../assets/icons/user.png" class="side-icon" alt="">Profil
        </a>

        <a href="chat.php" class="side-item">
            <img src="../assets/icons/chat.png" class="side-icon" alt=""> Chat
        </a>

        <a href="customer_service.php" class="side-item">
             <img src="../assets/icons/call-center.png" class="side-icon" alt=""> Customer Service
        </a>

        <a href="../auth/logout.php" class="side-item logout">
             <img src="../assets/icons/logout.png" class="side-icon" alt="">
        Logout
        </a>
    </nav>

</aside>

<main class="dashboard-main">

    <section class="hero-dashboard">
        <h1>
            Halo, <?php echo htmlspecialchars($username); ?>
        </h1>

        <p>
            Temukan produk terbaik dan kelola aktivitas belanjamu di CINTESA.
        </p>
    </section>

    <section class="fyp-section">
        <h2>Produk Untuk Kamu</h2>

        <div class="product-grid">

            <?php if (mysqli_num_rows($produk) > 0) { ?>

                <?php while ($row = mysqli_fetch_assoc($produk)) { 
                    $id_produk = $row['id_produk'];

                    $foto_query = mysqli_query($koneksi, "
                        SELECT * FROM produk_foto 
                        WHERE id_produk='$id_produk' 
                        LIMIT 1
                    ");

                    $foto = mysqli_fetch_assoc($foto_query);
                ?>

                    <div class="product-card">

                        <?php if (!empty($foto['foto'])) { ?>
                            <img
                                src="../uploads/produk/<?php echo htmlspecialchars($foto['foto']); ?>"
                                class="product-img"
                                alt="<?php echo htmlspecialchars($row['nama_produk']); ?>"
                            >
                        <?php } else { ?>
                            <div class="no-img">
                                Tidak ada foto
                            </div>
                        <?php } ?>

                        <div class="product-body">
                            <span class="category">
                                <?php echo htmlspecialchars($row['nama_kategori']); ?>
                            </span>

                            <h3>
                                <?php echo htmlspecialchars($row['nama_produk']); ?>
                            </h3>

                            <p class="price">
                                Rp<?php echo number_format($row['harga'], 0, ',', '.'); ?>
                            </p>

                            <p class="seller">
                                👤 <?php echo htmlspecialchars($row['username']); ?>
                            </p>

                            <a href="detail_produk.php?id=<?php echo $row['id_produk']; ?>" class="detail-btn">
                                Lihat Detail
                            </a>
                        </div>

                    </div>

                <?php } ?>

            <?php } else { ?>

                <div class="empty-box">
                    Belum ada produk tersedia.
                </div>

            <?php } ?>

        </div>
    </section>

</main>

<script src="../assets/js/dashboard_pembeli.js?v=99"></script>
</body>
</html>
