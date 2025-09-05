<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'siswa') {
    header("Location: Login_User.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak Kami - Pengaduan Digital SMKN 6 Bekasi</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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

        header {
            background: linear-gradient(135deg, #003366, #005a9e);
            color: white;
            padding: 15px 0;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
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
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .header-title h3 {
            font-size: 1.1rem;
            color: #cce6ff;
        }

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
            padding-left: 170px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .navbar a:hover {
            background-color: #005a9e;
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
        }

        .main-content {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
            min-height: calc(100vh - 160px);
        }

        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 40px;
        }

        .contact-info {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .contact-info h2 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 1.6rem;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 18px;
        }

        .info-item i {
            color: #005a9e;
            font-size: 1.2rem;
            margin-top: 4px;
        }

        .contact-form {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .contact-form h2 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 1.6rem;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Open Sans', sans-serif;
            font-size: 0.95rem;
        }

        .form-group textarea {
            height: 120px;
            resize: vertical;
        }

        .form-group button {
            background-color: #003366;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            transition: background 0.3s;
        }

        .form-group button:hover {
            background-color: #005a9e;
        }

        .map {
            width: 100%;
            height: 300px;
            border-radius: 12px;
            overflow: hidden;
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Embed Google Maps (contoh: SMKN 6 Bekasi) */
        .map iframe {
            width: 100%;
            height: 100%;
            border: 0;
        }

        .social-media {
            margin-top: 20px;
            display: flex;
            gap: 15px;
        }

        .social-media a {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #005a9e;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .social-media a:hover {
            text-decoration: underline;
        }

        footer {
            text-align: center;
            padding: 20px;
            background-color: #003366;
            color: #cce6ff;
            font-size: 0.9rem;
            margin-top: 50px;
        }

        @media (max-width: 768px) {
            .contact-container {
                flex-direction: column;
            }

            .navbar {
                padding-left: 20px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-title">
            <img src="Image/Logo SMK6.png" alt="Logo SMK 6">
            <div>
                <h1>PENGADUAN DIGITAL</h1>
                <h3>SMK NEGERI 6 KOTA BEKASI</h3>
            </div>
        </div>
    </header>

    <div class="navbar">
        <a href="index_siswa.php"><i class="bi bi-house-fill"></i> Home</a>
        <a href="gallery.php"><i class="bi bi-image"></i> Gallery</a>
        <a href="pengaduan.php"><i class="bi bi-exclamation-diamond"></i> Pengaduan</a>
        <a href="history.php"><i class="bi bi-clock-history"></i> History</a>
        <a href="contact.php"><i class="bi bi-person-circle"></i> Kontak</a>

        <form>
            <input type="text" placeholder="Cari sesuatu..." aria-label="Search">
            <button type="submit">Cari</button>
        </form>

        <div class="welcome">
            <span>Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="Login_User.php">Logout</a>
        </div>
    </div>

    <div class="main-content">
        <div class="contact-container">
            <!-- Informasi Kontak -->
            <div class="contact-info">
                <h2>Hubungi Kami</h2>
                <div class="info-item">
                    <i class="bi bi-geo-alt-fill"></i>
                    <div>
                        <strong>Alamat:</strong><br>
                        Jl. Kusuma Utara X No.169, RT.001/RW.016,Kel. Duren Jaya, Kec. Bekasi Timur., Kota Bekasi, Jawa Barat 17111
                    </div>
                </div>
                <div class="info-item">
                    <i class="bi bi-telephone-fill"></i>
                    <div>
                        <strong>Telepon:</strong><br>
                        +62 821 1285 6776
                    </div>
                </div>
                <div class="info-item">
                    <i class="bi bi-envelope-fill"></i>
                    <div>
                        <strong>Email:</strong><br>
                        info@smkn6bekasi.sch.id
                    </div>
                </div>
                <div class="info-item">
                    <i class="bi bi-clock-fill"></i>
                    <div>
                        <strong>Jam Operasional:</strong><br>
                        Senin - Jumat: 07.00 - 15.00 WIB
                    </div>
                </div>
                <div class="info-item">
                    <i class="bi bi-person-badge-fill"></i>
                    <div>
                        <strong>Penanggung Jawab:</strong><br>
                        R. Prawoto Hari Wibowo, M.Pd. (Kepala Sekolah)
                    </div>
                </div>

                <div class="social-media">
                    <a href="https://www.instagram.com/smkn6kotabekasi_real?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==i" target="_blank"><i class="bi bi-instagram"></i> Instagram</a>
                    <a href="https://youtube.com/@smkn6bekasi?si=C8_cWMjicdrBjt1O" target="_blank"><i class="bi bi-youtube"></i> YouTube</a>
                </div>
            </div>

            <!-- Formulir Kontak -->
            <div class="contact-form">
                <h2>Kirim Pesan</h2>
                <form action="#" method="POST">
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email / No HP</label>
                        <input type="text" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="subject">Subjek</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div class="form-group">
                        <label for="message">Pesan</label>
                        <textarea id="message" name="message" placeholder="Tulis pesan, saran, atau pertanyaan Anda..." required></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit">Kirim Pesan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Google Maps -->
        <div class="map">
            <!-- Ganti dengan link embed Google Maps milik SMKN 6 Bekasi -->
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.252156742468!2d107.03228277499043!3d-6.230451393757693!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e698eeb54ff59e9%3A0x2ac7e6b5da364366!2sSMK%20Negeri%206%20Kota%20Bekasi!5e0!3m2!1sid!2sid!4v1756311120076!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
    </footer>
</body>

</html>