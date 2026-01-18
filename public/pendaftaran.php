<?php
$title = "Pendaftaran Murid - Ruang Les by Ismaturrohmah";
require_once '../config.php';

// Include Header & Navbar
require_once 'template/header.php';
require_once 'template/navbar.php';
?>

<div id="layoutSidenav_content">
    <main>
        <div class="container-fluid px-4">
            <section class="bg-light py-4">
                <div class="container px-5 py-5 text-center">
                    <h1 class="fw-bolder">Pendaftaran</h1>
                </div>
            </section>

            <div id="formContainer">
                <form action="" method="POST" id="formPendaftaran">
                    
                    <div id="step1" class="d-flex justify-content-center align-items-center">
                        <div class="card col-md-9 mb-4 mt-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-user-edit me-1"></i>
                                A. Informasi Murid dan Orang Tua
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8 mx-auto">
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Nama Lengkap Anak</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="nama_camur" required>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Tanggal Lahir Anak </label>
                                            <div class="col-sm-4 mb-2 mb-sm-0">
                                                <input type="date" class="form-control" name="tgl_lahir_camur" required>
                                            </div>
                                            <div class="col-sm-5 d-flex align-items-center">
                                                <label class="me-3">Jenis Kelamin Anak:</label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jk_camur" value="Laki-laki" required>
                                                    <label class="form-check-label">Laki-laki</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jk_camur" value="Perempuan">
                                                    <label class="form-check-label">Perempuan</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Asal Sekolah</label>
                                            <div class="col-sm-5 mb-2 mb-sm-0">
                                                <input type="text" class="form-control" name="sekolah_camur" required>
                                            </div>
                                            <label class="col-sm-1 col-form-label text-end">Kelas</label>
                                            <div class="col-sm-3">
                                                <select class="form-select" name="id_tingkat" required>
                                                    <option value="">Pilih...</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Alamat</label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" name="alamat_camur" rows="2" required></textarea>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Nama Orang Tua</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="nama_orgtua_wali" required>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">No. Telepon</label>
                                            <div class="col-sm-9">
                                                <input type="number" class="form-control" name="telepon_orgtua_wali" required placeholder="08xxxxxxxx">
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Email</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" name="email_orgtua_wali" required placeholder="email@contoh.com">
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Penjelasan Karakteristik & Kemampuan Anak</label>
                                            <textarea class="form-control" name="karakteristik_camur" rows="3" placeholder="Contoh: Anak aktif, kurang fokus di matematika..."></textarea>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                            <button type="button" class="btn btn-primary px-4" onclick="showStep(2)">
                                                Berikutnya <i class="fas fa-arrow-right ms-2"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="step2" class="d-none d-flex justify-content-center align-items-center">
                        <div class="card col-md-9 mb-4 mt-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-list-alt me-1"></i>
                                B. Pilih Program Pembelajaran
                            </div>
                            <div class="card-body bg-light">
                                <div class="row justify-content-center">
                                    <div class="col-lg-10">
                                        
                                        <input type="hidden" name="id_program" id="selected_program_id" required>

                                        <div id="programContainer" class="row">
                                            <!-- Program akan diisi via JavaScript -->
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mt-4">
                                            <button type="button" class="btn btn-secondary" onclick="showStep(1)">
                                                <i class="fas fa-arrow-left ms-2"></i> Kembali
                                            </button>
                                            <button type="button" class="btn btn-success disabled" id="btnLanjutBayar" onclick="submitForm()">
                                                Lanjut Pembayaran <i class="fas fa-check-circle ms-2"></i>
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>

            <div id="successContainer" class="d-none">
                <div id="step3" class="d-flex justify-content-center align-items-center">
                    <div class="card col-md-9 mb-4 mt-4 shadow-sm border-success">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-receipt me-1"></i>
                            C. Proses Pembayaran
                        </div>
                        <div class="card-body text-center p-5">
                            
                            <div class="alert alert-success mb-4" role="alert">
                                <h4 class="alert-heading">Pendaftaran Berhasil Disimpan!</h4>
                                <p>Data pendaftaran putra/putri Anda telah kami terima.</p>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-8 text-start">
                                    <div class="card bg-light mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title fw-bold">Rekening Pembayaran</h5>
                                            <p class="card-text mb-1">Silakan transfer sesuai biaya program ke:</p>
                                            <ul class="list-unstyled fw-bold text-dark">
                                                <li>Bank ABC</li>
                                                <li>No. Rekening: 000000</li>
                                                <li>A.n: Ruangles by Ismaturrohmah</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <p>
                                        Mohon melakukan pembayaran untuk paket <strong id="programPilihanDisplay"></strong>.
                                        Setelah transfer, silakan konfirmasi melalui WhatsApp dengan mengirimkan bukti transfer.
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <a id="btnWhatsApp" href="" target="_blank" class="btn btn-success btn-lg shadow">
                                    <i class="fab fa-whatsapp me-2"></i> Konfirmasi Pembayaran via WhatsApp
                                </a>
                                <br>
                                <a href="beranda.php" class="btn btn-link mt-3 text-muted">Kembali ke Beranda</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>
    
    <?php require_once 'template/footer.php'; ?>
