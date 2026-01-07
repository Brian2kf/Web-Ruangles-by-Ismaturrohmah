<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";

// Ambil mata pelajaran dan ID dari parameter URL
$mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';
$id_materi = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (empty($mapel) || $id_materi == 0) {
    header("Location: materi_admin.php");
    exit();
}

// Ambil data materi yang akan diedit
$query_materi = mysqli_query($koneksi, "SELECT * FROM tbl_materi WHERE id_materi = $id_materi");
if (mysqli_num_rows($query_materi) == 0) {
    echo "<script>alert('Materi tidak ditemukan!'); window.location.href='daftar_materi_admin.php?mapel=" . urlencode($mapel) . "';</script>";
    exit();
}
$materi = mysqli_fetch_assoc($query_materi);

$title = "Edit Materi $mapel - Ruang Les by Ismaturrohmah";
require_once "../template/header_admin.php";
require_once "../template/navbar_admin.php";
require_once "../template/sidebar_admin.php";

// Ambil data tingkat program untuk dropdown
$query_tingkat = mysqli_query($koneksi, "SELECT * FROM tbl_tingkat_program ORDER BY id_tingkat");

// Handle form submission
if (isset($_POST['update'])) {
    $nama_materi = mysqli_real_escape_string($koneksi, $_POST['nama_materi']);
    $id_tingkat = $_POST['id_tingkat'];
    $deskripsi_materi = mysqli_real_escape_string($koneksi, $_POST['deskripsi_materi']);
    
    $error = '';
    $foto_cover = $materi['foto_cover']; // Default: pakai foto lama
    $file_materi = $materi['file_materi']; // Default: pakai file lama
    $tipe_file = $materi['tipe_file'];
    $ukuran_file = $materi['ukuran_file'];
    
    // Handle upload foto cover baru (jika ada)
    if (isset($_FILES['foto_cover']) && $_FILES['foto_cover']['error'] == 0) {
        $allowed_image_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['foto_cover']['type'];
        
        if (in_array($file_type, $allowed_image_types)) {
            // Hapus foto lama jika ada
            if (!empty($materi['foto_cover']) && file_exists("../../assets/img/materi/" . $materi['foto_cover'])) {
                unlink("../../assets/img/materi/" . $materi['foto_cover']);
            }
            
            $file_ext = pathinfo($_FILES['foto_cover']['name'], PATHINFO_EXTENSION);
            $foto_cover = 'cover_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = '../../assets/img/materi/';
            
            move_uploaded_file($_FILES['foto_cover']['tmp_name'], $upload_path . $foto_cover);
        } else {
            $error = 'Format foto cover tidak valid! Gunakan JPG, PNG, atau GIF.';
        }
    }
    
    // Handle upload file materi baru (jika ada)
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0 && empty($error)) {
        $allowed_file_types = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
            'application/x-zip-compressed',
            'application/x-rar-compressed',
            'text/plain'
        ];
        
        $file_type_upload = $_FILES['file_materi']['type'];
        $file_size = $_FILES['file_materi']['size'];
        $max_size = 10 * 1024 * 1024; // 10MB
        
        if ($file_size > $max_size) {
            $error = 'Ukuran file terlalu besar! Maksimal 10MB.';
        } elseif (!in_array($file_type_upload, $allowed_file_types)) {
            $error = 'Tipe file tidak diizinkan!';
        } else {
            // Hapus file lama jika ada
            if (!empty($materi['file_materi']) && file_exists("../../uploads/materi/" . $materi['file_materi'])) {
                unlink("../../uploads/materi/" . $materi['file_materi']);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['file_materi']['name'], PATHINFO_EXTENSION));
            $file_materi = 'materi_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = '../../uploads/materi/';
            
            if (move_uploaded_file($_FILES['file_materi']['tmp_name'], $upload_path . $file_materi)) {
                $tipe_file = $file_ext;
                $ukuran_file = $file_size;
            } else {
                $error = 'Gagal mengupload file materi!';
            }
        }
    }
    
    // Update database jika tidak ada error
    if (empty($error)) {
        $query = "UPDATE tbl_materi SET 
                  nama_materi = '$nama_materi',
                  id_tingkat = '$id_tingkat',
                  deskripsi_materi = '$deskripsi_materi',
                  foto_cover = '$foto_cover',
                  file_materi = '$file_materi',
                  tipe_file = '$tipe_file',
                  ukuran_file = '$ukuran_file'
                  WHERE id_materi = $id_materi";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                    alert('Materi berhasil diupdate!');
                    window.location.href = 'daftar_materi_admin.php?mapel=" . urlencode($mapel) . "';
                  </script>";
        } else {
            $error = 'Gagal mengupdate data ke database!';
        }
    }
    
    if (!empty($error)) {
        echo "<script>alert('$error');</script>";
    }
}

