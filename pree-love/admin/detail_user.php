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

$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'");

if (mysqli_num_rows($query) == 0) {
    echo "User tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($query);

// Admin utama tidak boleh dinonaktifkan dari halaman ini
if ($data['role'] == 'admin') {
    echo "Data admin tidak bisa dikelola dari halaman ini.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail User - Pree Love</title>
</head>
<body>

    <h2>Detail User</h2>

    <a href="manajemen_user.php">Kembali ke Manajemen User</a>

    <hr>

    <?php if (!empty($data['foto_profile'])) { ?>
        <img src="../uploads/profile/<?php echo $data['foto_profile']; ?>" width="120">
    <?php } else { ?>
        <p>Belum ada foto profile</p>
    <?php } ?>

    <p><strong>Username:</strong> <?php echo $data['username']; ?></p>
    <p><strong>Nama Lengkap:</strong> <?php echo $data['nama_lengkap']; ?></p>
    <p><strong>Email:</strong> <?php echo $data['email']; ?></p>
    <p><strong>Nomor Telepon:</strong> <?php echo $data['nomor_telepon']; ?></p>
    <p><strong>Role:</strong> <?php echo $data['role']; ?></p>
    <p><strong>Status:</strong> <?php echo $data['status']; ?></p>
    <p><strong>Tanggal Bergabung:</strong> <?php echo $data['tanggal_bergabung']; ?></p>

    <hr>

    <?php if ($data['status'] == 'aktif') { ?>
        <a href="nonaktifkan_user.php?id=<?php echo $data['id_user']; ?>" onclick="return confirm('Yakin ingin menonaktifkan user ini?')">
            Nonaktifkan User
        </a>
    <?php } else { ?>
        <a href="aktifkan_user.php?id=<?php echo $data['id_user']; ?>" onclick="return confirm('Yakin ingin mengaktifkan kembali user ini?')">
            Aktifkan User
        </a>
    <?php } ?>

</body>
</html>