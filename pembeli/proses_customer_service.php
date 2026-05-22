<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'pembeli') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['kirim'])) {
    $id_user = $_SESSION['id_user'];
    $jenis_pesan = mysqli_real_escape_string($koneksi, $_POST['jenis_pesan']);
    $isi_pesan = mysqli_real_escape_string($koneksi, $_POST['isi_pesan']);

    if (empty($jenis_pesan) || empty($isi_pesan)) {
        header("Location: customer_service.php?pesan=kosong");
        exit;
    }

    $jenis_valid = ['Keluhan', 'Saran', 'Kritik', 'Laporan'];

    if (!in_array($jenis_pesan, $jenis_valid)) {
        header("Location: customer_service.php?pesan=kosong");
        exit;
    }

    $query = mysqli_query($koneksi, "INSERT INTO customer_service 
        (id_user, jenis_pesan, isi_pesan, status_pesan)
        VALUES
        ('$id_user', '$jenis_pesan', '$isi_pesan', 'Belum Dibaca')
    ");

    if ($query) {
        header("Location: customer_service.php?pesan=berhasil");
        exit;
    } else {
        header("Location: customer_service.php?pesan=gagal");
        exit;
    }
} else {
    header("Location: customer_service.php");
    exit;
}
?>