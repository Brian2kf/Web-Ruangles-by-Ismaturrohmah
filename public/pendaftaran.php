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

            <!-- Alert Error Container -->
            <div id="alertContainer" class="container mt-4">
                <!-- Error messages akan ditampilkan di sini -->
            </div>

            <div id="formContainer">
                <form action="" method="POST" id="formPendaftaran" novalidate>
                    
                    <div id="step1" class="d-flex justify-content-center align-items-center">
                        <div class="card col-md-11 mb-4 mt-4 shadow-sm">
                            <div class="card-header bg-primary text-white">
                                <i class="fas fa-user-edit me-1"></i>
                                A. Informasi Murid dan Orang Tua
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8 mx-auto">
                                        
                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Nama Lengkap Anak <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="nama_camur" name="nama_camur" required>
                                                <small class="text-danger error-text" id="error-nama_camur"></small>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Tanggal Lahir Anak <span class="text-danger">*</span></label>
                                            <div class="col-sm-4 mb-2 mb-sm-0">
                                                <input type="date" class="form-control" id="tgl_lahir_camur" name="tgl_lahir_camur" required>
                                                <small class="text-danger error-text" id="error-tgl_lahir_camur"></small>
                                            </div>
                                            <div class="col-sm-5 d-flex align-items-center">
                                                <label class="me-3">Jenis Kelamin Anak <span class="text-danger">*</span></label>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jk_camur" id="laki_laki" value="Laki-laki" required>
                                                    <label class="form-check-label" for="laki_laki">Laki-laki</label>
                                                </div>
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="jk_camur" id="perempuan" value="Perempuan">
                                                    <label class="form-check-label" for="perempuan">Perempuan</label>
                                                </div>
                                                <small class="text-danger error-text d-block" id="error-jk_camur"></small>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Asal Sekolah</label>
                                            <div class="col-sm-5 mb-2 mb-sm-0">
                                                <input type="text" class="form-control" id="sekolah_camur" name="sekolah_camur">
                                            </div>
                                            <label class="col-sm-1 col-form-label text-end">Kelas <span class="text-danger">*</span></label>
                                            <div class="col-sm-3">
                                                <select class="form-select" id="id_tingkat" name="id_tingkat" required>
                                                    <option value="">Pilih...</option>
                                                </select>
                                                <small class="text-danger error-text" id="error-id_tingkat"></small>
                                            </div>
                                        </div>

                                        <hr>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Nama Orang Tua <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="nama_orgtua_wali" name="nama_orgtua_wali" required>
                                                <small class="text-danger error-text" id="error-nama_orgtua_wali"></small>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">No. Telepon Orang Tua <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" id="telepon_orgtua_wali" name="telepon_orgtua_wali" placeholder="08xxxxxxxx" required>
                                                <small class="text-danger error-text" id="error-telepon_orgtua_wali"></small>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Email Orang Tua</label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" id="email_orgtua_wali" name="email_orgtua_wali" placeholder="email@contoh.com">
                                                <small class="text-danger error-text" id="error-email_orgtua_wali"></small>
                                            </div>
                                        </div>

                                        <div class="mb-3 row">
                                            <label class="col-sm-3 col-form-label">Alamat <span class="text-danger">*</span></label>
                                            <div class="col-sm-9">
                                                <textarea class="form-control" id="alamat_camur" name="alamat_camur" rows="2" required></textarea>
                                                <small class="text-danger error-text" id="error-alamat_camur"></small>
                                            </div>
                                        </div>                                        

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Penjelasan Karakteristik & Kemampuan Anak</label>
                                            <textarea class="form-control" id="karakteristik_camur" name="karakteristik_camur" rows="3" placeholder="Contoh: Anak aktif, kurang fokus di matematika..."></textarea>
                                            <small class="text-danger error-text" id="error-karakteristik_camur"></small>
                                        </div>

                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                            <button type="button" class="btn btn-primary px-4" onclick="validateStep1()">
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
                                        <small class="text-danger error-text" id="error-id_program"></small>

                                        <div id="programContainer" class="row mt-3">
                                            <!-- Program akan diisi via JavaScript -->
                                        </div>
                                        
                                        <div class="d-flex justify-content-between mt-4">
                                            <button type="button" class="btn btn-secondary" onclick="showStep(1)">
                                                <i class="fas fa-arrow-left me-2"></i> Kembali
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
            .catch(error => {
                console.error("Error loading data:", error);
                showError("Gagal memuat data. Silakan refresh halaman.");
            });
    }

    // Clear error messages
    function clearErrors() {
        const errorElements = document.querySelectorAll(".error-text");
        errorElements.forEach(el => {
            el.textContent = "";
            el.style.display = "none";
        });
        document.getElementById("alertContainer").innerHTML = "";
    }

    // Show error for specific field
    function showFieldError(fieldName, message) {
        const errorElement = document.getElementById("error-" + fieldName);
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }
    }

    // Show alert at top
    function showError(message) {
        const alertContainer = document.getElementById("alertContainer");
        alertContainer.innerHTML = `
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error!</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    function showSuccess(message) {
        const alertContainer = document.getElementById("alertContainer");
        alertContainer.innerHTML = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Sukses!</strong> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
    }

    // Validasi Step 1 dengan error handling yang lebih baik
    function validateStep1() {
        clearErrors();
        let isValid = true;

        // Validasi nama anak
        const namaCamur = document.getElementById("nama_camur").value.trim();
        if (namaCamur === "") {
            showFieldError("nama_camur", "Nama lengkap anak harus diisi");
            isValid = false;
        } else if (namaCamur.length < 3) {
            showFieldError("nama_camur", "Nama minimal 3 karakter");
            isValid = false;
        }

        // Validasi tanggal lahir
        const tglLahir = document.getElementById("tgl_lahir_camur").value;
        if (tglLahir === "") {
            showFieldError("tgl_lahir_camur", "Tanggal lahir harus diisi");
            isValid = false;
        } else {
            const birthDate = new Date(tglLahir);
            const today = new Date();
            if (birthDate > today) {
                showFieldError("tgl_lahir_camur", "Tanggal lahir tidak boleh di masa depan");
                isValid = false;
            }
        }

        // Validasi jenis kelamin
        const jkCamur = document.querySelector("input[name='jk_camur']:checked");
        if (!jkCamur) {
            showFieldError("jk_camur", "Jenis kelamin harus dipilih");
            isValid = false;
        }

        // Validasi kelas
        const idTingkat = document.getElementById("id_tingkat").value;
        if (idTingkat === "") {
            showFieldError("id_tingkat", "Kelas harus dipilih");
            isValid = false;
        }

        // Validasi alamat
        const alamatCamur = document.getElementById("alamat_camur").value.trim();
        if (alamatCamur === "") {
            showFieldError("alamat_camur", "Alamat harus diisi");
            isValid = false;
        }

        // Validasi nama orang tua
        const namaOrgtuaWali = document.getElementById("nama_orgtua_wali").value.trim();
        if (namaOrgtuaWali === "") {
            showFieldError("nama_orgtua_wali", "Nama orang tua harus diisi");
            isValid = false;
        } else if (namaOrgtuaWali.length < 3) {
            showFieldError("nama_orgtua_wali", "Nama minimal 3 karakter");
            isValid = false;
        }

        // Validasi telepon
        const teleponOrgtuaWali = document.getElementById("telepon_orgtua_wali").value.trim();
        const phoneDigitsOnly = teleponOrgtuaWali.replace(/[^0-9]/g, '');
        if (teleponOrgtuaWali === "") {
            showFieldError("telepon_orgtua_wali", "No. telepon harus diisi");
            isValid = false;
        } else if (phoneDigitsOnly.length < 10) {
            showFieldError("telepon_orgtua_wali", "No. telepon harus minimal 10 digit");
            isValid = false;
        }

        // Validasi email (optional, tapi jika diisi harus valid)
        const emailOrgtuaWali = document.getElementById("email_orgtua_wali").value.trim();
        if (emailOrgtuaWali !== "") {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailOrgtuaWali)) {
                showFieldError("email_orgtua_wali", "Format email tidak valid");
                isValid = false;
            }
        }

        if (isValid) {
            showStep(2);
        } else {
            showError("Mohon lengkapi semua field yang diperlukan dengan benar.");
            window.scrollTo(0, 0);
        }
    }

    function showStep(stepNumber) {
        const step1 = document.getElementById("step1");
        const step2 = document.getElementById("step2");

        if(stepNumber === 2) {
            step1.classList.add("d-none");
            step2.classList.remove("d-none");
            window.scrollTo(0, 0);
        } else {
            step2.classList.add("d-none");
            step1.classList.remove("d-none");
            window.scrollTo(0, 0);
        }
    }

    function selectProgram(idProgram) {
        clearErrors();
        
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
        clearErrors();
        const programId = document.getElementById("selected_program_id").value;
        
        if (programId === "") {
            showFieldError("id_program", "Silakan pilih program terlebih dahulu");
            return;
        }

        const form = document.getElementById("formPendaftaran");
        const formData = new FormData(form);

        // Tampilkan loading state
        const btnBayar = document.getElementById("btnLanjutBayar");
        const originalText = btnBayar.innerHTML;
        btnBayar.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Memproses...';
        btnBayar.disabled = true;

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

                window.scrollTo(0, 0);
            } else {
                showError(data.message);
                btnBayar.innerHTML = originalText;
                btnBayar.disabled = false;
                window.scrollTo(0, 0);
            }
        })
        .catch(error => {
            console.error("Error:", error);
            showError("Terjadi kesalahan server. Silakan coba lagi.");
            btnBayar.innerHTML = originalText;
            btnBayar.disabled = false;
            window.scrollTo(0, 0);
        });
    }
</script>
