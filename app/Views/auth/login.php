<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Login - SI Akuntansi</title>

    <!-- Custom fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        /* Modern Gradient Background */
        body {
            background: linear-gradient(135deg, #1cc88a 0%, #4e73df 100%);
            min-height: 100vh; /* Menggunakan min-height agar responsif */
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px; /* Tambahan padding agar tidak mentok di HP */
        }

        .card-login {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 0.5rem 1rem 0 rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 900px; /* PENYESUAIAN: Batasi lebar agar gambar lebih proporsional */
        }

        .login-image {
            /* --- PENGATURAN GAMBAR (OFFLINE / LOKAL) --- */
            
            /* Cara Pasang Gambar Offline:
               1. Cari gambar animasi (format .png, .jpg, atau .svg).
               2. Simpan gambar tersebut di folder proyek Anda: public/assets/img/
                  (Misal nama filenya: login-animasi.png)
               3. Gunakan kode di bawah ini:
            */
            
            /* Ganti 'login-animasi.png' dengan nama file gambar Anda */
            background-image: url('<?= base_url("assets/img/login.jpg") ?>'); 

            /* Fallback jika gambar lokal belum ada, gunakan warna solid atau pattern */
            background-color: #f8f9fc; 

            background-size: cover; /* Gambar memenuhi area */
            background-position: center; /* Fokus gambar di tengah */
            min-height: 100%; /* Tinggi mengikuti kolom sebelahnya */
        }

        .login-form {
            padding: 3rem !important;
        }

        .btn-login {
            border-radius: 50px;
            font-size: 0.9rem;
            padding: 0.75rem 1rem;
            letter-spacing: 0.05rem;
            font-weight: bold;
            background-color: #4e73df;
            border-color: #4e73df;
            transition: all 0.2s;
        }

        .btn-login:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
        }

        .form-control-user {
            border-radius: 50px;
            padding: 1.5rem 1.5rem;
            height: auto;
            font-size: 0.9rem;
        }

        .brand-icon {
            font-size: 3rem;
            color: #4e73df;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>

    <div class="card card-login">
        <div class="row no-gutters"> <!-- no-gutters agar tidak ada celah putih -->
            
            <!-- Kolom Gambar (Kiri) - Lebar 5 dari 12 grid (sedikit lebih kecil dari separuh) -->
            <div class="col-lg-5 d-none d-lg-block login-image"></div>
            
            <!-- Kolom Form Login (Kanan) - Lebar 7 dari 12 grid -->
            <div class="col-lg-7">
                <div class="p-5 login-form">
                    <div class="text-center">
                        <div class="brand-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <h1 class="h4 text-gray-900 mb-2 font-weight-bold">Selamat Datang Kembali!</h1>
                        <p class="text-muted mb-4">Sistem Informasi Akuntansi</p>
                    </div>

                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger border-left-danger shadow-sm text-sm">
                            <i class="fas fa-exclamation-circle mr-1"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if(session()->getFlashdata('success')): ?>
                        <div class="alert alert-success border-left-success shadow-sm text-sm">
                            <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form class="user" action="<?= base_url('auth/login') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <input type="text" class="form-control form-control-user shadow-sm"
                                name="username" 
                                placeholder="Masukkan Username" required autofocus>
                        </div>
                        <div class="form-group">
                            <input type="password" class="form-control form-control-user shadow-sm"
                                name="password" placeholder="Masukkan Password" required>
                        </div>
                        
                        <button type="submit" class="btn btn-login btn-primary btn-user btn-block shadow">
                            MASUK
                        </button>
                    </form>
                    
                    <hr>
                    
                    <div class="text-center">
                        <a class="small" href="#">Butuh bantuan? Hubungi IT Support.</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap core JavaScript-->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

</body>
</html>