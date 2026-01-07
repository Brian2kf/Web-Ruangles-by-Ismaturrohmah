<?php
session_start();
// Pastikan path config benar
include '../config.php'; 

// ============================================================
// PERBAIKAN 1: SESSION CHECKER (SESUAIKAN DENGAN PROSESLOGIN.PHP)
// ============================================================
// Cek ssLogin, ssRole = 1 (Ortu), dan ssId
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data dari Session yang benar
$id_ortu = $_SESSION["ssId"];      // Sesuai proseslogin.php
$nama_ortu = $_SESSION["ssNama"];  // Sesuai proseslogin.php

// ============================================================
// PERBAIKAN 2: VARIABEL KONEKSI ($koneksi)
// ============================================================
// Pastikan variabel database konsisten. Di proseslogin anda pakai $koneksi.
// Jika di config.php variabelnya $conn, ubah baris bawah ini jadi: $koneksi = $conn;
if (!isset($koneksi)) {
    // Fallback jika nama variabel di config berbeda
    if (isset($conn)) {
        $koneksi = $conn;
    } else {
        die("Variabel koneksi database tidak ditemukan. Cek config.php");
    }
}

// ---------------------------------------------------------
// LOGIKA DATA DASHBOARD
// ---------------------------------------------------------

// 1. Logika Pilih Anak (Multi-Murid)
// Ambil semua anak dari ortu ini (Gunakan $koneksi)
$query_anak = mysqli_query($koneksi, "SELECT * FROM tbl_data_murid WHERE id_user_ortu = '$id_ortu'");

// Cek error query
if (!$query_anak) {
    die("Error Query Anak: " . mysqli_error($koneksi));
}

$daftar_anak = [];
while ($row = mysqli_fetch_assoc($query_anak)) {
    $daftar_anak[] = $row;
}

// Jika tidak ada data anak
if (empty($daftar_anak)) {
    // Tampilkan pesan error yang lebih rapi
    echo '<div style="padding: 20px; font-family: sans-serif;">
            <h3>Data anak tidak ditemukan.</h3>
            <p>Akun Anda terdaftar sebagai Orang Tua, namun belum ada data murid yang terhubung dengan akun ini.</p>
            <p>Silakan hubungi Admin untuk menghubungkan data Anak ke Akun Anda (ID User: '.$id_ortu.').</p>
            <a href="../auth/logout.php">Logout</a>
          </div>';
    exit;
}

// Tentukan Anak Aktif (dari Sesi atau Default anak pertama)
if (isset($_GET['id_murid'])) {
    $_SESSION['id_murid_aktif'] = $_GET['id_murid'];
}

// Jika belum ada session anak aktif, atau session anak aktif tidak valid (milik user lain), reset ke anak pertama
$id_anak_valid = false;
if (isset($_SESSION['id_murid_aktif'])) {
    foreach ($daftar_anak as $anak) {
        if ($anak['id_murid'] == $_SESSION['id_murid_aktif']) {
            $id_anak_valid = true;
            break;
        }
    }
}

if (!$id_anak_valid) {
    $_SESSION['id_murid_aktif'] = $daftar_anak[0]['id_murid'];
}

$id_murid_aktif = $_SESSION['id_murid_aktif'];

// Ambil detail anak aktif
$detail_anak = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tbl_data_murid WHERE id_murid = '$id_murid_aktif'"));
$nama_anak_aktif = $detail_anak['nama_murid'];
$id_kelas_aktif = $detail_anak['id_kelas_bimbel'];

// A. Jadwal Kelas
$query_jadwal = mysqli_query($koneksi, "SELECT * FROM tbl_jadwal_kelas WHERE id_kelas_bimbel = '$id_kelas_aktif'");

// B. Pembayaran Terakhir & Cek Tagihan
$query_bayar = mysqli_query($koneksi, "SELECT * FROM tbl_pembayaran WHERE id_murid = '$id_murid_aktif' ORDER BY id_pembayaran DESC LIMIT 1");
$data_bayar = mysqli_fetch_assoc($query_bayar);

// Logika Peringatan Bayar
$show_warning_bayar = false;
$sisa_sesi = 0;
$status_bayar = 'Belum Ada Data';

if ($data_bayar) {
    $sisa_sesi = $data_bayar['sesi_tersisa'];
    $status_bayar = $data_bayar['status_pembayaran'];
    
    if ($sisa_sesi <= 2) {
        $show_warning_bayar = true;
    }
} else {
    $show_warning_bayar = true;
}

// C. Progres Belajar (Ambil 3 Terbaru)
$query_progres = mysqli_query($koneksi, "SELECT * FROM tbl_progres WHERE id_murid = '$id_murid_aktif' ORDER BY id_progres DESC LIMIT 3");

