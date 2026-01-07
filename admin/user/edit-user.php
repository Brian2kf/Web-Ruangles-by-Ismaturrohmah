<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Edit Pengguna - Ruang Les";

require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Ambil ID dari URL
$id = $_GET['id'];

// Ambil data user berdasarkan ID
$queryUser = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE id = $id");
$data = mysqli_fetch_array($queryUser);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan'); window.location='kelola_pengguna.php';</script>";
    exit();
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Edit Pengguna</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="kelola_pengguna.php">Kelola Pengguna</a></li>
                <li class="breadcrumb-item active">Edit Pengguna</li>
            </ol>

            <form action="proses-user.php" method="POST">
                <div class="card mb-4">
                    <div class="card-header">
                        <span class="h5 my-2"><i class="fa-solid fa-pen-to-square" style="padding-top: 10px;"></i> Form Edit Pengguna</span>
                        <button type="submit" name="update" class="btn btn-primary float-end"><i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan</button>
                        <button type="reset" name="reset" class="btn btn-danger float-end me-2"><i class="fa-solid fa-xmark"></i> Reset</button>
                    </div>
                    <div class="card-body">
                        <input type="hidden" name="id" value="<?= $data['id'] ?>">
                        
                        <div class="mb-3 row">
                            <label for="username" class="col-sm-2 col-form-label">Username</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="username" value="<?= $data['username'] ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3 row">
                            <label for="nama_user" class="col-sm-2 col-form-label">Nama Lengkap</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="nama_user" name="nama_user" value="<?= $data['nama_user'] ?>" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="password" class="col-sm-2 col-form-label">Password</label>
                            <div class="col-sm-10">
                                <input type="password" class="form-control" id="password" name="password" placeholder="Kosongkan jika tidak ingin mengganti password">
                                <small class="text-muted">*Isi hanya jika ingin mengubah password.</small>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="email_user" class="col-sm-2 col-form-label">Email</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email_user" name="email_user" value="<?= $data['email_user'] ?>" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="telepon" class="col-sm-2 col-form-label">Telepon</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="telepon" name="telepon" value="<?= $data['telepon'] ?>" required>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="alamat" class="col-sm-2 col-form-label">Alamat</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= $data['alamat'] ?></textarea>
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="role" class="col-sm-2 col-form-label">Role</label>
                            <div class="col-sm-10">
                                <select name="role" id="role" class="form-control" required>
                                    <option value="1" <?= ($data['role'] == '1') ? 'selected' : '' ?>>Orang Tua</option>
                                    <option value="2" <?= ($data['role'] == '2') ? 'selected' : '' ?>>Admin</option>
                                    <option value="3" <?= ($data['role'] == '3') ? 'selected' : '' ?>>Pengajar</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>

<?php
require_once "../template/footer_admin.php";
?>