<?php
session_start();

// 1. Cek Session Login & Role Orang Tua
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// Tangani variabel koneksi (fallback)
if (!isset($koneksi) && isset($conn)) {
    $koneksi = $conn;
}

// 2. Mengatur Judul Halaman
$title = "Data Absensi Anak - Ruang Les";

// 3. Include Template
require_once "../template/header_ortu.php";
require_once "../template/navbar_ortu.php";
require_once "../template/sidebar_ortu.php";

// 4. LOGIKA PILIH ANAK (Konsisten dengan halaman lain)
$id_ortu = $_SESSION["ssId"];

// Ambil daftar anak
$query_anak = mysqli_query($koneksi, "SELECT * FROM tbl_data_murid WHERE id_user_ortu = '$id_ortu'");
$daftar_anak = [];
while ($row = mysqli_fetch_assoc($query_anak)) {
    $daftar_anak[] = $row;
}

// Validasi data anak
if (empty($daftar_anak)) {
    echo "<script>alert('Data anak tidak ditemukan.'); window.location='../dashboard_orangtua.php';</script>";
    exit;
}

// Tentukan Anak Aktif
if (isset($_GET['id_murid'])) {
    $id_murid_aktif = $_GET['id_murid'];
    // Validasi kepemilikan anak
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

// Fungsi Helper untuk Format Tanggal Indonesia
function tgl_indo($tanggal){
    $bulan = array (
        1 =>   'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecahkan = explode('-', $tanggal);
    // Format: Tanggal Bulan Tahun (contoh: 22 November 2025)
    return $pecahkan[2] . ' ' . $bulan[ (int)$pecahkan[1] ] . ' ' . $pecahkan[0];
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Data Absensi</h1>
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_orangtua.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Absensi</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="h5 my-2"><i class="fa-solid fa-calendar-check me-1" style="padding-top: 10px;"></i> Riwayat Kehadiran: <strong><?= $nama_anak_aktif; ?></strong></span>
                    </div>
                    
                    <?php if(count($daftar_anak) > 1): ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownAbsensi" data-bs-toggle="dropdown" aria-expanded="false">
                            Ganti Anak
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAbsensi">
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
                    <table class="table table-hover table-bordered" id="datatablesSimple">
                        <thead class="table-light">
                            <tr>
                                <th scope="col" width="5%" class="text-center">No</th>
                                <th scope="col" width="25%">Hari, Tanggal</th>
                                <th scope="col" width="20%" class="text-center">Status Kehadiran</th>
                                <th scope="col">Keterangan / Catatan</th> </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Query Absensi berdasarkan ID Murid
                            $queryAbsensi = mysqli_query($koneksi, "
                                SELECT * FROM tbl_absensi 
                                WHERE id_murid = '$id_murid_aktif' 
                                ORDER BY tgl_absensi DESC
                            ");
                            
                            while ($row = mysqli_fetch_assoc($queryAbsensi)) { 
                                // Logic warna badge status
                                $status = $row['status_absensi'];
                                $badgeClass = 'bg-secondary'; // Default
                                
                                if ($status == 'Hadir') {
                                    $badgeClass = 'bg-success';
                                } elseif ($status == 'Izin') {
                                    $badgeClass = 'bg-info text-dark';
                                } elseif ($status == 'Sakit') {
                                    $badgeClass = 'bg-warning text-dark';
                                } elseif ($status == 'Alpa') {
                                    $badgeClass = 'bg-danger';
                                }

                                // Mendapatkan nama hari
                                $namaHari = date('l', strtotime($row['tgl_absensi']));
                                $daftarHari = [
                                    'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
                                    'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
                                ];
                                $hariIndo = $daftarHari[$namaHari];
                            ?>
                            <tr>
                                <td align="center"><?= $no++ ?></td>
                                
                                <td>
                                    <?= $hariIndo . ', ' . tgl_indo($row['tgl_absensi']); ?>
                                </td>
                                
                                <td align="center">
                                    <span class="badge <?= $badgeClass; ?> py-2 px-3 rounded-pill" style="font-size: 0.9em;">
                                        <?= $status; ?>
                                    </span>
                                </td>
                                
                                <td class="text-muted">
                                    <?= ($status == 'Hadir') ? 'Mengikuti kegiatan belajar' : 'Tidak hadir di kelas'; ?>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                    <?php if(mysqli_num_rows($queryAbsensi) == 0): ?>
                        <div class="alert alert-warning mt-3 text-center">
                            Belum ada data absensi yang tercatat untuk bulan ini.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php require_once "../template/footer_ortu.php"; ?>
</div>