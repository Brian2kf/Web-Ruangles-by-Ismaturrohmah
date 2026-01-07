<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";
$title = "Materi Pembelajaran - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Daftar mata pelajaran yang tersedia
$mata_pelajaran = [
    'Matematika',
    'IPA',
    'Bahasa Inggris',
    'Bahasa Indonesia',
    'Pendidikan Kewarganegaraan'
];
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Materi Pembelajaran</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item active">Materi</li>
            </ol>

            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fas fa-book" style="padding-top: 10px;"></i> Pilih Mata Pelajaran</span>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <?php foreach ($mata_pelajaran as $mapel): ?>
                        <div class="col-lg-4 col-md-6">
                            <a href="daftar_materi_admin.php?mapel=<?= urlencode($mapel) ?>" class="text-decoration-none">
                                <div class="card h-100 border shadow-sm hover-shadow" style="transition: all 0.3s;">
                                    <div class="card-body text-center py-5">
                                        <div class="mb-3">
                                            <?php
                                            // Icon berbeda untuk setiap mata pelajaran
                                            $icons = [
                                                'Matematika' => 'fa-calculator',
                                                'IPA' => 'fa-flask',
                                                'Bahasa Inggris' => 'fa-language',
                                                'Bahasa Indonesia' => 'fa-book-open',
                                                'Pendidikan Kewarganegaraan' => 'fa-landmark'
                                            ];
                                            $icon = $icons[$mapel] ?? 'fa-book';
                                            ?>
                                            <i class="fas <?= $icon ?> fa-4x text-secondary"></i>
                                        </div>
                                        <h5 class="card-title text-dark mb-3"><?= $mapel ?></h5>
                                        <?php
                                        // Hitung jumlah materi per mata pelajaran
                                        $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_materi WHERE nama_mapel = '$mapel'");
                                        $data = mysqli_fetch_assoc($query);
                                        $total = $data['total'];
                                        ?>
                                        <p class="text-muted mb-0"><?= $total ?> Materi</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    </style>

    <?php require_once "../template/footer_admin.php"; ?>
</div>
</div>
</body>
</html>