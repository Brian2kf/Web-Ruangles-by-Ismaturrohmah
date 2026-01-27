<?php
require_once '../config.php';
$title = "Ruang Les by Ismaturrohmah - Bimbingan Belajar Terpadu";
require_once 'template/header.php';
require_once 'template/navbar.php';
?>
<!-- foto -->
<!-- Carousel Header -->
<div id="carouselHeader" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
        <div class="carousel-item active">
            <div class="d-flex align-items-center justify-content-center text-white text-center" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/Kegiatan1.png'); background-size: cover; background-position: center; min-height: 650px;">
                <div class="container px-4">
                    <h1 class="display-3 fw-bold mb-3">Selamat Datang di Ruang les</h1>
                    <p class="lead mb-4 fs-4">Bimbingan belajar terbaik untuk mencerdaskan generasi bangsa dengan metode yang menyenangkan.</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a class="btn btn-primary btn-lg px-4 gap-3 fw-bold rounded-pill" href="pendaftaran.php">Daftar Sekarang</a>
                        <a class="btn btn-outline-light btn-lg px-4 rounded-pill" href="#program">Lihat Program</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-flex align-items-center justify-content-center text-white text-center" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/Kegiatan2.png'); background-size: cover; background-position: center; min-height: 650px;">
                <div class="container px-4">
                    <h1 class="display-3 fw-bold mb-3">Belajar Menyenangkan & Interaktif</h1>
                    <p class="lead mb-4 fs-4">Metode belajar inovatif dengan pengajar profesional dan materi lengkap.</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a class="btn btn-primary btn-lg px-4 gap-3 fw-bold rounded-pill" href="pendaftaran.php">Daftar Sekarang</a>
                        <a class="btn btn-outline-light btn-lg px-4 rounded-pill" href="#keunggulan">Keunggulan Kami</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="carousel-item">
            <div class="d-flex align-items-center justify-content-center text-white text-center" style="background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../assets/img/Kegiatan3.png'); background-size: cover; background-position: center; min-height: 650px;">
                <div class="container px-4">
                    <h1 class="display-3 fw-bold mb-3">Akses Materi Kapan Saja</h1>
                    <p class="lead mb-4 fs-4">Dapatkan e-book dan laporan perkembangan belajar secara online.</p>
                    <div class="d-grid gap-3 d-sm-flex justify-content-sm-center">
                        <a class="btn btn-primary btn-lg px-4 gap-3 fw-bold rounded-pill" href="pendaftaran.php">Daftar Sekarang</a>
                        <a class="btn btn-outline-light btn-lg px-4 rounded-pill" href="#program">Lihat Program</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselHeader" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselHeader" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
    </button>
</div>

<section class="py-5 bg-light" id="keunggulan">
    <div class="container mt-n5"> <div class="row g-4 justify-content-center">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center py-4">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                            <i class="fas fa-chalkboard-teacher fa-3x"></i>
                        </div>
                        <h3 class="fw-bold">Pengajar Profesional</h3>
                        <p class="text-muted mb-0">Tim pengajar berpengalaman untuk setiap jenjang pendidikan.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center py-4">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                            <i class="fa-solid fa-file-pen fa-3x"></i>
                        </div>
                        <h3 class="fw-bold">Laporan Perkembangan</h3>
                        <p class="text-muted mb-0">Pantau progress belajar anak dengan laporan detail.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm text-center py-4">
                    <div class="card-body">
                        <div class="mb-3 text-primary">
                            <i class="fas fa-book-reader fa-3x"></i>
                        </div>
                        <h3 class="fw-bold">Materi Lengkap</h3>
                        <p class="text-muted mb-0">Akses repository pembelajaran dengan e-book.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-white" id="tentang">
    <div class="container py-5">
        <div class="row align-items-center gx-5">
            <div class="col-lg-6 mb-5 mb-lg-0">
                <!-- Foto -->
                <img src="../assets/img/Kegiatan.png" alt="Tentang Ruangles" class="img-fluid rounded-3 shadow">
            </div>
            <div class="col-lg-6">
                <h2 class="fw-bold mb-4">Mengapa Memilih Ruangles?</h2>
                <p class="lead text-muted mb-4">
                    Ruangles by Ismaturrohmah hadir untuk memberikan solusi belajar yang efektif dan efisien. 
                    Kami percaya setiap anak memiliki potensi yang luar biasa jika dibimbing dengan cara yang tepat.
                </p>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Metode belajar interaktif</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Kurikulum Nasional</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Konsultasi PR gratis</li>
                    <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Biaya terjangkau</li>
                </ul>
                <a href="#program" class="btn btn-primary mt-3 rounded-pill px-4">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-light" id="program">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Program Belajar Kami</h2>
            <p class="text-muted">Pilih paket yang sesuai dengan kebutuhan putra-putri Anda</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-top">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-3 d-inline-block mb-3">
                            <i class="fas fa-users fa-2x text-primary"></i>
                        </div>
                        <h4 class="card-title fw-bold">Reguler Class</h4>
                        <p class="card-text text-muted mb-4">Belajar seru bersama teman-teman dengan maksimal 6 murid per kelas.</p>
                        <hr>
                        <ul class="list-unstyled text-start small mb-4 ms-3">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Konsultasi PR Harian</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Materi Sekolah & Tambahan</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 60 Menit per Sesi</li>
                        </ul>
                        <div class="d-grid">
                            <a href="pendaftaran.php" class="btn btn-outline-primary rounded-pill">Daftar Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow hover-top transform-scale">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <span class="fw-bold">PALING DIMINATI</span>
                    </div>
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-warning bg-opacity-10 p-3 d-inline-block mb-3">
                            <i class="fas fa-user-friends fa-2x text-warning"></i>
                        </div>
                        <h4 class="card-title fw-bold">Semi Private</h4>
                        <p class="card-text text-muted mb-4">Fokus lebih intensif dengan kelompok kecil maksimal 2 murid.</p>
                        <hr>
                        <ul class="list-unstyled text-start small mb-4 ms-3">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Soft file latihan</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Konsultasi PR Harian</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 60 Menit per Sesi</li>
                        </ul>
                        <div class="d-grid">
                            <a href="pendaftaran.php" class="btn btn-primary rounded-pill">Daftar Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm hover-top">
                    <div class="card-body text-center p-4">
                        <div class="rounded-circle bg-success bg-opacity-10 p-3 d-inline-block mb-3">
                            <i class="fas fa-user fa-2x text-success"></i>
                        </div>
                        <h4 class="card-title fw-bold">Private Class</h4>
                        <p class="card-text text-muted mb-4">Belajar personal 1 on 1 dengan pengajar, materi menyesuaikan kebutuhan siswa.</p>
                        <hr>
                        <ul class="list-unstyled text-start small mb-4 ms-3">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Jadwal Fleksibel</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> Pengajar Datang ke Rumah/Online</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i> 60 Menit per Sesi</li>
                        </ul>
                        <div class="d-grid">
                            <a href="pendaftaran.php" class="btn btn-outline-primary rounded-pill">Daftar Sekarang</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-primary text-white text-center">
    <div class="container px-4">
        <h2 class="fw-bold mb-3">Siap Meningkatkan Prestasi Anak?</h2>
        <p class="lead mb-4">Bergabunglah dengan siswa lain yang telah merasakan manfaat belajar di Ruangles.</p>
        <a href="pendaftaran.php" class="btn btn-light btn-lg rounded-pill px-5 fw-bold text-primary">Daftar Sekarang</a>
    </div>
</section>

<?php require_once 'template/footer.php'; ?>