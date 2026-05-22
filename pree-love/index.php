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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CINTESA - PL Business</title>
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

    <button class="theme-toggle" id="themeToggle" aria-label="Ganti tema">
        <span id="themeIcon">☾</span>
    </button>

    <main class="hero">
        <div class="hero-content">
            <h1 class="title">CINTESA</h1>
            <h2 class="subtitle">PL Business</h2>

            <p class="welcome">Welcome to CINTESA</p>

            <p class="description">
                CINTESA menjadi tempat bagi pengguna untuk menjual serta membeli produk dengan mudah.
            </p>

            <div class="action-box">
                <a href="auth/login.php" class="option-card">Login</a>
                <a href="auth/register.php" class="option-card">Register</a>
            </div>
        </div>
    </main>

    <script src="assets/js/index.js"></script>
</body>
</html>