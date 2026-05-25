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

if (isset($_POST['simpan'])) {
    $id_user = $_SESSION['id_user'];

    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $nama_produk = mysqli_real_escape_string($koneksi, $_POST['nama_produk']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $kondisi_barang = mysqli_real_escape_string($koneksi, $_POST['kondisi_barang']);
    $ukuran = mysqli_real_escape_string($koneksi, $_POST['ukuran']);
    $merek = mysqli_real_escape_string($koneksi, $_POST['merek']);
    $alamat_produk = mysqli_real_escape_string($koneksi, $_POST['alamat_produk']);
    $harga = mysqli_real_escape_string($koneksi, $_POST['harga']);
    $keterangan_nego = mysqli_real_escape_string($koneksi, $_POST['keterangan_nego']);
    $status_produk = mysqli_real_escape_string($koneksi, $_POST['status_produk']);

    if (
        empty($id_kategori) ||
        empty($nama_produk) ||
        empty($deskripsi) ||
        empty($kondisi_barang) ||
        empty($ukuran) ||
        empty($merek) ||
        empty($alamat_produk) ||
        empty($harga) ||
        empty($keterangan_nego) ||
        empty($status_produk)
    ) {
        header("Location: tambah_produk.php?pesan=data_kosong");
        exit;
    }

    if (count($_FILES['foto_produk']['name']) > 3) {
        header("Location: tambah_produk.php?pesan=foto_lebih");
        exit;
    }

    $ekstensi_valid = ['jpg', 'jpeg', 'png'];

    foreach ($_FILES['foto_produk']['name'] as $nama_file) {
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        if (!in_array($ekstensi, $ekstensi_valid)) {
            header("Location: tambah_produk.php?pesan=foto_invalid");
            exit;
        }
    }

    $simpan_produk = mysqli_query($koneksi, "INSERT INTO produk 
        (
            id_user,
            id_kategori,
            nama_produk,
            deskripsi,
            kondisi_barang,
            ukuran,
            merek,
            alamat_produk,
            harga,
            keterangan_nego,
            status_produk
        )
        VALUES 
        (
            '$id_user',
            '$id_kategori',
            '$nama_produk',
            '$deskripsi',
            '$kondisi_barang',
            '$ukuran',
            '$merek',
            '$alamat_produk',
            '$harga',
            '$keterangan_nego',
            '$status_produk'
        )
    ");

    if ($simpan_produk) {
        $id_produk = mysqli_insert_id($koneksi);

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

        header("Location: produk.php?pesan=tambah_berhasil");
        exit;
    } else {
        header("Location: produk.php?pesan=tambah_gagal");
        exit;
    }
} else {
    header("Location: tambah_produk.php");
    exit;
}
?>