<?php
session_start();

// Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
require_once "fungsi_invoice.php";

// 1. Ambil ID Invoice dari URL
$id_pembayaran = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_pembayaran == 0) {
    header("Location: daftar_invoice_admin.php");
    exit();
}

// 2. Ambil Data Invoice Saat Ini
$query = "SELECT p.*, m.nama_murid, k.nama_kelas_bimbel, 
          k.id_program, k.id_tingkat, tp.nama_program 
          FROM tbl_pembayaran p
          JOIN tbl_data_murid m ON p.id_murid = m.id_murid
          JOIN tbl_kelas_bimbel k ON p.id_kelas_bimbel = k.id_kelas_bimbel
          JOIN tbl_tipe_program tp ON k.id_program = tp.id_program
          WHERE p.id_pembayaran = $id_pembayaran";

$result = mysqli_query($koneksi, $query);
$invoice = mysqli_fetch_assoc($result);

if (!$invoice) {
    echo "<script>alert('Invoice tidak ditemukan!'); window.location.href='daftar_invoice_admin.php';</script>";
    exit();
}

// Cek apakah Program PRIVAT (ID 3)
// Hanya Program Privat yang boleh ganti lokasi ke Rumah.
$is_privat = ($invoice['id_program'] == 3);

// PENGAMAN 1: Cegah Edit jika Status Lunas
if ($invoice['status_pembayaran'] == 'Lunas') {
    echo "<script>
            alert('⛔ AKSES DITOLAK!\\nInvoice LUNAS tidak boleh diedit.'); 
            window.location.href='detail_invoice_admin.php?id=$id_pembayaran';
          </script>";
    exit();
}

// PROSES UPDATE DATA
if (isset($_POST['update'])) {
    $jenis_paket_baru = $_POST['jenis_paket'];
    $catatan_baru     = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    
    // Tangkap Lokasi Les (Jika didisable di form, ambil dari hidden input atau default)
    if (isset($_POST['lokasi_les'])) {
        $lokasi_les_baru = $_POST['lokasi_les'];
    } else {
        $lokasi_les_baru = 'Ruangles';
    }

    // PENGAMAN EXTRA: Validasi Lokasi vs Program
    if (!$is_privat && $lokasi_les_baru == 'Rumah') {
        echo "<script>
                alert('GAGAL UPDATE! ❌\\n\\nProgram Reguler/Semi Privat TIDAK BISA memilih lokasi Rumah (Tarif tidak tersedia).');
                window.history.back();
              </script>";
        exit();
    }

    // Tentukan Jumlah Sesi Baru
    $jumlah_sesi_baru = ($jenis_paket_baru == '8x_sesi') ? 8 : 1;
    $sesi_terpakai_saat_ini = (int)$invoice['sesi_terpakai'];

    // PENGAMAN 2: Validasi Logika Sesi (Downgrade Check)
    if ($jumlah_sesi_baru < $sesi_terpakai_saat_ini) {
        echo "<script>
                alert('GAGAL UPDATE! ❌\\n\\nMurid ini sudah menggunakan $sesi_terpakai_saat_ini sesi.\\nTidak bisa ubah ke paket $jumlah_sesi_baru sesi.');
              </script>";
    } else {
        // Hitung Tarif Baru
        $tarif = getTarif($koneksi, $invoice['id_program'], $invoice['id_tingkat'], $lokasi_les_baru);
        
        // Validasi Tarif Nol (Mencegah bug Rp 0 jika data tarif kosong)
        $harga_cek = ($jenis_paket_baru == '8x_sesi') ? $tarif['harga_8x'] : $tarif['harga_1x'];
        
        if ($harga_cek <= 0) {
             echo "<script>
                alert('GAGAL! Tarif tidak ditemukan untuk kombinasi Program & Lokasi ini.');
              </script>";
        } else {
            if ($jenis_paket_baru == '8x_sesi') {
                $total_tagihan_baru = $tarif['harga_8x'];
                $harga_per_sesi_baru = $total_tagihan_baru / 8;
            } else {
                $total_tagihan_baru = $tarif['harga_1x'];
                $harga_per_sesi_baru = $tarif['harga_1x'];
            }

            // Hitung Sisa Sesi Baru
            $sesi_tersisa_baru = $jumlah_sesi_baru - $sesi_terpakai_saat_ini;

            // Update Database
            $query_update = "UPDATE tbl_pembayaran SET 
                            jenis_paket = '$jenis_paket_baru',
                            jumlah_sesi = $jumlah_sesi_baru,
                            sesi_tersisa = $sesi_tersisa_baru,
                            harga_per_sesi = $harga_per_sesi_baru,
                            total_tagihan = $total_tagihan_baru,
                            lokasi_les = '$lokasi_les_baru',
                            catatan_pembayaran = '$catatan_baru',
                            updated_at = NOW()
                            WHERE id_pembayaran = $id_pembayaran";

            if (mysqli_query($koneksi, $query_update)) {
                echo "<script>
                        alert('✅ Invoice berhasil diperbarui!');
                        window.location.href = 'detail_invoice_admin.php?id=$id_pembayaran';
                    </script>";
            } else {
                echo "<script>alert('Gagal mengupdate database.');</script>";
            }
        }
    }
}

