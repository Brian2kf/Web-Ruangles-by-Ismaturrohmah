<?php
// Hubungkan ke config database
require_once "../../config.php";

$title = "Materi Belajar Gratis - Ruang Les by Ismaturrohmah";

// Memanggil Template Public
require_once "../template/header.php";
require_once "../template/navbar.php"; // Pastikan navbar dipanggil

// Daftar mata pelajaran yang tersedia (Hardcoded sesuai sistem pengajar)
$mata_pelajaran = [
    'Matematika',
    'IPA',
    'Bahasa Inggris',
    'Bahasa Indonesia',
    'Pendidikan Kewarganegaraan'
];
?>

<main>
    <section class="bg-light py-4">
        <div class="container px-5 py-5 text-center">
            <h1 class="fw-bolder">Materi</h1>
        </div>
    </section>

    <section class="py-5">
        <div class="container px-5 my-5">
            <div class="row gx-5 justify-content-center">
                <?php foreach ($mata_pelajaran as $mapel): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <a href="daftar_materi.php?mapel=<?= urlencode($mapel) ?>" class="text-decoration-none">
                        <div class="card h-100 shadow border-0 hover-animate">
                            <div class="card-body text-center p-5">
                                <div class="feature bg-primary bg-gradient text-white rounded-3 mb-3 mx-auto" style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                    <?php
                                    // Icon mapping (menggunakan FontAwesome)
                                    $icons = [
                                        'Matematika' => 'fa-calculator',
                                        'IPA' => 'fa-flask',
                                        'Bahasa Inggris' => 'fa-language',
                                        'Bahasa Indonesia' => 'fa-book-open',
                                        'Pendidikan Kewarganegaraan' => 'fa-landmark'
                                    ];
                                    $icon = $icons[$mapel] ?? 'fa-book';
                                    ?>
                                    <i class="fas <?= $icon ?> fa-2x"></i>
                                </div>
                                <h2 class="h4 fw-bolder text-dark"><?= $mapel ?></h2>
                                <?php
                                // Hitung jumlah materi
                                $query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM tbl_materi WHERE nama_mapel = '$mapel'");
                                $data = mysqli_fetch_assoc($query);
                                ?>
                                <p class="text-muted mb-0"><?= $data['total'] ?> Materi Tersedia</p>
                            </div>
                            <div class="card-footer p-4 pt-0 bg-transparent border-top-0">
                                <div class="d-grid">
                                    <button class="btn btn-outline-primary">Lihat Materi</button>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<style>
    .hover-animate { transition: transform 0.3s ease; }
    .hover-animate:hover { transform: translateY(-10px); }
</style>

<?php
require_once "../template/footer.php";
?>