<?php
session_start();
include '../include/koneksi.php';

if (isset($_POST['login'])) {
    $email_username = mysqli_real_escape_string($koneksi, $_POST['email_username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM user 
        WHERE email='$email_username' OR username='$email_username'
    ");

    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);

        if ($data['status'] == 'nonaktif') {
            header("Location: login.php?pesan=nonaktif");
            exit;
        }

        if ($password == $data['password']) {
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
                header("Location: login.php?pesan=gagal");
                exit;
            }
        } else {
            header("Location: login.php?pesan=gagal");
            exit;
        }
    } else {
        header("Location: login.php?pesan=gagal");
        exit;
    }
} else {
    header("Location: login.php");
    exit;
}
?>