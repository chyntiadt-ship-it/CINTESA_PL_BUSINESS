<?php
session_start();

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
    <title>Login - Pree Love</title>
</head>
<body>

    <h2>Login Pree Love</h2>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "register_berhasil") {
            echo "<p style='color:green;'>Register berhasil! Silakan login.</p>";
        } elseif ($_GET['pesan'] == "gagal") {
            echo "<p style='color:red;'>Email/username atau password salah!</p>";
        } elseif ($_GET['pesan'] == "nonaktif") {
            echo "<p style='color:red;'>Akun kamu sedang dinonaktifkan.</p>";
        } elseif ($_GET['pesan'] == "belum_login") {
            echo "<p style='color:red;'>Silakan login terlebih dahulu.</p>";
        } elseif ($_GET['pesan'] == "logout") {
            echo "<p style='color:green;'>Berhasil logout.</p>";
        }
    }
    ?>

    <form action="proses_login.php" method="POST">
        <label>Email atau Username</label><br>
        <input type="text" name="email_username" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit" name="login">Login</button>
    </form>

    <p>Belum punya akun? <a href="register.php">Register di sini</a></p>

</body>
</html>