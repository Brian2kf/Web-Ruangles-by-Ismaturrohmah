<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";
require_once "fungsi_invoice.php";

$title = "Konfirmasi Pembayaran - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Ambil ID invoice
$id_pembayaran = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id_pembayaran == 0) {
    header("Location: daftar_invoice_admin.php");
    exit();
}

// Ambil data invoice
$query = "SELECT p.*, m.nama_murid, k.nama_kelas_bimbel, tp.nama_program
          FROM tbl_pembayaran p
          JOIN tbl_data_murid m ON p.id_murid = m.id_murid
          JOIN tbl_kelas_bimbel k ON p.id_kelas_bimbel = k.id_kelas_bimbel
          JOIN tbl_tipe_program tp ON k.id_program = tp.id_program
          WHERE p.id_pembayaran = $id_pembayaran";

$result = mysqli_query($koneksi, $query);

if (mysqli_num_rows($result) == 0) {
    echo "<script>alert('Invoice tidak ditemukan!'); window.location.href='daftar_invoice_admin.php';</script>";
    exit();
}

$invoice = mysqli_fetch_assoc($result);

// Cek apakah sudah lunas
if ($invoice['status_pembayaran'] == 'Lunas') {
    echo "<script>alert('Invoice ini sudah lunas!'); window.location.href='detail_invoice_admin.php?id=$id_pembayaran';</script>";
    exit();
}

