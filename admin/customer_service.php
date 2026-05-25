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

    header("Location: customer_service.php?pesan=dibaca_berhasil");
    exit;
}

$query = mysqli_query($koneksi, "SELECT customer_service.*, user.username, user.nama_lengkap, user.role
    FROM customer_service
    JOIN user ON customer_service.id_user = user.id_user
    ORDER BY customer_service.tanggal_pesan DESC
");

$total_pesan = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM customer_service");
$data_total_pesan = mysqli_fetch_assoc($total_pesan);

$total_belum_dibaca = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM customer_service WHERE status_pesan='Belum Dibaca'");
$data_belum_dibaca = mysqli_fetch_assoc($total_belum_dibaca);

$total_sudah_dibaca = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM customer_service WHERE status_pesan='Sudah Dibaca'");
$data_sudah_dibaca = mysqli_fetch_assoc($total_sudah_dibaca);

$total_saran = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM customer_service WHERE jenis_pesan='Saran'");
$data_saran = mysqli_fetch_assoc($total_saran);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service Admin - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/admin.css?v=13">
</head>
<body>

<div class="admin-wrapper">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-circle"></div>
            <h2>CINTESA</h2>
        </div>

        <nav class="sidebar-menu">
            <a href="dashboard.php">Dashboard</a>
            <a href="manajemen_user.php">Manajemen User</a>
            <a href="manajemen_postingan.php">Manajemen Postingan</a>
            <a href="customer_service.php" class="active">Customer Service</a>
            <a href="profile.php">Profile Admin</a>
        </nav>

        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </aside>

    <main class="main-content">

        <section class="topbar">
            <div>
                <h1>Customer Service</h1>
                <p>
                    Pantau pesan, saran, keluhan, dan pertanyaan dari pengguna CINTESA.
                    Admin dapat menandai pesan sebagai sudah dibaca.
                </p>
            </div>

            <div class="admin-badge">
                Service Center
            </div>
        </section>

        <section class="stats-grid">
            <div class="stat-card">
                <h3>Total Pesan</h3>
                <p><?php echo $data_total_pesan['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Belum Dibaca</h3>
                <p><?php echo $data_belum_dibaca['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Sudah Dibaca</h3>
                <p><?php echo $data_sudah_dibaca['total']; ?></p>
            </div>

            <div class="stat-card">
                <h3>Total Saran</h3>
                <p><?php echo $data_saran['total']; ?></p>
            </div>
        </section>

        <section class="section-card">

            <div class="section-header">
                <div>
                    <h2>Daftar Pesan Customer Service</h2>
                    <p>Kelola pesan yang dikirim oleh pembeli maupun penjual.</p>
                </div>

                <a href="dashboard.php" class="small-btn">Kembali ke Dashboard</a>
            </div>

            <?php
            if (isset($_GET['pesan'])) {
                if ($_GET['pesan'] == "dibaca_berhasil") {
                    echo "<div class='alert success'>Pesan berhasil ditandai sudah dibaca.</div>";
                }
            }
            ?>

            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
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
                    </thead>

                    <tbody>
                        <?php
                        $no = 1;

                        if (mysqli_num_rows($query) > 0) {
                            while ($data = mysqli_fetch_assoc($query)) {
                                $status_class = $data['status_pesan'] == 'Belum Dibaca' ? 'status-dihapus' : 'status-tersedia';
                        ?>

                        <tr>
                            <td><?php echo $no++; ?></td>

                            <td>
                                <strong><?php echo htmlspecialchars($data['nama_lengkap']); ?></strong>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($data['username']); ?>
                            </td>

                            <td>
                                <span class="status-badge status-terjual">
                                    <?php echo htmlspecialchars(ucfirst($data['role'])); ?>
                                </span>
                            </td>

                            <td>
                                <strong><?php echo htmlspecialchars($data['jenis_pesan']); ?></strong>
                            </td>

                            <td class="message-cell">
                                <?php echo htmlspecialchars($data['isi_pesan']); ?>
                            </td>

                            <td>
                                <span class="status-badge <?php echo $status_class; ?>">
                                    <?php echo htmlspecialchars($data['status_pesan']); ?>
                                </span>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($data['tanggal_pesan']); ?>
                            </td>

                            <td>
                                <?php if ($data['status_pesan'] == 'Belum Dibaca') { ?>
                                    <a 
                                        href="customer_service.php?dibaca=<?php echo $data['id_cs']; ?>" 
                                        class="btn-action"
                                        onclick="return confirm('Tandai pesan ini sebagai sudah dibaca?')"
                                    >
                                        Tandai Dibaca
                                    </a>
                                <?php } else { ?>
                                    <span class="done-text">Sudah Dibaca</span>
                                <?php } ?>
                            </td>
                        </tr>

                        <?php
                            }
                        } else {
                        ?>

                        <tr>
                            <td colspan="9" class="empty-cell">
                                <div class="empty-state">
                                    <div class="empty-icon">✉</div>
                                    <h3>Belum ada pesan customer service</h3>
                                    <p>Pesan, saran, atau keluhan dari pengguna akan tampil di tabel ini.</p>
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