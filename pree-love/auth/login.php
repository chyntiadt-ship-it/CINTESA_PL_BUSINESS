<?php
session_start();
include '../include/koneksi.php';

if (isset($_POST['login'])) {
    $email_username = mysqli_real_escape_string($koneksi, $_POST['email_username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM user 
        WHERE email='$email_username' OR username='$email_username'
        LIMIT 1
    ");

    if (mysqli_num_rows($query) > 0) {
        $user = mysqli_fetch_assoc($query);

        if ($password == $user['password']) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
                exit;
            } elseif ($user['role'] == 'penjual') {
                header("Location: ../penjual/dashboard.php");
                exit;
            } elseif ($user['role'] == 'pembeli') {
                header("Location: ../pembeli/dashboard.php");
                exit;
            }
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
}
if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: ../admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == 'penjual') {
        header("Location: ../penjual/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] == 'pembeli') {
        header("Location: ../pembeli/dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to CINTESA</title>
    <link rel="stylesheet" href="../assets/css/login.css?v=2">
</head>
<body>

    <button class="theme-toggle" id="themeToggle" aria-label="Ganti tema">
        <span id="themeIcon">☾</span>
    </button>

    <?php if (isset($error)) : ?>
        <div class="popup-error">Email atau password yang salah</div>
    <?php endif; ?>

    <main class="login-page">
        <section class="login-container">
            <h1>Login to <span class="brand">CINTESA</span></h1>

            <form method="POST" class="login-form">
                <div class="input-group">
                   <img src="../assets/icon/user.png" class="input-icon icon-left" alt="">
                    <input type="text" name="email_username" placeholder="Email atau Username" required>
                </div>

                <div class="input-group">
                    <img src="../assets/icon/locked-computer.png" class="input-icon icon-left" alt="Password">
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <img src="../assets/icon/view.png" class="eye-icon" id="togglePassword" alt="">
                </div>

                <button type="submit" name="login" class="login-btn">Login</button>
            </form>

            <p class="register-text">
                Belum punya akun? <a href="register.php">Daftar disini</a>
            </p>
        </section>
    </main>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/login.js?v=10"></script>

</body>
</html>
