<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";
$title = "Absensi - Ruang Les by Ismaturrohmah";
require_once "../template/header_pengajar.php";
require_once "../template/navbar_pengajar.php";
require_once "../template/sidebar_pengajar.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Absensi</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_pengajar.php">Home</a></li>
                <li class="breadcrumb-item active">Absensi</li>
            </ol>
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-clipboard-list" style="padding-top: 10px;"></i> Daftar Kelas</span>
                </div>
                <div class="card-body">
                    <table class="table table-hover" id="datatablesSimple">
                        <thead>
                            <tr>
                            <th scope="col">No</th>
                            <th scope="col"><center>Nama Kelas</center></th>
                            <th scope="col"><center>Program Les</center></th>
                            <th scope="col"><center>Tingkat</center></th>
                            <th scope="col"><center>Aksi</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Query untuk menggabungkan 3 tabel (sama seperti di kelas.php)
                            $queryKelas = mysqli_query($koneksi, "
                                SELECT 
                                    kb.id_kelas_bimbel, 
                                    kb.nama_kelas_bimbel, 
                                    tp.nama_program,
                                    CASE 
                                        WHEN tkp.jenjang_program IN ('PAUD', 'TK') THEN tkp.jenjang_program
                                        WHEN tkp.id_tingkat IS NULL THEN NULL
                                        ELSE CONCAT(tkp.kelas_program, ' ', tkp.jenjang_program) 
                                    END AS nama_tingkat
                                FROM tbl_kelas_bimbel kb
                                LEFT JOIN tbl_tipe_program tp ON kb.id_program = tp.id_program
                                LEFT JOIN tbl_tingkat_program tkp ON kb.id_tingkat = tkp.id_tingkat
                                ORDER BY kb.nama_kelas_bimbel ASC
                            ");
                            
                            while ($data = mysqli_fetch_array($queryKelas)) { 
                            ?>
                            <tr>
                                <th scope="row"><?= $no++ ?></th>
                                <td><?= $data['nama_kelas_bimbel'] ?></td>
                                <td><?= $data['nama_program'] ?></td>
                                <td><?= $data['nama_tingkat'] ?></td>
                                <td align="center">
                                    <a href="detail-absensi.php?id_kelas=<?= $data['id_kelas_bimbel'] ?>" class="btn btn-sm btn-success text-white" title="Lihat Detail Absensi">
                                        <i class="fa-solid fa-eye"></i> Lihat Detail
                                    </a>
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
require_once "../template/footer_pengajar.php";
?>