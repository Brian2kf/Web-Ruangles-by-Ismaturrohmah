<?php
session_start();

// Cek Login & Role
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
require_once "../../admin/pembayaran/fungsi_invoice.php"; // Reuse fungsi helper admin

$title = "Riwayat Pembayaran - Ruang Les";
require_once "../template/header_ortu.php";
require_once "../template/navbar_ortu.php";
require_once "../template/sidebar_ortu.php";

// --- LOGIKA PILIH ANAK ---
$id_ortu = $_SESSION["ssId"];
$query_anak = mysqli_query($koneksi, "SELECT * FROM tbl_data_murid WHERE id_user_ortu = '$id_ortu'");
$daftar_anak = [];
while ($row = mysqli_fetch_assoc($query_anak)) {
    $daftar_anak[] = $row;
}

if (empty($daftar_anak)) {
    echo "<script>window.location='../dashboard_orangtua.php';</script>";
    exit;
}

// Set Anak Aktif
if (isset($_GET['id_murid'])) {
    $id_murid_aktif = $_GET['id_murid'];
    $valid_anak = false;
    foreach($daftar_anak as $anak) {
        if($anak['id_murid'] == $id_murid_aktif) {
            $valid_anak = true; 
            $nama_anak_aktif = $anak['nama_murid'];
            break; 
        }
    }
    if(!$valid_anak) {
        $id_murid_aktif = $daftar_anak[0]['id_murid'];
        $nama_anak_aktif = $daftar_anak[0]['nama_murid'];
    }
} else {
    $id_murid_aktif = $daftar_anak[0]['id_murid'];
    $nama_anak_aktif = $daftar_anak[0]['nama_murid'];
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Riwayat Pembayaran</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_orangtua.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Pembayaran</li>
            </ol>

            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="h5 my-2"><i class="fa-solid fa-file-invoice-dollar me-2" style="padding-top: 10px;"></i> Tagihan untuk: <strong><?= $nama_anak_aktif; ?></strong></span>
                    </div>

                    <?php if(count($daftar_anak) > 1): ?>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Ganti Anak
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php foreach($daftar_anak as $anak): ?>
                                <li><a class="dropdown-item" href="?id_murid=<?= $anak['id_murid']; ?>"><?= $anak['nama_murid']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <table class="table table-hover table-bordered" id="datatablesSimple">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>No Invoice</th>
                                <th>Program</th>
                                <th>Total Tagihan</th>
                                <th>Status</th>
                                <th>Tanggal Invoice</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $query = "SELECT p.*, k.nama_kelas_bimbel, tp.nama_program 
                                      FROM tbl_pembayaran p
                                      JOIN tbl_kelas_bimbel k ON p.id_kelas_bimbel = k.id_kelas_bimbel
                                      JOIN tbl_tipe_program tp ON k.id_program = tp.id_program
                                      WHERE p.id_murid = '$id_murid_aktif'
                                      ORDER BY p.created_at DESC";
                            
                            $result = mysqli_query($koneksi, $query);

                            while ($data = mysqli_fetch_assoc($result)) {
                                // Badge Status
                                $status = $data['status_pembayaran'];
                                $badge = 'bg-danger'; // Default Belum Lunas
                                
                                if ($status == 'Lunas') {
                                    $badge = 'bg-success';
                                } elseif ($status == 'Pending') {
                                    $badge = 'bg-warning text-dark';
                                }
                            ?>
                            <tr>
                                <td align="center"><?= $no++ ?></td>
                                <td><strong><?= $data['no_invoice'] ?></strong></td>
                                <td>
                                    <?= $data['nama_program'] ?> <br>
                                    <small class="text-muted"><?= ($data['jenis_paket']=='8x_sesi')?'Paket 8x Sesi':'Paket 1x Sesi'; ?></small>
                                </td>
                                <td><?= formatRupiah($data['total_tagihan']) ?></td>
                                <td align="center"><span class="badge <?= $badge ?>"><?= $status ?></span></td>
                                <td><?= date('d/m/Y', strtotime($data['created_at'])) ?></td>
                                <td align="center">
                                    <a href="detail_pembayaran.php?id=<?= $data['id_pembayaran'] ?>" class="btn btn-primary btn-sm">
                                        <i class="fa-solid fa-eye"></i> Detail & Bayar
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
<?php require_once "../template/footer_ortu.php"; ?>
</div>