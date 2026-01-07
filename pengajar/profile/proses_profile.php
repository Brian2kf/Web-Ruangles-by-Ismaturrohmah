<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// Tombol simpan ditekan
if (isset($_POST['simpan'])) {
    // Ambil semua value dari form
    $id         = $_POST['id'];
    $username   = trim(htmlspecialchars($_POST['username']));
    $nama_user  = trim(htmlspecialchars($_POST['nama_user']));
    $email_user = trim(htmlspecialchars($_POST['email_user']));
    $telepon    = trim(htmlspecialchars($_POST['telepon']));
    $alamat     = trim(htmlspecialchars($_POST['alamat']));
    $oldPass    = trim(htmlspecialchars($_POST['oldPass'])); // Ambil password lama
    $newPass    = trim(htmlspecialchars($_POST['newPass'])); // Ambil password baru

    // Ambil data user yang sekarang (terutama untuk hash password)
    $queryUser = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE id = '$id'");
    $data = mysqli_fetch_assoc($queryUser);
    $currentDbHash = $data['password'];

    // Cek jika username diganti, apakah username baru sudah ada?
    if ($username !== $data['username']) {
        $checkUser = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$username'");
        if (mysqli_num_rows($checkUser) > 0) {
            // Username sudah terpakai, kirim pesan error
            header("location:profile_pengajar.php?msg=username_exists");
            exit;
        }
    }

    // Query UPDATE (hanya data profil)
    $query_sql = "UPDATE tbl_user SET 
                    username = '$username',
                    nama_user = '$nama_user',
                    email_user = '$email_user',
                    telepon = '$telepon',
                    alamat = '$alamat'";

    // Logika Pengecekan dan Update Password
    // Cek apakah pengguna berniat mengganti password (mengisi kolom 'Password Baru')
    if (!empty($newPass)) {
        
        // Jika 'Password Baru' diisi, maka 'Password Lama' WAJIB diisi
        if (empty($oldPass)) {
            // Jika 'Password Lama' kosong, kirim pesan error
            header("location:profile_pengajar.php?msg=oldpass_empty");
            exit;
        }

        // Jika 'Password Lama' diisi, verifikasi dengan hash di database
        if (password_verify($oldPass, $currentDbHash)) {
            // Password Lama BENAR, hash password baru
            $pass = password_hash($newPass, PASSWORD_DEFAULT);
            // Tambahkan password baru ke query SQL
            $query_sql .= ", password = '$pass'";
        } else {
            // Password Lama SALAH, kirim pesan error
            header("location:profile_pengajar.php?msg=oldpass_wrong");
            exit;
        }
    }
    // JIKA $newPass KOSONG, tidak ada kode yang dieksekusi di blok 'if' ini,
    // sehingga $query_sql tidak ditambahi password, dan password lama tetap aman.

    
    // Selesaikan dan Eksekusi Query
    $query_sql .= " WHERE id = '$id'";
    mysqli_query($koneksi, $query_sql);

    // Update session jika username berubah
    if ($username !== $_SESSION["ssUser"]) {
        $_SESSION["ssUser"] = $username;
    }

    // Redirect dengan pesan sukses
    header("location:profile_pengajar.php?msg=updated");
    return;
}
?>