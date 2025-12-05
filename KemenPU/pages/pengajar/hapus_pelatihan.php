<?php
require $_SERVER['DOCUMENT_ROOT'] . '/kemenPU/include/autoload.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("ID pelatihan tidak ditemukan.");
}

$id = intval($_GET['id']);

// Ambil info pelatihan untuk tahu dinas_id (agar bisa kembali ke halaman sebelumnya)
$q = mysqli_query($conn, "SELECT dinas_id FROM identifikasi_pelatihan WHERE id = $id");
$d = mysqli_fetch_assoc($q);

if (!$d) die("Data pelatihan tidak ditemukan.");

$dinas_id = $d['dinas_id'];

// Hapus data
mysqli_query($conn, "DELETE FROM identifikasi_pelatihan WHERE id = $id");

header("Location: detail_dinas.php?id=" . $dinas_id);
exit;
