<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: manajemen_user.php");
    exit;
}

$id_user = mysqli_real_escape_string($koneksi, $_GET['id']);

// Pastikan yang dinonaktifkan bukan admin
$cek = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'");

if (mysqli_num_rows($cek) == 0) {
    echo "User tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($cek);

if ($data['role'] == 'admin') {
    echo "Admin tidak boleh dinonaktifkan.";
    exit;
}

mysqli_query($koneksi, "UPDATE user SET status='nonaktif' WHERE id_user='$id_user'");

header("Location: detail_user.php?id=$id_user");
exit;
?>