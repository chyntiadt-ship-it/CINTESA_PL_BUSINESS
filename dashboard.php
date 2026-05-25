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

// Jika admin klik tandai sudah dibaca
if (isset($_GET['dibaca'])) {
    $id_cs = mysqli_real_escape_string($koneksi, $_GET['dibaca']);

    mysqli_query($koneksi, "UPDATE customer_service 
        SET status_pesan='Sudah Dibaca' 
        WHERE id_cs='$id_cs'
    ");

    header("Location: customer_service.php");
    exit;
}

$query = mysqli_query($koneksi, "SELECT customer_service.*, user.username, user.nama_lengkap, user.role
    FROM customer_service
    JOIN user ON customer_service.id_user = user.id_user
    ORDER BY customer_service.tanggal_pesan DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Customer Service Admin - Pree Love</title>
</head>
<body>

    <h2>Customer Service Admin</h2>

    <a href="dashboard.php">Kembali ke Dashboard</a>

    <hr>

    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>No</th>
            <th>Nama User</th>
            <th>Username</th>
            <th>Role</th>
            <th>Jenis Pesan</th>
            <th>Isi Pesan</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>

        <?php
        $no = 1;

        if (mysqli_num_rows($query) > 0) {
            while ($data = mysqli_fetch_assoc($query)) {
        ?>

        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $data['nama_lengkap']; ?></td>
            <td><?php echo $data['username']; ?></td>
            <td><?php echo $data['role']; ?></td>
            <td><?php echo $data['jenis_pesan']; ?></td>
            <td><?php echo $data['isi_pesan']; ?></td>
            <td><?php echo $data['status_pesan']; ?></td>
            <td><?php echo $data['tanggal_pesan']; ?></td>
            <td>
                <?php if ($data['status_pesan'] == 'Belum Dibaca') { ?>
                    <a href="customer_service.php?dibaca=<?php echo $data['id_cs']; ?>">Tandai Sudah Dibaca</a>
                <?php } else { ?>
                    Sudah Dibaca
                <?php } ?>
            </td>
        </tr>

        <?php
            }
        } else {
        ?>

        <tr>
            <td colspan="9">Belum ada pesan customer service.</td>
        </tr>

        <?php } ?>

    </table>

</body>
</html>