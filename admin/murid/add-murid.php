<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Tambah Murid - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Ambil data untuk dropdown dinamis
//Ambil data Program
$queryProgram = mysqli_query($koneksi, "SELECT * FROM tbl_tipe_program");
$queryOrtu = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE role = '1' ORDER BY nama_user ASC");

// Ambil data Tingkat (dengan format nama khusus)
$queryTingkat = mysqli_query($koneksi, "
    SELECT id_tingkat, 
           CASE 
               WHEN jenjang_program IN ('PAUD', 'TK') THEN jenjang_program
               ELSE CONCAT(kelas_program, ' ', jenjang_program) 
           END AS nama_tingkat
    FROM tbl_tingkat_program
    ORDER BY id_tingkat ASC
");

// Ambil data Kelas Bimbel
$queryKelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas_bimbel ORDER BY nama_kelas_bimbel ASC");
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Tambah Siswa</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="murid.php">Murid</a></li>
                <li class="breadcrumb-item active">Tambah Murid</li>
            </ol>
            <form action="proses-murid.php" method="POST" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-square-plus" style="padding-top: 10px;"></i> Tambah Murid</span>
                        <button type="submit" name="simpan" class="btn btn-primary float-end"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                        <button type="reset" name="reset" class="btn btn-danger float-end me-1"><i class="fa-solid fa-xmark"></i> Reset</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <div class="mb-3 row">
                                    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                    <label for="nama" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="text" name="nama" required class="form-control border-0 border-bottom ps-2" placeholder="Masukkan Nama Murid">
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
                                    <label for="id_tingkat" class="col-sm-2 col-form-label">Jenjang Pendidikan</label>
                                    <label for="id_tingkat" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="id_tingkat" id="id_tingkat" class="form-select border-0 border-bottom" required>
                                            <option value="" selected>--Pilih Jenjang Pendidikan--</option>
                                            <?php
                                            while ($data = mysqli_fetch_array($queryTingkat)) {
                                                echo '<option value="' . $data['id_tingkat'] . '">' . $data['nama_tingkat'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_kelas_bimbel" class="col-sm-2 col-form-label">Kelas Bimbel</label>
                                    <label for="id_kelas_bimbel" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="id_kelas_bimbel" id="id_kelas_bimbel" class="form-select border-0 border-bottom" required>
                                            <option value="" selected>--Pilih Kelas--</option>
                                            <?php
                                            while ($data = mysqli_fetch_array($queryKelas)) {
                                                echo '<option value="' . $data['id_kelas_bimbel'] . '">' . $data['nama_kelas_bimbel'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                    <label for="alamat" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <textarea name="alamat" id="alamat" cols="30" rows="3" placeholder="Alamat Siswa" class="form-control" required></textarea>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_user_ortu" class="col-sm-2 col-form-label">Orang Tua</label>
                                    <label for="id_user_ortu" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="id_user_ortu" id="id_user_ortu" class="form-select border-0 border-bottom">
                                            <option value="" selected>--Pilih Orang Tua--</option>
                                            <?php
                                            while ($ortu = mysqli_fetch_array($queryOrtu)) {
                                                echo '<option value="' . $ortu['id'] . '">' . $ortu['nama_user'] . ' (Username: ' . $ortu['username'] . ')</option>';
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