<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Pengaduan | SMKN 6 Bekasi</title>
    <style>
        /* Reset & Font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&family=Open+Sans&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            color: #333;
            line-height: 1.6;
            background: #f5f7fa;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #003366;
        }

        .header h1 {
            font-size: 1.6rem;
            color: #003366;
            font-weight: 700;
            margin: 5px 0;
        }

        .header h2 {
            font-size: 1.3rem;
            color: #005a9e;
            font-weight: 600;
            margin: 5px 0;
        }

        .header img {
            width: 80px;
            vertical-align: middle;
            margin-right: 15px;
        }

        /* Info Siswa */
        .info {
            margin: 20px 0;
            font-size: 16px;
            padding: 15px;
            background: #f8f9fa;
            border-left: 4px solid #003366;
            border-radius: 6px;
        }

        .info strong {
            color: #003366;
            min-width: 120px;
            display: inline-block;
        }

        /* Tabel */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 15px;
        }

        th,
        td {
            padding: 12px 10px;
            text-align: left;
            border: 1px solid #333;
        }

        th {
            background-color: #003366;
            color: white;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
        }

        td {
            vertical-align: top;
        }

        td.center {
            text-align: center;
        }

        /* Gambar */
        .photo {
            max-width: 120px;
            max-height: 100px;
            object-fit: cover;
            border: 1px solid #ddd;
        }

        /* Tanda Tangan */
        .signature {
            margin-top: 60px;
            text-align: right;
            font-size: 16px;
        }

        .signature p {
            margin: 8px 0;
            line-height: 1.5;
        }

        .signature u {
            border-bottom: 1px solid black;
            text-decoration: none;
        }

        /* Tombol (Hanya saat di layar) */
        .no-print {
            text-align: center;
            margin: 30px 0 10px;
        }

        .btn {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
        }

        .btn-print {
            background: #007bff;
            color: white;
        }

        .btn-back {
            background: #6c757d;
            color: white;
        }

        /* Gaya saat cetak */
        @media print {

            body,
            .container {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }

            .no-print {
                display: none;
            }

            @page {
                margin: 1.5cm;
                size: A4 portrait;
            }

            .signature {
                break-inside: avoid;
                page-break-inside: avoid;
            }

            table {
                page-break-inside: avoid;
            }
        }

        /* Responsif */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            .header h1 {
                font-size: 1.4rem;
            }

            .header h2 {
                font-size: 1.2rem;
            }

            table {
                font-size: 14px;
            }

            th,
            td {
                padding: 8px 6px;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <img src="Image/Logo SMK6.jpg" alt="Logo SMK 6">
            <h1>DATA LAPORAN PENGADUAN SISWA DIGITAL</h1>
            <h2>SMK NEGERI 6 KOTA BEKASI</h2>
        </div>

        <?php
        include 'koneksi.php';

        $id_pengaduan = isset($_GET['id_pengaduan']) ? mysqli_real_escape_string($koneksi, $_GET['id_pengaduan']) : '';

        if (!empty($id_pengaduan)) {
            $query = "SELECT * FROM pengaduan WHERE id_pengaduan = '$id_pengaduan'";
            $result = mysqli_query($koneksi, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                $data = mysqli_fetch_assoc($result);
                $nis = $data['nis'];

                $query_siswa = "SELECT nama FROM siswa WHERE nis = '$nis'";
                $result_siswa = mysqli_query($koneksi, $query_siswa);
                $nama_siswa = $nis;

                if ($result_siswa && mysqli_num_rows($result_siswa) > 0) {
                    $siswa = mysqli_fetch_assoc($result_siswa);
                    $nama_siswa = $siswa['nama'];
                }
        ?>

                <!-- Info Siswa -->
                <div class="info">
                    <strong>NIS:</strong> <?php echo htmlspecialchars($data['nis']); ?> &nbsp;&nbsp;&nbsp;
                    <strong>Nama Siswa:</strong> <?php echo htmlspecialchars($nama_siswa); ?>
                </div>

                <!-- Tabel Pengaduan -->
                <table>
                    <thead>
                        <tr>
                            <th>NO</th>
                            <th>ID PENGADUAN</th>
                            <th>TANGGAL</th>
                            <th>ISI LAPORAN</th>
                            <th>FOTO</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="center">1</td>
                            <td><?php echo htmlspecialchars($data['id_pengaduan']); ?></td>
                            <td><?php echo date('d-m-Y', strtotime($data['tgl_pengaduan'])); ?></td>
                            <td style="max-width: 250px;"><?php echo nl2br(htmlspecialchars($data['isi_laporan'])); ?></td>
                            <td class="center">
                                <?php if (!empty($data['foto_laporan']) && file_exists('gambaraduan/' . $data['foto_laporan'])): ?>
                                    <img src="gambaraduan/<?php echo htmlspecialchars($data['foto_laporan']); ?>"
                                        alt="Foto Laporan" class="photo">
                                <?php else: ?>
                                    Tidak ada
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($data['status']); ?></td>
                        </tr>
                    </tbody>
                </table>

                <!-- Tanda Tangan -->
                <div class="signature">
                    <p>Bekasi, <?php echo date('d F Y'); ?></p>
                    <br><br>
                    <p><u><?php echo htmlspecialchars($nama_siswa); ?></u></p>
                    <p>NIS: <?php echo htmlspecialchars($data['nis']); ?></p>
                </div>

        <?php
            } else {
                echo '<p style="color: red; text-align: center; font-size: 18px;">❌ Data pengaduan tidak ditemukan!</p>';
            }
        } else {
            echo '<p style="color: red; text-align: center; font-size: 18px;">❌ ID Pengaduan tidak valid!</p>';
        }
        ?>

        <!-- Tombol (Hanya di layar) -->
        <div class="no-print">
            <button class="btn btn-print" onclick="window.print()">
                🖨️ Cetak Laporan
            </button>
            <a href="index_siswa.php" class="btn btn-back">
                🏠 Kembali
            </a>
        </div>
    </div>

    <!-- Auto Print -->
    <script>
        window.onload = function() {
            setTimeout(function() {
                const confirmPrint = confirm('Apakah Anda ingin mencetak laporan pengaduan ini?');
                if (confirmPrint) {
                    window.print();
                    window.onafterprint = function() {
                        window.location.href = 'index_siswa.php';
                    };
                } else {
                    window.location.href = 'index_siswa.php';
                }
            }, 500);
        };
    </script>

</body>

</html>