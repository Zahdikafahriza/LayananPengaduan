<?php
session_start();
include "koneksi.php";

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
    $data = mysqli_query($koneksi, "INSERT INTO siswa VALUES('$nis','$nama','$kelas','$username','$password','$telp')");
    if ($data) {
        header("Location: Login_User.php");
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
    <title>Register Siswa</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Open+Sans&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Open Sans', sans-serif;
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }

        .login-box h2 {
            text-align: center;
            color: #003366;
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 25px;
        }

        .error {
            background: #ffe3e3;
            color: #d00000;
            border: 1px solid #e63946;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            padding: 12px;
            font-size: 0.95rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #003366;
            font-size: 0.95rem;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            background: #f8f9fa;
        }

        .form-group input:focus {
            border-color: #005a9e;
            box-shadow: 0 0 0 4px rgba(0, 90, 158, 0.1);
            outline: none;
        }

        .btn-submit {
            background: linear-gradient(135deg, #003366, #005a9e);
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #005a9e, #003366);
            transform: translateY(-2px);
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 0.9rem;
        }

        .footer a {
            color: #005a9e;
            text-decoration: none;
            font-weight: 600;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        @media (max-width: 520px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Register Siswa</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
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
            <button type="submit" name="registrasi" class="btn-submit">Daftar</button>
        </form>
        <div class="footer">
            Sudah punya akun? <a href="Login_User.php">Login disini</a>
        </div>
    </div>
</body>

</html>