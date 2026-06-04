<?php
session_start();

if (isset($_SESSION['id_user'])) {
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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>CINTESA - PL Business</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="assets/css/base.css?v=4">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="landing-page">

<header class="simple-navbar">
    <a href="index.php" class="brand-logo">CINTESA</a>

    <div class="navbar-actions">
        <a href="#anggota" class="nav-button">Anggota</a>
        <a href="auth/login.php" class="nav-button">Login</a>
        <a href="auth/register.php" class="nav-button dark">Daftar</a>
    </div>
</header>

<main class="landing-hero">
    <section class="landing-content">
        <h1>Welcome to <span>CINTESA</span></h1>
        <p>
            Platform sederhana untuk menjual dan membeli produk preloved.
        </p>

        <div class="landing-actions">
            <a href="auth/login.php" class="main-button">Mulai Sekarang</a>
        </div>
    </section>
</main>

<section class="anggota-section" id="anggota">
    <div class="section-heading">
        <h2>Anggota Kelompok</h2>
        <p>Informasi anggota kelompok pengembang website CINTESA.</p>
    </div>

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

</body>
</html>