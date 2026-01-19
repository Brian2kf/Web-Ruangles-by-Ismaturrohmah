<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
require_once "fungsi_invoice.php";

$title = "Dashboard Pembayaran - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Hitung statistik
$total_belum_lunas = hitungInvoiceByStatus($koneksi, 'Belum Lunas');
$total_pending = hitungInvoiceByStatus($koneksi, 'Pending');
$total_lunas = hitungInvoiceByStatus($koneksi, 'Lunas');

// Ambil invoice terbaru
$query_terbaru = "SELECT p.*, m.nama_murid, k.nama_kelas_bimbel 
                  FROM tbl_pembayaran p
                  JOIN tbl_data_murid m ON p.id_murid = m.id_murid
                  JOIN tbl_kelas_bimbel k ON p.id_kelas_bimbel = k.id_kelas_bimbel
                  ORDER BY p.created_at DESC
                  LIMIT 5";
$result_terbaru = mysqli_query($koneksi, $query_terbaru);
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Pembayaran Murid</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Pembayaran</li>
            </ol>

            <!-- Statistik Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                        Belum Lunas
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_belum_lunas ?> Invoice</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-circle fa-2x text-danger"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Pending
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_pending ?> Invoice</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-3">
                    <div class="card py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Lunas
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_lunas ?> Invoice</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-success"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            <!-- Invoice Terbaru -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="h5 my-2"><i class="fa-solid fa-history" style="padding-top: 3px;"></i> Invoice Terbaru</span>
                            <a href="daftar_invoice_admin.php" class="btn btn-primary">Lihat Semua</a>
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($result_terbaru) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th>No Invoice</th>
                                            <th><center>Murid</center></th>
                                            <th><center>Kelas</center></th>
                                            <th><center>Paket</center></th>
                                            <th><center>Total</center></th>
                                            <th><center>Status</center></th>
                                            <th><center>Tanggal</center></th>
                                            <th><center>Aksi</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($invoice = mysqli_fetch_assoc($result_terbaru)): ?>
                                        <tr>
                                            <td><strong><?= $invoice['no_invoice'] ?></strong></td>
                                            <td><?= $invoice['nama_murid'] ?></td>
                                            <td><?= $invoice['nama_kelas_bimbel'] ?></td>
                                            <td>
                                                <span>
                                                    <?= $invoice['jenis_paket'] == '8x_sesi' ? '8x Sesi' : '1x Sesi' ?>
                                                </span>
                                            </td>
                                            <td><?= formatRupiah($invoice['total_tagihan']) ?></td>
                                            <td>
                                                <span class="badge bg-<?= getBadgeStatus($invoice['status_pembayaran']) ?>">
                                                    <?= $invoice['status_pembayaran'] ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($invoice['created_at'])) ?></td>
                                            <td>
                                                <a href="detail_invoice_admin.php?id=<?= $invoice['id_pembayaran'] ?>" class="btn btn-sm btn-info text-white">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <?php if ($invoice['status_pembayaran'] != 'Lunas'): ?>
                                                <a href="konfirmasi_bayar_admin.php?id=<?= $invoice['id_pembayaran'] ?>" class="btn btn-sm btn-success">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info text-center mb-0">
                                <i class="fas fa-info-circle"></i> Belum ada invoice yang dibuat.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require_once "../template/footer_admin.php"; ?>
</div>

</div>
</body>
</html>