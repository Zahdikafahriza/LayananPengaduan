<?php
// Memulai session
session_start();

// Cek login
if (!isset($_SESSION['username'])) {
    header("Location: Login_Admin.php");
    exit();
}

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'pengaduandigital';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil filter tanggal dari GET
$dari_tanggal = isset($_GET['dari']) ? $_GET['dari'] : '';
$sampai_tanggal = isset($_GET['sampai']) ? $_GET['sampai'] : '';

// Validasi tanggal
$whereClause = "";
if ($dari_tanggal && $sampai_tanggal) {
    $whereClause = " WHERE DATE(p.tgl_pengaduan) BETWEEN ? AND ? ";
} elseif ($dari_tanggal) {
    $whereClause = " WHERE DATE(p.tgl_pengaduan) >= ? ";
} elseif ($sampai_tanggal) {
    $whereClause = " WHERE DATE(p.tgl_pengaduan) <= ? ";
}

// Query untuk ambil data pengaduan + siswa + tanggapan
$query = "
    SELECT 
        s.nama AS nama_siswa,
        s.nis,
        s.kelas,
        s.telp,
        p.id_pengaduan,
        p.isi_laporan,
        p.foto_laporan,
        p.tgl_pengaduan,
        t.tanggapan,
        t.tgl_tanggapan,
        o.username AS petugas
    FROM pengaduan p
    JOIN siswa s ON p.nis = s.nis
    LEFT JOIN tanggapan t ON p.id_pengaduan = t.id_pengaduan
    LEFT JOIN petugas o ON t.id_petugas = o.id_petugas
";

if ($whereClause) {
    $query .= $whereClause;
}
$query .= " ORDER BY p.tgl_pengaduan DESC";

$stmt = $conn->prepare($query);

if ($dari_tanggal && $sampai_tanggal) {
    $stmt->bind_param("ss", $dari_tanggal, $sampai_tanggal);
} elseif ($dari_tanggal) {
    $stmt->bind_param("s", $dari_tanggal);
} elseif ($sampai_tanggal) {
    $stmt->bind_param("s", $sampai_tanggal);
}

