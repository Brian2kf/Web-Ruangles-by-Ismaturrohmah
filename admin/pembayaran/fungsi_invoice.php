<?php
/**
 * FUNGSI HELPER UNTUK SISTEM INVOICE
 * File ini berisi fungsi-fungsi pembantu untuk sistem pembayaran
 */

/**
 * Generate nomor invoice otomatis
 * Format: INV/YYYY/MM/XXX
 * XXX = increment terus (001, 002, 003...)
 */
function generateNoInvoice($koneksi) {
    $tahun = date('Y');
    $bulan = date('m');
    
    // Ambil invoice terakhir untuk tahun dan bulan ini
    $query = "SELECT no_invoice FROM tbl_pembayaran 
              WHERE no_invoice LIKE 'INV/$tahun/$bulan/%' 
              ORDER BY id_pembayaran DESC 
              LIMIT 1";
    
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $last_invoice = $data['no_invoice'];
        
        // Extract nomor urut (XXX) dari INV/2025/11/XXX
        $parts = explode('/', $last_invoice);
        $last_number = intval($parts[3]);
        $new_number = $last_number + 1;
    } else {
        // Belum ada invoice di bulan ini, mulai dari 001
        $new_number = 1;
    }
    
    // Format: INV/2025/11/001
    $no_invoice = sprintf("INV/%s/%s/%03d", $tahun, $bulan, $new_number);
    
    return $no_invoice;
}

/**
 * Ambil tarif berdasarkan program, tingkat, dan lokasi
 * Return array: ['harga_8x' => xxx, 'harga_1x' => xxx]
 */
function getTarif($koneksi, $id_program, $id_tingkat, $lokasi = 'Ruangles') {
    // Ambil info tingkat
    $query_tingkat = "SELECT jenjang_program, kelas_program FROM tbl_tingkat_program WHERE id_tingkat = $id_tingkat";
    $result_tingkat = mysqli_query($koneksi, $query_tingkat);
    $tingkat = mysqli_fetch_assoc($result_tingkat);
    
    $jenjang = $tingkat['jenjang_program'];
    $kelas = intval($tingkat['kelas_program']);
    
    // Tentukan kategori jenjang untuk tarif
    $kategori_jenjang = '';
    
    if ($jenjang == 'PAUD') {
        $kategori_jenjang = 'PAUD';
    } elseif ($jenjang == 'TK') {
        $kategori_jenjang = 'TK';
    } elseif ($jenjang == 'SD') {
        if ($kelas >= 1 && $kelas <= 3) {
            $kategori_jenjang = 'SD_1_3';
        } else {
            $kategori_jenjang = 'SD_4_6';
        }
    } elseif ($jenjang == 'SMP') {
        $kategori_jenjang = 'SMP';
    }
    
    // Query tarif
    $query = "SELECT harga_8x, harga_1x FROM tbl_tarif 
              WHERE id_program = $id_program 
              AND jenjang = '$kategori_jenjang' 
              AND lokasi = '$lokasi'";
    
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return ['harga_8x' => 0, 'harga_1x' => 0];
}

/**
 * Format rupiah
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Hitung total tagihan
 */
function hitungTotalTagihan($harga_per_sesi, $jumlah_sesi) {
    return $harga_per_sesi * $jumlah_sesi;
}

/**
 * Cek apakah murid punya invoice aktif dengan sesi tersisa
 */
function cekInvoiceAktif($koneksi, $id_murid) {
    $query = "SELECT COUNT(*) as total FROM tbl_pembayaran 
              WHERE id_murid = $id_murid 
              AND status_pembayaran = 'Lunas' 
              AND sesi_tersisa > 0";
    
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    
    return $data['total'] > 0;
}

/**
 * Ambil invoice aktif murid (yang punya sesi tersisa)
 */
