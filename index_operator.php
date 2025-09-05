<?php
// Memulai session
session_start();

// Cek login: hanya operator yang boleh masuk
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header("Location: Login_Admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator - Pengaduan Digital SMKN 6 Bekasi</title>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans&display=swap" rel="stylesheet">

    <style>
        /* Reset & Global Styles */
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

        /* Header Styling */
        header {
            background: linear-gradient(135deg, #003366, #005a9e);
            color: white;
            padding: 15px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            text-align: center;
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
            padding-left: 100px;
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

        /* Main Content Wrapper */
        .main-content {
            background: linear-gradient(135deg, #f0f4f8, #d9e6f2, #c0d7eb);
            padding: 20px;
            min-height: 100vh;
        }

        /* Flex container untuk sidebar dan konten */
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
            height: 110vh;
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
        }

        .slide img {
            width: 100%;
            height: 500px;
            object-fit: contain;
            border-radius: 12px;
        }

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
            margin: 40px 20px 0;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .visitors h2 {
            color: #003366;
            margin-bottom: 15px;
            font-size: 1.8rem;
            text-align: center;
        }

        .visitors p {
            color: #555;
            font-size: 1rem;
            line-height: 1.8;
            text-align: justify;
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

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-content {
                margin-left: 20px;
            }

            .slider {
                height: 300px;
            }

            .slide img {
                height: 300px;
            }
        }

        @media (max-width: 768px) {
            .navbar {
                flex-wrap: wrap;
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
        }

        /* Animasi halus saat masuk */
        .slide {
            opacity: 0;
            animation: fadeIn 1s ease-in-out forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
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
        <a href="index_operator.php"><i class="bi bi-house-fill"></i> Home</a>
        <a href="pengaduan_admin.php"><i class="bi bi-exclamation-diamond"></i> Pengaduan</a>
        <a href="tanggapan.php"><i class="bi bi-clock-history"></i> Tanggapan</a>
        <a href="isi_laporan.php"><i class="bi bi-file-earmark-text"></i> Isi Laporan</a>

        <form>
            <input type="text" placeholder="Cari sesuatu..." aria-label="Search">
            <button type="submit">Cari</button>
        </form>

        <div class="welcome">
            <span>Selamat Datang, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (Operator)</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="content-wrapper">

            <!-- Sidebar -->
            <div class="sidebar">
                <a href="pengaduan.php"><i class="bi bi-chevron-right"></i> Pengaduan Sarana</a>
                <a href="pengaduan.php"><i class="bi bi-chevron-right"></i> Pengaduan Prasarana</a>
                <a href="pengaduan.php"><i class="bi bi-chevron-right"></i> Pengaduan KBM</a>

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
                        <div class="slide"><img src="Image/BAPAK KEPSEK.png" alt="Kepsek"></div>
                        <div class="slide"><img src="Image/RPL LOGO.png" alt="RPL"></div>
                        <div class="slide"><img src="Image/LOGO TPTU.png" alt="TPTU"></div>
                        <div class="slide"><img src="Image/lp.png" alt="Lingkungan"></div>
                        <div class="slide"><img src="Image/dpib.png" alt="DPIB"></div>
                    </div>
                    <a class="prev" onclick="moveSlide(-1)">&#10094;</a>
                    <a class="next" onclick="moveSlide(1)">&#10095;</a>
                </div>

                <!-- Visitors Info -->
                <div class="visitors">
                    <h2>LAYANAN PENGADUAN DIGITAL</h2>
                    <p>
                        SMKN 6 Kota Bekasi menyediakan Mekanisme Penyampaian Pengaduan Digital bagi siswa dan warga sekolah
                        untuk menyampaikan keluhan terkait sarana, prasarana, dan kegiatan belajar mengajar.
                        Dengan partisipasi aktif seluruh warga sekolah, kami berkomitmen mewujudkan lingkungan belajar yang lebih baik
                        demi tercapainya visi dan misi sekolah.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2025 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
    </footer>

    <!-- JavaScript untuk slider -->
    <script>
        let slideIndex = 0;
        const slides = document.getElementById('slides');
        const totalSlides = slides.children.length;

        function moveSlide(n) {
            slideIndex = (slideIndex + n + totalSlides) % totalSlides;
            updateSlide();
        }

        function updateSlide() {
            slides.style.transform = `translateX(-${slideIndex * 100}%)`;
        }

        // Auto slide setiap 5 detik
        setInterval(() => {
            moveSlide(1);
        }, 5000);

        // Inisialisasi slider
        updateSlide();
    </script>

</body>

</html>