<?php
session_start();

// Koneksi database
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'pengaduandigital';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : null;
$all = isset($_GET['all']);
$dari = $_GET['dari'] ?? '';
$sampai = $_GET['sampai'] ?? '';

$whereClause = "";
$params = [];
$types = "";

if ($id) {
    $whereClause = " AND p.id_pengaduan = ? ";
    $params[] = $id;
    $types .= "i";
}

if ($all) {
    if ($dari && $sampai) {
        $whereClause .= " AND DATE(p.tgl_pengaduan) BETWEEN ? AND ? ";
        $params[] = $dari;
        $params[] = $sampai;
        $types .= "ss";
    } elseif ($dari) {
        $whereClause .= " AND DATE(p.tgl_pengaduan) >= ? ";
        $params[] = $dari;
        $types .= "s";
    } elseif ($sampai) {
        $whereClause .= " AND DATE(p.tgl_pengaduan) <= ? ";
        $params[] = $sampai;
        $types .= "s";
    }
}

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
        p.status,
        t.tanggapan,
        t.tgl_tanggapan,
        o.username AS petugas
    FROM pengaduan p
    JOIN siswa s ON p.nis = s.nis
    LEFT JOIN tanggapan t ON p.id_pengaduan = t.id_pengaduan
    LEFT JOIN petugas o ON t.id_petugas = o.id_petugas
    WHERE 1=1 " . $whereClause . "
    ORDER BY p.tgl_pengaduan DESC";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

$total = $result->num_rows;
$result->data_seek(0); // Reset pointer
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengaduan | SMKN 6 Bekasi</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Open+Sans&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
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

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        /* Header */
        .page-header {
            text-align: center;
            margin-bottom: 30px;
            padding: 20px;
            background: linear-gradient(135deg, #003366, #005a9e);
            color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .page-header img {
            width: 100px;
            vertical-align: middle;
            margin-right: 15px;
        }

        .page-header h1 {
            font-size: 1.8rem;
            margin: 5px 0;
        }

        .page-header p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        /* Filter Box */
        .filter-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }

        .filter-box h3 {
            margin-bottom: 15px;
            color: #003366;
            font-size: 1.2rem;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            align-items: end;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #003366;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #007bff;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-print {
            background: #28a745;
            color: white;
        }

        /* Tabel */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background: #003366;
            color: white;
            text-align: center;
            padding: 12px 10px;
            font-weight: 600;
        }

        td {
            padding: 10px;
            border-top: 1px solid #eee;
            vertical-align: top;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        .photo {
            max-width: 100px;
            max-height: 80px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .status {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .status-belum {
            background: #fff3cd;
            color: #856404;
        }

        .status-selesai {
            background: #d4edda;
            color: #155724;
        }

        /* Tombol Aksi */
        .action-buttons {
            text-align: center;
            margin: 20px 0;
        }

        .action-buttons .btn {
            margin: 0 10px;
        }

        /* Responsif */
        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
            }

            .container {
                padding: 10px;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            table {
                font-size: 13px;
            }

            th,
            td {
                padding: 8px 6px;
            }
        }

        /* === HILANGKAN SAAT CETAK === */
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                font-size: 12pt;
            }

            .page-header {
                background: #003366 !important;
                color: white !important;
            }

            .table-container {
                box-shadow: none !important;
            }

            table {
                border-collapse: collapse;
                font-size: 11pt;
            }

            th {
                background: #003366 !important;
                color: white !important;
                padding: 8px;
            }

            td {
                padding: 6px;
            }

            .photo {
                max-width: 80px;
                max-height: 60px;
            }

            .status {
                font-size: 0.7rem;
            }
        }
    </style>
</head>

