<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

if (!isset($koneksi) && isset($conn)) {
    $koneksi = $conn;
}

$title = "Riwayat Feedback - Ruang Les";
require_once "../template/header_ortu.php";
require_once "../template/navbar_ortu.php";
require_once "../template/sidebar_ortu.php";

// Menangani pesan sukses kirim
$alert = "";
if (isset($_GET['msg']) && $_GET['msg'] == 'sent') {
    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fa-solid fa-check-circle me-2"></i> Feedback berhasil dikirim! Silakan tunggu balasan dari kami.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
}

$id_user = $_SESSION["ssId"];
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Riwayat Feedback</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_orangtua.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="feedback.php">Kirim Feedback</a></li>
                <li class="breadcrumb-item active">Riwayat</li>
            </ol>

            <?= $alert ?>

            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="h5 my-2"><i class="fa-solid fa-history me-1" style="padding-top: 10px;"></i> History Pesan Anda</span>
                        </div>
                        <a href="feedback.php" class="btn btn-primary"><i class="fa-solid fa-plus"></i> Buat Baru</a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-hover table-bordered" id="datatablesSimple">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Tanggal</th>
                                <th width="20%">Kepada</th>
                                <th>Isi Pesan</th>
                                <th width="15%">Status</th>
                                <th width="10%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Ambil feedback HANYA milik user yang sedang login
                            $query = "SELECT * FROM tbl_feedback WHERE id_orgtua_wali = '$id_user' ORDER BY tgl_feedback DESC";
                            $result = mysqli_query($koneksi, $query);

                            while ($data = mysqli_fetch_assoc($result)) {
                                // Format Tanggal
                                $tgl = ($data['tgl_feedback']) ? date('d-m-Y', strtotime($data['tgl_feedback'])) : '-';
                                
                                // Potong pesan panjang
                                $isi = substr($data['isi_feedback'], 0, 50) . '...';

                                // Cek Status Balasan
                                if (!empty($data['balasan'])) {
                                    $status = '<span class="badge bg-success"><i class="fa-solid fa-check"></i> Dibalas</span>';
                                    $btnClass = 'btn-success';
                                    $btnText = 'Lihat Balasan';
                                } else {
                                    $status = '<span class="badge bg-warning text-white"><i class="fa-solid fa-clock"></i> Belum Dibalas</span>';
                                    $btnClass = 'btn-secondary';
                                    $btnText = 'Detail';
                                }
                            ?>
                                <tr>
                                    <td align="center"><?= $no++ ?></td>
                                    <td><?= $tgl ?></td>
                                    <td><?= $data['nama_tujuan'] ?></td>
                                    <td><?= $isi ?></td>
                                    <td align="center"><?= $status ?></td>
                                    <td align="center">
                                        <button type="button" class="btn <?= $btnClass ?> btn-sm" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $data['id_feedback'] ?>">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>

                                        <div class="modal fade" id="modalDetail<?= $data['id_feedback'] ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Detail Feedback</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <div class="mb-3">
                                                            <label class="fw-bold text-muted small">Dikirim Kepada:</label>
                                                            <div><?= $data['nama_tujuan'] ?></div>
                                                            <div class="text-muted small"><?= $tgl ?></div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="fw-bold text-muted small">Pesan Anda:</label>
                                                            <div class="p-2 bg-light border rounded"><?= nl2br($data['isi_feedback']) ?></div>
                                                        </div>
                                                        
                                                        <hr>
                                                        
                                                        <div class="mb-3">
                                                            <label class="fw-bold text-muted small">Balasan:</label>
                                                            <?php if(!empty($data['balasan'])): ?>
                                                                <div class="alert alert-success mb-0">
                                                                    <?= nl2br($data['balasan']) ?>
                                                                    <div class="mt-2 text-end small text-muted fst-italic">
                                                                        <?= date('d M Y H:i', strtotime($data['tgl_balasan'])) ?>
                                                                    </div>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="alert alert-warning mb-0">
                                                                    Belum ada balasan dari pihak Ruangles.
                                                                </div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Tutup</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

<?php require_once "../template/footer_ortu.php"; ?>
</div>