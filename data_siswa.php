<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: Login_Admin.php");
    exit();
}

include 'koneksi.php';

$query = "SELECT * FROM siswa ORDER BY kelas, nama";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Open Sans', sans-serif;
        }

        body {
            background: #f5f7fa;
            color: #333;
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
            padding-left: 100px;
        }

        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            transition: all 0.3s;
            font-size: 0.95rem;
        }

        .navbar a:hover {
            background-color: #005a9e;
        }

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

        .welcome {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
            margin-left: auto;
        }

        .main-content {
            padding: 20px;
            min-height: 100vh;
            background: #f0f4f8;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: #003366;
            margin-bottom: 20px;
        }

        .table-container {
            overflow-x: auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            background: white;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #003366;
            color: white;
        }

        th,
        td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        tbody tr:hover {
            background-color: #f0f9ff;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: #003366;
            color: #cce6ff;
            font-size: 0.9rem;
        }

        .print-btn {
            background: #003366;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
            transition: background 0.3s;
        }

        .print-btn:hover {
            background: #005a9e;
        }

        /* Perbaikan cetak */
        @media print {
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            #print-area {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                padding: 10px;
                background: white;
            }

            #print-area table {
                width: 100%;
                border-collapse: collapse;
                font-size: 12pt;
            }

            #print-area th,
            #print-area td {
                border: 1px solid #000;
                padding: 8px;
            }

            #print-area th {
                background-color: #003366 !important;
                color: white !important;
            }

            /* Sembunyikan elemen UI yang tidak perlu */
            .navbar,
            header,
            .main-content>div>div:not(#print-area),
            .welcome,
            .dropdown,
            form,
            button,
            .footer {
                display: none !important;
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
        <a href="pengaduan.php"><i class="bi bi-exclamation-diamond"></i> Pengaduan</a>
        <a href="tanggapan.php"><i class="bi bi-clock-history"></i> Tanggapan</a>
        <a href="isi_laporan.php"><i class="bi bi-file-earmark-text"></i> Isi Laporan</a>

        <div class="dropdown">
            <a href="javascript:void(0)"><i class="bi bi-info-circle"></i> Info Data ▼</a>
            <div class="dropdown-content">
                <a href="data_siswa.php"><i class="bi bi-people-fill"></i> Info Data Siswa</a>
                <a href="data_admin.php"><i class="bi bi-person-vcard"></i> Info Data Admin</a>
                <a href="data_operator.php"><i class="bi bi-person-badge"></i> Info Data Operator</a>
            </div>
        </div>

        <form style="margin-left:auto; margin-right:20px;">
            <input type="text" placeholder="Cari..." style="padding:8px; border-radius:20px; width:200px; border:none; outline:none;">
            <button type="submit" style="background:#005a9e; color:white; border:none; padding:8px 12px; border-radius:20px; cursor:pointer;">Cari</button>
        </form>

        <div class="welcome">
            <span>Selamat Datang, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (Admin)</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <h2><i class="bi bi-people-fill"></i> 📋 Data Siswa</h2>

            <!-- Tombol Cetak -->
            <div style="text-align: right; margin-bottom: 15px;">
                <button onclick="window.print()" class="print-btn">🖨️ Cetak Data Siswa</button>
            </div>

            <!-- Header Cetak (Tidak Terlihat di Layar) -->
            <!-- Area Cetak -->
            <div id="print-area">
                <!-- Header Cetak -->
                <div style="text-align:center; margin-bottom:15px;">
                    <img src="Image/Logo SMK6.png" alt="Logo SMK 6" width="80">
                    <h2>Data Siswa - Pengaduan Digital SMK Negeri 6 Bekasi</h2>
                    <hr>
                </div>

                <!-- Tabel Cetak -->
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>Kelas</th>
                                <th>Telepon</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; ?>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= htmlspecialchars($row['nis']) ?></td>
                                        <td><?= htmlspecialchars($row['kelas']) ?></td>
                                        <td><?= htmlspecialchars($row['telp']) ?></td>
                                        <td><?= htmlspecialchars($row['email'] ?? '-') ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" style="text-align:center; padding:20px; color:#777;">Tidak ada data siswa.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
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