// Fungsi format ukuran file
function formatFileSize($bytes) {
    if ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    }
    return $bytes . ' B';
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Materi Pembelajaran</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_admin.php">Home</a></li>
                <li class="breadcrumb-item"><a href="materi_admin.php">Materi Pembelajaran</a></li>
                <li class="breadcrumb-item"><a href="daftar_materi_admin.php?mapel=<?= urlencode($mapel) ?>"><?= $mapel ?></a></li>
                <li class="breadcrumb-item active">Edit Materi</li>
            </ol>

            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>Perhatian:</strong> Jika Anda mengganti foto cover atau file materi, file lama akan dihapus dan diganti dengan file baru.
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="h5 my-2"><i class="fas fa-book" style="padding-top: 10px;"></i> Edit Materi - <?= $mapel ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Foto Cover Saat Ini</label>
                                <div class="border rounded p-3 text-center bg-light mb-3">
                                    <?php if (!empty($materi['foto_cover']) && file_exists("../../assets/img/materi/" . $materi['foto_cover'])): ?>
                                        <img src="../../assets/img/materi/<?= $materi['foto_cover'] ?>" class="img-fluid rounded" style="max-height: 200px;">
                                    <?php else: ?>
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                        <p class="text-muted mt-2">Tidak ada foto cover</p>
                                    <?php endif; ?>
                                </div>
                                
                                <label class="form-label fw-bold">Ganti Foto Cover (Opsional)</label>
                                <div class="border rounded p-4 text-center bg-light">
                                    <div id="preview-container" class="mb-3" style="display: none;">
                                        <p class="text-muted mb-2">Preview foto baru:</p>
                                    </div>
                                    <input type="file" name="foto_cover" id="foto_cover" class="form-control" accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengganti</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">File Materi Saat Ini</label>
                                <div class="border rounded p-3 bg-light mb-3">
                                    <?php if (!empty($materi['file_materi'])): ?>
                                        <div class="text-center">
                                            <i class="fas fa-file fa-3x text-primary mb-2"></i>
                                            <p class="mb-1 fw-bold"><?= $materi['file_materi'] ?></p>
                                            <p class="text-muted mb-2">
                                                Tipe: <?= strtoupper($materi['tipe_file']) ?> | 
                                                Ukuran: <?= formatFileSize($materi['ukuran_file']) ?>
                                            </p>
                                            <a href="download_materi.php?id=<?= $id_materi ?>" class="btn btn-sm btn-primary">
                                                <i class="fas fa-download"></i> Download File Saat Ini
                                            </a>
                                        </div>
                                    <?php else: ?>
                                        <p class="text-muted text-center">Tidak ada file materi</p>
                                    <?php endif; ?>
                                </div>
                                
                                <label class="form-label fw-bold">Ganti File Materi (Opsional)</label>
                                <div class="border rounded p-4 text-center bg-light">
                                    <div id="file-info" class="mb-3" style="display: none;">
                                        <p class="text-muted mb-2">File baru:</p>
                                    </div>
                                    <input type="file" name="file_materi" id="file_materi" class="form-control" onchange="showFileInfo(this)">
                                    <small class="text-muted">Kosongkan jika tidak ingin mengganti<br>Max: 10MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Judul Materi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_materi" class="form-control" required value="<?= htmlspecialchars($materi['nama_materi']) ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tingkat <span class="text-danger">*</span></label>
                                <select name="id_tingkat" class="form-select" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <?php while ($tingkat = mysqli_fetch_assoc($query_tingkat)): ?>
                                    <option value="<?= $tingkat['id_tingkat'] ?>" <?= $tingkat['id_tingkat'] == $materi['id_tingkat'] ? 'selected' : '' ?>>
                                        <?= $tingkat['jenjang_program'] ?> - Kelas <?= $tingkat['kelas_program'] ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Deskripsi Materi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi_materi" class="form-control" rows="5" required><?= htmlspecialchars($materi['deskripsi_materi']) ?></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-0">
                            <a href="daftar_materi_admin.php?mapel=<?= urlencode($mapel) ?>" class="btn btn-danger me-2">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" name="update" class="btn btn-primary text-white">
                                <i class="fas fa-save"></i> Update Materi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <script>
    function previewImage(input) {
        const previewContainer = document.getElementById('preview-container');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewContainer.style.display = 'block';
                previewContainer.innerHTML = `
                    <p class="text-muted mb-2">Preview foto baru:</p>
                    <img src="${e.target.result}" class="img-fluid rounded" style="max-height: 180px; object-fit: cover;">
                `;
            };
            
            reader.readAsDataURL(input.files[0]);
        }
    }

    function showFileInfo(input) {
        const fileInfo = document.getElementById('file-info');
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileName = file.name;
            const fileSize = (file.size / 1024 / 1024).toFixed(2);
            const fileExt = fileName.split('.').pop().toUpperCase();
            
            let iconClass = 'fa-file';
            if (fileExt === 'PDF') iconClass = 'fa-file-pdf text-danger';
            else if (['DOC', 'DOCX'].includes(fileExt)) iconClass = 'fa-file-word text-primary';
            else if (['PPT', 'PPTX'].includes(fileExt)) iconClass = 'fa-file-powerpoint text-warning';
            else if (['XLS', 'XLSX'].includes(fileExt)) iconClass = 'fa-file-excel text-success';
            
            fileInfo.style.display = 'block';
            fileInfo.innerHTML = `
                <p class="text-muted mb-2">File baru:</p>
                <i class="fas ${iconClass} fa-3x mb-2"></i>
                <p class="mb-1 fw-bold">${fileName}</p>
                <p class="text-muted mb-0 small">${fileSize} MB</p>
            `;
        }
    }
    </script>

    <?php require_once "../template/footer_admin.php"; ?>
</div>
</body>
</html>