<?php
// Memulai session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: Login_Admin.php");
    exit();
}

// Koneksi ke database
include 'koneksi.php'; // Menggunakan file koneksi eksternal seperti pada file admin

// Ambil id_petugas berdasarkan username yang login
$username = $_SESSION['username'];
$query_petugas = "SELECT id_petugas FROM petugas WHERE username = '" . mysqli_real_escape_string($koneksi, $username) . "'";
$result_petugas = mysqli_query($koneksi, $query_petugas);

if (!$result_petugas || mysqli_num_rows($result_petugas) == 0) {
    die("Error: Data petugas tidak ditemukan. Silahkan login ulang.");
}

$petugas_data = mysqli_fetch_assoc($result_petugas);
$id_petugas = $petugas_data['id_petugas'];

// Proses pengiriman tanggapan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_tanggapan'])) {
    $id_pengaduan = mysqli_real_escape_string($koneksi, $_POST['id_pengaduan']);
    $tanggapan = mysqli_real_escape_string($koneksi, $_POST['tanggapan']);
    $tgl_tanggapan = date("Y-m-d H:i:s");
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);

    // Cek apakah pengaduan sudah pernah ditanggapi
    $check_query = "SELECT id_tanggapan FROM tanggapan WHERE id_pengaduan = '$id_pengaduan'";
    $check_result = mysqli_query($koneksi, $check_query);

    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Pengaduan ini sudah pernah ditanggapi sebelumnya.";
    } else {
        // Insert tanggapan
        $query = "INSERT INTO tanggapan (id_pengaduan, tanggapan, id_petugas, tgl_tanggapan) 
                  VALUES ('$id_pengaduan', '$tanggapan', '$id_petugas', '$tgl_tanggapan')";

        if (mysqli_query($koneksi, $query)) {
            // Update status pengaduan
            $update_query = "UPDATE pengaduan SET status = '$status' WHERE id_pengaduan = '$id_pengaduan'";
            if (mysqli_query($koneksi, $update_query)) {
                $success_message = "Tanggapan berhasil dikirim dan status pengaduan telah diperbarui!";
            } else {
                $success_message = "Tanggapan berhasil dikirim, tetapi gagal memperbarui status: " . mysqli_error($conn);
            }
        } else {
            $error_message = "Gagal mengirim tanggapan: " . mysqli_error($koneksi);
        }
    }
}

