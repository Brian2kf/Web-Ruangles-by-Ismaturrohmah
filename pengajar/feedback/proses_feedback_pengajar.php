<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

if (isset($_POST['kirim_balasan'])) {
    $id_feedback = $_POST['id_feedback'];
    $balasan     = htmlspecialchars($_POST['balasan']);
    $tgl_balasan = date('Y-m-d H:i:s'); 

    $query = "UPDATE tbl_feedback SET 
              balasan = '$balasan', 
              tgl_balasan = '$tgl_balasan' 
              WHERE id_feedback = $id_feedback";
    
    $exec = mysqli_query($koneksi, $query);

    if ($exec) {
        // Redirect ke feedback_pengajar.php (BUKAN admin)
        header("location: feedback_pengajar.php?msg=replied");
    } else {
        echo "<script>
                alert('Gagal mengirim balasan: " . mysqli_error($koneksi) . "');
                window.location = 'detail_feedback_pengajar.php?id=$id_feedback';
              </script>";
    }
} else {
    header("location: feedback_pengajar.php");
}
?>