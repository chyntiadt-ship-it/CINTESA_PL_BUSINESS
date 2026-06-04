<?php
session_start();
include '../include/koneksi.php';

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

$error = "";

if (isset($_POST['login'])) {
    $email_username = mysqli_real_escape_string($koneksi, trim($_POST['email_username']));
    $password = mysqli_real_escape_string($koneksi, trim($_POST['password']));

    $query = mysqli_query($koneksi, "
        SELECT * FROM user 
        WHERE email='$email_username' OR username='$email_username'
        LIMIT 1
    ");

    if ($query && mysqli_num_rows($query) > 0) {
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
            } else {
                header("Location: ../pembeli/dashboard.php");
                exit;
            }
        } else {
            $error = "Email/username atau password salah.";
        }
    } else {
        $error = "Email/username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/auth.css?v=2">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="auth-page">

<main class="auth-container">
    <section class="auth-card">
        <h1>Login to <span>CINTESA</span></h1>

        <?php if ($error != "") { ?>
            <div class="alert error"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <form method="POST" class="auth-form">
            <div class="input-group">
                <input 
                    type="text" 
                    name="email_username" 
                    placeholder="Email atau Username" 
                    required
                >
            </div>

            <div class="input-group">
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    placeholder="Password" 
                    required
                >

                <button 
                    type="button" 
                    class="password-toggle" 
                    id="togglePassword"
                    data-eye="../assets/icons/eye.png"
                    data-view="../assets/icons/view.png"
                    aria-label="Tampilkan password"
                >
                    <img 
                        src="..assets/icons/eye.png"
                        id="passwordIcon"
                        alt="Toggle password"
                    >
                </button>
            </div>

            <button type="submit" name="login" class="main-button full">
                Login
            </button>
        </form>

        <p class="auth-switch">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>
    </section>
</main>

<script src="../assets/js/global.js?v=3"></script>
</body>
</html>