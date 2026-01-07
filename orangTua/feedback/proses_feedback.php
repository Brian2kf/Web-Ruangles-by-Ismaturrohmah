<?php
session_start();

// Cek Login & Role Orang Tua
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// Fallback koneksi
if (!isset($koneksi) && isset($conn)) {
    $koneksi = $conn;
}

if (isset($_POST['kirim_feedback'])) {
    $id_user     = $_SESSION["ssId"]; // ID Orang Tua
    $tujuan      = htmlspecialchars($_POST['tujuan']);
    $isi         = htmlspecialchars($_POST['isi_feedback']);
    $tgl_kirim   = date('Y-m-d H:i:s');

    // Query Insert
    $query = "INSERT INTO tbl_feedback (id_orgtua_wali, nama_tujuan, isi_feedback, tgl_feedback) 
              VALUES ('$id_user', '$tujuan', '$isi', '$tgl_kirim')";
    
    $exec = mysqli_query($koneksi, $query);

    if ($exec) {
        // Berhasil, arahkan ke halaman riwayat
        header("location: riwayat_feedback.php?msg=sent");
    } else {
        echo "<script>
                alert('Gagal mengirim feedback: " . mysqli_error($koneksi) . "');
                window.location = 'feedback.php';
              </script>";
    }
} else {
    header("location: feedback.php");
}
?>