// Handle form submit
if (isset($_POST['konfirmasi'])) {
    $tgl_pembayaran = $_POST['tgl_pembayaran'];
    $metode_bayar = mysqli_real_escape_string($koneksi, $_POST['metode_bayar']);
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    
    $bukti_bayar = '';
    $error = '';
    
    // Handle upload bukti transfer
    if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];
        $file_type = $_FILES['bukti_bayar']['type'];
        $file_size = $_FILES['bukti_bayar']['size'];
        $max_size = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file_type, $allowed_types)) {
            $error = 'Format file harus JPG atau PNG!';
        } elseif ($file_size > $max_size) {
            $error = 'Ukuran file maksimal 2MB!';
        } else {
            $file_ext = pathinfo($_FILES['bukti_bayar']['name'], PATHINFO_EXTENSION);
            
            // PERBAIKAN: Sanitize no_invoice - ganti "/" dengan "-"
            $no_invoice_clean = str_replace('/', '-', $invoice['no_invoice']);
            $bukti_bayar = 'bukti_' . $no_invoice_clean . '_' . time() . '.' . $file_ext;
            
            // PERBAIKAN: Path yang benar dari admin/pembayaran/ ke root
            $upload_path = '../../uploads/bukti_bayar/';
            
            // Hapus bukti lama jika ada
            if (!empty($invoice['bukti_bayar']) && file_exists($upload_path . $invoice['bukti_bayar'])) {
                unlink($upload_path . $invoice['bukti_bayar']);
            }
            
            if (!move_uploaded_file($_FILES['bukti_bayar']['tmp_name'], $upload_path . $bukti_bayar)) {
                $error = 'Gagal mengupload bukti transfer! Path: ' . $upload_path;
            }
        }
    }
    
    if (empty($error)) {
        // Update status pembayaran
        $query_update = "UPDATE tbl_pembayaran SET
                         status_pembayaran = 'Lunas',
                         tgl_pembayaran = '$tgl_pembayaran',
                         metode_bayar = '$metode_bayar'";
        
        if (!empty($bukti_bayar)) {
            $query_update .= ", bukti_bayar = '$bukti_bayar'";
        }
        
        if (!empty($catatan)) {
            $query_update .= ", catatan_pembayaran = '$catatan'";
        }
        
        $query_update .= " WHERE id_pembayaran = $id_pembayaran";
        
        if (mysqli_query($koneksi, $query_update)) {
            echo "<script>
                    alert('Pembayaran berhasil dikonfirmasi!');
                    window.location.href = 'detail_invoice_admin.php?id=$id_pembayaran';
                  </script>";
        } else {
            $error = 'Gagal mengupdate status pembayaran!';
        }
    }
    
    if (!empty($error)) {
        echo "<script>alert('$error');</script>";
    }
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Konfirmasi Pembayaran</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="pembayaran_admin.php">Pembayaran</a></li>
                <li class="breadcrumb-item"><a href="daftar_invoice_admin.php">Daftar Invoice</a></li>
                <li class="breadcrumb-item active">Konfirmasi Pembayaran</li>
            </ol>

            <div class="row">
                <!-- Info Invoice -->
                <div class="col-md-5 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-file-invoice"></i> Informasi Invoice
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <tr>
                                    <th width="40%">No Invoice</th>
                                    <td><strong><?= $invoice['no_invoice'] ?></strong></td>
                                </tr>
                                <tr>
                                    <th>Murid</th>
                                    <td><?= $invoice['nama_murid'] ?></td>
                                </tr>
                                <tr>
                                    <th>Kelas</th>
                                    <td><?= $invoice['nama_kelas_bimbel'] ?></td>
                                </tr>
                                <tr>
                                    <th>Program</th>
                                    <td><?= $invoice['nama_program'] ?></td>
                                </tr>
                                <tr>
                                    <th>Paket</th>
                                    <td>
                                        <?= $invoice['jenis_paket'] == '8x_sesi' ? '8x Sesi' : '1x Sesi' ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Lokasi Les</th>
                                    <td><?= $invoice['lokasi_les'] ?></td>
                                </tr>
                                <tr>
                                    <th>Total Tagihan</th>
                                    <td><h5 class="text-primary mb-0"><?= formatRupiah($invoice['total_tagihan']) ?></h5></td>
                                </tr>
                                <tr>
                                    <th>Status Saat Ini</th>
                                    <td>
                                        <span class="badge bg-<?= getBadgeStatus($invoice['status_pembayaran']) ?>">
                                            <?= $invoice['status_pembayaran'] ?>
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Form Konfirmasi -->
                <div class="col-md-7">
                    <div class="card shadow">
                        <div class="card-header">
                            <i class="fas fa-check-circle"></i> Form Konfirmasi Pembayaran
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Tanggal Pembayaran <span class="text-danger">*</span></label>
                                    <input type="date" name="tgl_pembayaran" class="form-control" value="<?= date('Y-m-d') ?>" required max="<?= date('Y-m-d') ?>">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Metode Pembayaran <span class="text-danger">*</span></label>
                                    <select name="metode_bayar" class="form-select" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="Transfer Bank">Transfer Bank</option>
                                        <option value="Tunai">Tunai</option>
                                        <option value="E-Wallet (DANA)">E-Wallet (DANA)</option>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Upload Bukti Transfer</label>
                                    <input type="file" name="bukti_bayar" class="form-control" accept="image/*" onchange="previewBukti(this)">
                                    <small class="text-muted">Format: JPG, PNG (Max 2MB) - Opsional</small>
                                    
                                    <div id="preview-bukti" class="mt-3" style="display: none;">
                                        <p class="mb-2">Preview:</p>
                                        <img id="img-preview" src="" class="img-fluid rounded border" style="max-height: 300px;">
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">Catatan Tambahan</label>
                                    <textarea name="catatan" class="form-control" rows="3" placeholder="Catatan pembayaran (opsional)"><?= $invoice['catatan_pembayaran'] ?></textarea>
                                </div>

                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>Perhatian:</strong> Setelah dikonfirmasi, status invoice akan berubah menjadi "Lunas" dan murid dapat mulai menggunakan sesi yang dibayarkan.
                                </div>

                                <div class="text-end">
                                    <a href="daftar_invoice_admin.php" class="btn btn-secondary me-2">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                    <button type="submit" name="konfirmasi" class="btn btn-success">
                                        <i class="fas fa-check"></i> Konfirmasi Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    function previewBukti(input) {
        const preview = document.getElementById('preview-bukti');
        const img = document.getElementById('img-preview');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>

    <?php require_once "../template/footer_admin.php"; ?>
</div>
</div>
</body>
</html>