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
    <title>Gallery - Pengaduan Digital SMKN 6 Bekasi</title>
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

        .gallery-title {
            text-align: center;
            margin-bottom: 30px;
            color: #003366;
        }

        .gallery-title h2 {
            font-size: 1.8rem;
            font-weight: 700;
        }

        .gallery-title p {
            color: #555;
            font-size: 1rem;
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .gallery-item {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s;
        }

        .gallery-item:hover {
            transform: translateY(-5px);
        }

        .gallery-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .gallery-caption {
            background: white;
            padding: 12px;
            text-align: center;
        }

        .gallery-caption h3 {
            font-size: 1rem;
            margin-bottom: 5px;
            color: #003366;
        }

        .gallery-caption p {
            font-size: 0.9rem;
            color: #666;
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
            .navbar {
                padding-left: 20px;
                flex-wrap: wrap;
            }

            .navbar form {
                width: 100%;
                margin: 10px auto;
            }

            .header-title h1 {
                font-size: 1.5rem;
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
        <div class="gallery-title">
            <h2>GALERI KEGIATAN & FASILITAS</h2>
            <p>Dokumentasi kegiatan sekolah dan perbaikan berdasarkan pengaduan siswa</p>
        </div>

        <div class="gallery-grid">
            <div class="gallery-item">
                <img src="Image/samsung.jpeg" alt="Halaman Sekolah">
                <div class="gallery-caption">
                    <h3>Ruang Kelas Samsung</h3>
                    <p>Kelas Industri Jurusan Rekayasa Perangakt Lunak (RPL)</p>
                </div>
            </div>

            <div class="gallery-item">
                <img src="Image/toilet.jpg" alt="Toilet Bersih">
                <div class="gallery-caption">
                    <h3>Toilet Siswa</h3>
                    <p>Perbaikan Saluran dan Penggantian Keran</p>
                </div>
            </div>

            <div class="gallery-item">
                <img src="Image/lab.jpeg" alt="Lab Komputer">
                <div class="gallery-caption">
                    <h3>Lab Komputer RPL</h3>
                    <p>Upgrade PC dan Pendingin Ruangan</p>
                </div>
            </div>

            <div class="gallery-item">
                <img src="Image/masjid.jpg" alt="OSIS">
                <div class="gallery-caption">
                    <h3>Masjid SMKN 6</h3>
                    <p>Pemasangan Granit dan Plafon </p>
                </div>
            </div>

            <div class="gallery-item">
                <img src="Image/perpus.jpg" alt="Perpustakaan">
                <div class="gallery-caption">
                    <h3>Perpustakaan Digital</h3>
                    <p>Dilengkapi Dengan Tablet dan Wi-Fi Gratis</p>
                </div>
            </div>

            <div class="gallery-item">
                <img src="Image/bank mini.jpeg" alt="Kantin Sehat">
                <div class="gallery-caption">
                    <h3>Bank Mini SMKN 6</h3>
                    <p>Praktik Perbankan Nyata oleh Siswa Jurusan PKM.</p>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2025 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
    </footer>
</body>

</html>