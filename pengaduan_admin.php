<?php
session_start();

/* Koneksi ke database */
$koneksi = mysqli_connect("localhost", "root", "", "pengaduandigital");
if (mysqli_connect_error()) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Cek login dan level
if (
    !isset($_SESSION['username']) ||
    (!isset($_SESSION['level']) || !in_array($_SESSION['level'], ['admin', 'operator']))
) {
    header("Location: login_Admin.php");
    exit();
}

// === Fungsi Generate ID Pengaduan (Aman) ===
function generateIdPengaduanSafe($koneksi)
{
    $sql = "SELECT id_pengaduan FROM pengaduan 
            WHERE id_pengaduan REGEXP '^[0-9]+$' 
            ORDER BY CAST(id_pengaduan AS UNSIGNED) DESC 
            LIMIT 1";
    $result = mysqli_query($koneksi, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastId = (int)$row['id_pengaduan'];
        $newId = str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newId = "0001";
    }
    return $newId;
}

function getNextIdPreview($koneksi)
{
    return generateIdPengaduanSafe($koneksi);
}

function getSiswaData($koneksi)
{
    $sql = "SELECT nis, nama FROM siswa ORDER BY nama ASC";
    $result = mysqli_query($koneksi, $sql);
    if (!$result) {
        die("Error query siswa: " . mysqli_error($koneksi));
    }
    return $result;
}

// === PROSES SIMPAN PENGADUAN ===
$message = '';
$success = false;
$last_id_pengaduan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_pengaduan'])) {
    // Ambil data form
    $id_pengaduan = mysqli_real_escape_string($koneksi, $_POST['id_pengaduan']);
    $tgl_pengaduan = mysqli_real_escape_string($koneksi, $_POST['tgl_pengaduan']);
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $isi_laporan = mysqli_real_escape_string($koneksi, $_POST['isi_laporan']);
    $status = "Belum Diproses";

    // Validasi NIS
    $checkNis = "SELECT COUNT(*) as count FROM siswa WHERE nis = '$nis'";
    $checkNisResult = mysqli_query($koneksi, $checkNis);
    if (!$checkNisResult) {
        $message = "Error query NIS: " . mysqli_error($koneksi);
    } else {
        $checkNisRow = mysqli_fetch_assoc($checkNisResult);
        if ($checkNisRow['count'] == 0) {
            $message = "NIS tidak ditemukan di database!";
        } else {
            // Upload file
            $image_dir = "gambaraduan/";
            if (!is_dir($image_dir)) {
                mkdir($image_dir, 0777, true); // Buat folder jika belum ada
            }

            $file = $_FILES['foto_laporan'];
            $fileName = $file['name'] ?? '';
            $fileTmp = $file['tmp_name'] ?? '';
            $fileSize = $file['size'] ?? 0;
            $fileError = $file['error'] ?? 0;

            if ($fileError !== UPLOAD_ERR_OK) {
                $message = "Error upload file: Kode error $fileError";
            } elseif (empty($fileName)) {
                $message = "Harap pilih file foto!";
            } else {
                $allowed = ['png', 'jpg', 'jpeg', 'gif'];
                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed)) {
                    $message = "Format file tidak didukung! Gunakan: PNG, JPG, JPEG, GIF.";
                } elseif ($fileSize > 5242880) { // 5MB
                    $message = "File terlalu besar! Maksimal 5MB.";
                } else {
                    $rand = rand(1000, 9999);
                    $nama_file_baru = $id_pengaduan . '_' . $rand . '.' . $ext;
                    $uploadPath = $image_dir . $nama_file_baru;

                    if (move_uploaded_file($fileTmp, $uploadPath)) {
                        // Simpan ke database
                        $stmt = mysqli_prepare($koneksi, "INSERT INTO pengaduan 
                            (id_pengaduan, tgl_pengaduan, nis, isi_laporan, foto_laporan, status) 
                            VALUES (?, ?, ?, ?, ?, ?)");

                        if (!$stmt) {
                            $message = "Prepare gagal: " . mysqli_error($koneksi);
                        } else {
                            mysqli_stmt_bind_param(
                                $stmt,
                                "ssssss",
                                $id_pengaduan,
                                $tgl_pengaduan,
                                $nis,
                                $isi_laporan,
                                $nama_file_baru,
                                $status
                            );

                            if (mysqli_stmt_execute($stmt)) {
                                $message = "✅ Pengaduan berhasil disimpan! ID: $id_pengaduan";
                                $success = true;
                                $last_id_pengaduan = $id_pengaduan;
                            } else {
                                $message = "❌ Gagal simpan ke database: " . mysqli_stmt_error($stmt);
                            }
                            mysqli_stmt_close($stmt);
                        }
                    } else {
                        $message = "❌ Gagal upload file ke server. Cek folder 'gambaraduan' dan izin penulisan.";
                        error_log("move_uploaded_file gagal: $fileTmp -> $uploadPath");
                    }
                }
            }
        }
    }
}

