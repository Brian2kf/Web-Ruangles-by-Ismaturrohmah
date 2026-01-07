<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

if (!isset($_GET['id_kelas'])) {
    echo "<script>alert('ID kelas tidak ditemukan.'); document.location.href = 'catatan_murid.php';</script>";
    exit();
}
$id_kelas = (int)$_GET['id_kelas'];

$queryKelas = mysqli_query($koneksi, "SELECT nama_kelas_bimbel FROM tbl_kelas_bimbel WHERE id_kelas_bimbel = $id_kelas");
$dataKelas = mysqli_fetch_array($queryKelas);
if ($dataKelas == null) {
     echo "<script>alert('Data kelas tidak ditemukan.'); document.location.href = 'catatan_murid.php';</script>";
    exit();
}
$nama_kelas = $dataKelas['nama_kelas_bimbel'];
$title = "Detail Catatan - $nama_kelas"; 

require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Catatan Perkembangan Murid</h1>
            <h5 class="mb-4 text-muted"><?= $nama_kelas ?></h5>
            
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="catatan_murid.php">Catatan Murid</a></li>
                <li class="breadcrumb-item active">Detail Catatan</li>
            </ol>
            
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-file-signature" style="padding-top: 10px;"></i> Data Catatan</span>
                    <a href="add-catatan.php?id_kelas=<?= $id_kelas ?>" class="btn btn-primary btn float-end me-1"><i class="fa-solid fa-plus"></i> Tambah Catatan</a>
                </div>
                <div class="card-body">
                    <table class="table table-hover" id="datatablesSimple">
                        <thead>
                            <tr>
                                <th scope="col" style="width: 5%;">No</th>
                                <th scope="col" style="width: 15%;">Nama Murid</th>
                                <th scope="col" style="width: 15%;">Nama Pengajar</th> 
                                <th scope="col" style="width: 15%;">Mata Pelajaran</th>
                                <th scope="col" style="width: 15%;">Materi</th>
                                <th scope="col" style="width: 20%;">Catatan</th>
                                <th scope="col" style="width: 15%;"><center>Aksi</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // 2. QUERY DIPERBARUI DENGAN JOIN KE TBL_DATA_PENGAJAR
                            $queryProgres = mysqli_query($koneksi, 
                                "SELECT 
                                    p.id_progres,
                                    m.nama_murid,
                                    peng.nama_pengajar, -- Mengambil nama pengajar
                                    p.mata_pelajaran,
                                    p.materi,
                                    p.isi_progres
                                FROM tbl_progres p
                                JOIN tbl_data_murid m ON p.id_murid = m.id_murid
                                -- Menggunakan LEFT JOIN jika ada kemungkinan id_pengajar NULL
                                LEFT JOIN tbl_data_pengajar peng ON p.id_pengajar = peng.id_pengajar
                                WHERE p.id_kelas_bimbel = $id_kelas
                                ORDER BY m.nama_murid ASC, p.id_progres DESC"
                            );

                            $jumlah_data = mysqli_num_rows($queryProgres);
                            if ($jumlah_data > 0) {
                                while ($data = mysqli_fetch_array($queryProgres)) {
                            ?>
                            <tr>
                                <th scope="row"><?= $no++ ?></th>
                                <td><?= $data['nama_murid'] ?></td>
                                <td><?= $data['nama_pengajar'] ?? 'N/A' ?></td> 
                                <td><?= $data['mata_pelajaran'] ?></td>
                                <td><?= $data['materi'] ?></td>
                                <td><?= $data['isi_progres'] ?></td>
                                <td>
                                    <a href="edit-catatan.php?id_progres=<?= $data['id_progres'] ?>&id_kelas=<?= $id_kelas ?>" class="btn btn-sm btn-warning text-white" title="Edit Catatan"><i class="fa-solid fa-pen text-white"></i></a>
                                    <a href="hapus-catatan.php?id_progres=<?= $data['id_progres'] ?>&id_kelas=<?= $id_kelas ?>" class="btn btn-sm btn-danger" title="Hapus Catatan" onclick="return confirm('Anda yakin akan menghapus data catatan ini?')"> <i class="fa-solid fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php 
                                } 
                            } else { 
                                // 4. Colspan diubah menjadi 7
                                echo '<tr><td colspan="7" class="text-center">Belum ada data catatan perkembangan untuk kelas ini.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>
<?php
require_once "../template/footer_admin.php";
?>