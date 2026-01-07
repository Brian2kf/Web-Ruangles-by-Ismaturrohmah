<?php
session_start();

// Cek Login & Role
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '1') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// Fallback koneksi
if (!isset($koneksi) && isset($conn)) {
    $koneksi = $conn;
}

$title = "Kelola Akun - Ruang Les";
require_once "../template/header_ortu.php";
require_once "../template/navbar_ortu.php";
require_once "../template/sidebar_ortu.php";

$id_ortu = $_SESSION["ssId"];

// 1. AMBIL DATA ORANG TUA
$queryOrtu = mysqli_query($koneksi, "SELECT * FROM tbl_user WHERE id = '$id_ortu'");
$dataOrtu  = mysqli_fetch_assoc($queryOrtu);

// 2. AMBIL DATA ANAK (Multi-Murid Logic)
$query_anak = mysqli_query($koneksi, "SELECT * FROM tbl_data_murid WHERE id_user_ortu = '$id_ortu'");
$daftar_anak = [];
while ($row = mysqli_fetch_assoc($query_anak)) {
    $daftar_anak[] = $row;
}

// Set Anak Aktif (Default anak pertama)
$id_murid_aktif = 0;
if (!empty($daftar_anak)) {
    $id_murid_aktif = $daftar_anak[0]['id_murid']; // Default
    
    // Jika ada pilihan dari dropdown
    if (isset($_GET['id_murid'])) {
        foreach($daftar_anak as $anak) {
            if($anak['id_murid'] == $_GET['id_murid']) {
                $id_murid_aktif = $_GET['id_murid'];
                break;
            }
        }
    }
}

// Ambil Detail Info Anak Aktif (Join ke tabel Kelas, Program, Tingkat)
$dataAnakLengkap = null;
if ($id_murid_aktif != 0) {
    $queryDetail = "SELECT m.nama_murid, 
                           k.nama_kelas_bimbel, 
                           tp.nama_program, 
                           t.jenjang_program, t.kelas_program
                    FROM tbl_data_murid m
                    LEFT JOIN tbl_kelas_bimbel k ON m.id_kelas_bimbel = k.id_kelas_bimbel
                    LEFT JOIN tbl_tipe_program tp ON k.id_program = tp.id_program
                    LEFT JOIN tbl_tingkat_program t ON k.id_tingkat = t.id_tingkat
                    WHERE m.id_murid = '$id_murid_aktif'";
    $resDetail = mysqli_query($koneksi, $queryDetail);
    $dataAnakLengkap = mysqli_fetch_assoc($resDetail);
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Kelola Akun</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_orangtua.php">Dashboard</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>

            <div class="row">
                
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white">
                            <i class="fa-solid fa-user-gear me-1"></i> Informasi Akun
                        </div>
                        <div class="card-body">
                            <form action="proses_profile.php" method="POST">
                                
                                <div class="mb-3 row">
                                    <label for="nama" class="col-sm-3 col-form-label fw-bold">Nama</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="nama" name="nama_user" value="<?= $dataOrtu['nama_user'] ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="username" class="col-sm-3 col-form-label fw-bold">Username</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="username" name="username" value="<?= $dataOrtu['username'] ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="email" class="col-sm-3 col-form-label fw-bold">Email</label>
                                    <div class="col-sm-9">
                                        <input type="email" class="form-control" id="email" name="email" value="<?= $dataOrtu['email_user'] ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="telepon" class="col-sm-3 col-form-label fw-bold">No. Telepon</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control" id="telepon" name="telepon" value="<?= $dataOrtu['telepon'] ?>" required>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label for="alamat" class="col-sm-3 col-form-label fw-bold">Alamat</label>
                                    <div class="col-sm-9">
                                        <textarea class="form-control" id="alamat" name="alamat" rows="3" required><?= $dataOrtu['alamat'] ?></textarea>
                                    </div>
                                </div>

                                <hr>
                                <p class="text-muted small mb-3"><i class="fa-solid fa-lock"></i> Ganti Password (Biarkan kosong jika tidak ingin mengubah)</p>

                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <input type="password" class="form-control" name="password_lama" placeholder="Password Saat Ini">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <input type="password" class="form-control" name="password_baru" placeholder="Password Baru">
                                    </div>
                                </div>

                                <div class="mt-3 text-end">
                                    <button type="submit" name="simpan_profil" class="btn btn-primary">
                                        Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-child me-1"></i> Informasi Anak</span>
                            
                            <?php if(count($daftar_anak) > 1): ?>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Pilih Anak
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <?php foreach($daftar_anak as $anak): ?>
                                        <li>
                                            <a class="dropdown-item <?= ($anak['id_murid'] == $id_murid_aktif) ? 'active' : ''; ?>" 
                                               href="?id_murid=<?= $anak['id_murid']; ?>">
                                               <?= $anak['nama_murid']; ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="card-body">
                            <?php if ($dataAnakLengkap): ?>
                                <div class="mb-4 row">
                                    <label class="col-sm-3 col-form-label fw-bold">Nama Anak</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control-plaintext border-bottom" readonly value="<?= $dataAnakLengkap['nama_murid'] ?>">
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label class="col-sm-3 col-form-label fw-bold">Tingkat</label>
                                    <div class="col-sm-9">
                                        <?php 
                                            $tingkat = $dataAnakLengkap['jenjang_program'] ?? '-';
                                            if(isset($dataAnakLengkap['kelas_program'])) {
                                                $tingkat .= " (Kelas " . $dataAnakLengkap['kelas_program'] . ")";
                                            }
                                        ?>
                                        <input type="text" class="form-control-plaintext border-bottom" readonly value="<?= $tingkat ?>">
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label class="col-sm-3 col-form-label fw-bold">Kelas</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control-plaintext border-bottom" readonly value="<?= $dataAnakLengkap['nama_kelas_bimbel'] ?? 'Belum ditentukan' ?>">
                                    </div>
                                </div>

                                <div class="mb-4 row">
                                    <label class="col-sm-3 col-form-label fw-bold">Program</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control-plaintext border-bottom" readonly value="<?= $dataAnakLengkap['nama_program'] ?? '-' ?>">
                                    </div>
                                </div>

                                <div class="alert alert-light border border-secondary text-center mt-5">
                                    Catatan: Data anak dikelola oleh admin. Hubungi admin jika ada perubahan data anak.
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning text-center">
                                    Data anak tidak ditemukan atau belum terhubung dengan akun ini.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
<?php require_once "../template/footer_ortu.php"; ?>
</div>