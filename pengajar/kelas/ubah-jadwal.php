<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
$title = "Kelola Jadwal - Ruang Les by Ismaturrohmah";
require_once "../template/header_pengajar.php";
require_once "../template/navbar_pengajar.php";
require_once "../template/sidebar_pengajar.php";

// 1. Ambil ID Kelas dari URL
if (!isset($_GET['id_kelas_bimbel'])) {
    echo "<script>alert('ID kelas tidak ditemukan.'); document.location.href = 'kelas.php';</script>";
    exit();
}
$id_kelas = (int)$_GET['id_kelas_bimbel'];

// 2. Query untuk mengambil data nama kelas
$queryKelas = mysqli_query($koneksi, "SELECT * FROM tbl_kelas_bimbel WHERE id_kelas_bimbel = $id_kelas");
$data = mysqli_fetch_array($queryKelas);
if ($data == null) {
     echo "<script>alert('Data kelas tidak ditemukan.'); document.location.href = 'kelas.php';</script>";
    exit();
}

// 3. Query untuk mengambil SEMUA jadwal yang ada untuk kelas ini
$queryJadwal = mysqli_query($koneksi, 
    "SELECT *, DATE_FORMAT(jam_mulai, '%H:%i') AS jam_mulai_f, DATE_FORMAT(jam_selesai, '%H:%i') AS jam_selesai_f
     FROM tbl_jadwal_kelas 
     WHERE id_kelas_bimbel = $id_kelas
     ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')"
);

?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Kelola Jadwal Kelas</h1>
            <h5 class="mb-4 text-muted"><?= $data['nama_kelas_bimbel'] ?></h5>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_pengajar.php">Home</a></li>
                <li class="breadcrumb-item"><a href="kelas.php">Kelas</a></li>
                <li class="breadcrumb-item active">Kelola Jadwal</li>
            </ol>

            <div class="row">
                <div class="col-md-5">
                    <form action="proses-kelas.php" method="POST">
                        <div class="card">
                            <div class="card-header">
                                <span class="h5 my-2"><i class="fa-solid fa-plus" style="padding-top: 10px;"></i> Tambah Jadwal Baru</span>
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="id_kelas_bimbel" value="<?= $id_kelas ?>">

                                <div class="mb-3">
                                    <label for="hari" class="form-label">Hari</label>
                                    <select name="hari" id="hari" class="form-select" required>
                                        <option value="" selected>--Pilih Hari--</option>
                                        <option value="Senin">Senin</option>
                                        <option value="Selasa">Selasa</option>
                                        <option value="Rabu">Rabu</option>
                                        <option value="Kamis">Kamis</option>
                                        <option value="Jumat">Jumat</option>
                                        <option value="Sabtu">Sabtu</option>
                                        <option value="Minggu">Minggu</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="jam_mulai" class="form-label">Jam Mulai</label>
                                    <input type="time" name="jam_mulai" id="jam_mulai" required class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label for="jam_selesai" class="form-label">Jam Selesai</label>
                                    <input type="time" name="jam_selesai" id="jam_selesai" required class="form-control">
                                </div>
                                <button type="submit" name="tambah_jadwal" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan Jadwal</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header">
                            <span class="h5 my-2"><i class="fa-solid fa-list" style="padding-top: 10px;"></i> Jadwal Saat Ini</span>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Hari</th>
                                        <th>Jam Mulai</th>
                                        <th>Jam Selesai</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (mysqli_num_rows($queryJadwal) > 0) {
                                        while ($jadwal = mysqli_fetch_array($queryJadwal)) {
                                    ?>
                                        <tr>
                                            <td><?= $jadwal['hari'] ?></td>
                                            <td><?= $jadwal['jam_mulai_f'] ?></td>
                                            <td><?= $jadwal['jam_selesai_f'] ?></td>
                                            <td>
                                                <a href="proses-kelas.php?aksi=hapus&id_jadwal=<?= $jadwal['id_jadwal'] ?>&id_kelas=<?= $id_kelas ?>" 
                                                class="btn btn-sm btn-danger" title="Hapus Jadwal"
                                                onclick="return confirm('Anda yakin akan menghapus jadwal ini ?')">
                                                <i class="fa-solid fa-trash"></i> Hapus
                                                </a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    } else {
                                        echo '<tr><td colspan="4" class="text-center">Belum ada jadwal yang ditambahkan.</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php
require_once "../template/footer_pengajar.php";
?>