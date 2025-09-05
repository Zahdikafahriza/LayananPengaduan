<?php
session_start();
include "koneksi.php"; // Pastikan file koneksi.php ada

$host = "localhost";
$user = "root";        // Sesuaikan dengan username database
$password = "";        // Sesuaikan dengan password database
$db = "pengaduandigital";

$koneksi = mysqli_connect($host, $user, $password, $db);
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

$error = "";

if (isset($_POST['registrasi'])) {
    $id_petugas = $_POST['id_petugas'];
    $nama_petugas = $_POST['nama_petugas'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $telp = $_POST['telp'];
    $level = $_POST['level'];
    $data = mysqli_query($koneksi, "insert into petugas values('$id_petugas','$nama_petugas','$username','$password','telp','$level')");
    if (isset($data))
        header("location: Login_Admin.php");
    else echo "data gagal disimpan";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Petugas</title>
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
            /* Lebih lebar untuk 2 kolom */
            animation: fadeInUp 0.7s;
        }

        .login-box h2 {
            text-align: center;
            color: #003366;
            font-family: 'Poppins', sans-serif;
            font-size: 1.8rem;
            margin-bottom: 25px;
            letter-spacing: 0.5px;
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
            animation: slideIn 0.5s;
        }

        /* Grid 2 Kolom */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #003366;
            font-size: 0.95rem;
        }

        /* Input & Select */
        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
            background: #f8f9fa;
            font-family: 'Open Sans', sans-serif;
        }

        /* Styling untuk Select agar mirip input */
        .form-group select {
            appearance: none;
            -webkit-appearance: none;
            background-image: url("image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23003366' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            color: #333;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #005a9e;
            box-shadow: 0 0 0 4px rgba(0, 90, 158, 0.1);
            outline: none;
        }

        /* Full width untuk tombol */
        .full-width {
            grid-column: 1 / -1;
            text-align: center;
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
            box-shadow: 0 4px 12px rgba(0, 51, 102, 0.15);
            transition: background 0.3s, transform 0.2s;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, #005a9e, #003366);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 90, 158, 0.18);
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

        /* Animasi */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Responsive: 1 kolom di mobile */
        @media (max-width: 520px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .login-box {
                padding: 24px;
                max-width: 95%;
            }

            .login-box h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Register Petugas</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-grid">
                <!-- Kolom 1 -->
                <div class="form-group">
                    <label for="id_petugas">ID Petugas</label>
                    <input type="text" name="id_petugas" id="id_petugas" placeholder="Masukkan ID Petugas" required>
                </div>
                <div class="form-group">
                    <label for="nama_petugas">Nama Petugas</label>
                    <input type="text" name="nama_petugas" id="nama_petugas" placeholder="Masukkan Nama Petugas" required>
                </div>

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Masukkan Username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Masukkan Password" required>
                </div>

                <div class="form-group">
                    <label for="telp">Nomor Telepon</label>
                    <input type="text" name="telp" id="telp" placeholder="Masukkan Nomor Telepon" required>
                </div>
                <div class="form-group">
                    <label for="level">Level</label>
                    <select name="level" id="level" required>
                        <option value="" disabled selected>Pilih Level</option>
                        <option value="admin">Admin</option>
                        <option value="operator">Operator</option>
                    </select>
                </div>
            </div>

            <!-- Tombol Daftar (full width) -->
            <div class="full-width" style="margin-top: 20px;">
                <button type="submit" name="registrasi" class="btn-submit">Daftar</button>
            </div>
        </form>

        <div class="footer">
            Sudah punya akun? <a href="Login_Admin.php">Login disini</a>
        </div>
    </div>
</body>

</html>