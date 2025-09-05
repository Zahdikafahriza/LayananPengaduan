<?php
header('Content-Type: application/json');

include 'koneksi.php';

if (!isset($_GET['username'])) {
    echo json_encode(['level' => null]);
    exit();
}

$username = mysqli_real_escape_string($koneksi, $_GET['username']);
$query = "SELECT level FROM petugas WHERE username = '$username'";
$result = mysqli_query($koneksi, $query);

if ($row = mysqli_fetch_assoc($result)) {
    echo json_encode(['level' => $row['level']]);
} else {
    echo json_encode(['level' => null]);
}
?>