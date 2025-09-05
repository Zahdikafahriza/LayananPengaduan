<?php
session_start();
include 'koneksi.php';

$error = '';

if ($_POST) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM petugas WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);

        // Cocokkan password (plain text, tidak disarankan untuk produksi)
        if ($password === $row['password']) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['level'] = $row['level']; // Simpan level: admin atau operator

            // Redirect berdasarkan level
            if ($row['level'] === 'admin') {
                header("Location: index_admin.php");
            } elseif ($row['level'] === 'operator') {
                header("Location: index_operator.php");
            } else {
                $error = "Level tidak valid.";
            }
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Akun petugas tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas</title>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
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
            margin-bottom: 18px;
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
            transition: border-color 0.3s;
            background: #f8f9fa;
        }

        .form-group input:focus {
            border-color: #005a9e;
            outline: none;
        }

        /* Level Display */
        .level-display {
            padding: 10px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 15px;
            border: 1px solid #bbdefb;
            display: none;
        }

        .level-admin {
            background: #bbdefb;
            color: #000000ff;
            border-color: #ef9a9a;
        }

        .level-operator {
            background: #bbdefb;
            color: #000000ff;
            border-color: #a5d6a7;
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
    </style>
</head>

<body>
    <div class="login-box">
        <h2>Login Petugas</h2>

        <?php if (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" placeholder="Masukkan username" required>
            </div>

            <!-- Tampilan Level -->
            <div class="level-display" id="levelDisplay"></div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" placeholder="Masukkan password" required>
            </div>

            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="footer">
            Belum punya akun? <a href="register_petugas.php">Daftar disini</a>
        </div>
    </div>

    <!-- AJAX untuk tampilkan level saat ketik username -->
    <script>
        const usernameInput = document.getElementById('username');
        const levelDisplay = document.getElementById('levelDisplay');

        usernameInput.addEventListener('blur', function() {
            const username = this.value.trim();
            if (username === '') {
                levelDisplay.style.display = 'none';
                return;
            }

            fetch('get_level.php?username=' + encodeURIComponent(username))
                .then(response => response.json())
                .then(data => {
                    if (data.level === 'admin') {
                        levelDisplay.className = 'level-display level-admin';
                        levelDisplay.textContent = 'Level Anda Adalah: ADMIN';
                        levelDisplay.style.display = 'block';
                    } else if (data.level === 'operator') {
                        levelDisplay.className = 'level-display level-operator';
                        levelDisplay.textContent = 'Level Anda Adalah: OPERATOR';
                        levelDisplay.style.display = 'block';
                    } else {
                        levelDisplay.style.display = 'none';
                    }
                })
                .catch(err => {
                    levelDisplay.style.display = 'none';
                });
        });
    </script>
</body>

</html>