function getInvoiceAktif($koneksi, $id_murid, $id_kelas_bimbel) {
    // Kita hapus filter 'Lunas' agar invoice 'Belum Lunas' pun bisa terdeteksi 
    // dan terpotong sesinya saat absen.
    $query = "SELECT * FROM tbl_pembayaran 
              WHERE id_murid = $id_murid 
              AND id_kelas_bimbel = $id_kelas_bimbel
              AND sesi_tersisa > 0 
              ORDER BY id_pembayaran ASC
              LIMIT 1";
    
    $result = mysqli_query($koneksi, $query);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

function syncSesiInvoice($koneksi, $id_pembayaran) {
    // 1. Ambil data invoice target
    $q_inv = mysqli_query($koneksi, "SELECT * FROM tbl_pembayaran WHERE id_pembayaran = $id_pembayaran");
    $invoice = mysqli_fetch_assoc($q_inv);
    
    if (!$invoice) return false;

    $id_murid = $invoice['id_murid'];
    $id_kelas = $invoice['id_kelas_bimbel'];
    
    // Kita butuh tahu berapa kapasitas sisa di invoice ini
    // Agar tidak mengambil absensi melebihi jatah paket
    $sisa_kuota = $invoice['sesi_tersisa'];
    
    if ($sisa_kuota <= 0) return 0; // Jika invoice sudah habis, stop.

    // 2. Cari Absensi 'Hadir' yang "Yatim" (Belum masuk tbl_sesi_terpakai manapun)
    // Kita urutkan dari tanggal TERLAMA (ASC) agar hutang sesi lama terbayar duluan.
    // LIMIT sesuai sisa_kuota, agar tidak mengambil lebih dari yang dibeli.
    $query_missed = "SELECT a.id_absensi, a.tgl_absensi 
                     FROM tbl_absensi a
                     WHERE a.id_murid = $id_murid 
                     AND a.id_kelas_bimbel = $id_kelas
                     AND a.status_absensi = 'Hadir'
                     AND a.id_absensi NOT IN (
                        SELECT id_absensi FROM tbl_sesi_terpakai WHERE id_absensi IS NOT NULL
                     )
                     ORDER BY a.tgl_absensi ASC
                     LIMIT $sisa_kuota";

    $result_missed = mysqli_query($koneksi, $query_missed);
    $count_fixed = 0;

    while ($absen = mysqli_fetch_assoc($result_missed)) {
        $tgl = $absen['tgl_absensi'];
        $id_absen = $absen['id_absensi'];
        
        // Ambil data terkini invoice (karena di dalam loop, sisa berubah)
        $cek_update = mysqli_query($koneksi, "SELECT sesi_terpakai, jumlah_sesi FROM tbl_pembayaran WHERE id_pembayaran = $id_pembayaran");
        $info_update = mysqli_fetch_assoc($cek_update);
        
        $ket = "Sinkronisasi: Sesi " . ($info_update['sesi_terpakai'] + 1) . " dari " . $info_update['jumlah_sesi'];

        // Insert ke tbl_sesi_terpakai
        $insert = "INSERT INTO tbl_sesi_terpakai (id_pembayaran, id_absensi, tgl_pertemuan, keterangan) 
                   VALUES ($id_pembayaran, $id_absen, '$tgl', '$ket')";
        
        if (mysqli_query($koneksi, $insert)) {
            // Update tbl_pembayaran (Kurangi Sisa, Tambah Terpakai)
            mysqli_query($koneksi, "UPDATE tbl_pembayaran SET 
                                    sesi_terpakai = sesi_terpakai + 1, 
                                    sesi_tersisa = sesi_tersisa - 1 
                                    WHERE id_pembayaran = $id_pembayaran");
            
            $count_fixed++;
        }
    }

    return $count_fixed;
}

/**
 * Kurangi sesi tersisa saat absensi hadir
 */
function kurangiSesiTersisa($koneksi, $id_pembayaran, $id_absensi, $tgl_absensi) {
    // Update tbl_pembayaran
    $query_update = "UPDATE tbl_pembayaran SET 
                     sesi_terpakai = sesi_terpakai + 1,
                     sesi_tersisa = sesi_tersisa - 1
                     WHERE id_pembayaran = $id_pembayaran 
                     AND sesi_tersisa > 0";
    
    $update = mysqli_query($koneksi, $query_update);
    
    if ($update) {
        // Ambil info sesi
        $query_info = "SELECT sesi_terpakai, jumlah_sesi FROM tbl_pembayaran WHERE id_pembayaran = $id_pembayaran";
        $result_info = mysqli_query($koneksi, $query_info);
        $info = mysqli_fetch_assoc($result_info);
        
        $keterangan = "Sesi " . $info['sesi_terpakai'] . " dari " . $info['jumlah_sesi'];
        
        // Insert ke tbl_sesi_terpakai
        $query_insert = "INSERT INTO tbl_sesi_terpakai (id_pembayaran, id_absensi, tgl_pertemuan, keterangan) 
                         VALUES ($id_pembayaran, $id_absensi, '$tgl_absensi', '$keterangan')";
        
        mysqli_query($koneksi, $query_insert);
        
        return true;
    }
    
    return false;
}

/**
 * Cek apakah perlu reminder (sesi tersisa <= 2)
 */
function perluReminder($koneksi, $id_murid) {
    $query = "SELECT COUNT(*) as total FROM tbl_pembayaran 
              WHERE id_murid = $id_murid 
              AND status_pembayaran = 'Lunas' 
              AND sesi_tersisa <= 2 
              AND sesi_tersisa > 0";
    
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    
    return $data['total'] > 0;
}

/**
 * Ambil daftar murid yang perlu reminder
 */
function getMuridPerluReminder($koneksi) {
    $query = "SELECT DISTINCT m.id_murid, m.nama_murid, p.no_invoice, p.sesi_tersisa, p.jenis_paket
              FROM tbl_pembayaran p
              JOIN tbl_data_murid m ON p.id_murid = m.id_murid
              WHERE p.status_pembayaran = 'Lunas'
              AND p.sesi_tersisa <= 2
              AND p.sesi_tersisa > 0
              ORDER BY p.sesi_tersisa ASC, m.nama_murid ASC";
    
    return mysqli_query($koneksi, $query);
}

/**
 * Hitung total invoice berdasarkan status
 */
function hitungInvoiceByStatus($koneksi, $status) {
    $query = "SELECT COUNT(*) as total FROM tbl_pembayaran WHERE status_pembayaran = '$status'";
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['total'];
}

/**
 * Hitung total pendapatan (invoice lunas)
 */
function hitungTotalPendapatan($koneksi, $bulan = null, $tahun = null) {
    $query = "SELECT SUM(total_tagihan) as total FROM tbl_pembayaran WHERE status_pembayaran = 'Lunas'";
    
    if ($bulan && $tahun) {
        $query .= " AND MONTH(tgl_pembayaran) = $bulan AND YEAR(tgl_pembayaran) = $tahun";
    } elseif ($tahun) {
        $query .= " AND YEAR(tgl_pembayaran) = $tahun";
    }
    
    $result = mysqli_query($koneksi, $query);
    $data = mysqli_fetch_assoc($result);
    return $data['total'] ?? 0;
}

/**
 * Format tanggal Indonesia
 */
function formatTanggalIndo($tanggal) {
    if (empty($tanggal)) return '-';
    
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

/**
 * Get badge color berdasarkan status
 */
function getBadgeStatus($status) {
    $badges = [
        'Lunas' => 'success',
        'Belum Lunas' => 'danger',
        'Pending' => 'warning'
    ];
    
    return $badges[$status] ?? 'secondary';
}
?>