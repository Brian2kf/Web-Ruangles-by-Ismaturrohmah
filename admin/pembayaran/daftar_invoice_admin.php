<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
require_once "fungsi_invoice.php";

$title = "Daftar Invoice - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Filter
$filter_status = isset($_GET['filter']) ? $_GET['filter'] : 'Semua';
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

// Query dasar
$query = "SELECT p.*, m.nama_murid, k.nama_kelas_bimbel, tp.nama_program 
          FROM tbl_pembayaran p
          JOIN tbl_data_murid m ON p.id_murid = m.id_murid
          JOIN tbl_kelas_bimbel k ON p.id_kelas_bimbel = k.id_kelas_bimbel
          JOIN tbl_tipe_program tp ON k.id_program = tp.id_program
          WHERE 1=1";

// Apply filter status
if ($filter_status != 'Semua') {
    $query .= " AND p.status_pembayaran = '$filter_status'";
}

// Apply search
if (!empty($search)) {
    $query .= " AND (p.no_invoice LIKE '%$search%' OR m.nama_murid LIKE '%$search%')";
}

$query .= " ORDER BY p.created_at DESC";

$result = mysqli_query($koneksi, $query);
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Daftar Invoice</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="pembayaran_admin.php">Pembayaran</a></li>
                <li class="breadcrumb-item active">Daftar Invoice</li>
            </ol>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="h5 my-2"><i class="fa-solid fa-file-invoice" style="padding-top: 3px;"></i> Data Pembayaran</span>
                    <a href="tambah_invoice_admin.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Buat Invoice Baru
                    </a>
                </div>


                <div class="card-body">
                    <!-- Filter & Search -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="" class="d-flex gap-2">
                                <input type="text" name="search" class="form-control" placeholder="Cari no invoice atau nama murid..." value="<?= $search ?>">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                                <?php if (!empty($search)): ?>
                                <a href="daftar_invoice_admin.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i>
                                </a>
                                <?php endif; ?>
                            </form>
                        </div>
                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <a href="?filter=Semua" class="btn btn-<?= $filter_status == 'Semua' ? 'secondary' : 'outline-secondary' ?>">
                                    Semua
                                </a>
                                <a href="?filter=Belum Lunas" class="btn btn-<?= $filter_status == 'Belum Lunas' ? 'secondary' : 'outline-secondary' ?>">
                                    Belum Lunas
                                </a>
                                <a href="?filter=Pending" class="btn btn-<?= $filter_status == 'Pending' ? 'secondary' : 'outline-secondary' ?>">
                                    Pending
                                </a>
                                <a href="?filter=Lunas" class="btn btn-<?= $filter_status == 'Lunas' ? 'secondary' : 'outline-secondary' ?>">
                                    Lunas
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Invoice -->
                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th><center>No Invoice</center></th>
                                    <th><center>Murid</center></th>
                                    <th><center>Kelas</center></th>
                                    <th><center>Program</center></th>
                                    <th><center>Paket</center></th>
                                    <th><center>Sesi</center></th>
                                    <th><center>Total</center></th>
                                    <th><center>Status</center></th>
                                    <th><center>Tanggal</center></th>
                                    <th><center>Aksi</center></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($invoice = mysqli_fetch_assoc($result)): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= $invoice['no_invoice'] ?></strong></td>
                                    <td><?= $invoice['nama_murid'] ?></td>
                                    <td><?= $invoice['nama_kelas_bimbel'] ?></td>
                                    <td>
                                        <span>
                                            <?= $invoice['nama_program'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span>
                                            <?= $invoice['jenis_paket'] == '8x_sesi' ? '8x Sesi' : '1x Sesi' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small>
                                            Terpakai: <strong><?= $invoice['sesi_terpakai'] ?></strong><br>
                                            Tersisa: <strong class="text-<?= $invoice['sesi_tersisa'] <= 2 ? 'danger' : 'success' ?>">
                                                <?= $invoice['sesi_tersisa'] ?>
                                            </strong>
                                        </small>
                                    </td>
                                    <td><?= formatRupiah($invoice['total_tagihan']) ?></td>
                                    <td>
                                        <span class="badge bg-<?= getBadgeStatus($invoice['status_pembayaran']) ?>">
                                            <?= $invoice['status_pembayaran'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small><?= date('d/m/Y', strtotime($invoice['created_at'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="btn-group-sm">
                                            <a href="detail_invoice_admin.php?id=<?= $invoice['id_pembayaran'] ?>" class="btn btn-info text-white" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if ($invoice['status_pembayaran'] != 'Lunas'): ?>
                                            <a href="konfirmasi_bayar_admin.php?id=<?= $invoice['id_pembayaran'] ?>" class="btn btn-success" title="Konfirmasi Pembayaran">
                                                <i class="fas fa-check"></i>
                                            </a>
                                            <a href="edit_invoice_admin.php?id=<?= $invoice['id_pembayaran'] ?>" class="btn btn-warning text-white" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                        <h5>Tidak ada invoice ditemukan</h5>
                        <p class="mb-3">
                            <?php if (!empty($search)): ?>
                                Hasil pencarian untuk "<?= $search ?>" tidak ditemukan.
                            <?php elseif ($filter_status != 'Semua'): ?>
                                Belum ada invoice dengan status "<?= $filter_status ?>".
                            <?php else: ?>
                                Belum ada invoice yang dibuat.
                            <?php endif; ?>
                        </p>
                        <a href="tambah_invoice_admin.php" class="btn btn-success">
                            <i class="fas fa-plus"></i> Buat Invoice Pertama
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php require_once "../template/footer_admin.php"; ?>
</div>
</div>
</body>
</html>