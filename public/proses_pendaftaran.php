<?php
require_once '../config.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

try {
    // Validasi request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Validasi kehadiran required fields
    $required_fields = [
        'nama_camur', 'tgl_lahir_camur', 'jk_camur', 'sekolah_camur', 
        'id_tingkat', 'alamat_camur', 'nama_orgtua_wali', 'telepon_orgtua_wali', 
        'email_orgtua_wali', 'id_program'
    ];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Field '{$field}' tidak boleh kosong");
        }
    }

    // Sanitasi data
    $nama_camur = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['nama_camur']));
    $tgl_lahir_camur = mysqli_real_escape_string($koneksi, $_POST['tgl_lahir_camur']);
    $jk_camur = mysqli_real_escape_string($koneksi, $_POST['jk_camur']);
    $sekolah_camur = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['sekolah_camur']));
    $id_tingkat = (int)$_POST['id_tingkat'];
    $alamat_camur = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['alamat_camur']));
    $nama_orgtua_wali = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['nama_orgtua_wali']));
    $telepon_orgtua_wali = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['telepon_orgtua_wali']));
    $email_orgtua_wali = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['email_orgtua_wali']));
    $karakteristik_camur = mysqli_real_escape_string($koneksi, htmlspecialchars($_POST['karakteristik_camur'] ?? ''));
    $id_program = (int)$_POST['id_program'];

    // Validasi email
    if (!filter_var($email_orgtua_wali, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Format email tidak valid');
    }

    // Validasi nomor telepon (minimal 10 digit)
    if (!preg_match('/^\d{10,}$/', preg_replace('/[^0-9]/', '', $telepon_orgtua_wali))) {
        throw new Exception('Nomor telepon harus minimal 10 digit');
    }

    // Insert ke database dengan prepared statement (untuk security)
    $query_insert = "INSERT INTO tbl_pendaftaran 
        (nama_camur, tgl_lahir_camur, jk_camur, sekolah_camur, alamat_camur, 
        nama_orgtua_wali, telepon_orgtua_wali, email_orgtua_wali, karakteristik_camur, 
        id_program, id_tingkat, status_pendaftaran, created_at) 
        VALUES 
        ('$nama_camur', '$tgl_lahir_camur', '$jk_camur', '$sekolah_camur', '$alamat_camur', 
        '$nama_orgtua_wali', '$telepon_orgtua_wali', '$email_orgtua_wali', '$karakteristik_camur', 
        $id_program, $id_tingkat, 'Pending', NOW())";

    if (!mysqli_query($koneksi, $query_insert)) {
        throw new Exception('Database error: ' . mysqli_error($koneksi));
    }

    $id_pendaftaran_baru = mysqli_insert_id($koneksi);

    // Ambil nama program untuk pesan WA
    $q_prog = mysqli_query($koneksi, "SELECT nama_program FROM tbl_tipe_program WHERE id_program = $id_program");
    
    if (!$q_prog) {
        throw new Exception('Error querying program: ' . mysqli_error($koneksi));
    }

    $d_prog = mysqli_fetch_assoc($q_prog);
    $program_pilihan = $d_prog['nama_program'] ?? 'Unknown Program';

    // Prepare response data
    $response['success'] = true;
    $response['message'] = 'Pendaftaran berhasil disimpan!';
    $response['data'] = [
        'id_pendaftaran' => $id_pendaftaran_baru,
        'nama_camur' => $nama_camur,
        'program_pilihan' => $program_pilihan,
        'no_wa_admin' => '6289659595969',
        'pesan_wa' => urlencode(
            "Halo Admin, saya konfirmasi pendaftaran baru.\n" .
            "ID Pendaftaran: " . $id_pendaftaran_baru . "\n" .
            "Nama: " . $nama_camur . "\n" .
            "Program: " . $program_pilihan . "\n" .
            "Berikut saya lampirkan bukti pembayarannya."
        )
    ];

} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>
