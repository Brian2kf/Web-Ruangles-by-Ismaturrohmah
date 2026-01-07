<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '3') {
    header("location: ../../auth/login.php");
    exit();
}

require_once "../../config.php";

// Validasi parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID materi tidak valid!");
}

$id_materi = (int)$_GET['id'];

// Ambil data materi dari database
$query = mysqli_query($koneksi, "SELECT * FROM tbl_materi WHERE id_materi = $id_materi");

if (mysqli_num_rows($query) == 0) {
    die("Materi tidak ditemukan!");
}

$data = mysqli_fetch_assoc($query);

// Cek apakah file materi ada
if (empty($data['file_materi'])) {
    die("File materi tidak tersedia!");
}

// Path file
$file_path = '../../uploads/materi/' . $data['file_materi'];

// Cek apakah file fisik ada
if (!file_exists($file_path)) {
    die("File tidak ditemukan di server!");
}

// Ambil informasi file
$file_name = $data['file_materi'];
$file_size = filesize($file_path);
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Set nama file untuk download (gunakan nama materi)
$download_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['nama_materi']) . '.' . $file_ext;

// Tentukan MIME type berdasarkan ekstensi
$mime_types = [
    'pdf' => 'application/pdf',
    'doc' => 'application/msword',
    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'ppt' => 'application/vnd.ms-powerpoint',
    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    'xls' => 'application/vnd.ms-excel',
    'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'zip' => 'application/zip',
    'rar' => 'application/x-rar-compressed',
    'txt' => 'text/plain'
];

$mime_type = isset($mime_types[$file_ext]) ? $mime_types[$file_ext] : 'application/octet-stream';

// Bersihkan output buffer
if (ob_get_level()) {
    ob_end_clean();
}

// Set headers untuk download
header('Content-Description: File Transfer');
header('Content-Type: ' . $mime_type);
header('Content-Disposition: attachment; filename="' . $download_name . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . $file_size);

// Baca dan kirim file
readfile($file_path);

// Log download (opsional - bisa ditambahkan tabel log_download)
// $user_id = $_SESSION['ssLogin'];
// $tgl_download = date('Y-m-d H:i:s');
// mysqli_query($koneksi, "INSERT INTO log_download (id_materi, id_user, tanggal_download) VALUES ($id_materi, $user_id, '$tgl_download')");

exit();
?>