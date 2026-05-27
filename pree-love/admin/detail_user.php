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

$query = mysqli_query($koneksi, "
    SELECT * FROM user 
    WHERE id_user='$id_user'
");

if (mysqli_num_rows($query) == 0) {
    echo "User tidak ditemukan.";
    exit;
}

$data = mysqli_fetch_assoc($query);

if ($data['role'] == 'admin') {
    echo "Data admin tidak bisa dikelola.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail User - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/detail_user.css">
</head>
<body>

<nav class="top-navbar">

    <div class="nav-left">
        <button class="menu-btn" id="menuToggle">☰</button>
        <h2>CINTESA</h2>
    </div>

    <div class="nav-center">
        <input type="text" placeholder="Cari user...">
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

        <a href="customer_service.php">Customer Service</a>

        <button class="dropdown-btn active">
            Manajemen User
            <span>▾</span>
        </button>

        <div class="dropdown-menu show">
            <a href="manajemen_user.php">Semua User</a>
        </div>

        <a href="../auth/logout.php" class="logout-sidebar">Logout</a>

    </div>

</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-container">

    <div class="admin-header">
        <h1>Detail <span class="brand">User</span></h1>
        <p>Informasi lengkap akun pengguna</p>
    </div>

    <div class="detail-card">

        <div class="profile-top">

            <div class="profile-photo">

                <?php if (!empty($data['foto_profile'])) { ?>

                    <img src="../uploads/profile/<?php echo htmlspecialchars($data['foto_profile']); ?>">

                <?php } else { ?>

                    <div class="empty-photo">
                        Tidak Ada Foto
                    </div>

                <?php } ?>

            </div>

            <div class="profile-info">
                <h2><?php echo htmlspecialchars($data['nama_lengkap']); ?></h2>
                <span>@<?php echo htmlspecialchars($data['username']); ?></span>
            </div>

        </div>

        <div class="detail-grid">

            <div class="detail-item">
                <label>Email</label>
                <p><?php echo htmlspecialchars($data['email']); ?></p>
            </div>

            <div class="detail-item">
                <label>Nomor Telepon</label>
                <p><?php echo htmlspecialchars($data['nomor_telepon']); ?></p>
            </div>

            <div class="detail-item">
                <label>Role</label>
                <p><?php echo htmlspecialchars($data['role']); ?></p>
            </div>

            <div class="detail-item">
                <label>Status</label>
                <p><?php echo htmlspecialchars($data['status']); ?></p>
            </div>

            <div class="detail-item">
                <label>Tanggal Bergabung</label>
                <p><?php echo htmlspecialchars($data['tanggal_bergabung']); ?></p>
            </div>

        </div>

        <div class="detail-action">

            <a href="<?php echo ($data['role'] == 'penjual') ? 'list_penjual.php' : 'list_pembeli.php'; ?>" class="back-action-btn">
                Kembali
            </a>

            <?php if ($data['status'] == 'aktif') { ?>

                <a href="nonaktifkan_user.php?id=<?php echo $data['id_user']; ?>" class="danger-btn">
                    Nonaktifkan User
                </a>

            <?php } else { ?>

                <a href="aktifkan_user.php?id=<?php echo $data['id_user']; ?>" class="success-btn">
                    Aktifkan User
                </a>

            <?php } ?>

        </div>

    </div>

</div>

<script>

const toggle = document.getElementById('themeToggle');

toggle.addEventListener('click', () => {
    document.body.classList.toggle('dark');
    toggle.textContent =
    document.body.classList.contains('dark')
    ? '☀'
    : '☾';
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