<?php
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - Pree Love</title>
</head>
<body>

    <h2>Register Akun Pree Love</h2>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "username_ada") {
            echo "<p style='color:red;'>Username sudah digunakan!</p>";
        } elseif ($_GET['pesan'] == "email_ada") {
            echo "<p style='color:red;'>Email sudah digunakan!</p>";
        } elseif ($_GET['pesan'] == "gagal") {
            echo "<p style='color:red;'>Register gagal, coba lagi!</p>";
        } elseif ($_GET['pesan'] == "role_tidak_valid") {
            echo "<p style='color:red;'>Role tidak valid!</p>";
        }
    }
    ?>

    <form action="proses_register.php" method="POST">
        <label>Username</label><br>
        <input type="text" name="username" required><br><br>

        <label>Nama Lengkap</label><br>
        <input type="text" name="nama_lengkap" required><br><br>

        <label>Email</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password</label><br>
        <input type="password" name="password" required><br><br>

        <label>Nomor Telepon</label><br>
        <input type="text" name="nomor_telepon" required><br><br>

        <label>Daftar Sebagai</label><br>
        <select name="role" required>
            <option value="">-- Pilih Role --</option>
            <option value="penjual">Penjual</option>
            <option value="pembeli">Pembeli</option>
        </select><br><br>

        <button type="submit" name="register">Register</button>
    </form>

    <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>

</body>
</html>