<?php
include 'koneksi.php';
session_start();

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Fungsi untuk generate ID pengaduan yang aman
function generateIdPengaduanSafe($koneksi)
{
    $sql = "SELECT CAST(id_pengaduan AS UNSIGNED) as numeric_id FROM pengaduan 
            WHERE id_pengaduan REGEXP '^[0-9]+$' 
            ORDER BY CAST(id_pengaduan AS UNSIGNED) DESC 
            LIMIT 1";

    $result = mysqli_query($koneksi, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $lastNumericId = $row['numeric_id'];
        $newId = str_pad($lastNumericId + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newId = "0001";
    }

    // Double check untuk memastikan ID tidak duplicate
    $checkSql = "SELECT COUNT(*) as count FROM pengaduan WHERE id_pengaduan = '$newId'";
    $checkResult = mysqli_query($koneksi, $checkSql);
    $checkRow = mysqli_fetch_assoc($checkResult);

    if ($checkRow['count'] > 0) {
        // Jika masih duplicate, coba increment sekali lagi
        $newId = str_pad($lastNumericId + 2, 4, '0', STR_PAD_LEFT);
    }

    return $newId;
}

// Logika penyimpanan pengaduan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tgl_pengaduan'], $_POST['nis'], $_POST['isi_laporan'])) {

    // GENERATE ID BARU OTOMATIS (abaikan ID dari form)
    $id_pengaduan = generateIdPengaduanSafe($koneksi);

    // Ambil data dari form dengan sanitasi
    $tgl_pengaduan = mysqli_real_escape_string($koneksi, $_POST['tgl_pengaduan']);
    $nis = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $isi_laporan = mysqli_real_escape_string($koneksi, $_POST['isi_laporan']);
    $status = "Belum Diproses";

    // Validasi NIS exists di tabel siswa
    $checkNis = "SELECT COUNT(*) as count FROM siswa WHERE nis = '$nis'";
    $checkNisResult = mysqli_query($koneksi, $checkNis);
    $checkNisRow = mysqli_fetch_assoc($checkNisResult);

    if ($checkNisRow['count'] == 0) {
        $_SESSION['message'] = 'NIS tidak ditemukan dalam database siswa!';
        header("Location: pengaduan.php?error=1");
        exit();
    }

    // Pengaturan untuk upload gambar
    $rand = rand();
    $ekstensi_diizinkan = array('png', 'jpg', 'jpeg', 'gif');
    $filename = $_FILES['foto_laporan']['name'];
    $image_dir = "gambaraduan/";
    $namasementara = $_FILES['foto_laporan']['tmp_name'];
    $ukuran = $_FILES['foto_laporan']['size'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // Pastikan folder "gambaraduan" ada
    if (!is_dir($image_dir)) {
        mkdir($image_dir, 0777, true);
    }

    // Periksa apakah file ada dan valid
    if (!empty($filename) && $ext !== '') {
        if (in_array($ext, $ekstensi_diizinkan)) {
            // Ubah batas ukuran menjadi 5MB (bukan 500MB)
            if ($ukuran <= 5242880) { // 5MB dalam byte
                // Proses upload file dengan nama unik berdasarkan ID
                $nama_file_baru = $id_pengaduan . '_' . $rand . '.' . $ext;

                if (move_uploaded_file($namasementara, $image_dir . $nama_file_baru)) {

                    // FINAL CHECK: pastikan ID tidak duplicate sebelum insert
                    $finalCheckSql = "SELECT COUNT(*) as count FROM pengaduan WHERE id_pengaduan = '$id_pengaduan'";
                    $finalCheckResult = mysqli_query($koneksi, $finalCheckSql);
                    $finalCheckRow = mysqli_fetch_assoc($finalCheckResult);

                    if ($finalCheckRow['count'] > 0) {
                        // Generate ID baru jika masih duplicate
                        $id_pengaduan = generateIdPengaduanSafe($koneksi);
                    }

                    // Query insert dengan prepared statement untuk keamanan
                    $stmt = mysqli_prepare($koneksi, "INSERT INTO pengaduan (id_pengaduan, tgl_pengaduan, nis, isi_laporan, foto_laporan, status) VALUES (?, ?, ?, ?, ?, ?)");

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, "ssssss", $id_pengaduan, $tgl_pengaduan, $nis, $isi_laporan, $nama_file_baru, $status);

                        if (mysqli_stmt_execute($stmt)) {
                            $_SESSION['message'] = "Pengaduan berhasil disimpan dengan ID: $id_pengaduan";
                            $_SESSION['last_id_pengaduan'] = $id_pengaduan;

                            // Reset session ID untuk pengaduan berikutnya
                            unset($_SESSION['id_pengaduan']);

                            mysqli_stmt_close($stmt);
                            header("Location: pengaduan.php?status=success");
                            exit();
                        } else {
                            $_SESSION['message'] = 'Gagal menyimpan data: ' . mysqli_stmt_error($stmt);
                            mysqli_stmt_close($stmt);
                        }
                    } else {
                        $_SESSION['message'] = 'Error preparing statement: ' . mysqli_error($koneksi);
                    }
                } else {
                    $_SESSION['message'] = 'Gagal upload file!';
                }
            } else {
                $_SESSION['message'] = 'Ukuran file melebihi batas maksimal (5MB)';
            }
        } else {
            $_SESSION['message'] = 'Ekstensi file tidak diizinkan! Hanya PNG, JPG, JPEG, GIF yang diperbolehkan.';
        }
    } else {
        $_SESSION['message'] = 'File foto harus diunggah!';
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['message'] = 'Data tidak lengkap. Pastikan semua field diisi!';
}

// Redirect jika ada error
if (isset($_SESSION['message']) && $_SESSION['message'] != '') {
    header("Location: pengaduan.php?error=1");
    exit();
}

// Logika konfirmasi cetak jika data berhasil disimpan
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    $id_pengaduan = isset($_SESSION['last_id_pengaduan']) ? $_SESSION['last_id_pengaduan'] : '';

    if ($id_pengaduan) {
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

mysqli_close($koneksi);
