<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$nama_admin = $_SESSION['nama_lengkap'] ?? 'Admin';

$total_produk = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM produk");
$data_produk = mysqli_fetch_assoc($total_produk);

$total_user = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user");
$data_user = mysqli_fetch_assoc($total_user);

$total_penjual = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='penjual'");
$data_penjual = mysqli_fetch_assoc($total_penjual);

$total_pembeli = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='pembeli'");
$data_pembeli = mysqli_fetch_assoc($total_pembeli);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<nav class="top-navbar">
    <div class="nav-left">
        <button class="menu-btn" id="menuToggle">☰</button>
        <h2>CINTESA</h2>
    </div>

    <div class="nav-right"></div>
</nav>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>CINTESA</h2>
        <button id="closeSidebar">✕</button>
    </div>

    <div class="sidebar-menu">
        <a href="dashboard.php" class="active">Dashboard</a>
        <a href="profile.php">Profile</a>

        <button class="dropdown-btn">
            Manajemen Postingan
            <span>▾</span>
        </button>
        <div class="dropdown-menu">
            <a href="manajemen_postingan.php">Semua Postingan</a>
        </div>

        <a href="customer_service.php">Customer Service</a>

        <button class="dropdown-btn">
            Manajemen User
            <span>▾</span>
        </button>
        <div class="dropdown-menu">
            <a href="manajemen_user.php">Semua User</a>
        </div>

        <a href="../auth/logout.php" class="logout-sidebar">Logout</a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-container">
    <div class="admin-header">
        <h1>Dashboard <span class="brand">CINTESA</span></h1>
        <p>
            Selamat datang,
            <strong><?php echo htmlspecialchars($nama_admin); ?></strong>
        </p>
    </div>

    <div class="summary-grid">
        <div class="summary-card">
            <h3>Total Produk</h3>
            <p><?php echo $data_produk['total']; ?></p>
        </div>

        <div class="summary-card">
            <h3>Total User</h3>
            <p><?php echo $data_user['total']; ?></p>
        </div>

        <div class="summary-card">
            <h3>Total Penjual</h3>
            <p><?php echo $data_penjual['total']; ?></p>
        </div>

        <div class="summary-card">
            <h3>Total Pembeli</h3>
            <p><?php echo $data_pembeli['total']; ?></p>
        </div>
    </div>

    <div class="menu-card">
        <h2>Menu Admin</h2>

        <div class="admin-menu">
            <a href="profile.php">Profile</a>
            <a href="manajemen_postingan.php">Manajemen Postingan</a>
            <a href="customer_service.php">Customer Service</a>
            <a href="manajemen_user.php">Manajemen User</a>
        </div>
    </div>
</div>

<footer class="footer-cintesa">
    <p>CINTESA PL BUSINESS - 2026</p>
</footer>

<script>
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const menuToggle = document.getElementById('menuToggle');
const closeSidebar = document.getElementById('closeSidebar');

menuToggle.addEventListener('click', () => {
    sidebar.classList.add('active');
    overlay.classList.add('active');
});

closeSidebar.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});

overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});

document.querySelectorAll('.dropdown-btn').forEach(button => {
    button.addEventListener('click', () => {
        button.classList.toggle('active');

        if (button.nextElementSibling) {
            button.nextElementSibling.classList.toggle('show');
        }
    });
});
</script>

</body>
</html>