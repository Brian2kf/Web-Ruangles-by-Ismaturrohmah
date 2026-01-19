<?php
session_start();
require_once "../../config.php";
require_once "../pembayaran/fungsi_invoice.php"; // TAMBAH INI

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

// 2. Menangkap ID Absensi dan ID Kelas dari URL
if (isset($_GET['id_absensi']) && is_numeric($_GET['id_absensi']) && isset($_GET['id_kelas'])) {
    
    $id_absensi = (int)$_GET['id_absensi'];
    $id_kelas   = (int)$_GET['id_kelas'];
    
    // ============================================
    // INTEGRASI PEMBAYARAN: KEMBALIKAN SESI JIKA STATUS HADIR
    // ============================================
    
    // Ambil data absensi yang akan dihapus
    $query_cek = "SELECT a.status_absensi, a.id_murid, m.nama_murid 
                  FROM tbl_absensi a
                  JOIN tbl_data_murid m ON a.id_murid = m.id_murid
                  WHERE a.id_absensi = $id_absensi";
    $result_cek = mysqli_query($koneksi, $query_cek);
    $data_absen = mysqli_fetch_assoc($result_cek);
    
    $pesan_tambahan = "";
    
    // Jika status adalah "Hadir", kembalikan sesi
    if ($data_absen && $data_absen['status_absensi'] == 'Hadir') {
        
        // Hapus dari tbl_sesi_terpakai
        $query_hapus_sesi = "DELETE FROM tbl_sesi_terpakai WHERE id_absensi = $id_absensi";
        mysqli_query($koneksi, $query_hapus_sesi);
        
        // Tambah kembali sesi tersisa
        $query_tambah = "UPDATE tbl_pembayaran SET 
                         sesi_terpakai = GREATEST(sesi_terpakai - 1, 0),
                         sesi_tersisa = sesi_tersisa + 1
                         WHERE id_murid = " . $data_absen['id_murid'] . "
                         AND id_kelas_bimbel = $id_kelas
                         AND status_pembayaran = 'Lunas'
                         ORDER BY id_pembayaran ASC
                         LIMIT 1";
        
        if (mysqli_query($koneksi, $query_tambah)) {
            $pesan_tambahan = "\n\n♻️ Sesi " . $data_absen['nama_murid'] . " telah dikembalikan karena absensi 'Hadir' dihapus.";
        }
    }
    
    // ============================================

    // 3. Hapus data absensi
    $query = "DELETE FROM tbl_absensi WHERE id_absensi = $id_absensi";
    $result = mysqli_query($koneksi, $query);

    // 5. Feedback
    if ($result) {
        $pesan_alert = "Data absensi berhasil dihapus!" . $pesan_tambahan;
        echo "<script>
            alert(" . json_encode($pesan_alert) . ");
            document.location.href = 'detail-absensi.php?id_kelas=" . $id_kelas . "';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menghapus data absensi. Error: " . addslashes($error_msg) . "');
                document.location.href = 'detail-absensi.php?id_kelas=" . $id_kelas . "';
              </script>";
    }

} else {
    echo "<script>
            alert('ID absensi atau ID kelas tidak valid!');
            document.location.href = 'absensi.php';
          </script>";
}
?>