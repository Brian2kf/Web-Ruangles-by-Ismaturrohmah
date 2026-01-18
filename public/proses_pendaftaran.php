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

    // Validasi kehadiran REQUIRED fields (tidak termasuk optional fields)
    $required_fields = [
        'nama_camur', 'tgl_lahir_camur', 'jk_camur', 'id_tingkat', 
        'alamat_camur', 'nama_orgtua_wali', 'telepon_orgtua_wali', 
        'id_program'
    ];

    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            throw new Exception("Field '{$field}' tidak boleh kosong");
        }
    }

    // Sanitasi data REQUIRED
    $nama_camur = mysqli_real_escape_string($koneksi, htmlspecialchars(trim($_POST['nama_camur'])));
    $tgl_lahir_camur = mysqli_real_escape_string($koneksi, trim($_POST['tgl_lahir_camur']));
    $jk_camur = mysqli_real_escape_string($koneksi, $_POST['jk_camur']);
    $id_tingkat = (int)$_POST['id_tingkat'];
    $alamat_camur = mysqli_real_escape_string($koneksi, htmlspecialchars(trim($_POST['alamat_camur'])));
    $nama_orgtua_wali = mysqli_real_escape_string($koneksi, htmlspecialchars(trim($_POST['nama_orgtua_wali'])));
    $telepon_orgtua_wali = mysqli_real_escape_string($koneksi, trim($_POST['telepon_orgtua_wali']));
    $id_program = (int)$_POST['id_program'];

    // Sanitasi data OPTIONAL
    $sekolah_camur = !empty($_POST['sekolah_camur']) ? mysqli_real_escape_string($koneksi, htmlspecialchars(trim($_POST['sekolah_camur']))) : '';
    $email_orgtua_wali = !empty($_POST['email_orgtua_wali']) ? mysqli_real_escape_string($koneksi, strtolower(trim($_POST['email_orgtua_wali']))) : '';
    $karakteristik_camur = !empty($_POST['karakteristik_camur']) ? mysqli_real_escape_string($koneksi, htmlspecialchars(trim($_POST['karakteristik_camur']))) : '';

    // Validasi nama anak (minimal 3 karakter)
    if (strlen($nama_camur) < 3) {
        throw new Exception('Nama lengkap anak minimal 3 karakter');
    }

    // Validasi nama orang tua (minimal 3 karakter)
    if (strlen($nama_orgtua_wali) < 3) {
        throw new Exception('Nama orang tua minimal 3 karakter');
    }

    // Validasi tanggal lahir
    $birthDate = DateTime::createFromFormat('Y-m-d', $tgl_lahir_camur);
    if (!$birthDate) {
        throw new Exception('Format tanggal lahir tidak valid');
    }
    
    $today = new DateTime();
    if ($birthDate > $today) {
        throw new Exception('Tanggal lahir tidak boleh di masa depan');
    }

    // Validasi jenis kelamin
    if ($jk_camur !== 'Laki-laki' && $jk_camur !== 'Perempuan') {
        throw new Exception('Jenis kelamin tidak valid');
    }

    // Validasi nomor telepon (minimal 10 digit)
    $phoneDigitsOnly = preg_replace('/[^0-9]/', '', $telepon_orgtua_wali);
    if (strlen($phoneDigitsOnly) < 10) {
        throw new Exception('No. telepon harus minimal 10 digit');
    }

    // Validasi id_tingkat
    if ($id_tingkat <= 0) {
        throw new Exception('Kelas tidak valid');
    }

    // Validasi id_program
    if ($id_program <= 0) {
        throw new Exception('Program tidak valid');
    }

    // Validasi email JIKA diisi (optional)
    if ($email_orgtua_wali !== '') {
        if (!filter_var($email_orgtua_wali, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Format email tidak valid');
        }

        // Check jika email sudah terdaftar
        $check_email = mysqli_query($koneksi, "SELECT id_pendaftaran FROM tbl_pendaftaran WHERE email_orgtua_wali = '$email_orgtua_wali'");
        if (!$check_email) {
            throw new Exception('Error checking email: ' . mysqli_error($koneksi));
        }
        if (mysqli_num_rows($check_email) > 0) {
            throw new Exception('Email sudah terdaftar. Silakan gunakan email lain atau hubungi admin.');
        }
    }

    // Check jika tingkat exists
    $check_tingkat = mysqli_query($koneksi, "SELECT id_tingkat FROM tbl_tingkat_program WHERE id_tingkat = $id_tingkat");
    if (!$check_tingkat) {
        throw new Exception('Error checking level: ' . mysqli_error($koneksi));
    }
    if (mysqli_num_rows($check_tingkat) === 0) {
        throw new Exception('Kelas yang dipilih tidak valid');
    }

    // Check jika program exists
    $check_program = mysqli_query($koneksi, "SELECT id_program FROM tbl_tipe_program WHERE id_program = $id_program");
    if (!$check_program) {
        throw new Exception('Error checking program: ' . mysqli_error($koneksi));
    }
    if (mysqli_num_rows($check_program) === 0) {
        throw new Exception('Program yang dipilih tidak valid');
    }

    // Insert ke database
    $query_insert = "INSERT INTO tbl_pendaftaran 
        (nama_camur, tgl_lahir_camur, jk_camur, sekolah_camur, alamat_camur, 
        nama_orgtua_wali, telepon_orgtua_wali, email_orgtua_wali, karakteristik_camur, 
        id_program, id_tingkat, status_pendaftaran, created_at) 
        VALUES 
        ('$nama_camur', '$tgl_lahir_camur', '$jk_camur', '$sekolah_camur', '$alamat_camur', 
        '$nama_orgtua_wali', '$telepon_orgtua_wali', '$email_orgtua_wali', '$karakteristik_camur', 
        $id_program, $id_tingkat, 'Pending', NOW())";

    if (!mysqli_query($koneksi, $query_insert)) {
        throw new Exception('Gagal menyimpan data. Silakan coba lagi. Error: ' . mysqli_error($koneksi));
    }

    $id_pendaftaran_baru = mysqli_insert_id($koneksi);

    // Ambil nama program untuk pesan WA
    $q_prog = mysqli_query($koneksi, "SELECT nama_program FROM tbl_tipe_program WHERE id_program = $id_program");
    
    if (!$q_prog) {
        throw new Exception('Gagal mengambil data program');
    }

    $d_prog = mysqli_fetch_assoc($q_prog);
    $program_pilihan = $d_prog['nama_program'] ?? 'Program Unknown';

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
