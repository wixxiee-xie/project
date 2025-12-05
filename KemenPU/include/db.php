<?php
$host = "localhost"; // Ganti dengan host Anda
$user = "root"; // Ganti dengan email database
$password = ''; // Ganti dengan password database
$dbname = "kemenpu2"; // Ganti dengan nama_lengkap database Anda

$conn = mysqli_connect($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
