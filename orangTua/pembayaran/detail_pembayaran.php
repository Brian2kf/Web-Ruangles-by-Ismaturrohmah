<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
require_once "../../admin/pembayaran/fungsi_invoice.php";

$title = "Detail Pembayaran - Ruang Les";
require_once "../template/header_ortu.php";
require_once "../template/navbar_ortu.php";
require_once "../template/sidebar_ortu.php";

$id_pembayaran = $_GET['id'];
$id_ortu = $_SESSION["ssId"];

// Ambil Data Invoice (Keamanan: Pastikan milik anak dari user ini)
$query = "SELECT p.*, m.nama_murid, m.alamat_murid, k.nama_kelas_bimbel, tp.nama_program 
          FROM tbl_pembayaran p
          JOIN tbl_data_murid m ON p.id_murid = m.id_murid
          JOIN tbl_kelas_bimbel k ON p.id_kelas_bimbel = k.id_kelas_bimbel
          JOIN tbl_tipe_program tp ON k.id_program = tp.id_program
          WHERE p.id_pembayaran = '$id_pembayaran' AND m.id_user_ortu = '$id_ortu'";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan.'); window.location='pembayaran.php';</script>";
    exit;
}

// Variabel untuk pesan WhatsApp
$no_wa_admin = "6289659595969"; // Ganti dengan nomor WA Admin (format 62...)
$pesan_wa = "Halo Admin Ruangles, saya ingin melakukan pembayaran/konfirmasi untuk:%0a%0a";
$pesan_wa .= "No Invoice: *" . $data['no_invoice'] . "*%0a";
$pesan_wa .= "Nama Murid: " . $data['nama_murid'] . "%0a";
$pesan_wa .= "Total Tagihan: " . formatRupiah($data['total_tagihan']) . "%0a%0a";
$pesan_wa .= "Mohon informasinya. Terima kasih.";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Rincian Invoice</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="pembayaran.php">Riwayat</a></li>
                <li class="breadcrumb-item active">Detail Invoice</li>
            </ol>

            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <div class="card shadow border-0 mb-4">
                        <div class="card-header bg-white p-4 border-bottom">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h4 class="mb-0 fw-bold text-primary">RUANG LES</h4>
                                    <small class="text-muted">by Ismaturrohmah</small>
                                </div>
                                <div class="text-end">
                                    <h5 class="mb-1 text-secondary">INVOICE</h5>
                                    <div class="fw-bold"><?= $data['no_invoice'] ?></div>
                                    <div class="mt-2">
                                        <?php if($data['status_pembayaran'] == 'Lunas'): ?>
                                            <span class="badge bg-success border border-success px-3 py-2 rounded-pill">LUNAS / PAID</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger border border-danger px-3 py-2 rounded-pill">BELUM LUNAS / UNPAID</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body p-4">
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <h6 class="mb-3 font-weight-bold text-uppercase text-muted small">Ditagihkan Kepada:</h6>
                                    <h5 class="text-dark mb-1"><?= $data['nama_murid'] ?></h5>
                                    <p class="text-muted mb-0"><?= $data['alamat_murid'] ?></p>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <h6 class="mb-3 font-weight-bold text-uppercase text-muted small">Detail Pembayaran:</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li>Tanggal Invoice: <strong><?= date('d M Y', strtotime($data['created_at'])) ?></strong></li>
                                        <li>Jatuh Tempo: <strong>Segera</strong></li>
                                        <li>Status: <strong><?= $data['status_pembayaran'] ?></strong></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="table-responsive mb-4">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Deskripsi</th>
                                            <th class="text-center">Jumlah Sesi</th>
                                            <th class="text-end">Harga/Sesi</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong><?= $data['nama_program'] ?></strong><br>
                                                <small class="text-muted">Kelas: <?= $data['nama_kelas_bimbel'] ?></small><br>
                                                <small class="text-muted">Paket: <?= ($data['jenis_paket']=='8x_sesi') ? '8x Pertemuan (Bulanan)' : '1x Pertemuan (Harian)'; ?></small>
                                            </td>
                                            <td class="text-center"><?= $data['jumlah_sesi'] ?></td>
                                            <td class="text-end"><?= formatRupiah($data['harga_per_sesi']) ?></td>
                                            <td class="text-end"><?= formatRupiah($data['total_tagihan']) ?></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">TOTAL TAGIHAN</td>
                                            <td class="text-end fw-bold text-primary h5"><?= formatRupiah($data['total_tagihan']) ?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="row align-items-center bg-light p-3 rounded mx-0">
                                <div class="col-md-7 mb-3 mb-md-0">
                                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-building-columns"></i> Metode Pembayaran:</h6>
                                    <p class="mb-1 small">Silakan transfer ke salah satu rekening berikut:</p>
                                    <ul class="mb-0 small text-muted">
                                        <li><strong>BCA:</strong> 0000-0000-0000 (Ismaturrohmah)</li>
                                        <li><strong>BRI:</strong> 0000-0000-0000 (Ismaturrohmah)</li>
                                        <li><strong>DANA/OVO:</strong> 0000-0000-0000</li>
                                    </ul>
                                </div>
                                <div class="col-md-5 text-center text-md-end">
                                    <?php if($data['status_pembayaran'] != 'Lunas'): ?>
                                        <p class="mb-2 small text-danger fw-bold">Belum dibayar?</p>
                                        <a href="https://wa.me/<?= $no_wa_admin ?>?text=<?= $pesan_wa ?>" target="_blank" class="btn btn-success w-100">
                                            <i class="fa-brands fa-whatsapp fa-lg me-1"></i> Konfirmasi Pembayaran
                                        </a>
                                        <small class="d-block mt-1 text-muted fst-italic" style="font-size: 0.75rem;">
                                            Klik tombol di atas untuk mengirim bukti transfer via WhatsApp Admin.
                                        </small>
                                    <?php else: ?>
                                        <div class="alert alert-success mb-0 py-2">
                                            <i class="fa-solid fa-check-circle"></i> Pembayaran Lunas<br>
                                            <small><?= date('d M Y', strtotime($data['tgl_pembayaran'])) ?></small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
            </div>
        </div>
    </main>
<?php require_once "../template/footer_ortu.php"; ?>
</div>