</div>

<script>
    // Load data tingkat dan program saat halaman dibuka
    document.addEventListener("DOMContentLoaded", function() {
        loadDataPendaftaran();
    });

    function loadDataPendaftaran() {
        fetch("get_data_pendaftaran.php")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Populate tingkat
                    const tingkatSelect = document.querySelector("select[name='id_tingkat']");
                    data.tingkat.forEach(tingkat => {
                        const option = document.createElement("option");
                        option.value = tingkat.id_tingkat;
                        option.textContent = tingkat.jenjang_program + (tingkat.kelas_program ? " - Kls " + tingkat.kelas_program : "");
                        tingkatSelect.appendChild(option);
                    });

                    // Populate program
                    const programContainer = document.getElementById("programContainer");
                    data.program.forEach(program => {
                        const col = document.createElement("div");
                        col.className = "col-md-4 mb-3";
                        col.innerHTML = `
                            <div class="card h-100 text-center cursor-pointer shadow-sm program-card-item" 
                                 id="card_prog_${program.id_program}"
                                 onclick="selectProgram(${program.id_program})"
                                 style="cursor: pointer;">
                                <div class="card-body">
                                    <div class="mb-3">
                                        <i class="fas fa-book-reader fa-3x text-secondary icon-program"></i>
                                    </div>
                                    <h5 class="card-title fw-bold">${program.nama_program} Class</h5>
                                    <p class="card-text small">
                                        ${program.deskripsi_program}
                                    </p>
                                </div>
                                <div class="card-footer bg-transparent border-top-0">
                                    <small class="text-muted status-text">Klik untuk memilih</small>
                                </div>
                            </div>
                        `;
                        programContainer.appendChild(col);
                    });
                }
            })
            .catch(error => console.error("Error loading data:", error));
    }

    function showStep(stepNumber) {
        const step1 = document.getElementById("step1");
        const step2 = document.getElementById("step2");

        // Validasi Sederhana Step 1
        if(stepNumber === 2) {
            const nama = document.getElementsByName("nama_camur")[0].value;
            const hp = document.getElementsByName("telepon_orgtua_wali")[0].value;
            if(nama === "" || hp === "") {
                alert("Mohon lengkapi Nama Lengkap dan No. Telepon terlebih dahulu.");
                return;
            }

            // Sembunyikan Step 1, Tampilkan Step 2
            step1.classList.add("d-none");
            step2.classList.remove("d-none");
        } else {
            // Kembali ke Step 1
            step2.classList.add("d-none");
            step1.classList.remove("d-none");
        }
    }

    function selectProgram(idProgram) {
        // 1. Reset tampilan semua kartu program ke default
        const allCards = document.querySelectorAll(".program-card-item");
        allCards.forEach(card => {
            card.classList.remove("bg-primary", "text-white", "border-primary");
            
            const icon = card.querySelector(".icon-program");
            if(icon) {
                icon.classList.remove("text-white");
                icon.classList.add("text-secondary");
            }

            const status = card.querySelector(".status-text");
            if(status) {
                status.innerText = "Klik untuk memilih";
                status.classList.remove("text-white");
                status.classList.add("text-muted");
            }
        });

        // 2. Set tampilan kartu yang DIPILIH
        const selectedCard = document.getElementById("card_prog_" + idProgram);
        if(selectedCard) {
            selectedCard.classList.add("bg-primary", "text-white", "border-primary");
            
            const icon = selectedCard.querySelector(".icon-program");
            if(icon) {
                icon.classList.remove("text-secondary");
                icon.classList.add("text-white");
            }

            const status = selectedCard.querySelector(".status-text");
            if(status) {
                status.innerText = "Terpilih";
                status.classList.remove("text-muted");
                status.classList.add("text-white");
            }
        }

        // 3. Update Value Input Hidden
        document.getElementById("selected_program_id").value = idProgram;

        // 4. Aktifkan Tombol Submit
        const btnBayar = document.getElementById("btnLanjutBayar");
        btnBayar.classList.remove("disabled");
        btnBayar.disabled = false;
    }

    function submitForm() {
        const form = document.getElementById("formPendaftaran");
        const formData = new FormData(form);

        // Tambah flag untuk identifikasi proses pendaftaran
        formData.append("daftar_sekarang", "1");

        fetch("proses_pendaftaran.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Tampilkan success step
                document.getElementById("formContainer").classList.add("d-none");
                document.getElementById("successContainer").classList.remove("d-none");

                // Update WhatsApp button
                document.getElementById("programPilihanDisplay").textContent = data.data.program_pilihan;
                const waLink = "https://wa.me/" + data.data.no_wa_admin + "?text=" + data.data.pesan_wa;
                document.getElementById("btnWhatsApp").href = waLink;
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Terjadi kesalahan. Silakan coba lagi.");
        });
    }
</script>
