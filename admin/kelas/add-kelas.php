<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Tambah Kelas - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Ambil data untuk dropdown dinamis
// 1. Ambil data Program
$queryProgram = mysqli_query($koneksi, "SELECT * FROM tbl_tipe_program ORDER BY nama_program ASC");

// 2. Ambil data Tingkat (dengan format nama khusus)
$queryTingkat = mysqli_query($koneksi, "
    SELECT id_tingkat, 
           CASE 
               WHEN jenjang_program IN ('PAUD', 'TK') THEN jenjang_program
               ELSE CONCAT(kelas_program, ' ', jenjang_program) 
           END AS nama_tingkat
    FROM tbl_tingkat_program
    ORDER BY id_tingkat ASC
");
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Tambah Kelas</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="kelas.php">Kelas</a></li>
                <li class="breadcrumb-item active">Tambah Kelas</li>
            </ol>
            <form action="proses-kelas.php" method="POST">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-square-plus" style="padding-top: 10px;"></i> Tambah Kelas</span>
                        <button type="submit" name="simpan" class="btn btn-primary float-end"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                        <button type="reset" name="reset" class="btn btn-danger float-end me-1"><i class="fa-solid fa-xmark"></i> Reset</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mb-3 row">
                                    <label for="nama_kelas_bimbel" class="col-sm-2 col-form-label">Nama Kelas</label>
                                    <label for="nama_kelas_bimbel" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="text" name="nama_kelas_bimbel" required class="form-control border-0 border-bottom ps-2" placeholder="Masukkan Nama Kelas (Cth: SD 1 Reguler A)">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_program" class="col-sm-2 col-form-label">Program</label>
                                    <label for="id_program" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="id_program" id="id_program" class="form-select border-0 border-bottom" required>
                                            <option value="" selected>--Pilih Program--</option>
                                            <?php
                                            while ($data = mysqli_fetch_array($queryProgram)) {
                                                echo '<option value="' . $data['id_program'] . '">' . $data['nama_program'] . '</option>';
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
                                            <option value="" selected>--Pilih Tingkat--</option>
                                            <?php
                                            while ($data = mysqli_fetch_array($queryTingkat)) {
                                                echo '<option value="' . $data['id_tingkat'] . '">' . $data['nama_tingkat'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="hari" class="col-sm-2 col-form-label">Hari</label>
                                    <label for="hari" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="hari" id="hari" class="form-select border-0 border-bottom" required>
                                            <option value="" selected>--Pilih Hari--</option>
                                            <option value="Senin">Senin</option>
                                            <option value="Selasa">Selasa</option>
                                            <option value="Rabu">Rabu</option>
                                            <option value="Kamis">Kamis</option>
                                            <option value="Jumat">Jumat</option>
                                            <option value="Sabtu">Sabtu</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="jam_mulai" class="col-sm-2 col-form-label">Jam Mulai</label>
                                    <label for="jam_mulai" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="time" name="jam_mulai" required class="form-control border-0 border-bottom ps-2">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="jam_selesai" class="col-sm-2 col-form-label">Jam Selesai</label>
                                    <label for="jam_selesai" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="time" name="jam_selesai" required class="form-control border-0 border-bottom ps-2">
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