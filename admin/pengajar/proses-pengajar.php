<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

// 2. Logika untuk "SIMPAN" (Tambah Data Baru)
if (isset($_POST['simpan'])) {
    
    // Ambil dan bersihkan data dari form add-pengajar.php
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $telepon  = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $alamat   = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // Kueri SQL untuk INSERT
    $query = "INSERT INTO tbl_data_pengajar 
              (nama_pengajar, no_telepon, email, alamat_pengajar) 
              VALUES 
              ('$nama', '$telepon', '$email', '$alamat')";

    // Eksekusi Kueri
    $result = mysqli_query($koneksi, $query);

    // Umpan Balik
    if ($result) {
        echo "<script>
                alert('Data pengajar baru berhasil ditambahkan!');
                document.location.href = 'pengajar.php';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menambahkan data pengajar. Error: " . addslashes($error_msg) . "');
                document.location.href = 'add-pengajar.php';
              </script>";
    }

} 
// 3. Logika untuk "UPDATE" (Edit Data)
else if (isset($_POST['update'])) {

    // Ambil dan bersihkan data dari form edit-pengajar.php
    $id_pengajar = (int)$_POST['id_pengajar']; // Ambil ID dari input hidden
    $nama        = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $telepon     = mysqli_real_escape_string($koneksi, $_POST['no_telepon']);
    $email       = mysqli_real_escape_string($koneksi, $_POST['email']);
    $alamat      = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // Kueri SQL untuk UPDATE
    $query = "UPDATE tbl_data_pengajar SET 
                nama_pengajar = '$nama',
                no_telepon = '$telepon',
                email = '$email',
                alamat_pengajar = '$alamat'
              WHERE id_pengajar = $id_pengajar";
    
    // Eksekusi Kueri
    $result = mysqli_query($koneksi, $query);

    // Umpan Balik
    if ($result) {
        echo "<script>
                alert('Data pengajar berhasil diperbarui!');
                document.location.href = 'pengajar.php';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal memperbarui data pengajar. Error: " . addslashes($error_msg) . "');
                document.location.href = 'edit-pengajar.php?id_pengajar=" . $id_pengajar . "';
              </script>";
    }

} 
// 4. Jika diakses tanpa tombol
else {
    // Arahkan kembali ke halaman data pengajar jika diakses tanpa menekan tombol
    header("location: pengajar.php");
    exit();
}
?>