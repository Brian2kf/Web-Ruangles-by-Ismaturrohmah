<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

if (isset($_POST['kirim_balasan'])) {
    $id_feedback = $_POST['id_feedback'];
    $balasan     = htmlspecialchars($_POST['balasan']);
    // Menggunakan format tanggal dan jam lengkap (Y-m-d H:i:s)
    $tgl_balasan = date('Y-m-d H:i:s'); 

    $query = "UPDATE tbl_feedback SET 
              balasan = '$balasan', 
              tgl_balasan = '$tgl_balasan' 
              WHERE id_feedback = $id_feedback";
    
    $exec = mysqli_query($koneksi, $query);

    if ($exec) {
        header("location: feedback.php?msg=replied");
    } else {
        echo "<script>
                alert('Gagal mengirim balasan: " . mysqli_error($koneksi) . "');
                window.location = 'detail_feedback.php?id=$id_feedback';
              </script>";
    }
} else {
    header("location: feedback.php");
}
?>