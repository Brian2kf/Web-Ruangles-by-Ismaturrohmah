<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

// 2. Menangkap ID Absensi dan ID Kelas dari URL
// id_absensi untuk menghapus data, id_kelas untuk redirect kembali
if (isset($_GET['id_absensi']) && is_numeric($_GET['id_absensi']) && isset($_GET['id_kelas'])) {
    
    $id_absensi = (int)$_GET['id_absensi'];
    $id_kelas   = (int)$_GET['id_kelas']; // Untuk redirect kembali

    // 3. Membuat Kueri SQL untuk DELETE
    $query = "DELETE FROM tbl_absensi WHERE id_absensi = $id_absensi";

    // 4. Mengeksekusi Kueri
    $result = mysqli_query($koneksi, $query);

    // 5. Memberikan Umpan Balik (Feedback)
    if ($result) {
        // Jika kueri berhasil dijalankan
        echo "<script>
                alert('Data absensi berhasil dihapus!');
                document.location.href = 'detail-absensi.php?id_kelas=" . $id_kelas . "';
              </script>";
    } else {
        // Jika kueri gagal
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menghapus data absensi. Error: " . addslashes($error_msg) . "');
                document.location.href = 'detail-absensi.php?id_kelas=" . $id_kelas . "';
              </script>";
    }

} else {
    // Jika ID tidak valid atau tidak ada
    echo "<script>
            alert('ID absensi atau ID kelas tidak valid!');
            document.location.href = 'absensi.php';
          </script>";
}
?>