$title = "Dashboard Orang Tua - Ruang Les by Ismaturrohmah";

// Include Template
include 'template/header_ortu.php'; 
include 'template/navbar_ortu.php'; 
include 'template/sidebar_ortu.php'; 
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="mt-4">Dashboard</h1>
                    <p class="text-muted">Selamat Datang, <?= $nama_ortu; ?></p>
                </div>
                
                <?php if(count($daftar_anak) > 1): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownAnak" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-graduate me-2"></i> <?= $nama_anak_aktif; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownAnak">
                        <?php foreach($daftar_anak as $anak): ?>
                            <li><a class="dropdown-item" href="?id_murid=<?= $anak['id_murid']; ?>">
                                <?= $anak['nama_murid']; ?>
                            </a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php else: ?>
                    <button class="btn btn-primary" disabled><i class="fas fa-user-graduate me-2"></i> <?= $nama_anak_aktif; ?></button>
                <?php endif; ?>
            </div>

            <div class="row">
                
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border-1 shadow-sm h-100 py-2">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h5 class="font-weight-bold text-dark text-uppercase mb-1">Jadwal Kelas</h5>
                                <hr class="mx-auto" style="width: 50px; border-top: 3px solid #000;">
                            </div>
                            
                            <?php if(mysqli_num_rows($query_jadwal) > 0): ?>
                                <?php while($jadwal = mysqli_fetch_assoc($query_jadwal)): ?>
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-end fw-bold">Hari :</div>
                                        <div class="col-8 text-start"><?= $jadwal['hari']; ?></div>
                                    </div>
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-4 text-end fw-bold">Waktu :</div>
                                        <div class="col-8 text-start">
                                            Jam <?= date('H.i', strtotime($jadwal['jam_mulai'])); ?> 
                                            sampai <?= date('H.i', strtotime($jadwal['jam_selesai'])); ?>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="text-center text-muted">Belum ada jadwal kelas.</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border-1 shadow-sm h-100 py-2">
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            
                            <?php if ($show_warning_bayar): ?>
                                <div class="mb-3">
                                    <i class="fas fa-exclamation-triangle text-warning fa-2x mb-2"></i>
                                    <h5 class="font-weight-bold text-dark">
                                        <?= ($sisa_sesi == 0) ? "Sesi Habis!" : "Sisa Sesi: " . $sisa_sesi; ?>
                                    </h5>
                                    <p class="card-text text-danger mb-1">Mohon segera lakukan pembayaran.</p>
                                </div>
                            <?php else: ?>
                                <div class="mb-3">
                                    <h5 class="font-weight-bold text-dark">Sisa Sesi: <?= $sisa_sesi; ?></h5>
                                    <p class="text-muted">Status Pembayaran: <?= $status_bayar; ?></span></p>
                                </div>
                            <?php endif; ?>

                            <div class="mt-3">
                                <p class="mb-2 text-dark fw-bold">Status: <?= $status_bayar; ?></p>
                                <a href="pembayaran/pembayaran.php?id_murid=<?= $id_murid_aktif; ?>" 
                                    class="btn btn-primary btn-sm px-4">
                                    <i class="fa-solid fa-file-invoice-dollar me-1"></i> Lihat Daftar Pembayaran
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 mb-4">
                    <div class="card border-1 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title font-weight-bold text-dark mb-4">Progres Anak</h5>
                            
                            <?php if(mysqli_num_rows($query_progres) > 0): ?>
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle">
                                        <?php while($prog = mysqli_fetch_assoc($query_progres)): ?>
                                        <tr class="border-bottom">
                                            <td style="width: 20%;" class="fw-bold text-dark">Mata Pelajaran</td>
                                            <td style="width: 5%;">:</td>
                                            <td class="text-dark"><?= $prog['mata_pelajaran']; ?></td>
                                        </tr>
                                        <tr class="border-bottom">
                                            <td class="fw-bold text-dark">Materi</td>
                                            <td>:</td>
                                            <td class="text-dark"><?= $prog['materi']; ?></td>
                                        </tr>
                                        <tr class="mb-4">
                                            <td class="fw-bold text-dark align-top">Catatan</td>
                                            <td class="align-top">:</td>
                                            <td class="text-dark fst-italic"><?= $prog['isi_progres']; ?></td>
                                        </tr>
                                        <tr><td colspan="3" class="py-3"></td></tr>
                                        <?php endwhile; ?>
                                    </table>
                                </div>
                            <?php else: ?>
                                <p class="text-muted text-center">Belum ada catatan progres belajar.</p>
                            <?php endif; ?>
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

<?php include 'template/footer_ortu.php'; ?>