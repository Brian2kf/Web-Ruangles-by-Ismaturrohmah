<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";

// 1. PROSES SIMPAN DATA BARU (CREATE)
if (isset($_POST['simpan'])) {
    // ambil value elemen yang diposting
    $username   = trim(htmlspecialchars($_POST['username']));
    $nama_user  = trim(htmlspecialchars($_POST['nama_user']));
    $email_user = trim(htmlspecialchars($_POST['email_user']));
    $telepon    = trim(htmlspecialchars($_POST['telepon']));
    $alamat     = trim(htmlspecialchars($_POST['alamat']));
    $role       = $_POST['role'];
    
    // Set password default 1234
    $password   = 1234;
    $pass       = password_hash($password, PASSWORD_DEFAULT);

    // Cek Username ganda
    $cekusername = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$username'");
    if (mysqli_num_rows($cekusername) > 0) {
        header("location:add-user.php?msg=cancel");
        return;
    }

    mysqli_query($koneksi, "INSERT INTO tbl_user VALUES(null, '$username','$pass','$nama_user','$email_user','$telepon','$alamat','$role')");
    header("location:add-user.php?msg=added");
    return;
}

// 2. PROSES UPDATE DATA (EDIT)
else if (isset($_POST['update'])) {
    // Ambil ID
    $id = $_POST['id'];
    
    // Ambil dan bersihkan inputan
    $username   = trim(htmlspecialchars($_POST['username']));
    $nama_user  = trim(htmlspecialchars($_POST['nama_user']));
    $email_user = trim(htmlspecialchars($_POST['email_user']));
    $telepon    = trim(htmlspecialchars($_POST['telepon']));
    $alamat     = trim(htmlspecialchars($_POST['alamat']));
    $role       = $_POST['role'];
    $password   = $_POST['password']; // Password dari input form edit

    // Logika Cek Password:
    // Jika kolom password kosong, artinya admin tidak ingin mengubah password user.
    // Jika terisi, maka kita hash password barunya.
    if (empty($password)) {
        $query = "UPDATE tbl_user SET 
                    username    = '$username',
                    nama_user   = '$nama_user',
                    email_user  = '$email_user',
                    telepon     = '$telepon',
                    alamat      = '$alamat',
                    role        = '$role'
                  WHERE id = $id";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $query = "UPDATE tbl_user SET 
                    username    = '$username',
                    password    = '$passwordHash',
                    nama_user   = '$nama_user',
                    email_user  = '$email_user',
                    telepon     = '$telepon',
                    alamat      = '$alamat',
                    role        = '$role'
                  WHERE id = $id";
    }

    $exec = mysqli_query($koneksi, $query);

    // Redirect kembali ke halaman kelola_pengguna (bukan add-user)
    if ($exec) {
        header("location: kelola_pengguna.php?msg=updated");
    } else {
        header("location: kelola_pengguna.php?msg=cancel");
    }
    return;
}

// 3. PROSES HAPUS DATA (DELETE)
else if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    
    $queryHapus = "DELETE FROM tbl_user WHERE id = $id";
    $execHapus = mysqli_query($koneksi, $queryHapus);

    if ($execHapus) {
        header("location: kelola_pengguna.php?msg=deleted");
    } else {
        header("location: kelola_pengguna.php?msg=cancel");
    }
    return;
}
?>