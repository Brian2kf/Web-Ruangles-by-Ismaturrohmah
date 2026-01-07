<?php
session_start();

// Cek Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

if (isset($_POST['simpan_profil'])) {
    $id_user     = $_SESSION["ssId"];
    $nama        = htmlspecialchars($_POST['nama_user']);
    $username    = htmlspecialchars($_POST['username']); // Username biasanya readonly/tidak diganti, tapi jika ingin diganti:
    $email       = htmlspecialchars($_POST['email']);
    $telepon     = htmlspecialchars($_POST['telepon']);
    $alamat      = htmlspecialchars($_POST['alamat']);
    
    // Logika Ganti Password
    $pass_lama = $_POST['password_lama'];
    $pass_baru = $_POST['password_baru'];
    
    // Ambil data user saat ini untuk cek password lama
    $queryUser = mysqli_query($koneksi, "SELECT password FROM tbl_user WHERE id = '$id_user'");
    $dataUser  = mysqli_fetch_assoc($queryUser);
    $hash_db   = $dataUser['password'];

    $query_update = "";

    // Skenario 1: User ingin ganti password
    if (!empty($pass_baru)) {
        // Cek apakah password lama kosong
        if (empty($pass_lama)) {
            echo "<script>alert('Harap isi Password Saat Ini untuk mengubah password.'); window.location='profile.php';</script>";
            exit;
        }

        // Verifikasi password lama
        if (password_verify($pass_lama, $hash_db)) {
            // Hash password baru
            $hash_baru = password_hash($pass_baru, PASSWORD_DEFAULT);
            
            $query_update = "UPDATE tbl_user SET 
                             nama_user = '$nama',
                             username = '$username', 
                             email_user = '$email',
                             telepon = '$telepon',
                             alamat = '$alamat',
                             password = '$hash_baru'
                             WHERE id = '$id_user'";
        } else {
            echo "<script>alert('Password Saat Ini salah! Perubahan gagal disimpan.'); window.location='profile.php';</script>";
            exit;
        }
    } 
    // Skenario 2: User hanya ganti biodata (tanpa ganti password)
    else {
        $query_update = "UPDATE tbl_user SET 
                         nama_user = '$nama',
                         username = '$username',
                         email_user = '$email',
                         telepon = '$telepon',
                         alamat = '$alamat'
                         WHERE id = '$id_user'";
    }

    // Eksekusi Query
    if (mysqli_query($koneksi, $query_update)) {
        // Update Session Nama jika berubah
        $_SESSION["ssNama"] = $nama;
        echo "<script>alert('Profil berhasil diperbarui!'); window.location='profile.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui profil: " . mysqli_error($koneksi) . "'); window.location='profile.php';</script>";
    }

} else {
    header("location: profile.php");
}
?>