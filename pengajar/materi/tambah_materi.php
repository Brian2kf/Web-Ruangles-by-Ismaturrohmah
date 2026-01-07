<?php
session_start();

if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}
require_once "../../config.php";

// Ambil mata pelajaran dari parameter URL
$mapel = isset($_GET['mapel']) ? $_GET['mapel'] : '';

if (empty($mapel)) {
    header("Location: materi.php");
    exit();
}

$title = "Tambah Materi $mapel - Ruang Les by Ismaturrohmah";
require_once "../template/header_pengajar.php";
require_once "../template/navbar_pengajar.php";
require_once "../template/sidebar_pengajar.php";

// Ambil data tingkat program untuk dropdown
$query_tingkat = mysqli_query($koneksi, "SELECT * FROM tbl_tingkat_program ORDER BY id_tingkat");

// Handle form submission
if (isset($_POST['simpan'])) {
    $nama_materi = mysqli_real_escape_string($koneksi, $_POST['nama_materi']);
    $id_tingkat = $_POST['id_tingkat'];
    $deskripsi_materi = mysqli_real_escape_string($koneksi, $_POST['deskripsi_materi']);
    $nama_mapel = mysqli_real_escape_string($koneksi, $mapel);
    
    $error = '';
    $foto_cover = '';
    $file_materi = '';
    $tipe_file = '';
    $ukuran_file = 0;
    
    // Handle upload foto cover
    if (isset($_FILES['foto_cover']) && $_FILES['foto_cover']['error'] == 0) {
        $allowed_image_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $file_type = $_FILES['foto_cover']['type'];
        
        if (in_array($file_type, $allowed_image_types)) {
            $file_ext = pathinfo($_FILES['foto_cover']['name'], PATHINFO_EXTENSION);
            $foto_cover = 'cover_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = '../../assets/img/materi/';
            
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            move_uploaded_file($_FILES['foto_cover']['tmp_name'], $upload_path . $foto_cover);
        } else {
            $error = 'Format foto cover tidak valid! Gunakan JPG, PNG, atau GIF.';
        }
    }
    
    // Handle upload file materi
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0 && empty($error)) {
        // Tipe file yang diizinkan
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
        
        // Validasi ukuran file
        if ($file_size > $max_size) {
            $error = 'Ukuran file terlalu besar! Maksimal 10MB.';
        }
        // Validasi tipe file
        elseif (!in_array($file_type_upload, $allowed_file_types)) {
            $error = 'Tipe file tidak diizinkan! Gunakan PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, atau TXT.';
        }
        else {
            $file_ext = strtolower(pathinfo($_FILES['file_materi']['name'], PATHINFO_EXTENSION));
            $file_materi = 'materi_' . time() . '_' . rand(1000, 9999) . '.' . $file_ext;
            $upload_path = '../../uploads/materi/';
            
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            
            if (move_uploaded_file($_FILES['file_materi']['tmp_name'], $upload_path . $file_materi)) {
                $tipe_file = $file_ext;
                $ukuran_file = $file_size;
            } else {
                $error = 'Gagal mengupload file materi!';
            }
        }
    } elseif (empty($_FILES['file_materi']['name'])) {
        $error = 'File materi wajib diupload!';
    }
    
    // Insert ke database jika tidak ada error
    if (empty($error)) {
        $query = "INSERT INTO tbl_materi (nama_mapel, nama_materi, id_tingkat, deskripsi_materi, foto_cover, file_materi, tipe_file, ukuran_file) 
                  VALUES ('$nama_mapel', '$nama_materi', '$id_tingkat', '$deskripsi_materi', '$foto_cover', '$file_materi', '$tipe_file', '$ukuran_file')";
        
        if (mysqli_query($koneksi, $query)) {
            echo "<script>
                    alert('Materi berhasil ditambahkan!');
                    window.location.href = 'daftar_materi.php?mapel=" . urlencode($mapel) . "';
                  </script>";
        } else {
            $error = 'Gagal menyimpan data ke database!';
        }
    }
    
    // Tampilkan error jika ada
    if (!empty($error)) {
        echo "<script>alert('$error');</script>";
    }
}
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <h1 class="mt-4">Materi Pembelajaran</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item"><a href="../dashboard_pengajar.php">Home</a></li>
                <li class="breadcrumb-item"><a href="materi.php">Materi Pembelajaran</a></li>
                <li class="breadcrumb-item"><a href="daftar_materi.php?mapel=<?= urlencode($mapel) ?>"><?= $mapel ?></a></li>
                <li class="breadcrumb-item active">Tambah Materi</li>
            </ol>

            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Catatan:</strong> Pastikan file materi sudah benar sebelum diupload.
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <span class="h5 my-2"><i class="fas fa-book" style="padding-top: 10px;"></i> Tambah Materi - <?= $mapel ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Unggah Foto Cover</label>
                                <div class="border rounded p-4 text-center bg-light" style="min-height: 200px;">
                                    <div id="preview-container" class="mb-3">
                                        <i class="fas fa-image fa-4x text-muted"></i>
                                        <p class="text-muted mt-2 mb-0">Preview foto cover</p>
                                    </div>
                                    <input type="file" name="foto_cover" id="foto_cover" class="form-control" accept="image/*" onchange="previewImage(this)">
                                    <small class="text-muted">Format: JPG, PNG, GIF (Opsional)</small>
                                </div>
                            </div>

                            <div class="col-md-6 mb-4">
                                <label class="form-label fw-bold">Unggah File Materi <span class="text-danger">*</span></label>
                                <div class="border rounded p-4 text-center bg-light" style="min-height: 200px;">
                                    <div id="file-info" class="mb-3">
                                        <i class="fas fa-file fa-4x text-muted"></i>
                                        <p class="text-muted mt-2 mb-0">Pilih file materi</p>
                                    </div>
                                    <input type="file" name="file_materi" id="file_materi" class="form-control" required onchange="showFileInfo(this)">
                                    <small class="text-muted">Format: PDF, DOC, DOCX, PPT, PPTX, XLS, XLSX, ZIP, TXT<br>Maksimal: 10MB</small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Judul Materi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_materi" class="form-control" required placeholder="Masukkan judul materi">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Tingkat <span class="text-danger">*</span></label>
                                <select name="id_tingkat" class="form-select" required>
                                    <option value="">-- Pilih Tingkat --</option>
                                    <?php while ($tingkat = mysqli_fetch_assoc($query_tingkat)): ?>
                                    <option value="<?= $tingkat['id_tingkat'] ?>">
                                        <?= $tingkat['jenjang_program'] ?> - Kelas <?= $tingkat['kelas_program'] ?>
                                    </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Deskripsi Materi <span class="text-danger">*</span></label>
                                <textarea name="deskripsi_materi" class="form-control" rows="5" required placeholder="Masukkan deskripsi materi"></textarea>
                            </div>
                        </div>

                        <div class="text-end mt-4">
                            <a href="daftar_materi.php?mapel=<?= urlencode($mapel) ?>" class="btn btn-danger me-2">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit" name="simpan" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Materi
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
                previewContainer.innerHTML = `
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
            const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
            const fileExt = fileName.split('.').pop().toUpperCase();
            
            // Icon berdasarkan tipe file
            let iconClass = 'fa-file';
            if (fileExt === 'PDF') iconClass = 'fa-file-pdf text-danger';
            else if (['DOC', 'DOCX'].includes(fileExt)) iconClass = 'fa-file-word text-primary';
            else if (['PPT', 'PPTX'].includes(fileExt)) iconClass = 'fa-file-powerpoint text-warning';
            else if (['XLS', 'XLSX'].includes(fileExt)) iconClass = 'fa-file-excel text-success';
            else if (['ZIP', 'RAR'].includes(fileExt)) iconClass = 'fa-file-archive text-secondary';
            
            fileInfo.innerHTML = `
                <i class="fas ${iconClass} fa-4x mb-2"></i>
                <p class="mb-1 fw-bold">${fileName}</p>
                <p class="text-muted mb-0 small">${fileSize} MB</p>
            `;
        }
    }
    </script>

    <?php require_once "../template/footer_pengajar.php"; ?>
</div>
</div>
</body>
</html>