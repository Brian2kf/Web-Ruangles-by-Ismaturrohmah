<?php
session_start();
require_once "../../config.php"; 

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

/**
 * Fungsi updateJumlahMurid (Tetap sama, tidak perlu diubah)
 */
function updateJumlahMurid($koneksi, $id_kelas_bimbel) {
    $queryHitung = "SELECT COUNT(*) AS total FROM tbl_data_murid WHERE id_kelas_bimbel = $id_kelas_bimbel";
    $resultHitung = mysqli_query($koneksi, $queryHitung);
    $dataHitung = mysqli_fetch_assoc($resultHitung);
    $totalMurid = (int)$dataHitung['total'];

    $queryUpdate = "UPDATE tbl_kelas_bimbel SET jumlah_murid = $totalMurid WHERE id_kelas_bimbel = $id_kelas_bimbel";
    mysqli_query($koneksi, $queryUpdate);
}


// 2. Logika untuk "SIMPAN" (Tambah Data Baru)
if (isset($_POST['simpan'])) {
    
    // Ambil dan bersihkan data
    $nama       = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat     = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $id_program = (int)$_POST['id_program'];
    $id_tingkat = (int)$_POST['id_tingkat'];
    $id_kelas   = (int)$_POST['id_kelas_bimbel']; 

    // -- LOGIKA BARU UNTUK ORANG TUA --
    // Cek jika user memilih orang tua, jika kosong set NULL
    $id_ortu_post = $_POST['id_user_ortu'];
    if (!empty($id_ortu_post)) {
        $id_ortu_val = (int)$id_ortu_post; // Pastikan integer
    } else {
        $id_ortu_val = "NULL"; // String "NULL" untuk SQL
    }
    // ---------------------------------

    // Kueri SQL untuk INSERT (Perhatikan penambahan id_user_ortu)
    $query = "INSERT INTO tbl_data_murid 
              (nama_murid, id_user_ortu, id_program, id_tingkat, alamat_murid, id_kelas_bimbel) 
              VALUES 
              ('$nama', $id_ortu_val, $id_program, $id_tingkat, '$alamat', $id_kelas)";
    
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        updateJumlahMurid($koneksi, $id_kelas);
        echo "<script>
                alert('Data murid baru berhasil ditambahkan!');
                document.location.href = 'murid.php';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menambahkan data murid. Error: " . addslashes($error_msg) . "');
                document.location.href = 'add-murid.php';
              </script>";
    }

} 
// 3. Logika untuk "UPDATE" (Edit Data)
else if (isset($_POST['update'])) {

    $id_murid     = (int)$_POST['id_murid'];
    $nama         = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $alamat       = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $id_program   = (int)$_POST['id_program'];
    $id_tingkat   = (int)$_POST['id_tingkat'];
    $id_kelas_baru = (int)$_POST['id_kelas_bimbel'];

    // -- LOGIKA BARU UNTUK ORANG TUA --
    $id_ortu_post = $_POST['id_user_ortu'];
    if (!empty($id_ortu_post)) {
        $id_ortu_val = (int)$id_ortu_post;
    } else {
        $id_ortu_val = "NULL";
    }
    // ---------------------------------

    // Dapatkan ID kelas LAMA
    $queryDataLama = mysqli_query($koneksi, "SELECT id_kelas_bimbel FROM tbl_data_murid WHERE id_murid = $id_murid");
    $dataLama = mysqli_fetch_assoc($queryDataLama);
    $id_kelas_lama = (int)$dataLama['id_kelas_bimbel'];

    // Kueri SQL untuk UPDATE (Perhatikan penambahan id_user_ortu)
    $query = "UPDATE tbl_data_murid SET 
                nama_murid = '$nama',
                id_user_ortu = $id_ortu_val,
                id_program = $id_program,
                id_tingkat = $id_tingkat,
                alamat_murid = '$alamat',
                id_kelas_bimbel = $id_kelas_baru
              WHERE id_murid = $id_murid";
    
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        updateJumlahMurid($koneksi, $id_kelas_baru);
        if ($id_kelas_lama != $id_kelas_baru) {
            updateJumlahMurid($koneksi, $id_kelas_lama);
        }
        echo "<script>
                alert('Data murid berhasil diperbarui!');
                document.location.href = 'murid.php';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal memperbarui data murid. Error: " . addslashes($error_msg) . "');
                document.location.href = 'edit-murid.php?id_murid=" . $id_murid . "';
              </script>";
    }

} 
else {
    header("location: murid.php");
    exit();
}
?>