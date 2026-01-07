<?php
session_start();

// 1. Cek Session Login & Role Orang Tua
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// Tangani variabel koneksi (fallback jika beda nama variabel di config)
if (!isset($koneksi) && isset($conn)) {
    $koneksi = $conn;
}

// 2. Mengatur Judul Halaman
$title = "Progres Belajar Anak - Ruang Les";

// 3. Include Template (Path naik satu level dari folder catatan_murid)
require_once "../template/header_ortu.php";
require_once "../template/navbar_ortu.php";
require_once "../template/sidebar_ortu.php";

// 4. LOGIKA PILIH ANAK (Sama seperti Dashboard)
$id_ortu = $_SESSION["ssId"];

// Ambil daftar anak milik orang tua ini
$query_anak = mysqli_query($koneksi, "SELECT * FROM tbl_data_murid WHERE id_user_ortu = '$id_ortu'");
$daftar_anak = [];
while ($row = mysqli_fetch_assoc($query_anak)) {
    $daftar_anak[] = $row;
}

// Jika tidak ada data anak
if (empty($daftar_anak)) {
    echo "<script>alert('Data anak tidak ditemukan. Hubungi Admin.'); window.location='../dashboard_orangtua.php';</script>";
    exit;
}

// Tentukan ID Anak yang Aktif (Bisa dari GET param atau Default anak pertama)
if (isset($_GET['id_murid'])) {
    $id_murid_aktif = $_GET['id_murid'];
    // Validasi apakah id_murid ini benar milik ortu tersebut (mencegah ganti ID manual di URL)
    $is_valid = false;
    foreach ($daftar_anak as $anak) {
        if ($anak['id_murid'] == $id_murid_aktif) {
            $is_valid = true;
            $nama_anak_aktif = $anak['nama_murid'];
            break;
        }
    }
    if (!$is_valid) {
        $id_murid_aktif = $daftar_anak[0]['id_murid'];
        $nama_anak_aktif = $daftar_anak[0]['nama_murid'];
    }
} else {
    // Default anak pertama
    $id_murid_aktif = $daftar_anak[0]['id_murid'];
    $nama_anak_aktif = $daftar_anak[0]['nama_murid'];
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Progres Belajar</h1>
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_orangtua.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Progres Belajar</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="h5 my-2"><i class="fa-solid fa-book-open me-1" style="padding-top: 10px;"></i> Data Catatan Belajar: <strong><?= $nama_anak_aktif; ?></strong></span>
                    </div>
                    
                    <?php if(count($daftar_anak) > 1): ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Ganti Anak
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton1">
                            <?php foreach($daftar_anak as $anak): ?>
                                <li>
                                    <a class="dropdown-item <?= ($anak['id_murid'] == $id_murid_aktif) ? 'active' : ''; ?>" 
                                       href="?id_murid=<?= $anak['id_murid']; ?>">
                                       <?= $anak['nama_murid']; ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <table class="table table-hover table-striped table-bordered" id="datatablesSimple">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Mata Pelajaran</th>
                                <th>Materi Dipelajari</th>
                                <th>Catatan Perkembangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Query mengambil data progres berdasarkan ID Murid yang aktif
                            $queryProgres = mysqli_query($koneksi, "
                                SELECT * FROM tbl_progres 
                                WHERE id_murid = '$id_murid_aktif' 
                                ORDER BY id_progres DESC
                            ");
                            
                            // Cek apakah ada data
                            if(mysqli_num_rows($queryProgres) > 0) {
                                while ($data = mysqli_fetch_array($queryProgres)) { 
                            ?>
                            <tr>
                                <td align="center"><?= $no++ ?></td>
                                <td><?= $data['mata_pelajaran'] ?></td>
                                <td><?= $data['materi'] ?></td> 
                                <td><?= $data['isi_progres'] ?></td>
                            </tr>
                            <?php 
                                } 
                            }
                            ?>
                        </tbody>
                    </table>
                    
                    <?php if(mysqli_num_rows($queryProgres) == 0): ?>
                        <div class="alert alert-info mt-3 mb-0 text-center">
                            Belum ada catatan progres untuk <?= $nama_anak_aktif; ?>.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php require_once "../template/footer_ortu.php"; ?>
</div>