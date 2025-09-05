<?php
session_start();

/* Koneksi ke database */
$koneksi = mysqli_connect("localhost", "root", "", "pengaduandigital");
if (mysqli_connect_error()) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi untuk mendapatkan preview ID berikutnya (hanya untuk tampilan)
function getNextIdPreview($koneksi)
{
    $sql = "SELECT CAST(id_pengaduan AS UNSIGNED) as numeric_id FROM pengaduan 
            WHERE id_pengaduan REGEXP '^[0-9]+$' 
            ORDER BY CAST(id_pengaduan AS UNSIGNED) DESC 
            LIMIT 1";

    $result = mysqli_query($koneksi, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastNumericId = $row['numeric_id'];
        $nextId = str_pad($lastNumericId + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $nextId = "0001";
    }

    return $nextId;
}

/* Ambil NIS dari tabel siswa berdasarkan username login */
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $sqlSiswa = "SELECT nis FROM siswa WHERE username = '$username' LIMIT 1";
    $resultSiswa = mysqli_query($koneksi, $sqlSiswa);
    if ($resultSiswa && mysqli_num_rows($resultSiswa) > 0) {
        $rowSiswa = mysqli_fetch_assoc($resultSiswa);
        $_SESSION['nis'] = $rowSiswa['nis'];
    } else {
        $_SESSION['nis'] = "";
    }
}

/* Pesan sukses/gagal */
if (isset($_SESSION['message'])) {
    echo '<div class="alert">';
    echo $_SESSION['message'];
    echo '</div>';
    unset($_SESSION['message']);
}

/* Redirect setelah sukses */
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $id_pengaduan = isset($_SESSION['last_id_pengaduan']) ? $_SESSION['last_id_pengaduan'] : '';

    if (!empty($id_pengaduan)) {
        echo "
        <script>
            if (confirm('Pengaduan berhasil disimpan dengan ID: $id_pengaduan\\n\\nApakah Anda ingin mencetak pengaduan ini?')) {
                window.location.href = 'cetakpengaduan.php?id_pengaduan=' + encodeURIComponent('$id_pengaduan');
            } else {
                window.location.href = 'indexsiswa.php';
            }
        </script>";

        unset($_SESSION['last_id_pengaduan']);
    } else {
        echo "
        <script>
            alert('Pengaduan berhasil disimpan!');
            window.location.href = 'indexsiswa.php';
        </script>";
    }
}

