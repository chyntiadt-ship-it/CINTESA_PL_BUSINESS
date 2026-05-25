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

$activePage = 'manajemen_user';

$query = mysqli_query($koneksi, "SELECT * FROM user 
    WHERE role='pembeli' 
    ORDER BY tanggal_bergabung DESC
");

$total_pembeli = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='pembeli'");
$data_total_pembeli = mysqli_fetch_assoc($total_pembeli);

$total_aktif = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='pembeli' AND status='aktif'");
$data_aktif = mysqli_fetch_assoc($total_aktif);

$total_nonaktif = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM user WHERE role='pembeli' AND status='nonaktif'");
$data_nonaktif = mysqli_fetch_assoc($total_nonaktif);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pembeli - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/admin.css?v=30">
</head>
<body>

<div class="admin-wrapper">

    <?php include 'layouts/sidebar.php'; ?>

    <main class="main-content">

        <section class="topbar">
            <div>
                <h1>List Pembeli</h1>
                <p>
                    Lihat data akun pembeli yang terdaftar di CINTESA.
                    Admin dapat membuka detail user untuk melihat informasi lebih lengkap.
                </p>
            </div>

            <div class="admin-badge">
                Buyer List
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <h3>Total Pembeli</h3>
                <p><?php echo $data_total_pembeli['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Pembeli Aktif</h3>
                <p><?php echo $data_aktif['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Pembeli Nonaktif</h3>
                <p><?php echo $data_nonaktif['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Jenis User</h3>
                <p>Pembeli</p>
            </div>
        </section>

        <section class="section-card">

            <div class="section-header">
                <div>
                    <h2>Daftar Pembeli</h2>
                    <p>Pantau akun pembeli, status akun, dan informasi kontak yang terdaftar.</p>
                </div>

                <a href="manajemen_user.php" class="small-btn">Kembali ke Manajemen User</a>
            </div>

            <div class="table-wrapper">
                <table class="admin-table user-list-table">
                    <thead>
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
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;

                        if (mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                                $status_class = strtolower($data['status']) == 'aktif' ? 'status-tersedia' : 'status-dihapus';
                        ?>

                        <tr>
                            <td><?php echo $no++; ?></td>

                            <td>
                                <?php if (!empty($data['foto_profile'])) { ?>
                                    <img 
                                        src="../uploads/profile/<?php echo htmlspecialchars($data['foto_profile']); ?>" 
                                        class="table-img user-table-img"
                                        alt="Foto User"
                                    >
                                <?php } else { ?>
                                    <div class="user-avatar-mini">
                                        <?php echo strtoupper(substr($data['nama_lengkap'], 0, 1)); ?>
                                    </div>
                                <?php } ?>
                            </td>

                            <td>
                                <strong><?php echo htmlspecialchars($data['username']); ?></strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($data['nama_lengkap']); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($data['email']); ?>
                            </td>

                            <td>
                                <?php echo !empty($data['nomor_telepon']) ? htmlspecialchars($data['nomor_telepon']) : '-'; ?>
                            </td>

                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($data['status']); ?>
                                </span>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($data['tanggal_bergabung']); ?>
                            </td>

                            <td>
                                <a href="detail_user.php?id=<?php echo $data['id_user']; ?>" class="btn-action">
                                    Detail
                                </a>
                            </td>
                        </tr>

                        <?php
                            }
                        } else {
                        ?>

                        <tr>
                            <td colspan="9" class="empty-cell">
                                <div class="empty-state">
                                    <div class="empty-icon">□</div>
                                    <h3>Belum ada data pembeli</h3>
                                    <p>Data pembeli yang terdaftar akan tampil di tabel ini.</p>
                                </div>
                            </td>
                        </tr>

                        <?php } ?>
                    </tbody>
                </table>
            </div>

        </section>

    </main>

</div>

</body>
</html>