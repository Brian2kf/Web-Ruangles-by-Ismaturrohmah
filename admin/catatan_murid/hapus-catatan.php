<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// 1. Ambil ID Progres dan ID Kelas dari URL
if (!isset($_GET['id_progres']) || !isset($_GET['id_kelas'])) {
    // Jika salah satu ID tidak ada, kembali ke halaman utama
    echo "<script>alert('ID tidak ditemukan.'); document.location.href = 'catatan_murid.php';</script>";
    exit();
}

$id_progres = (int)$_GET['id_progres'];
$id_kelas   = (int)$_GET['id_kelas'];

// 2. Buat query DELETE
$query = "DELETE FROM tbl_progres WHERE id_progres = $id_progres";

// 3. Eksekusi query
$result = mysqli_query($koneksi, $query);

// 4. Cek hasil eksekusi dan redirect kembali ke halaman detail
if ($result) {
    echo "<script>
            alert('Data catatan berhasil dihapus.');
            document.location.href = 'detail-catatan.php?id_kelas=$id_kelas';
          </script>";
} else {
    echo "<script>
            alert('Gagal menghapus data catatan: " . mysqli_error($koneksi) . "');
            document.location.href = 'detail-catatan.php?id_kelas=$id_kelas';
          </script>";
}
?>