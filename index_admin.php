<?php
// Memulai session
session_start();

// Cek apakah user sudah login dan levelnya admin
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: Login_Admin.php");
    exit();
}

// Koneksi ke database
include 'koneksi.php';

// Ambil data statistik
$total_pengaduan = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pengaduan")->fetch_assoc()['total'];
$belum_tanggapan = mysqli_query($koneksi, "
    SELECT COUNT(*) as total FROM pengaduan 
    WHERE id_pengaduan NOT IN (SELECT id_pengaduan FROM tanggapan)
")->fetch_assoc()['total'];
$jumlah_admin = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM petugas WHERE level = 'admin'")->fetch_assoc()['total'];
$jumlah_operator = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM petugas WHERE level = 'operator'")->fetch_assoc()['total'];
$jumlah_siswa = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM siswa")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pengaduan Digital</title>
    <!-- Bootstrap Icons -->
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

        .navbar {
            background-color: #003366;
            display: flex;
            align-items: center;
            justify-content: space-between;
            /* ✅ Distribusi ruang yang lebih baik */
            padding: 0 20px;
            height: 60px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            flex-wrap: nowrap;
            /* ✅ Mencegah pembungkusan */
        }

        /* Container untuk menu navigasi */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
            /* ✅ Jarak yang konsisten antar menu */
            flex: 1;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 1 rem;
            /* ✅ Ukuran font lebih kecil */
            font-weight: 500;
            white-space: nowrap;
            /* ✅ Mencegah teks terpotong */
        }

        .navbar a:hover {
            background-color: #005a9e;
            transform: translateY(-2px);
        }

        /* Dropdown Info Data */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #003366;
            min-width: 200px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            border-radius: 6px;
            z-index: 1001;
            margin-top: 8px;
        }

        .dropdown-content a {
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            font-size: 0.9rem;
        }

        .dropdown-content a:hover {
            background-color: #005a9e;
        }

        .dropdown:hover .dropdown-content {
            display: block;
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
            margin-left: 300px;
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

        /* Main Content */
        .main-content {
            padding: 30px 20px;
            min-height: 100vh;
            background: #f0f4f8;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .dashboard-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .dashboard-header h2 {
            color: #003366;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .dashboard-header p {
            color: #555;
            font-size: 1.1rem;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            width: 200px;
            text-align: center;
            transition: transform 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card i {
            font-size: 2.5rem;
            margin-bottom: 10px;
            color: #003366;
        }

        .stat-card .title {
            font-size: 0.9rem;
            color: #666;
            font-weight: 500;
        }

        .stat-card .value {
            font-size: 1.8rem;
            font-weight: 700;
            color: #003366;
            margin: 8px 0;
        }

        .quick-actions {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            text-align: center;
        }

        .quick-actions h3 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 1.3rem;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-btn {
            background: #003366;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .action-btn:hover {
            background: #005a9e;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #003366;
            color: #cce6ff;
            font-size: 0.9rem;
            margin-top: 40px;
        }


        @media (max-width: 768px) {
            .navbar {
                padding-left: 20px;
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

            .stats {
                flex-direction: column;
                align-items: center;
            }

            .action-buttons {
                flex-direction: column;
            }

            .action-btn {
                width: 100%;
                max-width: 300px;
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
        <a href="index_admin.php"><i class="bi bi-house-fill"></i> Home</a>
        <a href="profil.php"><i class="bi bi-person-circle"></i> Profil</a>
        <a href="register_petugas.php"><i class="bi bi-person-circle"></i> Registrasi</a>
        <!-- <a href="pengaduan_admin.php"><i class="bi bi-exclamation-diamond"></i> Pengaduan</a> -->
        <a href="tanggapan.php"><i class="bi bi-clock-history"></i> Tanggapan</a>
        <a href="isi_laporan.php"><i class="bi bi-file-earmark-text"></i> Laporan</a>

        <!-- Dropdown Info Data -->
        <div class="dropdown">
            <a href="javascript:void(0)"><i class="bi bi-info-circle"></i> Info Data ▼</a>
            <div class="dropdown-content">
                <a href="data_siswa.php"><i class="bi bi-people-fill"></i> Info Data Siswa</a>
                <a href="data_admin.php"><i class="bi bi-person-vcard"></i> Info Data Admin</a>
                <a href="data_operator.php"><i class="bi bi-person-badge"></i> Info Data Operator</a>
                <a href="info_pengaduan.php"><i class="bi bi-exclamation-diamond"></i> Info Pengaduan</a>
                <a href="tanggapan.php"><i class="bi bi-clock-history"></i> Info Tanggapan</a>
            </div>
        </div>

        <!-- Welcome -->
        <div class="welcome">
            <span>Selamat Datang, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (Admin)</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="dashboard-header">
                <h2>👋 Selamat Datang, Admin <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
                <p>Anda memiliki akses penuh ke sistem pengaduan digital.</p>
            </div>

            <!-- Statistik -->
            <div class="stats">
                <div class="stat-card">
                    <i class="bi bi-exclamation-diamond"></i>
                    <div class="title">Total Pengaduan</div>
                    <div class="value"><?= $total_pengaduan ?></div>
                </div>

                <div class="stat-card">
                    <i class="bi bi-clock"></i>
                    <div class="title">Belum Ditanggapi</div>
                    <div class="value"><?= $belum_tanggapan ?></div>
                </div>

                <div class="stat-card">
                    <i class="bi bi-person-vcard"></i>
                    <div class="title">Jumlah Admin</div>
                    <div class="value"><?= $jumlah_admin ?></div>
                </div>

                <div class="stat-card">
                    <i class="bi bi-person-badge"></i>
                    <div class="title">Jumlah Operator</div>
                    <div class="value"><?= $jumlah_operator ?></div>
                </div>

                <div class="stat-card">
                    <i class="bi bi-people-fill"></i>
                    <div class="title">Jumlah Siswa</div>
                    <div class="value"><?= $jumlah_siswa ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
    </footer>

</body>

</html>