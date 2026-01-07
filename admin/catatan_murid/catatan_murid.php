<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php"; 

// 1. Mengubah Judul Halaman
$title = "Catatan Murid - Ruang Les by Ismaturrohmah"; 

// Path ini sudah benar, mengarah ke admin/template/
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Catatan Perkembangan Murid</h1> 
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Catatan Murid</li>
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
                            <th scope="col"><center>Tingkat</center></th> 
                            <th scope="col"><center>Aksi</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Query ini mengambil data KELAS, persis seperti di absensi.php
                            // Ini sudah benar untuk halaman daftar kelas.
                            $queryKelas = mysqli_query($koneksi, "
                                SELECT 
                                    kb.id_kelas_bimbel, 
                                    kb.nama_kelas_bimbel, 
                                    tp.nama_program,
                                    CASE 
                                        WHEN tkp.jenjang_program IN ('PAUD', 'TK') THEN tkp.jenjang_program
                                        WHEN tkp.id_tingkat IS NULL THEN 'Umum'
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
                                <td><?= $data['nama_tingkat'] ?></td> 
                                <td align="center">
                                    <a href="detail-catatan.php?id_kelas=<?= $data['id_kelas_bimbel'] ?>" class="btn btn-sm btn-success" title="Lihat Catatan Murid">
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
</div>
<?php
// Path ini sudah benar
require_once "../template/footer_admin.php";
?>