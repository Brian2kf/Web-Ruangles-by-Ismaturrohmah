<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

if (isset($_POST['aksi'])) {
    $id_pendaftaran = $_POST['id_pendaftaran'];
    $aksi = $_POST['aksi'];

    // 1. Ambil Data Pendaftar Terlebih Dahulu
    $queryAmbil = mysqli_query($koneksi, "SELECT * FROM tbl_pendaftaran WHERE id_pendaftaran = $id_pendaftaran");
    $dataCalon = mysqli_fetch_array($queryAmbil);

    // Validasi jika data tidak ada
    if (!$dataCalon) {
        echo "<script>alert('Data pendaftar tidak ditemukan!'); window.location='pendaftar.php';</script>";
        exit;
    }

    // --- LOGIKA TERIMA ---
    if ($aksi == 'terima') {
        $status_baru = 'Diterima';
        $msg = 'accepted';

        // A. Masukkan Data ke Tabel Murid
        $nama_murid     = mysqli_real_escape_string($koneksi, $dataCalon['nama_camur']);
        $id_program     = $dataCalon['id_program'];
        $alamat_murid   = mysqli_real_escape_string($koneksi, $dataCalon['alamat_camur']);
        
        // AMBIL ID TINGKAT DARI DATA PENDAFTAR
        $id_tingkat     = $dataCalon['id_tingkat']; 
        
        // UPDATE QUERY INSERT: Tambahkan kolom id_tingkat
        // Pastikan id_tingkat tidak kosong/null saat insert, atau biarkan null jika tidak ada
        $val_tingkat = !empty($id_tingkat) ? "'$id_tingkat'" : "NULL";

        $queryInsertMurid = "INSERT INTO tbl_data_murid (nama_murid, id_program, id_tingkat, alamat_murid) 
                             VALUES ('$nama_murid', '$id_program', $val_tingkat, '$alamat_murid')";
        
        $execMurid = mysqli_query($koneksi, $queryInsertMurid);

        // B. Buatkan Akun Orang Tua di Tabel User (tbl_user)
        // Username: Nama Depan Orang Tua + Angka Acak (agar unik)
        // Password Default: 1234
        if ($execMurid) {
            $nama_ortu  = mysqli_real_escape_string($koneksi, $dataCalon['nama_orgtua_wali']);
            $telepon    = mysqli_real_escape_string($koneksi, $dataCalon['telepon_orgtua_wali']);
            $alamat     = mysqli_real_escape_string($koneksi, $dataCalon['alamat_camur']);
            
            // Generate Username (Hapus spasi, ambil nama depan, tambah 3 digit acak)
            $username_base = explode(" ", $nama_ortu)[0]; 
            $username   = strtolower($username_base) . rand(100, 999);
            
            $email_user = mysqli_real_escape_string($koneksi, $dataCalon['email_orgtua_wali']);

            $password   = password_hash("1234", PASSWORD_DEFAULT);
            $role       = '1'; // Role 1 = Orang Tua

            $queryInsertUser = "INSERT INTO tbl_user (username, password, nama_user, email_user, telepon, alamat, role) 
                                VALUES ('$username', '$password', '$nama_ortu', '$email_user', '$telepon', '$alamat', '$role')";
            
            mysqli_query($koneksi, $queryInsertUser);
        }

    // --- LOGIKA TOLAK ---
    } else if ($aksi == 'tolak') {
        $status_baru = 'Ditolak';
        $msg = 'rejected';
    } else {
        header("location: pendaftar.php");
        exit;
    }

    // 2. Update Status Pendaftaran Akhir
    $queryUpdate = "UPDATE tbl_pendaftaran SET status_pendaftaran = '$status_baru' WHERE id_pendaftaran = $id_pendaftaran";
    $exec = mysqli_query($koneksi, $queryUpdate);

    if ($exec) {
        header("location: pendaftar.php?msg=$msg");
    } else {
        echo "<script>
                alert('Gagal memproses data: " . mysqli_error($koneksi) . "');
                window.location = 'detail_pendaftar.php?id=$id_pendaftaran';
              </script>";
    }

} else {
    header("location: pendaftar.php");
}
?>