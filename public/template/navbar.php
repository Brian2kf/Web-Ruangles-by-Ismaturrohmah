<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Ruang Les by Ismaturrohmah - Platform Belajar Online Terbaik" />
    <meta name="author" content="Ismaturrohmah" />
    <title>Ruang Les by Ismaturrohmah</title>
    
    <!-- Bootstrap CSS -->
    <link href="assets/sb-admin/css/styles.css" rel="stylesheet" />
    
    <!-- Font Awesome -->
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    
    <!-- Custom Landing Page CSS -->
    <style>
        /* Navbar Landing Page */
        .navbar-landing {
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
        }
        
        .navbar-landing .navbar-brand {
            color: #212529;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .navbar-landing .nav-link {
            color: #212529;
            font-weight: 500;
            padding: 0.5rem 1.5rem;
            transition: color 0.3s ease;
        }
        
        .navbar-landing .nav-link:hover {
            color: #0d6efd;
        }
        
        .navbar-landing .btn-login {
            border: 2px solid #212529;
            color: #212529;
            padding: 0.5rem 1.5rem;
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .navbar-landing .btn-login:hover {
            background-color: #212529;
            color: #ffffff;
        }
        
        /* Responsive */
        @media (max-width: 991.98px) {
            .navbar-landing .nav-link {
                padding: 0.5rem 0;
            }
            
            .navbar-landing .btn-login {
                margin-top: 1rem;
                display: inline-block;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-landing fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= $main_url ?>public/beranda.php">
                Ruang Les by<br>Ismaturrohmah
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $main_url ?>public/beranda.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $main_url ?>public/materi/materi.php">Materi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $main_url ?>public/pendaftaran.php">Pendaftaran</a>
                    </li>
                    <li class="nav-item ms-lg-3">
                        <a class="btn btn-login" href="<?= $main_url ?>auth/login.php">
                            <i class="fas fa-user-circle me-2"></i>Login
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <!-- Add padding to body for fixed navbar -->
    <div style="padding-top: 80px;">