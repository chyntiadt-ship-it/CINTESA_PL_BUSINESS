<?php
session_start();
include '../include/koneksi.php';

$error = "";

// Jika sudah login, langsung arahkan sesuai role
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

// Proses login saat tombol login diklik
if (isset($_POST['login'])) {
    $email_username = mysqli_real_escape_string($koneksi, $_POST['email_username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM user 
        WHERE email = '$email_username' 
        OR username = '$email_username'
        LIMIT 1
    ");

    if (!$query) {
        $error = "Query error: " . mysqli_error($koneksi);
    } elseif (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        // Karena password di database kamu masih teks biasa, contoh: admin123
        if ($password == $data['password']) {

            if ($data['status'] != 'aktif') {
                $error = "Akun belum aktif atau sedang dinonaktifkan.";
            } else {
                $_SESSION['id_user'] = $data['id_user'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
                $_SESSION['role'] = $data['role'];

                if ($data['role'] == 'admin') {
                    header("Location: ../admin/dashboard.php");
                    exit;
                } elseif ($data['role'] == 'penjual') {
                    header("Location: ../penjual/dashboard.php");
                    exit;
                } elseif ($data['role'] == 'pembeli') {
                    header("Location: ../pembeli/dashboard.php");
                    exit;
                } else {
                    $error = "Role tidak valid.";
                }
            }

        } else {
            $error = "Password salah.";
        }

    } else {
        $error = "Email atau username tidak ditemukan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login to CINTESA</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

    <button class="theme-toggle" id="themeToggle" aria-label="Ganti tema">
        <span id="themeIcon">☾</span>
    </button>

    <?php if (!empty($error)) : ?>
        <div class="popup-error">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <main class="login-page">
        <section class="login-container">
            <h1>Login to <span class="brand">CINTESA</span></h1>

            <form method="POST" class="login-form">
                <div class="input-group">
                    <span class="icon-left">👤</span>
                    <input type="text" name="email_username" placeholder="Email atau Username" required>
                </div>

                <div class="input-group">
                    <span class="icon-left">🔒</span>
                    <input type="password" name="password" id="password" placeholder="Password" required>
                    <span class="eye-icon" id="togglePassword">👁</span>
                </div>

                <button type="submit" name="login" class="login-btn">Login</button>
            </form>

            <p class="register-text">
                Belum punya akun? <a href="register.php">Daftar disini</a>
            </p>
        </section>
    </main>

    <script src="../assets/js/main.js"></script>
    <script src="../assets/js/login.js"></script>

</body>
</html>