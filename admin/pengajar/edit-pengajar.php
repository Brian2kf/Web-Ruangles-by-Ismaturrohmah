<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Edit Pengajar - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// 1. Ambil ID Pengajar dari URL
if (!isset($_GET['id_pengajar'])) {
    echo "<script>
            alert('ID pengajar tidak ditemukan.');
            document.location.href = 'pengajar.php';
          </script>";
    exit();
}
$id_pengajar = (int)$_GET['id_pengajar'];

// 2. Query untuk mengambil data pengajar yang spesifik
$queryPengajar = mysqli_query($koneksi, "SELECT * FROM tbl_data_pengajar WHERE id_pengajar = $id_pengajar");
$data = mysqli_fetch_array($queryPengajar);

// Jika data tidak ditemukan
if ($data == null) {
     echo "<script>
            alert('Data pengajar tidak ditemukan.');
            document.location.href = 'pengajar.php';
          </script>";
    exit();
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Edit Pengajar</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="pengajar.php">Pengajar</a></li>
                <li class="breadcrumb-item active">Edit Pengajar</li>
            </ol>
            <form action="proses-pengajar.php" method="POST">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-pen-to-square" style="padding-top: 10px;"></i> Edit Pengajar</span>
                        <button type="submit" name="update" class="btn btn-primary float-end"><i class="fa-solid fa-floppy-disk"></i> Update</button>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-8">
                                <input type="hidden" name="id_pengajar" value="<?= $data['id_pengajar'] ?>">

                                <div class="mb-3 row">
                                    <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                    <label for="nama" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="text" name="nama" required class="form-control border-0 border-bottom ps-2" value="<?= $data['nama_pengajar'] ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="no_telepon" class="col-sm-2 col-form-label">No. Telepon</label>
                                    <label for="no_telepon" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="text" name="no_telepon" required class="form-control border-0 border-bottom ps-2" value="<?= $data['no_telepon'] ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <label for="email" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="email" name="email" required class="form-control border-0 border-bottom ps-2" value="<?= $data['email'] ?>">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                    <label for="alamat" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <textarea name="alamat" id="alamat" cols="30" rows="3" class="form-control" required><?= $data['alamat_pengajar'] ?></textarea>
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