<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Detail Pendaftar - Ruang Les";

require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

$id = $_GET['id'];

// UPDATE QUERY: Tambahkan JOIN ke tbl_tingkat_program
$query = "SELECT p.*, tp.nama_program, ttp.jenjang_program, ttp.kelas_program 
          FROM tbl_pendaftaran p
          LEFT JOIN tbl_tipe_program tp ON p.id_program = tp.id_program
          LEFT JOIN tbl_tingkat_program ttp ON p.id_tingkat = ttp.id_tingkat
          WHERE p.id_pendaftaran = $id";
$exec = mysqli_query($koneksi, $query);
$data = mysqli_fetch_array($exec);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='pendaftar.php';</script>";
    exit;
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Detail Pendaftar</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="pendaftar.php">Pendaftar</a></li>
                <li class="breadcrumb-item active">Detail</li>
            </ol>

            <div class="row">
                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fa-solid fa-child"></i> Data Calon Murid
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="35%">Nama Lengkap</th>
                                    <td>: <?= $data['nama_camur'] ?></td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td>: <?= date('d M Y', strtotime($data['tgl_lahir_camur'])) ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis Kelamin</th>
                                    <td>: <?= $data['jk_camur'] ?></td>
                                </tr>
                                <tr>
                                    <th>Kelas Saat Ini</th>
                                    <td>: <?= ($data['kelas_program'] ?? '-') . ' ' . ($data['jenjang_program'] ?? '') ?></td>
                                </tr>
                                <tr>
                                    <th>Program Pilihan</th>
                                    <td>: <strong><?= $data['nama_program'] ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Karakteristik</th>
                                    <td>: <?= nl2br($data['karakteristik_camur']) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fa-solid fa-user-group"></i> Data Orang Tua / Wali
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="35%">Nama Orang Tua</th>
                                    <td>: <?= $data['nama_orgtua_wali'] ?></td>
                                </tr>
                                <tr>
                                    <th>No. Telepon</th>
                                    <td>: <?= $data['telepon_orgtua_wali'] ?></td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>: <?= $data['alamat_camur'] ?></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>: <?= $data['email_orgtua_wali'] ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fa-solid fa-gavel"></i> Konfirmasi Pendaftaran
                        </div>
                        <div class="card-body text-center">
                            <h5 class="mb-3">Status Saat Ini: 
                                <?php 
                                if($data['status_pendaftaran'] == 'Pending') echo '<span class="badge bg-warning text-white">Pending</span>';
                                elseif($data['status_pendaftaran'] == 'Diterima') echo '<span class="badge bg-success">Diterima</span>';
                                else echo '<span class="badge bg-danger">Ditolak</span>';
                                ?>
                            </h5>

                            <?php if ($data['status_pendaftaran'] == 'Pending') { ?>
                                <p class="card-text text-muted mb-3">Silakan pilih tindakan untuk pendaftar ini.</p>
                                <form action="proses_pendaftar.php" method="POST">
                                    <input type="hidden" name="id_pendaftaran" value="<?= $data['id_pendaftaran'] ?>">
                                    
                                    <button type="submit" name="aksi" value="terima" class="btn btn-sm btn-primary me-2" onclick="return confirm('Yakin ingin menerima murid ini?')">
                                        <i class="fa-solid fa-check"></i> Terima
                                    </button>
                                    
                                    <button type="submit" name="aksi" value="tolak" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menolak pendaftaran ini?')">
                                        <i class="fa-solid fa-xmark"></i> Tolak
                                    </button>
                                </form>
                            <?php } else { ?>
                                <div class="alert alert-info">
                                    Data pendaftaran ini sudah diproses.
                                </div>
                            <?php } ?>
                            
                            <div class="mt-3">
                                <a href="pendaftar.php" class="btn btn-secondary btn-sm">Kembali ke Daftar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

<?php
require_once "../template/footer_admin.php";
?>