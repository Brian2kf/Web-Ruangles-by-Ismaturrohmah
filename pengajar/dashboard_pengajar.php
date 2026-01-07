<?php
session_start();

// Cek sesi login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../auth/login.php");
    exit();
}

require_once "../config.php";
$title = "Dashboard Pengajar - Ruang Les by Ismaturrohmah";
require_once "template/header_pengajar.php";
require_once "template/navbar_pengajar.php";
require_once "template/sidebar_pengajar.php";

// ==========================================
// 1. AMBIL DATA PENGAJAR YANG LOGIN
// ==========================================
// Kita asumsikan saat login, username disimpan di $_SESSION['ssUser']
// Kita perlu nama lengkap untuk mencocokkan dengan 'nama_tujuan' di tabel feedback
$username = $_SESSION["ssUser"];
$queryUser = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$username'");
$userData = mysqli_fetch_assoc($queryUser);
$nama_pengajar = $userData['nama_user']; // Nama lengkap pengajar (misal: Brian Jonathan)

// ==========================================
// 2. HITUNG DATA (REALTIME DATABASE)
// ==========================================

// Hitung Total Kelas (Semua kelas aktif)
$queryKelas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_kelas_bimbel");
$dataKelas = mysqli_fetch_assoc($queryKelas);
$totalKelas = $dataKelas['total'];

// Hitung Total Materi
$queryMateri = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_materi");
$dataMateri = mysqli_fetch_assoc($queryMateri);
$totalMateri = $dataMateri['total'];

// ==========================================
// 3. AMBIL FEEDBACK KHUSUS PENGAJAR INI
// ==========================================
// Mengambil feedback dimana nama_tujuan = nama pengajar yang login
$queryFeedback = "SELECT f.isi_feedback, f.tgl_feedback, u.nama_user as nama_ortu
                  FROM tbl_feedback f
                  JOIN tbl_user u ON f.id_orgtua_wali = u.id
                  WHERE f.nama_tujuan = '$nama_pengajar'
                  ORDER BY f.id_feedback DESC";
$resultFeedback = mysqli_query($koneksi, $queryFeedback);
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Dashboard</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item text-muted">Home</li>
            </ol>

            <div class="row justify-content-center my-4">
                
                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border rounded-3 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                            <h4 class="fw-normal">Total Kelas</h4>
                            <div class="display-1 fw-bold mt-2"><?= $totalKelas ?></div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6 mb-4">
                    <div class="card border rounded-3 shadow-sm">
                        <div class="card-body d-flex flex-column justify-content-center align-items-center py-5">
                            <h4 class="fw-normal">Total Materi</h4>
                            <div class="display-1 fw-bold mt-2"><?= $totalMateri ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-5">
                <div class="col-xl-12">
                    <div class="card border rounded-3 shadow-sm">
                        <div class="card-header bg-white border-0 pt-3 ps-4">
                            <h4 class="fw-bold">Feedback Orang Tua</h4>
                        </div>
                        <div class="card-body ps-4 pe-4 pb-4">
                            
                            <?php 
                            if (mysqli_num_rows($resultFeedback) > 0) {
                                while ($fb = mysqli_fetch_assoc($resultFeedback)) { 
                            ?>
                                <div class="p-3 mb-3" style="background-color: #e0e0e0; border-radius: 5px;">
                                    <div class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                        <?= $fb['nama_ortu'] ?> 
                                        <span class="text-muted fw-normal" style="font-size: 0.8rem;">
                                            - <?= date('d M Y', strtotime($fb['tgl_feedback'])) ?>
                                        </span>
                                    </div>
                                    <div class="text-dark small">
                                        <?= nl2br($fb['isi_feedback']) ?>
                                    </div>
                                </div>
                            <?php 
                                } // End While
                            } else { 
                            ?>
                                <div class="alert alert-info text-center">
                                    Belum ada feedback dari orang tua untuk Anda.
                                </div>
                            <?php } ?>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

<?php
require_once "template/footer_pengajar.php";
?>