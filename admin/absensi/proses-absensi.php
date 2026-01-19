<?php
session_start();
require_once "../../config.php";
require_once "../pembayaran/fungsi_invoice.php"; // TAMBAH INI: Import fungsi invoice

// 1. Pengecekan Sesi Login
if (!isset($_SESSION["ssLogin"]) || $_SESSION["ssRole"] != '2') {
    header("location: ../../auth/login.php");
    exit();
}

// 2. Logika untuk "SIMPAN MASSAL"
if (isset($_POST['simpan_massal'])) {
    
    // 3. Ambil data utama dari form
    $id_kelas    = (int)$_POST['id_kelas'];
    $tgl_absensi = mysqli_real_escape_string($koneksi, $_POST['tgl_absensi']);
    
    // Pastikan 'status' adalah array dan tidak kosong
    if (isset($_POST['status']) && is_array($_POST['status']) && !empty($_POST['status'])) {
        $status_array = $_POST['status'];

        // 4. Cek Duplikasi
        $queryCek = "SELECT COUNT(*) AS total FROM tbl_absensi 
                     WHERE id_kelas_bimbel = $id_kelas AND tgl_absensi = '$tgl_absensi'";
        $resultCek = mysqli_query($koneksi, $queryCek);
        $dataCek = mysqli_fetch_assoc($resultCek);

        if ($dataCek['total'] > 0) {
            echo "<script>
                    alert('Gagal! Absensi untuk kelas ini pada tanggal " . date('d M Y', strtotime($tgl_absensi)) . " sudah pernah diinput.');
                    document.location.href = 'add-absensi.php?id_kelas=" . $id_kelas . "';
                  </script>";
            exit();
        }

        // 5. Mulai Transaksi Database
        mysqli_begin_transaction($koneksi);
        $berhasil = true;
        
        // TAMBAH: Array untuk tracking sesi yang dikurangi
        $sesi_dikurangi = [];
        $peringatan_sesi = [];

        try {
            // 6. Looping data array status murid
            foreach ($status_array as $id_murid => $status) {
                
                $id_murid_aman = (int)$id_murid;
                $status_aman = mysqli_real_escape_string($koneksi, $status);

                // 7. INSERT absensi
                $query = "INSERT INTO tbl_absensi (id_kelas_bimbel, id_murid, tgl_absensi, status_absensi) 
                          VALUES ($id_kelas, $id_murid_aman, '$tgl_absensi', '$status_aman')";
                
                $result = mysqli_query($koneksi, $query);

                if (!$result) {
                    $berhasil = false;
                    break; 
                }
                
                // ============================================
                // INTEGRASI PEMBAYARAN: AUTO KURANGI SESI
                // ============================================
                
                // Hanya kurangi sesi jika status = "Hadir"
                if ($status_aman == 'Hadir' && $berhasil) {
                    $id_absensi_baru = mysqli_insert_id($koneksi);
                    
                    // Cari invoice aktif murid di kelas ini
                    $invoice_aktif = getInvoiceAktif($koneksi, $id_murid_aman, $id_kelas);
                    
                    if ($invoice_aktif) {
                        // Kurangi sesi tersisa
                        $berhasil_kurangi = kurangiSesiTersisa(
                            $koneksi, 
                            $invoice_aktif['id_pembayaran'], 
                            $id_absensi_baru, 
                            $tgl_absensi
                        );
                        
                        if ($berhasil_kurangi) {
                            $sisa_sesi = $invoice_aktif['sesi_tersisa'] - 1;
                            
                            // Ambil nama murid untuk pesan
                            $query_nama = "SELECT nama_murid FROM tbl_data_murid WHERE id_murid = $id_murid_aman";
                            $result_nama = mysqli_query($koneksi, $query_nama);
                            $data_nama = mysqli_fetch_assoc($result_nama);
                            $nama_murid = $data_nama['nama_murid'];
                            
                            // Tracking sesi dikurangi
                            $sesi_dikurangi[] = "$nama_murid (Sisa: $sisa_sesi sesi)";
                            
                            // Cek reminder: sesi tersisa <= 2
                            if ($sisa_sesi <= 2 && $sisa_sesi > 0) {
                                $peringatan_sesi[] = "⚠️ $nama_murid - Sesi tinggal $sisa_sesi! Segera hubungi orang tua.";
                            } elseif ($sisa_sesi == 0) {
                                $peringatan_sesi[] = "🔴 $nama_murid - Sesi HABIS! Buat invoice baru.";
                            }
                        }
                    } else {
                        // Tidak ada invoice aktif - beri peringatan tapi tetap simpan absensi
                        $query_nama = "SELECT nama_murid FROM tbl_data_murid WHERE id_murid = $id_murid_aman";
                        $result_nama = mysqli_query($koneksi, $query_nama);
                        $data_nama = mysqli_fetch_assoc($result_nama);
                        $nama_murid = $data_nama['nama_murid'];
                        
                        $peringatan_sesi[] = "⚠️ $nama_murid - BELUM ADA INVOICE AKTIF! Silakan buat invoice baru.";
                    }
                }
                // ============================================
                // AKHIR INTEGRASI
                // ============================================
                
            } // Akhir foreach loop

            // 8. Cek status flag
            if ($berhasil) {
                // Commit transaksi
                mysqli_commit($koneksi);
                // Susun pesan alert
                $pesan = "✅ Absensi tanggal " . date('d M Y', strtotime($tgl_absensi)) . " berhasil disimpan!\n";
                if (!empty($sesi_dikurangi)) {
                    $pesan .= "📊 SESI DIKURANGI:\n";
                    foreach ($sesi_dikurangi as $info) {
                        $pesan .= "• $info\n";
                    }
                }
                if (!empty($peringatan_sesi)) {
                    $pesan .= "\n🔔 PERINGATAN:\n";
                    foreach ($peringatan_sesi as $warning) {
                        $pesan .= "$warning\n";
                    }
                }
                echo "<script>
                        alert(" . json_encode($pesan) . ");
                        document.location.href = 'detail-absensi.php?id_kelas=" . $id_kelas . "';
                      </script>";
            } else {
                mysqli_rollback($koneksi);
                throw new Exception("Terjadi kegagalan saat menyimpan salah satu data absensi.");
            }

        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            echo "<script>
                    alert('Gagal total menyimpan absensi. Semua data dibatalkan. Error: " . addslashes($e->getMessage()) . "');
                    document.location.href = 'add-absensi.php?id_kelas=" . $id_kelas . "';
                  </script>";
        }

    } else {
        echo "<script>
                alert('Tidak ada data murid untuk diabsen.');
                document.location.href = 'add-absensi.php?id_kelas=" . $id_kelas . "';
              </script>";
    }

} 
// 3. Logika untuk "UPDATE" (Edit 1 Data)
else if (isset($_POST['update'])) {
    
    $id_absensi = (int)$_POST['id_absensi'];
    $id_kelas   = (int)$_POST['id_kelas'];
    $tgl_absensi = mysqli_real_escape_string($koneksi, $_POST['tgl_absensi']);
    $status_absensi = mysqli_real_escape_string($koneksi, $_POST['status_absensi']);
    
    // TAMBAH: Ambil status lama untuk cek perubahan
    $query_old = "SELECT a.status_absensi, a.id_murid FROM tbl_absensi a WHERE id_absensi = $id_absensi";
    $result_old = mysqli_query($koneksi, $query_old);
    $data_old = mysqli_fetch_assoc($result_old);
    $status_lama = $data_old['status_absensi'];
    $id_murid = $data_old['id_murid'];

    // 5. Update absensi
    $query = "UPDATE tbl_absensi SET 
                tgl_absensi = '$tgl_absensi',
                status_absensi = '$status_absensi'
              WHERE id_absensi = $id_absensi";

    $result = mysqli_query($koneksi, $query);

    if ($result) {
        $pesan_tambahan = "";
        // ============================================
        // INTEGRASI PEMBAYARAN: HANDLE PERUBAHAN STATUS
        // ============================================
        // CASE 1: Status lama bukan "Hadir", sekarang jadi "Hadir" → Kurangi sesi
        if ($status_lama != 'Hadir' && $status_absensi == 'Hadir') {
            $invoice_aktif = getInvoiceAktif($koneksi, $id_murid, $id_kelas);
            if ($invoice_aktif) {
                kurangiSesiTersisa($koneksi, $invoice_aktif['id_pembayaran'], $id_absensi, $tgl_absensi);
                $sisa = $invoice_aktif['sesi_tersisa'] - 1;
                $pesan_tambahan = "\n✅ Sesi dikurangi. Sisa: $sisa sesi";
                if ($sisa <= 2 && $sisa > 0) {
                    $pesan_tambahan .= "\n⚠️ Sesi hampir habis!";
                }
            } else {
                $pesan_tambahan = "\n⚠️ Murid belum punya invoice aktif!";
            }
        }
        // CASE 2: Status lama "Hadir", sekarang jadi bukan "Hadir" → Tambah kembali sesi
        elseif ($status_lama == 'Hadir' && $status_absensi != 'Hadir') {
            // Hapus dari tbl_sesi_terpakai
            $query_hapus_sesi = "DELETE FROM tbl_sesi_terpakai WHERE id_absensi = $id_absensi";
            mysqli_query($koneksi, $query_hapus_sesi);
            // Tambah kembali sesi tersisa
            $query_tambah_sesi = "UPDATE tbl_pembayaran SET 
                                  sesi_terpakai = GREATEST(sesi_terpakai - 1, 0),
                                  sesi_tersisa = sesi_tersisa + 1
                                  WHERE id_murid = $id_murid 
                                  AND id_kelas_bimbel = $id_kelas
                                  AND status_pembayaran = 'Lunas'
                                  ORDER BY id_pembayaran ASC
                                  LIMIT 1";
            mysqli_query($koneksi, $query_tambah_sesi);
            $pesan_tambahan = "\n♻️ Sesi dikembalikan karena status diubah dari Hadir";
        }
        // ============================================
        $pesan_alert = "Data absensi berhasil diperbarui!" . $pesan_tambahan;
        echo "<script>
                alert(" . json_encode($pesan_alert) . ");
                document.location.href = 'detail-absensi.php?id_kelas=" . $id_kelas . "';
              </script>";
    } else {
        $error_msg = mysqli_error($koneksi);
        echo "<script>
                alert('Gagal memperbarui data absensi. Error: " . addslashes($error_msg) . "');
                document.location.href = 'edit-absensi.php?id_absensi=" . $id_absensi . "&id_kelas=" . $id_kelas . "';
              </script>";
    }
}

else {
    header("location: absensi.php");
    exit();
}
?>