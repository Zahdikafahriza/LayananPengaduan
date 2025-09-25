<?php
session_start();
include "koneksi.php";

// Cek kalau yang login harus operator
if (!isset($_SESSION['username']) || $_SESSION['level'] !== 'operator') {
    header("Location: login.php");
    exit();
}

$koneksi = mysqli_connect("localhost", "root", "", "pengaduandigital");
if (!$koneksi) die("Koneksi gagal: " . mysqli_connect_error());

$error = "";
if (isset($_POST['registrasi'])) {
    $nis = $_POST['nis'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $telp = $_POST['telp'];

    $sql = "INSERT INTO siswa (nis, nama, kelas, username, password, telp) 
            VALUES ('$nis','$nama','$kelas','$username','$password','$telp')";
    $data = mysqli_query($koneksi, $sql);

    if ($data) {
        header("Location: index_operator.php"); // balik ke operator
        exit;
    } else {
        $error = "Gagal menyimpan data!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Siswa (Operator)</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #eef2f3, #d9e4f5);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-box {
            background: #fff;
            padding: 40px 35px;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            width: 100%;
            max-width: 650px;
            animation: fadeIn 0.6s ease;
        }

        .register-box h2 {
            text-align: center;
            color: #003366;
            font-size: 1.9rem;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .error {
            background: #ffeaea;
            color: #d00000;
            border: 1px solid #e63946;
            border-radius: 10px;
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            font-size: 0.95rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 500;
            color: #003366;
            font-size: 0.92rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 0.95rem;
            background: #fafafa;
            transition: all 0.3s ease;
        }

        .form-group input:focus {
            border-color: #005a9e;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(0, 90, 158, 0.15);
            outline: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, #003366, #005a9e);
            color: #fff;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 10px;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 25px;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #005a9e, #003366);
            transform: translateY(-2px);
        }

        .footer {
            text-align: center;
            margin-top: 22px;
            font-size: 0.9rem;
            color: #444;
        }

        .footer a {
            color: #005a9e;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="register-box">
        <h2>Registrasi Siswa</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?= $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nis">NIS</label>
                    <input type="text" name="nis" id="nis" placeholder="Masukkan NIS" required>
                </div>
                <div class="form-group">
                    <label for="nama">Nama</label>
                    <input type="text" name="nama" id="nama" placeholder="Masukkan Nama" required>
                </div>
                <div class="form-group">
                    <label for="kelas">Kelas</label>
                    <input type="text" name="kelas" id="kelas" placeholder="Masukkan Kelas" required>
                </div>
                <div class="form-group">
                    <label for="telp">Telepon</label>
                    <input type="text" name="telp" id="telp" placeholder="Masukkan Telepon" required>
                </div>
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Masukkan Username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan Password" required>
                </div>
            </div>
            <button type="submit" name="registrasi" class="btn-submit">Buat Akun</button>
        </form>
        <div class="footer">
            <a href="index_operator.php"><i class="bi bi-arrow-left-circle"></i> Kembali ke Index Operator</a>
        </div>
    </div>
</body>

</html>