<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion bg-light" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Home</div>
                    <a class="nav-link" href="<?= $main_url ?>admin/dashboard_admin.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
                    <hr class="mb-0">
                    <div class="sb-sidenav-menu-heading">User</div>
                    <a class="nav-link" href="<?= $main_url ?>admin/user/add-user.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-user"></i></div>
                        Tambah Pengguna
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/user/kelola_pengguna.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-key"></i></div>
                        Kelola Pengguna
                    </a>
                    <hr class="mb-0">
                    <div class="sb-sidenav-menu-heading">Data</div>
                    <a class="nav-link" href="<?= $main_url ?>admin/murid/murid.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-users"></i></div>
                        Murid
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/pengajar/pengajar.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                        Pengajar
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/kelas/kelas.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-chalkboard"></i></div>
                        Kelas
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/absensi/absensi.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-address-book"></i></div>
                        Absensi
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/catatan_murid/catatan_murid.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-book-bookmark"></i></div>
                        Catatan Murid
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/materi/materi_admin.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-file"></i></div>
                        Materi
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/feedback/feedback.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-comments"></i></div>
                        Feedback Orang Tua
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/pembayaran/pembayaran_admin.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-money-bills"></i></div>
                        Pembayaran
                    </a>
                    <a class="nav-link" href="<?= $main_url ?>admin/pendaftar/pendaftar.php">
                        <div class="sb-nav-link-icon"><i class="fa-solid fa-user-plus"></i></div>
                        Pendaftar
                    </a>
                </div>
            </div>
            <hr class="mb-0">
            <div class="sb-sidenav-footer">
                
                <div class="small">Logged in as:</div>
                Admin
            </div>
        </nav>
    </div>