<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// Fallback koneksi
if (!isset($koneksi) && isset($conn)) {
    $koneksi = $conn;
}

$title = "Kirim Feedback - Ruang Les";
require_once "../template/header_ortu.php";
require_once "../template/navbar_ortu.php";
require_once "../template/sidebar_ortu.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Kirim Feedback</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_orangtua.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Tulis Pesan</li>
            </ol>

            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <span class="h5 my-2"><i class="fa-solid fa-pen-to-square me-2" style="padding-top: 10px;"></i> Form Kritik & Saran</span>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">Silakan tuliskan masukan, pertanyaan, atau kendala Anda kepada Admin atau Pengajar.</p>
                            
                            <form action="proses_feedback.php" method="POST">
                                <div class="mb-3">
                                    <label for="tujuan" class="form-label fw-bold">Tujuan Pesan:</label>
                                    <select class="form-select" name="tujuan" id="tujuan" required>
                                        <option value="" selected disabled>-- Pilih Penerima --</option>
                                        <option value="Admin Ruangles">Admin Ruangles</option>
                                        
                                        <?php
                                        $queryPengajar = mysqli_query($koneksi, "SELECT nama_pengajar FROM tbl_data_pengajar");
                                        while($p = mysqli_fetch_assoc($queryPengajar)){
                                            echo '<option value="'.$p['nama_pengajar'].'">Pengajar: '.$p['nama_pengajar'].'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="isi_feedback" class="form-label fw-bold">Isi Pesan:</label>
                                    <textarea class="form-control" name="isi_feedback" id="isi_feedback" rows="6" placeholder="Tuliskan pesan Anda disini secara detail..." required></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" name="kirim_feedback" class="btn btn-primary">
                                        <i class="fa-solid fa-paper-plane me-2"></i> Kirim Feedback
                                    </button>
                                    
                                    <a href="riwayat_feedback.php" class="btn btn-outline-secondary">
                                        <i class="fa-solid fa-clock-rotate-left me-2"></i> Lihat Riwayat Feedback
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php require_once "../template/footer_ortu.php"; ?>
</div>