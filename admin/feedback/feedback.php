<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Daftar Feedback - Ruang Les";

require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

if (isset($_GET['msg'])) {
    $msg = $_GET['msg'];
} else {
    $msg = "";
}

$alert = "";
if ($msg == 'replied') {
    $alert = '<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fa-solid fa-check"></i> Balasan berhasil dikirim.
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  </div>';
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Feedback Orang Tua</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Feedback</li>
            </ol>

            <?= $alert ?>

            <div class="card mb-4">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-list" style="padding-top: 10px;"></i> Daftar Pesan Masuk</span>
                </div>
                <div class="card-body">
                    <table id="datatablesSimple" class="table table-hover">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th><center>Tanggal</center></th>
                                <th><center>Nama Orang Tua</center></th>
                                <th><center>Ditujukan Untuk</center></th>
                                <th><center>Pesan Awal</center></th>
                                <th><center>Status</center></th>
                                <th><center>Aksi</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            // MENYESUAIKAN QUERY DENGAN STRUKTUR DATABASE ANDA
                            // Menggunakan f.id_orgtua_wali bukan f.id_user
                            $query = "SELECT f.*, u.nama_user 
                                      FROM tbl_feedback f 
                                      JOIN tbl_user u ON f.id_orgtua_wali = u.id 
                                      ORDER BY f.id_feedback DESC"; 
                            $result = mysqli_query($koneksi, $query);

                            while ($data = mysqli_fetch_array($result)) {
                                // Potong pesan agar rapi
                                $pesan_pendek = substr($data['isi_feedback'], 0, 100) . '...';
                                
                                // Jika tgl_feedback masih NULL (data lama), tampilkan strip
                                $tanggal = ($data['tgl_feedback']) ? date('d-m-Y', strtotime($data['tgl_feedback'])) : '-';

                                // Cek status
                                if (!empty($data['balasan'])) {
                                    $status = '<span class="badge bg-success">Sudah Dibalas</span>';
                                } else {
                                    $status = '<span class="badge bg-warning text-white">Belum Dibalas</span>';
                                }
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= $tanggal ?></td>
                                    <td><?= $data['nama_user'] ?></td>
                                    <td><?= $data['nama_tujuan'] ?></td>
                                    <td><?= $pesan_pendek ?></td>
                                    <td><?= $status ?></td>
                                    <td>
                                        <a href="detail_feedback.php?id=<?= $data['id_feedback'] ?>" class="btn btn-success btn-sm">
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
require_once "../template/footer_admin.php";
?>