<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../auth/login.php");
    exit();
}
require_once "../config.php";

// ============================================================
// 1. Hitung Total Murid (dari tbl_data_murid)
// ============================================================
$queryMurid = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_data_murid");
$dataMurid  = mysqli_fetch_assoc($queryMurid);
$jumlahMurid = $dataMurid['total'];

// ============================================================
// 2. Hitung Total Kelas (dari tbl_kelas_bimbel)
// ============================================================
$queryKelas = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_kelas_bimbel");
$dataKelas  = mysqli_fetch_assoc($queryKelas);
$jumlahKelas = $dataKelas['total'];

// ============================================================
// 3. Hitung Total Materi (dari tbl_materi)
// ============================================================
$queryMateri = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_materi");
$dataMateri  = mysqli_fetch_assoc($queryMateri);
$jumlahMateri = $dataMateri['total'];


$title = "Dashboard Admin - Ruang Les by Ismaturrohmah";
require_once "template/header_admin.php";
require_once "template/navbar_admin.php";
require_once "template/sidebar_admin.php";
?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Dashboard</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                        <div class="row justify-content-center text-center my-5">
                            
                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 border rounded-3 shadow-sm"> <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                                        <h5 class="mb-3">Total Murid Saat Ini</h5>
                                        <div class="display-1 fw-bold"><?= $jumlahMurid ?></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 border rounded-3 shadow-sm">
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                                        <h5 class="mb-3">Total Kelas Saat Ini</h5>
                                        <div class="display-1 fw-bold"><?= $jumlahKelas ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row justify-content-center mb-5">
                            <div class="col-xl-6 col-lg-8">
                                <div class="card border rounded-3 shadow-sm">
                                    <div class="card-body d-flex flex-column justify-content-center align-items-center py-4">
                                        <h5 class="mb-3">Total Materi Pembelajaran</h5>
                                        <div class="display-1 fw-bold"><?= $jumlahMateri ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>

<?php
require_once "template/footer_admin.php";
?>