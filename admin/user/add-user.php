<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";
$title = "Tambah User - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";   

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
} else {
    $msg = '';
}

$alert = '';
if ($msg == 'cancel') {
    $alert = '<div class="alert alert-warning alert-dismissible fade show" id="cancel" role="alert">
    <i class="fa-solid fa-xmark"></i> Tambah user gagal, username sudah ada!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}

if ($msg == 'added') {
    $alert = '<div class="alert alert-success alert-dismissible fade show" id="added" role="alert">
    <i class="fa-solid fa-circle-check"></i> Tambah user berhasil, silahkan ganti password anda!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Tambah Pengguna</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="<?= $main_url ?>admin/dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Tambah Pengguna</li>
            </ol>
            <form action="proses-user.php" method="POST" enctype="multipart/form-data">
            <?php
                if ($msg !== '') {
                    echo $alert;
                }
            ?>
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-square-plus" style="padding-top: 10px;"></i> Tambah Pengguna</span>
                    <button type="submit" name="simpan" class="btn btn-primary float-end"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                    <button type="reset" name="reset" class="btn btn-danger float-end me-2"><i class="fa-solid fa-xmark"></i> Reset</button>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <div class="mb-3 row">
                                <label for="username" class="col-sm-2 col-form-label">Username</label>
                                <label for="" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                <input type="text" pattern="[A-Za-z0-9]{3,}" title="Minimal 3 karakter kombinasi huruf besar huruf kecil dan angka" class="form-control border-0 border-bottom" id="username" name="username" maxlength="20" required>
                                </div>
                            </div>                  
                            <div class="mb-3 row">
                                <label for="nama_user" class="col-sm-2 col-form-label">Nama</label>
                                <label for="" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                <input type="text" class="form-control border-0 border-bottom" id="nama_user" name="nama_user" maxlength="128" required>  
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="email_user" class="col-sm-2 col-form-label">Email</label>
                                <label for="" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                <input type="email" class="form-control border-0 border-bottom" id="email_user" name="email_user" maxlength="100" required>  
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="telepon" class="col-sm-2 col-form-label">Telepon</label>
                                <label for="" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                <input type="tel" class="form-control border-0 border-bottom" id="telepon" name="telepon" maxlength="30" required>  
                                </div>
                            </div>                            
                            <div class="mb-3 row">
                                <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                <label for="" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <textarea name="alamat" id="alamat" cols="30" rows="3" class="form-control" placeholder="Domisili" required></textarea>
                                </div>
                            </div>                            
                            <div class="mb-3 row">
                                <label for="role" class="col-sm-2 col-form-label">Role</label>
                                <label for="" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <select name="role" id="role" class="form-select border-0 border-bottom" required>
                                        <option value="" selected>--Pilih Role--</option>
                                        <option value="1">Orang Tua</option>
                                        <option value="2">Admin</option>
                                        <option value="3">Pengajar</option>
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
require_once "../template/footer_admin.php"
?>
