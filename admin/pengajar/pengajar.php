<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";
$title = "Pengajar - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Pengajar</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Pengajar</li>
            </ol>
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-users" style="padding-top: 10px;"></i> Data Pengajar</span>
                    <a href="<?= $main_url ?>admin/pengajar/add-pengajar.php" class="btn btn-primary float-end" title="Tambah Pengajar"><i class="fa-solid fa-plus"></i> Tambah Pengajar</a>
                </div>
                <div class="card-body">
                    <table class="table table-hover" id="datatablesSimple">
                        <thead>
                            <tr>
                            <th scope="col">No</th>
                            <th scope="col"><center>Nama</center></th>
                            <th scope="col"><center>No. Telepon</center></th>
                            <th scope="col"><center>Email</center></th>
                            <th scope="col"><center>Alamat</center></th>
                            <th scope="col"><center>Aksi</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Mengambil data dari tbl_data_pengajar
                            $queryPengajar = mysqli_query($koneksi, "SELECT * FROM tbl_data_pengajar");
                            while ($data = mysqli_fetch_array($queryPengajar)) { ?>
                            <tr>
                                <th scope="row"><?= $no++ ?></th>
                                <td><?= $data['nama_pengajar'] ?></td>
                                <td><?= $data['no_telepon'] ?></td>
                                <td><?= $data['email'] ?></td>
                                <td><?= $data['alamat_pengajar'] ?></td>
                                <td>
                                    <a href="edit-pengajar.php?id_pengajar=<?= $data['id_pengajar'] ?>" class="btn btn-sm btn-warning" title="Update Pengajar"><i class="fa-solid fa-pen text-white"></i></a>
                                    <a href="hapus-pengajar.php?id_pengajar=<?= $data['id_pengajar'] ?>" class="btn btn-sm btn-danger" title="Hapus Pengajar" onclick="return confirm('Anda yakin akan menghapus data ini ?')"><i class="fa-solid fa-trash"></i></a>
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