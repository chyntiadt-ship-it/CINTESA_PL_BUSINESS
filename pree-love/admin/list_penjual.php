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

$keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
$keyword_safe = mysqli_real_escape_string($koneksi, $keyword);

$where_search = "";

if ($keyword_safe != '') {
    $where_search = "AND username LIKE '%$keyword_safe%'";
}

$query = mysqli_query($koneksi, "
    SELECT * FROM user 
    WHERE role='penjual'
    $where_search
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
    <link rel="stylesheet" href="../assets/css/list_user.css">
</head>
<body class="list-user-page">

<nav class="top-navbar">
    <div class="nav-left">
        <button class="menu-btn" id="menuToggle">☰</button>
        <h2>CINTESA</h2>
    </div>

    <div class="nav-center">
        <form method="GET" action="list_penjual.php" class="admin-search-form" id="searchForm">
            <input 
                type="text" 
                name="q"
                id="searchInput"
                placeholder="Cari username penjual..."
                value="<?php echo htmlspecialchars($keyword); ?>"
                autocomplete="off"
            >
        </form>
    </div>

    <div class="nav-right"></div>
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
            <a href="list_penjual.php" class="active">List Penjual</a>
            <a href="list_pembeli.php">List Pembeli</a>
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

    <?php if ($keyword != ''): ?>
        <div class="search-result-info">
            <span>
                Hasil pencarian username:
                <strong><?php echo htmlspecialchars($keyword); ?></strong>
            </span>

            <a href="list_penjual.php">Reset</a>
        </div>
    <?php endif; ?>

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
                                <img 
                                    class="table-img" 
                                    src="../uploads/profile/<?php echo htmlspecialchars($data['foto_profile']); ?>"
                                    alt="Foto <?php echo htmlspecialchars($data['username']); ?>"
                                >
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
                        <td colspan="9" class="empty-data">
                            <?php if ($keyword != '') { ?>
                                Username penjual tidak ditemukan.
                            <?php } else { ?>
                                Belum ada data penjual.
                            <?php } ?>
                        </td>
                    </tr>

                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<footer class="footer-cintesa">
    <p>CINTESA PL BUSINESS - 2026</p>
</footer>

<script src="../assets/js/list_user.js"></script>

</body>
</html>