<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
require_once "fungsi_invoice.php";

// Ambil ID invoice
$id_pembayaran = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_pembayaran == 0) {
    header("Location: daftar_invoice_admin.php");
    exit();
}

// Ambil data invoice lengkap
$query = "SELECT p.*, m.nama_murid, m.alamat_murid, k.nama_kelas_bimbel, 
          tp.nama_program, t.jenjang_program, t.kelas_program,
          u.nama_user as nama_orgtua, u.telepon, u.email_user
          FROM tbl_pembayaran p
          JOIN tbl_data_murid m ON p.id_murid = m.id_murid
          JOIN tbl_kelas_bimbel k ON p.id_kelas_bimbel = k.id_kelas_bimbel
          JOIN tbl_tipe_program tp ON k.id_program = tp.id_program
          LEFT JOIN tbl_tingkat_program t ON k.id_tingkat = t.id_tingkat
          LEFT JOIN tbl_user u ON m.id_murid IN (
              SELECT id_murid FROM tbl_data_murid WHERE id_murid = m.id_murid
          )
          WHERE p.id_pembayaran = $id_pembayaran";

$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Invoice tidak ditemukan!'); window.location.href='daftar_invoice_admin.php';</script>";
    exit();
}

$invoice = mysqli_fetch_assoc($result);
// --- TAMBAHAN: HANDLER SINKRONISASI ---
if (isset($_POST['btn_sinkronisasi'])) {
    $fixed_count = syncSesiInvoice($koneksi, $id_pembayaran);
    if ($fixed_count > 0) {
        echo "<script>
                alert('Berhasil sinkronisasi! $fixed_count sesi absensi yang terlewat telah ditambahkan.');
                window.location.href = 'detail_invoice_admin.php?id=$id_pembayaran';
              </script>";
    } else {
        echo "<script>
                alert('Data sudah sinkron. Tidak ada absensi terlewat yang ditemukan.');
                window.location.href = 'detail_invoice_admin.php?id=$id_pembayaran';
              </script>";
    }
}

// Ambil histori sesi terpakai
$query_sesi = "SELECT st.*, a.tgl_absensi, a.status_absensi
               FROM tbl_sesi_terpakai st
               JOIN tbl_absensi a ON st.id_absensi = a.id_absensi
               WHERE st.id_pembayaran = $id_pembayaran
               ORDER BY st.tgl_pertemuan DESC";
$result_sesi = mysqli_query($koneksi, $query_sesi);