// Ambil data siswa untuk dropdown
$siswaResult = getSiswaData($koneksi);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORM PENGADUAN DIGITAL</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: #c0d7eb;
            color: #333;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 700px;
            margin: 40px auto;
            background: #d9e6f2;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .header {
            background: linear-gradient(135deg, #d9e6f2, #c0d7eb);
            color: black;
            padding: 25px;
            text-align: center;
        }

        .header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .header p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        .user-info {
            background: rgba(0, 0, 0, 0.1);
            padding: 10px;
            border-radius: 8px;
            margin-top: 10px;
            font-size: 0.85rem;
        }

        .form-container {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #003366;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .readonly-input {
            background-color: #f8f9fa;
            color: #555;
            cursor: not-allowed;
        }

        .file-hint {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 6px;
            font-style: italic;
        }

        .alert {
            padding: 14px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 0.9rem;
        }

        .alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            flex: 1;
            text-align: center;
        }

        .btn-primary {
            background: #003366;
            color: white;
        }

        .btn-secondary,
        .btn-back {
            background: #6c757d;
            color: white;
        }

        .btn-back {
            background: #28a745;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: #d9e6f2;
            font-size: 0.9rem;
            color: black;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>FORM PENGADUAN DIGITAL</h2>
            <p>SMK NEGERI 6 KOTA BEKASI</p>
            <div class="user-info">
                <strong>Login sebagai:</strong> <?php echo strtoupper($_SESSION['level']); ?> - <?php echo $_SESSION['username']; ?>
            </div>
        </div>

        <div class="form-container">
            <!-- Tampilkan pesan -->
            <?php if (!empty($message)): ?>
                <div class="alert <?php echo $success ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                    <?php if ($success): ?>
                        <div style="margin-top: 10px;">
                            <a href="cetakpengaduan.php?id_pengaduan=<?php echo urlencode($last_id_pengaduan); ?>"
                                class="btn btn-secondary" target="_blank">Cetak</a>
                            <button onclick="location.href='pengaduan_admin.php'" class="btn btn-back">Baru</button>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>ID Pengaduan</label>
                    <input type="text" name="id_pengaduan" value="<?php echo getNextIdPreview($koneksi); ?>" readonly class="readonly-input">
                </div>

                <div class="form-group">
                    <label>Tanggal Pengaduan</label>
                    <input type="date" name="tgl_pengaduan" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Pilih Siswa</label>
                    <select name="nis" required>
                        <option value="">-- Pilih Siswa --</option>
                        <?php while ($s = mysqli_fetch_assoc($siswaResult)): ?>
                            <option value="<?php echo htmlspecialchars($s['nis']); ?>">
                                <?php echo htmlspecialchars($s['nis'] . ' - ' . $s['nama']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Isi Laporan</label>
                    <textarea name="isi_laporan" placeholder="Jelaskan pengaduan..." required></textarea>
                </div>

                <div class="form-group">
                    <label>Foto Pendukung</label>
                    <input type="file" name="foto_laporan" style="background-color: white;" accept="image/*" required>
                    <p class="file-hint">PNG, JPG, JPEG, GIF (max 5MB)</p>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <input type="text" value="Belum Diproses" readonly class="readonly-input">
                    <input type="hidden" name="status" value="Belum Diproses">
                </div>

                <div class="form-buttons">
                    <button type="submit" name="submit_pengaduan" class="btn btn-primary">Simpan Pengaduan</button>
                    <button type="button" class="btn btn-back" onclick="window.location.href='<?php echo ($_SESSION['level'] == 'admin') ? 'index_admin.php' : 'index_operator.php'; ?>'">Kembali</button>
                </div>
            </form>
        </div>

        <div class="footer">
            &copy; 2024 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI
        </div>
    </div>
</body>

</html>

<?php
mysqli_close($koneksi);
?>