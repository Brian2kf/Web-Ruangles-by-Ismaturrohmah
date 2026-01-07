<?php
session_start();
require_once "../config.php";

if (isset($_POST['login'])) {
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    // 1. Cek username
    $result = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$username'");

    if (mysqli_num_rows($result) === 1) {
        $row = mysqli_fetch_assoc($result);

        // 2. Cek password
        if (password_verify($password, $row["password"])) {
            
            // 3. Password benar, set semua session yang diperlukan
            $_SESSION["ssLogin"] = true;
            $_SESSION["ssUser"] = $username;
            $_SESSION["ssNama"] = $row['nama_user'];
            $_SESSION["ssRole"] = $row['role']; // <-- PENTING: Simpan role di session
            $_SESSION["ssId"] = $row['id'];     // <-- PENTING: Simpan ID user

            // 4. Arahkan pengguna berdasarkan role
            switch ($row['role']) {
                case '1': // Role 1 = Orang Tua
                    header("location: ../orangTua/dashboard_orangtua.php");
                    break;
                case '2': // Role 2 = Admin
                    header("location: ../admin/dashboard_admin.php");
                    break;
                case '3': // Role 3 = Pengajar
                    header("location: ../pengajar/dashboard_pengajar.php");
                    break;
                default:
                    // Jika rolenya tidak dikenal (seharusnya tidak terjadi)
                    echo "<script>alert('Role tidak dikenal! Hubungi administrator.');document.location.href= 'login.php';</script>";
            }
            exit; // Penting untuk menghentikan eksekusi script setelah redirect

        } else {
            // Password salah
            echo "<script>alert('Username atau Password salah!');document.location.href= 'login.php';</script>";
        }

    } else {
        // Username tidak ditemukan
        echo "<script>alert('Username atau Password salah!');document.location.href= 'login.php';</script>";
    }
}
?>