<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Data Pendaftar - Ruang Les";

require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
} else {
    $msg = "";
}

$alert = "";
if ($msg == 'accepted') {
    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-check"></i> Pendaftar berhasil disetujui.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
} else if ($msg == 'rejected') {
    $alert = '<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-check"></i> Pendaftar telah ditolak.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Data Pendaftar Baru</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Pendaftar</li>
            </ol>

            <?= $alert ?>

            <div class="card mb-4">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-user-plus" style="padding-top: 10px;"></i> Daftar Calon Murid</span>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th><center>Nama Anak</center></th>
                                <th><center>Tingkat</center></th>
                                <th><center>Program Pilihan</center></th>
                                <th><center>Nama Orang Tua</center></th>
                                <th><center>Status</center></th>
                                <th><center>Aksi</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // GABUNGKAN (JOIN) dengan tbl_tingkat_program
                            $query = "SELECT p.*, tp.nama_program, ttp.jenjang_program, ttp.kelas_program 
                                    FROM tbl_pendaftaran p
                                    LEFT JOIN tbl_tipe_program tp ON p.id_program = tp.id_program
                                    LEFT JOIN tbl_tingkat_program ttp ON p.id_tingkat = ttp.id_tingkat
                                    ORDER BY p.id_pendaftaran DESC";
                            $result = mysqli_query($koneksi, $query);

                            while ($data = mysqli_fetch_array($result)) {
                                // Styling Status Badge
                                if ($data['status_pendaftaran'] == 'Pending') {
                                    $badge = 'bg-warning text-white';
                                } elseif ($data['status_pendaftaran'] == 'Diterima') {
                                    $badge = 'bg-success';
                                } else {
                                    $badge = 'bg-danger';
                                }
                                
                                // Format Tampilan Kelas (Contoh: 1 SD atau A TK)
                                // Gunakan operator null coalescing (??) untuk mencegah error jika data kosong
                                $info_kelas = ($data['kelas_program'] ?? '-') . ' ' . ($data['jenjang_program'] ?? '');
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $data['nama_camur'] ?></td>
                                    <td><?= $info_kelas ?></td> 
                                    <td><?= $data['nama_program'] ?></td>
                                    <td><?= $data['nama_orgtua_wali'] ?></td>
                                    <td><span class="badge <?= $badge ?>"><?= $data['status_pendaftaran'] ?></span></td>
                                    <td>
                                        <a href="detail_pendaftar.php?id=<?= $data['id_pendaftaran'] ?>" class="btn btn-success btn-sm text-white" title="Lihat Detail">
                                            <i class="fa-solid fa-eye"></i> Detail
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
require_once "../template/footer_admin.php";
?>