<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";
$title = "Kelas - Ruang Les by Ismaturrohmah";
require_once "../template/header_pengajar.php";
require_once "../template/navbar_pengajar.php";
require_once "../template/sidebar_pengajar.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Kelas</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_pengajar.php">Home</a></li>
                <li class="breadcrumb-item active">Kelas</li>
            </ol>
            <div class="card">
                <div class="card-header">
                    <span class="h5 my-2"><i class="fa-solid fa-chalkboard-user" style="padding-top: 10px;"></i> Data Kelas</span>
                </div>
                <div class="card-body">
                    <table class="table table-hover" id="datatablesSimple">
                        <thead>
                            <tr>
                            <th scope="col">No</th>
                            <th scope="col"><center>Nama Kelas Bimbel</center></th>
                            <th scope="col"><center>Program</center></th>
                            <th scope="col"><center>Tingkat</center></th>
                            <th scope="col"><center>Jadwal Pertemuan</center></th>
                            <th scope="col"><center>Jumlah Murid</center></th>
                            <th scope="col"><center>Aksi</center></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            // Query utama tetap sama, tapi HAPUS kolom jadwal dari sini
                            $queryKelas = mysqli_query($koneksi, "
                                SELECT 
                                    kb.id_kelas_bimbel, 
                                    kb.nama_kelas_bimbel, 
                                    kb.jumlah_murid,
                                    tp.nama_program,
                                    CASE 
                                        WHEN tkp.jenjang_program IN ('PAUD', 'TK') THEN tkp.jenjang_program
                                        WHEN tkp.id_tingkat IS NULL THEN NULL
                                        ELSE CONCAT(tkp.kelas_program, ' ', tkp.jenjang_program) 
                                    END AS nama_tingkat
                                FROM tbl_kelas_bimbel kb
                                LEFT JOIN tbl_tipe_program tp ON kb.id_program = tp.id_program
                                LEFT JOIN tbl_tingkat_program tkp ON kb.id_tingkat = tkp.id_tingkat
                                ORDER BY kb.id_kelas_bimbel DESC
                            ");
                            
                            while ($data = mysqli_fetch_array($queryKelas)) { 
                                $id_kelas_saat_ini = $data['id_kelas_bimbel'];
                            ?>
                            <tr>
                                <th scope="row"><?= $no++ ?></th>
                                <td><?= $data['nama_kelas_bimbel'] ?></td>
                                <td><?= $data['nama_program'] ?></td>
                                <td><?= $data['nama_tingkat'] ?></td>
                                
                                <td>
                                    <?php
                                    // 1. Query baru untuk mengambil SEMUA jadwal kelas ini
                                    $queryJadwal = mysqli_query($koneksi, 
                                        "SELECT *, DATE_FORMAT(jam_mulai, '%H:%i') AS jam_mulai_f, DATE_FORMAT(jam_selesai, '%H:%i') AS jam_selesai_f
                                         FROM tbl_jadwal_kelas 
                                         WHERE id_kelas_bimbel = $id_kelas_saat_ini
                                         ORDER BY FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu')"
                                    );
                                    
                                    $jumlah_jadwal = mysqli_num_rows($queryJadwal);

                                    if ($jumlah_jadwal > 0) {
                                        // 2. Tampilkan sebagai daftar jika ada jadwal
                                        echo '<ul>';
                                        while ($jadwal = mysqli_fetch_array($queryJadwal)) {
                                            echo '<li>' . $jadwal['hari'] . ', ' . $jadwal['jam_mulai_f'] . ' - ' . $jadwal['jam_selesai_f'] . '</li>';
                                        }
                                        echo '</ul>';
                                    } else {
                                        // 3. Tampilkan pesan jika belum ada jadwal
                                        echo "(Jadwal belum diatur)";
                                    }
                                    ?>
                                </td>
                                <td align="center"><?= $data['jumlah_murid'] ?></td>
                                <td>
                                    <a href="ubah-jadwal.php?id_kelas_bimbel=<?= $data['id_kelas_bimbel'] ?>" class="btn btn-sm btn-success" title="Kelola Jadwal"><i class="fa-solid fa-clock text-white"></i> Ubah Jadwal</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

<?php
require_once "../template/footer_pengajar.php";
?>