<?php
// Koneksi ke database MySQL
$host = 'localhost';
$user = 'root';
$pass = '';
$db_name = 'app_kost'; // Ganti dengan nama database Anda

$conn = mysqli_connect($host, $user, $pass, $db_name);

if (!$conn) {
    die('Koneksi gagal: ' . mysqli_connect_error());
}
?>
