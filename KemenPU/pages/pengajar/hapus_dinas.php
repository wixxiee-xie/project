<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID dinas tidak ditemukan.");
}

$id = intval($_GET['id']);

// Ambil wilayah_id sebelum dihapus
$q = mysqli_query($conn, "SELECT wilayah_id FROM dinas WHERE id = $id");
$d = mysqli_fetch_assoc($q);

if (!$d) die("Data dinas tidak ditemukan.");

$wilayah_id = $d['wilayah_id'];

// Hapus dinas
mysqli_query($conn, "DELETE FROM dinas WHERE id = $id");

// Redirect kembali ke halaman wilayah
header("Location: wilayah.php?id=" . $wilayah_id);
exit;
