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

$query = mysqli_query($koneksi, "SELECT produk.*, kategori.nama_kategori, user.username, user.nama_lengkap
    FROM produk
    JOIN kategori ON produk.id_kategori = kategori.id_kategori
    JOIN user ON produk.id_user = user.id_user
    WHERE produk.status_produk != 'Dihapus'
    ORDER BY produk.tanggal_upload DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Postingan - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/table_admin.css">
    <link rel="stylesheet" href="../assets/css/manajemen_postingan.css">
</head>

<body class="manajemen-postingan-page">

<nav class="top-navbar">

    <div class="nav-left">
        <button class="menu-btn" id="menuToggle">☰</button>
        <h2>CINTESA</h2>
    </div>

    <div class="nav-center">
        <input type="text" placeholder="Cari postingan...">
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

        <a href="dashboard.php">
            Dashboard
        </a>

        <a href="profile.php">
            Profile
        </a>

        <!-- Manajemen Postingan -->
        <button class="dropdown-btn active">
            Manajemen Postingan
            <span>▾</span>
        </button>

        <div class="dropdown-menu show">
            <a href="manajemen_postingan.php" class="active">
                Semua Postingan
            </a>
        </div>

        <!-- Customer Service -->
        <a href="customer_service.php">
            Customer Service
        </a>

        <!-- Manajemen User -->
        <button class="dropdown-btn">
            Manajemen User
            <span>▾</span>
        </button>

        <div class="dropdown-menu">
            <a href="manajemen_user.php">
                Semua User
            </a>
        </div>

        <!-- Logout -->
        <a href="../auth/logout.php" class="logout-sidebar">
            Logout
        </a>

    </div>

</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="admin-container">

    <div class="admin-header">
        <h1>Manajemen <span class="brand">Postingan</span></h1>
        <p>Kelola semua postingan produk pengguna</p>
    </div>

    <?php if (isset($_GET['pesan'])) { ?>

        <div class="alert-box">

            <?php
            if ($_GET['pesan'] == "hapus_berhasil") {
                echo "Postingan berhasil dihapus.";
            } elseif ($_GET['pesan'] == "hapus_gagal") {
                echo "Postingan gagal dihapus.";
            }
            ?>

        </div>

    <?php } ?>

    <div class="table-card">

        <div class="table-title">
            <h2>Data Postingan Produk</h2>
        </div>

        <div class="table-responsive">

            <table class="admin-table">

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Foto</th>
                        <th>Nama Produk</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Status</th>
                        <th>Penjual</th>
                        <th>Tanggal Upload</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $no = 1;

                    if (mysqli_num_rows($query) > 0) {

                        while ($produk = mysqli_fetch_assoc($query)) {

                            $id_produk = $produk['id_produk'];

                            $foto_query = mysqli_query($koneksi, "SELECT * FROM produk_foto 
                                WHERE id_produk='$id_produk'
                                LIMIT 1
                            ");

                            $foto = mysqli_fetch_assoc($foto_query);
                    ?>

                    <tr>

                        <td>
                            <?php echo $no++; ?>
                        </td>

                        <td>

                            <?php if (!empty($foto['foto'])) { ?>

                                <img
                                    class="table-img"
                                    src="../uploads/produk/<?php echo htmlspecialchars($foto['foto']); ?>"
                                >

                            <?php } else { ?>

                                <span class="empty-text">
                                    Tidak ada foto
                                </span>

                            <?php } ?>

                        </td>

                        <td>
                            <?php echo htmlspecialchars($produk['nama_produk']); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($produk['nama_kategori']); ?>
                        </td>

                        <td>
                            Rp<?php echo number_format($produk['harga'], 0, ',', '.'); ?>
                        </td>

                        <td>
                            <span class="status-badge">
                                <?php echo htmlspecialchars($produk['status_produk']); ?>
                            </span>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($produk['username']); ?>
                            <br>

                            <small>
                                <?php echo htmlspecialchars($produk['nama_lengkap']); ?>
                            </small>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($produk['tanggal_upload']); ?>
                        </td>

                        <td>

                            <a
                                class="btn-delete"
                                href="hapus_postingan.php?id=<?php echo $produk['id_produk']; ?>"
                                onclick="return confirm('Yakin ingin menghapus postingan ini?')"
                            >
                                Hapus
                            </a>

                        </td>

                    </tr>

                    <?php
                        }
                    } else {
                    ?>

                    <tr>

                        <td colspan="9" class="empty-data">
                            Belum ada postingan produk.
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