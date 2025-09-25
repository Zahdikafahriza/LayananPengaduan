<?php
session_start();
include 'koneksi.php';

if ($_POST) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password']; // Password dalam bentuk teks biasa

    $query = "SELECT * FROM siswa WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Bandingkan password langsung (karena tidak di-hash)
        if ($password === $row['password']) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['level'] = 'siswa'; // Tandai sebagai siswa
            header("Location: index_siswa.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Akun siswa tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Siswa</title>
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
            max-width: 400px;
            animation: fadeInUp 0.7s;
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

        @media (max-width: 480px) {
            .login-box {
                padding: 24px;
            }

            .login-box h2 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Login Siswa</h2>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>
        <div class="footer">
            Belum punya akun? <a href="register_siswa.php">Daftar disini</a> <br><br>
            <a href="dashboard.php"><i class="bi bi-arrow-left-circle"></i> Kembali ke dashboard</a>
        </div>
    </div>
</body>

</html>