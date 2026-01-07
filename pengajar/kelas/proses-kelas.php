<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

// ===========================================================
// LOGIKA 1: TAMBAH JADWAL (Create Schedule)
// ===========================================================
if (isset($_POST['tambah_jadwal'])) {

    // Ambil data dari form ubah-jadwal.php
    $id_kelas    = (int)$_POST['id_kelas_bimbel'];
    $hari        = mysqli_real_escape_string($koneksi, $_POST['hari']);
    $jam_mulai   = mysqli_real_escape_string($koneksi, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($koneksi, $_POST['jam_selesai']);

    // Validasi sederhana
    if (empty($hari) || empty($jam_mulai) || empty($jam_selesai)) {
        echo "<script>
                alert('Semua data jadwal harus diisi!');
                document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
              </script>";
        exit();
    }

    // Kueri INSERT
    $query = "INSERT INTO tbl_jadwal_kelas (id_kelas_bimbel, hari, jam_mulai, jam_selesai) 
              VALUES ($id_kelas, '$hari', '$jam_mulai', '$jam_selesai')";
    
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
                alert('Jadwal baru berhasil ditambahkan!');
                document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
              </script>";
    } else {
        echo "<script>
                alert('Gagal menambahkan jadwal: " . mysqli_error($koneksi) . "');
                document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
              </script>";
    }
}

// ===========================================================
// LOGIKA 2: HAPUS JADWAL (Delete Schedule)
// ===========================================================
else if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    
    // Pastikan ID tersedia
    if (isset($_GET['id_jadwal']) && isset($_GET['id_kelas'])) {
        $id_jadwal = (int)$_GET['id_jadwal'];
        $id_kelas  = (int)$_GET['id_kelas']; // Untuk redirect kembali

        $query = "DELETE FROM tbl_jadwal_kelas WHERE id_jadwal = $id_jadwal";
        $result = mysqli_query($koneksi, $query);

        if ($result) {
            echo "<script>
                    alert('Jadwal berhasil dihapus!');
                    document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
                  </script>";
        } else {
            echo "<script>
                    alert('Gagal menghapus jadwal: " . mysqli_error($koneksi) . "');
                    document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
                  </script>";
        }
    } else {
        echo "<script>alert('ID tidak valid.'); window.history.back();</script>";
    }
}

// Jika diakses langsung tanpa parameter yang benar
else {
    header("location: kelas.php");
    exit();
}
?>