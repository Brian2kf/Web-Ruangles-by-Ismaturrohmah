<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

/**
 * Fungsi untuk menghitung ulang dan memperbarui jumlah murid di tbl_kelas_bimbel.
 * Fungsi ini akan menghitung semua murid yang memiliki id_kelas_bimbel yang sama.
 *
 * @param mysqli $koneksi - Variabel koneksi database Anda.
 * @param int $id_kelas_bimbel - ID kelas yang akan dihitung ulang.
 */
function updateJumlahMurid($koneksi, $id_kelas_bimbel) {
    // 1. Hitung jumlah murid di kelas tersebut
    $queryHitung = "SELECT COUNT(*) AS total FROM tbl_data_murid WHERE id_kelas_bimbel = $id_kelas_bimbel";
    $resultHitung = mysqli_query($koneksi, $queryHitung);
    $dataHitung = mysqli_fetch_assoc($resultHitung);
    $totalMurid = (int)$dataHitung['total'];

    // 2. Update jumlah murid di tabel kelas
    $queryUpdate = "UPDATE tbl_kelas_bimbel SET jumlah_murid = $totalMurid WHERE id_kelas_bimbel = $id_kelas_bimbel";
    mysqli_query($koneksi, $queryUpdate);
}


// Menangkap ID Murid dari URL
if (isset($_GET['id_murid']) && is_numeric($_GET['id_murid'])) {
    
    $id_murid = (int)$_GET['id_murid']; // Ambil ID murid

    // PERUBAHAN: Dapatkan ID kelas murid SEBELUM dihapus
    $queryDataLama = mysqli_query($koneksi, "SELECT id_kelas_bimbel FROM tbl_data_murid WHERE id_murid = $id_murid");
    $dataLama = mysqli_fetch_assoc($queryDataLama);
    $id_kelas = (int)$dataLama['id_kelas_bimbel'];

    // Kueri SQL untuk DELETE
    $query = "DELETE FROM tbl_data_murid WHERE id_murid = $id_murid";
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        // PERUBAHAN: Panggil fungsi update count untuk kelas yang baru ditinggalkan murid
        if ($id_kelas > 0) { // Pastikan ID kelas valid
            updateJumlahMurid($koneksi, $id_kelas);
        }

        echo "<script>
                alert('Data murid berhasil dihapus!');
                document.location.href = 'murid.php';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menghapus data murid. Error: " . addslashes($error_msg) . "');
                document.location.href = 'murid.php';
              </script>";
    }

} else {
    echo "<script>
            alert('ID murid tidak valid!');
            document.location.href = 'murid.php';
          </script>";
}
?>