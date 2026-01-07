<?php
session_start();

// Cek Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Kelola Pengguna - Ruang Les";

require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Konfigurasi Alert Pesan
if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
} else {
    $msg = "";
}

$alert = "";
if ($msg == 'deleted') {
    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-check"></i> Data pengguna berhasil dihapus.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
} else if ($msg == 'updated') {
    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-check"></i> Data pengguna berhasil diperbarui.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
} else if ($msg == 'cancel') {
    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-xmark"></i> Data pengguna gagal diperbarui.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Kelola Pengguna</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Kelola Pengguna</li>
            </ol>

            <?php if ($msg != "") { echo $alert; } ?>

            <div class="card mb-4">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-users" style="padding-top: 10px;"></i> Data Pengguna</span>
                    <a href="add-user.php" class="btn btn-primary float-end"><i class="fa-solid fa-plus"></i> Tambah Pengguna</a>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Telepon</th>
                                <th>Alamat</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // Query mengambil semua data user
                            $queryUser = mysqli_query($koneksi, "SELECT * FROM tbl_user ORDER BY id DESC");
                            while ($data = mysqli_fetch_array($queryUser)) {
                                // Logika sederhana untuk menampilkan Nama Role
                                $role = "";
                                if($data['role'] == '1'){
                                    $role = "Orang Tua";
                                } else if($data['role'] == '2'){
                                    $role = "Admin"; // Sesuaikan dengan kode role di db kamu
                                } else if($data['role'] == '3'){
                                    $role = "Pengajar";
                                } else {
                                    $role = "User";
                                }
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $data['username'] ?></td>
                                    <td><?= $data['nama_user'] ?></td>
                                    <td><?= $data['email_user'] ?></td>
                                    <td><?= $data['telepon'] ?></td>
                                    <td><?= $data['alamat'] ?></td>
                                    <td><span class="badge bg-secondary"><?= $role ?></span></td>
                                    <td>
                                        <a href="edit-user.php?id=<?= $data['id'] ?>" class="btn btn-warning btn-sm" title="Edit"><i class="fa-solid fa-pen text-white"></i></a>
                                        
                                        <a href="proses-user.php?id=<?= $data['id'] ?>&aksi=hapus" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')"><i class="fa-solid fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

<?php
require_once "../template/footer_admin.php";
?>