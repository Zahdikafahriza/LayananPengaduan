<?php
// Memulai session
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: Login_User.php");
    exit();
}

// Koneksi ke database
$host = "localhost";
$username_db = "root";
$password_db = "";
$database = "pengaduandigital";

$conn = mysqli_connect($host, $username_db, $password_db, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Filter berdasarkan tanggal
$where_clause = "";
if (isset($_GET['from_date']) && isset($_GET['to_date']) && !empty($_GET['from_date']) && !empty($_GET['to_date'])) {
    $from_date = mysqli_real_escape_string($conn, $_GET['from_date']);
    $to_date = mysqli_real_escape_string($conn, $_GET['to_date']);
    $where_clause = "WHERE p.tgl_pengaduan BETWEEN '$from_date' AND '$to_date'";
}

// Cek kolom di tabel tanggapan
$check_columns_query = "SHOW COLUMNS FROM tanggapan";
$columns_result = mysqli_query($conn, $check_columns_query);
$available_columns = [];
while ($col = mysqli_fetch_assoc($columns_result)) {
    $available_columns[] = $col['Field'];
}

// Bangun query dengan aman — hanya gunakan kolom yang ada
$id_petugas_field = in_array('id_petugas', $available_columns) ? 't.id_petugas' : 'NULL as id_petugas';

// Query utama: ambil data pengaduan + nama petugas dari tabel petugas
$query = "
    SELECT 
        p.id_pengaduan,
        s.nama,
        p.nis,
        p.isi_laporan,
        p.tgl_pengaduan,
        p.status,
        p.foto_laporan,
        t.tanggapan,
        t.tgl_tanggapan,
        -- Ambil nama petugas jika ada id_petugas, atau kosong
        COALESCE(pt.nama_petugas, 'Admin') AS petugas
    FROM pengaduan p
    JOIN siswa s ON p.nis = s.nis
    LEFT JOIN tanggapan t ON p.id_pengaduan = t.id_pengaduan
    LEFT JOIN petugas pt ON t.id_petugas = pt.id_petugas  -- Asumsi: t.id_petugas merujuk ke pt.id_petugas
    $where_clause
    ORDER BY p.tgl_pengaduan ASC
";

$result = mysqli_query($conn, $query);

if (!$result) {
    die("Query gagal: " . mysqli_error($conn));
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Pengaduan</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        /* Reset & Global */
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
            padding-left: 300px;
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
            font-weight: 600;
            margin-left: auto;
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
            padding: 25px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* Histori Container */
        .histori-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            max-width: 1400px;
            margin: 0 auto;
        }

        .histori-container h2 {
            color: #003366;
            text-align: center;
            margin-bottom: 25px;
            font-size: 1.8rem;
            font-weight: 700;
        }

        /* Form Pencarian */
        .search-form {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .search-form label {
            font-weight: 600;
            color: #495057;
        }

        .search-form input[type="date"] {
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .search-form button,
        .search-form .reset-btn {
            padding: 8px 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            text-align: center;
        }

        .search-form button {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
        }

        .search-form button:hover {
            background: linear-gradient(135deg, #0056b3, #004499);
            transform: translateY(-2px);
        }

        .search-form .reset-btn {
            background: linear-gradient(135deg, #6c757d, #545b62);
            color: white;
            display: inline-block;
        }

        .search-form .reset-btn:hover {
            background: linear-gradient(135deg, #545b62, #495057);
            transform: translateY(-2px);
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            padding: 12px;
            border: 1px solid #e9ecef;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: linear-gradient(135deg, #003366, #005a9e);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }

        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        tbody tr:hover {
            background-color: #e3f2fd;
        }

        /* Status */
        .status-pending {
            background: #fff3cd;
            color: #856404;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }

        .status-proses {
            background: #cce7ff;
            color: #004085;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }

        .status-selesai {
            background: #d4edda;
            color: #155724;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
            display: inline-block;
        }

        /* Foto */
        .photo-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #dee2e6;
        }

        .photo-thumbnail:hover {
            border-color: #007bff;
            transform: scale(1.1);
        }

        .no-photo {
            width: 60px;
            height: 60px;
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-size: 0.75rem;
            text-align: center;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
        }

        .modal-content {
            margin: 2% auto;
            display: block;
            width: 80%;
            max-width: 800px;
            max-height: 90%;
            object-fit: contain;
            border-radius: 10px;
        }

        .modal-header {
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 1.2rem;
        }

        .close {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #bbb;
        }

        /* Responsif */
        @media (max-width: 768px) {
            .content-wrapper {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
            }

            .search-form {
                flex-direction: column;
                gap: 10px;
            }

            .navbar a {
                font-size: 0.9rem;
            }

            .header-title h1 {
                font-size: 1.5rem;
            }

            .header-title h3 {
                font-size: 1rem;
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
        <?php
        $home_link = (isset($_SESSION['level']) && $_SESSION['level'] === 'admin') ? 'index_admin.php' : 'index_operator.php';
        ?>
        <a href="<?php echo $home_link; ?>"><i class="bi bi-house-fill"></i> Home</a>
        <a href="profil.php"><i class="bi bi-person-circle"></i> Profil</a>
        <a href="tanggapan.php"><i class="bi bi-clock-history"></i> Tanggapan</a>
        <a href="isi_laporan.php"><i class="bi bi-file-earmark-text"></i> Laporan</a>
        <div class="dropdown">
            <a href="javascript:void(0)"><i class="bi bi-info-circle"></i> Info Data ▼</a>
            <div class="dropdown-content">
                <a href="data_siswa.php"><i class="bi bi-people-fill"></i> Info Data Siswa</a>
                <a href="data_operator.php"><i class="bi bi-person-badge"></i> Info Data Operator</a>
                <?php if ($_SESSION['level'] === 'admin'): ?>
                    <a href="data_admin.php"><i class="bi bi-person-vcard"></i> Info Data Admin</a>
                <?php endif; ?>
                <a href="info_pengaduan.php"><i class="bi bi-exclamation-diamond"></i> Info Pengaduan</a>
                <a href="info_tanggapan.php"><i class="bi bi-clock-history"></i> Info Tanggapan</a>
            </div>
        </div>
        <div class="welcome">
            <span>Selamat Datang, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> (<?php echo ucfirst($_SESSION['level']); ?>)</span>
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
                <div class="histori-container">
                    <h2>INFO PENGADUAN</h2>

                    <!-- Form Pencarian -->
                    <form class="search-form" method="GET" action="">
                        <label>Dari Tanggal:</label>
                        <input type="date" name="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>">
                        <label>Sampai Tanggal:</label>
                        <input type="date" name="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>">
                        <button type="submit">🔍 Cari</button>
                        <a href="history.php" class="reset-btn">🔄 Reset</a>
                    </form>

                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NIS</th>
                                    <th>Nama</th>
                                    <th>Laporan</th>
                                    <th>Foto</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                    <th>Tanggapan</th>
                                    <th>Petugas</th>
                                    <th>Tgl Respon</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($row['id_pengaduan']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($row['nis']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td style="max-width: 200px;">
                                            <?php
                                            $isi = htmlspecialchars($row['isi_laporan']);
                                            echo strlen($isi) > 100 ? substr($isi, 0, 100) . '...' : $isi;
                                            ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <?php if (!empty($row['foto_laporan']) && file_exists('gambaraduan/' . $row['foto_laporan'])): ?>
                                                <img src="gambaraduan/<?php echo htmlspecialchars($row['foto_laporan']); ?>"
                                                    alt="Foto" class="photo-thumbnail"
                                                    onclick="openModal('<?php echo htmlspecialchars($row['foto_laporan']); ?>', '<?php echo htmlspecialchars($row['nama']); ?>')">
                                            <?php else: ?>
                                                <div class="no-photo">Tidak ada</div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tgl_pengaduan'])); ?></td>
                                        <td>
                                            <?php
                                            $raw_status = $row['status']; // Simpan aslinya untuk debug
                                            $status = strtolower(trim($row['status'])); // Ubah jadi lowercase & hilangkan spasi

                                            $class = '';
                                            $label = '';

                                            if (in_array($status, ['pending', '0', 'menunggu', 'belum diproses'])) {
                                                $class = 'status-pending';
                                                $label = 'Pending';
                                            } elseif (in_array($status, ['proses', '1', 'diproses', 'dalam proses', 'on progress'])) {
                                                $class = 'status-proses';
                                                $label = 'Diproses';
                                            } elseif (in_array($status, ['selesai', '2', 'done', 'completed', 'sudah selesai'])) {
                                                $class = 'status-selesai';
                                                $label = 'Selesai';
                                            } else {
                                                $label = 'Tidak diketahui';
                                            }
                                            ?>
                                            <span class="<?php echo $class; ?>"><?php echo $label; ?></span>
                                        </td>
                                        <td style="max-width: 150px;">
                                            <?php
                                            $tanggapan = !empty($row['tanggapan']) ? htmlspecialchars($row['tanggapan']) : 'Belum ada';
                                            echo strlen($tanggapan) > 80 ? substr($tanggapan, 0, 80) . '...' : $tanggapan;
                                            ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($row['petugas'] ?? '-'); ?></td>
                                        <td><?php echo $row['tgl_tanggapan'] ? date('d/m/Y', strtotime($row['tgl_tanggapan'])) : '-'; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <div class="no-data" style="text-align:center; padding:40px; color:#666;">
                            <h3>📋 Tidak Ada Data</h3>
                            <p>
                                <?php echo isset($_GET['from_date']) ? 'Tidak ada histori dalam rentang tanggal ini.' : 'Belum ada pengaduan yang diajukan.'; ?>
                            </p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Foto -->
    <div id="photoModal" class="modal">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h3 id="modalTitle">Foto Pengaduan</h3>
        </div>
        <img class="modal-content" id="modalImg">
    </div>

    <!-- Footer -->
    <footer style="text-align:center; padding:20px; background:#003366; color:#cce6ff; font-size:0.9rem;">
        &copy; 2025 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI
    </footer>

    <!-- JavaScript -->
    <script>
        function openModal(photo, name) {
            const modal = document.getElementById("photoModal");
            const img = document.getElementById("modalImg");
            const title = document.getElementById("modalTitle");
            modal.style.display = "block";
            img.src = "gambaraduan/" + photo;
            title.textContent = "Foto Pengaduan - " + name;
        }

        document.querySelector(".close").onclick = function() {
            document.getElementById("photoModal").style.display = "none";
        };

        window.onclick = function(event) {
            const modal = document.getElementById("photoModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                document.getElementById("photoModal").style.display = "none";
            }
        });
    </script>

    <?php mysqli_close($conn); ?>
</body>

</html>