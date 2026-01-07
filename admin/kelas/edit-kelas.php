<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Edit Kelas - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// 1. Ambil ID Kelas dari URL
if (!isset($_GET['id_kelas_bimbel'])) {
    echo "<script>alert('ID kelas tidak ditemukan.'); document.location.href = 'kelas.php';</script>";
    exit();
}
$id_kelas = (int)$_GET['id_kelas_bimbel'];

// 2. Query untuk mengambil data kelas yang spesifik
$queryKelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas_bimbel WHERE id_kelas_bimbel = $id_kelas");
$data = mysqli_fetch_array($queryKelas);
if ($data == null) {
     echo "<script>alert('Data kelas tidak ditemukan.'); document.location.href = 'kelas.php';</script>";
    exit();
}

// 3. Ambil data untuk dropdown (Program dan Tingkat)
$queryProgram = mysqli_query($koneksi, "SELECT * FROM tbl_tipe_program ORDER BY nama_program ASC");
$queryTingkat = mysqli_query($koneksi, "
    SELECT id_tingkat, CASE 
        WHEN jenjang_program IN ('PAUD', 'TK') THEN jenjang_program
        ELSE CONCAT(kelas_program, ' ', jenjang_program) 
    END AS nama_tingkat
    FROM tbl_tingkat_program ORDER BY id_tingkat ASC
");
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Edit Data Kelas</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="kelas.php">Kelas</a></li>
                <li class="breadcrumb-item active">Edit Data Kelas</li>
            </ol>
            <form action="proses-kelas.php" method="POST">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-pen-to-square" style="padding-top: 10px;"></i> Edit Data Kelas</span>
                        <button type="submit" name="update_data" class="btn btn-primary float-end"><i class="fa-solid fa-floppy-disk"></i> Update Data</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <input type="hidden" name="id_kelas_bimbel" value="<?= $data['id_kelas_bimbel'] ?>">

                                <div class="mb-3 row">
                                    <label for="nama_kelas_bimbel" class="col-sm-2 col-form-label">Nama Kelas</label>
                                    <label for="nama_kelas_bimbel" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="text" name="nama_kelas_bimbel" required class="form-control border-0 border-bottom ps-2" value="<?= $data['nama_kelas_bimbel'] ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_program" class="col-sm-2 col-form-label">Program</label>
                                    <label for="id_program" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="id_program" id="id_program" class="form-select border-0 border-bottom" required>
                                            <option value="">--Pilih Program--</option>
                                            <?php
                                            while ($d_program = mysqli_fetch_array($queryProgram)) {
                                                $selected = ($d_program['id_program'] == $data['id_program']) ? 'selected' : '';
                                                echo '<option value="' . $d_program['id_program'] . '" ' . $selected . '>' . $d_program['nama_program'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_tingkat" class="col-sm-2 col-form-label">Tingkat</label>
                                    <label for="id_tingkat" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="id_tingkat" id="id_tingkat" class="form-select border-0 border-bottom" required>
                                            <option value="">--Pilih Tingkat--</option>
                                            <?php
                                            while ($d_tingkat = mysqli_fetch_array($queryTingkat)) {
                                                $selected = ($d_tingkat['id_tingkat'] == $data['id_tingkat']) ? 'selected' : '';
                                                echo '<option value="' . $d_tingkat['id_tingkat'] . '" ' . $selected . '>' . $d_tingkat['nama_tingkat'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
require_once "../template/footer_admin.php";
?>