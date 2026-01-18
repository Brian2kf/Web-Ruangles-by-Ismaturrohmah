<?php
require_once "../config.php";

header("Content-Type: application/json");

$response = [
    "success" => false,
    "tingkat" => [],
    "program" => []
];

try {
    // Ambil data tingkat
    $query_tingkat = mysqli_query($koneksi, "SELECT id_tingkat, jenjang_program, kelas_program FROM tbl_tingkat_program ORDER BY id_tingkat ASC");
    
    if (!$query_tingkat) {
        throw new Exception("Error querying tingkat: " . mysqli_error($koneksi));
    }
    
    $tingkat_data = [];
    while ($row = mysqli_fetch_assoc($query_tingkat)) {
        $tingkat_data[] = $row;
    }

    // Ambil data program
    $query_program = mysqli_query($koneksi, "SELECT id_program, nama_program, deskripsi_program FROM tbl_tipe_program ORDER BY id_program ASC");
    
    if (!$query_program) {
        throw new Exception("Error querying program: " . mysqli_error($koneksi));
    }
    
    $program_data = [];
    while ($row = mysqli_fetch_assoc($query_program)) {
        $program_data[] = $row;
    }

    $response["success"] = true;
    $response["tingkat"] = $tingkat_data;
    $response["program"] = $program_data;

} catch (Exception $e) {
    $response["success"] = false;
    $response["message"] = $e->getMessage();
}

echo json_encode($response);
?>
