<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['balas'])) {
    $id_pengirim = $_SESSION['id_user'];
    $id_chat = mysqli_real_escape_string($koneksi, $_POST['id_chat']);
    $isi_pesan = mysqli_real_escape_string($koneksi, $_POST['isi_pesan']);

    if (empty($isi_pesan)) {
        header("Location: detail_chat.php?id_chat=$id_chat");
        exit;
    }

    // Pastikan chat ini milik penjual yang sedang login
    $cek_chat = mysqli_query($koneksi, "SELECT * FROM chat 
        WHERE id_chat='$id_chat' 
        AND id_penjual='$id_pengirim'
    ");

    if (mysqli_num_rows($cek_chat) == 0) {
        echo "Chat tidak valid.";
        exit;
    }

    mysqli_query($koneksi, "INSERT INTO pesan 
        (id_chat, id_pengirim, isi_pesan)
        VALUES
        ('$id_chat', '$id_pengirim', '$isi_pesan')
    ");

    header("Location: detail_chat.php?id_chat=$id_chat");
    exit;
} else {
    header("Location: pesan.php");
    exit;
}
?>