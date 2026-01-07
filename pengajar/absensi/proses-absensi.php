<?php
session_start();
require_once "../../config.php"; // Memuat file koneksi database

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

// 2. Logika untuk "SIMPAN MASSAL"
if (isset($_POST['simpan_massal'])) {
    
    // 3. Ambil data utama dari form
    $id_kelas    = (int)$_POST['id_kelas'];
    $tgl_absensi = mysqli_real_escape_string($koneksi, $_POST['tgl_absensi']);
    
    // Pastikan 'status' adalah array dan tidak kosong
    if (isset($_POST['status']) && is_array($_POST['status']) && !empty($_POST['status'])) {
        $status_array = $_POST['status']; // Ini adalah array [id_murid => status]

        // 4. Cek Duplikasi: Periksa apakah sudah ada absensi untuk kelas ini di tanggal ini
        $queryCek = "SELECT COUNT(*) AS total FROM tbl_absensi 
                     WHERE id_kelas_bimbel = $id_kelas AND tgl_absensi = '$tgl_absensi'";
        $resultCek = mysqli_query($koneksi, $queryCek);
        $dataCek = mysqli_fetch_assoc($resultCek);

        if ($dataCek['total'] > 0) {
            // Data sudah ada, berikan error
            echo "<script>
                    alert('Gagal! Absensi untuk kelas ini pada tanggal " . date('d M Y', strtotime($tgl_absensi)) . " sudah pernah diinput.');
                    document.location.href = 'add-absensi.php?id_kelas=" . $id_kelas . "';
                  </script>";
            exit(); // Hentikan script
        }

        // 5. Mulai Transaksi Database
        mysqli_begin_transaction($koneksi);
        $berhasil = true; // Set flag default ke true

        try {
            // 6. Looping data array status murid
            foreach ($status_array as $id_murid => $status) {
                
                // Sanitasi data di dalam loop
                $id_murid_aman = (int)$id_murid;
                $status_aman = mysqli_real_escape_string($koneksi, $status);

                // 7. Buat query INSERT untuk setiap murid
                $query = "INSERT INTO tbl_absensi (id_kelas_bimbel, id_murid, tgl_absensi, status_absensi) 
                          VALUES ($id_kelas, $id_murid_aman, '$tgl_absensi', '$status_aman')";
                
                $result = mysqli_query($koneksi, $query);

                // Jika satu saja query gagal, set flag ke false dan hentikan loop
                if (!$result) {
                    $berhasil = false;
                    break; 
                }
            } // Akhir foreach loop

            // 8. Cek status flag
            if ($berhasil) {
                // Jika semua berhasil, simpan permanen (Commit)
                mysqli_commit($koneksi);
                echo "<script>
                        alert('Absensi untuk tanggal " . date('d M Y', strtotime($tgl_absensi)) . " berhasil disimpan!');
                        document.location.href = 'detail-absensi.php?id_kelas=" . $id_kelas . "';
                      </script>";
            } else {
                // Jika ada yang gagal, batalkan semua (Rollback)
                mysqli_rollback($koneksi);
                throw new Exception("Terjadi kegagalan saat menyimpan salah satu data absensi.");
            }

        } catch (Exception $e) {
            // 9. Tangkap error jika terjadi (termasuk dari rollback)
            mysqli_rollback($koneksi); // Pastikan di-rollback lagi jika error
            echo "<script>
                    alert('Gagal total menyimpan absensi. Semua data dibatalkan. Error: " . addslashes($e->getMessage()) . "');
                    document.location.href = 'add-absensi.php?id_kelas=" . $id_kelas . "';
                  </script>";
        }

    } else {
        // Jika array 'status' kosong (kemungkinan tidak ada murid di kelas)
        echo "<script>
                alert('Tidak ada data murid untuk diabsen.');
                document.location.href = 'add-absensi.php?id_kelas=" . $id_kelas . "';
              </script>";
    }

} 
// 3. Logika untuk "UPDATE" (Edit 1 Data)
else if (isset($_POST['update'])) {
    
    // 4. Ambil data dari form edit-absensi.php
    $id_absensi = (int)$_POST['id_absensi'];
    $id_kelas   = (int)$_POST['id_kelas']; // Untuk redirect
    $tgl_absensi = mysqli_real_escape_string($koneksi, $_POST['tgl_absensi']);
    $status_absensi = mysqli_real_escape_string($koneksi, $_POST['status_absensi']);

    // 5. Buat Kueri SQL untuk UPDATE
    $query = "UPDATE tbl_absensi SET 
                tgl_absensi = '$tgl_absensi',
                status_absensi = '$status_absensi'
              WHERE id_absensi = $id_absensi";

    // 6. Eksekusi Kueri
    $result = mysqli_query($koneksi, $query);

    // 7. Memberikan Umpan Balik (Feedback)
    if ($result) {
        // Jika kueri berhasil
        echo "<script>
                alert('Data absensi berhasil diperbarui!');
                document.location.href = 'detail-absensi.php?id_kelas=" . $id_kelas . "';
              </script>";
    } else {
        // Jika kueri gagal
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal memperbarui data absensi. Error: " . addslashes($error_msg) . "');
                document.location.href = 'edit-absensi.php?id_absensi=" . $id_absensi . "&id_kelas=" . $id_kelas . "';
              </script>";
    }
}

// 10. Jika file diakses secara langsung
else {
    header("location: absensi.php");
    exit();
}
?>