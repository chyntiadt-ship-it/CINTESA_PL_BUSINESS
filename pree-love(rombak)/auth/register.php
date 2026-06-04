<?php
session_start();
include '../include/koneksi.php';

$error = "";

if (isset($_POST['register'])) {
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $email = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $password = mysqli_real_escape_string($koneksi, trim($_POST['password']));
    $role = mysqli_real_escape_string($koneksi, trim($_POST['role']));

    if ($nama_lengkap == "" || $username == "" || $email == "" || $password == "" || $role == "") {
        $error = "Semua data wajib diisi.";
    } elseif (!in_array($role, ['pembeli', 'penjual'])) {
        $error = "Role tidak valid.";
    } else {
        $cek_username = mysqli_query($koneksi, "
            SELECT * FROM user 
            WHERE username='$username' 
            LIMIT 1
        ");

        $cek_email = mysqli_query($koneksi, "
            SELECT * FROM user 
            WHERE email='$email' 
            LIMIT 1
        ");

        if ($cek_username && mysqli_num_rows($cek_username) > 0) {
            $error = "Username sudah digunakan.";
        } elseif ($cek_email && mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah digunakan.";
        } else {
            $insert = mysqli_query($koneksi, "
                INSERT INTO user (nama_lengkap, username, email, password, role)
                VALUES ('$nama_lengkap', '$username', '$email', '$password', '$role')
            ");

            if ($insert) {
                header("Location: login.php?pesan=register_berhasil");
                exit;
            } else {
                $error = "Registrasi gagal: " . mysqli_error($koneksi);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/auth.css?v=4">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="auth-page">

<main class="auth-container">
    <section class="auth-card">
        <h1>Daftar <span>CINTESA</span></h1>

        <?php if ($error != "") { ?>
            <div class="alert error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php } ?>

        <form method="POST" class="auth-form">
            <div class="input-group">
                <input 
                    type="text" 
                    name="nama_lengkap" 
                    placeholder="Nama Lengkap" 
                    required
                >
            </div>

            <div class="input-group">
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username" 
                    required
                >
            </div>

            <div class="input-group">
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email" 
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

            <div class="input-group select-group">
                <select name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="pembeli">Pembeli</option>
                    <option value="penjual">Penjual</option>
                </select>

                <img 
                    src="../assets/icons/down.png" 
                    class="select-arrow-icon" 
                    alt=""
                >
            </div>

            <button type="submit" name="register" class="main-button full">
                Daftar
            </button>
        </form>

        <p class="auth-switch">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>
    </section>
</main>

<script src="../assets/js/global.js?v=3"></script>
</body>
</html>