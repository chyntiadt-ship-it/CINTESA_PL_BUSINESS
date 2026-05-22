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

$query = mysqli_query($koneksi, "SELECT * FROM user 
    WHERE role='penjual' 
    ORDER BY tanggal_bergabung DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>List Penjual - Pree Love</title>
</head>
<body>

    <h2>List Penjual</h2>

    <a href="manajemen_user.php">Kembali ke Manajemen User</a>

    <hr>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Foto</th>
            <th>Username</th>
            <th>Nama Lengkap</th>
            <th>Email</th>
            <th>No Telepon</th>
            <th>Status</th>
            <th>Tanggal Bergabung</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>

            <td>
                <?php if (!empty($data['foto_profile'])) { ?>
                    <img src="../uploads/profile/<?php echo $data['foto_profile']; ?>" width="60">
                <?php } else { ?>
                    Tidak ada foto
                <?php } ?>
            </td>

            <td><?php echo $data['username']; ?></td>
            <td><?php echo $data['nama_lengkap']; ?></td>
            <td><?php echo $data['email']; ?></td>
            <td><?php echo $data['nomor_telepon']; ?></td>
            <td><?php echo $data['status']; ?></td>
            <td><?php echo $data['tanggal_bergabung']; ?></td>
            <td>
                <a href="detail_user.php?id=<?php echo $data['id_user']; ?>">Detail</a>
            </td>
        </tr>

        <?php
            }
        } else {
        ?>

        <tr>
            <td colspan="9">Belum ada data penjual.</td>
        </tr>

        <?php } ?>

    </table>

</body>
</html>