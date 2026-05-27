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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/table_admin.css">
</head>
<body>

<nav class="top-navbar">
    <div class="nav-left">
        <button class="menu-btn" id="menuToggle">☰</button>
        <h2>CINTESA</h2>
    </div>

    <div class="nav-center">
        <input type="text" placeholder="Cari pesan customer service...">
    </div>

    <div class="nav-right">
        <button class="theme-toggle" id="themeToggle">☾</button>
    </div>
</nav>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h2>CINTESA</h2>
        <button id="closeSidebar">✕</button>
    </div>

    <div class="sidebar-menu">
        <a href="dashboard.php">Dashboard</a>
        <a href="profile.php">Profile</a>

        <button class="dropdown-btn">
            Manajemen Postingan
            <span>▾</span>
        </button>
        <div class="dropdown-menu">
            <a href="manajemen_postingan.php">Semua Postingan</a>
        </div>

        <a href="customer_service.php" class="active">Customer Service</a>

        <button class="dropdown-btn">
            Manajemen User
            <span>▾</span>
        </button>
        <div class="dropdown-menu">
            <a href="manajemen_user.php">Semua User</a>
        </div>

        <a href="../auth/logout.php" class="logout-sidebar">Logout</a>
    </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-container">
    <div class="admin-header">
        <h1>Customer <span class="brand">Service</span></h1>
        <p>Kelola pesan, saran, dan laporan dari pengguna</p>
    </div>

    <div class="table-card">
        <div class="table-title">
            <h2>Data Pesan Customer Service</h2>
        </div>

        <div class="table-responsive">
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
                    ?>

                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($data['username']); ?></td>
                        <td><?php echo htmlspecialchars($data['role']); ?></td>
                        <td><?php echo htmlspecialchars($data['jenis_pesan']); ?></td>
                        <td><?php echo htmlspecialchars($data['isi_pesan']); ?></td>

                        <td>
                            <span class="status-badge">
                                <?php echo htmlspecialchars($data['status_pesan']); ?>
                            </span>
                        </td>

                        <td><?php echo htmlspecialchars($data['tanggal_pesan']); ?></td>

                        <td>
                            <?php if ($data['status_pesan'] == 'Belum Dibaca') { ?>
                                <a class="btn-detail" href="customer_service.php?dibaca=<?php echo $data['id_cs']; ?>">
                                    Tandai Dibaca
                                </a>
                            <?php } else { ?>
                                <span class="empty-text">Sudah Dibaca</span>
                            <?php } ?>
                        </td>
                    </tr>

                    <?php
                        }
                    } else {
                    ?>

                    <tr>
                        <td colspan="9" class="empty-data">
                            Belum ada pesan customer service.
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const toggle = document.getElementById('themeToggle');

toggle.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    toggle.textContent = document.body.classList.contains('dark') ? '☀' : '☾';
});

const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('sidebarOverlay');
const menuToggle = document.getElementById('menuToggle');
const closeSidebar = document.getElementById('closeSidebar');

menuToggle.addEventListener('click', () => {
    sidebar.classList.add('active');
    overlay.classList.add('active');
});

closeSidebar.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});

overlay.addEventListener('click', () => {
    sidebar.classList.remove('active');
    overlay.classList.remove('active');
});

document.querySelectorAll('.dropdown-btn').forEach(button => {
    button.addEventListener('click', () => {
        button.classList.toggle('active');
        button.nextElementSibling.classList.toggle('show');
    });
});
</script>

</body>
</html>