$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Isi Laporan - Pengaduan Digital SMKN 6 Bekasi</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
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
            font-size: 1.8rem;
        }

        .search-filter {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
            justify-content: center;
        }

        .search-filter label {
            font-weight: 500;
            color: #333;
        }

        .search-filter input[type="date"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }

        .search-filter button {
            background-color: #003366;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .search-filter button:hover {
            background-color: #005a9e;
        }

        .table-container {
            overflow-x: auto;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            background: linear-gradient(to right, white, #e6f7ff);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.95rem;
        }

        thead {
            background: #003366;
            color: white;
        }

        thead th {
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
        }

        tbody tr {
            border-bottom: 1px solid #e0e7ff;
            transition: background-color 0.2s;
        }

        tbody tr:hover {
            background-color: #f0f9ff;
        }

        tbody td {
            padding: 12px;
            color: #444;
        }

        .foto-thumbnail {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            border: 2px solid #ddd;
            transition: transform 0.2s;
        }

        .foto-thumbnail:hover {
            transform: scale(1.1);
            border-color: #005a9e;
        }

        .print-btn {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 10px;
            border-radius: 5px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: background 0.3s;
        }

        .print-btn:hover {
            background-color: #218838;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .no-response {
            color: #777;
            font-style: italic;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #003366;
            color: #cce6ff;
            font-size: 0.9rem;
            margin-top: 40px;
        }

        /* Modal Foto */
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

        .modal-content {
            margin: 2% auto;
            display: block;
            width: 80%;
            max-width: 800px;
            max-height: 90%;
            object-fit: contain;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .search-filter {
                flex-direction: column;
            }

            .navbar {
                flex-wrap: wrap;
                padding-left: 20px;
            }

            .header-title h1 {
                font-size: 1.5rem;
            }

            thead th,
            td {
                padding: 10px 8px;
                font-size: 0.9rem;
            }

            .foto-thumbnail {
                width: 50px;
                height: 50px;
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
            <h2>📋 Semua Data Laporan & Tanggapan</h2>

            <!-- Filter Tanggal -->
            <div class="search-filter">
                <div>
                    <label for="dari">Dari Tanggal:</label>
                    <input type="date" id="dari" name="dari" value="<?= htmlspecialchars($dari_tanggal) ?>">
                </div>
                <div>
                    <label for="sampai">Sampai Tanggal:</label>
                    <input type="date" id="sampai" name="sampai" value="<?= htmlspecialchars($sampai_tanggal) ?>">
                </div>
                <button onclick="filterByDate()">Filter</button>
                <button onclick="printAll()" style="background-color: #007bff;">🖨️ Cetak Semua</button>
            </div>

            <div class="table-container">
                <table id="laporanTable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas</th>
                            <th>Telepon</th>
                            <th>Foto Laporan</th>
                            <th>Isi Laporan</th>
                            <th>Tanggal Lapor</th>
                            <th>Tanggapan</th>
                            <th>Dari Petugas</th>
                            <th>Tanggal Tanggapan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                                    <td><?= htmlspecialchars($row['nis']) ?></td>
                                    <td><?= htmlspecialchars($row['kelas']) ?></td>
                                    <td><?= htmlspecialchars($row['telp']) ?></td>
                                    <td>
                                        <?php if ($row['foto_laporan']): ?>
                                            <?php
                                            $foto_path = "gambaraduan/" . htmlspecialchars($row['foto_laporan']);
                                            $image_src = file_exists($foto_path) ? $foto_path : "https://via.placeholder.com/150?text=No+Image";
                                            ?>
                                            <img src="<?= $image_src ?>"
                                                alt="Foto Laporan"
                                                class="foto-thumbnail"
                                                onclick="openModal('<?= $image_src ?>', '<?= htmlspecialchars($row['nama_siswa']) ?>')">
                                        <?php else: ?>
                                            <span class="no-response">Tidak ada foto</span>
                                        <?php endif; ?>
                                    </td>
                                    <td title="<?= htmlspecialchars($row['isi_laporan']) ?>">
                                        <?= strlen($row['isi_laporan']) > 80 ? substr(htmlspecialchars($row['isi_laporan']), 0, 80) . '...' : htmlspecialchars($row['isi_laporan']) ?>
                                    </td>
                                    <td><?= date('d-m-Y H:i', strtotime($row['tgl_pengaduan'])) ?></td>
                                    <td>
                                        <?php if ($row['tanggapan']): ?>
                                            <?= htmlspecialchars($row['tanggapan']) ?>
                                        <?php else: ?>
                                            <span class="no-response">Belum ditanggapi</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['petugas']): ?>
                                            <span class="badge"><?= htmlspecialchars($row['petugas']) ?></span>
                                        <?php else: ?>
                                            <span class="no-response">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($row['tgl_tanggapan']): ?>
                                            <?= date('d-m-Y H:i', strtotime($row['tgl_tanggapan'])) ?>
                                        <?php else: ?>
                                            <span class="no-response">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="print-btn" onclick="printReport(<?= $row['id_pengaduan'] ?>)">
                                            🖨️ Cetak
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="12" style="text-align: center; color: #777; padding: 20px;">
                                    Tidak ada data laporan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Foto (Sama seperti di history.php) -->
    <div id="photoModal" class="modal">
        <div class="modal-header">
            <span class="close">&times;</span>
            <h3 id="modalTitle">Foto Pengaduan</h3>
        </div>
        <img class="modal-content" id="modalImg">
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2025 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
    </footer>

    <!-- JavaScript -->
    <script>
        function filterByDate() {
            const dari = document.getElementById('dari').value;
            const sampai = document.getElementById('sampai').value;

            if (!dari && !sampai) {
                alert("Pilih minimal satu tanggal.");
                return;
            }

            let url = 'isi_laporan.php?';
            if (dari) url += 'dari=' + dari + '&';
            if (sampai) url += 'sampai=' + sampai;

            window.location.href = url;
        }

        function printReport(id) {
            window.open('print_laporan.php?id=' + id, '_blank');
        }

        function printAll() {
            const dari = '<?= $dari_tanggal ?>';
            const sampai = '<?= $sampai_tanggal ?>';
            let url = 'print_laporan.php?all=1';
            if (dari) url += '&dari=' + dari;
            if (sampai) url += '&sampai=' + sampai;
            window.open(url, '_blank');
        }

        // Buka modal dengan foto dan nama siswa
        function openModal(fotoSrc, namaSiswa) {
            const modal = document.getElementById("photoModal");
            const img = document.getElementById("modalImg");
            const title = document.getElementById("modalTitle");
            modal.style.display = "block";
            img.src = fotoSrc;
            title.textContent = "Foto Pengaduan - " + namaSiswa;
        }

        // Tutup modal dengan tombol close
        document.querySelector("#photoModal .close").onclick = function() {
            document.getElementById("photoModal").style.display = "none";
        };

        // Tutup modal dengan klik di luar gambar
        window.onclick = function(event) {
            const modal = document.getElementById("photoModal");
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };

        // Tutup modal dengan tombol Escape
        document.addEventListener("keydown", function(e) {
            if (e.key === "Escape") {
                document.getElementById("photoModal").style.display = "none";
            }
        });
    </script>

</body>

</html>