/* Handle error */
if (isset($_GET['error'])) {
    echo "
    <script>
        // Auto refresh jika terjadi error untuk reset form
        setTimeout(function() {
            if (window.location.search.includes('error=1')) {
                window.location.href = 'pengaduan.php';
            }
        }, 3000);
    </script>";
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FORM PENGADUAN DIGITAL</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Reset & Global */
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

        /* Container Utama */
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #d9e6f2, #c0d7eb);
        }

        /* Header Form */
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
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        .header p {
            font-size: 0.9rem;
            opacity: 0.9;
        }

        /* Form Container */
        .form-container {
            padding: 30px;
        }

        /* Form Group */
        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #003366;
            font-size: 0.95rem;
        }

        /* Input & Textarea */
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group textarea,
        .form-group input[type="file"] {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            font-family: 'Open Sans', sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #005a9e;
            box-shadow: 0 0 0 4px rgba(0, 90, 158, 0.1);
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
            line-height: 1.6;
        }

        /* Input Readonly */
        .readonly-input {
            background-color: #f8f9fa !important;
            color: #555;
            cursor: not-allowed;
            font-weight: 500;
        }

        /* File Input Wrapper */
        .file-input-wrapper {
            position: relative;
        }

        .file-hint {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 6px;
            font-style: italic;
        }

        /* Tombol Aksi */
        .form-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #003366, #005a9e);
            color: white;
            flex: 1;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(0, 51, 102, 0.2);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
            padding: 12px 20px;
            font-size: 15px;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-1px);
        }

        /* Alert Messages */
        .alert {
            padding: 14px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            font-size: 0.9rem;
        }

        .alert.error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }

        /* Info ID Pengaduan */
        .id-info {
            font-size: 0.85rem;
            color: #005a9e;
            margin-top: 5px;
            padding: 6px 10px;
            background: #e7f3ff;
            border: 1px solid #b3d9ff;
            border-radius: 5px;
            display: inline-block;
        }

        /* Footer */
        .footer {
            background: linear-gradient(135deg, #d9e6f2, #c0d7eb);
            color: black;
            text-align: center;
            padding: 20px;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Loading Animation */
        .loading {
            display: none;
            width: 18px;
            height: 18px;
            border: 2px solid #ffffff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            .container {
                margin: 15px auto;
                border-radius: 10px;
            }

            .form-container {
                padding: 20px;
            }

            .header h2 {
                font-size: 1.4rem;
            }

            .form-buttons {
                flex-direction: column;
            }

            .btn {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>FORM PENGADUAN DIGITAL SISWA</h2>
            <p>SMK NEGERI 6 KOTA BEKASI</p>
        </div>

        <div class="form-container">
            <!-- Tampilkan warning jika NIS kosong -->
            <?php if (isset($_SESSION['nis']) && empty($_SESSION['nis'])): ?>
                <div class="alert error">
                    <strong>Peringatan:</strong> NIS tidak ditemukan. Pastikan Anda sudah login dengan benar.
                </div>
            <?php endif; ?>

            <form action="prosespengaduan.php" method="post" enctype="multipart/form-data" id="pengaduanForm">
                <div class="form-group">
                    <label for="id_pengaduan">ID PENGADUAN</label>
                    <input type="text"
                        name="id_pengaduan"
                        id="id_pengaduan"
                        value="<?php echo getNextIdPreview($koneksi); ?>"
                        class="readonly-input"
                        readonly>
                </div>

                <div class="form-group">
                    <label for="tgl_pengaduan">Tanggal Pengaduan</label>
                    <input type="date"
                        name="tgl_pengaduan"
                        id="tgl_pengaduan"
                        value="<?php echo date('Y-m-d'); ?>"
                        required>
                </div>

                <div class="form-group">
                    <label for="nis">NIS (Nomor Induk Siswa)</label>
                    <input type="text"
                        name="nis"
                        id="nis"
                        value="<?php echo isset($_SESSION['nis']) ? $_SESSION['nis'] : ''; ?>"
                        class="readonly-input"
                        readonly>
                </div>

                <div class="form-group">
                    <label for="isi_laporan">Isi Laporan</label>
                    <textarea name="isi_laporan"
                        id="isi_laporan"
                        placeholder="Jelaskan detail pengaduan Anda dengan jelas dan lengkap..."
                        required></textarea>
                </div>

                <div class="form-group">
                    <label for="foto_laporan">Foto Pendukung</label>
                    <div class="file-input-wrapper">
                        <input type="file"
                            name="foto_laporan"
                            id="foto_laporan"
                            accept="image/*"
                            required>
                        <p class="file-hint">Format yang diperbolehkan: PNG, JPG, JPEG, GIF (Maksimal 5MB)</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="status">Status Pengaduan</label>
                    <input type="text"
                        name="status"
                        id="status"
                        value="Belum Diproses"
                        class="readonly-input"
                        readonly>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span class="loading"></span>
                        Simpan Pengaduan
                    </button>
                    <button type="button" class="btn btn-secondary" id="previewBtn">
                        Preview Foto
                    </button>
                </div>
            </form>
        </div>

        <div class="footer">
            <p>&copy; 2024 Layanan Pengaduan Digital | SMK NEGERI 6 KOTA BEKASI</p>
        </div>
    </div>

    <script>
        const inputFile = document.getElementById('foto_laporan');
        const previewBtn = document.getElementById('previewBtn');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('pengaduanForm');

        // File preview functionality
        inputFile.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                // Validasi ukuran file (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 5MB.');
                    this.value = '';
                    previewBtn.style.display = 'none';
                    return;
                }

                // Tampilkan tombol preview
                previewBtn.style.display = 'inline-flex';
                previewBtn.onclick = function() {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewWindow = window.open('', '_blank', 'width=800,height=600,scrollbars=yes');
                        previewWindow.document.write(`
                            <html>
                            <head>
                                <title>Preview Foto - ${file.name}</title>
                                <style>
                                    body {
                                        margin: 0;
                                        padding: 20px;
                                        background: #f5f5f5;
                                        font-family: Arial, sans-serif;
                                        display: flex;
                                        flex-direction: column;
                                        align-items: center;
                                    }
                                    .header {
                                        background: white;
                                        padding: 15px;
                                        border-radius: 8px;
                                        margin-bottom: 20px;
                                        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                                    }
                                    img {
                                        max-width: 90%;
                                        max-height: 70vh;
                                        border-radius: 8px;
                                        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
                                    }
                                    .close-btn {
                                        position: fixed;
                                        top: 20px;
                                        right: 20px;
                                        background: #dc3545;
                                        color: white;
                                        border: none;
                                        padding: 10px 15px;
                                        border-radius: 5px;
                                        cursor: pointer;
                                        font-weight: bold;
                                    }
                                    .close-btn:hover {
                                        background: #c82333;
                                    }
                                </style>
                            </head>
                            <body>
                                <div class="header">
                                    <h3>Preview Foto: ${file.name}</h3>
                                    <p>Ukuran: ${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                                </div>
                                <img src="${e.target.result}" alt="Preview Foto">
                                <button class="close-btn" onclick="window.close()">Tutup</button>
                            </body>
                            </html>
                        `);
                    };
                    reader.readAsDataURL(file);
                };
            } else {
                previewBtn.style.display = 'none';
            }
        });

        // Form submission with loading
        form.addEventListener('submit', function(e) {
            const loading = submitBtn.querySelector('.loading');
            loading.style.display = 'inline-block';
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="loading"></span> Menyimpan...';
        });

        // Auto-resize textarea
        const textarea = document.getElementById('isi_laporan');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(120, this.scrollHeight) + 'px';
        });
    </script>
</body>

</html>