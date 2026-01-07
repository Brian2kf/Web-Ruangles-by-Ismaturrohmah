<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

// Menangkap ID Kelas dari URL
if (isset($_GET['id_kelas_bimbel']) && is_numeric($_GET['id_kelas_bimbel'])) {
    
    $id_kelas_bimbel = (int)$_GET['id_kelas_bimbel']; // Ambil dan bersihkan ID

    // Membuat Kueri SQL untuk DELETE
    // Berdasarkan SQL Anda, murid yang ada di kelas ini akan 
    // otomatis di-set `id_kelas_bimbel` = NULL (ON DELETE SET NULL)
    $query = "DELETE FROM tbl_kelas_bimbel WHERE id_kelas_bimbel = $id_kelas_bimbel";

    // Mengeksekusi Kueri
    $result = mysqli_query($koneksi, $query);

    // Memberikan Umpan Balik (Feedback)
    if ($result) {
        echo "<script>
                alert('Data kelas berhasil dihapus!');
                document.location.href = 'kelas.php';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menghapus data kelas. Error: " . addslashes($error_msg) . "');
                document.location.href = 'kelas.php';
              </script>";
    }

} else {
    // Jika ID tidak valid atau tidak ada
    echo "<script>
            alert('ID kelas tidak valid!');
            document.location.href = 'kelas.php';
          </script>";
}
?>