// Filter berdasarkan tanggal jika ada
$where_clause = "WHERE p.status IN ('proses', 'Belum Diproses')";
if (isset($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from_date = mysqli_real_escape_string($koneksi, $_GET['from_date']);
    $to_date = mysqli_real_escape_string($koneksi, $_GET['to_date']);
    $where_clause .= " AND p.tgl_pengaduan BETWEEN '$from_date' AND '$to_date'";
}

// Ambil daftar pengaduan dari database yang belum ditanggapi
$query_pengaduan = "
    SELECT p.id_pengaduan, s.nama, p.isi_laporan, p.tgl_pengaduan, p.status, p.foto_laporan
    FROM pengaduan p
    JOIN siswa s ON p.nis = s.nis
    LEFT JOIN tanggapan t ON p.id_pengaduan = t.id_pengaduan
    $where_clause AND t.id_tanggapan IS NULL
    ORDER BY p.tgl_pengaduan DESC
";
$result_pengaduan = mysqli_query($koneksi, $query_pengaduan);

if (!$result_pengaduan) {
    die("Query gagal: " . mysqli_error($koneksi));
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tanggapan Pengaduan</title>

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
            padding: 0 20px;
            height: 60px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            padding-left: 190px;
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

        .welcome a {
            color: #ff6b6b;
            text-decoration: none;
            padding: 5px 10px;
            border-radius: 5px;
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

        /* Tanggapan Container */
        .tanggapan-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .tanggapan-container h2 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 1.8rem;
            font-weight: 700;
        }

        /* Search Form */
        .search-form {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
            flex-wrap: wrap;
            align-items: center;
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        .search-form label {
            font-weight: 600;
            font-size: 1rem;
            color: #003366;
        }

        .search-form input[type="date"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
        }

        .search-form button {
            background-color: #005a9e;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .search-form button:hover {
            background-color: #003366;
        }

        /* Table */
        .table-container {
            overflow-x: auto;
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            border-radius: 8px;
            overflow: hidden;
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
            font-weight: 600;
            color: #003366;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        td img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 5px;
            object-fit: cover;
        }

        .no-photo {
            color: #666;
            font-style: italic;
        }

        /* Form Tanggapan */
        .form-tanggapan {
            background-color: #f9f9f9;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            max-width: 700px;
            margin: 30px auto 0;
        }

        .form-tanggapan h3 {
            color: #003366;
            margin-bottom: 20px;
            font-size: 1.4rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #003366;
        }

        .form-tanggapan select,
        .form-tanggapan textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1rem;
            font-family: 'Open Sans', sans-serif;
        }

        .form-tanggapan textarea {
            min-height: 120px;
            resize: vertical;
        }

        .form-tanggapan button {
            background-color: #005a9e;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            width: 100%;
            transition: background 0.3s;
        }

        .form-tanggapan button:hover {
            background-color: #003366;
        }

        /* Messages */
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
            font-size: 1rem;
            font-weight: 500;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .no-data {
            text-align: center;
            color: #666;
            font-style: italic;
            margin: 30px 0;
            font-size: 1.1rem;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            background-color: #003366;
            color: #cce6ff;
            font-size: 0.9rem;
            margin-top: 40px;
        }

        /* Responsive */
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

            .search-form {
                flex-direction: column;
                gap: 10px;
            }

            .table-container {
                overflow-x: scroll;
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
        <?php if ($_SESSION['level'] === 'admin'): ?>
            <a href="index_admin.php"><i class="bi bi-house-fill"></i> Home</a>
        <?php else: ?>
            <a href="index_operator.php"><i class="bi bi-house-fill"></i> Home</a>
        <?php endif; ?>

        <a href="pengaduan_admin.php"><i class="bi bi-exclamation-diamond"></i> Pengaduan</a>
        <a href="tanggapan.php"><i class="bi bi-clock-history"></i> Tanggapan</a>
        <a href="isi_laporan.php"><i class="bi bi-file-earmark-text"></i> Isi Laporan</a>

        <?php if ($_SESSION['level'] === 'admin'): ?>
            <!-- Dropdown Info Data untuk Admin -->
            <div class="dropdown">
                <a href="javascript:void(0)"><i class="bi bi-info-circle"></i> Info Data ▼</a>
                <div class="dropdown-content">
                    <a href="data_siswa.php"><i class="bi bi-people-fill"></i> Info Data Siswa</a>
                    <a href="data_admin.php"><i class="bi bi-person-vcard"></i> Info Data Admin</a>
                    <a href="data_operator.php"><i class="bi bi-person-badge"></i> Info Data Operator</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="welcome">
            <span>Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> (<?php echo ucfirst($_SESSION['level']); ?>)</span>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <div class="tanggapan-container">
                <h2>Tanggapan Pengaduan</h2>

                <?php if (isset($success_message)): ?>
                    <div class="message success"><?php echo $success_message; ?></div>
                <?php elseif (isset($error_message)): ?>
                    <div class="message error"><?php echo $error_message; ?></div>
                <?php endif; ?>

                <!-- Form Pencarian -->
                <form class="search-form" method="GET" action="">
                    <label>Dari Tanggal:</label>
                    <input type="date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>">
                    <label>Sampai Tanggal:</label>
                    <input type="date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>">
                    <button type="submit">Cari</button>
                    <?php if (isset($_GET['from_date']) || isset($_GET['to_date'])): ?>
                        <a href="tanggapan.php" style="background: #6c757d; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none;">Reset</a>
                    <?php endif; ?>
                </form>

                <h3>Daftar Pengaduan yang Belum Ditanggapi</h3>
                <?php if (mysqli_num_rows($result_pengaduan) > 0): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Pengaduan</th>
                                    <th>Nama Pelapor</th>
                                    <th>Foto</th>
                                    <th>Isi Laporan</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result_pengaduan)): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['id_pengaduan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td>
                                            <?php if (!empty($row['foto_laporan']) && file_exists("gambaraduan/" . $row['foto_laporan'])): ?>
                                                <img src="gambaraduan/<?php echo htmlspecialchars($row['foto_laporan']); ?>" alt="Foto Pengaduan">
                                            <?php else: ?>
                                                <span class="no-photo">Tidak ada foto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['isi_laporan']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($row['tgl_pengaduan'])); ?></td>
                                        <td>
                                            <span style="background: #ffc107; color: #000; padding: 3px 8px; border-radius: 4px; font-size: 0.8rem;">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="no-data">Tidak ada pengaduan yang perlu ditanggapi.</p>
                <?php endif; ?>

                <!-- Form Tanggapan -->
                <?php if (mysqli_num_rows($result_pengaduan) > 0): ?>
                    <div class="form-tanggapan">
                        <h3>Berikan Tanggapan</h3>
                        <form method="POST" action="">
                            <div class="form-group">
                                <label for="id_pengaduan">Pilih Pengaduan:</label>
                                <select name="id_pengaduan" id="id_pengaduan" required>
                                    <option value="">-- Pilih ID Pengaduan --</option>
                                    <?php
                                    mysqli_data_seek($result_pengaduan, 0);
                                    while ($row = mysqli_fetch_assoc($result_pengaduan)): ?>
                                        <option value="<?php echo $row['id_pengaduan']; ?>">
                                            <?php echo $row['id_pengaduan'] . " - " . substr(htmlspecialchars($row['isi_laporan']), 0, 50) . "..."; ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="tanggapan">Tanggapan:</label>
                                <textarea name="tanggapan" id="tanggapan" placeholder="Tulis tanggapan untuk pengaduan ini..." required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="status">Status Pengaduan:</label>
                                <select name="status" id="status" required>
                                    <option value="">-- Pilih Status --</option>
                                    <option value="diproses">Diproses</option>
                                    <option value="selesai">Selesai</option>
                                </select>
                            </div>

                            <button type="submit" name="submit_tanggapan">Kirim Tanggapan</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
    </footer>
</body>

</html>

<?php
// Tutup koneksi database
mysqli_close($koneksi);
?>