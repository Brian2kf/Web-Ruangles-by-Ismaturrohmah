<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

// 2. Menangkap ID Jadwal dan ID Kelas dari URL
if (isset($_GET['id_jadwal']) && is_numeric($_GET['id_jadwal']) && isset($_GET['id_kelas'])) {
    
    $id_jadwal = (int)$_GET['id_jadwal'];
    $id_kelas  = (int)$_GET['id_kelas']; // Untuk redirect kembali

    // 3. Membuat Kueri SQL untuk DELETE
    $query = "DELETE FROM tbl_jadwal_kelas WHERE id_jadwal = $id_jadwal";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
                alert('Jadwal berhasil dihapus!');
                // Arahkan kembali ke halaman kelola jadwal
                document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menghapus jadwal. Error: " . addslashes($error_msg) . "');
                document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
              </script>";
    }

} else {
    // Jika ID tidak valid
    echo "<script>
            alert('ID jadwal tidak valid!');
            document.location.href = 'kelas.php';
          </script>";
}
?>