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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Penjual - CINTESA</title>

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
        <input type="text" placeholder="Cari penjual...">
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

        <button class="dropdown-btn active">
            Manajemen Postingan
            <span>▾</span>
        </button>

        <div class="dropdown-menu show">
            <a href="manajemen_postingan.php">Semua Postingan</a>
        </div>

        <a href="customer_service.php">Customer Service</a>

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
        <h1>List <span class="brand">Penjual</span></h1>
        <p>Kelola dan lihat semua data akun penjual</p>
    </div>

    <div class="table-card">
        <div class="table-title">
            <h2>Data Penjual</h2>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
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
                    ?>

                    <tr>
                        <td><?php echo $no++; ?></td>

                        <td>
                            <?php if (!empty($data['foto_profile'])) { ?>
                                <img class="table-img" src="../uploads/profile/<?php echo htmlspecialchars($data['foto_profile']); ?>">
                            <?php } else { ?>
                                <span class="empty-text">Tidak ada foto</span>
                            <?php } ?>
                        </td>

                        <td><?php echo htmlspecialchars($data['username']); ?></td>
                        <td><?php echo htmlspecialchars($data['nama_lengkap']); ?></td>
                        <td><?php echo htmlspecialchars($data['email']); ?></td>
                        <td><?php echo htmlspecialchars($data['nomor_telepon']); ?></td>

                        <td>
                            <span class="status-badge">
                                <?php echo htmlspecialchars($data['status']); ?>
                            </span>
                        </td>

                        <td><?php echo htmlspecialchars($data['tanggal_bergabung']); ?></td>

                        <td>
                            <a class="btn-detail" href="detail_user.php?id=<?php echo $data['id_user']; ?>">
                                Detail
                            </a>
                        </td>
                    </tr>

                    <?php
                        }
                    } else {
                    ?>

                    <tr>
                        <td colspan="9" class="empty-data">Belum ada data penjual.</td>
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