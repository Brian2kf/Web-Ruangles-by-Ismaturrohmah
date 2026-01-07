<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// 1. Ambil ID Kelas dari URL dan validasi
if (!isset($_GET['id_kelas'])) {
    echo "<script>alert('ID kelas tidak ditemukan.'); document.location.href = 'absensi.php';</script>";
    exit();
}
$id_kelas = (int)$_GET['id_kelas'];

// 2. Ambil data nama kelas untuk judul
$queryKelas = mysqli_query($koneksi, "SELECT nama_kelas_bimbel FROM tbl_kelas_bimbel WHERE id_kelas_bimbel = $id_kelas");
$dataKelas = mysqli_fetch_array($queryKelas);
if ($dataKelas == null) {
     echo "<script>alert('Data kelas tidak ditemukan.'); document.location.href = 'absensi.php';</script>";
    exit();
}
$nama_kelas = $dataKelas['nama_kelas_bimbel'];
$title = "Riwayat Absensi - $nama_kelas"; // Set title dinamis

// Panggil template
require_once "../template/header_pengajar.php";
require_once "../template/navbar_pengajar.php";
require_once "../template/sidebar_pengajar.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Riwayat Absensi</h1>
            <h5 class="mb-4 text-muted"><?= $nama_kelas ?></h5>
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_pengajar.php">Home</a></li>
                <li class="breadcrumb-item"><a href="absensi.php">Absensi</a></li>
                <li class="breadcrumb-item active">Riwayat Absensi</li>
            </ol>
            
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-table" style="padding-top: 10px;"></i> Data Absensi Murid</span>
                    <a href="add-absensi.php?id_kelas=<?= $id_kelas ?>" class="btn btn-primary btn float-end me-1"><i class="fa-solid fa-plus"></i> Input Absensi Harian</a>
                </div>
                <div class="card-body">
                    <table class="table table-hover" id="datatablesSimple">
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Nama Murid</th>
                                <th scope="col">Tanggal</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // 3. Query untuk mengambil riwayat absensi
                            $queryRiwayat = mysqli_query($koneksi, 
                                "SELECT 
                                    a.id_absensi,
                                    m.nama_murid,
                                    a.tgl_absensi,
                                    a.status_absensi
                                FROM tbl_absensi a
                                JOIN tbl_data_murid m ON a.id_murid = m.id_murid
                                WHERE a.id_kelas_bimbel = $id_kelas
                                ORDER BY a.tgl_absensi DESC, m.nama_murid ASC"
                            );

                            $jumlah_data = mysqli_num_rows($queryRiwayat);
                            if ($jumlah_data > 0) {
                                while ($data = mysqli_fetch_array($queryRiwayat)) {
                            ?>
                            <tr>
                                <th scope="row"><?= $no++ ?></th>
                                <td><?= $data['nama_murid'] ?></td>
                                <td><?= date('d M Y', strtotime($data['tgl_absensi'])) ?></td>
                                <td>
                                    <?php
                                    // Beri warna status (badge) agar mudah dibaca
                                    $status = $data['status_absensi'];
                                    $badge_color = 'bg-success'; // Default Hadir
                                    if ($status == 'Izin') {
                                        $badge_color = 'bg-warning text-white';
                                    } elseif ($status == 'Sakit') {
                                        $badge_color = 'bg-info text-white';
                                    } elseif ($status == 'Alpa') {
                                        $badge_color = 'bg-danger';
                                    }
                                    echo '<span class="badge ' . $badge_color . '">' . $status . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="edit-absensi.php?id_absensi=<?= $data['id_absensi'] ?>&id_kelas=<?= $id_kelas ?>" class="btn btn-sm btn-warning" title="Edit Absensi"><i class="fa-solid fa-pen text-white"></i></a>
                                    <a href="hapus-absensi.php?id_absensi=<?= $data['id_absensi'] ?>&id_kelas=<?= $id_kelas ?>" class="btn btn-sm btn-danger" title="Hapus Absensi" onclick="return confirm('Anda yakin akan menghapus data absensi ini ?')"><i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php 
                                } // Akhir while loop
                            } else { // Jika tidak ada data
                                echo '<tr><td colspan="5" class="text-center">Belum ada data absensi untuk kelas ini.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

<?php
require_once "../template/footer_pengajar.php";
?>