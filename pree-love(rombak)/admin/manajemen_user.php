<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$role_filter = isset($_GET['role']) ? mysqli_real_escape_string($koneksi, trim($_GET['role'])) : "";
$keyword = isset($_GET['keyword']) ? mysqli_real_escape_string($koneksi, trim($_GET['keyword'])) : "";

$where = "WHERE role IN ('penjual', 'pembeli')";

if ($role_filter == 'penjual' || $role_filter == 'pembeli') {
    $where .= " AND role='$role_filter'";
}

if ($keyword != "") {
    $where .= " AND (
        nama_lengkap LIKE '%$keyword%' OR
        username LIKE '%$keyword%' OR
        email LIKE '%$keyword%'
    )";
}

$query_user = mysqli_query($koneksi, "
    SELECT * FROM user
    $where
    ORDER BY id_user DESC
");

if (isset($_GET['status']) && isset($_GET['id'])) {
    $id_user = (int) $_GET['id'];
    $status = $_GET['status'] == 'nonaktif' ? 'nonaktif' : 'aktif';

    mysqli_query($koneksi, "
        UPDATE user SET status_akun='$status'
        WHERE id_user='$id_user'
        AND role IN ('penjual', 'pembeli')
    ");

    header("Location: manajemen_user.php?pesan=status_berhasil");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User - CINTESA</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/base.css?v=1">
    <link rel="stylesheet" href="../assets/css/admin.css?v=2">
    <link rel="stylesheet" href="../assets/css/theme.css?v=1">
</head>
<body class="admin-page">

<header class="role-navbar">
    <a href="dashboard.php" class="icon-button back-button" title="Kembali">
        <img 
            src="../assets/icons/penjual/back-arrow.png" 
            class="back-icon" 
            alt="Kembali"
            >
    </a>

    <a href="dashboard.php" class="brand-logo">CINTESA</a>

    <a href="../auth/logout.php" class="icon-button" title="Logout">
        <img src="../assets/icons/logout.png" alt="Logout">
    </a>
</header>

<main class="role-main">
    <section class="page-heading center">
        <h1>Manajemen User</h1>
        <p>Kelola data pembeli dan penjual di CINTESA.</p>
    </section>

    <section class="table-card">
        <div class="table-header">
            <div>
                <h2>Data User</h2>
                <p>Pilih role atau cari user berdasarkan nama, username, atau email.</p>
            </div>

            <form method="GET" class="filter-form">
                <select name="role">
                    <option value="">Semua User</option>
                    <option value="penjual" <?php echo $role_filter == 'penjual' ? 'selected' : ''; ?>>Penjual</option>
                    <option value="pembeli" <?php echo $role_filter == 'pembeli' ? 'selected' : ''; ?>>Pembeli</option>
                </select>

                <input 
                    type="text" 
                    name="keyword" 
                    placeholder="Cari user..." 
                    value="<?php echo htmlspecialchars($keyword); ?>"
                >

                <button type="submit">Cari</button>
            </form>
        </div>

        <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'status_berhasil') { ?>
            <div class="alert success">Status user berhasil diperbarui.</div>
        <?php } ?>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($query_user && mysqli_num_rows($query_user) > 0) { ?>
                        <?php $no = 1; while ($user = mysqli_fetch_assoc($query_user)) { 
                            $status_akun = $user['status_akun'] ?? 'aktif';
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><?php echo htmlspecialchars($user['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['no_telp'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($user['role']); ?></td>
                                <td><?php echo htmlspecialchars($status_akun); ?></td>
                                <td>
                                    <?php if ($status_akun == 'aktif') { ?>
                                        <a 
                                            href="manajemen_user.php?status=nonaktif&id=<?php echo $user['id_user']; ?>" 
                                            class="btn-danger"
                                        >
                                            Nonaktifkan
                                        </a>
                                    <?php } else { ?>
                                        <a 
                                            href="manajemen_user.php?status=aktif&id=<?php echo $user['id_user']; ?>" 
                                            class="btn-primary-small"
                                        >
                                            Aktifkan
                                        </a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="8" class="empty-row">Tidak ada data user.</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </section>
</main>

</body>
</html>