<?php
include '../include/koneksi.php';

if (isset($_POST['register'])) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $nomor_telepon = mysqli_real_escape_string($koneksi, $_POST['nomor_telepon']);
    $role = mysqli_real_escape_string($koneksi, $_POST['role']);

    // Role yang boleh daftar hanya penjual dan pembeli
    if ($role != "penjual" && $role != "pembeli") {
        header("Location: register.php?pesan=role_tidak_valid");
        exit;
    }

    // Cek username sudah dipakai atau belum
    $cek_username = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username'");
    if (mysqli_num_rows($cek_username) > 0) {
        header("Location: register.php?pesan=username_ada");
        exit;
    }

    // Cek email sudah dipakai atau belum
    $cek_email = mysqli_query($koneksi, "SELECT * FROM user WHERE email='$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        header("Location: register.php?pesan=email_ada");
        exit;
    }

    // Simpan data ke tabel user
    $query = mysqli_query($koneksi, "INSERT INTO user 
        (foto_profile, username, nama_lengkap, email, password, nomor_telepon, role, status)
        VALUES 
        ('', '$username', '$nama_lengkap', '$email', '$password', '$nomor_telepon', '$role', 'aktif')
    ");

    if ($query) {
        header("Location: login.php?pesan=register_berhasil");
        exit;
    } else {
        header("Location: register.php?pesan=gagal");
        exit;
    }
} else {
    header("Location: register.php");
    exit;
}
?>