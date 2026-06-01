<?php
session_start();

/*if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == 'penjual') {
        header("Location: penjual/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == 'pembeli') {
        header("Location: pembeli/dashboard.php");
        exit;
    }
}
    */
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINTESA - PL Business</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<header class="top-navbar">
    <button class="menu-toggle" id="menuToggle">☰</button>

    <a href="index.php" class="nav-logo">CINTESA</a>

    <form class="desktop-search" autocomplete="off">
        <span class="search-icon">⌕</span>
        <input type="text" placeholder="Cari produk, kategori, atau toko...">
    </form>

    <div class="nav-actions">
        <a href="#" class="nav-icon" aria-label="Notifikasi">♧</a>
        <a href="auth/login.php" class="nav-link">Login</a>
    </div>
</header>

<aside class="side-navbar" id="sideNavbar">
    <div class="sidebar-header">
        <button class="close-sidebar" id="closeSidebar">×</button>
        <h2>CINTESA</h2>
    </div>

    <nav class="side-menu">
        <a href="index.php" class="side-item active">
            <span>⌂</span> Beranda
        </a>

        <a href="#anggota" class="side-item">
            <span>◈</span> Anggota
        </a>

        <a href="#" class="side-item">
            <span>◉</span> Profil
        </a>

        <a href="#" class="side-item">
            <span>▣</span> Chat
        </a>

        <a href="#" class="side-item">
            <span>＋</span> Jual
        </a>
    </nav>
</aside>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<main class="hero">
    <section class="hero-content">
        <h1 class="welcome">Welcome to CINTESA</h1>
        <h2 class="subtitle">PL Business</h2>
        <p class="description">
            CINTESA menjadi tempat bagi pengguna untuk menjual serta membeli produk dengan mudah.
        </p>
    </section>
</main>

<section class="anggota-section" id="anggota">

    <div class="anggota-grid">

        <article class="anggota-card">
            <div class="anggota-photo">
                <img src="assets/img/anggota/anggota1.jpg" alt="Foto Anggota 1">
            </div>

            <div class="anggota-info">
                <h3>SANCIA ALICIA TALIGANGSING</h3>
                <p>NIM: 240211060025</p>
            </div>
        </article>

        <article class="anggota-card">
            <div class="anggota-photo">
                <img src="assets/img/anggota/anggota2.jpg" alt="Foto Anggota 2">
            </div>

            <div class="anggota-info">
                <h3>YEDIDA ABIGAIL SABARU</h3>
                <p>NIM: 240211060026</p>
            </div>
        </article>

        <article class="anggota-card">
            <div class="anggota-photo">
                <img src="assets/img/anggota/anggota3.jpg" alt="Foto Anggota 3">
            </div>

            <div class="anggota-info">
                <h3>CHYNTIA DEWI TOMASOA</h3>
                <p>NIM: 240211060027</p>
            </div>
        </article>

        <article class="anggota-card">
            <div class="anggota-photo">
                <img src="assets/img/anggota/anggota4.jpg" alt="Foto Anggota 4">
            </div>

            <div class="anggota-info">
                <h3>GRELIYANI BERMULA</h3>
                <p>NIM: 240211060087</p>
            </div>
        </article>

    </div>

</section>

<footer class="footer-cintesa">
    <p>CINTESA PL BUSINESS - 2026</p>
</footer>

<script src="assets/js/index.js"></script>
</body>
</html>