$title = "Edit Invoice - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Edit Invoice</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="daftar_invoice_admin.php">Daftar Invoice</a></li>
                <li class="breadcrumb-item active">Edit</li>
            </ol>

            <div class="alert alert-info border-0 shadow-sm">
                <i class="fas fa-info-circle"></i> 
                <strong>Perhatian:</strong> Nama Murid dikunci. Untuk Program Reguler/Semi Privat, lokasi Les dikunci di Ruangles.
            </div>

            <div class="card">
                <div class="card-header">
                    <i class="fas fa-edit"></i> Form Edit Invoice : <strong><?= $invoice['no_invoice'] ?></strong>
                </div>
                <div class="card-body">

                    <form method="POST">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Nama Murid</label>
                                <input type="text" class="form-control bg-light" value="<?= $invoice['nama_murid'] ?>" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Kelas & Program</label>
                                <input type="text" class="form-control bg-light" value="<?= $invoice['nama_kelas_bimbel'] ?> - <?= $invoice['nama_program'] ?>" readonly>
                            </div>

                            <input type="hidden" id="id_program" value="<?= $invoice['id_program'] ?>">
                            <input type="hidden" id="id_tingkat" value="<?= $invoice['id_tingkat'] ?>">
                            <input type="hidden" id="sesi_terpakai" value="<?= $invoice['sesi_terpakai'] ?>">

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Jenis Paket <span class="text-danger">*</span></label>
                                <select name="jenis_paket" id="jenis_paket" class="form-select" required onchange="hitungUlang()">
                                    <option value="8x_sesi" <?= $invoice['jenis_paket'] == '8x_sesi' ? 'selected' : '' ?>>8x Sesi (Paket Bulanan)</option>
                                    <option value="1x_sesi" <?= $invoice['jenis_paket'] == '1x_sesi' ? 'selected' : '' ?>>1x Sesi (Per Pertemuan)</option>
                                </select>
                                <div id="warning_paket" class="text-danger mt-1 small" style="display:none;">
                                    <i class="fas fa-exclamation-triangle"></i> Tidak bisa ubah ke 1x sesi karena murid sudah pakai <span id="info_terpakai"></span> sesi.
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Lokasi Les <span class="text-danger">*</span></label>
                                
                                <?php if ($is_privat): ?>
                                    <select name="lokasi_les" id="lokasi_les" class="form-select" required onchange="hitungUlang()">
                                        <option value="Ruangles" <?= $invoice['lokasi_les'] == 'Ruangles' ? 'selected' : '' ?>>Di Ruangles</option>
                                        <option value="Rumah" <?= $invoice['lokasi_les'] == 'Rumah' ? 'selected' : '' ?>>Ke Rumah Murid</option>
                                    </select>
                                    <small class="text-muted">Lokasi mempengaruhi tarif Privat.</small>
                                <?php else: ?>
                                    <input type="text" class="form-control bg-light" value="Di Ruangles (Program <?= $invoice['nama_program'] ?>)" readonly>
                                    <input type="hidden" name="lokasi_les" id="lokasi_les" value="Ruangles">
                                    <small class="text-muted text-danger">Hanya program Privat yang bisa memilih lokasi Rumah.</small>
                                <?php endif; ?>
                            </div>

                            <div class="col-12 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title fw-bold">Simulasi Perubahan:</h6>
                                        <div class="row">
                                            <div class="col-md-4">
                                                <small>Sesi Terpakai:</small><br>
                                                <strong><?= $invoice['sesi_terpakai'] ?> Sesi</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small>Sisa Sesi Baru:</small><br>
                                                <strong id="preview_sisa">-</strong>
                                            </div>
                                            <div class="col-md-4">
                                                <small>Total Tagihan Baru:</small><br>
                                                <h4 class="text-success" id="preview_total">Rp 0</h4>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Catatan Pembayaran</label>
                                <textarea name="catatan" class="form-control" rows="3"><?= $invoice['catatan_pembayaran'] ?></textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <a href="detail_invoice_admin.php?id=<?= $id_pembayaran ?>" class="btn btn-secondary me-2">Batal</a>
                            <button type="submit" name="update" id="btnUpdate" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
    function formatRupiah(angka) {
        return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function hitungUlang() {
        const idProgram = document.getElementById('id_program').value;
        const idTingkat = document.getElementById('id_tingkat').value;
        const lokasi = document.getElementById('lokasi_les').value;
        const jenisPaket = document.getElementById('jenis_paket').value;
        const sesiTerpakai = parseInt(document.getElementById('sesi_terpakai').value);
        
        const btnUpdate = document.getElementById('btnUpdate');
        const warningPaket = document.getElementById('warning_paket');
        const infoTerpakai = document.getElementById('info_terpakai');

        // Validasi Sesi
        let jumlahSesiBaru = (jenisPaket === '8x_sesi') ? 8 : 1;
        
        if (jumlahSesiBaru < sesiTerpakai) {
            warningPaket.style.display = 'block';
            infoTerpakai.innerText = sesiTerpakai;
            btnUpdate.disabled = true; 
            document.getElementById('preview_total').innerText = "Tidak Valid";
            document.getElementById('preview_sisa').innerText = "Minus";
            return; 
        } else {
            warningPaket.style.display = 'none';
            btnUpdate.disabled = false;
        }

        // Ambil Tarif
        fetch(`get_tarif.php?id_program=${idProgram}&id_tingkat=${idTingkat}&lokasi=${lokasi}`)
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    let totalTagihan = 0;
                    if (jenisPaket === '8x_sesi') {
                        totalTagihan = data.harga_8x;
                    } else {
                        totalTagihan = data.harga_1x;
                    }
                    
                    // Update Tampilan
                    document.getElementById('preview_total').innerText = formatRupiah(totalTagihan);
                    document.getElementById('preview_sisa').innerText = (jumlahSesiBaru - sesiTerpakai) + " Sesi";
                }
            });
    }

    document.addEventListener('DOMContentLoaded', hitungUlang);
    </script>

    <?php require_once "../template/footer_admin.php"; ?>
</div>
</div>
</body>
</html>