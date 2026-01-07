<?php
/**
 * API untuk mengambil tarif via AJAX
 * Digunakan di form tambah invoice
 */

require_once "../../config.php";
require_once "fungsi_invoice.php";

header('Content-Type: application/json');

if (isset($_GET['id_program']) && isset($_GET['id_tingkat'])) {
    $id_program = (int)$_GET['id_program'];
    $id_tingkat = (int)$_GET['id_tingkat'];
    $lokasi = isset($_GET['lokasi']) ? $_GET['lokasi'] : 'Ruangles';
    
    $tarif = getTarif($koneksi, $id_program, $id_tingkat, $lokasi);
    
    echo json_encode([
        'success' => true,
        'harga_8x' => (float)$tarif['harga_8x'],
        'harga_1x' => (float)$tarif['harga_1x']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Parameter tidak lengkap'
    ]);
}
?>