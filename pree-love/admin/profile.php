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

$id_user = $_SESSION['id_user'];

$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user='$id_user'");
$data = mysqli_fetch_assoc($query);

if (isset($_POST['update'])) {
    $username_baru = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nomor_telepon = mysqli_real_escape_string($koneksi, $_POST['nomor_telepon']);
    $foto_lama = $data['foto_profile'];

    if ($username_baru != $data['username']) {
        $cek_log = mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM username_log 
            WHERE id_user='$id_user' 
            AND MONTH(tanggal_ubah)=MONTH(CURRENT_DATE()) 
            AND YEAR(tanggal_ubah)=YEAR(CURRENT_DATE())
        ");

        $log = mysqli_fetch_assoc($cek_log);

        if ($log['total'] >= 2) {
            header("Location: profile.php?pesan=username_limit");
            exit;
        }

        $cek_username = mysqli_query($koneksi, "SELECT * FROM user 
            WHERE username='$username_baru' 
            AND id_user != '$id_user'
        ");

        if (mysqli_num_rows($cek_username) > 0) {
            header("Location: profile.php?pesan=username_ada");
            exit;
        }

        mysqli_query($koneksi, "INSERT INTO username_log 
            (id_user, username_lama, username_baru) 
            VALUES 
            ('$id_user', '{$data['username']}', '$username_baru')
        ");
    }

    $foto_profile = $foto_lama;

    if (!empty($_FILES['foto_profile']['name'])) {
        $nama_file = $_FILES['foto_profile']['name'];
        $tmp_file = $_FILES['foto_profile']['tmp_name'];
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        $ekstensi_valid = ['jpg', 'jpeg', 'png'];

        if (!in_array($ekstensi, $ekstensi_valid)) {
            header("Location: profile.php?pesan=foto_invalid");
            exit;
        }

        $nama_baru = 'profile_' . $id_user . '_' . time() . '.' . $ekstensi;
        $folder_upload = '../uploads/profile/' . $nama_baru;

        if (move_uploaded_file($tmp_file, $folder_upload)) {
            $foto_profile = $nama_baru;
        }
    }

    $update = mysqli_query($koneksi, "UPDATE user SET 
        username='$username_baru',
        nomor_telepon='$nomor_telepon',
        foto_profile='$foto_profile'
        WHERE id_user='$id_user'
    ");

    if ($update) {
        $_SESSION['username'] = $username_baru;
        header("Location: profile.php?pesan=berhasil");
        exit;
    } else {
        header("Location: profile.php?pesan=gagal");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Admin - CINTESA</title>

    <link rel="stylesheet" href="../assets/css/login.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="../assets/css/profile_admin.css">
</head>
<body>

<nav class="top-navbar">
    <div class="nav-left">
        <button class="menu-btn" id="menuToggle">☰</button>
        <h2>CINTESA</h2>
    </div>

    <div class="nav-center">
        <input type="text" placeholder="Cari menu admin...">
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
        <a href="profile.php" class="active">Profile</a>

        <button class="dropdown-btn">
            Manajemen Postingan
            <span>▾</span>
        </button>
        <div class="dropdown-menu">
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
        <h1>Profile <span class="brand">Admin</span></h1>
        <p>Kelola informasi akun admin CINTESA</p>
    </div>

    <form action="" method="POST" enctype="multipart/form-data" class="profile-admin-card">

        <div class="profile-photo-side">
            <div class="photo-wrapper">
                <?php if (!empty($data['foto_profile'])) { ?>
                    <img class="admin-photo" src="../uploads/profile/<?php echo htmlspecialchars($data['foto_profile']); ?>">
                <?php } else { ?>
                    <div class="placeholder-photo">
                        <?php echo strtoupper(substr($data['username'], 0, 1)); ?>
                    </div>
                <?php } ?>

                <label for="foto_profile" class="edit-photo-btn">✎</label>
                <input type="file" name="foto_profile" id="foto_profile" class="hidden">
            </div>

            <h2><?php echo htmlspecialchars($data['nama_lengkap']); ?></h2>
            <p>@<?php echo htmlspecialchars($data['username']); ?></p>
        </div>

        <div class="profile-form-side">

            <?php if (isset($_GET['pesan'])) { ?>
                <div class="profile-alert">
                    <?php
                    if ($_GET['pesan'] == "berhasil") {
                        echo "Profile berhasil diperbarui.";
                    } elseif ($_GET['pesan'] == "gagal") {
                        echo "Profile gagal diperbarui.";
                    } elseif ($_GET['pesan'] == "username_limit") {
                        echo "Username hanya bisa diganti maksimal 2x dalam sebulan.";
                    } elseif ($_GET['pesan'] == "username_ada") {
                        echo "Username sudah digunakan user lain.";
                    } elseif ($_GET['pesan'] == "foto_invalid") {
                        echo "Foto harus berformat JPG, JPEG, atau PNG.";
                    }
                    ?>
                </div>
            <?php } ?>

            <div class="edit-note">
                <strong>Informasi Profile</strong>
                <p>Username maksimal diganti 2 kali dalam sebulan. Nama, email, role, dan tanggal bergabung bersifat readonly.</p>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($data['username']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Nomor Telepon</label>
                    <input type="text" name="nomor_telepon" value="<?php echo htmlspecialchars($data['nomor_telepon']); ?>">
                </div>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input class="readonly-input" type="text" value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input class="readonly-input" type="email" value="<?php echo htmlspecialchars($data['email']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Role</label>
                    <input class="readonly-input" type="text" value="<?php echo htmlspecialchars($data['role']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Tanggal Bergabung</label>
                    <input class="readonly-input" type="text" value="<?php echo htmlspecialchars($data['tanggal_bergabung']); ?>" readonly>
                </div>
            </div>

            <div class="save-area">
                <button type="submit" name="update" class="save-btn">
                    Update Profile
                </button>
            </div>

        </div>

    </form>

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