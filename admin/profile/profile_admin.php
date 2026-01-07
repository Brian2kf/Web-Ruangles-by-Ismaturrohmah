<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";
$title = "Kelola Profile - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";   

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
} else {
    $msg = '';
}

$alert = '';
if ($msg == 'updated') {
    $alert = '<div class="alert alert-success alert-dismissible fade show" id="updated" role="alert">
    <i class="fa-solid fa-circle-check"></i> Data profil berhasil diperbaharui!
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
// Tambahkan alert untuk error
if ($msg == 'oldpass_empty') {
    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-triangle-exclamation"></i> Gagal! Password lama harus diisi jika ingin mengganti password.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
if ($msg == 'oldpass_wrong') {
    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-triangle-exclamation"></i> Gagal! Password lama yang Anda masukkan salah.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}
if ($msg == 'username_exists') {
    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-triangle-exclamation"></i> Gagal! Username tersebut sudah digunakan oleh akun lain.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
}


$username = $_SESSION["ssUser"];
$queryuser = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE username = '$username'");
$profile = mysqli_fetch_array($queryuser);
?>
<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Kelola Akun</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="<?= $main_url ?>admin/dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Kelola Akun</li>
            </ol>
            <?php
                // Tampilkan alert jika ada
                if ($msg !== '') {
                    echo $alert;
                }
            ?>
            <form action="proses_profile.php" method="POST">
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-user" style="padding-top: 10px;"></i> Informasi Akun</span>
                    <button type="submit" name="simpan" class="btn btn-primary float-end"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
                </div>

                <div class="card-body">
                    <div class="row">
                        <div class="col-8">
                            <input type="hidden" name="id" value="<?= $profile['id'] ?>">
                            <div class="mb-3 row">
                                <label for="nama" class="col-sm-2 col-form-label">Username</label>
                                <label for="nama" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <input type="text" class="form-control border-0 border-bottom" id="username" name="username" value="<?= $profile['username'] ?>" placeholder="Username" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="nama" class="col-sm-2 col-form-label">Nama</label>
                                <label for="nama" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <input type="text" class="form-control border-0 border-bottom" id="nama_user" name="nama_user" value="<?= $profile['nama_user'] ?>" placeholder="Nama" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="email" class="col-sm-2 col-form-label">Email</label>
                                <label for="email" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <input type="email" class="form-control border-0 border-bottom" id="email_user" name="email_user" value="<?= $profile['email_user'] ?>" placeholder="Email" required>
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="telepon" class="col-sm-2 col-form-label">Telepon</label>
                                <label for="telepon" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <input type="tel" class="form-control border-0 border-bottom" id="telepon" name="telepon" value="<?= $profile['telepon'] ?>" placeholder="Telepon" required>
                                </div>
                            </div>                            
                            <div class="mb-3 row">
                                <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                                <label for="alamat" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <textarea name="alamat" id="alamat" clos="30" rows="3" class="form-control" required><?= $profile['alamat'] ?></textarea>
                                </div>
                            </div>
                            <hr>
                            <div class="mb-3 row">
                                <label for="oldPass" class="col-sm-2 col-form-label">Password Lama</label>
                                <label for="oldPass" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <input type="password" class="form-control border-0 border-bottom" id="oldPass" name="oldPass" placeholder="Kosongkan jika tidak ganti password">
                                </div>
                            </div>
                            <div class="mb-3 row">
                                <label for="newPass" class="col-sm-2 col-form-label">Password Baru</label>
                                <label for="newPass" class="col-sm-1 col-form-label">:</label>
                                <div class="col-sm-9" style="margin-left: -40px;">
                                    <input type="password" class="form-control border-0 border-bottom" id="newPass" name="newPass"  placeholder="Kosongkan jika tidak ganti password">
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