<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
require_once "fungsi_invoice.php";

$title = "Buat Invoice Baru - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Ambil daftar murid
$query_murid = "SELECT m.*, k.nama_kelas_bimbel, k.id_program, k.id_tingkat, tp.nama_program,
                t.jenjang_program, t.kelas_program
                FROM tbl_data_murid m
                LEFT JOIN tbl_kelas_bimbel k ON m.id_kelas_bimbel = k.id_kelas_bimbel
                LEFT JOIN tbl_tipe_program tp ON k.id_program = tp.id_program
                LEFT JOIN tbl_tingkat_program t ON k.id_tingkat = t.id_tingkat
                ORDER BY m.nama_murid ASC";
$result_murid = mysqli_query($koneksi, $query_murid);

// Handle form submit
if (isset($_POST['simpan'])) {
    $id_murid = $_POST['id_murid'];
    $id_kelas_bimbel = $_POST['id_kelas_bimbel'];
    $jenis_paket = $_POST['jenis_paket'];
    $lokasi_les = $_POST['lokasi_les'];
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    
    // Generate no invoice
    $no_invoice = generateNoInvoice($koneksi);
    
    // Ambil data murid untuk hitung tarif
    $query_data = "SELECT k.id_program, k.id_tingkat FROM tbl_data_murid m
                   JOIN tbl_kelas_bimbel k ON m.id_kelas_bimbel = k.id_kelas_bimbel
                   WHERE m.id_murid = $id_murid";
    $result_data = mysqli_query($koneksi, $query_data);
    $data_murid = mysqli_fetch_assoc($result_data);
    
    // Ambil tarif
    $tarif = getTarif($koneksi, $data_murid['id_program'], $data_murid['id_tingkat'], $lokasi_les);
    
    // Tentukan harga dan jumlah sesi
    if ($jenis_paket == '8x_sesi') {
        $harga_per_sesi = $tarif['harga_8x'] / 8;
        $total_tagihan = $tarif['harga_8x'];
        $jumlah_sesi = 8;
        $sesi_tersisa = 8;
    } else {
        $harga_per_sesi = $tarif['harga_1x'];
        $total_tagihan = $tarif['harga_1x'];
        $jumlah_sesi = 1;
        $sesi_tersisa = 1;
    }
    
    // Periode mulai = hari ini
    $periode_mulai = date('Y-m-d');
    $periode_selesai = NULL; // Fleksibel sesuai kehadiran
    
    // Insert invoice
    $query_insert = "INSERT INTO tbl_pembayaran 
                     (no_invoice, id_murid, id_kelas_bimbel, jenis_paket, jumlah_sesi, 
                      sesi_terpakai, sesi_tersisa, harga_per_sesi, total_tagihan, 
                      periode_mulai, periode_selesai, status_pembayaran, lokasi_les, catatan_pembayaran)
                     VALUES 
                     ('$no_invoice', $id_murid, $id_kelas_bimbel, '$jenis_paket', $jumlah_sesi,
                      0, $sesi_tersisa, $harga_per_sesi, $total_tagihan,
                      '$periode_mulai', NULL, 'Belum Lunas', '$lokasi_les', '$catatan')";
    
    if (mysqli_query($koneksi, $query_insert)) {
        // Ambil ID invoice yang baru saja dibuat
        $id_invoice_baru = mysqli_insert_id($koneksi);
        // Jalankan sinkronisasi untuk menarik absensi yang lalu-lalu
        $tertarik = syncSesiInvoice($koneksi, $id_invoice_baru);
        $pesan_tambahan = ($tertarik > 0) ? "\\nOtomatis memasukkan $tertarik sesi absensi yang lalu." : "";
        
        echo "<script>
                alert('Invoice berhasil dibuat!\\nNo Invoice: $no_invoice" . $pesan_tambahan . "');
                window.location.href = 'daftar_invoice_admin.php';
              </script>";
    } else {
        echo "<script>alert('Gagal membuat invoice!');</script>";
    }
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Buat Invoice Baru</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="pembayaran_admin.php">Pembayaran</a></li>
                <li class="breadcrumb-item active">Buat Invoice</li>
            </ol>

            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-plus-circle"></i> Form Buat Invoice Baru
                </div>
                <div class="card-body">
                    <form method="POST" id="formInvoice">
                        <div class="row">
                            <!-- Pilih Murid -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Pilih Murid <span class="text-danger">*</span></label>
                                <select name="id_murid" id="id_murid" class="form-select" required onchange="loadInfoMurid()">
                                    <option value="">-- Pilih Murid --</option>
                                    <?php while ($murid = mysqli_fetch_assoc($result_murid)): ?>
                                    <option value="<?= $murid['id_murid'] ?>" 
                                            data-kelas="<?= $murid['id_kelas_bimbel'] ?>"
                                            data-nama-kelas="<?= $murid['nama_kelas_bimbel'] ?>"
                                            data-program="<?= $murid['nama_program'] ?>"
                                            data-id-program="<?= $murid['id_program'] ?>"
                                            data-tingkat="<?= $murid['jenjang_program'] ?> Kelas <?= $murid['kelas_program'] ?>"
                                            data-id-tingkat="<?= $murid['id_tingkat'] ?>">
                                        <?= $murid['nama_murid'] ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <!-- Info Kelas (Auto Fill) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Kelas</label>
                                <input type="text" id="info_kelas" class="form-control" readonly placeholder="Pilih murid terlebih dahulu">
                                <input type="hidden" name="id_kelas_bimbel" id="id_kelas_bimbel">
                            </div>

                            <!-- Info Program (Auto Fill) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Program</label>
                                <input type="text" id="info_program" class="form-control" readonly>
                                <input type="hidden" id="id_program">
                            </div>

                            <!-- Info Tingkat (Auto Fill) -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tingkat</label>
                                <input type="text" id="info_tingkat" class="form-control" readonly>
                                <input type="hidden" id="id_tingkat">
                            </div>

                            <!-- Pilih Paket -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Paket <span class="text-danger">*</span></label>
                                <select name="jenis_paket" id="jenis_paket" class="form-select" required onchange="hitungTarif()">
                                    <option value="">-- Pilih Paket --</option>
                                    <option value="8x_sesi">8x Sesi (Paket Bulanan)</option>
                                    <option value="1x_sesi">1x Sesi (Per Pertemuan)</option>
                                </select>
                            </div>

                            <!-- Lokasi Les (Khusus Privat) -->
                            <div class="col-md-6 mb-3" id="div_lokasi" style="display: none;">
                                <label class="form-label fw-bold">Lokasi Les <span class="text-danger">*</span></label>
                                <select name="lokasi_les" id="lokasi_les" class="form-select" onchange="hitungTarif()">
                                    <option value="Ruangles">Di Ruang les</option>
                                    <option value="Rumah">Ke Rumah Murid</option>
                                </select>
                            </div>

                            <!-- Info Tarif -->
                            <div class="col-12 mb-3">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h5 class="card-title">Rincian Tarif</h5>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1">Harga per Sesi: <strong id="harga_per_sesi">-</strong></p>
                                                <p class="mb-1">Jumlah Sesi: <strong id="jumlah_sesi">-</strong></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1">Total Tagihan:</p>
                                                <h3 class="text-primary" id="total_tagihan">Rp 0</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catatan -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Catatan</label>
                                <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan tambahan (opsional)"></textarea>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Informasi:</strong> Nomor invoice akan di-generate otomatis. Status invoice akan "Belum Lunas" sampai dikonfirmasi pembayaran.
                        </div>

                        <div class="text-end">
                            <a href="daftar_invoice_admin.php" class="btn btn-secondary me-2">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" name="simpan" class="btn btn-success" id="btnSimpan" disabled>
                                <i class="fas fa-save"></i> Buat Invoice
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
    // Data tarif (akan diambil dari server via AJAX atau embedded)
    let tarifData = {};
    
    function loadInfoMurid() {
        const select = document.getElementById('id_murid');
        const option = select.options[select.selectedIndex];
        
        if (option.value) {
            document.getElementById('id_kelas_bimbel').value = option.dataset.kelas;
            document.getElementById('info_kelas').value = option.dataset.namaKelas;
            document.getElementById('info_program').value = option.dataset.program;
            document.getElementById('id_program').value = option.dataset.idProgram;
            document.getElementById('info_tingkat').value = option.dataset.tingkat;
            document.getElementById('id_tingkat').value = option.dataset.idTingkat;
            
            // Show lokasi if Privat
            if (option.dataset.idProgram == '3') {
                document.getElementById('div_lokasi').style.display = 'block';
                document.getElementById('lokasi_les').required = true;
            } else {
                document.getElementById('div_lokasi').style.display = 'none';
                document.getElementById('lokasi_les').required = false;
                document.getElementById('lokasi_les').value = 'Ruangles';
            }
            
            // Load tarif via AJAX
            loadTarif();
        } else {
            document.getElementById('info_kelas').value = '';
            document.getElementById('info_program').value = '';
            document.getElementById('info_tingkat').value = '';
        }
    }
    
    function loadTarif() {
        const idProgram = document.getElementById('id_program').value;
        const idTingkat = document.getElementById('id_tingkat').value;
        const lokasi = document.getElementById('lokasi_les').value;
        
        if (!idProgram || !idTingkat) return;
        
        // AJAX call to get tarif
        fetch(`get_tarif.php?id_program=${idProgram}&id_tingkat=${idTingkat}&lokasi=${lokasi}`)
            .then(response => response.json())
            .then(data => {
                tarifData = data;
                hitungTarif();
            });
    }
    
    function hitungTarif() {
        const jenisPaket = document.getElementById('jenis_paket').value;
        
        if (!jenisPaket || !tarifData.harga_8x) {
            document.getElementById('btnSimpan').disabled = true;
            return;
        }
        
        let hargaPerSesi, jumlahSesi, totalTagihan;
        
        if (jenisPaket == '8x_sesi') {
            totalTagihan = tarifData.harga_8x;
            hargaPerSesi = totalTagihan / 8;
            jumlahSesi = 8;
        } else {
            totalTagihan = tarifData.harga_1x;
            hargaPerSesi = tarifData.harga_1x;
            jumlahSesi = 1;
        }
        
        document.getElementById('harga_per_sesi').innerText = formatRupiah(hargaPerSesi);
        document.getElementById('jumlah_sesi').innerText = jumlahSesi + ' sesi';
        document.getElementById('total_tagihan').innerText = formatRupiah(totalTagihan);
        document.getElementById('btnSimpan').disabled = false;
    }
    
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // On change lokasi
    document.getElementById('lokasi_les').addEventListener('change', loadTarif);
    </script>

    <?php require_once "../template/footer_admin.php"; ?>
</div>
</div>
</body>
</html>