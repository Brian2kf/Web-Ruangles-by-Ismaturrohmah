<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Tambah Pengajar - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Tambah Pengajar</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="pengajar.php">Pengajar</a></li>
                <li class="breadcrumb-item active">Tambah Pengajar</li>
            </ol>
            <form action="proses-pengajar.php" method="POST">
                <div class="card">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-user-plus" style="padding-top: 10px;"></i> Tambah Pengajar</span>
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
                                        <input type="text" name="nama" required class="form-control border-0 border-bottom ps-2" placeholder="Masukkan Nama Pengajar">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="no_telepon" class="col-sm-2 col-form-label">No. Telepon</label>
                                    <label for="no_telepon" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="text" name="no_telepon" required class="form-control border-0 border-bottom ps-2" placeholder="Masukkan No. Telepon (cth: 0812...)">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <label for="email" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <input type="email" name="email" required class="form-control border-0 border-bottom ps-2" placeholder="Masukkan Email (cth: email@contoh.com)">
                                    </div>
                                </div>
                                <div class="mb-3 row">
                                    <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                    <label for="alamat" class="col-sm-1 col-form-label">:</label>
                                    <div class="col-sm-9" style="margin-left: -40px;">
                                        <textarea name="alamat" id="alamat" cols="30" rows="3" placeholder="Alamat Pengajar" class="form-control" required></textarea>
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