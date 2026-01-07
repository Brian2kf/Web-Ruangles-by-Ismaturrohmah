<?php
// Koneksi database
require_once "../../config.php";

// 1. Validasi ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("ID materi tidak valid.");
}
$id_materi = (int)$_GET['id'];

// 2. Query Data Materi
$query = mysqli_query($koneksi, "SELECT * FROM tbl_materi WHERE id_materi = $id_materi");

if (mysqli_num_rows($query) == 0) {
    die("Data materi tidak ditemukan di database.");
}
$data = mysqli_fetch_assoc($query);

// 3. Cek Fisik File
// Perhatikan path: folder public sejajar dengan uploads, jadi mundur satu langkah (../)
$file_path = '../../uploads/materi/' . $data['file_materi'];

if (!file_exists($file_path) || empty($data['file_materi'])) {
    die("File fisik tidak ditemukan di server.");
}

// 4. Proses Download
$file_name = $data['file_materi'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
// Nama file saat didownload user (bersihkan karakter aneh)
$download_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $data['nama_materi']) . '.' . $file_ext;

// Tentukan Content-Type
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
$content_type = isset($mime_types[$file_ext]) ? $mime_types[$file_ext] : 'application/octet-stream';

// Header untuk memaksa download
header('Content-Description: File Transfer');
header('Content-Type: ' . $content_type);
header('Content-Disposition: attachment; filename="' . $download_name . '"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($file_path));

// Bersihkan output buffer agar file tidak korup
ob_clean();
flush();

// Baca file
readfile($file_path);
exit;
?>