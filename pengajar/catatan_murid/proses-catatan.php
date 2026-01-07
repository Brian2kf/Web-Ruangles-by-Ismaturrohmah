<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// PROSES SIMPAN DATA (CREATE)
// Cek apakah tombol 'simpan' (dari add-catatan.php) telah ditekan
if (isset($_POST['simpan'])) {
    
    // 1. Ambil semua data dari formulir
    $id_kelas       = (int)$_POST['id_kelas'];
    $id_tingkat     = (int)$_POST['id_tingkat'];
    $id_murid       = (int)$_POST['id_murid'];
    $id_pengajar    = (int)$_POST['id_pengajar'];
    
    $mata_pelajaran = mysqli_real_escape_string($koneksi, $_POST['mata_pelajaran']);
    $materi         = mysqli_real_escape_string($koneksi, $_POST['materi']);
    $isi_progres    = mysqli_real_escape_string($koneksi, $_POST['isi_progres']);

    // 2. Validasi
    if (empty($id_murid) || empty($id_pengajar)) {
        echo "<script>
                alert('Murid dan Pengajar wajib dipilih.');
                window.history.back();
              </script>";
        exit();
    }

    // 3. Buat query INSERT (Logika ini sudah ada sebelumnya)
    $query = "INSERT INTO tbl_progres 
                  (id_kelas_bimbel, id_tingkat, id_pengajar, id_murid, mata_pelajaran, materi, isi_progres)
              VALUES 
                  ($id_kelas, $id_tingkat, $id_pengajar, $id_murid, '$mata_pelajaran', '$materi', '$isi_progres')";

    $result = mysqli_query($koneksi, $query);

    // 5. Cek hasil eksekusi dan redirect
    if ($result) {
        echo "<script>
                alert('Data catatan baru berhasil disimpan.');
                document.location.href = 'detail-catatan.php?id_kelas=$id_kelas';
              </script>";
    } else {
         echo "<script>
                alert('Gagal menyimpan data catatan: " . mysqli_error($koneksi) . "');
                document.location.href = 'detail-catatan.php?id_kelas=$id_kelas';
              </script>";
    }
}

// PROSES UPDATE DATA (UPDATE)
// Cek apakah tombol 'update' (dari edit-catatan.php) telah ditekan
else if (isset($_POST['update'])) {
    
    // 1. Ambil semua data dari formulir
    $id_progres     = (int)$_POST['id_progres']; // ID Kunci untuk WHERE
    $id_kelas       = (int)$_POST['id_kelas'];   // Untuk redirect
    $id_tingkat     = (int)$_POST['id_tingkat']; // Data yang akan diupdate
    $id_murid       = (int)$_POST['id_murid'];   // Data yang akan diupdate
    $id_pengajar    = (int)$_POST['id_pengajar'];// Data yang akan diupdate
    
    $mata_pelajaran = mysqli_real_escape_string($koneksi, $_POST['mata_pelajaran']);
    $materi         = mysqli_real_escape_string($koneksi, $_POST['materi']);
    $isi_progres    = mysqli_real_escape_string($koneksi, $_POST['isi_progres']);

    // 2. Validasi
    if (empty($id_murid) || empty($id_pengajar)) {
        echo "<script>
                alert('Murid dan Pengajar wajib dipilih.');
                window.history.back();
              </script>";
        exit();
    }

    // 3. Buat query UPDATE
    $query_update = "UPDATE tbl_progres SET 
                        id_tingkat     = $id_tingkat,
                        id_pengajar    = $id_pengajar,
                        id_murid       = $id_murid,
                        mata_pelajaran = '$mata_pelajaran',
                        materi         = '$materi',
                        isi_progres    = '$isi_progres'
                     WHERE 
                        id_progres = $id_progres";

    // 4. Eksekusi query
    $result_update = mysqli_query($koneksi, $query_update);

    // 5. Cek hasil eksekusi dan redirect
    if ($result_update) {
        echo "<script>
                alert('Data catatan berhasil diperbarui.');
                document.location.href = 'detail-catatan.php?id_kelas=$id_kelas';
              </script>";
    } else {
         echo "<script>
                alert('Gagal memperbarui data: " . mysqli_error($koneksi) . "');
                document.location.href = 'detail-catatan.php?id_kelas=$id_kelas';
              </script>";
    }
}

// Jika tidak ada 'simpan' atau 'update', redirect ke halaman utama
else {
    header("location: catatan_murid.php");
    exit();
}
?>