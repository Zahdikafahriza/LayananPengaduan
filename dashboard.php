<?php
// Memulai session
session_start();

$isLoggedIn = isset($_SESSION['username']);
$level = $_SESSION['level'] ?? null;
$username = $_SESSION['username'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Pengaduan Digital - SMKN 6 Bekasi</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }

        /* Header */
        header {
            background: linear-gradient(135deg, #003366, #005a9e);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .header-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }

        .header-title img {
            width: 90px;
            border-radius: 8px;
        }

        .header-title h1 {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            margin: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .header-title h3 {
            font-size: 1.1rem;
            font-weight: 500;
            color: #cce6ff;
            margin: 0;
        }

        /* Navbar */
        .navbar {
            background-color: #003366;
            display: flex;
            align-items: center;
            padding: 0 20px;
            height: 60px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            justify-content: center;
            gap: 8px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .navbar a:hover {
            background-color: #005a9e;
            transform: translateY(-2px);
        }

        .navbar form {
            display: flex;
            align-items: center;
            margin-left: auto;
            margin-right: 20px;
        }

        .navbar input[type="text"] {
            padding: 8px 12px;
            border: none;
            border-radius: 20px;
            width: 200px;
            font-size: 0.9rem;
            outline: none;
        }

        .navbar button[type="submit"] {
            background-color: #005a9e;
            color: white;
            border: none;
            padding: 8px 12px;
            margin-left: 8px;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background 0.3s;
        }

        .navbar button[type="submit"]:hover {
            background-color: #003366;
        }

        .welcome {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            font-weight: 600;
            margin-left: 20px;
        }

        .welcome a {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .welcome a:hover {
            background-color: rgba(255, 107, 107, 0.2);
        }

        /* Dropdown Login (navbar) */
        .nav-login {
            margin-left: 10px;
            position: relative;
        }

        .nav-login .btn-login {
            background: #ffffff;
            color: #003366;
            padding: 8px 12px;
            border-radius: 8px;
            border: none;
            font-weight: 700;
            cursor: pointer;
        }

        .nav-login .login-menu {
            display: none;
            position: absolute;
            right: 0;
            top: 44px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            min-width: 180px;
            z-index: 1500;
        }

        .nav-login .login-menu a {
            display: block;
            padding: 10px 14px;
            color: #003366;
            text-decoration: none;
        }

        .nav-login .login-menu a:hover {
            background: #f1f5fb;
        }

        .nav-login:hover .login-menu {
            display: block;
        }

        /* Popup peringatan (untuk pengaduan/history saat belum login) */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal .modal-box {
            background: #fff;
            padding: 22px;
            border-radius: 10px;
            width: 95%;
            max-width: 420px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(2, 6, 23, 0.2);
        }

        .modal h3 {
            margin-bottom: 8px;
            color: #003366;
        }

        .modal p {
            margin-bottom: 18px;
            color: #333;
        }

        .modal .actions {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            display: inline-block;
            padding: 8px 12px;
            border-radius: 8px;
            background: #003366;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .btn.ghost {
            background: transparent;
            border: 2px solid #e6eefc;
            color: #003366;
        }

        .btn.cancel {
            background: #e5e7eb;
            color: #111;
        }

        /* Main Content */
        .main-content {
            background: linear-gradient(135deg, #f0f4f8, #d9e6f2, #c0d7eb);
            padding: 20px;
            min-height: 100vh;
        }

        .content-wrapper {
            display: flex;
            gap: 20px;
            min-height: calc(100vh - 160px);
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #1a1a2e;
            padding-top: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            flex-shrink: 0;
            position: relative;
        }

        .sidebar a {
            display: block;
            color: #e0e0e0;
            padding: 12px 20px;
            text-decoration: none;
            font-size: 1rem;
            transition: all 0.3s;
        }

        .sidebar a:hover {
            background-color: #005a9e;
            color: white;
            padding-left: 25px;
        }

        .sidebar .statistik {
            background-color: #003366;
            color: white;
            padding: 15px;
            margin: 20px 20px 0;
            border-radius: 8px;
            font-size: 0.9rem;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Konten Utama */
        .page-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 30px;
        }

        /* Slider */
        .slider {
            width: 100%;
            max-width: 800px;
            height: 500px;
            margin: 30px auto;
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            position: relative;
            background: white;
        }

        .slides {
            display: flex;
            transition: transform 0.6s ease-in-out;
            height: 100%;
        }

        .slide {
            min-width: 100%;
            flex: 0 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            opacity: 1;
        }

        .slide img {
            width: 100%;
            height: 500px;
            object-fit: contain;
            border-radius: 12px;
        }

        /* Navigation Buttons */
        .prev,
        .next {
            cursor: pointer;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background-color: rgba(0, 0, 0, 0.5);
            color: white;
            border: none;
            padding: 15px;
            font-size: 24px;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s;
            z-index: 10;
        }

        .prev:hover,
        .next:hover {
            background-color: rgba(0, 0, 0, 0.8);
        }

        .prev {
            left: 15px;
        }

        .next {
            right: 15px;
        }

        /* Visitors Section */
        .visitors {
            background: white;
            padding: 30px;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            text-align: center;
        }

        .visitors h2 {
            color: #003366;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }

        .visitors p {
            color: #555;
            font-size: 1rem;
            line-height: 1.8;
            text-align: justify;
        }

        /* Info Sistem (baru) */
        .info-sistem {
            max-width: 900px;
            margin: 0 auto 16px;
            background: #fff;
            padding: 18px;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(2, 6, 23, 0.06);
        }

        .info-sistem h3 {
            color: #003366;
            margin-bottom: 8px;
        }

        /* Footer */
        footer {
            text-align: center;
            padding: 20px;
            background-color: #003366;
            color: #cce6ff;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Dropdown */
        .dropdown {
            position: relative;
        }

        .dropdown>a::after {
            content: " ▼";
            font-size: 12px;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background: #fff;
            min-width: 180px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
            border-radius: 6px;
            overflow: hidden;
            z-index: 999;
        }

        .dropdown-content a {
            color: #003366;
            padding: 10px;
            text-decoration: none;
        }

        .dropdown-content a:hover {
            background: #cce6ff;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .navbar form {
                margin: 10px auto;
                width: 90%;
            }

            .navbar input[type="text"] {
                width: 100%;
            }

            .header-title h1 {
                font-size: 1.5rem;
            }

            .header-title h3 {
                font-size: 1rem;
            }

            .slider {
                height: 300px;
            }

            .slide img {
                height: 300px;
            }
        }
    </style>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="header-title">
            <img src="Image/Logo SMK6.png" alt="Logo SMK 6">
            <div>
                <h1>PENGADUAN DIGITAL</h1>
                <h3>SMK NEGERI 6 KOTA BEKASI</h3>
            </div>
        </div>
    </header>

    <!-- Navbar -->
    <div class="navbar">
        <a href="dashboard.php"><i class="bi bi-house-fill"></i> Home</a>
        <!-- Semua selain Home butuh login -->
        <a href="<?php echo $isLoggedIn ? 'gallery.php' : '#'; ?>" class="need-login" data-logged="<?php echo $isLoggedIn ? '1' : '0'; ?>"><i class="bi bi-image"></i> Gallery</a>
        <a href="<?php echo $isLoggedIn ? 'pengaduan.php' : '#'; ?>" class="need-login" data-logged="<?php echo $isLoggedIn ? '1' : '0'; ?>"><i class="bi bi-exclamation-diamond"></i> Pengaduan</a>
        <a href="<?php echo $isLoggedIn ? 'history.php' : '#'; ?>" class="need-login" data-logged="<?php echo $isLoggedIn ? '1' : '0'; ?>"><i class="bi bi-clock-history"></i> History</a>
        <a href="<?php echo $isLoggedIn ? 'contact.php' : '#'; ?>" class="need-login" data-logged="<?php echo $isLoggedIn ? '1' : '0'; ?>"><i class="bi bi-person-circle"></i> Kontak</a>
        <a href="#info"><i class="bi bi-info-circle"></i> Info Sistem</a>
        <div class="dropdown"> <a href="javascript:void(0)"><i class="bi bi-box-arrow-in-right"></i> Login</a>
            <div class="dropdown-content"> <a href="Login_User.php"><i class="bi bi-people-fill"></i> Login Siswa</a> <br> <a href="Login_Admin.php"><i class="bi bi-person-vcard"></i> Login Petugas</a> </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-wrapper">

            <!-- Sidebar (tetap seperti aslinya) -->
            <div class="sidebar">
                <a href="<?php echo $isLoggedIn ? 'pengaduan.php' : '#'; ?>" class="need-login" data-logged="<?php echo $isLoggedIn ? '1' : '0'; ?>"><i class="bi bi-chevron-right"></i> Pengaduan Sarana</a>
                <a href="<?php echo $isLoggedIn ? 'pengaduan.php' : '#'; ?>" class="need-login" data-logged="<?php echo $isLoggedIn ? '1' : '0'; ?>"><i class="bi bi-chevron-right"></i> Pengaduan Prasarana</a>
                <a href="<?php echo $isLoggedIn ? 'pengaduan.php' : '#'; ?>" class="need-login" data-logged="<?php echo $isLoggedIn ? '1' : '0'; ?>"><i class="bi bi-chevron-right"></i> Pengaduan KBM</a>

                <!-- Statistik Pengunjung -->
                <div class="statistik">
                    <h4>Statistik Pengunjung</h4>
                    <p>
                        <?php
                        $file = "counter.txt";
                        if (!file_exists($file)) {
                            file_put_contents($file, 0);
                        }
                        $count = (int)file_get_contents($file);
                        $count++;
                        file_put_contents($file, $count);
                        echo "Pengunjung: <strong>$count</strong>";
                        ?>
                    </p>
                </div>
            </div>

            <!-- Konten Utama -->
            <div class="page-content">

                <!-- Slider -->
                <div class="slider">
                    <div class="slides" id="slides">
                        <div class="slide"><img src="Image/rpl.png" alt="RPL"></div>
                        <div class="slide"><img src="Image/tptu.png" alt="TPTU"></div>
                        <div class="slide"><img src="Image/lp.png" alt="Lingkungan"></div>
                        <div class="slide"><img src="Image/dpib.png" alt="DPIB"></div>
                        <div class="slide"><img src="Image/BAPAK KEPSEK.png" alt="Kepsek"></div>
                    </div>
                    <a class="prev" onclick="moveSlide(-1)">&#10094;</a>
                    <a class="next" onclick="moveSlide(1)">&#10095;</a>
                </div>

                <!-- Visitors Info -->
                <div class="visitors">
                    <h2>LAYANAN PENGADUAN DIGITAL</h2>
                    <p>
                        SMKN 6 KOTA BEKASI menyediakan Mekanisme Penyampaian Pengaduan Digital apabila terdapat keluhan dari siswa guna peningkatan sarana, prasaranan sekolah dan pelayanan. Dalam rangka mewujudkan visi dan misi sekolah agar berjalan dengan sukses, ditunjut peran besar seluruh partisipasi warga sekolah demi kemajuan SMK Negeri 6 Kota Bekasi.
                    </p>
                </div>

                <!-- Info Sistem (MOD: tambahan content) -->
                <div class="info-sistem" id="info">
                    <h3>Info Sistem</h3>
                    <p>
                        Sistem Pengaduan Digital SMKN 6 Bekasi adalah layanan yang memudahkan siswa untuk menyampaikan
                        keluhan terkait sarana, prasarana, dan proses KBM. Untuk membuat pengaduan atau melihat riwayat lengkap, silakan login sebagai <strong>Siswa</strong> atau
                        <strong>Petugas</strong>. Jika belum punya akun, pilih menu Login → Register di halaman login.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
    </footer>

    <!-- Modal Popup (peringatan login) -->
    <div class="modal" id="loginModal" aria-hidden="true">
        <div class="modal-box">
            <h3>Login Diperlukan</h3>
            <p>Untuk mengakses fitur ini, Anda harus login terlebih dahulu.</p>
            <div class="actions">
                <button class="btn" id="modalOk">OK, login</button>
                <button class="btn cancel" id="modalCancel">Batal</button>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Slider
        let slideIndex = 0;
        const slides = document.getElementById('slides');
        const totalSlides = slides.children.length;

        function moveSlide(n) {
            slideIndex = (slideIndex + n + totalSlides) % totalSlides;
            slides.style.transform = `translateX(-${slideIndex * 100}%)`;
        }

        setInterval(() => moveSlide(1), 5000);
        moveSlide(0);

        // MOD: popup peringatan untuk link yang butuh login
        (function() {
            const needLogin = document.querySelectorAll('.need-login');
            const modal = document.getElementById('loginModal');
            const okBtn = document.getElementById('modalOk');
            const cancelBtn = document.getElementById('modalCancel');
            let redirectTarget = 'Login_User.php'; // default target jika user klik OK

            needLogin.forEach(el => {
                el.addEventListener('click', function(e) {
                    const logged = el.getAttribute('data-logged') === '1';
                    const target = el.getAttribute('data-target') || 'Login_User.php';
                    if (!logged) {
                        e.preventDefault();
                        redirectTarget = target;
                        modal.style.display = 'flex';
                        modal.setAttribute('aria-hidden', 'false');
                    } // else biarkan link berjalan normal
                });
            });

            okBtn.addEventListener('click', function() {
                window.location.href = redirectTarget;
            });
            cancelBtn.addEventListener('click', function() {
                modal.style.display = 'none';
                modal.setAttribute('aria-hidden', 'true');
            });

            // tutup modal saat klik luar
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                    modal.setAttribute('aria-hidden', 'true');
                }
            });
        })();
    </script>
</body>

</html>