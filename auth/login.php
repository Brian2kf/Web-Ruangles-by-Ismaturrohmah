<?php
session_start();
// Cek jika sudah login
if (isset($_SESSION["ssLogin"]) && $_SESSION["ssLogin"] == true) {
    
    // Jika sudah login, arahkan ke dashboard yang sesuai dengan rolenya
    if (isset($_SESSION["ssRole"])) {
        switch ($_SESSION["ssRole"]) {
            case '1': // Orang Tua
                header("location: ../orangTua/dashboard_orangtua.php");
                break;
            case '2': // Admin
                header("location: ../admin/dashboard_admin.php");
                break;
            case '3': // Pengajar
                header("location: ../pengajar/dashboard_pengajar.php");
                break;
            default:
                // Jika role tidak jelas, hancurkan session dan paksa login ulang
                session_destroy();
                header("location: login.php");
                break;
        }
        exit; // Pastikan exit setelah header
    } else {
        // Jika login tapi role tidak ada (seharusnya tidak terjadi), hancurkan session
        session_destroy();
        header("location: login.php");
        exit;
    }
}
require_once "../config.php";
?>  
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Login</title>
        <link href="<?= $main_url ?>assets/sb-admin/css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <link rel="icon" type="image/xicon" href="<?= $main_url ?>assets/image/logo.jpg">
    </head>
    <body class="bg-primary">
        <div id="layoutAuthentication">
            <div id="layoutAuthentication_content">
                <main>
                    <div class="container mt-4">
                        <div class="row justify-content-center">
                            <div class="col-lg-5">
                                <div class="card shadow-lg border-0 rounded-lg mt-5">
                                    <div class="card-header"><h4 class="text-center font-weight-light my-4">Login</h4></div>
                                    <div class="card-body">
                                        <form action="proseslogin.php" method="POST">
                                            <div class="form-floating mb-3">
                                                <input class="form-control border-bottom border-0" id="username" name="username" type="text" pattern="[A-Za-z0-9]{3,}" title="Kombinasi angka dan huruf minimal 3 karakter" placeholder="username" autocomplete="off" required/>
                                                <label for="username">Username</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control border-bottom border-0" id="password" type="password" placeholder="Password" name="password" required/>
                                                <label for="password">Password</label>
                                            </div>
                                            <button type="submit" name="login" class="btn btn-primary col-12 rounded-pill my-2">Login</button>
                                            <a href="<?= $main_url ?>public/beranda.php" class="btn d-flex justify-content-center btn-link mt-3 text-muted">Kembali ke Beranda</a>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="<?= $main_url ?>assets/sb-admin/js/scripts.js"></script>
    </body>
</html>
