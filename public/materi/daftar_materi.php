<?php
require_once "../../config.php";

// Ambil parameter mapel
$mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';

// Jika tidak ada mapel, kembalikan ke halaman materi utama
if (empty($mapel)) {
    header("Location: materi.php");
    exit();
}

$title = "Materi $mapel - Ruang Les by Ismaturrohmah";

require_once "../template/header.php";
require_once "../template/navbar.php";

// Logika Pencarian
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$queryStr = "SELECT m.*, t.jenjang_program, t.kelas_program 
             FROM tbl_materi m 
             LEFT JOIN tbl_tingkat_program t ON m.id_tingkat = t.id_tingkat 
             WHERE m.nama_mapel = '$mapel'";

if (!empty($search)) {
    $queryStr .= " AND (m.nama_materi LIKE '%$search%' OR m.deskripsi_materi LIKE '%$search%')";
}

$queryStr .= " ORDER BY m.id_tingkat ASC, m.nama_materi ASC";
$result = mysqli_query($koneksi, $queryStr);
?>

<main>
    <section class="bg-light py-4 border-bottom">
        <div class="container px-5 py-5">
            <div class="row align-items-center justify-content-between">
                <div class="col-lg-6">
                    <h1 class="fw-bolder mb-1"><?= $mapel ?></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="materi.php">Materi</a></li>
                            <li class="breadcrumb-item active" aria-current="page"><?= $mapel ?></li>
                        </ol>
                    </nav>
                </div>
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <form action="" method="GET" class="input-group">
                        <input type="hidden" name="mapel" value="<?= $mapel ?>">
                        <input type="text" class="form-control" name="search" placeholder="Cari judul materi..." value="<?= htmlspecialchars($search) ?>">
                        <button class="btn btn-primary" type="submit"><i class="fas fa-search"></i> Cari</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container px-5">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="row gx-5">
                    <?php while ($data = mysqli_fetch_assoc($result)): ?>
                        <div class="col-lg-4 col-md-6 mb-5">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px; overflow: hidden; border-top-left-radius: 0.25rem; border-top-right-radius: 0.25rem;">
                                    <?php if (!empty($data['foto_cover']) && file_exists("../../assets/img/materi/" . $data['foto_cover'])): ?>
                                        <img src="../../assets/img/materi/<?= $data['foto_cover'] ?>" alt="<?= $data['nama_materi'] ?>" style="width: 100%; height: 100%; object-fit: cover;">
                                    <?php else: ?>
                                        <i class="fas fa-book-open fa-3x text-secondary opacity-50"></i>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="card-body p-4">
                                    <div class="badge bg-primary bg-gradient rounded-pill mb-2">
                                        <?= $data['jenjang_program'] ?> - Kelas <?= $data['kelas_program'] ?>
                                    </div>
                                    
                                    <h5 class="card-title mb-3 fw-bold"><?= $data['nama_materi'] ?></h5>
                                    <p class="card-text text-muted mb-0">
                                        <?= substr($data['deskripsi_materi'], 0, 100) ?>...
                                    </p>
                                </div>
                                <div class="card-footer p-4 pt-0 bg-transparent border-top-0">
                                    <div class="d-grid">
                                        <a href="download.php?id=<?= $data['id_materi'] ?>" class="btn btn-outline-dark">
                                            <i class="fas fa-download me-2"></i>Download Materi
                                        </a>
                                    </div>
                                    <div class="text-center mt-2">
                                        <small class="text-muted fst-italic">
                                            File: <?= strtoupper($data['tipe_file']) ?> (<?= number_format($data['ukuran_file'] / 1024 / 1024, 2) ?> MB)
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-3"><i class="fas fa-folder-open fa-3x text-muted"></i></div>
                    <h4 class="fw-normal text-muted">Belum ada materi yang tersedia untuk kategori ini.</h4>
                    <a href="materi.php" class="btn btn-link">Kembali ke Kategori</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php
require_once "../template/footer.php";
?>