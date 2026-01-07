<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// 1. Ambil ID Kelas dari URL dan validasi
if (!isset($_GET['id_kelas'])) {
    echo "<script>alert('ID kelas tidak ditemukan.'); document.location.href = 'catatan_murid.php';</script>";
    exit();
}
$id_kelas = (int)$_GET['id_kelas'];

// 2. Ambil data nama kelas & id_tingkat untuk judul dan data tersembunyi
$queryKelas = mysqli_query($koneksi, "SELECT nama_kelas_bimbel, id_tingkat FROM tbl_kelas_bimbel WHERE id_kelas_bimbel = $id_kelas");
$dataKelas = mysqli_fetch_array($queryKelas);
if ($dataKelas == null) {
     echo "<script>alert('Data kelas tidak ditemukan.'); document.location.href = 'catatan_murid.php';</script>";
    exit();
}
$nama_kelas = $dataKelas['nama_kelas_bimbel'];
$id_tingkat = $dataKelas['id_tingkat']; // Kita perlukan ini untuk disimpan ke tbl_progres

$title = "Tambah Catatan - $nama_kelas"; // Set title dinamis

// 3. Ambil daftar murid di kelas ini
$queryMurid = mysqli_query($koneksi, "SELECT id_murid, nama_murid FROM tbl_data_murid WHERE id_kelas_bimbel = $id_kelas ORDER BY nama_murid ASC");

// 4. Ambil daftar semua pengajar
$queryPengajar = mysqli_query($koneksi, "SELECT id_pengajar, nama_pengajar FROM tbl_data_pengajar ORDER BY nama_pengajar ASC");

// Panggil template
require_once "../template/header_pengajar.php";
require_once "../template/navbar_pengajar.php";
require_once "../template/sidebar_pengajar.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Tambah Catatan Perkembangan</h1>
            <h5 class="mb-4 text-muted"><?= $nama_kelas ?></h5>
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_pengajar.php">Home</a></li>
                <li class="breadcrumb-item"><a href="catatan_murid.php">Catatan Murid</a></li>
                <li class="breadcrumb-item"><a href="detail-catatan.php?id_kelas=<?= $id_kelas ?>">Detail Catatan</a></li>
                <li class="breadcrumb-item active">Tambah Catatan</li>
            </ol>
            <form action="proses-catatan.php" method="POST">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-plus" style="padding-top: 10px;"></i> Input Catatan Baru</span>
                    </div>
                    <div class="card-body">
                        
                        <input type="hidden" name="id_kelas" value="<?= $id_kelas ?>">
                        <input type="hidden" name="id_tingkat" value="<?= $id_tingkat ?>">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="id_murid" class="form-label">Nama Murid</label>
                                    <select name="id_murid" id="id_murid" class="form-select" required>
                                        <option value="" selected>-- Pilih Murid --</option>
                                        <?php
                                        while ($murid = mysqli_fetch_array($queryMurid)) {
                                            echo '<option value="' . $murid['id_murid'] . '">' . $murid['nama_murid'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="id_pengajar" class="form-label">Nama Pengajar</label>
                                    <select name="id_pengajar" id="id_pengajar" class="form-select" required>
                                        <option value="" selected>-- Pilih Pengajar --</option>
                                        <?php
                                        while ($pengajar = mysqli_fetch_array($queryPengajar)) {
                                            echo '<option value="' . $pengajar['id_pengajar'] . '">' . $pengajar['nama_pengajar'] . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label for="mata_pelajaran" class="form-label">Mata Pelajaran</label>
                                    <input type="text" name="mata_pelajaran" id="mata_pelajaran" class="form-control" placeholder="Contoh: Matematika" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="materi" class="form-label">Materi yang Diajarkan</label>
                                    <input type="text" name="materi" id="materi" class="form-control" placeholder="Contoh: Penjumlahan 1-20" required>
                                </div>

                                <div class="mb-3">
                                    <label for="isi_progres" class="form-label">Catatan Perkembangan</label>
                                    <textarea name="isi_progres" id="isi_progres" rows="5" class="form-control" placeholder="Tulis catatan perkembangan murid di sini..." required></textarea>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="submit" name="simpan" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan
                        </button>
                        <a href="detail-catatan.php?id_kelas=<?= $id_kelas ?>" class="btn btn-danger">
                            <i class="fa-solid fa-xmark"></i> Batal
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>
<?php
require_once "../template/footer_pengajar.php";
?>