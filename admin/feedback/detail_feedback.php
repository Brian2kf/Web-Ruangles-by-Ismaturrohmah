<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Detail Feedback - Ruang Les";

require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

$id_feedback = $_GET['id'];

// QUERY DISESUAIKAN DENGAN DATABASE ANDA
// Join f.id_orgtua_wali ke u.id
$query = "SELECT f.*, u.nama_user 
          FROM tbl_feedback f 
          JOIN tbl_user u ON f.id_orgtua_wali = u.id 
          WHERE f.id_feedback = $id_feedback";
$exec = mysqli_query($koneksi, $query);
$data = mysqli_fetch_array($exec);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='feedback.php';</script>";
    exit;
}

// Handle tanggal jika data lama (sebelum kolom tgl dibuat)
$tgl_feedback = ($data['tgl_feedback']) ? date('d M Y', strtotime($data['tgl_feedback'])) : 'Tidak tercatat';
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Detail Pesan</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="feedback.php">Feedback</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <i class="fa-solid fa-user"></i> Pesan dari <strong><?= $data['nama_user'] ?></strong>
                            <span class="float-end text-muted small"><?= $tgl_feedback ?></span>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="small mb-1 fw-bold">Ditujukan Kepada:</label>
                                <p><?= $data['nama_tujuan'] ?></p>
                            </div>
                            <div class="mb-3">
                                <label class="small mb-1 fw-bold">Isi Feedback:</label>
                                <div class="alert alert-secondary" role="alert">
                                    <?= nl2br($data['isi_feedback']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fa-solid fa-reply"></i> Balasan Anda
                        </div>
                        <div class="card-body">
                            <form action="proses_feedback.php" method="POST">
                                <input type="hidden" name="id_feedback" value="<?= $data['id_feedback'] ?>">
                                
                                <div class="mb-3">
                                    <label for="balasan" class="form-label fw-bold">Tulis Balasan:</label>
                                    <textarea class="form-control" id="balasan" name="balasan" rows="6" placeholder="Tulis balasan untuk orang tua di sini..." required><?= $data['balasan'] ?></textarea>
                                    <div class="form-text">Balasan ini akan dapat dilihat oleh orang tua di dashboard mereka.</div>
                                </div>
                                
                                <div class="d-grid gap-2">
                                    <button type="submit" name="kirim_balasan" class="btn btn-primary">
                                        <i class="fa-solid fa-paper-plane"></i> Kirim Balasan
                                    </button>
                                    <a href="feedback.php" class="btn btn-secondary">Kembali</a>
                                </div>
                            </form>
                        </div>
                        <?php if(!empty($data['tgl_balasan'])): ?>
                        <div class="card-footer text-muted small">
                            Terakhir dibalas pada: <?= date('d M Y H:i', strtotime($data['tgl_balasan'])) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php
require_once "../template/footer_admin.php";
?>