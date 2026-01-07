<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// 1. Ambil ID Absensi dan ID Kelas dari URL
if (!isset($_GET['id_absensi']) || !isset($_GET['id_kelas'])) {
    echo "<script>alert('ID tidak ditemukan.'); document.location.href = 'absensi.php';</script>";
    exit();
}
$id_absensi = (int)$_GET['id_absensi'];
$id_kelas   = (int)$_GET['id_kelas'];

// 2. Ambil data absensi yang spesifik
$queryAbsen = mysqli_query($koneksi, 
    "SELECT 
        a.id_absensi, a.tgl_absensi, a.status_absensi,
        m.nama_murid,
        k.nama_kelas_bimbel
     FROM tbl_absensi a
     JOIN tbl_data_murid m ON a.id_murid = m.id_murid
     JOIN tbl_kelas_bimbel k ON a.id_kelas_bimbel = k.id_kelas_bimbel
     WHERE a.id_absensi = $id_absensi"
);
$data = mysqli_fetch_array($queryAbsen);
if ($data == null) {
     echo "<script>alert('Data absensi tidak ditemukan.'); document.location.href = 'absensi.php';</script>";
    exit();
}
$title = "Edit Absensi - " . $data['nama_murid'];

// Panggil template
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Edit Absensi</h1>
            <h5 class="mb-4 text-muted"><?= $data['nama_murid'] . ' (' . $data['nama_kelas_bimbel'] . ')' ?></h5>
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="absensi.php">Absensi</a></li>
                <li class="breadcrumb-item"><a href="detail-absensi.php?id_kelas=<?= $id_kelas ?>">Riwayat Absensi</a></li>
                <li class="breadcrumb-item active">Edit Absensi</li>
            </ol>
            
            <form action="proses-absensi.php" method="POST">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-pen-to-square" style="padding-top: 10px;"></i> Edit Data Absensi</span>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <input type="hidden" name="id_absensi" value="<?= $data['id_absensi'] ?>">
                                <input type="hidden" name="id_kelas" value="<?= $id_kelas ?>"> <div class="mb-3">
                                    <label for="tgl_absensi" class="form-label">Tanggal Absensi</label>
                                    <input type="date" name="tgl_absensi" id="tgl_absensi" class="form-control" 
                                           value="<?= $data['tgl_absensi'] ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="status_absensi" class="form-label">Status Absensi</label>
                                    <select name="status_absensi" id="status_absensi" class="form-select" required>
                                        <?php
                                        $status_list = ['Hadir', 'Izin', 'Sakit', 'Alpa'];
                                        foreach ($status_list as $status) {
                                            $selected = ($status == $data['status_absensi']) ? 'selected' : '';
                                            echo '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" name="update" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Update
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
require_once "../template/footer_admin.php";
?>