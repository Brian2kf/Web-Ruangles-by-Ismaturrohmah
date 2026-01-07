<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";

// Ambil mata pelajaran dari parameter URL
$mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';

if (empty($mapel)) {
    header("Location: materi_admin.php");
    exit();
}

$title = "Materi $mapel - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Handle delete materi
if (isset($_GET['hapus'])) {
    $id_materi = $_GET['hapus'];
    
    // Ambil data untuk dihapus
    $query = mysqli_query($koneksi, "SELECT foto_cover, file_materi FROM tbl_materi WHERE id_materi = $id_materi");
    $data = mysqli_fetch_assoc($query);
    
    // Hapus file foto jika ada
    if (!empty($data['foto_cover']) && file_exists("../../assets/img/materi/" . $data['foto_cover'])) {
        unlink("../../assets/img/materi/" . $data['foto_cover']);
    }
    
    // Hapus file materi jika ada
    if (!empty($data['file_materi']) && file_exists("../assets/files/materi/" . $data['file_materi'])) {
        unlink("../assets/files/materi/" . $data['file_materi']);
    }
    
    // Hapus data dari database
    mysqli_query($koneksi, "DELETE FROM tbl_materi WHERE id_materi = $id_materi");
    
    echo "<script>
            alert('Materi berhasil dihapus!');
            window.location.href = 'daftar_materi_admin.php?mapel=" . urlencode($mapel) . "';
          </script>";
}

// Ambil data materi berdasarkan mata pelajaran
$search = isset($_GET['search']) ? $_GET['search'] : '';
$query = "SELECT m.*, t.jenjang_program, t.kelas_program 
          FROM tbl_materi m 
          LEFT JOIN tbl_tingkat_program t ON m.id_tingkat = t.id_tingkat 
          WHERE m.nama_mapel = '$mapel'";

if (!empty($search)) {
    $query .= " AND (m.nama_materi LIKE '%$search%' OR m.deskripsi_materi LIKE '%$search%')";
}

$query .= " ORDER BY m.id_tingkat, m.nama_materi";
$result = mysqli_query($koneksi, $query);

// Function untuk format ukuran file
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Materi Pembelajaran</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="materi_admin.php">Materi Pembelajaran</a></li>
                <li class="breadcrumb-item active"><?= $mapel ?></li>
            </ol>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="h5 my-2"><i class="fas fa-book" style="padding-top: 10px;"></i> <?= $mapel ?></span>
                    </div>
                    <div class="col-md-4 text-end">
                        <a href="tambah_materi_admin.php?mapel=<?= urlencode($mapel) ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Materi
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4 d-flex justify-content-end">
                        <div class="col-md-3">
                            <form method="GET" action="">
                                <input type="hidden" name="mapel" value="<?= $mapel ?>">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Cari materi..." value="<?= $search ?>">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="fas fa-search"></i> Cari
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="row g-4">
                        <?php while ($data = mysqli_fetch_assoc($result)): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card h-100">
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px; overflow: hidden;">
                                    <?php if (!empty($data['foto_cover']) && file_exists("../../assets/img/materi/" . $data['foto_cover'])): ?>
                                        <img src="../../assets/img/materi/<?= $data['foto_cover'] ?>" alt="<?= $data['nama_materi'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-image fa-3x text-muted"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <h5 class="card-title"><?= $data['nama_materi'] ?></h5>
                                    <p class="card-text text-muted small"><?= substr($data['deskripsi_materi'], 0, 130)?></p>
                                    
                                    <div class="mb-0">
                                        <span class="badge bg-success ">
                                            <i class="fas fa-graduation-cap"></i> <?= $data['jenjang_program'] ?> <?= $data['kelas_program'] ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="card-footer bg-white border-top-0 mb-2">
                                    <div class="d-flex justify-content-between gap-1"> <?php if (!empty($data['file_materi'])): ?>
                                        <a href="download_materi.php?id=<?= $data['id_materi'] ?>" class="btn btn-sm btn-secondary text-white" title="Download File">
                                            <i class="fas fa-download"></i> Unduh
                                        </a>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled><i class="fas fa-ban"></i></button>
                                        <?php endif; ?>

                                        <div class="d-flex gap-1">
                                            <a href="edit_materi_admin.php?id=<?= $data['id_materi'] ?>&mapel=<?= urlencode($mapel) ?>" class="btn btn-sm btn-warning text-white" title="Edit">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                            <a href="?mapel=<?= urlencode($mapel) ?>&hapus=<?= $data['id_materi'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus materi ini?')" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Belum ada materi untuk mata pelajaran <?= $mapel ?>.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <?php require_once "../template/footer_admin.php"; ?>
</div>
</div>
</body>
</html>