<?php
session_start();
include '../include/koneksi.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../auth/login.php?pesan=belum_login");
    exit;
}

if ($_SESSION['role'] != 'penjual') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_POST['update'])) {
    $id_user = $_SESSION['id_user'];

    $id_produk = mysqli_real_escape_string($koneksi, $_POST['id_produk']);
    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $alamat_produk = mysqli_real_escape_string($koneksi, $_POST['alamat_produk']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $keterangan_nego = mysqli_real_escape_string($koneksi, $_POST['keterangan_nego']);
    $status_produk = mysqli_real_escape_string($koneksi, $_POST['status_produk']);

    if (
        empty($id_produk) ||
        empty($id_kategori) ||
        empty($nama_produk) ||
        empty($deskripsi) ||
        empty($alamat_produk) ||
        empty($harga) ||
        empty($keterangan_nego) ||
        empty($status_produk)
    ) {
        header("Location: edit_produk.php?id=$id_produk&pesan=data_kosong");
        exit;
    }

    // Pastikan produk milik penjual yang login
    $cek_produk = mysqli_query($koneksi, "SELECT * FROM produk 
        WHERE id_produk='$id_produk' 
        AND id_user='$id_user'
    ");

    if (mysqli_num_rows($cek_produk) == 0) {
        echo "Produk tidak ditemukan atau bukan milik Anda.";
        exit;
    }

    // Update data produk
    $update_produk = mysqli_query($koneksi, "UPDATE produk SET
        id_kategori='$id_kategori',
        nama_produk='$nama_produk',
        deskripsi='$deskripsi',
        alamat_produk='$alamat_produk',
        harga='$harga',
        keterangan_nego='$keterangan_nego',
        status_produk='$status_produk'
        WHERE id_produk='$id_produk'
        AND id_user='$id_user'
    ");

    if (!$update_produk) {
        header("Location: edit_produk.php?id=$id_produk&pesan=gagal");
        exit;
    }

    // Jika user upload foto baru
    if (!empty($_FILES['foto_produk']['name'][0])) {
        if (count($_FILES['foto_produk']['name']) > 3) {
            header("Location: edit_produk.php?id=$id_produk&pesan=foto_lebih");
            exit;
        }

        $ekstensi_valid = ['jpg', 'jpeg', 'png'];

        foreach ($_FILES['foto_produk']['name'] as $nama_file) {
            $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

            if (!in_array($ekstensi, $ekstensi_valid)) {
                header("Location: edit_produk.php?id=$id_produk&pesan=foto_invalid");
                exit;
            }
        }

        // Hapus foto lama dari folder
        $foto_lama = mysqli_query($koneksi, "SELECT * FROM produk_foto WHERE id_produk='$id_produk'");

        while ($foto = mysqli_fetch_assoc($foto_lama)) {
            $path_foto = '../uploads/produk/' . $foto['foto'];

            if (file_exists($path_foto)) {
                unlink($path_foto);
            }
        }

        // Hapus foto lama dari database
        mysqli_query($koneksi, "DELETE FROM produk_foto WHERE id_produk='$id_produk'");

        // Simpan foto baru
        foreach ($_FILES['foto_produk']['name'] as $index => $nama_file) {
            $tmp_file = $_FILES['foto_produk']['tmp_name'][$index];
            $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

            if (!empty($nama_file)) {
                $nama_baru = 'produk_' . $id_produk . '_' . time() . '_' . $index . '.' . $ekstensi;
                $folder_upload = '../uploads/produk/' . $nama_baru;

                if (move_uploaded_file($tmp_file, $folder_upload)) {
                    mysqli_query($koneksi, "INSERT INTO produk_foto 
                        (id_produk, foto) 
                        VALUES 
                        ('$id_produk', '$nama_baru')
                    ");
                }
            }
        }
    }

    header("Location: produk.php?pesan=edit_berhasil");
    exit;
} else {
    header("Location: produk.php");
    exit;
}
?>