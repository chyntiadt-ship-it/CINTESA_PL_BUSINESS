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

    // Cek apakah username berubah
    if ($username_baru != $data['username']) {
        // Cek jumlah perubahan username bulan ini
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

        // Cek username baru sudah dipakai user lain atau belum
        $cek_username = mysqli_query($koneksi, "SELECT * FROM user 
            WHERE username='$username_baru' 
            AND id_user != '$id_user'
        ");

        if (mysqli_num_rows($cek_username) > 0) {
            header("Location: profile.php?pesan=username_ada");
            exit;
        }

        // Simpan log perubahan username
        mysqli_query($koneksi, "INSERT INTO username_log 
            (id_user, username_lama, username_baru) 
            VALUES 
            ('$id_user', '{$data['username']}', '$username_baru')
        ");
    }

    // Upload foto jika ada
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
    <title>Profile Admin - Pree Love</title>
</head>
<body>

    <h2>Profile Admin</h2>

    <?php
    if (isset($_GET['pesan'])) {
        if ($_GET['pesan'] == "berhasil") {
            echo "<p style='color:green;'>Profile berhasil diperbarui.</p>";
        } elseif ($_GET['pesan'] == "gagal") {
            echo "<p style='color:red;'>Profile gagal diperbarui.</p>";
        } elseif ($_GET['pesan'] == "username_limit") {
            echo "<p style='color:red;'>Username hanya bisa diganti maksimal 2x dalam sebulan.</p>";
        } elseif ($_GET['pesan'] == "username_ada") {
            echo "<p style='color:red;'>Username sudah digunakan user lain.</p>";
        } elseif ($_GET['pesan'] == "foto_invalid") {
            echo "<p style='color:red;'>Foto harus berformat JPG, JPEG, atau PNG.</p>";
        }
    }
    ?>

    <?php if (!empty($data['foto_profile'])) { ?>
        <img src="../uploads/profile/<?php echo $data['foto_profile']; ?>" width="120">
    <?php } else { ?>
        <p>Belum ada foto profile</p>
    <?php } ?>

    <form action="" method="POST" enctype="multipart/form-data">
        <br>

        <label>Foto Profile</label><br>
        <input type="file" name="foto_profile"><br><br>

        <label>Username</label><br>
        <input type="text" name="username" value="<?php echo $data['username']; ?>" required><br><br>

        <label>Nama Lengkap</label><br>
        <input type="text" value="<?php echo $data['nama_lengkap']; ?>" readonly><br><br>

        <label>Email</label><br>
        <input type="email" value="<?php echo $data['email']; ?>" readonly><br><br>

        <label>Nomor Telepon</label><br>
        <input type="text" name="nomor_telepon" value="<?php echo $data['nomor_telepon']; ?>"><br><br>

        <label>Role</label><br>
        <input type="text" value="<?php echo $data['role']; ?>" readonly><br><br>

        <label>Tanggal Bergabung</label><br>
        <input type="text" value="<?php echo $data['tanggal_bergabung']; ?>" readonly><br><br>

        <button type="submit" name="update">Update Profile</button>
    </form>

    <br>
    <a href="dashboard.php">Kembali ke Dashboard</a>

</body>
</html>