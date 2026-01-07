<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

// 2. Logika untuk "SIMPAN" (Tambah Data Baru) - DITULIS ULANG
if (isset($_POST['simpan'])) {
    
    // Ambil data utama kelas
    $nama_kelas = mysqli_real_escape_string($koneksi, $_POST['nama_kelas_bimbel']);
    $id_program = (int)$_POST['id_program'];
    $id_tingkat = (int)$_POST['id_tingkat'];

    // Ambil data jadwal pertama
    $hari        = mysqli_real_escape_string($koneksi, $_POST['hari']);
    $jam_mulai   = mysqli_real_escape_string($koneksi, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($koneksi, $_POST['jam_selesai']);

    // Mulai Transaksi Database
    // Ini memastikan jika salah satu query gagal, semua query akan dibatalkan
    mysqli_begin_transaction($koneksi);

    try {
        // Langkah A: Insert data utama ke tbl_kelas_bimbel
        $query_kelas = "INSERT INTO tbl_kelas_bimbel (nama_kelas_bimbel, id_program, id_tingkat) 
                        VALUES ('$nama_kelas', $id_program, $id_tingkat)";
        mysqli_query($koneksi, $query_kelas);

        // Langkah B: Ambil ID kelas yang baru saja dibuat
        $id_kelas_baru = mysqli_insert_id($koneksi);

        // Jika ID kelas tidak valid, lemparkan error
        if ($id_kelas_baru == 0) {
            throw new Exception("Gagal mendapatkan ID kelas baru.");
        }

        // Langkah C: Insert data jadwal ke tbl_jadwal_kelas
        $query_jadwal = "INSERT INTO tbl_jadwal_kelas (id_kelas_bimbel, hari, jam_mulai, jam_selesai) 
                         VALUES ($id_kelas_baru, '$hari', '$jam_mulai', '$jam_selesai')";
        mysqli_query($koneksi, $query_jadwal);

        // Langkah D: Jika semua berhasil, simpan perubahan
        mysqli_commit($koneksi);

        // Umpan balik sukses
        echo "<script>
                alert('Data kelas baru dan jadwal pertamanya berhasil ditambahkan!');
                document.location.href = 'kelas.php';
              </script>";

    } catch (Exception $e) {
        // Langkah E: Jika ada error, batalkan semua perubahan
        mysqli_rollback($koneksi);

        // Umpan balik gagal
        echo "<script>
                alert('Gagal menambahkan data. Terjadi error: " . addslashes($e->getMessage()) . "');
                document.location.href = 'add-kelas.php';
              </script>";
    }

} 
// 3. Logika untuk "UPDATE DATA" (Edit Data Utama) - TIDAK BERUBAH
else if (isset($_POST['update_data'])) {

    // Ambil dan bersihkan data
    $id_kelas   = (int)$_POST['id_kelas_bimbel'];
    $nama_kelas = mysqli_real_escape_string($koneksi, $_POST['nama_kelas_bimbel']);
    $id_program = (int)$_POST['id_program'];
    $id_tingkat = (int)$_POST['id_tingkat'];

    // Kueri SQL untuk UPDATE Data Utama
    $query = "UPDATE tbl_kelas_bimbel SET 
                nama_kelas_bimbel = '$nama_kelas',
                id_program = $id_program,
                id_tingkat = $id_tingkat
              WHERE id_kelas_bimbel = $id_kelas";
    
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
                alert('Data kelas berhasil diperbarui!');
                document.location.href = 'kelas.php';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal memperbarui data kelas. Error: " . addslashes($error_msg) . "');
                document.location.href = 'edit-kelas.php?id_kelas_bimbel=" . $id_kelas . "';
              </script>";
    }

}
// 4. Logika untuk "TAMBAH JADWAL" (Menambah jadwal baru ke kelas yang sudah ada)
else if (isset($_POST['tambah_jadwal'])) {

    // Ambil dan bersihkan data dari form di ubah-jadwal.php
    $id_kelas    = (int)$_POST['id_kelas_bimbel'];
    $hari        = mysqli_real_escape_string($koneksi, $_POST['hari']);
    $jam_mulai   = mysqli_real_escape_string($koneksi, $_POST['jam_mulai']);
    $jam_selesai = mysqli_real_escape_string($koneksi, $_POST['jam_selesai']);

    // Kueri SQL untuk INSERT JADWAL BARU
    $query = "INSERT INTO tbl_jadwal_kelas (id_kelas_bimbel, hari, jam_mulai, jam_selesai) 
              VALUES ($id_kelas, '$hari', '$jam_mulai', '$jam_selesai')";
    
    $result = mysqli_query($koneksi, $query);

    if ($result) {
        echo "<script>
                alert('Jadwal baru berhasil ditambahkan!');
                // Arahkan kembali ke halaman kelola jadwal
                document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal menambahkan jadwal. Error: " . addslashes($error_msg) . "');
                document.location.href = 'ubah-jadwal.php?id_kelas_bimbel=" . $id_kelas . "';
              </script>";
    }
}

// 5. Jika diakses tanpa tombol
else {
    header("location: kelas.php");
    exit();
}
?>