$title = "Detail Invoice " . $invoice['no_invoice'] . " - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Hitung persentase sesi terpakai
$persentase = 0;
if ($invoice['jumlah_sesi'] > 0) {
    $persentase = ($invoice['sesi_terpakai'] / $invoice['jumlah_sesi']) * 100;
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Detail Invoice</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="pembayaran_admin.php">Pembayaran</a></li>
                <li class="breadcrumb-item"><a href="daftar_invoice_admin.php">Daftar Invoice</a></li>
                <li class="breadcrumb-item active">Detail Invoice</li>
            </ol>

            <!-- Header Invoice dengan Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-invoice"></i> <?= $invoice['no_invoice'] ?></h5>
                </div>
            </div>

            <div class="row">
                <!-- Kolom Kiri: Info Invoice & Murid -->
                <div class="col-lg-8">
                    <!-- Info Murid -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user"></i> Informasi Murid
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <th width="40%">Nama Murid</th>
                                            <td><strong><?= $invoice['nama_murid'] ?></strong></td>
                                        </tr>
                                        <tr>
                                            <th>Kelas</th>
                                            <td><?= $invoice['nama_kelas_bimbel'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Program</th>
                                            <td><?= $invoice['nama_program'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Tingkat</th>
                                            <td><?= $invoice['jenjang_program'] ?> - Kelas <?= $invoice['kelas_program'] ?></td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table">
                                        <tr>
                                            <th width="40%">Alamat</th>
                                            <td><?= $invoice['alamat_murid'] ?></td>
                                        </tr>
                                        <tr>
                                            <th>Orang Tua/Wali</th>
                                            <td><?= $invoice['nama_orgtua'] ?? '-' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Telepon</th>
                                            <td><?= $invoice['telepon'] ?? '-' ?></td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td><?= $invoice['email_user'] ?? '-' ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Detail Tagihan -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-money-bill-wave"></i> Detail Tagihan
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Paket</th>
                                    <td>
                                        <?= $invoice['jenis_paket'] == '8x_sesi' ? '8x Sesi (Paket Bulanan)' : '1x Sesi (Per Pertemuan)' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lokasi Les</th>
                                    <td>
                                        <?= $invoice['lokasi_les'] ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Harga per Sesi</th>
                                    <td><?= formatRupiah($invoice['harga_per_sesi']) ?></td>
                                </tr>
                                <tr>
                                    <th>Jumlah Sesi</th>
                                    <td><?= $invoice['jumlah_sesi'] ?> sesi</td>
                                </tr>
                                <tr class="table-active">
                                    <th>Total Tagihan</th>
                                    <td><h5 class="text-primary mb-0"><?= formatRupiah($invoice['total_tagihan']) ?></h5></td>
                                </tr>
                            </table>

                            <!-- Progress Sesi -->
                            <div class="mt-4">
                                <h6>Progress Sesi:</h6>
                                <div class="progress mb-2" style="height: 30px;">
                                    <div class="progress-bar <?= $persentase >= 75 ? 'bg-warning' : 'bg-success' ?>" 
                                         role="progressbar" 
                                         style="width: <?= $persentase ?>%;" 
                                         aria-valuenow="<?= $persentase ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?= number_format($persentase, 1) ?>%
                                    </div>
                                </div>
                                <div class="row text-center">
                                    <div class="col-4">
                                        <div class="border rounded p-2 bg-light">
                                            <small class="text-muted">Terpakai</small>
                                            <h5 class="mb-0"><?= $invoice['sesi_terpakai'] ?></h5>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2 bg-light">
                                            <small class="text-muted">Tersisa</small>
                                            <h5 class="mb-0 text-<?= $invoice['sesi_tersisa'] <= 2 ? 'danger' : 'success' ?>">
                                                <?= $invoice['sesi_tersisa'] ?>
                                            </h5>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <div class="border rounded p-2 bg-light">
                                            <small class="text-muted">Total</small>
                                            <h5 class="mb-0"><?= $invoice['jumlah_sesi'] ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if ($invoice['sesi_tersisa'] <= 2 && $invoice['sesi_tersisa'] > 0): ?>
                            <div class="alert alert-warning mt-3 mb-0">
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Peringatan:</strong> Sesi hampir habis! Segera hubungi orang tua untuk perpanjangan.
                            </div>
                            <?php elseif ($invoice['sesi_tersisa'] == 0): ?>
                            <div class="alert alert-danger mt-3 mb-0">
                                <i class="fas fa-ban"></i> 
                                <strong>Sesi Habis!</strong> Silakan buat invoice baru untuk melanjutkan les.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Histori Sesi Terpakai -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <i class="fas fa-history"></i> Histori Sesi Terpakai
                        </div>
                        <div class="card-body">
                            <?php if (mysqli_num_rows($result_sesi) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal Pertemuan</th>
                                            <th>Keterangan</th>
                                            <th>Status Absensi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        while ($sesi = mysqli_fetch_assoc($result_sesi)): 
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= formatTanggalIndo($sesi['tgl_pertemuan']) ?></td>
                                            <td><?= $sesi['keterangan'] ?></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    <?= $sesi['status_absensi'] ?>
                                                </span>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-info mb-0 text-center">
                                <i class="fas fa-info-circle"></i> Belum ada sesi yang digunakan.
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Status Pembayaran & Action -->
                <div class="col-lg-4">
                    <!-- Status Pembayaran -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-receipt"></i> Status Pembayaran
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th>Tanggal Invoice</th>
                                    <td><?= formatTanggalIndo(date('Y-m-d', strtotime($invoice['created_at']))) ?></td>
                                </tr>
                                <tr>
                                    <th>Periode Mulai</th>
                                    <td><?= $invoice['periode_mulai'] ? formatTanggalIndo($invoice['periode_mulai']) : '-' ?></td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        <span class="badge bg-<?= getBadgeStatus($invoice['status_pembayaran']) ?>">
                                            <?= $invoice['status_pembayaran'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php if ($invoice['status_pembayaran'] == 'Lunas'): ?>
                                <tr>
                                    <th>Tanggal Bayar</th>
                                    <td><?= formatTanggalIndo($invoice['tgl_pembayaran']) ?></td>
                                </tr>
                                <tr>
                                    <th>Metode Bayar</th>
                                    <td><?= $invoice['metode_bayar'] ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>

                            <?php if (!empty($invoice['bukti_bayar'])): ?>
                            <div class="mt-3">
                                <label class="form-label fw-bold">Bukti Transfer:</label>
                                <div class="text-center">
                                    <img src="../../uploads/bukti_bayar/<?= $invoice['bukti_bayar'] ?>" 
                                         class="img-fluid rounded border" 
                                         alt="Bukti Transfer"
                                         style="max-height: 300px; cursor: pointer;"
                                         onclick="window.open(this.src)">
                                    <br>
                                    <small class="text-muted">Klik untuk memperbesar</small>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($invoice['catatan_pembayaran'])): ?>
                            <div class="mt-3">
                                <label class="form-label fw-bold">Catatan:</label>
                                <p class="text-muted"><?= nl2br($invoice['catatan_pembayaran']) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="card shadow mb-4">
                        <div class="card-header">
                            <i class="fas fa-cog"></i> Aksi
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <?php if ($invoice['status_pembayaran'] != 'Lunas'): ?>
                                <a href="konfirmasi_bayar_admin.php?id=<?= $id_pembayaran ?>" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Konfirmasi Pembayaran
                                </a>
                                <a href="edit_invoice_admin.php?id=<?= $id_pembayaran ?>" class="btn btn-warning text-white">
                                    <i class="fas fa-edit"></i> Edit Invoice
                                </a>
                                <?php endif; ?>

                                <form method="POST" onsubmit="return confirm('Fitur ini akan mengecek absensi Hadir yang belum terhitung masuk ke invoice ini. Lanjutkan?');">
                                    <button type="submit" name="btn_sinkronisasi" class="btn btn-info w-100 mb-2 text-white">
                                        <i class="fas fa-sync"></i> Sinkronisasi Sesi Absensi
                                    </button>
                                </form>                             
                                
                                <a href="daftar_invoice_admin.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Info Tambahan -->
                    <div class="card shadow">
                        <div class="card-header bg-light">
                            <i class="fas fa-info-circle"></i> Informasi
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="fas fa-calendar-check"></i> 
                                    Dibuat: <?= date('d M Y, H:i', strtotime($invoice['created_at'])) ?>
                                </li>
                                <li>
                                    <i class="fas fa-sync-alt"></i> 
                                    Update: <?= date('d M Y, H:i', strtotime($invoice['updated_at'])) ?>
                                </li>
                            </ul>
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