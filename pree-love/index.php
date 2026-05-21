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
    <title>Pree Love - Platform Jual Beli Fashion</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="landing-container">
        <div class="landing-box">
            <h1>PREE LOVE</h1>
            <p class="tagline">Tempat jual fashion jadi lebih mudah.</p>

            <p class="description">
                Pree Love adalah platform jual beli fashion berbasis web yang membantu penjual
                memasarkan produk fashion preloved dan membantu pembeli menemukan produk favorit
                dengan lebih mudah.
            </p>

            <div class="button-group">
                <a href="auth/login.php" class="btn btn-login">Login</a>
                <a href="auth/register.php" class="btn btn-register">Register</a>
            </div>
        </div>
    </div>

</body>
</html>