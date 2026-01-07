<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

// Menangkap ID Pengajar dari URL
// Pastikan ID ada dan merupakan angka
if (isset($_GET['id_pengajar']) && is_numeric($_GET['id_pengajar'])) {
    
    $id_pengajar = (int)$_GET['id_pengajar']; // Ambil dan bersihkan ID

    // Membuat Kueri SQL untuk DELETE
    $query = "DELETE FROM tbl_data_pengajar WHERE id_pengajar = $id_pengajar";

    // Mengeksekusi Kueri
    $result = mysqli_query($koneksi, $query);

    // Memberikan Umpan Balik (Feedback)
    if ($result) {
        // Jika kueri berhasil dijalankan
        echo "<script>
                alert('Data pengajar berhasil dihapus!');
                document.location.href = 'pengajar.php';
              </script>";
    } else {
        // Jika kueri gagal
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menghapus data pengajar. Error: " . addslashes($error_msg) . "');
                document.location.href = 'pengajar.php';
              </script>";
    }

} else {
    // Jika ID tidak valid atau tidak ada
    echo "<script>
            alert('ID pengajar tidak valid!');
            document.location.href = 'pengajar.php';
          </script>";
}
?>