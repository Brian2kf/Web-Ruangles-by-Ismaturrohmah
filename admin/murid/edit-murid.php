<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Edit Murid - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// 1. Ambil ID Murid dari URL
if (!isset($_GET['id_murid'])) {
    echo "<script>
            alert('ID murid tidak ditemukan.');
            document.location.href = 'murid.php';
          </script>";
    exit();
}
$id_murid = (int)$_GET['id_murid'];

// 2. Query untuk mengambil data murid yang spesifik
$queryMurid = mysqli_query($koneksi, "SELECT * FROM tbl_data_murid WHERE id_murid = $id_murid");
$data = mysqli_fetch_array($queryMurid);

// Jika data tidak ditemukan
if ($data == null) {
     echo "<script>
            alert('Data murid tidak ditemukan.');
            document.location.href = 'murid.php';
          </script>";
    exit();
}

// 3. Ambil data untuk dropdown (sama seperti add-murid.php)
$queryOrtu = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE role = '1' ORDER BY nama_user ASC");
$queryProgram = mysqli_query($koneksi, "SELECT * FROM tbl_tipe_program");
$queryTingkat = mysqli_query($koneksi, "
    SELECT id_tingkat, 
           CASE 
               WHEN jenjang_program IN ('PAUD', 'TK') THEN jenjang_program
               ELSE CONCAT(kelas_program, ' ', jenjang_program) 
           END AS nama_tingkat
    FROM tbl_tingkat_program ORDER BY id_tingkat ASC
");
$queryKelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas_bimbel ORDER BY nama_kelas_bimbel ASC");
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Edit Siswa</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="murid.php">Murid</a></li>
                <li class="breadcrumb-item active">Edit Murid</li>
            </ol>
            <form action="proses-murid.php" method="POST" enctype="multipart/form-data">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-pen-to-square" style="padding-top: 10px;"></i> Edit Murid</span>
                        <button type="submit" name="update" class="btn btn-primary float-end"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <input type="hidden" name="id_murid" value="<?= $data['id_murid'] ?>">

                                <div class="mb-3 row">
                                    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                    <label for="nama" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="text" name="nama" required class="form-control border-0 border-bottom ps-2" value="<?= $data['nama_murid'] ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_program" class="col-sm-2 col-form-label">Program</label>
                                    <label for="id_program" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="id_program" id="id_program" class="form-select border-0 border-bottom" required>
                                            <option value="" selected>--Pilih Program--</option>
                                            <?php
                                            while ($d_program = mysqli_fetch_array($queryProgram)) {
                                                // Tambahkan 'selected' jika ID-nya cocok
                                                $selected = ($d_program['id_program'] == $data['id_program']) ? 'selected' : '';
                                                echo '<option value="' . $d_program['id_program'] . '" ' . $selected . '>' . $d_program['nama_program'] . '</option>';
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
                                            while ($d_tingkat = mysqli_fetch_array($queryTingkat)) {
                                                $selected = ($d_tingkat['id_tingkat'] == $data['id_tingkat']) ? 'selected' : '';
                                                echo '<option value="' . $d_tingkat['id_tingkat'] . '" ' . $selected . '>' . $d_tingkat['nama_tingkat'] . '</option>';
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
                                            while ($d_kelas = mysqli_fetch_array($queryKelas)) {
                                                $selected = ($d_kelas['id_kelas_bimbel'] == $data['id_kelas_bimbel']) ? 'selected' : '';
                                                echo '<option value="' . $d_kelas['id_kelas_bimbel'] . '" ' . $selected . '>' . $d_kelas['nama_kelas_bimbel'] . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                    <label for="alamat" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <textarea name="alamat" id="alamat" cols="30" rows="3" placeholder="Alamat Siswa" class="form-control" required><?= $data['alamat_murid'] ?></textarea>
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="id_user_ortu" class="col-sm-2 col-form-label">Orang Tua</label>
                                    <label for="id_user_ortu" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <select name="id_user_ortu" id="id_user_ortu" class="form-select border-0 border-bottom">
                                            <option value="">--Pilih Orang Tua--</option>
                                            <?php
                                            while ($ortu = mysqli_fetch_array($queryOrtu)) {
                                                // Cek apakah ID user ortu di tabel murid sama dengan ID di loop ini
                                                $selected = ($ortu['id'] == $data['id_user_ortu']) ? 'selected' : '';
                                                echo '<option value="' . $ortu['id'] . '" ' . $selected . '>' . $ortu['nama_user'] . ' (Username: ' . $ortu['username'] . ')</option>';
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