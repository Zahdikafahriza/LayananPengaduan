<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'admin') {
    header("Location: Login_Admin.php");
    exit();
}

$home_link = (isset($_SESSION['level']) && $_SESSION['level'] === 'admin') ? 'index_admin.php' : 'index_operator.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
if ($search) {
    $search = mysqli_real_escape_string($koneksi, $search); // penting: hindari SQL injection
    $where_clause = "AND (nama_petugas LIKE '%$search%' 
                        OR username LIKE '%$search%' 
                        OR nama_petugas LIKE '%$search%'
                        OR id_petugas LIKE '%$search%')";
} else {
    $where_clause = '';
}

$query = "SELECT * FROM petugas WHERE level = 'admin' $where_clause ORDER BY username,nama_petugas,id_petugas";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Admin - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        /* (Styles tetap sama seperti sebelumnya) */
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
            padding: 8px 15px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.95rem;
        }

        .print-btn:hover {
            background: #005a9e;
        }

        .signature-section {
            margin-top: 50px;
            text-align: right;
            font-size: 14px;
        }

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

            .signature-section {
                page-break-inside: avoid;
            }

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
        <a href="<?php echo $home_link; ?>"><i class="bi bi-house-fill"></i> Home</a>
        <a href="profil.php"><i class="bi bi-person-circle"></i> Profil</a>
        <a href="tanggapan.php"><i class="bi bi-clock-history"></i> Tanggapan</a>
        <a href="isi_laporan.php"><i class="bi bi-file-earmark-text"></i> Laporan</a>
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
        <div class="welcome">
            <span>Selamat Datang, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong> (Admin)</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="main-content">
        <div class="container">
            <h2><i class="bi bi-person-vcard"></i> 👤 Data Admin</h2>
            <div style="text-align: right; margin-bottom: 15px;">
                <form method="GET" action="" style="display: inline-block; margin-right: 10px;">
                    <input type="text" name="search" placeholder="Cari data admin..." value="<?php echo htmlspecialchars($search); ?>" style="padding: 8px; border-radius: 6px; border: 1px solid #ccc;">
                    <button type="submit" class="print-btn" style="background: #005a9e;">🔍 Cari</button>
                    <?php if ($search): ?>
                        <a href="data_admin.php" class="print-btn" style="background: #6c757d; text-decoration: none;">🗙 Reset</a>
                    <?php endif; ?>
                </form>
                <button onclick="window.print()" class="print-btn">🖨️ Cetak Data</button>
            </div>
            <div id="print-area">
                <div style="text-align: center; margin-bottom: 15px;">
                    <img src="Image/Logo SMK6.png" alt="Logo SMK 6" width="80">
                    <h2>Data Admin - Pengaduan Digital</h2>
                    <hr>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>ID Admin</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Password</th>
                                <th>Telepon</th>
                                <th>Level</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            if ($result && mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $no++ . "</td>";
                                    echo "<td>" . $row['id_petugas'] . "</td>";
                                    echo "<td>" . $row['nama_petugas'] . "</td>";
                                    echo "<td>" . $row['username'] . "</td>";
                                    echo "<td> ****** </td>";
                                    echo "<td>" . $row['telp'] . "</td>";
                                    echo "<td style='font-weight: bold;'>" . $row['level'] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align:center;'>Tidak ada data admin</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="signature-section" style="margin-top: 60px; text-align: right;">
                    <p>Bekasi, <?= date('d F Y') ?></p>
                    <p><?php echo ucfirst($_SESSION['level']); ?></p>
                    <br><br><br> <!-- Jarak buat tanda tangan -->
                    <p><strong><?= $_SESSION['username'] ?? '________________' ?></strong></p>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>&copy; 2025 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
    </footer>
</body>

</html>