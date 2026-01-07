<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";
$title = "Murid - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Murid</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Murid</li>
            </ol>
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-users" style="padding-top: 10px;"></i> Data Murid</span>
                    <a href="<?= $main_url ?>admin/murid/add-murid.php" class="btn btn-primary float-end" title="Tambah Murid"><i class="fa-solid fa-plus"></i> Tambah Murid</a>
                </div>
                <div class="card-body">
                    <table class="table table-hover" id="datatablesSimple">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col"><center>Nama Murid</center></th>
                                <th scope="col"><center>Orang Tua</center></th>
                                <th scope="col"><center>Program</center></th>
                                <th scope="col"><center>Tingkat</center></th>
                                <th scope="col"><center>Kelas</center></th>
                                <th scope="col"><center>Alamat</center></th>
                                <th scope="col"><center>Aksi</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // QUERY UPDATE: Tambahkan JOIN ke tbl_user (alias u)
                            $querySiswa = mysqli_query($koneksi, "
                                SELECT m.id_murid, m.nama_murid, m.alamat_murid,
                                        tp.nama_program,
                                        kb.nama_kelas_bimbel,
                                        u.nama_user AS nama_ortu, 
                                        CASE 
                                            WHEN tkp.jenjang_program IN ('PAUD', 'TK') THEN tkp.jenjang_program
                                            WHEN tkp.id_tingkat IS NULL THEN NULL
                                            ELSE CONCAT(tkp.kelas_program, ' ', tkp.jenjang_program) 
                                        END AS nama_tingkat
                                FROM tbl_data_murid m
                                LEFT JOIN tbl_tipe_program tp ON m.id_program = tp.id_program
                                LEFT JOIN tbl_tingkat_program tkp ON m.id_tingkat = tkp.id_tingkat
                                LEFT JOIN tbl_kelas_bimbel kb ON m.id_kelas_bimbel = kb.id_kelas_bimbel
                                LEFT JOIN tbl_user u ON m.id_user_ortu = u.id  -- Join baru ke tbl_user
                            ");
                            
                            while ($data = mysqli_fetch_array($querySiswa)) { ?>
                            <tr>
                                <th scope="row"><?= $no++ ?></th>
                                <td><?= $data['nama_murid'] ?></td>
                                
                                <td>
                                    <?php 
                                        if($data['nama_ortu']) {
                                            echo $data['nama_ortu'];
                                        } else {
                                            echo '<span class="text-muted fst-italic">-Belum ada-</span>';
                                        }
                                    ?>
                                </td>
                                <td><?= $data['nama_program'] ?></td>
                                <td><?= $data['nama_tingkat'] ?></td>
                                <td>
                                    <?php 
                                        if($data['nama_kelas_bimbel']) {
                                            echo $data['nama_kelas_bimbel'];
                                        } else {
                                            echo '<span class="text-muted fst-italic">-Belum ada-</span>';
                                        }
                                    ?>
                                </td>
                                <td><?= $data['alamat_murid'] ?></td>
                                <td>
                                    <a href="edit-murid.php?id_murid=<?= $data['id_murid'] ?>" class="btn btn-sm btn-warning" title="Update Siswa"><i class="fa-solid fa-pen text-white"></i></a>
                                    <a href="hapus-murid.php?id_murid=<?= $data['id_murid'] ?>" class="btn btn-sm btn-danger" title="Hapus Siswa" onclick="return confirm('Anda yakin akan menghapus data ini ?')"><i class="fa-solid fa-trash"></i></a>
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