<body>

    <div class="container">

        <!-- Header Halaman -->
        <div class="page-header">
            <img src="Image/Logo SMK6.png" alt="Logo SMK 6">
            <h1>DATA LAPORAN PENGADUAN SISWA DIGITAL</h1>
            <p>SMK NEGERI 6 KOTA BEKASI</p>
        </div>

        <!-- Filter Form -->
        <div class="filter-box no-print">
            <h3><i class="bi bi-funnel"></i> Filter Laporan</h3>
            <div class="filter-row">
                <div class="form-group">
                    <label>ID Pengaduan</label>
                    <input type="number" id="filterId" placeholder="Masukkan ID" value="<?= $id ?>">
                </div>
                <div class="form-group">
                    <label>Dari Tanggal</label>
                    <input type="date" id="filterDari" value="<?= $dari ?>">
                </div>
                <div class="form-group">
                    <label>Sampai Tanggal</label>
                    <input type="date" id="filterSampai" value="<?= $sampai ?>">
                </div>
                <div class="form-group">
                    <button class="btn btn-primary" onclick="filterReport()">
                        <i class="bi bi-search"></i> Cari
                    </button>
                    <button class="btn btn-secondary" onclick="resetFilter()">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- Info Jumlah -->
        <p style="margin-bottom: 15px; font-size: 15px;">
            <strong>Total Data:</strong> <?= $total ?> laporan
            <?php if ($id): ?> | <strong>ID:</strong> <?= $id ?> <?php endif; ?>
            <?php if ($dari || $sampai): ?>
                | <strong>Periode:</strong> <?= $dari ? date('d-m-Y', strtotime($dari)) : 'Awal' ?> s/d <?= $sampai ? date('d-m-Y', strtotime($sampai)) : 'Sekarang' ?>
            <?php endif; ?>
        </p>

        <!-- Tabel -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>ID</th>
                        <th>Siswa</th>
                        <th>NIS</th>
                        <th>Kelas</th>
                        <th>Isi Laporan</th>
                        <th>Tgl Lapor</th>
                        <th>Status</th>
                        <th>Tanggapan</th>
                        <th>Petugas</th>
                        <th>Foto</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($total == 0): ?>
                        <tr>
                            <td colspan="11" style="text-align: center; color: #6c757d;">Tidak ada data ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1;
                        while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="center"><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['id_pengaduan']) ?></td>
                                <td><?= htmlspecialchars($row['nama_siswa']) ?></td>
                                <td><?= htmlspecialchars($row['nis']) ?></td>
                                <td><?= htmlspecialchars($row['kelas']) ?></td>
                                <td style="max-width: 200px;"><?= nl2br(htmlspecialchars($row['isi_laporan'])) ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tgl_pengaduan'])) ?></td>
                                <td>
                                    <span class="status status-<?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>
                                <td><?= $row['tanggapan'] ? nl2br(htmlspecialchars($row['tanggapan'])) : '-' ?></td>
                                <td><?= $row['petugas'] ?: '-' ?></td>
                                <td class="center">
                                    <?php if ($row['foto_laporan'] && file_exists('gambaraduan/' . $row['foto_laporan'])): ?>
                                        <a href="gambaraduan/<?= htmlspecialchars($row['foto_laporan']) ?>" target="_blank">
                                            <img src="gambaraduan/<?= htmlspecialchars($row['foto_laporan']) ?>"
                                                alt="Foto" class="photo">
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Aksi -->
        <div class="action-buttons no-print">
            <button class="btn btn-print" onclick="window.print()">
                <i class="bi bi-printer"></i> Cetak Laporan
            </button>
            <a href="<?= ($_SESSION['level'] == 'admin') ? 'index_admin.php' : 'index_operator.php' ?>"
                class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

    </div>

    <!-- Script Filter -->
    <script>
        function filterReport() {
            const id = document.getElementById('filterId').value;
            const dari = document.getElementById('filterDari').value;
            const sampai = document.getElementById('filterSampai').value;

            let url = 'printlaporan.php?all=1';
            if (id) url += '&id=' + encodeURIComponent(id);
            if (dari) url += '&dari=' + encodeURIComponent(dari);
            if (sampai) url += '&sampai=' + encodeURIComponent(sampai);

            window.location.href = url;
        }

        function resetFilter() {
            document.getElementById('filterId').value = '';
            document.getElementById('filterDari').value = '';
            document.getElementById('filterSampai').value = '';
            window.location.href = 'printlaporan.php?all=1';
        }
    </script>

</body>

</html>