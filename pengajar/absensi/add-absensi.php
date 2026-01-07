<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// 1. Ambil ID Kelas dari URL dan validasi
if (!isset($_GET['id_kelas'])) {
    echo "<script>alert('ID kelas tidak ditemukan.'); document.location.href = 'absensi.php';</script>";
    exit();
}
$id_kelas = (int)$_GET['id_kelas'];

// 2. Ambil data nama kelas untuk judul
$queryKelas = mysqli_query($koneksi, "SELECT nama_kelas_bimbel FROM tbl_kelas_bimbel WHERE id_kelas_bimbel = $id_kelas");
$dataKelas = mysqli_fetch_array($queryKelas);
if ($dataKelas == null) {
     echo "<script>alert('Data kelas tidak ditemukan.'); document.location.href = 'absensi.php';</script>";
    exit();
}
$nama_kelas = $dataKelas['nama_kelas_bimbel'];
$title = "Input Absensi - $nama_kelas"; // Set title dinamis

// 3. Ambil daftar murid di kelas ini
$queryMurid = mysqli_query($koneksi, "SELECT id_murid, nama_murid FROM tbl_data_murid WHERE id_kelas_bimbel = $id_kelas ORDER BY nama_murid ASC");
$jumlah_murid = mysqli_num_rows($queryMurid);

// Panggil template
require_once "../template/header_pengajar.php";
require_once "../template/navbar_pengajar.php";
require_once "../template/sidebar_pengajar.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Input Absensi Harian</h1>
            <h5 class="mb-4 text-muted"><?= $nama_kelas ?></h5>
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_pengajar.php">Home</a></li>
                <li class="breadcrumb-item"><a href="absensi.php">Absensi</a></li>
                <li class="breadcrumb-item"><a href="detail-absensi.php?id_kelas=<?= $id_kelas ?>">Riwayat Absensi</a></li>
                <li class="breadcrumb-item active">Input Absensi</li>
            </ol>
            
            <form action="proses-absensi.php" method="POST">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-user-check" style="padding-top: 10px;"></i> Input Absensi</span>
                    </div>
                    <div class="card-body">
                        
                        <div class="row mb-3">
                            <label for="tgl_absensi" class="col-sm-2 col-form-label">Tanggal Absensi</label>
                            <div class="col-sm-3">
                                <input type="date" name="tgl_absensi" id="tgl_absensi" class="form-control" value="<?= date('Y-m-d') ?>" required>
                            </div>
                        </div>
                        
                        <input type="hidden" name="id_kelas" value="<?= $id_kelas ?>">

                        <table class="table table-bordered table-striped mt-3">
                            <thead>
                                <tr>
                                    <th scope="col" style="width: 10%;">No</th>
                                    <th scope="col" style="width: 50%;">Nama Murid</th>
                                    <th scope="col" style="width: 40%;">Status Absensi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if ($jumlah_murid > 0) {
                                    $no = 1;
                                    while ($murid = mysqli_fetch_array($queryMurid)) {
                                ?>
                                <tr>
                                    <th scope="row"><?= $no++ ?></th>
                                    <td><?= $murid['nama_murid'] ?></td>
                                    <td>
                                        <select name="status[<?= $murid['id_murid'] ?>]" class="form-select" required>
                                            <option value="Hadir" selected>Hadir</option>
                                            <option value="Izin">Izin</option>
                                            <option value="Sakit">Sakit</option>
                                            <option value="Alpa">Alpa</option>
                                        </select>
                                    </td>
                                </tr>
                                <?php
                                    } // Akhir while loop
                                } else { // Jika tidak ada murid
                                    echo '<tr><td colspan="3" class="text-center">Tidak ada murid di kelas ini.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <button type="submit" name="simpan_massal" class="btn btn-primary" 
                            <?= ($jumlah_murid == 0) ? 'disabled' : '' ?>>
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Absensi
                        </button>
                        <a href="detail-absensi.php?id_kelas=<?= $id_kelas ?>" class="btn btn-danger">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                    </div>
                </div>
            </form>

        </div>
    </main>

<?php
require_once "../template/footer_pengajar.php";
?>