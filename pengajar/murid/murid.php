<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";
$title = "Murid - Ruang Les by Ismaturrohmah";
require_once "../template/header_pengajar.php";
require_once "../template/navbar_pengajar.php";
require_once "../template/sidebar_pengajar.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Murid</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_pengajar.php">Home</a></li>
                <li class="breadcrumb-item active">Murid</li>
            </ol>
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-users" style="padding-top: 10px;"></i> Data Murid</span>
                </div>
                <div class="card-body">
                    <table class="table table-hover" id="datatablesSimple">
                        <thead>
                            <tr>
                            <th scope="col">No</th>
                            <th scope="col"><center>Nama</center></th>
                            <th scope="col"><center>Program</center></th>
                            <th scope="col"><center>Tingkat</center></th>
                            <th scope="col"><center>Kelas</center></th>
                            <th scope="col"><center>Alamat</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Kueri SQL diubah untuk menggabungkan tabel
                            $querySiswa = mysqli_query($koneksi, "
                                SELECT m.id_murid, m.nama_murid, m.alamat_murid,
                                       tp.nama_program,
                                       kb.nama_kelas_bimbel,
                                       CASE 
                                           WHEN tkp.jenjang_program IN ('PAUD', 'TK') THEN tkp.jenjang_program
                                           WHEN tkp.id_tingkat IS NULL THEN NULL
                                           ELSE CONCAT(tkp.kelas_program, ' ', tkp.jenjang_program) 
                                       END AS nama_tingkat
                                FROM tbl_data_murid m
                                LEFT JOIN tbl_tipe_program tp ON m.id_program = tp.id_program
                                LEFT JOIN tbl_tingkat_program tkp ON m.id_tingkat = tkp.id_tingkat
                                LEFT JOIN tbl_kelas_bimbel kb ON m.id_kelas_bimbel = kb.id_kelas_bimbel
                            ");
                            while ($data = mysqli_fetch_array($querySiswa)) { ?>
                            <tr>
                                <th scope="row"><?= $no++ ?></th>
                                <td><?= $data['nama_murid'] ?></td>
                                <td><?= $data['nama_program'] ?></td>
                                <td><?= $data['nama_tingkat'] ?></td>
                                <td><?= $data['nama_kelas_bimbel'] ?></td>
                                <td><?= $data['alamat_murid'] ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

<?php
require_once "../template/footer_pengajar.php";
?>