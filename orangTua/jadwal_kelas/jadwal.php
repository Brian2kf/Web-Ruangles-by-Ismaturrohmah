<?php
session_start();

// 1. Cek Session Login & Role Orang Tua
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// Tangani variabel koneksi
if (!isset($koneksi) && isset($conn)) {
    $koneksi = $conn;
}

// 2. Judul Halaman
$title = "Jadwal Kelas Anak - Ruang Les";

// 3. Include Template
require_once "../template/header_ortu.php";
require_once "../template/navbar_ortu.php";
require_once "../template/sidebar_ortu.php";

// 4. LOGIKA PILIH ANAK
$id_ortu = $_SESSION["ssId"];

// Ambil daftar anak
$query_anak = mysqli_query($koneksi, "SELECT * FROM tbl_data_murid WHERE id_user_ortu = '$id_ortu'");
$daftar_anak = [];
while ($row = mysqli_fetch_assoc($query_anak)) {
    $daftar_anak[] = $row;
}

// Validasi jika anak kosong
if (empty($daftar_anak)) {
    echo "<script>alert('Data anak tidak ditemukan.'); window.location='../dashboard_orangtua.php';</script>";
    exit;
}

// Tentukan Anak Aktif & Ambil Info Kelasnya
if (isset($_GET['id_murid'])) {
    $id_murid_aktif = $_GET['id_murid'];
    $is_valid = false;
    foreach ($daftar_anak as $anak) {
        if ($anak['id_murid'] == $id_murid_aktif) {
            $is_valid = true;
            $nama_anak_aktif = $anak['nama_murid'];
            $id_kelas_anak = $anak['id_kelas_bimbel']; // Kita butuh ini untuk query jadwal
            break;
        }
    }
    if (!$is_valid) {
        $id_murid_aktif = $daftar_anak[0]['id_murid'];
        $nama_anak_aktif = $daftar_anak[0]['nama_murid'];
        $id_kelas_anak = $daftar_anak[0]['id_kelas_bimbel'];
    }
} else {
    $id_murid_aktif = $daftar_anak[0]['id_murid'];
    $nama_anak_aktif = $daftar_anak[0]['nama_murid'];
    $id_kelas_anak = $daftar_anak[0]['id_kelas_bimbel'];
}

// Ambil Nama Kelas untuk Judul (Opsional, agar lebih informatif)
$nama_kelas_label = "Belum Terdaftar di Kelas";
if ($id_kelas_anak) {
    $qKelas = mysqli_query($koneksi, "SELECT nama_kelas_bimbel FROM tbl_kelas_bimbel WHERE id_kelas_bimbel = '$id_kelas_anak'");
    $dKelas = mysqli_fetch_assoc($qKelas);
    if ($dKelas) {
        $nama_kelas_label = $dKelas['nama_kelas_bimbel'];
    }
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Jadwal Pertemuan</h1>
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_orangtua.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Jadwal</li>
            </ol>
            
            <div class="card mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="h5 my-2 m-0">
                            <i class="fa-regular fa-calendar-days me-2" style="padding-top: 10px;"></i> Jadwal: <strong><?= $nama_anak_aktif; ?></strong>
                        </span>
                        <small class="text-muted">Kelas: <?= $nama_kelas_label; ?></small>
                    </div>
                    
                    <?php if(count($daftar_anak) > 1): ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" id="dropdownJadwal" data-bs-toggle="dropdown" aria-expanded="false">
                            Ganti Anak
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownJadwal">
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
                    <?php if ($id_kelas_anak): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="datatablesSimple" width="100%" cellspacing="0">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%" class="text-center">No</th>
                                        <th width="20%">Hari</th>
                                        <th width="20%" class="text-center">Jam Mulai</th>
                                        <th width="20%" class="text-center">Jam Selesai</th>
                                        <th width="35%">Durasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    // Menggunakan FIELD untuk mengurutkan hari (Senin s/d Minggu)
                                    $queryJadwal = mysqli_query($koneksi, "
                                        SELECT * FROM tbl_jadwal_kelas 
                                        WHERE id_kelas_bimbel = '$id_kelas_anak'
                                        ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'), jam_mulai ASC
                                    ");
                                    
                                    if(mysqli_num_rows($queryJadwal) > 0) {
                                        while ($data = mysqli_fetch_array($queryJadwal)) { 
                                            // Hitung Durasi (Opsional)
                                            $start = strtotime($data['jam_mulai']);
                                            $end = strtotime($data['jam_selesai']);
                                            $diff = $end - $start;
                                            $jam = floor($diff / (60 * 60));
                                            $menit = $diff - $jam * (60 * 60);
                                            $durasi = $jam . ' Jam ' . floor($menit / 60) . ' Menit';
                                    ?>
                                    <tr>
                                        <td align="center"><?= $no++ ?></td>
                                        <td class="fw-bold"><?= $data['hari'] ?></td>
                                        <td align="center"><?= date('H:i', strtotime($data['jam_mulai'])) ?> WIB</td> 
                                        <td align="center"><?= date('H:i', strtotime($data['jam_selesai'])) ?> WIB</td>
                                        <td><i class="fa-regular fa-clock me-1 text-muted"></i> <?= $durasi; ?></td>
                                    </tr>
                                    <?php 
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center text-danger">Tidak ada jadwal yang ditemukan untuk kelas ini.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning text-center" role="alert">
                            <h4 class="alert-heading"><i class="fa-solid fa-circle-exclamation"></i> Belum Ada Kelas</h4>
                            <p>Anak Anda (<strong><?= $nama_anak_aktif; ?></strong>) belum terdaftar dalam kelas bimbel manapun.</p>
                            <hr>
                            <p class="mb-0">Silakan hubungi Admin untuk konfirmasi pembagian kelas.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

<?php require_once "../template/footer_ortu.php"; ?>
</div>