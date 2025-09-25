<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['username']) || !isset($_SESSION['level'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$level = $_SESSION['level'];

$data = null;

if ($level === 'siswa') {
    $sql = "SELECT * FROM siswa WHERE username = '$username'";
    $result = mysqli_query($koneksi, $sql);
    $data = mysqli_fetch_assoc($result);
} else {
    $sql = "SELECT * FROM petugas WHERE username = '$username'";
    $result = mysqli_query($koneksi, $sql);
    $data = mysqli_fetch_assoc($result);
}

// Tentukan halaman kembali
if ($level === 'siswa') {
    $back_page = "index_siswa.php";
} elseif ($level === 'admin') {
    $back_page = "index_admin.php";
} else {
    $back_page = "index_operator.php";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            font-family: "Segoe UI", Arial, sans-serif;
            background: linear-gradient(135deg, #74b9ff, #0984e3);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 520px;
            margin: 60px auto;
            background: #ffffff;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.2);
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }
        .profile-icon {
            font-size: 100px;
            color: #0984e3;
            margin-bottom: 20px;
        }
        h2 {
            margin-bottom: 20px;
            font-size: 24px;
            color: #2d3436;
        }
        .profile-data {
            text-align: left;
            margin-top: 15px;
        }
        .profile-data p {
            font-size: 16px;
            margin: 14px 0;
            padding: 10px 14px;
            background: #f1f2f6;
            border-left: 5px solid #0984e3;
            border-radius: 6px;
            transition: transform 0.2s;
        }
        .profile-data p:hover {
            transform: translateX(5px);
            background: #e9f5ff;
        }
        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 22px;
            border: none;
            background: #0984e3;
            color: white;
            font-size: 15px;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
            transition: background 0.3s, transform 0.2s;
        }
        .btn:hover {
            background: #0652dd;
            transform: scale(1.05);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <i class="bi bi-person-circle profile-icon"></i>
        <h2>Profil</h2>
        <div class="profile-data">
            <?php if ($data): ?>
                <?php if ($level === 'siswa'): ?>
                    <p><b>ID Siswa:</b> <?= $data['nis']; ?></p>
                    <p><b>Nama:</b> <?= $data['nama']; ?></p>
                    <p><b>Username:</b> <?= $data['username']; ?></p>
                    <p><b>Kelas:</b> <?= $data['kelas']; ?></p>
                    <p><b>No. Telp:</b> <?= $data['telp']; ?></p>
                <?php else: ?>
                    <p><b>ID Petugas:</b> <?= $data['id_petugas']; ?></p>
                    <p><b>Nama:</b> <?= $data['nama_petugas']; ?></p>
                    <p><b>Username:</b> <?= $data['username']; ?></p>
                    <p><b>Level:</b> <?= $data['level']; ?></p>
                    <p><b>No. Telp:</b> <?= $data['telp']; ?></p>
                <?php endif; ?>
            <?php else: ?>
                <p>Data tidak ditemukan.</p>
            <?php endif; ?>
        </div>
        <a href="<?= $back_page; ?>" class="btn">⬅ Kembali</a>
    </div>
</body>
</html>
