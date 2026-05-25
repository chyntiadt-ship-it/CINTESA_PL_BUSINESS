<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register to CINTESA</title>

    <link rel="stylesheet" href="../assets/css/register.css">
</head>
<body>

<button class="theme-toggle" id="themeToggle">
    <span id="themeIcon">☾</span>
</button>

<main class="register-page">

    <section class="register-container">

        <h1>Register to <span class="brand">CINTESA</span></h1>

        <form method="POST" class="register-form">

            <div class="input-group">
                <span class="icon-left">👤</span>
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="input-group">
                <span class="icon-left">🪪</span>
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required>
            </div>

            <div class="input-group">
                <span class="icon-left">📧</span>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="input-group">
                <span class="icon-left">🔒</span>
                <input type="password" name="password" id="password" placeholder="Password" required>
                <span class="eye-icon" id="togglePassword">👁</span>
            </div>
            <div class="input-group">
                <span class="icon-left">🔒</span>
                <input type="password" name="konfirmasi_password" id="confirmPassword" placeholder="Konfirmasi Password" required>
                <span class="eye-icon" id="toggleConfirmPassword">👁</span>
            </div>
            <div class="input-group phone-group">
                <span class="icon-left">📱</span>
                <span class="phone-prefix">+62</span>
                <input
                    type="tel"
                    name="nomor_telepon"
                    id="phone"
                    maxlength="13"
                    required
                >
            </div>

            <div class="input-group">
                <span class="icon-left">🧾</span>

                <select name="role" required>
                    <option value="">Pilih Role</option>
                    <option value="penjual">Penjual</option>
                    <option value="pembeli">Pembeli</option>
                </select>
            </div>

            <button type="submit" class="register-btn">
                Register
            </button>

        </form>

        <p class="login-text">
            Sudah punya akun?
            <a href="login.php">Login disini</a>
        </p>

    </section>

</main>

<script src="../assets/js/main.js"></script>
<script src="../assets/js/register.js"></script>

</body>
</html>