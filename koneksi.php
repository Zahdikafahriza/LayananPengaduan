<?php

  $host = "localhost"; 
  $user = "root";
  $pass = "";
  $nama_db = "pengaduandigital"; 
  $koneksi = mysqli_connect($host,$user,$pass,$nama_db); 
  //pastikan urutan nya seperti ini, jangan tertukar

  // Memeriksa koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
} else {
    // Opsional: Pesan sukses (bisa dihapus jika tidak diperlukan)
    // echo "Koneksi berhasil!";
}
?>
