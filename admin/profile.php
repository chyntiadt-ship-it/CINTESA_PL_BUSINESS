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
            <a href="customer_service.php">Customer Service</a>
            <a href="profile.php" class="active">Profile Admin</a>
        </nav>

        <div class="sidebar-footer">
            <a href="../auth/logout.php" class="logout-btn">Logout</a>
        </div>
    </aside>

    <main class="main-content">

        <section class="topbar">
            <div>
                <h1>Profile Admin</h1>
                <p>
                    Kelola informasi akun admin CINTESA. Admin dapat memperbarui username,
                    nomor telepon, dan foto profil.
                </p>
            </div>

            <div class="admin-badge">
                Admin Profile
            </div>
        </section>

        <section class="section-card">

            <div class="section-header">
                <div>
                    <h2>Informasi Profile</h2>
                    <p>Pastikan data profile admin tetap sesuai dan mudah dikenali.</p>
                </div>

                <a href="dashboard.php" class="small-btn">Kembali ke Dashboard</a>
            </div>

            <?php
            if (isset($_GET['pesan'])) {
                if ($_GET['pesan'] == "berhasil") {
                    echo "<div class='alert success'>Profile berhasil diperbarui.</div>";
                } elseif ($_GET['pesan'] == "gagal") {
                    echo "<div class='alert danger'>Profile gagal diperbarui.</div>";
                } elseif ($_GET['pesan'] == "username_limit") {
                    echo "<div class='alert danger'>Username hanya bisa diganti maksimal 2x dalam sebulan.</div>";
                } elseif ($_GET['pesan'] == "username_ada") {
                    echo "<div class='alert danger'>Username sudah digunakan user lain.</div>";
                } elseif ($_GET['pesan'] == "foto_invalid") {
                    echo "<div class='alert danger'>Foto harus berformat JPG, JPEG, atau PNG.</div>";
                }
            }
            ?>

            <div class="profile-layout">

                <div class="profile-preview-card">
                    <div class="profile-photo-wrap">
                        <?php if (!empty($data['foto_profile'])) { ?>
                            <img 
                                src="../uploads/profile/<?php echo htmlspecialchars($data['foto_profile']); ?>" 
                                class="profile-photo-admin"
                                alt="Foto Profile Admin"
                            >
                        <?php } else { ?>
                            <div class="profile-photo-empty">
                                <?php echo strtoupper(substr($data['nama_lengkap'], 0, 1)); ?>
                            </div>
                        <?php } ?>
                    </div>

                    <h3><?php echo htmlspecialchars($data['nama_lengkap']); ?></h3>
                    <p>@<?php echo htmlspecialchars($data['username']); ?></p>

                    <span class="status-badge status-terjual">
                        <?php echo htmlspecialchars(ucfirst($data['role'])); ?>
                    </span>
                </div>

                <form action="" method="POST" enctype="multipart/form-data" class="profile-form-card">

                    <div class="form-group full">
                        <label>Foto Profile</label>
                        <input type="file" name="foto_profile" class="form-control file-control">
                        <small>Format yang diperbolehkan: JPG, JPEG, PNG.</small>
                    </div>

                    <div class="form-group">
                        <label>Username</label>
                        <input 
                            type="text" 
                            name="username" 
                            value="<?php echo htmlspecialchars($data['username']); ?>" 
                            class="form-control"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input 
                            type="text" 
                            value="<?php echo htmlspecialchars($data['nama_lengkap']); ?>" 
                            class="form-control readonly"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input 
                            type="email" 
                            value="<?php echo htmlspecialchars($data['email']); ?>" 
                            class="form-control readonly"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Nomor Telepon</label>
                        <input 
                            type="text" 
                            name="nomor_telepon" 
                            value="<?php echo htmlspecialchars($data['nomor_telepon']); ?>" 
                            class="form-control"
                            placeholder="Masukkan nomor telepon"
                        >
                    </div>

                    <div class="form-group">
                        <label>Role</label>
                        <input 
                            type="text" 
                            value="<?php echo htmlspecialchars($data['role']); ?>" 
                            class="form-control readonly"
                            readonly
                        >
                    </div>

                    <div class="form-group">
                        <label>Tanggal Bergabung</label>
                        <input 
                            type="text" 
                            value="<?php echo htmlspecialchars($data['tanggal_bergabung']); ?>" 
                            class="form-control readonly"
                            readonly
                        >
                    </div>

                    <div class="form-action full">
                        <button type="submit" name="update" class="btn-submit">
                            Update Profile
                        </button>
                    </div>

                </form>

            </div>

        </section>

    </main>

</div>

</body>
</html>